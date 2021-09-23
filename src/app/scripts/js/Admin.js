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

})();