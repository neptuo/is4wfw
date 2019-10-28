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
				<strong>Debug mode:</strong>
			</legend>
			<web:condition when="post:debug">
				<web:switch when="post:debug">
					<web:case is="enable">
						<web:debug isEnabled="true" />
					</web:case>
					<web:case is="disable">
						<web:debug isEnabled="false" />
					</web:case>
				</web:switch>
				<web:redirectToSelf />
			</web:condition>

			<ui:form>
				<web:switch when="web:debug">
					<web:case is="true">
						<button name="debug" value="disable">Disable debug mode</button>
					</web:case>
					<web:case>
						<button name="debug" value="enable">Enable debug mode</button>
					</web:case>
				</web:switch>
			</ui:form>
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