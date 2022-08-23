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

})();