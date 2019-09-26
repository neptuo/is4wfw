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
						<ui:form>
							<input type="hidden" name="ce-table" value="<web:getProperty name="ce:tableName" />" />
							<input type="image" src="~/images/page_edi.png" name="ce-edit" value="edit" title="Edit table definition" disabled="disabled" />
						</ui:form>
						<ui:form>
							<input type="hidden" name="ce-table" value="<web:getProperty name="ce:tableName" />" />
							<input type="image" src="~/images/page_pro.png" name="ce-columns" value="columns" title="Edit table columns" />
						</ui:form>
						<ui:form>
							<input type="hidden" name="ce-table" value="<web:getProperty name="ce:tableName" />" />
							<input type="image" src="~/images/page_del.png" class="confirm" name="ce-delete" value="delete" title="Delete custom entity '<web:getProperty name="ce:tableName" />'" />
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