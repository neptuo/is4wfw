<controls:modalForm id="readme-editor" title="Readme.txt" submit="readme-save" submitText="Save" size="lg">
    <web:condition when="edit:load">
        <edit:set name="value" value="sys:readme" />
    </web:condition>
    <web:condition when="edit:save">
        <web:setProperty prefix="sys" name="readme" value="edit:value" />
    </web:condition>
    <web:condition when="edit:saved">
        <web:redirectToSelf />
    </web:condition>

    <bs:formGroup>
        <ui:textarea name="value" class="form-control h300" autofocus="autofocus" />
    </bs:formGroup>
</controls:modalForm>

<js:script placement="tail">

    var $readmeEditor = $("#readme-editor");

    $readmeEditor.find(".close-button").click(function() {
        $readmeEditor.modal("hide");
    });

    $readmeEditor.on("shown.bs.modal", function() {
        $readmeEditor.find("[autofocus]").focus();
    });

    $("[data-modal='readme-editor']").click(function(e) {
        $readmeEditor.modal("show");
        e.preventDefault();
    });
    
</js:script>