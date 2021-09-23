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
    <js:script placement="tail" path="~/js/Admin.js" />
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
                    <template:declare identifier="menuContainer">
                        <var:declare name="menuContainerCookie" value="template:cookie" />
                        <web:condition when="var:menuContainerCookie" isInverted="true">
                            <var:declare name="menuContainerCookie" value="template:defaultState" />
                        </web:condition>

                        <utils:concat output="menuContainerCssClass" separator=" " value1="mb-3 cms-menu" value2="template:class" />
                        <utils:concat output="menuContainerTogglerTitle" separator=" " value1="Collapse/Expand" value2="template:title" />
                        <web:condition when="var:menuContainerCookie" is="collapsed">
                            <utils:concat output="menuContainerCssClass" separator=" " value1="utils:menuContainerCssClass" value2="cms-menu-collapsed" />
                        </web:condition>
                        <v:panel class="utils:menuContainerCssClass" data-menu="template:name" security:requirePerm="template:permission"> 
                            <div class="menu-root">
                                <web:a pageId="template:rootUrl" title="template:title">
                                    <span>
                                        <web:out text="template:title" />
                                    </span>
                                </web:a>
                                <button class="menu-toggler" title="<web:out text="utils:menuContainerTogglerTitle" />"></button>
                            </div>
                            <web:condition when="template:menu">
                                <m:xmlMenu file="template:menu" />
                            </web:condition>
                            <web:condition when="template:menu" isInverted="true">
                                <template:content />
                            </web:condition>
                        </v:panel>
                    </template:declare>

                    <v:panel class="mb-3 cms-menu cms-menu-6 d-block d-md-none cms-menu-home">
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
                    <template:menuContainer name="web" title="Web" permission="CMS.Web" rootUrl="~/in/pages.view" cookie="cookie:cmsMenu-web" menu="~/templates/menus/web.xml" />
                    <template:menuContainer name="floorball" title="Floorball" permission="CMS.Floorball" rootUrl="~/in/floorball/seasons.view" cookie="cookie:cmsMenu-floorball" menu="~/templates/menus/floorball.xml" defaultState="collapsed" class="cms-menu-2" />
                    <template:menuContainer name="settings" title="Settings" permission="CMS.Settings" rootUrl="~/in/personal-notes.view" cookie="cookie:cmsMenu-settings" menu="~/templates/menus/settings.xml" class="cms-menu-4" />
                    <web:condition when="sys:hasAdminMenu">
                        <template:menuContainer name="custom" title="Custom" permission="CMS.AdminMenu" rootUrl="~/in/personal-notes.view" cookie="cookie:cmsMenu-custom" class="cms-menu-5">
                            <sys:adminMenu url="~/in/admin-menu.view" />
                        </template:menuContainer>
                    </web:condition>
                </nav>
            </div>
        </bs:column>

        <bs:column>
            <div class="navbar navbar-dark bg-dark sticky-top">
                <div>
                    <wp:selectProject showMsg="false" useFrames="false" label=" " />
                </div>
                <div class="mx-auto">
                    <web:condition when="var:is4wfw.instance.name">
                        <div class="navbar-text" data-modal="instance-name-editor">
							<web:out text="var:is4wfw.instance.name" />
                        </div>
                    </web:condition>
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

	<controls:instanceName />
</v:template>
