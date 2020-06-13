$(document).ready(function() {
    $('.comments-list').each(function() {

        var $list = $(this);
        var formId = $list.data('form');
        var $form = $(formId);

        var $deleteLink = $list.find('[data-toggle ="comments-delete"]');
        var $editLink = $list.find('[data-toggle ="comments-edit"]');
        var $abuseLink = $list.find('[data-toggle ="comments-abuse"]');
        var $replyLink = $list.find('[data-toggle ="comments-reply"]');

        $editLink.on('click', function (event) {
            event.preventDefault();
            var id = $(this).data('key');
            var $item = $list.find('[data-comment-id="'+id+'"]');
            var comment = $item.find('> .media-body > .media-content > .comment-text').text();
            $form.find('input#comments-id').val(id);
            $form.find('textarea#comments-comment').val(comment);
        });

        $replyLink.on('click', function (event) {
            event.preventDefault();
            var parent_id = $(this).data('key');
            $form.find('input#comments-parent_id').val(parent_id);
        });

    });
});