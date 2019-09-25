<v:template src="~/templates/in-template.view">
	<web:condition when="post:ce-manage-create" is="create">
		<web:frame title="Create">
			<ce:creator />
		</web:frame>
	</web:condition>
	<web:frame title="List">
		<!-- ce:manage / -->
		<hr />
		<ui:form>
			<button name="ce-manage-create" value="create">Create</button>
		</ui:form>
	</web:frame>
</v:template>