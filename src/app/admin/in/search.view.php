<v:template src="~/templates/in-template.view">
	<web:frame title="Search">
        <edit:form submit="search">
            <ui:filter>
                <div class="gray-box">
                    <label for="query" class="w100">Content:</label>
                    <ui:textbox name="q" id="query" class="w500" />
                </div>
                <div class="gray-box">
                    <button name="search">Search</button>
                </div>
            </ui:filter>
            <hr />
            <web:condition when="query:q">
                <p:search text="query:q">
                    <ui:empty items="p:searchList">
                        <h4 class="warning">Nothing has been found.</h4>
                    </ui:empty> 
                    <ui:grid items="p:searchList" class="data-table standart wmax">
                        <ui:column header="Type" th-class="w40" value="p:searchType" />
                        <ui:column header="Subtype" th-class="w40" value="p:searchSubType" />
                        <ui:column header="Id" th-class="w30" td-class="id" value="p:searchId" />
                        <ui:column header="Name" td-class="mw200 ellipsis" td-title="p:searchName" value="p:searchName" />
                        <ui:columnTemplate header="Content" td-class="file-content">
                            <div class="file-content-in">
                                <div class="foo">
                                    <utils:escapeHtml output="content" value="p:searchContent" />
                                    <web:getProperty name="utils:content" isEvaluated="false" />
                                </div>
                            </div>
                        </ui:columnTemplate>
                    </ui:grid>
                </p:search>
            </web:condition>
            <web:condition when="query:q" isInverted="true">
                <h4 class="warning">Query is empty.</h4>
            </web:condition>
        </edit:form>
    </web:frame>
</v:template>