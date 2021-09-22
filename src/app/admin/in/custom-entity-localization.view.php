<v:template src="~/templates/in-template.view">
	<web:a pageId="~/in/custom-entities.view" text="&laquo; Custom Entities" />
	
	<utils:concat output="title" value1="Custom Entity Localization" value2=" :: " value3="query:table" />
	<web:frame title="utils:title">
		<edit:form submit="ced-localizable-save">
			<ced:tableLocalizationEditor name="query:table" />
		</edit:form>
	</web:frame>
</v:template>