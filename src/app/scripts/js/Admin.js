(function() {
    var tooltips = [];
    var cookies = new Cookies();

    function createTooltips() {
        var elements = document.querySelectorAll('.main-menu [title]');
        for (let i = 0; i < elements.length; i++) {
            const element = elements[i];
            
            tooltips.push(new bootstrap.Tooltip(element, {
                boundary: 'window',
                placement: 'right'
            }));
        }
    }

    function destroyTooltips() {
        tooltips.forEach(function(t) {
            t.dispose();
        });

        tooltips = [];
    }

    var $mainMenuCol = $(".main-menu-col");
    $(".navbar-toggler").click(function(e) {
        e.preventDefault();
        
        if ($mainMenuCol.hasClass("main-menu-collapsed")) {
            $mainMenuCol.removeClass("main-menu-collapsed");
            cookies.create("mainMenu", null);
            destroyTooltips();
        } else {
            $mainMenuCol.addClass("main-menu-collapsed");
            cookies.create("mainMenu", "collapsed");
            createTooltips();
        }
    });

    if ($mainMenuCol.hasClass("main-menu-collapsed")) {
        createTooltips();
    }

    $(".menu-toggler").click(function(e) {
        const $root = $(this).parents(".cms-menu");
        
        const name = $root.attr("data-menu");
        if ($root.hasClass("cms-menu-collapsed")) {
            $root.removeClass("cms-menu-collapsed");
            cookies.create("cmsMenu-" + name, "opened");
        } else {
            $root.addClass("cms-menu-collapsed");
            cookies.create("cmsMenu-" + name, "collapsed");
        }

        e.preventDefault();
    });

    $(".favorites-settings a[data-mode]").click(function(e) {
        const $this = $(this);
        const mode = $this.data("mode");

        $this.parents("nav").attr("data-mode", mode);
        cookies.create("cmsMenuMode", mode);
        e.preventDefault();
    })

    let hasKeybindings = false;
    const keybindings = {};
    $("[data-keybinding]").each(function () {
        const $el = $(this);
        let binding = $el.data("keybinding");
        keybindings[binding] = $el;
        hasKeybindings = true;
    });

    if (hasKeybindings) {
        const handler = e => {
            for (const key in keybindings) {
                if (Object.hasOwnProperty.call(keybindings, key)) {
                    let skip = false;
                    const parts = key.split('+');
                    for (let i = 0; i < parts.length - 1; i++) {
                        const modifier = parts[i];
                        if (!e[modifier + "Key"]) {
                            skip = true;
                            continue;
                        }
                    }

                    if (skip || e.key !== parts[parts.length - 1]) {
                        continue;
                    }

                    e.preventDefault();

                    const $el = keybindings[key];
                    $el.click();
                    return false;
                }
            }

            return true;
        };
        document.addEventListener('keydown', handler);
        window.keybindingHandler = handler;
    }

    $(".monaco-container").each(function() {
		const $container = $(this);
		const $input = $container.find("textarea");

        require.config({ paths: { 'vs': 'https://unpkg.com/monaco-editor@0.34.0/min/vs' } });
        require(['vs/editor/editor.main'], function() {
            let editor = null;
            let editorParent = null
            let setEditorValue = null;
            let beforeSubmit = [];
		    if ($input.length == 1) {
                // Normal single editor.
                editorParent = $container[0];
                setEditorValue = () => editor.setValue($input.val());
                beforeSubmit.push(() => $input.val(editor.getValue()));
            } else if ($container.hasClass("monaco-container-list")) {
                // Page's multi field editor.
                editorParent = $container.find(".monaco-container-target")[0];
                setEditorValue = setLastEditorValue;
                beforeSubmit.push(setLastInputValue);

                const $buttons = $container.find("button");
                $buttons.click(e => {
                    if (lastName) {
                        setLastInputValue();
                    }

                    lastName = $(e.currentTarget).data("editor");
                    setLastEditorValue();

                    e.preventDefault();
                });
                
                let lastName = $buttons.last().data("editor");
                function setLastInputValue() {
                    const $lastInput = $input.filter("[name='" + lastName + "']");
                    $lastInput.val(editor.getValue());
                }

                function setLastEditorValue() {
                    editor.setValue($input.filter("[name='" + lastName + "']").val());
                }
            } else {
                $container.append("<h4 class='error'>Can't find an textarea to get value from</h4>");
                return;
            }

            editor = monaco.editor.create(editorParent, {
                scrollBeyondLastLine: false,
                language: $container.data('language'),
                theme: $container.data('theme')
            });
                    
            const editorKey = this.id + "-editorStoredSettings";
            
            setEditorValue();

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

                beforeSubmit.forEach(h => h());
            });
            
            editor.focus();
        });
	});

})();