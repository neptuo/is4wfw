<v:template src="~/templates/in-template.view">
    <v:panel security:requireGroup="admins">
        <php:using prefix="user" class="php.libs.User">
            <web:a pageId="~/in/group-perms-hint.view" class="fright" text="List of perms &raquo;" security:requirePerm="CMS.HintPerms" />
            <web:a pageId="~/in/users.view" text="&laquo; Back to users" security:requirePerm="CMS.Settings.Users"/>
            <user:newGroup />
            <user:editGroupPerms />
            <user:deleteGroup />
        </php:using>
    </v:panel>
</v:template>