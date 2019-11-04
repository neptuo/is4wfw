<v:template src="~/templates/in-template.view">
	<web:a pageId="~/in/custom-entities.view" text="&laquo; Custom Entities" />
	<web:frame title="query:table">
		<edit:form submit="ced-localizable-save">
			<ced:tableLocalizationEditor name="query:table" />
		</edit:form>
	</web:frame>
</v:template>