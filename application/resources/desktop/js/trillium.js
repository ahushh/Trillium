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
            var currentID = self.parent().parent().attr('id');
            currentID = currentID.replace(/post_/, '');
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