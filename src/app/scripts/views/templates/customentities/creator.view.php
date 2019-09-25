<ui:form method="POST">
    <div class="gray-box">
        <label class="w110" for="ce-creator-name">Entity Name:</label>
        <ui:textbox name="entity-name" />
    </div>
    <hr />
    <div class="ce-columns">
        <div class="ce-column">
            <strong>Column</strong>
            <div class="gray-box">
                <label class="w110" for="ce-creator-name">Name:</label>
                <ui:textbox name="column-name[]" />
            </div>
            <div class="gray-box">
                <label class="w110" for="ce-creator-name">Type:</label>
                <select name="column-type[]">
                    <option value="string">Text</option>
                    <option value="number">Number</option>
                    <option value="bool">Boolean</option>
                </select>
            </div>
        </div>
    </div>
    <hr />
    <div class="gray-box">
        <button type="button" name="ce-creator-add" class="ce-creator-add">Add Column</button>
        <button type="submit" name="ce-creator-save">Create</button>
    </div>
</ui:form>