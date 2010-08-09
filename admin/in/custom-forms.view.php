<v:template src="~/templates/in-template.view">
	<php:register tagPrefix="c" classPath="php.libs.CustomForm" />
		<c:formList />
		<c:formCreator />
	<php:unregister tagPrefix="c" />
</v:template>