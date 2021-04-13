<v:template src="~/templates/in-template.view">
	<div id="home-desktop" class="home-cover">
		<bs:card title="Personal notes" class="mb-4">
			<sys:printNotes useFrames="false" showMsg="false" />
		</bs:card>
		
		<bs:card title="Debug mode" class="mb-4">
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
						<bs:button name="debug" value="disable" text="Disable debug mode" />
					</web:case>
					<web:case>
						<bs:button name="debug" value="enable" text="Enable debug mode" />
					</web:case>
				</web:switch>
			</ui:form>
		</bs:card>

		<bs:card title="Repository" class="mb-4">
			<sys:repositoryLink text="Open project page at GitHub" />
			<br />
			<sys:repositoryIssueCreateLink text="Report a new issue" />
		</bs:card>
	</div>
</v:template>