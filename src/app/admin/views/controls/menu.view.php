<var:declare name="menuContainerCookie" value="template:cookie" />
<web:condition when="var:menuContainerCookie" isInverted="true">
    <var:declare name="menuContainerCookie" value="template:defaultState" />
</web:condition>

<utils:concat output="menuContainerCssClass" separator=" " value1="mb-3 cms-menu" value2="template:class" />
<utils:concat output="menuContainerTogglerTitle" separator=" " value1="Collapse/Expand" value2="template:title" />
<web:condition when="var:menuContainerCookie" is="collapsed">
    <utils:concat output="menuContainerCssClass" separator=" " value1="utils:menuContainerCssClass" value2="cms-menu-collapsed" />
</web:condition>
<web:out security:requirePerm="template:permission">
    <div class="<web:out text="utils:menuContainerCssClass" />" data-menu="<web:out text="template:name" />"> 
        <div class="menu-root">
            <web:a pageId="template:rootUrl" title="template:title" class="btn btn-primary btn-block">
                <web:condition when="template:icon">
                    <var:declare name="iconPrefix" value="fa" />
                    <web:condition when="template:iconPrefix">
                        <var:declare name="iconPrefix" value="template:iconPrefix" />
                    </web:condition>
                    <fa5:icon prefix="var:iconPrefix" name="template:icon" />
                </web:condition>
                <span class="text">
                    <web:out text="template:title" />
                </span>
            </web:a>
            <web:condition when="template:toggler" is="" isInverted="true">
                <button class="menu-toggler" title="<web:out text="utils:menuContainerTogglerTitle" />">
                    <fa5:icon name="chevron-down" class="menu-toggler-expand" />
                    <fa5:icon name="chevron-up" class="menu-toggler-collapse" />
                </button>
            </web:condition>
        </div>
        <web:condition when="template:menu">
            <div class="menu">
                <ul class="ul-1">
                    <ui:forEach items="list:menu" filter-parentId="template:menu">
                        <web:out security:requirePerm="list:menu-perm">
                            <li>
                                <div class="link">
                                    <web:a pageId="list:menu-url" title="list:menu-text" class="rounded">
                                        <web:condition when="list:menu-icon">
                                            <img src="<web:out text="list:menu-icon" />" width="16" height="16" />
                                        </web:condition>
                                        <span class="text">
                                            <web:out text="list:menu-text" />
                                        </span>
                                    </web:a>
                                </div>
                            </li>
                        </web:out>
                    </ui:forEach>
                </ul>
            </div>
        </web:condition>
        <web:condition when="template:menu" isInverted="true">
            <template:content />
        </web:condition>
    </div>
</web:out>