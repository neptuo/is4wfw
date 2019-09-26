<v:template src="~/templates/in-template.view">
	<web:frame title="post:ce-table">
		<table class="standart">
			<tr>
				<th>Name</th>
				<th>Type</th>
				<th></th>
			</tr>
			<ce:listTableColumns name="query:ce-table">
				<tr>
					<td>
						<web:getProperty name="ce:tableColumnName" />
					</td>
					<td>
						<web:getProperty name="ce:tableColumnType" />
					</td>
					<td>
						<ui:form>
							<input type="hidden" name="ce-column" value="<web:getProperty name="ce:tableColumnName" />" />
							<input type="hidden" name="ce-delete" value="delete" />
							<input type="image" src="~/images/page_del.png" class="confirm" title="Delete custom entity column '<web:getProperty name="ce:tableColumnName" />'" />
						</ui:form>
					</td>
				</tr>
			</ce:listTableColumns>
		<table>
		<hr />
		<ui:form>
			<button name="ce-column-create" value="create">Create</button>
		</ui:form>
	</web:frame>
</v:template>