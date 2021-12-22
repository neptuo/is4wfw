function initConfirm() {
    $(".confirm").click(function (event) {
        var title = 'Are you sure';
        if (this && this.title && this.title.length != 0) {
            title = this.title;
        }

        if (!confirm(title + "?")) {
            event.preventDefault();
            event.stopPropagation();
        }
    });
}