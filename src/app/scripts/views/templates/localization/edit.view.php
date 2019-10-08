<ui:form method="POST">

    <div class="loc-items">
        <div class="loc-header">
            <div class="gray-box-float">
                <strong class="w400">Key:</strong>
            </div>
            <div class="gray-box-float">
                <strong class="w400">
                Value
                (<web:getProperty name="query:lang" />):
                </strong>
            </div>
            <div class="clear"></div>
        </div>
        <ui:forEach items="loc:editItems">
            <div class="loc-item">
                <div class="gray-box-float">
                    <ui:textbox name="key" nameIndex="loc:editItemIndex" class="w400" />
                </div>
                <div class="gray-box-float">
                    <ui:textbox name="value" nameIndex="loc:editItemIndex" class="w400" />
                </div>
                <div class="clear"></div>
            </div>
        </ui:forEach>
    </div>

    <div class="gray-box">
        <button type="button" data-duplicator=".loc-items .loc-item:first">Add row</button>

        <input type="hidden" name="loc-edit-save" value="loc-edit-save" />
        <button type="submit" name="loc-edit-save" value="loc-edit-save">Save</button>
    </div>
</ui:form>