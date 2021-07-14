function initConfirm() {
    $(".confirm").click(function (event) {
        var elm = ((event.srcElement) ? event.srcElement : event.target);
        var title = 'this';
        if (elm && elm.title && elm.title.length != 0) {
            title = elm.title;
        }
        if (!confirm(title + "?")) {
            event.preventDefault();
            return false;
        } else {
            return true;
        }
    });
}