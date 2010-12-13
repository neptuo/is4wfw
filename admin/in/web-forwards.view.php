<v:template src="~/templates/in-template.view">
	<php:register tagPrefix="pg" classPath="php.libs.Page" />
		<pg:editWebForward />
		<pg:showWebForwards />
	<php:unregister tagPrefix="pg" />
</v:template>