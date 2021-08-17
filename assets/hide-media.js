import $ from 'jquery';

$(document).ready(function(){
    $("#js-media").click(function() {
        $("#media").toggleClass("d-none");
    });
});