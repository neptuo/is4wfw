<php:using prefix="pg" class="php.libs.Page">
	<pg:editTemplate id="query:id" />
	<pg:showTemplates />
</php:using>

<js:script path="https://unpkg.com/monaco-editor@0.34.0/min/vs/loader.js" placement="tail" />
<js:script placement="tail">
	$(".monaco-editor").each(function() {
		const $container = $(this);
		const $input = $container.find("textarea");
		if ($input.length == 1) {
			require.config({ paths: { 'vs': 'https://unpkg.com/monaco-editor@0.34.0/min/vs' } });
			require(['vs/editor/editor.main'], function() {
				let editor = monaco.editor.create($container[0], {
					value: $input.val(),
					scrollBeyondLastLine: false,
					language: "html",
					theme: $container.data('theme')
				});
				editor.focus();
				
				const editorKey = this.id + "-editorStoredSettings";

				const storedJson = localStorage.getItem(editorKey);
				if (storedJson) {
					const stored = JSON.parse(storedJson);
					editor.setSelection(stored.selection);
					editor.setScrollTop(stored.scroll.top);
					editor.setScrollLeft(stored.scroll.left);

					localStorage.removeItem(editorKey);
				}

				window.addEventListener("resize", () => {
					editor.layout();
				});

				$input[0].form.addEventListener("submit", e => {
					const selection = editor.getSelection();
					localStorage.setItem(editorKey, JSON.stringify({
						selection: selection,
						scroll: {
							top: editor.getScrollTop(),
							left: editor.getScrollLeft(),
						}
					}));

					$input.val(editor.getValue());
				});
			});
		} else {
			$container.append("<h4 class='error'>Can't find an textarea to get value from</h4>");
		}
	});
	
</js:script>
