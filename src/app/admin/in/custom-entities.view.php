<v:template src="~/templates/in-template.view">
	<loc:use name="customentityadmin">
		<web:condition when="post:delete" is="delete">
	        <ced:tableDeleter name="post:table">
				<web:redirectToSelf />
			</ced:tableDeleter>
		</web:condition>
		<web:condition when="post:create" is="create">
			<web:frame title="loc:tablelist.create">
				<ced:tableCreator />
			</web:frame>
		</web:condition>
		<web:frame title="loc:tablelist.title">
			<ced:listTables>
				<ui:empty items="ced:listTables">
					<h4 class="warning">
						<web:getProperty name="loc:tablelist.nodata" />
					</h4>
				</ui:empty>
				<ui:grid items="ced:listTables" class="standart clickable">
					<ui:column header="loc:tablelist.name" value="ced:tableName" />
					<ui:column header="loc:tablelist.description" value="ced:tableDescription" />
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
					<button name="create" value="create">
						<web:getProperty name="loc:tablelist.create" />
					</button>
				</div>
			</ui:form>
		</web:frame>
	</loc:use>
</v:template>