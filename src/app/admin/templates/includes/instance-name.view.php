<div id="instance-name-editor" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <edit:form submit="instance-name-save">
            <web:condition when="edit:load">
                <edit:set name="name" value="var:is4wfw.instance.name" />
            </web:condition>
            <web:condition when="edit:save">
                <var:declare name="is4wfw.instance.name" value="edit:name" scope="application" />
            </web:condition>
            <web:condition when="edit:saved">
                <web:redirectToSelf />
            </web:condition>

            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                        Instance name
                    </h4>
                    <button type="button" class="close close-button">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    
                    <bs:formGroup label="Name">
                        <ui:textbox name="name" class="form-control" autofocus="autofocus" />
                    </bs:formGroup>
    
                </div>
                <div class="modal-footer">
                    <button name="instance-name-save" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-light close-button">Close</button>
                </div>
            </div>
        </edit:form>
    </div>
</div>

<js:script placement="tail">

    var $instanceNameEditor = $("#instance-name-editor");

    $instanceNameEditor.find(".close-button").click(function() {
        $instanceNameEditor.modal("hide");
    });

    $instanceNameEditor.on("shown.bs.modal", function() {
        $instanceNameEditor.find("[autofocus]").focus();
    });

    $("[data-modal='instance-name-editor']").click(function(e) {
        $instanceNameEditor.modal("show");
        e.preventDefault();
    });
    
</js:script>