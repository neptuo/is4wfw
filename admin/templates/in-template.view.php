<v:title value="is4wfw" />
<v:template src="~/templates/template.view">
	<php:register tagPrefix="js" classPath="php.libs.Js" />
	<php:register tagPrefix="sys" classPath="php.libs.System" />
	<php:register tagPrefix="wp" classPath="php.libs.WebProject" />
	<js:cmsResources useWindows="sys:cmsWindowsStyle" />
	<php:unregister tagPrefix="sys" />
	<php:unregister tagPrefix="js" />
	<div class="cms">
		<div id="cms-head" class="head">
	    <login:logout group="web-admins" pageId="~/login.view" />
	    <div id="logon-count-down" class="logon-count-down">
	      <div class="count-down-cover">
	        <span class="count-down-label">Login session <br/>expires in: </span>
	        <span id="count-down-counter" class="count-down-counter"><web:systemPropertyValue name="Login.session" /></span>
	      </div>
	    </div>
	    <login:info />
	    <wp:selectProject showMsg="false" useFrames="false" />
	    <div class="web-version">
	      <div class="label">CMS version</div>
	      <div class="value">
	        <web:cmsVersion />
	      </div>
	    </div>
	    <div class="web-version">
	      <div class="label">Web version</div>
	      <div class="value">
	        <web:version />
	      </div>
	    </div>
	    <div id="loading" class="web-version lorequireGroupading">
	      Loading ...
	    </div>
	    <div id="cms-menus">
	      <div class="cms-menu cms-menu-3">
	        <span class="menu-root"><a href="~/in/hint.view"></a></span>
	      </div>
	      <v:panel class="cms-menu" security:requireGroup="cms-access">
	        <span class="menu-root"><a href="~/in/index.view">Web</a></span>
	        <m:xmlMenu file="~/templates/menus/web.xml" />
	      </v:panel>
	      <v:panel class="cms-menu cms-menu-2" security:requireGroup="floorball-access">
	        <span class="menu-root"><web:a pageId="~/in/floorball/seasons.view" text="Floorball" /></span>
	      	<m:xmlMenu file="~/templates/menus/floorball.xml" />
	      </v:panel>
	      <v:panel class="cms-menu cms-menu-4">
	        <span class="menu-root"><a href="~/in/personal-notes.view">Application settings</a></span>
	        <m:xmlMenu file="~/templates/menus/settings.xml" />
	      </v:panel>
	    </div>
	  </div>
	  <div class="dock-bar">
	    <div class="dock-in">
	      <div id="dock-left" class="dock-left">
	      </div>
	      <div id="dock" class="dock-mid">
	      </div>
	      <div id="dock-right" class="dock-right">
	        <div id="web-ajax-log-cover" class="web-ajax-log-cover">
	
	        </div>
	        <div id="clock" class="clock">
	          <div id="hours" class="clock-hours">
	          --
	          </div>:<div id="minutes" class="clock-minutes">
	          --
	          </div>:<div id="seconds" class="clock-seconds">
	          --
	          </div>
	        </div>
	      </div>
	    </div>
	  </div>
	  <div id="cms-body" class="body">
	    <v:content />
	  </div>
	</div>
	<php:unregister tagPrefix="wp" />
</v:template>
