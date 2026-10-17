/*
 * The upload dialog of the object relation list field (content/datatype/edit/ezobjectrelationlist_ajaxuploader.tpl):
 * Exponential UI's expAjaxUploader on the .relation-upload-new buttons; the object just created is added to the
 * relation list. The texts are data-text-* attributes of the button.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/features/6.0/content-security-policy.md).
 */
(function () {
    if (!window.Exp || !window.Exp.ready) { return; }
    Exp.ready(function ($) {
        if (!$.fn.expAjaxUploader) { return; }
        // the texts of the dialog: data-text-* of the first button (each button carries the same)
        var button = $('.relation-upload-new').first();
        var text = function (name) { return button.attr('data-text-' + name) || ''; };
        $('.relation-upload-new').expAjaxUploader({
            open: {
                action: 'ezajaxuploader::uploadform::ezobjectrelationlist'
            },
            upload: {
                action: 'ezajaxuploader::upload::ezobjectrelationlist?ContentType=html',
                form: 'form.ajaxuploader-upload'
            },
            location: {
                action: 'ezajaxuploader::preview::ezobjectrelationlist',
                form: 'form.ajaxuploader-location',
                browse: 'div.ajaxuploader-browse',
                required: text('required')
            },
            preview: {
                form: 'form.ajaxuploader-preview',

                // this is the uploader: the object just created is added to the relation list
                callback: function () {
                    var box = $('#ezobjectrelationlist_browse_' + this.conf.target.ObjectRelationsAttributeId),
                        table = box.find('table.list').first(),
                        tbody = table.find('tbody').first(),
                        last = tbody.children().last(),
                        tr, tds, priority,
                        result = this.lastMetaData;

                    if ( !table.hasClass('hide') ) {
                        // table is visible, clone the last line
                        tr = last.clone();
                        tbody.append(tr);
                        if ( last.hasClass('bgdark') ) {
                            tr.removeClass('bgdark').addClass('bglight');
                        } else {
                            tr.removeClass('bglight').addClass('bgdark');
                        }
                    } else {
                        // table is hidden, no related object yet
                        // the only line in the table is the "template line"
                        tr = last;
                        table.removeClass('hide');
                    }
                    tds = tr.children();
                    tds.eq(0).find('input').val(result.object_info.id);
                    tds.eq(1).text(result.object_info.name);
                    tds.eq(2).text(result.object_info.class_name);
                    tds.eq(3).text(result.object_info.section_name);
                    tds.eq(4).text(result.object_info.published);
                    priority = tds.eq(5).find('input').first();
                    priority.val(parseInt(priority.val(), 10) + 1);

                    box.find('.ezobject-relation-remove-button').first().removeClass('button-disabled').addClass('button').prop('disabled', false);
                    box.find('.ezobject-relation-no-relation').addClass('hide');

                    this.close();
                }
            },
            validationErrorText: text('validation'),
            parseJSONErrorText: text('parse-json'),
            title: text('title')
        });
    });
})();
