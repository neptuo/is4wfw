<v:template src="~/templates/in-template.view">
	<a href="~/in/hint.view">&laquo; TagLib manual</a>
	<php:using prefix="hint" class="php.libs.Hint">
		<hint:propertyList />

		<hint:autoRegister />
	</php:using>
</v:template>