<ui:form method="POST">
    <div class="gray-box">
        <label class="w90">Entity Name:</label>
        <ui:textbox name="entity-name" class="w200" />
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
    <div class="ce-primary-key-1 float-left">
        <div class="gray-box">
            <strong>Primary Key 1</strong>
        </div>
        <div class="gray-box">
            <label class="w90">Name:</label>
            <ui:textbox name="primary-key-1-name" />
        </div>
        <div class="gray-box">
            <label class="w90">Type:</label>
            <ui:dropdownlist name="primary-key-1-type" source="ced:tablePrimaryKeyTypes" display="name" value="key" data-toggler="type1" />
        </div>
        <div data-toggle="type1-int">
            <div class="gray-box">
                <strong>Integer</strong>
            </div>
            <div class="gray-box">
                <label class="w90">Size:</label>
                <ui:textbox name="primary-key-1-int-size" default="11" />
            </div>
            <div class="gray-box">
                <label>
                    <ui:checkbox name="primary-key-1-int-identity" default="1" />
                    Identity
                </label>
            </div>
        </div>
        <div data-toggle="type1-float">
            <div class="gray-box">
                <strong>Float</strong>
            </div>
            <div class="gray-box">
                <label class="w90">Size:</label>
                <ui:textbox name="primary-key-1-float-size" default="10" />
            </div>
            <div class="gray-box">
                <label class="w90">Decimals:</label>
                <ui:textbox name="primary-key-1-float-decimals" default="2" />
            </div>
        </div>
        <div data-toggle="type1-double">
            <div class="gray-box">
                <strong>Double</strong>
            </div>
            <div class="gray-box">
                <label class="w90">Size:</label>
                <ui:textbox name="primary-key-1-float-size" default="16" />
            </div>
            <div class="gray-box">
                <label class="w90">Decimals:</label>
                <ui:textbox name="primary-key-1-float-decimals" default="2" />
            </div>
        </div>
        <div data-toggle="type1-varchar">
            <div class="gray-box">
                <strong>Text (varchar)</strong>
            </div>
            <div class="gray-box">
                <label class="w90">Size:</label>
                <ui:textbox name="primary-key-1-varchar-size" default="50" />
            </div>
        </div>
        <div data-toggle="type1-singlereference">
            <div class="gray-box">
                <strong>Single Reference</strong>
            </div>
            <div class="gray-box">
                <label class="w90">Table:</label>
                <ui:textbox name="primary-key-1-singlerefence-table" />
            </div>
            <div class="gray-box">
                <label class="w90">Column:</label>
                <ui:textbox name="primary-key-1-singlerefence-column" />
            </div>
        </div>
    </div>
    <div class="ce-primary-key-2 float-left">
        <div class="gray-box">
            <strong>Primary Key 2</strong>
        </div>
        <div class="gray-box">
            <label class="w90">Name:</label>
            <ui:textbox name="primary-key-2-name" />
        </div>
        <div class="gray-box">
            <label class="w90">Type:</label>
            <ui:dropdownlist name="primary-key-2-type" source="ced:tablePrimaryKeyTypes" display="name" value="key" data-toggler="type2" />
        </div>
        <div data-toggle="type2-int">
            <div class="gray-box">
                <strong>Integer</strong>
            </div>
            <div class="gray-box">
                <label class="w90">Size:</label>
                <ui:textbox name="primary-key-2-int-size" default="11" />
            </div>
        </div>
        <div data-toggle="type2-float">
            <div class="gray-box">
                <strong>Float</strong>
            </div>
            <div class="gray-box">
                <label class="w90">Size:</label>
                <ui:textbox name="primary-key-2-float-size" default="10" />
            </div>
            <div class="gray-box">
                <label class="w90">Decimals:</label>
                <ui:textbox name="primary-key-2-float-decimals" default="2" />
            </div>
        </div>
        <div data-toggle="type2-double">
            <div class="gray-box">
                <strong>Double</strong>
            </div>
            <div class="gray-box">
                <label class="w90">Size:</label>
                <ui:textbox name="primary-key-2-float-size" default="16" />
            </div>
            <div class="gray-box">
                <label class="w90">Decimals:</label>
                <ui:textbox name="primary-key-2-float-decimals" default="2" />
            </div>
        </div>
        <div data-toggle="type2-varchar">
            <div class="gray-box">
                <strong>Text (varchar)</strong>
            </div>
            <div class="gray-box">
                <label class="w90">Size:</label>
                <ui:textbox name="primary-key-2-varchar-size" default="50" />
            </div>
        </div>
        <div data-toggle="type2-singlereference">
            <div class="gray-box">
                <strong>Single Reference</strong>
            </div>
            <div class="gray-box">
                <label class="w90">Table:</label>
                <ui:textbox name="primary-key-2-singlerefence-table" />
            </div>
            <div class="gray-box">
                <label class="w90">Column:</label>
                <ui:textbox name="primary-key-2-singlerefence-column" />
            </div>
        </div>
    </div>
    <div class="ce-primary-key-3 float-left">
        <div class="gray-box">
            <strong>Primary Key 3</strong>
        </div>
        <div class="gray-box">
            <label class="w90">Name:</label>
            <ui:textbox name="primary-key-3-name" />
        </div>
        <div class="gray-box">
            <label class="w90">Type:</label>
            <ui:dropdownlist name="primary-key-3-type" source="ced:tablePrimaryKeyTypes" display="name" value="key" data-toggler="type3" />
        </div>
        <div data-toggle="type3-int">
            <div class="gray-box">
                <strong>Integer</strong>
            </div>
            <div class="gray-box">
                <label class="w90">Size:</label>
                <ui:textbox name="primary-key-3-int-size" default="11" />
            </div>
        </div>
        <div data-toggle="type3-float">
            <div class="gray-box">
                <strong>Float</strong>
            </div>
            <div class="gray-box">
                <label class="w90">Size:</label>
                <ui:textbox name="primary-key-3-float-size" default="10" />
            </div>
            <div class="gray-box">
                <label class="w90">Decimals:</label>
                <ui:textbox name="primary-key-3-float-decimals" default="2" />
            </div>
        </div>
        <div data-toggle="type3-double">
            <div class="gray-box">
                <strong>Double</strong>
            </div>
            <div class="gray-box">
                <label class="w90">Size:</label>
                <ui:textbox name="primary-key-3-float-size" default="16" />
            </div>
            <div class="gray-box">
                <label class="w90">Decimals:</label>
                <ui:textbox name="primary-key-3-float-decimals" default="2" />
            </div>
        </div>
        <div data-toggle="type3-varchar">
            <div class="gray-box">
                <strong>Text (varchar)</strong>
            </div>
            <div class="gray-box">
                <label class="w90">Size:</label>
                <ui:textbox name="primary-key-3-varchar-size" default="50" />
            </div>
        </div>
        <div data-toggle="type3-singlereference">
            <div class="gray-box">
                <strong>Single Reference</strong>
            </div>
            <div class="gray-box">
                <label class="w90">Table:</label>
                <ui:textbox name="primary-key-3-singlerefence-table" />
            </div>
            <div class="gray-box">
                <label class="w90">Column:</label>
                <ui:textbox name="primary-key-3-singlerefence-column" />
            </div>
        </div>
    </div>
    <div class="clear"></div>
    <hr />
    <div class="gray-box">
        <input type="hidden" name="save" value="save-close" />
        <button type="submit" name="save" value="save-close">Create Table</button>
    </div>
</ui:form>