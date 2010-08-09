<v:template src="~/templates/in-template.view">
	<php:using prefix="sys" class="php.libs.System" security:requireGroup="admins">
		<sys:editConnection />
		<sys:listConnections />
	</php:using>
</v:template>