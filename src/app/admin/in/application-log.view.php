<v:template src="~/templates/in-template.view">
	<php:using prefix="applog" class="php.libs.ApplicationLog">
		<web:condition when="query:fileName">
			<web:frame title="Application Log">
				<div class="gray-box">
					<web:condition when="query:tail">
						<web:a pageId="~/in/application-log.view" param-fileName="query:fileName" class="button">Full file</web:a>
					</web:condition>
					<web:a pageId="~/in/application-log.view" class="button">Close file</web:a>
				</div>
				<div class="gray-box">
					<code>
						<applog:log fileName="query:fileName" tailLines="query:tail" /> 
					</code>
				</div>
			</web:frame>
		</web:condition>
		<web:frame title="Application Logs">
			<utils:arrayItem output="dates" key-name="Today" key-value="0.001" />
			<utils:arrayItem output="dates" key-name="Yesterday" key-value="1" />
			<utils:arrayItem output="dates" key-name="7 days" key-value="7" />
			<utils:arrayItem output="dates" key-name="14 days" key-value="14" />
			<utils:arrayItem output="dates" key-name="30 days" key-value="30" />
			<utils:arrayItem output="dates" key-name="All" key-value="" />
			<edit:form submit="search">
				<ui:filter session="logs-project,logs-age">
					<admin:field label="Project" label-class="w80">
						<ui:textbox name="logs-project" class="w300" />
					</admin:field>
					<admin:field label="Max age" label-class="w80">
						<ui:dropdownlist name="logs-age" source="utils:dates" value="value" display="name" />
					</admin:field>
					<div class="gray-box">
						<button name="search">Search</button>
					</div>
				</ui:filter>
			</edit:form>
			<hr />
			<applog:list filter-age="session:logs-age" filter-project="session:logs-project" />
		</web:frame>
	</php:using>
</v:template>