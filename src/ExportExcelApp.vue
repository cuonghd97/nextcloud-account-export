<template>
	<NcContent app-name="account-export">
		<NcAppContent>
			<div class="section">
				<h4>Display fields</h4>
				<div class="grid">
					<NcCheckboxRadioSwitch
						type="checkbox"
						value="displayName"
						name="displayField"
						v-model="displayColumn"
					>
						Display name
					</NcCheckboxRadioSwitch>
					<NcCheckboxRadioSwitch
						type="checkbox"
						value="accountName"
						name="displayField"
						v-model="displayColumn"
					>
						Account name
					</NcCheckboxRadioSwitch>
					<NcCheckboxRadioSwitch
						type="checkbox"
						value="password"
						name="displayField"
						v-model="displayColumn"
					>
						Password
					</NcCheckboxRadioSwitch>
					<NcCheckboxRadioSwitch
						type="checkbox"
						value="email"
						name="displayField"
						v-model="displayColumn"
					>
						Email
					</NcCheckboxRadioSwitch>
					<NcCheckboxRadioSwitch
						type="checkbox"
						value="groups"
						name="displayField"
						v-model="displayColumn"
					>
						Groups
					</NcCheckboxRadioSwitch>
					<NcCheckboxRadioSwitch
						type="checkbox"
						value="groupAdminFor"
						name="displayField"
						v-model="displayColumn"
					>
						Group admin for
					</NcCheckboxRadioSwitch>
					<NcCheckboxRadioSwitch
						type="checkbox"
						value="quota"
						name="displayField"
						v-model="displayColumn"
					>
						Quota
					</NcCheckboxRadioSwitch>
					<NcCheckboxRadioSwitch
						type="checkbox"
						value="manager"
						name="displayField"
						v-model="displayColumn"
					>
						Manager
					</NcCheckboxRadioSwitch>
					<NcCheckboxRadioSwitch
						type="checkbox"
						value="language"
						name="displayField"
						v-model="displayColumn"
					>
						Language
					</NcCheckboxRadioSwitch>
					<NcCheckboxRadioSwitch
						type="checkbox"
						value="accountBackend"
						name="displayField"
						v-model="displayColumn"
					>
						Account backend
					</NcCheckboxRadioSwitch>
					<NcCheckboxRadioSwitch
						type="checkbox"
						value="lastLogin"
						name="displayField"
						v-model="displayColumn"
					>
						Last login
					</NcCheckboxRadioSwitch>
				</div>
				<NcSelect v-bind="selectOrigin" v-model="selectOrigin.value" />
				<div class="download-group">
					<NcButton aria-label="Export" @click="downloadFileExport">
						<template #icon>
							<NcIconSvgWrapper :path="mdiDownload" name="plus" />
						</template>
						<template> Export Excel </template>
					</NcButton>
					<NcLoadingIcon
						v-show="isDownloading"
						appearance="dark"
						name="Loading on light background"
					/>
				</div>
			</div>
		</NcAppContent>
	</NcContent>
</template>

<script>
import { mdiPlus, mdiDownload } from '@mdi/js';
import NcAppContent from '@nextcloud/vue/dist/Components/NcAppContent.js';
import NcButton from '@nextcloud/vue/dist/Components/NcButton.js';
import NcSelect from '@nextcloud/vue/dist/Components/NcSelect.js';
import NcCheckboxRadioSwitch from '@nextcloud/vue/dist/Components/NcCheckboxRadioSwitch.js';
import NcContent from '@nextcloud/vue/dist/Components/NcContent.js';
import NcIconSvgWrapper from '@nextcloud/vue/dist/Components/NcIconSvgWrapper.js';
import NcLoadingIcon from '@nextcloud/vue/dist/Components/NcLoadingIcon.js';

import { generateOcsUrl, generateUrl } from '@nextcloud/router';
import axios from '@nextcloud/axios';

export default {
	name: 'ExportExcelApp',
	components: {
		NcContent,
		NcIconSvgWrapper,
		NcAppContent,
		NcButton,
		NcCheckboxRadioSwitch,
		NcSelect,
		NcLoadingIcon,
	},
	setup() {
		return {
			mdiPlus,
			mdiDownload,
		};
	},
	created() {
		const url = generateOcsUrl('apps/accountexport/group/list');
		axios.get(url).then((response) => {
			const listGroup = response['data']['ocs']['data'].map((group) => {
				return { id: group['name'], label: group['name'] };
			});

			this.selectOrigin.options = [
				...this.selectOrigin.options,
				...listGroup,
			];
		});
	},
	data() {
		return {
			isDownloading: false,
			displayColumn: [
				'no',
				'displayName',
				'accountName',
				'password',
				'email',
				'groups',
				'groupAdminFor',
				'quota',
				'manager',
				'language',
				'accountBackend',
				'lastLogin',
			],
			selectOrigin: {
				inputLabel: 'Origin',
				options: [
					{
						id: 'all',
						label: 'All',
					},
					{
						id: 'admin',
						label: 'Admins',
					},
					{
						id: 'recentlyActive',
						label: 'Recently Active',
					},
				],
				value: {
					id: 'all',
					label: 'All',
				},
			},
		};
	},
	methods: {
		downloadFileExport() {
			this.isDownloading = true;
			let url = generateOcsUrl(
				`apps/accountexport/group/${this.selectOrigin.value.id}/download-export`
			);

			if (this.selectOrigin.value.id === 'all') {
				url = generateOcsUrl(`apps/accountexport/all/download-export`);
			}

			if (this.selectOrigin.value.id === 'recentlyActive') {
				url = generateOcsUrl(
					`apps/accountexport/recenty/download-export`
				);
			}

			axios
				.get(url, {
					params: { displayFields: this.displayColumn.join(',') },
					responseType: 'blob',
				})
				.then((response) => {
					const href = URL.createObjectURL(response.data);

					const link = document.createElement('a');
					link.href = href;

					const now = new Date();
					const dateString =
						now.getUTCFullYear() +
						'/' +
						(now.getUTCMonth() + 1) +
						'/' +
						now.getUTCDate() +
						'_' +
						now.getUTCHours() +
						':' +
						now.getUTCMinutes() +
						':' +
						now.getUTCSeconds();
					// const fileName = `file_export_all_accounts_${dateString}.xlsx`;

					const fileName = response.headers["content-disposition"].split("filename=")[1];
					console.log(fileName[fileName.length])
					console.log(fileName[fileName.length-1])
					link.setAttribute('download', fileName);
					document.body.appendChild(link);
					link.click();

					document.body.removeChild(link);
					URL.revokeObjectURL(href);
					this.isDownloading = false;
				});
		},
	},
};
</script>

<style scoped lang="scss">
#accountexport {
	display: flex;
	justify-content: center;
	margin: 16px;
}
.grid {
	display: grid;
	gap: 12px;
	grid-template-columns: 1fr 1fr 1fr 1fr;
	grid-template-rows: repeat(auto-fill, auto);
	position: relative;
	margin: 12px 0;
}

.download-group {
	display: flex;
}
</style>
