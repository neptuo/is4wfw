<v:template src="~/templates/in-template.view">
	<php:register tagPrefix="pg" classPath="php.libs.Page" />
		<pg:showEditFile />
		<pg:showFiles editable="true" />
	<php:unregister tagPrefix="pg" />
</v:template>