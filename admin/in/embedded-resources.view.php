<v:template src="~/templates/in-template.view">
	<php:register tagPrefix="pg" classPath="php.libs.Page" />
		<pg:editEmbeddedResource />
		<pg:showEmbeddedResources />
	<php:unregister tagPrefix="pg" />
</v:template>