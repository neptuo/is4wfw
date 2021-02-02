<v:template src="~/templates/in-template.view">
    <web:a pageId="~/in/article-labels.view" class="fright" text="Article labels &raquo;" security:requirePerm="CMS.Web.ArticlesLabels" />
    <web:a pageId="~/in/articles.view" text="&laquo; Back to articles" security:requirePerm="CMS.Web.Articles" />
    <a:editLine />
    <a:showLines editable="true" newLineButton="true" />
</v:template>