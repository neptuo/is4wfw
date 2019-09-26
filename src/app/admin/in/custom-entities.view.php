<v:template src="~/templates/in-template.view">
	<web:condition when="post:ce-manage-create" is="create">
		<web:frame title="Create">
			<ce:tableCreator />
		</web:frame>
	</web:condition>
	<web:frame title="List">
		<table class="standart">
			<tr>
				<th>Name</th>
				<th></th>
			</tr>
			<ce:listTables>
				<tr>
					<td>
						<web:getProperty name="ce:tableName" />
					</td>
					<td>
						<ui:form method="GET" action="~/in/custom-entity-columns.view">
							<input type="hidden" name="ce-table" value="<web:getProperty name="ce:tableName" />" />
							<input type="hidden" name="ce-edit" value="edit" />
							<input type="image" src="~/images/page_edi.png" title="Edit table definition" disabled="disabled" />
						</ui:form>
						<ui:form method="GET" action="~/in/custom-entity-columns.view">
							<input type="hidden" name="ce-table" value="<web:getProperty name="ce:tableName" />" />
							<input type="hidden" name="ce-columns" value="columns" />
							<input type="image" src="~/images/page_pro.png" title="Edit table columns" />
						</ui:form>
						<ui:form>
							<input type="hidden" name="ce-table" value="<web:getProperty name="ce:tableName" />" />
							<input type="hidden" name="ce-delete" value="delete" />
							<input type="image" src="~/images/page_del.png" class="confirm" title="Delete custom entity '<web:getProperty name="ce:tableName" />'" />
						</ui:form>
					</td>
				</tr>
			</ce:listTables>
		<table>
		<hr />
		<ui:form>
			<button name="ce-manage-create" value="create">Create</button>
		</ui:form>
	</web:frame>
</v:template>