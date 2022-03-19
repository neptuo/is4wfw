<list:declare name="menu">
    <list:add key-text="Home" key-url="route:index" key-icon="home" key-iconPrefix="fas" key-class="d-block d-md-none cms-menu-home" key-id="home" key-order="100" />

    <list:add key-text="Documentation" key-url="route:hint" key-icon="question-circle" key-iconPrefix="fas" key-perm="CMS.Hint" key-id="docs" key-order="200" />

    <list:add key-text="Web" key-url="route:pages" key-icon="globe" key-iconPrefix="fas" key-perm="CMS.Web" key-id="web" key-cookie="cookie:cmsMenu-web" key-order="300" />
    <list:add key-text="Pages" key-url="route:pages" key-icon="~/images/icons/page_tag_red.gif" key-perm="CMS.Web.Pages" key-parentId="web" key-id="web.pages" key-order="301" />
    <list:add key-text="Text files" key-url="route:textFiles" key-icon="~/images/icons/page_script.gif" key-perm="CMS.Web.TextFiles" key-parentId="web" key-id="web.textfiles" key-order="302" />
    <list:add key-text="Templates" key-url="route:templates" key-icon="~/images/icons/page_code.png" key-perm="CMS.Web.Templates" key-parentId="web" key-id="web.templates" key-order="303" />
    <list:add key-text="Search" key-url="route:search" key-icon="~/images/icons/magnifier.png" key-perm="CMS.Web.Search" key-parentId="web" key-id="web.search" key-order="304" />
    <list:add key-text="Embedded Resources" key-url="route:embeddedResources" key-icon="~/images/icons/script_link.png" key-perm="CMS.Web.EmbeddedResources" key-parentId="web" key-id="web.embeddedresources" key-order="305" />
    <list:add key-text="Custom forms" key-url="route:customForms" key-icon="~/images/icons/page_attachment.gif" key-perm="CMS.Web.CustomForms" key-parentId="web" key-id="web.customforms" key-order="306" />
    <list:add key-text="Custom entities" key-url="route:customEntities" key-icon="~/images/icons/database.png" key-perm="CMS.Web.CustomEntities" key-parentId="web" key-id="web.customentities" key-order="307" />
    <list:add key-text="File manager" key-url="route:files" key-icon="~/images/icons/drive.png" key-perm="CMS.Web.FileManager" key-parentId="web" key-id="web.files" key-order="308" />
    <list:add key-text="Web forwards" key-url="route:webForwards" key-icon="~/images/icons/arrow_branch.png" key-perm="CMS.Web.WebForwards" key-parentId="web" key-id="web.webforwards" key-order="309" />
    <list:add key-text="Web projects" key-url="route:webProjects" key-icon="~/images/icons/building.png" key-perm="CMS.Web.WebProjects" key-parentId="web" key-id="web.webprojects" key-order="310" />
    
    <list:add key-text="Content" key-url="route:articles" key-icon="book-open" key-iconPrefix="fas" key-perm="CMS.Web" key-id="content" key-cookie="cookie:cmsMenu-content" key-order="400" />
    <list:add key-text="Articles" key-url="route:articles" key-icon="~/images/icons/book_open.png" key-perm="CMS.Web.Articles" key-parentId="content" key-id="content.articles" key-order="401" />
    <list:add key-text="Guestbooks" key-url="route:guestbooks" key-icon="~/images/icons/comment.png" key-perm="CMS.Web.Guestbooks" key-parentId="content" key-id="content.guestbooks" key-order="402" />
    <list:add key-text="Inquiries" key-url="route:inquiries" key-icon="~/images/icons/magnifier.png" key-perm="CMS.Web.Inquiries" key-parentId="content" key-id="content.inquiries" key-order="403" />

    <list:add key-text="Floorball" key-url="route:floorballSeasons" key-icon="futbol" key-iconPrefix="far" key-perm="CMS.Floorball" key-id="floorball" key-cookie="cookie:cmsMenu-floorball" key-defaultState="collapsed" key-order="500" />
    <list:add key-text="Projects" key-url="route:floorballProjects" key-icon="~/images/icons/floorball/projects.gif" key-perm="CMS.Floorball.Projects" key-parentId="floorball" key-id="floorball.projects" key-order="501" />
    <list:add key-text="Seasons" key-url="route:floorballSeasons" key-icon="~/images/icons/floorball/seasons.png" key-perm="CMS.Floorball.Seasons" key-parentId="floorball" key-id="floorball.seasons" key-order="502" />
    <list:add key-text="Tables" key-url="route:floorballTables" key-icon="~/images/icons/floorball/table.png" key-perm="CMS.Floorball.Tables" key-parentId="floorball" key-id="floorball.tables" key-order="503" />
    <list:add key-text="Rounds" key-url="route:floorballRounds" key-icon="~/images/icons/floorball/rounds.gif" key-perm="CMS.Floorball.Rounds" key-parentId="floorball" key-id="floorball.rounds" key-order="504" />
    <list:add key-text="Teams" key-url="route:floorballTeams" key-icon="~/images/icons/floorball/teams.png" key-perm="CMS.Floorball.Teams" key-parentId="floorball" key-id="floorball.teams" key-order="505" />
    <list:add key-text="Players" key-url="route:floorballPlayers" key-icon="~/images/icons/floorball/players.png" key-perm="CMS.Floorball.Players" key-parentId="floorball" key-id="floorball.players" key-order="506" />
    <list:add key-text="Matches" key-url="route:floorballMatches" key-icon="~/images/icons/floorball/matches.png" key-perm="CMS.Floorball.Matches" key-parentId="floorball" key-id="floorball.matches" key-order="507" />
    <list:add key-text="Tables Content" key-url="route:floorballTablesContent" key-icon="~/images/icons/floorball/table.png" key-perm="CMS.Floorball.TablesContent" key-parentId="floorball" key-id="floorball.tablescontent" key-order="508" />

    <list:add key-text="Users" key-url="route:users" key-icon="users" key-iconPrefix="fas" key-perm="CMS.Settings" key-id="users" key-cookie="cookie:cmsMenu-users" key-order="600" />
    <list:add key-text="Users &amp; Roles" key-url="route:users" key-icon="~/images/icons/user.png" key-perm="CMS.Settings.Users" key-parentId="users" key-id="users.users" key-order="601" />
    <list:add key-text="Role cache" key-url="route:roleCache" key-icon="~/images/icons/cog.png" key-perm="CMS.Settings.RoleCache" key-parentId="users" key-id="users.rolecache" key-order="602" />
    <list:add key-text="User log" key-url="route:userLog" key-icon="~/images/icons/userlog.png" key-perm="CMS.Settings.UserLog" key-parentId="users" key-id="users.userlog" key-order="603" />
    <list:add key-text="Personal properties" key-url="route:personalProperties" key-icon="~/images/icons/properties.png" key-perm="CMS.Settings.PersonalProperties" key-parentId="users" key-id="users.personalproperties" key-order="604" />
    <list:add key-text="Personal notes" key-url="route:personalNotes" key-icon="~/images/page_edi.png" key-perm="CMS.Settings.PersonalNotes" key-parentId="users" key-id="users.personalnotes" key-order="605" />

    <list:add key-text="Settings" key-url="route:personalNotes" key-icon="tools" key-iconPrefix="fas" key-perm="CMS.Settings" key-id="settings" key-cookie="cookie:cmsMenu-settings" key-order="0" key-order="700" />
    <list:add key-text="Url cache" key-url="route:urlCache" key-icon="~/images/icons/cog.png" key-perm="CMS.Settings.UrlCache" key-parentId="settings" key-id="settings.urlcache" key-order="701" />
    <list:add key-text="Languages" key-url="route:languages" key-icon="~/images/lang_add.png" key-perm="CMS.Settings.Languages" key-parentId="settings" key-id="settings.languages" key-order="702" />
    <list:add key-text="Localization bundles" key-url="route:localizationBundles" key-icon="~/images/lang.png" key-perm="CMS.Settings.LocalizationBundles" key-parentId="settings" key-id="settings.bundles" key-order="703" />
    <list:add key-text="Keywords &amp; robots.txt" key-url="route:keywords" key-icon="~/images/icons/book_open.png" key-perm="CMS.Settings.Keywords" key-parentId="settings" key-id="settings.keywords" key-order="704" />
    <list:add key-text="Application log" key-url="route:applicationLog" key-icon="~/images/icons/log.png" key-perm="CMS.Settings.ApplicationLog" key-parentId="settings" key-id="settings.logs" key-order="705" />
    <list:add key-text="Database connections" key-url="route:databaseConnections" key-icon="~/images/icons/page_code.png" key-perm="CMS.Settings.DatabaseConnections" key-parentId="settings" key-id="settings.dbconnections" key-order="706" />
    <list:add key-text="Available updates" key-url="route:update" key-icon="~/images/icons/brick.png" key-perm="CMS.Settings.Update" key-parentId="settings" key-id="settings.updates" key-order="707" />
    <list:add key-text="Modules" key-url="route:modules" key-icon="~/images/icons/plugin.png" key-perm="CMS.Settings.Modules" key-parentId="settings" key-id="settings.modules" key-order="708" />
    <list:add key-text="Admin menu" key-url="route:editAdminMenu" key-icon="~/images/icons/building.png" key-perm="CMS.Settings.AdminMenu" key-parentId="settings" key-id="settings.adminmenu" key-order="709" />
</list:declare>

