<div id="<web:out text="template:id" />" data-backdrop="static" class="modal fade <web:out text="template:class" />" tabindex="-1" role="dialog">
    <utils:clear output="modalFormSize" />
    <web:condition when="template:size">
        <utils:concat output="modalFormSize" value1="modal-" value2="template:size" />
    </web:condition>
    <div class="modal-dialog <web:out text="utils:modalFormSize" />" role="document">
        <edit:form submit="template:submit">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                        <web:out text="template:title" />
                    </h4>
                </div>
                <div class="modal-body">
                    <template:content />
                </div>
                <div class="modal-footer">
                    <web:condition when="template:submitText">
                        <bs:button name="template:submit">
                            <web:out text="template:submitText" />
                        </bs:button>
                    </web:condition>
                    <web:condition when="template:closeUrl">
                        <web:a pageId="template:closeUrl" class="btn btn-light close-button" text="Close" />
                    </web:condition>
                    <web:condition when="template:closeUrl" isInverted="true">
                        <bs:button type="button" color="light" class="close-button">Close</bs:button>
                    </web:condition>
                </div>
            </div>
        </edit:form>
    </div>
</div>