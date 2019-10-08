<v:template src="~/templates/in-template.view">
	<web:frame title="Localization Bundles :: Instance">
        <ui:filter submit="search">
            <div class="gray-box gray-box-float">
                <label class="w90">Language:</label>
                <ui:dropdownlist name="lang" source="language" display="language" value="language" class="w200" />
            </div>
            <div class="gray-box-float">
                <button name="search">Open</button>
            </div>
            <div class="clear"></div>
        </ui:filter>
        <web:condition when="query:lang">
            <hr />
            <loc:edit bundleName="instance" languageName="query:lang" />
        </web:condition>
    </web:frame>
</v:template>
