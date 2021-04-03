<v:head>
	<meta name="viewport" content="width=device-width" />
	<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no" />
    <bs:resources />
    <fa5:resources />
    <js:cmsResources />
</v:head>
<v:template src="~/templates/template.view">
    <nav class="navbar navbar-expand navbar-dark bg-dark sticky-top">
        <bs:container fluid="true">
            <a href="/" class="navbar-brand">
                is4wfw
            </a>
            <div id="main-navbar" class="collapse navbar-collapse">
                <div class="mr-auto">
                    <wp:selectProject showMsg="false" useFrames="false" />

                    <ul class="navbar-nav d-none d-md-flex nav">

                    </ul>
                </div>
                
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <span class="navbar-text mr-3">
                            <fa5:icon name="user" />
                            <login:info field="username" />
                        </span>
                    </li>
                </ul>


                <ul class="navbar-nav">
                    <li class="nav-item">
                        <span class="navbar-text mr-3">
                            <fa5:icon name="server" />
                            <web:version />
                        </span>
                    </li>
                </ul>

                <ul class="navbar-nav">
                    <li class="nav-item">
                        <span class="navbar-text mr-3">
                            <fa5:icon name="database" />
                            <web:dbVersion />
                        </span>
                    </li>
                </ul>

                <ui:form class="form-inline">
                    <button name="logout" value="logout" class="btn btn-sm btn-outline-danger">
                        <fa5:icon name="sign-out-alt" />
                        <span class="d-none d-md-inline">
                            Log out
                        </span>
                    </button>
                </ui:form>
            </div>
        </bs:container>
    </nav>

    <bs:row class="no-gutters">
        <bs:column default="2">
            <nav>
                <v:panel class="cms-menu cms-menu-3" security:requirePerm="CMS.Hint">
                    <span class="menu-root">
                        <web:a pageId="~/in/hint.view" text="Documentation" />
                    </span>
                </v:panel>
                <v:panel class="cms-menu" security:requirePerm="CMS.Web">
                    <span class="menu-root"><web:a pageId="~/in/index.view" text="Web" /></span>
                    <m:xmlMenu file="~/templates/menus/web.xml" />
                </v:panel>
                <v:panel class="cms-menu cms-menu-2" security:requirePerm="CMS.Floorball">
                    <span class="menu-root">
                        <web:a pageId="~/in/floorball/seasons.view">
                            <web:static value="Floorball" lang="en" />
                            <web:static value="Florbal" lang="cs" />
                        </web:a>
                    </span>
                    <m:xmlMenu file="~/templates/menus/floorball.xml" />
                </v:panel>
                <v:panel class="cms-menu cms-menu-4" security:requirePerm="CMS.Settings">
                    <span class="menu-root">
                        <web:a pageId="~/in/personal-notes.view">
                            <web:static value="Settings" lang="en" />
                            <web:static value="Nastavení" lang="cs" />
                        </web:a>
                    </span>
                    <m:xmlMenu file="~/templates/menus/settings.xml" />
                </v:panel>
                <web:condition when="sys:hasAdminMenu">
                    <v:panel class="cms-menu cms-menu-5" security:requirePerm="CMS.AdminMenu">
                        <span class="menu-root">
                            <web:a pageId="~/in/personal-notes.view">
                                <web:static value="Custom" lang="en" />
                                <web:static value="Další" lang="cs" />
                            </web:a>
                        </span>
                        <sys:adminMenu url="~/in/admin-menu.view" />
                    </v:panel>
                </web:condition>
            </nav>
        </bs:column>
        <bs:column default="10">
            <div class="cms">
                <div id="cms-body" class="body">
                    <v:content />
                </div>
            </div>
        </bs:column>
    </bs:row>

</v:template>
