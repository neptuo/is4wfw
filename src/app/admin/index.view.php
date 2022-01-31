<var:declare name="templatePath" value="php:null" />
<router:fromPath path="var:virtualUrl">
    <router:file path="login.view" name="login">
        <views:login />
    </router:file>
    <router:file path="index.view">
        <web:redirectTo pageId="route:index" />
    </router:file>
    <router:directory path="in">
        <web:condition when="router:isEvaluate">
            <var:declare name="templatePath" value="~/templates/in-template.view" />
        </web:condition>
        
        <router:file path="index.view" name="index">
            <views:index />
        </router:file>
        <router:file path="hint.view" name="hint">
            <views:hint />
        </router:file>
        <router:file path="hint-properties.view" name="hintProperties">
            <views:hintProperties />
        </router:file>
        <router:file path="modules.view" name="modules">
            <views:modules />
        </router:file>
        
        <router:file path="custom-entities.view" name="customEntities">
            <views:customEntities />
        </router:file>
        <router:file path="custom-entity-audit.view" name="customEntityAudit">
            <views:customEntityAudit />
        </router:file>
        <router:file path="custom-entity-columns.view" name="customEntityColumns">
            <views:customEntityColumns />
        </router:file>
        <router:file path="custom-entity-localization.view" name="customEntityLocalization">
            <views:customEntityLocalization />
        </router:file>
        <router:file path="custom-forms.view" name="customForms">
            <views:customForms />
        </router:file>
    </router:directory>
</router:fromPath>

<web:switch when="router:hasMatch">
    <web:case is="php:true">
        <web:switch when="var:templatePath">
            <web:case is="php:null">
                <router:render />
            </web:case>
            <web:case>
                <v:template src="var:templatePath">
                    <router:render />
                </v:template>
            </web:case>
        </web:switch>
    </web:case>
    <web:case>
        <v:process />
    </web:case>
</web:switch>