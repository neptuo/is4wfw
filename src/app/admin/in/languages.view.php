<v:template src="~/templates/in-template.view">
	<web:condition when="post:delete" is="delete">
		<lang:deleter id="post:id">
			<web:redirectTo pageId="~/in/languages.view" />
		</lang:deleter>
	</web:condition>
	<web:condition when="query:id">
		<web:frame title="Create/Edit">
			<lang:form id="query:id" submit="save" nextPageId="~/in/languages.view">
				<div class="gray-box">
					<label>Url:</label>
					<ui:textbox name="language" />
				</div>
				<div class="gray-box">
					<input type="hidden" name="edit" value="edit" />
					<button name="save" value="save">Save and Close</button>
					<web:a pageId="~/in/languages.view" text="Close" class="button" />
				</div>
			</lang:form>
		</web:frame>
	</web:condition>

	<web:frame title="Languages">
		<lang:list>
			<ui:empty items="lang:list">
				<h4 class="warning">No Languages</h4>
			</ui:empty>
			<ui:grid items="lang:list" class="standart clickable">
				<ui:column header="Id" value="lang:id" />
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
				<web:a pageId="~/in/languages.view" text="Create New Language" class="button" param-id="new" />
			</ui:form>
		</div>
	</web:frame>

	<php:using prefix="pg" class="php.libs.Page">
		<pg:showLanguages editable="true" />
	</php:using>
</v:template>