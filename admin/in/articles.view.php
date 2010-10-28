<v:template src="~/templates/article-template.view">
  <a href="~/in/article-labels.view" class="fright">Article labels &raquo;</a>
  <a href="~/in/article-lines.view">Article lines &raquo;</a>
  <artc:setLine method="session" />
  <artc:showManagement method="session" detailPageId="~/in/article-detail.view" newArticleButton="true" />
</v:template>