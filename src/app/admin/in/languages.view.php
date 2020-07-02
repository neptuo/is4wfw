<v:template src="~/templates/in-template.view">
	<var:declare name="selfUrl" value="~/in/languages.view" scope="request" />
	<web:condition when="post:delete" is="delete">
		<lang:deleter id="post:id">
			<web:redirectTo pageId="var:selfUrl" />
		</lang:deleter>
	</web:condition>

	<val:translate identifier="required" message="Required field" />

	<admin:edit id="query:id">
		<web:frame title="admin:editTitle">
			<edit:form submit="save">
				<lang:form id="admin:editId">
					<admin:field label="Name" label-class="w110">
						<ui:textbox name="name" />
						<admin:validation key="name" />
					</admin:field>
					<admin:field label="Natural name" label-class="w110">
						<ui:textbox name="natural_name" />
						<admin:validation key="natural_name" />
					</admin:field>
					<admin:field label="Url" label-class="w110">
						<ui:textbox name="language" class="w60" />
						<admin:validation key="language" />
					</admin:field>
					<hr />
					<div class="gray-box">
						<admin:saveButtons closePageId="var:selfUrl" saveParam-id="lang:id" />
					</div>
				</lang:form>
			</edit:form>
		</web:frame>
	</admin:edit>

	<web:frame title="List">
		<lang:list>
			<ui:empty items="lang:list">
				<h4 class="warning">No Languages</h4>
			</ui:empty>
			<ui:grid items="lang:list" class="standart clickable">
				<ui:column header="Id" value="lang:id" />
				<ui:column header="Name" value="lang:name" />
				<ui:column header="Natural name" value="lang:natural_name" />
				<ui:column header="Url" value="lang:url" />
				<ui:columnTemplate>
					<web:a pageId="var:selfUrl" param-id="lang:id" class="image-button button-edit">
						<img src="~/images/page_edi.png" />
					</web:a>
					<admin:deleteButton hiddenField="delete" confirmValue="lang:url" hidden-id="lang:id" />
				</ui:columnTemplate>
			</ui:grid>
		</lang:list>
		<hr />
		<div class="gray-box">
			<admin:newButton pageId="var:selfUrl" text="Create New Language" />
		</div>
	</web:frame>
</v:template>