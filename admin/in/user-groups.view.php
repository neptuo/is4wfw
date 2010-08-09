<v:template src="~/templates/in-template.view">
	<v:panel security:requireGroup="admins">
		<php:using prefix="user" class="php.libs.User">
  		<web:a pageId="~/in/users.view" text="Back to users" />
  		<user:newGroup />
	  	<user:deleteGroup />
		</php:using>
	</v:panel>
</v:template>