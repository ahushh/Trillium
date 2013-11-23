// Preview post in popup window
var previewPost = {
    stored: [],
    dialogSettings: {
        draggable: true,
        height:    'auto',
        width:     'auto',
        modal:     false,
        resizable: false,
        minWidth:  325,
        minHeight: 180,
        title:     'Post preview',
        autoOpen:  true,
        drag:      function (event, ui) {ui.position.top = event.pageY; /* Keep visible */}
    },
    show: function (event, post) {
        event.preventDefault();
        if (!previewPost.stored[post]) {
            var originalPost = $('#post_' + post);
            if (originalPost.length) {
                var preview = originalPost.clone().dialog(previewPost.dialogSettings);
            } else {
                preview = $('<div></div>').text('Post is not exists').dialog(previewPost.dialogSettings);
            }
            previewPost.stored[post] = preview;
        }
        previewPost.stored[post].dialog("open");
    }
};

// Answers map
var answers = {
    stored: {},
    store: function () {
        $('.answer').each(function () {
            var self = $(this);
            var currentID = self.attr('rel');
            var refID = self.attr('href').replace('#', 'post_');
            if (!answers.stored[refID]) {
                answers.stored[refID] = [];
            }
            answers.stored[refID].push(currentID);
        });
    },
    build: function () {
        answers.store();
        $.each(answers.stored, function (post, answers) {
            post = $('#' + post);
            var answersContainer = $('<div></div>').text('Answers: ');
            $.each(answers, function (index, value) {
                $('<a></a>')
                    .text('>>' + value)
                    .attr({href: '#', onclick: 'previewPost.show(event, ' + value + ')'})
                    .appendTo(answersContainer);
                answersContainer.html(answersContainer.html() + ' ');
            });
            answersContainer.appendTo(post);
        });
    }
};

// Preview OP Post
var previewThread = {
    stored: [],
    hideThreads: function () {
        $.each(previewThread.stored, function (index, item) {if (item) {item.hide();}});
    },
    run: function (id) {
        var wait = $('#wait');
        var previewContainer = $('#previewContainer');
        previewContainer.hide(400, function () {
            $('#messageForm').hide();
            wait.toggle();
        });
        if (!previewThread.stored[id]) {
            $.ajax('http://' + document.location.hostname + '/ajax/post/' + id, {async: false, dataType: "json"})
                .done(function (data) {
                    previewThread.stored[id] = previewThread.createPost(data.post, data.images).appendTo(previewContainer);
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR, textStatus, errorThrown);
                });
        }
        previewThread.hideThreads();
        wait.toggle(400, function () {
            previewThread.stored[id].show();
            previewContainer.show();
        });
    },
    createPost: function (post, images) {
        var postContainer = $('<div></div>').attr('class', 'item')
            .html('ID: ' + post.id + ' (' + post.time + ')');
        if (images.length) {
            var postImages = $('<div></div>');
            $.each(images, function (index, image) {
                var imageContainer = $('<div></div>');
                var imageLink = $('<a></a>').attr({href: image.original});
                $('<img />').attr({src: image.thumbnail}).appendTo(imageLink);
                imageLink.appendTo(imageContainer);
                $('<div></div>')
                    .text(image.resolution + ' / ' + image.size + ' / ' + image.type)
                    .appendTo(imageContainer);
                imageContainer.appendTo(postImages);
            });
            postImages.appendTo(postContainer);
        }
        return postContainer.html(postContainer.html() + '<p>' + post.text + '</p>');
    }
};