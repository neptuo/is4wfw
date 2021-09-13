<div id="<web:out text="template:id" />" data-backdrop="static" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
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
                    <bs:button name="template:submit">
                        <web:out text="template:submitText" />
                    </bs:button>
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