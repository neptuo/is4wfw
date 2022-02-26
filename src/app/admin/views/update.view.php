<web:frame title="Available Updates">
    <web:url pageId="route:update">
        <var:declare name="selfResolved" value="web:url" />
    </web:url>
    <web:url pageId="~/migrate.php" param-return="var:selfResolved">
        <sys:versionList afterUpdateUrl="web:url" />
    </web:url>
</web:frame>
<web:frame title="Tools" open="true">
    <div class="gray-box-float">
        <edit:form submit="clearTemplateCache">
            <web:condition when="edit:submit">
                <sys:clearTemplateCache>
                    <web:redirectToSelf />
                </sys:clearTemplateCache>
            </web:condition>
            <button name="clearTemplateCache" class="confirm">
                Clear compiled templates cache
            </button>
        </edit:form>
    </div>
    <div class="gray-box-float">
        <web:a pageId="~/migrate.php" param-return="route:update" text="Migrate database" class="button" />
    </div>
    <div class="gray-box-float">
        <edit:form submit="rebuildModuleInitializers">
            <web:condition when="edit:submit">
                <module:rebuildInitializers>
                    <web:redirectToSelf />
                </module:rebuildInitializers>
            </web:condition>
            <button name="rebuildModuleInitializers" class="confirm">
                Rebuild module initializers
            </button>
        </edit:form>
    </div>
    <div class="clear"></div>
</web:frame>