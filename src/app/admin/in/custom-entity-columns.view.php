<v:template src="~/templates/in-template.view">
	<web:a pageId="~/in/custom-entities.view" text="&laquo; Custom Entities" />
	<web:condition when="post:ce-delete" is="delete">
		<ced:tableColumnDeleter tableName="query:table" columnName="post:ce-column">
			<web:redirectToSelf />
		</ced:tableColumnDeleter>
	</web:condition>
	<web:condition when="post:ce-column-create" is="create">
		<web:frame title="Create Column">
			<ced:tableColumnCreator name="query:table" />
		</web:frame>
	</web:condition>
	<web:frame title="query:table">
		<ced:listTableColumns name="query:table">
			<ui:grid items="ced:listTableColumns" class="standart clickable">
				<ui:column header="Name" value="ced:tableColumnName" />
				<ui:column header="Type" value="ced:tableColumnType" />
				<ui:columnBoolean header="Primary" value="ced:tableColumnPrimaryKey" />
				<ui:columnBoolean header="Required" value="ced:tableColumnRequired" />
				<ui:columnBoolean header="Unique" value="ced:tableColumnUnique" />
				<ui:columnTemplate header="">
					<web:condition when="ced:tableColumnPrimaryKey" is="false">
						<ui:form>
							<input type="hidden" name="ce-column" value="<web:getProperty name="ced:tableColumnName" />" />
							<input type="hidden" name="ce-delete" value="delete" />
							<input type="image" src="~/images/page_del.png" class="confirm" title="Delete custom entity column '<web:getProperty name="ced:tableColumnName" />'" />
						</ui:form>
					</web:condition>
				</ui:columnTemplate>
			</ui:grid>
		</ced:listTableColumns>
		<hr />
		<ui:form>
			<div class="gray-box">
				<button name="ce-column-create" value="create">Create New Column</button>
			</div>
		</ui:form>
	</web:frame>
</v:template>