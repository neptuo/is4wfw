<php:using prefix="user" class="php.libs.User" security:requireGroup="admins">
    <web:a pageId="route:userGroupPermsHint" class="fright" text="Permissions &raquo;" security:requirePerm="CMS.HintPerms" />
    <web:a pageId="route:users" text="Users &raquo;" security:requirePerm="CMS.Settings.Users"/>
    <user:newGroup />
    <user:editGroupPerms />
    <user:deleteGroup />
</php:using>