<template>
	<NcAppContent>
		<router-view></router-view>
		<div class="section">
			<h4>Display fields</h4>
			<!-- <div class="grid">
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
			</div> -->
			<!-- <NcSelect v-bind="selectOrigin" v-model="selectOrigin.value" />
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
			</div> -->
		</div>
	</NcAppContent>
</template>
<script>
import {
	mdiAccount,
	mdiDownload,
	mdiPlus
} from '@mdi/js';
import NcAppContent from '@nextcloud/vue/dist/Components/NcAppContent.js';
import NcAppNavigation from '@nextcloud/vue/dist/Components/NcAppNavigation.js';
import NcAppNavigationItem from '@nextcloud/vue/dist/Components/NcAppNavigationItem.js';
import NcAppNavigationList from '@nextcloud/vue/dist/Components/NcAppNavigationList.js';
import NcButton from '@nextcloud/vue/dist/Components/NcButton.js';
import NcCheckboxRadioSwitch from '@nextcloud/vue/dist/Components/NcCheckboxRadioSwitch.js';
import NcIconSvgWrapper from '@nextcloud/vue/dist/Components/NcIconSvgWrapper.js';
import NcLoadingIcon from '@nextcloud/vue/dist/Components/NcLoadingIcon.js';
import NcSelect from '@nextcloud/vue/dist/Components/NcSelect.js';
export default {
	name: 'ExportExcelContent',
	components: {
		NcAppNavigation,
		NcAppNavigationList,
		NcAppNavigationItem,
		NcAppContent,
	},
	setup() {
		return {
			mdiAccount,
			mdiPlus,
			mdiDownload,
		};
	},
};
</script>
<style scoped>
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
