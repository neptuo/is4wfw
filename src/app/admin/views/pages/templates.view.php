<php:using prefix="pg" class="php.libs.Page">
	<pg:editTemplate id="query:id" />
	<pg:showTemplates />
</php:using>

<js:script path="https://unpkg.com/monaco-editor@0.34.0/min/vs/loader.js" placement="tail" />
<js:script placement="tail">
	const textarea = document.getElementById("template-edit-detail-content");
	const container = document.getElementById("template-edit-detail-editor");

	require.config({ paths: { 'vs': '/js/vs' } });
	require(['vs/editor/editor.main'], function() {
		let editor = monaco.editor.create(container, {
			value: textarea.value,
			scrollBeyondLastLine: false,
			language: "html"
		});
		editor.focus();

		textarea.form.addEventListener("submit", e => {
			textarea.value = editor.getValue();
		});
	});
</js:script>