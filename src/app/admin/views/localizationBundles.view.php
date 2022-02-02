<web:frame title="Localization Bundles :: Instance">
    <filter:declare name="languages">
        <filter:empty name="language" not="true" />
    </filter:declare>
    <edit:form submit="search">
        <ui:filter>
            <div class="gray-box gray-box-float">
                <label class="w90">Language:</label>
                <lang:list filter="filter:languages">
                    <ui:dropdownlist name="lang" source="lang:list" display="name" value="language" class="w200" />
                </lang:list>
            </div>
            <div class="gray-box-float">
                <button name="search">Open</button>
            </div>
            <div class="gray-box-float">
                <button formaction="<web:out text="route:localizationBundleDownload" />" method="get" name="search">Download</button>
            </div>
            <div class="clear"></div>
        </ui:filter>
    </edit:form>
    <web:condition when="query:lang">
        <hr />
        <edit:form submit="loc-edit-save">
            <web:condition when="edit:saved">
                <web:redirectToSelf />
            </web:condition>
                
            <loc:edit bundleName="instance" languageName="query:lang" />
        </edit:form>
    </web:condition>
</web:frame>