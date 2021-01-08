<v:template src="~/templates/in-template.view">
	<web:frame title="Available Updates">
        <sys:versionList />
    </web:frame>
    <web:frame title="Template cache" open="true">
        <div class="gray-box">
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
    </web:frame>
    <web:frame title="Notes" open="true">
        <div class="gray-box">
            If version update contains any DB migration scripts, you must execute them by browsing to:

            <ul>
                <li>
                    <web:a pageId="~/migrate.php" text="migrate.php" />
                </li>
            </ul>
        </div>
    </web:frame>
</v:template>