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
            <web:condition when="template:icon">
                <img src="<web:out text="template:icon" />" />
            </web:condition>
            <span>
                <web:out text="template:title" />
            </span>
        </web:a>
        <web:condition when="template:toggler" is="" isInverted="true">
            <button class="menu-toggler" title="<web:out text="utils:menuContainerTogglerTitle" />"></button>
        </web:condition>
    </div>
    <web:condition when="template:menu">
        <div class="menu">
            <ul class="ul-1">
                <ui:forEach items="list:menu" filter-parentId="template:menu">
                    <web:out security:requirePerm="list:menu-perm">
                        <li>
                            <div class="link">
                                <web:a pageId="list:menu-url" title="list:menu-text">
                                    <web:condition when="list:menu-icon">
                                        <img src="<web:out text="list:menu-icon" />" />
                                    </web:condition>
                                    <span>
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
</v:panel>