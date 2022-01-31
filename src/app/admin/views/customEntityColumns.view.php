<loc:use name="customentityadmin">
	<web:condition when="post:delete" is="delete">
		<ced:tableColumnDeleter tableName="query:table" columnName="post:column">
			<web:redirectToSelf />
		</ced:tableColumnDeleter>
	</web:condition>
		
	<utils:concat output="backButtonText" value1="&laquo;" value2=" " value3="loc:tablelist.title" />
	<web:a pageId="~/in/custom-entities.view" text="utils:backButtonText" />
	
	<admin:edit id="query:column">
		<utils:concat output="editTitle" value1="loc:columnlist.edit.title" value2=" :: " value3="query:column" />
		<web:frame title="utils:editTitle">
			<edit:form submit="save">
				<web:condition when="edit:saved">
					<web:condition when="query:column" is="new">
						<web:redirectTo pageId="~/in/custom-entity-columns.view" param-table="query:table" />
					</web:condition>
					<admin:redirectAfterSave saveName="save" saveParam-table="query:table" saveParam-column="query:column" closePageId="~/in/custom-entity-columns.view" closeParam-table="query:table" /> 
				</web:condition>

				<web:condition when="query:column" is="new">
					<ced:tableColumnCreator name="query:table" />
				</web:condition>
				<web:condition when="query:column" is="new" isInverted="true"> 
					<ced:tableColumnEditor tableName="query:table" columnName="query:column">
						<div class="gray-box">
							<label class="block">Description:</label>
							<ui:textarea name="column-description" class="w700 h100" />
						</div>
						<div class="gray-box">
							<admin:saveButtons closePageId="~/in/custom-entity-columns.view" closeParam-table="query:table" />
						</div>
					</ced:tableColumnEditor>
				</web:condition>
			</edit:form>
		</web:frame>
	</admin:edit>

	<utils:concat output="listTitle" value1="loc:columnlist.title" value2=" :: " value3="query:table" />
	<web:frame title="utils:listTitle">
		<ced:listTableColumns name="query:table">
			<ui:grid items="ced:listTableColumns" class="standart clickable">
				<ui:column header="loc:columnlist.name" value="ced:tableColumnName" />
				<ui:column header="loc:columnlist.description" value="ced:tableColumnDescription" />
				<ui:column header="loc:columnlist.type" value="ced:tableColumnType" />
				<ui:columnBoolean header="loc:columnlist.primary" value="ced:tableColumnPrimaryKey" />
				<ui:columnBoolean header="loc:columnlist.required" value="ced:tableColumnRequired" />
				<ui:columnBoolean header="loc:columnlist.unique" value="ced:tableColumnUnique" />
				<ui:columnTemplate header="">
					<web:a pageId="~/in/custom-entity-columns.view" param-table="query:table" param-column="ced:tableColumnName" class="image-button button-edit">
						<img src="~/images/page_edi.png" />
					</web:a>
					<web:condition when="ced:tableColumnPrimaryKey" is="false">
						<admin:deleteButton hiddenField="delete" confirmValue="ced:tableColumnName" hidden-column="ced:tableColumnName" />
					</web:condition>
				</ui:columnTemplate>
			</ui:grid>
		</ced:listTableColumns>
		<hr />
		<div class="gray-box">
			<admin:newButton pageId="~/in/custom-entity-columns.view" paramName="column" param-table="query:table" text="loc:columnlist.create" />
		</div>
	</web:frame>
</loc:use>