<web:frame title="Available Updates">
    <sys:versionList />
</web:frame>
<web:frame title="Tools" open="true">
    <div class="gray-box-float">
        <edit:form submit="clearTemplateCache">
            <web:condition when="edit:submit">
                <sys:clearTemplateCache>
                    <web:redirectToSelf />
                </sys:clearTemplateCache>
            </web:condition>
            <button name="clearTemplateCache">
                Clear compiled templates cache
            </button>
        </edit:form>
    </div>
    <div class="gray-box-float">
        <web:a pageId="~/migrate.php" param-return="route:update" text="Migrate database" class="button" />
    </div>
    <div class="clear"></div>
</web:frame>