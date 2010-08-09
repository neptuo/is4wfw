<v:template src="~/templates/article-template.view">
  <a href="~/in/article-lines.view">Edit article lines</a>
  <artc:setLine method="session" />
  <artc:showManagement method="session" detailPageId="~/in/article-detail.view" />
  <artc:createArticle detailPageId="~/in/article-detail.view" method="session" />
</v:template>