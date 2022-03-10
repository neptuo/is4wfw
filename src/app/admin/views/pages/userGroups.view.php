<php:using prefix="user" class="php.libs.User" security:requireGroup="admins">
    <web:a pageId="route:userGroupPermsHint" class="fright" text="List of perms &raquo;" security:requirePerm="CMS.HintPerms" />
    <web:a pageId="route:users" text="&laquo; Back to users" security:requirePerm="CMS.Settings.Users"/>
    <user:newGroup />
    <user:editGroupPerms />
    <user:deleteGroup />
</php:using>