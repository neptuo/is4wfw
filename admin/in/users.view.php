<v:template src="~/templates/in-template.view">
	<v:panel security:requireGroup="admins">
		<php:using prefix="user" class="php.libs.User">
  		<web:a pageId="~/in/user-groups.view" text="Edit user groups" />
  		<user:management />
		</php:using>
	</v:panel>
</v:template>