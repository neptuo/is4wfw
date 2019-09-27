<v:template src="~/templates/in-template.view">
	<web:condition when="post:ce-delete" is="delete">
		<ce:tableDeleter name="post:ce-table">
			<web:redirectToSelf />
		</ce:tableDeleter>
	</web:condition>
	<web:condition when="post:ce-manage-create" is="create">
		<web:frame title="Create">
			<ce:tableCreator />
		</web:frame>
	</web:condition>
	<web:frame title="Custom Entities">
		<table class="standart clickable">
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
						<web:a pageId="~/in/custom-entity-columns.view" param-table="ce:tableName" class="image-button button-edit">
							<img src="~/images/page_pro.png" />
						</web:a>
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
			<div class="gray-box">
				<button name="ce-manage-create" value="create">Create New Entity</button>
			</div>
		</ui:form>
	</web:frame>
</v:template>