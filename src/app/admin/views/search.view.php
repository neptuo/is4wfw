<web:frame title="Search">
    <utils:arrayItem output="location" key-id="page-tlstart" key-name="Page (tag lib start)" />
    <utils:arrayItem output="location" key-id="page-tlend" key-name="Page (tag lib end)" />
    <utils:arrayItem output="location" key-id="page-head" key-name="Page (head)" />
    <utils:arrayItem output="location" key-id="page-content" key-name="Page (content)" />
    <utils:arrayItem output="location" key-id="template-content" key-name="Template (content)" />
    <utils:arrayItem output="location" key-id="textfile-content" key-name="Text File (content)" />

    <edit:form submit="search">
        <ui:filter session="location">
            <div class="gray-box">
                <label for="query" class="w100">Text:</label>
                <ui:textbox name="q" id="query" class="w500" />
            </div>
            <div class="gray-box">
                <label class="w100">Location:</label>
                <ui:checkboxlist name="location" source="utils:location" display="name" value="id" repeat="horizontal" class="mr-1" />
            </div>
            <div class="gray-box">
                <button name="search">Search</button>
            </div>
        </ui:filter>
    </edit:form>
    <hr />
    <web:condition when="query:q">
        <p:search text="query:q" location="session:location">
            <ui:empty items="p:searchList">
                <h4 class="warning">Nothing has been found.</h4>
            </ui:empty> 
            <ui:grid items="p:searchList" class="data-table standart wmax">
                <ui:column header="Type" th-class="w50" value="p:searchType" />
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
                <ui:columnTemplate>
                    <web:condition when="p:searchType" is="Template">
                        <web:a pageId="~/in/templates.view" param-id="p:searchId">
                            <img src="~/images/page_edi.png" title="Edit template" />
                        </web:a>
                    </web:condition>
                    <web:condition when="p:searchType" is="Text File">
                        <ui:form pageId="~/in/text-files.view">
                            <input type="hidden" name="file-id" value="<web:out text="p:searchId" />" />
                            <input type="hidden" name="edit-file" value="Edit" />
                            <input type="image" src="~/images/page_edi.png" name="edit-file" value="Edit" title="Edit file" />
                        </ui:form>
                    </web:condition>
                </ui:columnTemplate>
            </ui:grid>
        </p:search>
    </web:condition>
    <web:condition when="query:q" isInverted="true">
        <h4 class="warning">Query is empty.</h4>
    </web:condition>
</web:frame>