<php:using prefix="user" class="php.libs.User" security:requireGroup="admins">
	<web:a pageId="route:userGroups" text="User groups &raquo;" security:requirePerm="CMS.Settings.Groups" />
	<user:management propertiesUrl="route:userProperties" />
</php:using>