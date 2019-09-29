<ui:form method="POST">
    <div class="gray-box">
        <label class="w90">Entity Name:</label>
        <ui:textbox name="entity-name" />
    </div>
    <div class="gray-box">
        <label class="w90">Description:</label>
        <ui:textbox name="entity-description" />
    </div>
    <hr />
    <div class="ce-primary-key-1">
        <div class="gray-box">
            <strong>Primary Key 1</strong>
        </div>
        <div class="gray-box">
            <label class="w90">Name:</label>
            <ui:textbox name="primary-key-1-name" />
        </div>
        <div class="gray-box">
            <label class="w90">Type:</label>
            <ui:dropdownlist name="primary-key-1-type" source="ced:tableColumnTypes" display="name" value="key" />
        </div>
        <div class="gray-box">
            <label>
                <ui:checkbox name="primary-key-1-identity" />
                Identity
            </label>
        </div>
        <hr />
    </div>
    <div class="ce-primary-key-2">
        <div class="gray-box">
            <strong>Primary Key 2</strong>
        </div>
        <div class="gray-box">
            <label class="w90">Name:</label>
            <ui:textbox name="primary-key-2-name" />
        </div>
        <div class="gray-box">
            <label class="w90">Type:</label>
            <ui:dropdownlist name="primary-key-2-type" source="ced:tableColumnTypes" display="name" value="key" />
        </div>
        <hr />
    </div>
    <div class="ce-primary-key-3">
        <div class="gray-box">
            <strong>Primary Key 3</strong>
        </div>
        <div class="gray-box">
            <label class="w90">Name:</label>
            <ui:textbox name="primary-key-3-name" />
        </div>
        <div class="gray-box">
            <label class="w90">Type:</label>
            <ui:dropdownlist name="primary-key-3-type" source="ced:tableColumnTypes" display="name" value="key" />
        </div>
        <hr />
    </div>
    <div class="gray-box">
        <input type="hidden" name="ce-manage-create" value="create" />
        <button type="submit" name="ce-creator-save" value="create">Create Table</button>
    </div>
</ui:form>