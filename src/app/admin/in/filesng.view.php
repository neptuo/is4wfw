<v:template src="~/templates/in-template.view">
	<loc:use name="fileadmin">
		<var:declare name="dirId" value="post:dir-id" />
		<web:condition when="var:dirId" isInverted="true">
			<var:declare name="dirId" value="0" />
		</web:condition>


		<web:condition when="post:new-import">
			<fa:importFileSystem dirId="var:dirId" />
		</web:condition>
		<web:condition when="post:file-swap-order">
			<fa:fileSwapOrder id1="post:file1-id" id2="post:file2-id" />
		</web:condition>
		<web:condition when="post:dir-swap-order">
			<fa:directorySwapOrder id1="post:dir1-id" id2="post:dir2-id" />
		</web:condition>

		<fa:upload />
		<fa:directoryEditor />

		<fa:directoryPath dirId="var:dirId" separator=" / ">
			<utils:concat output="title" value1="File browser" value2=" :: " value3="fa:directoryPath" />
			<web:frame title="utils:title">
				<div class="gray-box">
					<ui:form>
						<input type="hidden" name="dir-id" value="<web:out text="var:dirId" />" />
						<input type="submit" name="new-directory" value="<web:out text="loc:button.newdirectory" />" />
						<input type="submit" name="new-file" value="<web:out text="loc:button.newfile" />" />
					</ui:form>
					<edit:form submit="bulk-upload">
						<input type="hidden" name="dir-id" value="<web:out text="var:dirId" />" />
						<fa:upload dirId="var:dirId">
							<ui:filebox name="bulkFiles" isMulti="true" class="d-none" />
							<button name="bulk-upload" value="bulk-upload" class="d-none">XX</button>
							<button id="bulk-upload-button" type="button">Bulk upload</button>
						</fa:upload>
					</edit:form>
					<js:script placement="tail">
						
						$(function() {
							var $submit = $("button[name='bulk-upload']");
							var $filebox = $("input[name='bulkFiles[]']");
							$filebox.change(function() {
								$submit.click();
							});
							$("#bulk-upload-button").click(function(e) {
								e.preventDefault();
								$filebox.click();
							})
						})
						
					</js:script>
					|
					<ui:form>
						<input type="hidden" name="dir-id" value="<web:out text="var:dirId" />" />
						<input type="submit" name="new-zipfile" value="<web:out text="loc:button.newzipfile" />" class="new-zipfile" />
						<input type="submit" name="new-import" value="<web:out text="loc:button.import" />" class="confirm new-import" />
					</ui:form>

				</div>

				<fa:browser dirId="var:dirId" orderBy="order" parentName="..">
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
								<web:out text="fa:browserId" />
							</web:condition>
						</ui:columnTemplate>

						<ui:columnTemplate th-class="w80 th-edit" td-class="data-item" td-data-item-id="fa:browserId" td-data-item-type="fa:browserType">
							<web:condition when="fa:browserId" is="0" isInverted="true">
								<span class="action-placeholder">

								</span>
								<ui:form>
									<input type="hidden" name="dir-id" value="<web:out text="var:dirId" />" />
									<web:condition when="fa:browserType" is="0">
										<input type="hidden" name="directory-id" value="<web:out text="fa:browserId" />">
										<input type="hidden" name="edit-dir" value="Edit">
										<input type="image" src="/images/page_edi.png" name="edit-dir" value="Edit" title="Edit directory" class="">
									</web:condition>
									<web:condition when="fa:browserType" is="0" isInverted="true">
										<input type="hidden" name="file-id" value="<web:out text="fa:browserId" />" />
										<input type="hidden" name="edit-file" value="Edit" />
										<input type="image" src="/images/page_edi.png" name="edit-file" value="Edit" title="Edit file" />
									</web:condition>
								</ui:form>
								<ui:form>
									<input type="hidden" name="dir-id" value="<web:out text="var:dirId" />" />
									<web:condition when="fa:browserType" is="0">
										<input type="hidden" name="directory-id" value="<web:out text="fa:browserId" />" />
										<input type="hidden" name="delete-dir" value="Delete" />
										<utils:concat output="deleteMessage" value1="Delete directory, id(" value2="fa:browserId" value3=")" />
										<input class="confirm" type="image" src="/images/page_del.png" name="delete-dir" value="Delete" title="<web:out text="utils:deleteMessage" />" />
									</web:condition>
									<web:condition when="fa:browserType" is="0" isInverted="true">
										<input type="hidden" name="file-id" value="<web:out text="fa:browserId" />" />
										<input type="hidden" name="delete-file" value="Delete" />
										<utils:concat output="deleteMessage" value1="Delete file, id(" value2="fa:browserId" value3=")" />
										<input class="confirm" type="image" src="/images/page_del.png" name="delete-file" value="Delete" title="<web:out text="utils:deleteMessage" />" />
									</web:condition>
								</ui:form>
							</web:condition>
						</ui:columnTemplate>

						<ui:columnTemplate header="Name" th-class="th-name">
							<web:condition when="fa:browserType" is="0">
								<ui:form>
									<input type="hidden" name="dir-id" value="<web:out text="fa:browserId" />" />
									<input type="submit" name="ch-dir" value="<web:out text="fa:browserName" />" />
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

<js:script placement="tail">
	function prepareForm(dirId, type, id1, id2, direction) {
		var html = '<form method="post">';
		html += '<input type="hidden" name="dir-id" value="' + dirId + '" />';
		html += '<input type="image" src="/images/arro_' + (direction[0] + direction[1]) + '.png" name="' + type + '-swap-order" value="swap" title="Move ' + direction + '" />';
		html += '<input type="hidden" name="' + type + '-swap-order" value="swap" />';
		html += '<input type="hidden" name="' + type + '1-id" value="' + id1 + '" />';
		html += '<input type="hidden" name="' + type + '2-id" value="' + id2 + '" />';
		html += '</form>';
		return html;
	}

	function prepareForms($items, type) {
		$items.each(function(i) {
			var $item = $(this);
			var $placeholder = $item.find(".action-placeholder");

			var dirId = <web:out text="var:dirId" />;
			var html = '';
			var cssClass = [];
			if (i > 0) {
				html += prepareForm(dirId, type, $($items[i - 1]).attr("data-item-id"), $item.attr("data-item-id"), 'up');
				cssClass.push("has-prev");
			}
			if (i < $items.length - 1) {
				html += prepareForm(dirId, type, $item.attr("data-item-id"), $($items[i + 1]).attr("data-item-id"), 'down');
				cssClass.push("has-next");
			}

			cssClass = cssClass.join(" ");
			$placeholder.html(html).addClass(cssClass);
			
		});
	}

	var $dataItems = $(".data-item");
	var $directories = $dataItems.filter("[data-item-type=0]:not([data-item-id=0])");
	var $files = $dataItems.filter("[data-item-type]:not([data-item-type=0])");
	prepareForms($directories, "dir");
	prepareForms($files, "file");
</js:script>