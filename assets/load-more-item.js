import $ from 'jquery';

$(document).ready(function(){
    var click = 1;
    var buttonId = '#js-load-more-item';
    $(buttonId).on('click', function(event) {
        event.preventDefault();
        var itemToLoad = $(buttonId).data('itemToLoad') * click;
        click++;
        $.get(this.href, {itemToLoad: itemToLoad}, function(data) {
            $('#js-item-target').append(data.html);
        }, 'json');
    });
});