<web:out security:requireGroup="admins">
    <web:a pageId="route:userGroups" text="&laquo; Back to user groups" security:requirePerm="CMS.Settings.Groups" />
    <web:frame title="Group perms">
        <div class="gray-box">
            <strong>Pages:</strong>
        </div>
        <div class="gray-box">
            <ul>
                <li>Page.EditDetail</li>
                <li>Page.AddNew</li>
                <li>Page.ManageFiles</li>
                <li>Page.ManageProperties</li>
                <li>Page.Delete</li>
                <li>Page.MoveTree</li>
                <li>Page.CopyTree</li>
                <li>Page.MoveUpDown</li>
                <li>Page.AddLang</li>
                <li>Page.Delete</li>
                <li>Page.ManageRights</li>
                <li>Page.TagLibs</li>
                <li>Page.Head</li>
            </ul>
        </div>
        <div class="gray-box">
            <strong>Articles:</strong>
        </div>
        <div class="gray-box">
            <ul>
                <li>Article.Head</li>
                <li>Article.Content</li>
            </ul>
        </div>
        <div class="gray-box">
            <strong>Hint:</strong>
        </div>
        <div class="gray-box">
            <ul>
                <li>CMS.Hint</li>
                <li>CMS.HintProperties</li>
                <li>CMS.HintPerms</li>
            </ul>
        </div>
        <div class="gray-box">
            <strong>Web:</strong>
        </div>
        <div class="gray-box">
            <ul>
                <li>CMS.Web</li>
                <li>CMS.Web.Pages</li>
                <li>CMS.Web.TextFiles</li>
                <li>CMS.Web.Templates</li>
                <li>CMS.Web.Search</li>
                <li>CMS.Web.EmbeddedResources</li>
                <li>CMS.Web.CustomForms</li>
                <li>CMS.Web.CustomEntities</li>
                <li>CMS.Web.Articles</li>
                <li>CMS.Web.ArticleLines</li>
                <li>CMS.Web.ArticleLabels</li>
                <li>CMS.Web.FileManager</li>
                <li>CMS.Web.Guestbooks</li>
                <li>CMS.Web.Inquiries</li>
                <li>CMS.Web.WebForwards</li>
                <li>CMS.Web.WebProjects</li>
            </ul>
        </div>
        <div class="gray-box">
            <strong>Floorball:</strong>
        </div>
        <div class="gray-box">
            <ul>
                <li>CMS.Floorball</li>
                <li>CMS.Floorball.Projects</li>
                <li>CMS.Floorball.Seasons</li>
                <li>CMS.Floorball.Rounds</li>
                <li>CMS.Floorball.Tables</li>
                <li>CMS.Floorball.Teams</li>
                <li>CMS.Floorball.Players</li>
                <li>CMS.Floorball.Matches</li>
                <li>CMS.Floorball.TablesContent</li>
            </ul>
        </div>
        <div class="gray-box">
            <strong>Settings:</strong>
        </div>
        <div class="gray-box">
            <ul>
                <li>CMS.Settings</li>
                <li>CMS.Settings.UrlCache</li>
                <li>CMS.Settings.RoleCache</li>
                <li>CMS.Settings.Languages</li>
                <li>CMS.Settings.Keywords</li>
                <li>CMS.Settings.Users</li>
                <li>CMS.Settings.Groups</li>
                <li>CMS.Settings.UserLog</li>
                <li>CMS.Settings.ApplicationLog</li>
                <li>CMS.Settings.LocalizationBundles</li>
                <li>CMS.Settings.PersonalProperties</li>
                <li>CMS.Settings.PersonalNotes</li>
                <li>CMS.Settings.AdminMenu</li>
            </ul>
        </div>
    </web:frame>
</web:out>