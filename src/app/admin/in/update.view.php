<v:template src="~/templates/in-template.view">
	<web:frame title="Available Updates">
        <sys:versionList />
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