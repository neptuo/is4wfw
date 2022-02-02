<php:using prefix="gb" class="php.libs.Guestbook">
	<gb:listAll useFrame="true" />
	<gb:setIdFromList />
	<gb:show guestbookId="gb:id" editable="true" useFrame="true" />
</php:using>