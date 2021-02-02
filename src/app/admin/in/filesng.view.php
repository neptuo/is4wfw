<v:template src="~/templates/in-template.view">
	<loc:use name="FileAdmin">

		<web:condition when="post:new-import">
			<fa:importFileSystem dirId="post:dir-id" />
		</web:condition>

		<fa:upload />
		<fa:directoryEditor />

		<fa:directoryPath dirId="post:dir-id" separator=" / ">
			<utils:concat output="title" value1="File browser" value2=" :: " value3="fa:directoryPath" />
			<web:frame title="utils:title">
				<div class="gray-box">
					<ui:form>
						<input type="hidden" name="dir-id" value="<web:getProperty name="post:dir-id" />" />
						<input type="submit" name="new-file" value="<web:getProperty name="loc:button.newfile" />" />
						<input type="submit" name="new-directory" value="<web:getProperty name="loc:button.newdirectory" />" />
						|
						<input type="submit" name="new-zipfile" value="<web:getProperty name="loc:button.newzipfile" />" class="new-zipfile" />
						<input type="submit" name="new-import" value="<web:getProperty name="loc:button.import" />" class="confirm new-import" />
					</ui:form>
				</div>

				<fa:browser dirId="post:dir-id" orderBy="order" parentName="..">
					<ui:grid items="fa:browserList" class="dir-list standart">
						<utils:concat output="iconClass" value1="file-icon" value2=" " value3="fa:browserExtension" />
						<var:declare name="iconClass" value="utils:iconClass" />
						<web:condition when="fa:browserType" is="0">
							<var:declare name="iconClass" value="dir-icon" />
						</web:condition>
						
						<ui:columnTemplate th-class="w20" td-class="var:iconClass">
							
						</ui:columnTemplate>
						<ui:columnTemplate header="Id" th-class="w30 th-id">
							<web:condition when="fa:browserId" is="0" isInverted="true">
								<web:getProperty name="fa:browserId" />
							</web:condition>
						</ui:columnTemplate>

						<ui:columnTemplate th-class="w40 th-edit">
							<web:condition when="fa:browserId" is="0" isInverted="true">
								<ui:form>
									<input type="hidden" name="dir-id" value="<web:getProperty name="post:dir-id" />" />
									<web:condition when="fa:browserType" is="0">
										<input type="hidden" name="directory-id" value="<web:getProperty name="fa:browserId" />">
										<input type="hidden" name="edit-dir" value="Edit">
										<input type="image" src="/images/page_edi.png" name="edit-dir" value="Edit" title="Edit directory" class="">
									</web:condition>
									<web:condition when="fa:browserType" is="0" isInverted="true">
										<input type="hidden" name="file-id" value="<web:getProperty name="fa:browserId" />" />
										<input type="hidden" name="edit-file" value="Edit" />
										<input type="image" src="/images/page_edi.png" name="edit-file" value="Edit" title="Edit file" />
									</web:condition>
								</ui:form>
								<ui:form>
									<input type="hidden" name="dir-id" value="<web:getProperty name="post:dir-id" />" />
									<web:condition when="fa:browserType" is="0">
										<input type="hidden" name="directory-id" value="<web:getProperty name="fa:browserId" />" />
										<input type="hidden" name="delete-dir" value="Delete" />
										<utils:concat output="deleteMessage" value1="Delete directory, id(" value2="fa:browserId" value3=")" />
										<input class="confirm" type="image" src="/images/page_del.png" name="delete-dir" value="Delete" title="<web:getProperty name="utils:deleteMessage" />" />
									</web:condition>
									<web:condition when="fa:browserType" is="0" isInverted="true">
										<input type="hidden" name="file-id" value="<web:getProperty name="fa:browserId" />" />
										<input type="hidden" name="delete-file" value="Delete" />
										<utils:concat output="deleteMessage" value1="Delete file, id(" value2="fa:browserId" value3=")" />
										<input class="confirm" type="image" src="/images/page_del.png" name="delete-file" value="Delete" title="<web:getProperty name="utils:deleteMessage" />" />
									</web:condition>
								</ui:form>
							</web:condition>
						</ui:columnTemplate>

						<ui:columnTemplate header="Name" th-class="th-name">
							<web:condition when="fa:browserType" is="0">
								<ui:form>
									<input type="hidden" name="dir-id" value="<web:getProperty name="fa:browserId" />" />
									<input type="submit" name="ch-dir" value="<web:getProperty name="fa:browserName" />" />
								</ui:form>
							</web:condition>
							<web:condition when="fa:browserType" is="0" isInverted="true">
								<fileUrl:declare name="file" id="fa:browserId" />
								<web:a pageId="fileUrl:file" target="_blank" text="fa:browserName" />
							</web:condition>
						</ui:columnTemplate>

						<ui:columnTemplate header="Direct link" th-class="w100 th-dir-physical-path">
							<web:condition when="fa:browserType" is="0" isInverted="true">
								<fa:fileDirectUrl fileId="fa:browserId">
									<web:a target="_blank" pageId="fa:fileDirectUrl" text="Open" /> 
								</fa:fileDirectUrl>
							</web:condition>
						</ui:columnTemplate>

						<ui:columnDateTime header="Timestamp" value="fa:browserTimestamp" format="d.m.Y H:m:s" th-class="w160 th-timestamp" />

						<ui:column header="Type" value="fa:browserExtension" th-class="w60 th-type" />
					</ui:grid>
				</fa:browser>
			</web:frame>
		</fa:directoryPath>

	</loc:use>
</v:template>