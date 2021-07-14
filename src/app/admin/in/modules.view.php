<v:template src="~/templates/in-template.view">
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
    </bs:card>
</v:template>