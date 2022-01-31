<loc:use name="customentityadmin">
	<web:condition when="post:delete" is="delete">
		<ced:tableDeleter name="post:table">
			<web:redirectToSelf />
		</ced:tableDeleter>
	</web:condition>
	<admin:edit id="query:table">
		<utils:concat output="editTitle" value1="loc:tablelist.edit.title" value2=" :: " value3="query:table" />
		<web:frame title="utils:editTitle">
			<edit:form submit="save">
				<web:condition when="edit:saved">
					<admin:redirectAfterSave saveName="save" closePageId="~/in/custom-entities.view" saveParam-table="query:table" />
				</web:condition>

				<web:condition when="query:table" is="new">
					<ced:tableCreator />
				</web:condition>
				<web:condition when="query:table" is="new" isInverted="true">
					<ced:tableEditor name="query:table">
						<div class="gray-box">
							<label class="block">Description:</label>
							<ui:textarea name="entity-description" class="w700 h100" />
						</div>
						<div class="gray-box">
							<label class="w90">Audit:</label>
							<label>
								<ui:checkbox name="entity-audit-log" />
								Generate audit log
							</label>
						</div>
						<div class="gray-box">
							<admin:saveButtons closePageId="~/in/custom-entities.view" />
						</div>
					</ced:tableEditor>
				</web:condition>
			</edit:form>
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
					<web:a pageId="~/in/custom-entities.view" param-table="ced:tableName" class="image-button" title="Edit entity">
						<img src="~/images/page_edi.png" />
					</web:a>
					<web:a pageId="~/in/custom-entity-columns.view" param-table="ced:tableName" class="image-button button-edit" title="Entity columns">
						<img src="~/images/page_pro.png" />
					</web:a>
					<web:a pageId="~/in/custom-entity-localization.view" param-table="ced:tableName" class="image-button" title="Entity localized columns">
						<img src="~/images/lang.png" />
					</web:a>
					<web:a pageId="~/in/custom-entity-audit.view" param-table="ced:tableName" class="image-button" title="Entity audit">
						<img src="~/images/page_find.png" />
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