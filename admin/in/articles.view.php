<v:template src="~/templates/article-template.view">
    <web:a pageId="~/in/article-labels.view" class="fright" text="Article labels &raquo;" security:requirePerm="CMS.Web.ArticleLabels" />
    <web:a pageId="~/in/article-lines.view" text="Article lines &raquo;" security:requirePerm="CMS.Web.ArticleLines" />
    <artc:setLine method="session" />
    <artc:showManagement method="session" detailPageId="~/in/article-detail.view" newArticleButton="true" pageable="true" />
</v:template>