<v:template src="~/templates/in-template.view">
	<v:panel security:requireGroup="admins">
  		<web:a pageId="~/in/user-groups.view" text="&laquo; Back to user groups" />
		<web:frame title="Group perms">
			Page.EditDetail, Page.AddNew, Page.ManageFiles, Page.Delete, Page.MoveTree, Page.CopyTree, Page.MoveUpDown, Page.AddLang, Page.Delete, Page.ManageRights
		</web:frame>
	</v:panel>
</v:template>