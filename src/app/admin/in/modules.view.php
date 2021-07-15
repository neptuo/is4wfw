<web:condition when="post:rebuild">
    <module:rebuildInitializers>
        <web:redirectToSelf />
    </module:rebuildInitializers>
</web:condition>
<web:condition when="post:delete">
    <module:delete id="post:id">
        <web:redirectToSelf />
    </module:delete>
</web:condition>

<v:template src="~/templates/in-template.view">
    <div class="m-md-2 m-lg-4">
        <bs:card title="Modules">
            <module:list>
                <ui:empty items="module:list">
                    <bs:alert color="warning">
                        No modules installed...
                    </bs:alert>
                </ui:empty>
                <ui:grid class="table table-striped" thead-class="table-dark" items="module:list">
                    <ui:column header="Alias" value="module:alias" />
                    <ui:column header="Id" value="module:id" />
                    <ui:column header="Name" value="module:name" />
                    <ui:columnTemplate>
                        <ui:form class="d-inline">
                            <input type="hidden" name="id" value="<web:out text="module:id" />" />
                            <button name="delete" value="delete" class="icon-button confirm" title="Delete module">
                                <fa5:icon name="trash-alt" class="text-danger" />
                            </button>
                        </ui:form>
                    </ui:columnTemplate>
                </ui:grid>
            </module:list>

            <ui:form class="mt-3">
                <bs:button name="rebuild" value="rebuild" text="Rebuild initializers" class="confirm" title="Rebuild initializers" />
            </ui:form>
        </bs:card>
    </div>
</v:template>