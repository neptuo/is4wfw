<v:head>
	<meta name="viewport" content="width=device-width" />
	<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no" />
    <bs:resources />
    <fa5:resources />
    <js:cmsResources />
</v:head>
<v:template src="~/templates/template.view">
        <bs:row class="no-gutters">
            <bs:column default="2">
                <nav class="navbar navbar-expand navbar-dark bg-dark sticky-top">
                    <a href="~/in/index.view" class="navbar-brand mr-4">
                        &lt;is4wfw /&gt;
                    </a>
                </nav>
                    
                <div class="border-right shadow main-menu-background">
                    <nav class="py-3 px-2">
                        <v:panel class="mb-3 cms-menu cms-menu-3" security:requirePerm="CMS.Hint">
                            <div class="menu-root">
                                <web:a pageId="~/in/hint.view" text="Documentation" />
                            </div>
                        </v:panel>
                        <v:panel class="mb-3 cms-menu" security:requirePerm="CMS.Web">
                            <div class="menu-root">
                                <web:a pageId="~/in/index.view" text="Web" />
                            </div>
                            <m:xmlMenu file="~/templates/menus/web.xml" />
                        </v:panel>
                        <v:panel class="mb-3 cms-menu cms-menu-2" security:requirePerm="CMS.Floorball">
                            <div class="menu-root">
                                <web:a pageId="~/in/floorball/seasons.view">
                                    <web:static value="Floorball" lang="en" />
                                    <web:static value="Florbal" lang="cs" />
                                </web:a>
                            </div>
                            <m:xmlMenu file="~/templates/menus/floorball.xml" />
                        </v:panel>
                        <v:panel class="mb-3 cms-menu cms-menu-4" security:requirePerm="CMS.Settings">
                            <div class="menu-root">
                                <web:a pageId="~/in/personal-notes.view">
                                    <web:static value="Settings" lang="en" />
                                    <web:static value="Nastavení" lang="cs" />
                                </web:a>
                            </div>
                            <m:xmlMenu file="~/templates/menus/settings.xml" />
                        </v:panel>
                        <web:condition when="sys:hasAdminMenu">
                            <v:panel class="mb-3 cms-menu cms-menu-5" security:requirePerm="CMS.AdminMenu">
                                <div class="menu-root">
                                    <web:a pageId="~/in/personal-notes.view">
                                        <web:static value="Custom" lang="en" />
                                        <web:static value="Další" lang="cs" />
                                    </web:a>
                                </div>
                                <sys:adminMenu url="~/in/admin-menu.view" />
                            </v:panel>
                        </web:condition>
                    </nav>
                </div>
            </bs:column>

            <bs:column>
                <div class="navbar navbar-dark bg-dark sticky-top">
                    <div class="mr-auto">
                        <wp:selectProject showMsg="false" useFrames="false" label=" " />
                    </div>
                    
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
                                r<web:dbVersion />
                            </span>
                        </li>
                    </ul>
                    

                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <span class="navbar-text mr-3">
                                <fa5:icon name="user" />
                                <login:info field="username" />
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

                <div class="p-3 cms">
                    <div id="cms-body" class="body">
                        <v:content />
                    </div>
                </div>
            </bs:column>
        </bs:row>

</v:template>
