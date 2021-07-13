import $ from 'jquery';

$(document).ready(function() {
    // Get the div that holds the collection of videos
    var $videoCollectionHolder = $('div.videos');
    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $videoCollectionHolder.data('index', $videoCollectionHolder.find('input').length);
    // add a delete link to all videos
    $videoCollectionHolder.find('div.item').each(function() {
        addFormDeleteLink($(this));
    });

    // Get the div that holds the collection of imagess
    var $imageCollectionHolder = $('div.images');
    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $imageCollectionHolder.data('index', $videoCollectionHolder.find('input').length);
    // add a delete link to all videos
    $imageCollectionHolder.find('div.item').each(function() {
        addFormDeleteLink($(this));
    });

    $('body').on('click', '.js-add-item-link', function(e) {
        var $collectionHolderClass = $(e.currentTarget).data('collectionHolderClass');
        // add a new tag form (see next code block)
        addFormToCollection($collectionHolderClass);
    })
});

function addFormToCollection($collectionHolderClass) {
    // Get the ul that holds the collection of tags
    var $collectionHolder = $('.' + $collectionHolderClass);

    // Get the data-prototype explained earlier
    var prototype = $collectionHolder.data('prototype');

    // get the new index
    var index = $collectionHolder.data('index');

    var newForm = prototype;
    // You need this only if you didn't set 'label' => false in your tags field in TaskType
    // Replace '__name__label__' in the prototype's HTML to
    // instead be a number based on how many items we have
    // newForm = newForm.replace(/__name__label__/g, index);

    // Replace '__name__' in the prototype's HTML to
    // instead be a number based on how many items we have
    newForm = newForm.replace(/__name__/g, index);

    // increase the index with one for the next item
    $collectionHolder.data('index', index + 1);

    // Display the form in the page in an div, before the "Add a tag" link
    var $newFormItem = $('<div class="item"></div>').append(newForm);
    // Add the new form at the end of the list
    $collectionHolder.append($newFormItem)

    // add a delete link to the new form
    addFormDeleteLink($newFormItem);
}

function addFormDeleteLink($formItem) {
    var $removeFormButton = $('<button type="button" class="btn btn-danger">Supprimer</button>');
    $formItem.append($removeFormButton);

    $removeFormButton.on('click', function(e) {
        // remove the form
        $formItem.remove();
    });
}
