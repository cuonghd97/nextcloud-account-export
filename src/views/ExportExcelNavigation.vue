<template>
	<NcAppNavigation aria-label="Account Export">
		<NcAppNavigationList
			class="account-management__system-list"
			data-cy-users-settings-navigation-groups="system"
		>
			<NcAppNavigationItem
				:exact="true"
				name="All accounts"
				:to="{ name: 'all' }"
			>
				<template #icon>
					<NcIconSvgWrapper :path="mdiAccount" />
				</template>
				<template #counter>
					<NcCounterBubble> 100 </NcCounterBubble>
				</template>
			</NcAppNavigationItem>
		</NcAppNavigationList>
	</NcAppNavigation>
</template>
<script>
import NcActionInput from '@nextcloud/vue/dist/Components/NcActionInput.js';
import NcActionText from '@nextcloud/vue/dist/Components/NcActionText.js';
import NcAppNavigation from '@nextcloud/vue/dist/Components/NcAppNavigation.js';
import NcAppNavigationCaption from '@nextcloud/vue/dist/Components/NcAppNavigationCaption.js';
import NcAppNavigationItem from '@nextcloud/vue/dist/Components/NcAppNavigationItem.js';
import NcAppNavigationList from '@nextcloud/vue/dist/Components/NcAppNavigationList.js';
import NcAppNavigationNew from '@nextcloud/vue/dist/Components/NcAppNavigationNew.js';
import NcButton from '@nextcloud/vue/dist/Components/NcButton.js';
import NcCounterBubble from '@nextcloud/vue/dist/Components/NcCounterBubble.js';
import NcIconSvgWrapper from '@nextcloud/vue/dist/Components/NcIconSvgWrapper.js';
import NcLoadingIcon from '@nextcloud/vue/dist/Components/NcLoadingIcon.js';
import {
	mdiAccount,
	mdiAccountGroup,
	mdiAccountOff,
	mdiCog,
	mdiPlus,
	mdiShieldAccount,
	mdiHistory,
} from '@mdi/js';
export default {
	name: 'ExportExcelNavigation',
	components: {
		NcAppNavigation,
		NcAppNavigationList,
		NcAppNavigationItem,
        NcIconSvgWrapper,
        NcCounterBubble
	},
	setup() {
		return {
			mdiAccount,
		};
	},
};
</script>
<style scoped></style>
