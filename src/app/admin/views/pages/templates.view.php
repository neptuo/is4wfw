<php:using prefix="pg" class="php.libs.Page">
	<pg:editTemplate id="query:id" />
	<pg:showTemplates />
</php:using>

<js:script path="https://unpkg.com/monaco-editor@0.34.0/min/vs/loader.js" placement="tail" />
<js:script placement="tail">
	$(".monaco-container").each(function() {
		$container = $(this);
		const textarea = $container.find("textarea")[0];
		if (textarea) {
			require.config({ paths: { 'vs': 'https://unpkg.com/monaco-editor@0.34.0/min/vs' } });
			require(['vs/editor/editor.main'], function() {
				let editor = monaco.editor.create($container.find(".monaco-editor")[0], {
					value: textarea.value,
					scrollBeyondLastLine: false,
					language: "html"
				});
				editor.focus();
				
				const editorKey = textarea.id + "editorSelection";

				const selectionJson = localStorage.getItem(editorKey);
				if (selectionJson) {
					const selection = JSON.parse(selectionJson);
					editor.setSelection(selection);

					localStorage.removeItem(editorKey);
				}

				window.addEventListener("resize", () => {
					editor.layout();
				});

				textarea.form.addEventListener("submit", e => {
					const selection = editor.getSelection();
					localStorage.setItem(editorKey, JSON.stringify(selection));

					textarea.value = editor.getValue();
				});
			});
		}
	});
	
</js:script>
