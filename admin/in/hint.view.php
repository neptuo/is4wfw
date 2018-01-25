<v:template src="~/templates/in-template.view">
	<web:a pageId="~/in/hint-properties.view" text="User interface properties &amp; auto registered libraries &raquo;" />
	<php:using prefix="hint" class="php.libs.Hint">
		<hint:selectLib />
		<hint:lib classPath="hint:classPath" />
	</php:using>
</v:template>