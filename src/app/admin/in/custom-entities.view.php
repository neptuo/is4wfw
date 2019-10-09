<v:template src="~/templates/in-template.view">
	<web:condition when="post:delete" is="delete">
		<ced:tableDeleter name="post:table">
			<web:redirectToSelf />
		</ced:tableDeleter>
	</web:condition>
	<web:condition when="post:ce-manage-create" is="create">
		<web:frame title="Create">
			<ced:tableCreator />
		</web:frame>
	</web:condition>
	<web:frame title="Custom Entities">
		<ced:listTables>
			<ui:empty items="ced:listTables">
				<h4 class="warning">No Custom Entities</h4>
			</ui:empty>
			<ui:grid items="ced:listTables" class="standart clickable">
				<ui:column header="Name" value="ced:tableName" />
				<ui:column header="Description" value="ced:tableDescription" />
				<ui:columnBoolean header="Audit log" value="ced:tableAuditLog" />
				<ui:columnTemplate header="">
					<web:a pageId="~/in/custom-entity-columns.view" param-table="ced:tableName" class="image-button button-edit">
						<img src="~/images/page_pro.png" />
					</web:a>
					<web:a pageId="~/in/custom-entity-localization.view" param-table="ced:tableName" class="image-button">
						<img src="~/images/lang.png" />
					</web:a>
					<admin:deleteButton hiddenField="delete" confirmValue="ced:tableName" hidden-table="ced:tableName" />
				</ui:columnTemplate>
			</ui:grid>
		</ced:listTables>
		<hr />
		<ui:form>
			<div class="gray-box">
				<button name="ce-manage-create" value="create">Create New Entity</button>
			</div>
		</ui:form>
	</web:frame>
</v:template>