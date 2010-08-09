<v:template src="~/templates/in-template.view">
	<php:using prefix="sys" class="php.libs.System" security:requireGroup="admins">
		<sys:manageProperties />
	</php:using>
</v:template>