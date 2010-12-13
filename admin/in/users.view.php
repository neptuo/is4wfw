<v:template src="~/templates/in-template.view">
	<v:panel security:requireGroup="admins">
		<php:using prefix="user" class="php.libs.User">
                    <web:a pageId="~/in/user-groups.view" text="User groups &raquo;" security:requirePerm="CMS.Settings.Groups" />
  		<user:management />
		</php:using>
	</v:panel>
</v:template>