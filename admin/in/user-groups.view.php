<v:template src="~/templates/in-template.view">
	<v:panel security:requireGroup="admins">
		<php:using prefix="user" class="php.libs.User">
			<web:a pageId="~/in/group-perms-hint.view" class="fright" text="List of perms &raquo;" />
			<web:a pageId="~/in/users.view" text="&laquo; Back to users" />
			<user:newGroup />
			<user:editGroupPerms />
			<user:deleteGroup />
		</php:using>
	</v:panel>
</v:template>