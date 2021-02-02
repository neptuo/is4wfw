<v:template src="~/templates/in-template.view">
    <web:a pageId="~/in/article-labels.view" class="fright" text="Article labels &raquo;" security:requirePerm="CMS.Web.ArticleLabels" />
    <web:a pageId="~/in/article-lines.view" text="Article lines &raquo;" security:requirePerm="CMS.Web.ArticleLines" />
    <a:setLine method="session" />
    <a:showManagement method="session" detailPageId="~/in/article-detail.view" newArticleButton="true" pageable="true" />
</v:template>