<v:template src="~/templates/in-template.view">
	<web:frame title="Nastavení zobrazení">
		<s:selectProject useFrames="false" />
		<s:selectSeason useFrames="false" showMsg="false" />
		<s:selectTeam useFrames="false" showMsg="false" />
		<s:selectTable useFrames="false" showMsg="false" />
		<div class="clear"></div>
	</web:frame>

	<v:content />
</v:template>