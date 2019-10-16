<v:template src="~/templates/in-template.view">
	<loc:use name="customentityadmin">
		<web:condition when="post:delete" is="delete">
	        <ced:tableDeleter name="post:table">
				<web:redirectToSelf />
			</ced:tableDeleter>
		</web:condition>
		<admin:edit id="query:table">
			<web:frame title="loc:tablelist.create">
				<ced:tableCreator />
			</web:frame>
		</admin:edit>
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
			<div class="gray-box">
				<admin:newButton pageId="~/in/custom-entities.view" paramName="table" text="loc:tablelist.create" />
			</div>
		</web:frame>
	</loc:use>
</v:template>