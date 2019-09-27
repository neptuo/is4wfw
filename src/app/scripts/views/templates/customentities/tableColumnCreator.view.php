<ui:form method="POST">
    <div class="ce-primary-key-1">
        <div class="gray-box">
            <label class="w90">Name:</label>
            <ui:textbox name="column-name" />
        </div>
        <div class="gray-box">
            <label class="w90">Type:</label>
            <ui:dropdownlist name="column-type" source="ce:tableColumnTypes" display="name" value="key" />
        </div>
        <div class="gray-box">
            <label>
                <ui:checkbox name="column-required" />
                Required
            </label>
        </div>
    </div>
    <div class="gray-box">
        <input type="hidden" name="ce-column-create" value="create" />
        <button type="submit" name="ce-column-creator-save" value="create">Create</button>
    </div>
</ui:form>