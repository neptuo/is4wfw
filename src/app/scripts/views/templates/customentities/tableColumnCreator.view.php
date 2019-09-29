<ui:form method="POST">
    <div class="ce-primary-key-1">
        <div class="gray-box">
            <label class="w90">Name:</label>
            <ui:textbox name="column-name" />
        </div>
        <div class="gray-box">
            <label class="w90">Type:</label>
            <ui:dropdownlist name="column-type" source="ced:tableColumnTypes" display="name" value="key" data-toggler="type" />
        </div>
        <div class="gray-box">
            <label>
                <ui:checkbox name="column-required" />
                Required
            </label>
        </div>
        <div data-toggle="type-singlereference">
            <div class="gray-box">
                <strong>Reference</strong>
            </div>
            <div class="gray-box">
                <label class="w90">Table:</label>
                <ui:textbox name="column-singlerefence-table" />
            </div>
            <div class="gray-box">
                <label class="w90">Column:</label>
                <ui:textbox name="column-singlerefence-column" />
            </div>
        </div>
    </div>
    <div class="gray-box">
        <input type="hidden" name="ce-column-create" value="create" />
        <button type="submit" name="ce-column-creator-save" value="create">Create</button>
    </div>
</ui:form>