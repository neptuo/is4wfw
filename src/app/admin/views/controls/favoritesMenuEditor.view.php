<controls:modalForm id="favorites-editor" title="Favorites" size="sm" submit="favorites-save" submitText="Save">
    <web:condition when="edit:load">
        <edit:set name="value" value="var:userMenu" />
    </web:condition>
    <web:condition when="edit:save">
        <utils:concat output="userMenu" value1="edit:value" separator="," />
        <var:declare name="userMenu" value="utils:userMenu" scope="user" />
    </web:condition>
    <web:condition when="edit:saved">
        <web:redirectToSelf />
    </web:condition>

    <bs:formGroup>
        <ui:checkboxlist name="value" source="list:menu" display="text" value="id" />
    </bs:formGroup>
</controls:modalForm>

<js:style>
    
    #favorites-editor div label input {
        display: inline-block;
        margin-right: 8px;
    }

</js:style>
<js:script placement="tail">

    var $favoritesEditor = $("#favorites-editor");

    $favoritesEditor.find(".close-button").click(function() {
        $favoritesEditor.modal("hide");
    });

    $favoritesEditor.on("shown.bs.modal", function() {
        $favoritesEditor.find("[autofocus]").focus();
    });

    $("[data-modal='favorites-editor']").click(function(e) {
        $favoritesEditor.modal("show");
        e.preventDefault();
    });
    
</js:script>