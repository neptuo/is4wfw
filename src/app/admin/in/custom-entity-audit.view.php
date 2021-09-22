<v:template src="~/templates/in-template.view">
	<web:a pageId="~/in/custom-entities.view" text="&laquo; Custom Entities" />
	
	<utils:concat output="title" value1="Custom Entity Audit" value2=" :: " value3="query:table" />
	<web:frame title="utils:title">
		<ced:listTableAudit name="query:table">
			<ui:empty items="ced:listTableAudit">
				<h4 class="warning">
					<web:getProperty name="No audit data" />
				</h4>
			</ui:empty>
			<ui:grid items="ced:listTableAudit" class="standart">
				<ui:columnDateTime header="Timestamp" value="ced:tableAuditTimestamp" format="d.m.Y H:m:s" td-title="ced:tableAuditTimestamp" />
				<ui:column header="Sql" value="ced:tableAuditSql" />
				<ui:columnTemplate>
					<web:a pageId="~/in/custom-entity-audit.view" param-table="query:table" param-timestamp="ced:tableAuditTimestamp">
						Generate
					</web:a>
				</ui:columnTemplate>
			</ui:grid>
		</ced:listTableAudit>
	</web:frame>

	<web:condition when="query:timestamp">
		<utils:concat output="title" value1="Custom Entity Audit SQL" value2=" :: " value3="query:table" />
		<web:frame title="utils:title" open="true">
			<ced:tableAuditSql name="query:table" timestamp="query:timestamp">
				<div class="gray-box">
					<textarea class="h300"><web:out text="ced:tableAuditSql" /></textarea>
				</div>
			</ced:tableAuditSql>
		</web:frame>
	</web:condition>
</v:template>