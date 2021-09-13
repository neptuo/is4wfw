<div id="<web:out text="template:id" />" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <edit:form submit="template:submit">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                        <web:out text="template:title" />
                    </h4>
                    <button type="button" class="close close-button">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <template:content />
                </div>
                <div class="modal-footer">
                    <bs:button name="template:submit">
                        <web:out text="template:submitText" />
                    </bs:button>
                    <bs:button type="button" color="light" class="close-button">Close</bs:button>
                </div>
            </div>
        </edit:form>
    </div>
</div>