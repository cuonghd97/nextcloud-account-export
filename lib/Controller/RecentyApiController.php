<?php

declare(strict_types=1);

namespace OCA\AccountExport\Controller;

use Exception;
use OCP\AppFramework\Http\Attribute\ApiRoute;
use OC\Authentication\Token\RemoteWipe;
use OC\KnownUser\KnownUserService;
use OC\SubAdmin;
use OCA\Settings\Mailer\NewUserMailHelper;
use OCP\Accounts\IAccountManager;
use OCP\AppFramework\Http\Attribute\NoAdminRequired;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\FileDisplayResponse;
use OCP\AppFramework\Http\StreamResponse;
use OCP\AppFramework\OCS\OCSNotFoundException;
use OCP\EventDispatcher\IEventDispatcher;
use OCP\Group\ISubAdmin;
use OCP\IConfig;
use OCP\IGroupManager;
use OCP\IL10N;
use OCP\IPhoneNumberUtil;
use OCP\IRequest;
use OCP\IURLGenerator;
use OCP\IUser;
use OCP\IUserManager;
use OCP\IUserSession;
use OCP\L10N\IFactory;
use OCP\Security\ISecureRandom;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Psr\Log\LoggerInterface;

use function Sabre\Uri\split;

/**
 * RecentyApiController for account export
 */
class RecentyApiController extends AUserExportData
{
	private $defaultHeader = [
		"no" => "No",
		"displayName" => "Display Name",
		"accountName" => "Account Name",
		"password" => "Password",
		"email" => "Email",
		"groups" => "Groups",
		"groupAdminFor" => "Group admin for",
		"quota" => "Quota",
		"manager" => "Manager",
		"language" => "Language",
		"accountBackend" => "Account Backend",
		"lastLogin" => "Last login"
	];
	private IL10N $l10n;

	public function __construct(
		string $appName,
		IRequest $request,
		IUserManager $userManager,
		IConfig $config,
		IGroupManager $groupManager,
		IUserSession $userSession,
		IAccountManager $accountManager,
		ISubAdmin $subAdminManager,
		IFactory $l10nFactory,
		private LoggerInterface $logger,
	) {
		parent::__construct(
			$appName,
			$request,
			$userManager,
			$config,
			$groupManager,
			$userSession,
			$accountManager,
			$subAdminManager,
			$l10nFactory
		);

		$this->l10n = $l10nFactory->get($appName);
	}

	/**
	 * API download file export account
	 *
	 * @return DataResponse<Http::STATUS_OK, array{message: string}, array{}>
	 *
	 * 200: Data returned
	 */
	#[NoAdminRequired]
	#[ApiRoute(verb: 'GET', url: '/recenty/download-export')]
	public function getLastLoggedInUsersAPI(string $displayFields)
	{
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$highestColumn = $sheet->getHighestColumn();
		$sheet->getStyle('A:A')->getAlignment()->setHorizontal('center');
		$sheet->getStyle('A1:' . $highestColumn . '1')->getFont()->setBold(true);

		$listDisplayFieldRequest = explode(",", $displayFields);
		$listHeaders = [];
		$listItemDisplay = [];
		foreach ($this->defaultHeader as $key => $value) {
			if (in_array($key, $listDisplayFieldRequest)) {
				array_push($listHeaders, $value);
				array_push($listItemDisplay, $key);
			}
		}

		foreach ($listHeaders as $colIndex => $header) {
			$columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
			$sheet->setCellValue($columnLetter . "1", $header);
			if ($listItemDisplay[$colIndex] == "email") {
				$sheet->getColumnDimension($columnLetter)->setWidth(20);
			}

			if ($listItemDisplay[$colIndex] == "displayName") {
				$sheet->getColumnDimension($columnLetter)->setWidth(18);
			}

			if ($listItemDisplay[$colIndex] == "accountName") {
				$sheet->getColumnDimension($columnLetter)->setWidth(18);
			}

			if ($listItemDisplay[$colIndex] == "groupAdminFor") {
				$sheet->getColumnDimension($columnLetter)->setWidth(15);
			}

			if ($listItemDisplay[$colIndex] == "accountBackend") {
				$sheet->getColumnDimension($columnLetter)->setWidth(25);
			}

			if ($listItemDisplay[$colIndex] == "quota") {
				$sheet->getColumnDimension($columnLetter)->setWidth(18);
			}
		}
		$offset = 0;
		$list_users_result = [];

		$rowExcelIndex = 2;
		do {
			$users = $this->userManager->getLastLoggedInUsers(25, $offset, "");

			$list_users_result = array_merge($list_users_result, $users);

			$listUserDetail = [];
			foreach ($users as $userId) {

				$userId = (string)$userId;
				try {
					$userData = $this->getUserData($userId);
				} catch (Exception) {
					$this->logger->warning('Find user error');
				}
				if ($userData !== null) {
					$listUserDetail[$userId] = $userData;

					foreach ($listItemDisplay as $colIndex => $columnKey) {
						$columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);

						if ($columnKey == "no") {
							$sheet->setCellValue($columnLetter . $rowExcelIndex, $rowExcelIndex - 1);
						}

						if ($columnKey == "displayName") {
							$sheet->setCellValue($columnLetter . $rowExcelIndex, $userData['displayname']);
						}

						if ($columnKey == "accountName") {
							$sheet->setCellValue($columnLetter . $rowExcelIndex, $userData['id']);
						}

						if ($columnKey == "password") {
							$sheet->setCellValue($columnLetter . $rowExcelIndex, "");
						}

						if ($columnKey == "email") {
							$sheet->setCellValue($columnLetter . $rowExcelIndex, $userData['email']);
						}

						if ($columnKey == "groups") {
							$sheet->setCellValue($columnLetter . $rowExcelIndex, join(", ", $userData['groups']));
						}

						if ($columnKey == "groupAdminFor") {
							$sheet->setCellValue($columnLetter . $rowExcelIndex, join(", ", $userData['subadmin']));
						}

						if ($columnKey == "quota") {
							$used = $userData['quota']['used'] . "B";

							if ($userData['quota']['quota'] == "none") {
								$quota = "Unlimited";
							} else {
								$quota = intdiv((int)$userData['quota']['total'], (1024 * 1024 * 1024))  . "GB";
							}

							$sheet->setCellValue($columnLetter . $rowExcelIndex, $quota . "(" . $used . ")");
						}

						if ($columnKey == "manager") {
							$sheet->setCellValue($columnLetter . $rowExcelIndex, $userData['manager']);
						}

						if ($columnKey == "language") {
							$sheet->setCellValue($columnLetter . $rowExcelIndex, $userData['language']);
						}

						if ($columnKey == "accountBackend") {
							$sheet->setCellValue($columnLetter . $rowExcelIndex, $userData['backend'] . "\n" . $userData['storageLocation']);
						}

						if ($columnKey == "lastLogin") {
							if ((int)$userData['lastLogin'] > 0) {
								$timestamp = intdiv((int)$userData['lastLogin'], 1000);
								$datetimeFormat = 'Y-m-d H:i:s';
								$date = new \DateTime();
								$date->setTimestamp($timestamp);
								$lastLogin = $date->format($datetimeFormat) . " UTC";
							} else {
								$lastLogin = "";
							}
							$sheet->setCellValue($columnLetter . $rowExcelIndex, $lastLogin);
						}
					}

					$rowExcelIndex += 1;
				} else {
					$listUserDetail[$userId] = ['id' => $userId];
				}
			}
			$offset += 25;
		} while (count($users) > 0);

		$writer = new Xlsx($spreadsheet);

		$tempFile = tempnam(sys_get_temp_dir(), 'export_') . '.xlsx';

		$writer->save($tempFile);

		$datetimeFormat = 'Y-m-d_H:i:s';
		$now = new \DateTime();

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename=export_recently_acounts' . $now->format($datetimeFormat) . '.xlsx');
		header('Content-Length: ' . filesize($tempFile));
		readfile($tempFile);
		// return new DataResponse(
		// 	['message' => $list_users_result]
		// );
		unlink($tempFile);
		exit;
	}
}
