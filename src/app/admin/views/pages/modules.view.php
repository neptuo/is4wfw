<web:condition when="post:rebuild">
    <module:rebuildInitializers>
        <web:redirectToSelf />
    </module:rebuildInitializers>
</web:condition>
<web:condition when="post:repostupdate">
    <module:runPostUpdate>
        <web:redirectToSelf />
    </module:runPostUpdate>
</web:condition>
<web:condition when="post:import">
    <module:importExisting>
        <web:redirectToSelf />
    </module:importExisting>
</web:condition>
<web:condition when="post:delete">
    <module:delete id="post:id">
        <web:redirectToSelf />
    </module:delete>
</web:condition>

<var:declare name="selfUrl" value="route:modules" />

<div class="m-md-2 m-lg-4">
    <admin:edit id="query:id">
        <utils:concat output="title" value1="admin:editTitle" value2=" module" />
        <controls:modalForm id="module-edit" title="utils:title" submit="save" submitText="Save" closeUrl="var:selfUrl">
            <module:edit id="admin:editId">
                <web:condition when="edit:saved">
                    <web:redirectTo pageId="var:selfUrl" />
                </web:condition>

                <ui:editable is="admin:new">
                    <bs:formGroup label="Alias" field="alias">
                        <ui:textbox name="alias" class="bs:fieldValidatorCssClass" />
                    </bs:formGroup>
                </ui:editable>
                <bs:fieldValidator name="zip" cssClass="custom-file">
                    <bs:formGroup label="Upload zip">
                        <div class="<web:out text="bs:fieldValidatorCssClass" />">
                            <ui:filebox name="zip" class="custom-file-input" />
                            <label class="custom-file-label" for="bs-2">Choose file</label>
                        </div>
                        <bs:fieldValidationMessage name="zip" />
                    </bs:formGroup>
                </bs:fieldValidator>
            </module:edit>
        </controls:modalForm>
        <js:script placement="tail">
            $("#module-edit").modal("show");
        </js:script>
    </admin:edit>
    
    <admin:edit id="query:ghupdate">
        <controls:modalForm id="ghupdate-modal" title="Download update from GitHub" submit="ghdownload" closeUrl="var:selfUrl" size="lg">
            <web:condition when="edit:save">
                <module:gitHubUpdate moduleId="admin:editId" updateId="post:ghdownload" userName="post:ghUserName" accessToken="post:ghAccessToken">
                    <web:redirectTo pageId="var:selfUrl" />
                </module:gitHubUpdate>
            </web:condition>
                
            <bs:row class="form-row align-items-end">
                <bs:column>
                    <bs:formGroup label="User name:">
                        <input type="text" name="ghUserName" value="<web:out text="post:ghUserName" />" autocomplete="off" class="form-control" />
                    </bs:formGroup>
                </bs:column>
                <bs:column>
                    <bs:formGroup label="Access token:">
                        <input type="password" name="ghAccessToken" value="<web:out text="post:ghAccessToken" />" autocomplete="off" class="form-control" />
                    </bs:formGroup>
                </bs:column>
                <bs:column default="auto">
                    <bs:formGroup>
                        <bs:button>
                            <fa5:icon name="search" />
                            Search
                        </bs:button>
                    </bs:formGroup>
                </bs:column>
            </bs:row>

            <web:condition when="edit:render">
                <module:gitHubUpdateList moduleId="admin:editId" userName="post:ghUserName" accessToken="post:ghAccessToken">
                    <ui:empty items="module:gitHubUpdateList">
                        <bs:alert color="warning">
                            No releases...
                        </bs:alert>
                    </ui:empty>
                    <ui:grid class="table table-striped" thead-class="table-dark" items="module:gitHubUpdateList">
                        <utils:formatBytes output="gitHubUpdateSize" value="module:gitHubUpdateSize" />

                        <ui:column header="Version" value="module:gitHubUpdateVersion" />
                        <ui:column header="Name" value="module:gitHubUpdateName" />
                        <ui:column header="Published at" value="module:gitHubUpdatePublishedAt" />
                        <ui:column header="Size" value="utils:gitHubUpdateSize" />
                        <ui:columnTemplate header="">
                            <a target="_blank" href="<web:out text="module:gitHubUpdateHtmlUrl" />" class="text-dark" title="Release notes">
                                <fa5:icon prefix="fas" name="external-link-alt" />
                            </a>
                            <button name="ghdownload" value="<web:out text="module:gitHubUpdateId" />" class="icon-button confirm" title="Download update '<web:out text="module:gitHubUpdateVersion" />', file '<web:out text="module:gitHubUpdateName" />'">
                                <fa5:icon prefix="fas" name="cloud-download-alt" />
                            </button>
                        </ui:columnTemplate>
                    </ui:grid>
                </module:gitHubUpdateList>
            </web:condition>
        </controls:modalForm>
        <js:script placement="tail">
            $("#ghupdate-modal").modal("show");
        </js:script>
    </admin:edit>

    <bs:card title="Modules">
        <module:list>
            <ui:empty items="module:list">
                <bs:alert color="warning">
                    No modules installed...
                </bs:alert>
            </ui:empty>
            <ui:grid class="table table-striped" thead-class="table-dark" items="module:list">
                <ui:columnTemplate header="Alias">
                    <web:condition when="module:isSupported" is="php:false">
                        <fa5:icon name="times" class="text-danger" title="Module is not supported on current version of instance" />
                    </web:condition>
                    <web:out text="module:alias" />
                </ui:columnTemplate>
                <ui:column header="Id" value="module:id" />
                <ui:column header="Name" value="module:name" />
                <ui:column header="Version" value="module:version" />
                <ui:columnTemplate>
                    <web:condition when="module:gitHubRepositoryName">
                        <web:a pageId="var:selfUrl" class="icon-button" param-ghupdate="module:id">
                            <fa5:icon prefix="fab" name="github" class="text-dark" />
                        </web:a>
                    </web:condition>
                </ui:columnTemplate>
                <ui:columnTemplate>
                    <web:condition when="module:canEdit">
                        <web:a pageId="var:selfUrl" class="icon-button" param-id="module:id">
                            <fa5:icon name="pencil-alt" class="text-dark" />
                        </web:a>
                        <ui:form class="d-inline">
                            <input type="hidden" name="id" value="<web:out text="module:id" />" />
                            <button name="delete" value="delete" class="icon-button confirm" title="Delete module '<web:out text="module:alias" />'">
                                <fa5:icon name="trash-alt" class="text-danger" />
                            </button>
                        </ui:form>
                    </web:condition>
                </ui:columnTemplate>
            </ui:grid>
        </module:list>

        <ui:form class="d-flex justify-content-between mt-3">
            <div>
                <web:a pageId="var:selfUrl" param-id="new" text="New module" class="btn btn-primary" />
            </div>
            <div>
                <bs:button name="rebuild" value="rebuild" text="Rebuild initializers" class="confirm" isOutline="true" title="Rebuild initializers" />
                <bs:button name="repostupdate" value="repostupdate" text="Re-run post update" class="confirm" isOutline="true" title="Re-run post update on all modules" />
                <bs:button name="import" value="import" text="Import from file system" class="confirm" isOutline="true" title="Import modules from file system" />
            </div>
        </ui:form>
    </bs:card>
</div>