<ui:form method="POST">
    <div class="gray-box">
        <label class="w90">Entity Name:</label>
        <ui:textbox name="entity-name" />
    </div>
    <div class="gray-box">
        <label class="w90">Engine:</label>
        <ui:dropdownlist name="entity-engine" source="ced:tableEngines" display="name" value="key" />
    </div>
    <div class="gray-box">
        <label class="w90">Audit:</label>
        <label>
            <ui:checkbox name="entity-audit-log" />
            Generate audit log
        </label>
    </div>
    <div class="gray-box">
        <label class="block">Description:</label>
        <ui:textarea name="entity-description" class="w700 h100" />
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
        <input type="hidden" name="save" value="save-close" />
        <button type="submit" name="save" value="save-close">Create Table</button>
    </div>
</ui:form>