<v:template src="~/templates/in-template.view">
	<div class="m-md-2 m-lg-4">
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

		<bs:row>
			<bs:column default="12" large="6" class="mb-4">
				<bs:card title="Repository" class="h-100">
					<div class="mb-1">
						<fa5:icon name="github" prefix="fab" />
						<sys:repositoryLink text="Open project page at GitHub" />
					</div>
					<div class="mb-1">
						<fa5:icon name="bug" />
						<sys:repositoryIssueCreateLink text="Report a new issue" />
					</div>
					<div>
						<fa5:icon name="globe-europe" />
						<a target="_blank" href="http://is4wfw.neptuo.com">Product website</a>
					</div>
				</bs:card>
			</bs:column>
			<bs:column default="12" large="6" class="mb-4">
				<bs:card title="Application" class="h-100">
					<div class="mb-1">
						<fa5:icon name="server" />
						<web:version />
					</div>
					<div class="mb-1">
						<fa5:icon name="database" />
						r<web:dbVersion />
					</div>
					<div>
						<fa5:icon name="user" />
						<login:info field="username" />
					</div>
				</bs:card>
			</bs:column>
		</bs:row>
	</div>
</v:template>