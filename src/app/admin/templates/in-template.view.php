<login:init group="web-admins" />
<login:refresh group="web-admins">
    <var:declare name="adminLastPage" value="web:currentPath" scope="session" />
    <web:redirectTo pageId="~/login.view" />
</login:refresh>
<web:condition when="post:logout" is="logout">
    <login:logout group="web-admins">
        <var:declare name="adminLastPage" value="web:currentPath" scope="session" />
        <web:redirectTo pageId="~/login.view" />
    </login:logout>
</web:condition>
<v:head>
	<meta name="viewport" content="width=device-width" />
	<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no" />
    <bs:resources />
    <fa5:resources />
    <js:cmsResources />
</v:head>
<v:template src="~/templates/template.view">
    <bs:row class="no-gutters main-layout">
        <var:declare name="mainMenuColCssClass" value="main-menu-col" />
        <web:condition when="cookie:mainMenu" is="collapsed">
            <utils:concat output="mainMenuColCssClass" separator=" " value1="var:mainMenuColCssClass" value2="main-menu-collapsed" />
            <var:declare name="mainMenuColCssClass" value="utils:mainMenuColCssClass" />
        </web:condition>
        <bs:column default="3" large="2" class="var:mainMenuColCssClass">
            <nav class="navbar navbar-expand navbar-dark bg-dark sticky-top">
                <a href="~/in/index.view" class="navbar-brand d-none d-md-block mr-auto">
                    &lt;is4wfw /&gt;
                </a>
                <button class="navbar-toggler" type="button"><span class="navbar-toggler-icon"></span></button>
            </nav>
                
            <div class="border-right shadow main-menu main-menu-background">
                <nav class="py-3 px-2">
                    <v:panel class="mb-3 cms-menu cms-menu-6 d-block d-md-none">
                        <div class="menu-root">
                            <web:a pageId="~/in/index.view" title="Home">
                                <span>Home</span>
                            </web:a>
                        </div>
                    </v:panel>
                    <v:panel class="mb-3 cms-menu cms-menu-3" security:requirePerm="CMS.Hint">
                        <div class="menu-root">
                            <web:a pageId="~/in/hint.view" title="Documentation">
                                <span>Documentation</span>
                            </web:a>
                        </div>
                    </v:panel>
                    <v:panel class="mb-3 cms-menu" security:requirePerm="CMS.Web">
                        <div class="menu-root">
                            <web:a pageId="~/in/index.view" title="Web">
                                <span>Web</span>
                            </web:a>
                        </div>
                        <m:xmlMenu file="~/templates/menus/web.xml" />
                    </v:panel>
                    <v:panel class="mb-3 cms-menu cms-menu-2" security:requirePerm="CMS.Floorball">
                        <div class="menu-root">
                            <web:a pageId="~/in/floorball/seasons.view" title="Floorball">
                                <span>Floorball</span>
                            </web:a>
                        </div>
                        <m:xmlMenu file="~/templates/menus/floorball.xml" />
                    </v:panel>
                    <v:panel class="mb-3 cms-menu cms-menu-4" security:requirePerm="CMS.Settings">
                        <div class="menu-root">
                            <web:a pageId="~/in/personal-notes.view" title="Settings">
                                <span>Settings</span>
                            </web:a>
                        </div>
                        <m:xmlMenu file="~/templates/menus/settings.xml" />
                    </v:panel>
                    <web:condition when="sys:hasAdminMenu">
                        <v:panel class="mb-3 cms-menu cms-menu-5" security:requirePerm="CMS.AdminMenu">
                            <div class="menu-root">
                                <web:a pageId="~/in/personal-notes.view" title="Custom">
                                    <span>Custom</span>
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
                
                <ul class="navbar-nav flex-row d-none d-md-flex">
                    <li class="nav-item">
                        <span class="navbar-text mr-3">
                            <fa5:icon name="server" />
                            <web:version />
                        </span>
                    </li>
                    <li class="nav-item">
                        <span class="navbar-text mr-3">
                            <fa5:icon name="database" />
                            r<web:dbVersion />
                        </span>
                    </li>
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

    <js:script placement="tail">

        var tooltips = [];

        function createTooltips() {
            var elements = document.querySelectorAll('.main-menu [title]');
            for (let i = 0; i < elements.length; i++) {
                const element = elements[i];
                
                tooltips.push(new bootstrap.Tooltip(element, {
                    boundary: 'window',
                    placement: 'right'
                }));
            }
        }

        function destroyTooltips() {
            tooltips.forEach(function(t) {
                t.dispose();
            });

            tooltips = [];
        }

        var $mainMenuCol = $(".main-menu-col");
        $(".navbar-toggler").click(function(e) {
            e.preventDefault();
            
            if ($mainMenuCol.hasClass("main-menu-collapsed")) {
                $mainMenuCol.removeClass("main-menu-collapsed");
                
                new Cookies().create("mainMenu", null);
                destroyTooltips();
            } else {
                $mainMenuCol.addClass("main-menu-collapsed");

                new Cookies().create("mainMenu", "collapsed");
                createTooltips();
            }
        });

        if ($mainMenuCol.hasClass("main-menu-collapsed")) {
            createTooltips();
        }

    </js:script>
</v:template>
