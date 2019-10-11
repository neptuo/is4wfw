<v:template src="~/templates/in-template.view">
	<loc:use name="customentityadmin">
		<web:condition when="post:delete" is="delete">
			<ced:tableColumnDeleter tableName="query:table" columnName="post:column">
				<web:redirectToSelf />
			</ced:tableColumDeleter>
		</web:condition>
			
		<utils:concat output="backButtonText" value1="&laquo;" value2=" " value3="loc:tablelist.title" />
		<web:a pageId="~/in/custom-entities.view" text="utils:backButtonText" />
		
		<web:condition when="post:create" is="create">
			<web:frame title="loc:columnlist.create">
				<ced:tableColumnCreator name="query:table" />
			</web:frame>
		</web:condition>

		<utils:concat output="listTitle" value1="loc:columnlist.title" value2=" :: " value3="query:table" />
		<web:frame title="utils:listTitle">
			<ced:listTableColumns name="query:table">
				<ui:grid items="ced:listTableColumns" class="standart clickable">
					<ui:column header="loc:columnlist.name" value="ced:tableColumnName" />
					<ui:column header="loc:columnlist.type" value="ced:tableColumnType" />
					<ui:columnBoolean header="loc:columnlist.primary" value="ced:tableColumnPrimaryKey" />
					<ui:columnBoolean header="loc:columnlist.required" value="ced:tableColumnRequired" />
					<ui:columnBoolean header="loc:columnlist.unique" value="ced:tableColumnUnique" />
					<ui:columnTemplate header="">
						<web:condition when="ced:tableColumnPrimaryKey" is="false">
    						<admin:deleteButton hiddenField="delete" confirmValue="ced:tableColumnName" hidden-column="ced:tableColumnName" />
						</web:condition>
					</ui:columnTemplate>
				</ui:grid>
			</ced:listTableColumns>
			<hr />
			<ui:form>
				<div class="gray-box">
					<button name="create" value="create">
						<web:getProperty name="loc:columnlist.create" />
					</button>
				</div>
			</ui:form>
		</web:frame>
	</loc:use>
</v:template>