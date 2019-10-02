<v:template src="~/templates/in-template.view">
	<web:condition when="post:ce-delete" is="delete">
		<ced:tableDeleter name="post:ce-table">
			<web:redirectToSelf />
		</ced:tableDeleter>
	</web:condition>
	<web:condition when="post:ce-manage-create" is="create">
		<web:frame title="Create">
			<ced:tableCreator />
		</web:frame>
	</web:condition>
	<web:frame title="Custom Entities">
		<table class="standart clickable">
			<tr>
				<th>Name</th>
				<th>Description</th>
				<th></th>
			</tr>
			<ced:listTables>
				<tr>
					<td>
						<web:getProperty name="ced:tableName" />
					</td>
					<td>
						<web:getProperty name="ced:tableDescription" />
					</td>
					<td>
						<web:a pageId="~/in/custom-entity-columns.view" param-table="ced:tableName" class="image-button button-edit">
							<img src="~/images/page_pro.png" />
						</web:a>
						<web:a pageId="~/in/custom-entity-localization.view" param-table="ced:tableName" class="image-button button-edit">
							<img src="~/images/lang.png" />
						</web:a>
						<ui:form>
							<input type="hidden" name="ce-table" value="<web:getProperty name="ced:tableName" />" />
							<input type="hidden" name="ce-delete" value="delete" />
							<input type="image" src="~/images/page_del.png" class="confirm" title="Delete custom entity '<web:getProperty name="ced:tableName" />'" />
						</ui:form>
					</td>
				</tr>
			</ced:listTables>
		</table>
		<hr />
		<ui:form>
			<div class="gray-box">
				<button name="ce-manage-create" value="create">Create New Entity</button>
			</div>
		</ui:form>
	</web:frame>
</v:template>