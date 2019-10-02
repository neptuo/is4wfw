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
		<table class="standart">
			<tr>
				<th>Name</th>
				<th>Type</th>
				<th>Primary</th>
				<th>Required</th>
				<th>Unique</th>
				<th></th>
			</tr>
			<ced:listTableColumns name="query:table">
				<tr>
					<td>
						<web:getProperty name="ced:tableColumnName" />
					</td>
					<td>
						<web:getProperty name="ced:tableColumnType" />
					</td>
					<td>
						<web:condition when="ced:tableColumnPrimaryKey">
							Yes
						</web:condition>
					</td>
					<td>
						<web:condition when="ced:tableColumnRequired">
							Yes
						</web:condition>
					</td>
					<td>
						<web:condition when="ced:tableColumnUnique">
							Yes
						</web:condition>
					</td>
					<td>
						<web:condition when="ced:tableColumnPrimaryKey" is="false">
							<ui:form>
								<input type="hidden" name="ce-column" value="<web:getProperty name="ced:tableColumnName" />" />
								<input type="hidden" name="ce-delete" value="delete" />
								<input type="image" src="~/images/page_del.png" class="confirm" title="Delete custom entity column '<web:getProperty name="ced:tableColumnName" />'" />
							</ui:form>
						</web:condition>
					</td>
				</tr>
			</ced:listTableColumns>
		<table>
		<hr />
		<ui:form>
			<div class="gray-box">
				<button name="ce-column-create" value="create">Create New Column</button>
			</div>
		</ui:form>
	</web:frame>
</v:template>