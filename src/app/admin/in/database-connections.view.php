<v:template src="~/templates/in-template.view">
	<v:panel security:requireGroup="admins">
		<sys:editConnection showMsg="true" />
		<sys:listConnections />
	</v:panel>
</v:template>