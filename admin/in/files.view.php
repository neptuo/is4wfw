<v:template src="~/templates/in-template.view">
	<php:register tagPrefix="fl" classPath="php.libs.File" />
		<fl:showUploadForm />
		<fl:showNewDirectoryForm />
		<fl:showDirectory />
	<php:unregister tagPrefix="fl" />
</v:template>