<web:a pageId="route:articleLabels" class="fright" text="Article labels &raquo;" security:requirePerm="CMS.Web.ArticleLabels" />
<web:a pageId="route:articles" text="&laquo; Back to article list" security:requirePerm="CMS.Web.Articles" />
<a:editArticle backPageId="route:articles" />

<a:detail id="query:article-id" articleLangId="query:language-id" showError="false">
    <web:condition when="a:directoryId">
        <fa:upload dirId="a:directoryId" />
        <fa:browser dirId="a:directoryId" browsable="false" />
    </web:condition>
</a:detail>