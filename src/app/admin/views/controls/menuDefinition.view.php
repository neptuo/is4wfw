<list:declare name="menu">
    <!-- Extra items -->
    <list:add key-text="Home" key-url="route:index" key-icon="home" key-iconPrefix="fas" key-parentId="extra" key-id="home" key-order="001" />
    <list:add key-text="Documentation" key-url="route:docs" key-icon="question-circle" key-iconPrefix="fas" key-perm="CMS.Hint" key-parentId="extra" key-id="documentation" key-order="002" />

    <!-- Standard items -->
    <list:add key-text="Home" key-url="route:index" key-icon="home" key-iconPrefix="fas" key-class="d-block d-md-none cms-menu-home" key-order="100" />

    <list:add key-text="Documentation" key-url="route:docs" key-icon="question-circle" key-iconPrefix="fas" key-perm="CMS.Hint" key-id="docs" key-cookie="cookie:cmsMenu-docs" key-defaultState="collapsed" key-order="200" />
    <list:add key-text="Libraries" key-url="route:docs" key-icon="question-circle" key-iconPrefix="fas" key-perm="CMS.Hint" key-parentId="docs" key-order="201" />
    <list:add key-text="Properties" key-url="route:hintProperties" key-icon="list" key-iconPrefix="fas" key-perm="CMS.Hint" key-parentId="docs" key-order="202" />

    <list:add key-text="Web" key-url="route:pages" key-icon="globe" key-iconPrefix="fas" key-perm="CMS.Web" key-id="web" key-cookie="cookie:cmsMenu-web" key-order="300" />
    <list:add key-text="Pages" key-url="route:pages" key-icon="~/images/icons/page_tag_red.gif" key-perm="CMS.Web.Pages" key-parentId="web" key-id="pages" key-order="301" />
    <list:add key-text="Text files" key-url="route:textFiles" key-icon="~/images/icons/page_script.gif" key-perm="CMS.Web.TextFiles" key-parentId="web" key-id="textfiles" key-order="302" />
    <list:add key-text="Templates" key-url="route:templates" key-icon="~/images/icons/page_code.png" key-perm="CMS.Web.Templates" key-parentId="web" key-id="templates" key-order="303" />
    <list:add key-text="Search" key-url="route:search" key-icon="~/images/icons/magnifier.png" key-perm="CMS.Web.Search" key-parentId="web" key-id="search" key-order="304" />
    <list:add key-text="Embedded Resources" key-url="route:embeddedResources" key-icon="~/images/icons/script_link.png" key-perm="CMS.Web.EmbeddedResources" key-parentId="web" key-id="embeddedresources" key-order="305" />
    <list:add key-text="Custom forms" key-url="route:customForms" key-icon="~/images/icons/page_attachment.gif" key-perm="CMS.Web.CustomForms" key-parentId="web" key-id="customforms" key-order="306" />
    <list:add key-text="Custom entities" key-url="route:customEntities" key-icon="~/images/icons/database.png" key-perm="CMS.Web.CustomEntities" key-parentId="web" key-id="customentities" key-order="307" />
    <list:add key-text="File manager" key-url="route:files" key-icon="~/images/icons/drive.png" key-perm="CMS.Web.FileManager" key-parentId="web" key-id="files" key-order="308" />
    <list:add key-text="Web forwards" key-url="route:webForwards" key-icon="~/images/icons/arrow_branch.png" key-perm="CMS.Web.WebForwards" key-parentId="web" key-id="webforwards" key-order="309" />
    <list:add key-text="Web projects" key-url="route:webProjects" key-icon="~/images/icons/building.png" key-perm="CMS.Web.WebProjects" key-parentId="web" key-id="webprojects" key-order="310" />
    
    <list:add key-text="Content" key-url="route:articles" key-icon="book-open" key-iconPrefix="fas" key-perm="CMS.Web" key-id="content" key-cookie="cookie:cmsMenu-content" key-order="400" />
    <list:add key-text="Articles" key-url="route:articles" key-icon="~/images/icons/book_open.png" key-perm="CMS.Web.Articles" key-parentId="content" key-id="articles" key-order="401" />
    <list:add key-text="Guestbooks" key-url="route:guestbooks" key-icon="~/images/icons/comment.png" key-perm="CMS.Web.Guestbooks" key-parentId="content" key-id="guestbooks" key-order="402" />
    <list:add key-text="Inquiries" key-url="route:inquiries" key-icon="~/images/icons/magnifier.png" key-perm="CMS.Web.Inquiries" key-parentId="content" key-id="inquiries" key-order="403" />

    <list:add key-text="Floorball" key-url="route:floorballSeasons" key-icon="futbol" key-iconPrefix="far" key-perm="CMS.Floorball" key-id="floorball" key-cookie="cookie:cmsMenu-floorball" key-defaultState="collapsed" key-order="500" />
    <list:add key-text="Projects" key-url="route:floorballProjects" key-icon="~/images/icons/floorball/projects.gif" key-perm="CMS.Floorball.Projects" key-parentId="floorball" key-id="floorball.projects" key-order="501" />
    <list:add key-text="Seasons" key-url="route:floorballSeasons" key-icon="~/images/icons/floorball/seasons.png" key-perm="CMS.Floorball.Seasons" key-parentId="floorball" key-id="floorball.seasons" key-order="502" />
    <list:add key-text="Tables" key-url="route:floorballTables" key-icon="~/images/icons/floorball/table.png" key-perm="CMS.Floorball.Tables" key-parentId="floorball" key-id="floorball.tables" key-order="503" />
    <list:add key-text="Rounds" key-url="route:floorballRounds" key-icon="~/images/icons/floorball/rounds.gif" key-perm="CMS.Floorball.Rounds" key-parentId="floorball" key-id="floorball.rounds" key-order="504" />
    <list:add key-text="Teams" key-url="route:floorballTeams" key-icon="~/images/icons/floorball/teams.png" key-perm="CMS.Floorball.Teams" key-parentId="floorball" key-id="floorball.teams" key-order="505" />
    <list:add key-text="Players" key-url="route:floorballPlayers" key-icon="~/images/icons/floorball/players.png" key-perm="CMS.Floorball.Players" key-parentId="floorball" key-id="floorball.players" key-order="506" />
    <list:add key-text="Matches" key-url="route:floorballMatches" key-icon="~/images/icons/floorball/matches.png" key-perm="CMS.Floorball.Matches" key-parentId="floorball" key-id="floorball.matches" key-order="507" />
    <list:add key-text="Tables Content" key-url="route:floorballTablesContent" key-icon="~/images/icons/floorball/table.png" key-perm="CMS.Floorball.TablesContent" key-parentId="floorball" key-id="floorball.tablescontent" key-order="508" />

    <list:add key-text="Users" key-url="route:users" key-icon="users" key-iconPrefix="fas" key-perm="CMS.Settings" key-id="accounts" key-cookie="cookie:cmsMenu-accounts" key-order="600" />
    <list:add key-text="Users" key-url="route:users" key-icon="~/images/icons/user.png" key-perm="CMS.Settings.Users" key-parentId="accounts" key-id="users" key-order="601" />
    <list:add key-text="Groups" key-url="route:userGroups" key-icon="~/images/icons/group.png" key-perm="CMS.Settings.Groups" key-parentId="accounts" key-id="groups" key-order="602" />
    <list:add key-text="Role cache" key-url="route:roleCache" key-icon="~/images/icons/cog.png" key-perm="CMS.Settings.RoleCache" key-parentId="accounts" key-id="rolecache" key-order="603" />
    <list:add key-text="User log" key-url="route:userLog" key-icon="~/images/icons/userlog.png" key-perm="CMS.Settings.UserLog" key-parentId="accounts" key-id="userlog" key-order="604" />
    <list:add key-text="Personal properties" key-url="route:personalProperties" key-icon="~/images/icons/properties.png" key-perm="CMS.Settings.PersonalProperties" key-parentId="accounts" key-id="personalproperties" key-order="605" />
    <list:add key-text="Personal notes" key-url="route:personalNotes" key-icon="~/images/page_edi.png" key-perm="CMS.Settings.PersonalNotes" key-parentId="accounts" key-id="personalnotes" key-order="606" />

    <list:add key-text="Settings" key-url="route:personalNotes" key-icon="tools" key-iconPrefix="fas" key-perm="CMS.Settings" key-id="settings" key-cookie="cookie:cmsMenu-settings" key-order="0" key-order="700" />
    <list:add key-text="Url cache" key-url="route:urlCache" key-icon="~/images/icons/cog.png" key-perm="CMS.Settings.UrlCache" key-parentId="settings" key-id="urlcache" key-order="701" />
    <list:add key-text="Languages" key-url="route:languages" key-icon="~/images/lang_add.png" key-perm="CMS.Settings.Languages" key-parentId="settings" key-id="languages" key-order="702" />
    <list:add key-text="Localization bundles" key-url="route:localizationBundles" key-icon="~/images/lang.png" key-perm="CMS.Settings.LocalizationBundles" key-parentId="settings" key-id="localizationbundles" key-order="703" />
    <list:add key-text="Keywords &amp; robots.txt" key-url="route:keywords" key-icon="~/images/icons/book_open.png" key-perm="CMS.Settings.Keywords" key-parentId="settings" key-id="keywords" key-order="704" />
    <list:add key-text="Application log" key-url="route:applicationLog" key-icon="~/images/icons/log.png" key-perm="CMS.Settings.ApplicationLog" key-parentId="settings" key-id="applicationlogs" key-order="705" />
    <list:add key-text="Database connections" key-url="route:databaseConnections" key-icon="~/images/icons/page_code.png" key-perm="CMS.Settings.DatabaseConnections" key-parentId="settings" key-id="dbconnections" key-order="706" />
    <list:add key-text="Available updates" key-url="route:update" key-icon="~/images/icons/brick.png" key-perm="CMS.Settings.Update" key-parentId="settings" key-id="updates" key-order="707" />
    <list:add key-text="Modules" key-url="route:modules" key-icon="~/images/icons/plugin.png" key-perm="CMS.Settings.Modules" key-parentId="settings" key-id="modules" key-order="708" />
    <list:add key-text="Admin menu" key-url="route:editAdminMenu" key-icon="~/images/icons/building.png" key-perm="CMS.Settings.AdminMenu" key-parentId="settings" key-id="adminmenu" key-order="709" />
    <list:add key-text="Environment" key-url="route:environment" key-icon="seedling" key-iconPrefix="fas" key-perm="CMS.Settings.Environment" key-parentId="settings" key-id="enviroment" key-order="710" />
</list:declare>

<list:declare name="favoritesEditor" />
<ui:forEach items="list:menu">
    <if:eval name="includeInFavorites">
        <if:equals value="list:menu-parentId" is="php:null" not="true" />
        <if:equals value="list:menu-id" is="php:null" not="true" />
    </if:eval>
    <web:out if:passed="includeInFavorites">
        <list:add name="favoritesEditor" key-text="list:menu-text" key-url="list:menu-url" key-icon="list:menu-icon" key-iconPrefix="list:menu-iconPrefix" key-class="list:menu-class" key-parentId="list:menu-parentId" key-id="list:menu-id" key-order="list:menu-order" />
    </web:out>
</ui:forEach>
<list:sort name="favoritesEditor" key-order="asc" />

<web:condition when="var:is4wfw.userMenu">
    <utils:splitToArray output="var:is4wfw.userMenu" value="var:is4wfw.userMenu" separator="," />
    <var:declare name="favoritesOrder" value="0" />
    <list:add name="menu" key-text="Favorites" key-url="route:index" key-icon="star" key-iconPrefix="fas" key-id="favorites" key-cookie="cookie:cmsMenu-favorites" key-order="var:favoritesOrder" />
    <ui:forEach items="list:menu">
        <if:arrayContains name="userMenu" value="var:is4wfw.userMenu" item="list:menu-id" />
        <web:out if:passed="userMenu">
            <math:number out="var:favoritesOrder" add="1" />
            <list:add name="menu" key-text="list:menu-text" key-url="list:menu-url" key-icon="list:menu-icon" key-iconPrefix="list:menu-iconPrefix" key-class="list:menu-class" key-parentId="favorites" key-order="var:favoritesOrder" />
        </web:out>
    </ui:forEach>

    <list:sort name="menu" key-order="asc" />
</web:condition>
