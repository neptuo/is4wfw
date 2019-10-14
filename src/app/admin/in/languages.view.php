<v:template src="~/templates/in-template.view">
	<web:condition when="post:delete" is="delete">
		<lang:deleter id="post:id">
			<web:redirectTo pageId="~/in/languages.view" />
		</lang:deleter>
	</web:condition>

	<admin:edit id="query:id">
		<web:frame title="admin:editTitle">
			<lang:form id="admin:editId" submit="save" nextPageId="~/in/languages.view">
				<admin:field label="Name" label-class="w110">
					<ui:textbox name="name" />
				</admin:field>
				<admin:field label="Natural name" label-class="w110">
					<ui:textbox name="natural_name" />
				</admin:field>
				<admin:field label="Url" label-class="w110">
					<ui:textbox name="language" class="w60" />
				</admin:field>
				<hr />
				<div class="gray-box">
					<input type="hidden" name="edit" value="edit" />
					<button name="save" value="save">Save and Close</button>
					<web:a pageId="~/in/languages.view" text="Close" class="button" />
				</div>
			</lang:form>
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
					<web:a pageId="~/in/languages.view" param-id="lang:id" class="image-button button-edit">
						<img src="~/images/page_edi.png" />
					</web:a>
					<admin:deleteButton hiddenField="delete" confirmValue="lang:url" hidden-id="lang:id" />
				</ui:columnTemplate>
			</ui:grid>
		</lang:list>
		<hr />
		<div class="gray-box">
			<ui:form>
				<admin:newButton pageId="~/in/languages.view" text="Create New Language" />
			</ui:form>
		</div>
	</web:frame>
</v:template>