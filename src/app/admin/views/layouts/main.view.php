<login:init group="web-admins" />
<login:refresh group="web-admins">
    <var:declare name="adminLastPage" value="web:currentPath" scope="session" />
    <web:redirectTo pageId="route:login" />
</login:refresh>
<web:condition when="post:logout" is="logout">
    <login:logout group="web-admins">
        <var:declare name="adminLastPage" value="web:currentPath" scope="session" />
        <web:redirectTo pageId="route:login" />
    </login:logout>
</web:condition>
<web:head>
	<meta name="viewport" content="width=device-width" />
	<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no" />
    <bs:resources />
    <fa5:resources />
    <js:cmsResources />
    <js:script placement="tail" path="~/js/Admin.js" />
</web:head>
<layouts:base>
    <bs:row class="no-gutters main-layout">
        <var:declare name="mainMenuColCssClass" value="main-menu-col" />
        <web:condition when="cookie:mainMenu" is="collapsed">
            <utils:concat output="mainMenuColCssClass" separator=" " value1="var:mainMenuColCssClass" value2="main-menu-collapsed" />
            <var:declare name="mainMenuColCssClass" value="utils:mainMenuColCssClass" />
        </web:condition>
        <bs:column default="3" large="2" class="var:mainMenuColCssClass">
            <nav class="navbar navbar-expand navbar-dark bg-dark sticky-top">
                <web:a pageId="route:index" class="navbar-brand d-none d-md-block mr-auto">
                    &lt;is4wfw /&gt;
                </web:a>
                <button class="navbar-toggler" type="button"><span class="navbar-toggler-icon"></span></button>
            </nav>
                
            <div class="border-right shadow main-menu main-menu-background">
                <nav class="pt-2 pb-3 px-2" data-mode="<web:out text="cookie:cmsMenuMode" />">
                    <div class="d-flex justify-content-between pb-1 favorites-settings">
                        <a data-mode="all">All</a>
                        <a data-mode="favorites">Favorites</a>
                        <a data-mode="standard">Standard</a>
                        <a data-modal="favorites-editor">Edit</a>
                    </div>
                    <controls:menuDefinition />
                    <ui:forEach items="list:menu" filter-parentId="php:null">
                        <controls:menu name="list:menu-id" title="list:menu-text" icon="list:menu-icon" permission="list:menu-perm" rootUrl="list:menu-url" cookie="list:menu-cookie" menu="list:menu-id" defaultState="list:menu-defaultState" toggler="list:menu-id" class="list:menu-class" />
                    </ui:forEach>
                    <web:condition when="sys:hasAdminMenu">
                        <controls:menu name="custom" title="Custom" permission="CMS.AdminMenu" rootUrl="route:personalNotes" cookie="cookie:cmsMenu-custom" icon="ellipsis-h" iconPrefix="fas" toggler="true">
                            <sys:adminMenu url="route:adminMenu" />
                        </controls:menu>
                    </web:condition>
                </nav>
            </div>
        </bs:column>

        <bs:column>
            <div class="navbar navbar-dark bg-dark sticky-top">
                <div>
                    <wp:selectProject showMsg="false" useFrames="false" label=" " />
                </div>
                <div class="ml-2 d-none d-lg-block">
                    <edit:form submit="layout-search" class="form-inline">
                        <ui:filter pageId="route:search">
                            <div class="input-group">
                                <ui:textbox name="q" class="form-control form-control-sm" placeholder="Search..." />
                                <div class="input-group-append">
                                    <bs:button name="layout-search" size="small">
                                        <fa5:icon name="search" />
                                    </bs:button>
                                </div>
                            </div>
                        </ui:filter>
                    </edit:form>
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
                    <template:content />
                </div>
            </div>
        </bs:column>
    </bs:row>

	<controls:instanceName />
	<controls:favoritesMenuEditor />
</layouts:base>
