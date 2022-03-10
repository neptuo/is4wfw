<php:using prefix="user" class="php.libs.User" security:requireGroup="admins">
	<web:frame title="User log">
		<user:truncateLog useFrames="false" />
		<hr />
		<user:showLog useFrames="false" />
	</web:frame>
</php:using>