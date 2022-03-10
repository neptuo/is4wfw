<controls:modalForm id="instance-name-editor" title="Instance name" submit="instance-name-save" submitText="Save">
    <web:condition when="edit:load">
        <edit:set name="name" value="var:is4wfw.instance.name" />
    </web:condition>
    <web:condition when="edit:save">
        <var:declare name="is4wfw.instance.name" value="edit:name" scope="application" />
    </web:condition>
    <web:condition when="edit:saved">
        <web:redirectToSelf />
    </web:condition>

    <bs:formGroup label="Name">
        <ui:textbox name="name" class="form-control" autofocus="autofocus" />
    </bs:formGroup>
</controls:modalForm>

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