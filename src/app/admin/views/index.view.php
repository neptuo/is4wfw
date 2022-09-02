<var:declare name="templateName" value="php:null" />
<router:fromPath path="var:virtualUrl">
    <router:file path="login.view" name="login">
        <views:login />
    </router:file>
    <router:file path="index.view">
        <web:redirectTo pageId="route:index" />
    </router:file>
    <router:directory path="in">
        <web:condition when="router:isEvaluate">
            <var:declare name="templateName" value="main" />
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
        <router:file path="web-projects.view" name="webProjects">
            <views:webProjects />
        </router:file>
        <router:file path="web-forwards.view" name="webForwards">
            <views:webForwards />
        </router:file>
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
        <router:file path="files.view" name="files">
            <views:files />
        </router:file>
        <router:file path="embedded-resources.view" name="embeddedResources">
            <views:embeddedResources />
        </router:file>
        <router:file path="guestbooks.view" name="guestbooks">
            <views:guestbooks />
        </router:file>
        <router:file path="inquiries.view" name="inquiries">
            <views:inquiries />
        </router:file>
        <router:file path="url-cache.view" name="urlCache">
            <views:urlCache />
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
        <router:file path="update.view" name="update">
            <views:update />
        </router:file>
        <router:file path="modules.view" name="modules">
            <views:modules />
        </router:file>
        <router:file path="application-log.view" name="applicationLog">
            <views:applicationLog />
        </router:file>
        <router:file path="database-connections.view" name="databaseConnections">
            <views:databaseConnections />
        </router:file>
        <router:file path="keywords.view" name="keywords">
            <views:keywords />
        </router:file>
        <router:file path="role-cache.view" name="roleCache">
            <views:roleCache />
        </router:file>
        <router:file path="admin-menu.view" name="adminMenu">
            <views:adminMenu />
        </router:file>
        <router:file path="edit-admin-menu.view" name="editAdminMenu">
            <views:editAdminMenu />
        </router:file>
        
        <!-- Languages -->
        <router:file path="languages.view" name="languages">
            <views:languages />
        </router:file>
        <router:file path="localization-bundles.view" name="localizationBundles">
            <views:localizationBundles />
        </router:file>
        <router:file path="localization-bundle-download.view" name="localizationBundleDownload">
            <views:localizationBundleDownload />
        </router:file>
        
        <!-- Users -->
        <router:file path="users.view" name="users">
            <views:users />
        </router:file>
        <router:file path="user-properties.view" name="userProperties">
            <views:userProperties />
        </router:file>
        <router:file path="user-groups.view" name="userGroups">
            <views:userGroups />
        </router:file>
        <router:file path="user-group-perms-hint.view" name="userGroupPermsHint">
            <views:userGroupPermsHint />
        </router:file>
        <router:file path="user-log.view" name="userLog">
            <views:userLog />
        </router:file>
        <router:file path="personal-notes.view" name="personalNotes">
            <views:personalNotes />
        </router:file>
        <router:file path="personal-properties.view" name="personalProperties">
            <views:personalProperties />
        </router:file>
        <router:file path="default-properties.view" name="defaultProperties">
            <views:defaultProperties />
        </router:file>

        <!-- Floorball -->
        <router:directory path="floorball">
            <web:condition when="router:isEvaluate">
                <var:declare name="templateName" value="floorball" />
            </web:condition>
        
            <router:file path="projects.view" name="floorballProjects">
                <floorball:projects />
            </router:file>
            <router:file path="seasons.view" name="floorballSeasons">
                <floorball:seasons />
            </router:file>
            <router:file path="tables.view" name="floorballTables">
                <floorball:tables />
            </router:file>
            <router:file path="tables-content.view" name="floorballTablesContent">
                <floorball:tablesContent />
            </router:file>
            <router:file path="rounds.view" name="floorballRounds">
                <floorball:rounds />
            </router:file>
            <router:file path="teams.view" name="floorballTeams">
                <floorball:teams />
            </router:file>
            <router:file path="matches.view" name="floorballMatches">
                <floorball:matches />
            </router:file>
            <router:file path="players.view" name="floorballPlayers">
                <floorball:players />
            </router:file>
        </router:directory>
    </router:directory>
    
    <router:directory path="api">
        <router:file path="intellisense.view">
            <api:intellisense />
        </router:file>
    </router:directory>
</router:fromPath>

<web:switch when="var:templateName">
    <web:case is="php:null">
        <router:render />
    </web:case>
    <web:case>
        <layouts:include identifier="var:templateName">
            <router:render />
        </layouts:include>
    </web:case>
</web:switch>