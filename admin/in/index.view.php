<v:template src="~/templates/in-template.view">
	<php:register tagPrefix="sys" classPath="php.libs.System" />
	<div id="home-desktop" class="home-cover">
    <fieldset>
        <legend>
            <strong>Personal notes:</strong>
         </legend>
        <sys:printNotes useFrames="false" showMsg="false" />
    </fieldset>
	</div>
	<php:unregister tagPrefix="sys" />
</v:template>