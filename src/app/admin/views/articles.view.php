<web:a pageId="route:articleLabels" class="fright" text="Article labels &raquo;" security:requirePerm="CMS.Web.ArticleLabels" />
<web:a pageId="route:articleLines" text="Article lines &raquo;" security:requirePerm="CMS.Web.ArticleLines" />
<a:setLine method="session" />
<a:showManagement method="session" detailPageId="route:articleDetail" newArticleButton="true" pageable="true" />