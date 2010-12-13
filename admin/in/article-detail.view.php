<v:template src="~/templates/article-template.view">
    <web:a pageId="~/in/article-labels.view" class="fright" text="Article labels &raquo;" security:requirePerm="CMS.Web.ArticleLabels" />
    <web:a pageId="~/in/articles.view" text="&laquo; Bact to article list" security:requirePerm="CMS.Web.Articles" />
    <artc:editArticle />
</v:template>