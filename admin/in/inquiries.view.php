<v:template src="~/templates/in-template.view">
	<php:register tagPrefix="i" classPath="php.libs.Inquiry" />
		<i:setCurrentId />
	
		<i:editAnswer />
		<i:listAnswers />
		<i:edit />
		<i:list />
	<php:unregister tagPrefix="i" />
</v:template>