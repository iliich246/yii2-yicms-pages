;(function() {
    var createPageButton = $('.create-page-button');
    var homeUrl = $(createPageButton).data('homeUrl');
    var pageUpUrl = homeUrl + '/pages/dev/page-up-order';
    var pageDownUrl = homeUrl + '/pages/dev/page-down-order';

    var pjaxContainer = $('#update-pages-list-container');

    $(document).on('click', '.glyphicon-arrow-up', function() {
        $.pjax({
            url: pageUpUrl + '?pageId=' + $(this).data('pageId'),
            container: '#update-pages-list-container',
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });

    $(document).on('click', '.glyphicon-arrow-down', function() {
        $.pjax({
            url: pageDownUrl + '?pageId=' + $(this).data('pageId'),
            container: '#update-pages-list-container',
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });

    $(pjaxContainer).on('pjax:error', function(xhr, textStatus) {
        bootbox.alert({
            size: 'large',
            title: "There are some error on ajax request!",
            message: textStatus.responseText,
            className: 'bootbox-error'
        });
    });
})();
