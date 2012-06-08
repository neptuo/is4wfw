<v:template src="~/templates/in-template.view">
    <v:panel security:requireGroup="admins">
        <web:a pageId="~/in/user-groups.view" text="&laquo; Back to user groups" security:requirePerm="CMS.Settings.Groups"/>
        <web:frame title="Group perms">
            Page.EditDetail, Page.AddNew, Page.ManageFiles, Page.ManageProperties, Page.Delete, Page.MoveTree, Page.CopyTree, Page.MoveUpDown, Page.AddLang, Page.Delete, Page.ManageRights, Page.TagLibs, Page.Head
			<br />
			Article.Head, Article.Content
            <br />
            CMS.Hint, CMS.HintProperties, CMS.HintPerms
            <br />
            CMS.Web, CMS.Web.Pages, CMS.Web.TextFiles, CMS.Web.Templates, CMS.Web.EmbeddedResources, CMS.Web.CustomForms, CMS.Web.Articles, CMS.Web.ArticleLines, CMS.Web.ArticleLabels, CMS.Web.FileManager, CMS.Web.Guestbooks, CMS.Web.Inquiries, CMS.Web.WebForwards, CMS.Web.WebProjects
            <br />
            CMS.Floorball, CMS.Floorball.Projects, CMS.Floorball.Seasons, CMS.Floorball.Rounds, CMS.Floorball.Tables, CMS.Floorball.Teams, CMS.Floorball.Players, CMS.Floorball.Matches, CMS.Floorball.TablesContent
            <br />
            CMS.Settings, CMS.Settings.UrlCache, CMS.Settings.RoleCache, CMS.Settings.Languages, CMS.Settings.Keywords, CMS.Settings.Users, CMS.Settings.Groups, CMS.Settings.UserLog, CMS.Settings.ApplicationLog, CMS.Settings.PersonalProperties, CMS.Settings.PersonalNotes, CMS.Settings.AdminMenu
        </web:frame>
    </v:panel>
</v:template>