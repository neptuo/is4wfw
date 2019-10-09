<v:template src="~/templates/in-template.view">
	<web:frame title="Languages">
		<lang:list>
			<ui:empty items="lang:list">
				<h4 class="warning">No Languages</h4>
			</ui:empty>
			<ui:grid items="lang:list" class="standart clickable">
				<ui:column header="Id" value="lang:id" />
				<ui:column header="Url" value="lang:url" />
			</ui:grid>
		</lang:list>
		<hr />
		<div class="gray-box">

		</div>
	</web:frame>

	<php:using prefix="pg" class="php.libs.Page">
		<pg:showLanguages editable="true" />
	</php:using>
</v:template>