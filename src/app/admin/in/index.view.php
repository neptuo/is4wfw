<v:template src="~/templates/in-template.view">
	<div id="home-desktop" class="home-cover">
		<fieldset>
			<legend>
				<strong>Personal notes:</strong>
			</legend>
			<sys:printNotes useFrames="false" showMsg="false" />
		</fieldset>

		<br >
		<fieldset>
			<legend>
				<strong>Repository:</strong>
			</legend>
			<sys:repositoryLink text="Open project page at GitHub" />
			<br />
			<sys:repositoryIssueCreateLink text="Report a new issue" />
		</fieldset>
	</div>
</v:template>