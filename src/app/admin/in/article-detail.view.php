<v:template src="~/templates/article-template.view">
    <web:a pageId="~/in/article-labels.view" class="fright" text="Article labels &raquo;" security:requirePerm="CMS.Web.ArticleLabels" />
    <web:a pageId="~/in/articles.view" text="&laquo; Back to article list" security:requirePerm="CMS.Web.Articles" />
    <artc:editArticle backPageId="~/in/articles.view" />
    
    <a:detail id="query:article-id" articleLangId="query:language-id" showError="false">
        <web:condition when="a:directoryId">
            <fa:upload dirId="a:directoryId" />
            <fa:browser dirId="a:directoryId" browsable="false" />
        </web:condition>
    </a:detail>
</v:template>