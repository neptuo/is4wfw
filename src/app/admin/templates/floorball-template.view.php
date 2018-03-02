<v:template src="~/templates/in-template.view">
	<php:using prefix="sport" class="php.libs.Sport">
		<web:frame title="Nastavení zobrazení">
    		<sport:selectProject useFrames="false" />
	    	<sport:selectSeason useFrames="false" showMsg="false" />
    		<sport:selectTeam useFrames="false" showMsg="false" />
    		<sport:selectTable useFrames="false" showMsg="false" />
	    	<div class="clear"></div>
		</web:frame>

		<v:content />
	</php:using>
</v:template>