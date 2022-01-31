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