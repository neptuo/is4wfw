<v:template src="~/templates/in-template.view">
	<php:register tagPrefix="artc" classPath="php.libs.Article" />
		<v:content />
	<php:unregister tagPrefix="artc" />
</v:template>