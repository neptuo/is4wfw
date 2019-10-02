<ui:form method="POST">
    <div class="gray-box">
        <label class="w90">Columns:</label>
    </div>
    <div class="gray-box">
        <ui:checkboxlist name="columns" source="ced:tableLocalizationColumns" display="name" value="name" />
    </div>
    <div class="gray-box">
        <input type="hidden" name="ced-localizable-save" value="save" />
        <button type="submit" name="ced-localizable-save" value="save">Save</button>
    </div>
</ui:form>