Mercury.on('saved', function() {
    var container = $('#notifications');
    if (! container.length ) {
        container = $('<div />', {
            id: 'notifications'
        }).appendTo('body');
    }
    var success = $('<div />', {
        class: 'success',
        text: 'Page saved successfully!'
    }).appendTo(container);
    success.fadeIn('fast').delay(3000).fadeOut('slow');
});
