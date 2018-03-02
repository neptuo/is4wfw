<v:template src="~/templates/article-template.view">
    <web:a pageId="~/in/articles.view" text="&laquo; Back to articles" security:requirePerm="CMS.Web.Articles" />
    <artc:showLabelEdit />
    <artc:showLabels />
</v:template>