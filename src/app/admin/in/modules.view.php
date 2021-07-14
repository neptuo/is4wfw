<web:condition when="post:rebuild">
    <module:rebuildInitializers>
        <web:redirectToSelf />
    </module:rebuildInitializers>
</web:condition>

<v:template src="~/templates/in-template.view">
    <div class="m-md-2 m-lg-4">
        <bs:card title="Modules">
            <module:list>
                <ui:grid class="table table-striped" thead-class="table-dark" items="module:list">
                    <ui:column header="Alias" value="module:alias" />
                    <ui:column header="Id" value="module:id" />
                    <ui:column header="Name" value="module:name" />
                    <ui:columnTemplate>
                        <fa5:icon name="trash-alt" class="text-danger" />
                    </ui:columnTemplate>
                </ui:grid>
            </module:list>

            <ui:form class="mt-3">
                <bs:button name="rebuild" value="rebuild" text="Rebuild initializers" class="confirm" title="Rebuild initializers" />
            </ui:form>
        </bs:card>
    </div>
</v:template>