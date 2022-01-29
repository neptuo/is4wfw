<list:declare name="menu">
    <list:add key-text="Home" key-url="~/in/index.view" key-icon="~/images/icons/house.png" />

    <list:add key-text="Documentation" key-url="~/in/hint.view" key-icon="~/images/icons/anchor.png" />

    <list:add key-text="Web" key-url="~/in/pages.view" key-icon="~/images/icons/at.png" key-perm="CMS.Web" key-id="web" />
    <list:add key-text="Pages" key-url="~/in/pages.view" key-icon="~/images/icons/page_tag_red.gif" key-perm="CMS.Web.Pages" key-parentId="web" />
    <list:add key-text="Text files" key-url="~/in/text-files.view" key-icon="~/images/icons/page_script.gif" key-perm="CMS.Web.TextFiles" key-parentId="web" />
    <list:add key-text="Templates" key-url="~/in/templates.view" key-icon="~/images/icons/page_code.png" key-perm="CMS.Web.Templates" key-parentId="web" />
    <list:add key-text="Search" key-url="~/in/search.view" key-icon="~/images/icons/magnifier.png" key-perm="CMS.Web.Search" key-parentId="web" />
    <list:add key-text="Embedded Resources" key-url="~/in/embedded-resources.view" key-icon="~/images/icons/script_link.png" key-perm="CMS.Web.EmbeddedResources" key-parentId="web" />
    <list:add key-text="Custom forms" key-url="~/in/custom-forms.view" key-icon="~/images/icons/page_attachment.gif" key-perm="CMS.Web.CustomForms" key-parentId="web" />
    <list:add key-text="Custom entities" key-url="~/in/custom-entities.view" key-icon="~/images/icons/database.png" key-perm="CMS.Web.CustomEntities" key-parentId="web" />
    <list:add key-text="File manager" key-url="~/in/filesng.view" key-icon="~/images/icons/drive.png" key-perm="CMS.Web.FileManager" key-parentId="web" />
    <list:add key-text="Articles" key-url="~/in/articles.view" key-icon="~/images/icons/book_open.png" key-perm="CMS.Web.Articles" key-parentId="web" />
    <list:add key-text="Guestbooks" key-url="~/in/guestbooks.view" key-icon="~/images/icons/comment.png" key-perm="CMS.Web.Guestbooks" key-parentId="web" />
    <list:add key-text="Inquiries" key-url="~/in/inquiries.view" key-icon="~/images/icons/magnifier.png" key-perm="CMS.Web.Inquiries" key-parentId="web" />
    <list:add key-text="Web forwards" key-url="~/in/web-forwards.view" key-icon="~/images/icons/arrow_branch.png" key-perm="CMS.Web.WebForwards" key-parentId="web" />
    <list:add key-text="Web projects" key-url="~/in/web-projects.view" key-icon="~/images/icons/building.png" key-perm="CMS.Web.WebProjects" key-parentId="web" />

    <list:add key-text="Floorball" key-url="~/in/floorball/seasons.view" key-icon="~/images/icons/floorball/ball.png" key-perm="CMS.Floorball" key-id="floorball" />
    <list:add key-text="Projects" key-url="~/in/floorball/projects.view" key-icon="~/images/icons/floorball/projects.gif" key-perm="CMS.Floorball.Projects" key-parentId="floorball" />
    <list:add key-text="Seasons" key-url="~/in/floorball/seasons.view" key-icon="~/images/icons/floorball/seasons.png" key-perm="CMS.Floorball.Seasons" key-parentId="floorball" />
    <list:add key-text="Tables" key-url="~/in/floorball/tables.view" key-icon="~/images/icons/floorball/table.png" key-perm="CMS.Floorball.Tables" key-parentId="floorball" />
    <list:add key-text="Rounds" key-url="~/in/floorball/rounds.view" key-icon="~/images/icons/floorball/rounds.gif" key-perm="CMS.Floorball.Rounds" key-parentId="floorball" />
    <list:add key-text="Teams" key-url="~/in/floorball/teams.view" key-icon="~/images/icons/floorball/teams.png" key-perm="CMS.Floorball.Teams" key-parentId="floorball" />
    <list:add key-text="Players" key-url="~/in/floorball/players.view" key-icon="~/images/icons/floorball/players.png" key-perm="CMS.Floorball.Players" key-parentId="floorball" />
    <list:add key-text="Matches" key-url="~/in/floorball/matches.view" key-icon="~/images/icons/floorball/matches.png" key-perm="CMS.Floorball.Matches" key-parentId="floorball" />
    <list:add key-text="Tables Content" key-url="~/in/floorball/tables-content.view" key-icon="~/images/icons/floorball/table.png" key-perm="CMS.Floorball.TablesContent" key-parentId="floorball" />

    <list:add key-text="Settings" key-url="~/in/personal-notes.view" key-icon="~/images/icons/radioactive.png" key-perm="CMS.Settings" key-id="settings" />
    <list:add key-text="Url cache" key-url="~/in/url-cache.view" key-icon="~/images/icons/cog.png" key-perm="CMS.Settings.UrlCache" key-parentId="settings" />
    <list:add key-text="Role cache" key-url="~/in/role-cache.view" key-icon="~/images/icons/cog.png" key-perm="CMS.Settings.RoleCache" key-parentId="settings" />
    <list:add key-text="Languages" key-url="~/in/languages.view" key-icon="~/images/lang_add.png" key-perm="CMS.Settings.Languages" key-parentId="settings" />
    <list:add key-text="Keywords &amp; robots.txt" key-url="~/in/keywords.view" key-icon="~/images/icons/book_open.png" key-perm="CMS.Settings.Keywords" key-parentId="settings" />
    <list:add key-text="Users &amp; Roles" key-url="~/in/users.view" key-icon="~/images/icons/user.png" key-perm="CMS.Settings.Users" key-parentId="settings" />
    <list:add key-text="User log" key-url="~/in/user-log.view" key-icon="~/images/icons/userlog.png" key-perm="CMS.Settings.UserLog" key-parentId="settings" />
    <list:add key-text="Application log" key-url="~/in/application-log.view" key-icon="~/images/icons/log.png" key-perm="CMS.Settings.ApplicationLog" key-parentId="settings" />
    <list:add key-text="Localization bundles" key-url="~/in/localization-bundles.view" key-icon="~/images/lang.png" key-perm="CMS.Settings.LocalizationBundles" key-parentId="settings" />
    <list:add key-text="Database connections" key-url="~/in/database-connections.view" key-icon="~/images/icons/page_code.png" key-perm="CMS.Settings.DatabaseConnections" key-parentId="settings" />
    <list:add key-text="Personal properties" key-url="~/in/personal-properties.view" key-icon="~/images/icons/properties.png" key-perm="CMS.Settings.PersonalProperties" key-parentId="settings" />
    <list:add key-text="Personal notes" key-url="~/in/personal-notes.view" key-icon="~/images/page_edi.png" key-perm="CMS.Settings.PersonalNotes" key-parentId="settings" />
    <list:add key-text="Available updates" key-url="~/in/update.view" key-icon="~/images/icons/brick.png" key-perm="CMS.Settings.Update" key-parentId="settings" />
    <list:add key-text="Modules" key-url="~/in/modules.view" key-icon="~/images/icons/plugin.png" key-perm="CMS.Settings.Modules" key-parentId="settings" />
    <list:add key-text="Admin menu" key-url="~/in/edit-admin-menu.view" key-icon="~/images/icons/building.png" key-perm="CMS.Settings.AdminMenu" key-parentId="settings" />
</list:declare>