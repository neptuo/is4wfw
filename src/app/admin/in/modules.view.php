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

<var:declare name="selfUrl" value="~/in/modules.view" />

<v:template src="~/templates/in-template.view">
    <div class="m-md-2 m-lg-4">
        <web:condition when="query:id">
            <bs:card title="New module" class="mb-4">
                <edit:form submit="save">
                    <module:edit>
                        <web:condition when="edit:saved">
                            <web:redirectTo pageId="var:selfUrl" />
                        </web:condition>

                        <bs:row>
                            <bs:column>
                                <bs:fieldValidator name="alias" cssClass="form-control">
                                    <bs:formGroup label="Alias">
                                        <ui:textbox name="alias" class="bs:fieldValidatorCssClass" />
                                        <bs:fieldValidationMessage name="alias" />
                                    </bs:formGroup>
                                </bs:fieldValidator>
                            </bs:column>
                            <bs:column>
                                <bs:fieldValidator name="alias" cssClass="custom-file">
                                    <bs:formGroup label="Upload zip">
                                        <div class="<web:out text="bs:fieldValidatorCssClass" />">
                                            <ui:filebox name="zip" class="custom-file-input" />
                                            <label class="custom-file-label" for="bs-2">Choose file</label>
                                        </div>
                                        <bs:fieldValidationMessage name="zip" />
                                    </bs:formGroup>
                                </bs:fieldValidator>
                            </bs:column>
                        </bs:row>

                        <bs:button name="save" value="save" text="Save" />
                        <web:a pageId="var:selfUrl" text="Close" class="btn btn-secondary" />
                    </module:edit>
                </edit:form>
            </bs:card>
        </web:condition>

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
                            <button name="delete" value="delete" class="icon-button confirm" title="Delete module '<web:out text="module:alias" />'">
                                <fa5:icon name="trash-alt" class="text-danger" />
                            </button>
                        </ui:form>
                    </ui:columnTemplate>
                </ui:grid>
            </module:list>

            <ui:form class="mt-3">
                <bs:button name="rebuild" value="rebuild" text="Rebuild initializers" class="confirm" title="Rebuild initializers" />
                <web:a pageId="var:selfUrl" param-id="new" text="New module" class="btn btn-outline-primary" />
            </ui:form>
        </bs:card>
    </div>
</v:template>