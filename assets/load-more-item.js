import $ from 'jquery';

$(document).ready(function(){
    var click = 1;
    var buttonId = '#js-load-more-item';
    var itemLimit = $(buttonId).data('itemToLoad');
    $(buttonId).on('click', function(event) {
        event.preventDefault();
        var itemOffset = itemLimit * click;
        click ++ ;
        $.get(this.href, {itemLimit: itemLimit, itemOffset: itemOffset}, function(data) {
            $('#js-item-target').append(data.html);
        }, 'json');
    });
});