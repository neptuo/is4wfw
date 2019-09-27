<v:template src="~/templates/in-template.view">
	<web:a pageId="~/in/custom-entities.view" text="&laquo; Custom Entities" />
	<web:condition when="post:ce-delete" is="delete">
		<ce:tableColumnDeleter tableName="query:table" columnName="post:ce-column">
			<web:redirectToSelf />
		</ce:tableColumnDeleter>
	</web:condition>
	<web:condition when="post:ce-column-create" is="create">
		<web:frame title="Create Column">
			<ce:tableColumnCreator name="query:table" />
		</web:frame>
	</web:condition>
	<web:frame title="query:table">
		<table class="standart">
			<tr>
				<th>Name</th>
				<th>Type</th>
				<th>Required</th>
				<th>Primary</th>
				<th></th>
			</tr>
			<ce:listTableColumns name="query:table">
				<tr>
					<td>
						<web:getProperty name="ce:tableColumnName" />
					</td>
					<td>
						<web:getProperty name="ce:tableColumnType" />
					</td>
					<td>
						<web:getProperty name="ce:tableColumnRequired" />
					</td>
					<td>
						<web:getProperty name="ce:tableColumnPrimaryKey" />
					</td>
					<td>
						<web:condition when="ce:tableColumnPrimaryKey" is="false">
							<ui:form>
								<input type="hidden" name="ce-column" value="<web:getProperty name="ce:tableColumnName" />" />
								<input type="hidden" name="ce-delete" value="delete" />
								<input type="image" src="~/images/page_del.png" class="confirm" title="Delete custom entity column '<web:getProperty name="ce:tableColumnName" />'" />
							</ui:form>
						</web:condition>
					</td>
				</tr>
			</ce:listTableColumns>
		<table>
		<hr />
		<ui:form>
			<button name="ce-column-create" value="create">Create New Column</button>
		</ui:form>
	</web:frame>
</v:template>