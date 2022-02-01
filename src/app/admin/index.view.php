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
        
        <!-- General -->
        <router:file path="index.view" name="index">
            <views:index />
        </router:file>
        <router:file path="hint.view" name="hint">
            <views:hint />
        </router:file>
        <router:file path="hint-properties.view" name="hintProperties">
            <views:hintProperties />
        </router:file>

        <!-- Web -->
        <router:file path="pages.view" name="pages">
            <views:pages />
        </router:file>
        <router:file path="text-files.view" name="textFiles">
            <views:textFiles />
        </router:file>
        <router:file path="templates.view" name="templates">
            <views:templates />
        </router:file>
        <router:file path="search.view" name="search">
            <views:search />
        </router:file>

        <!-- Articles -->
        <router:file path="articles.view" name="articles">
            <views:articles />
        </router:file>
        <router:file path="article-detail.view" name="articleDetail">
            <views:articleDetail />
        </router:file>
        <router:file path="article-labels.view" name="articleLabels">
            <views:articleLabels />
        </router:file>
        <router:file path="article-lines.view" name="articleLines">
            <views:articleLines />
        </router:file>
        <router:file path="article-line-detail.view" name="articleLinesDetail">
            <views:articleLinesDetail />
        </router:file>
        
        <!-- Custom entities -->
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
        
        <!-- Settings -->
        <router:file path="modules.view" name="modules">
            <views:modules />
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