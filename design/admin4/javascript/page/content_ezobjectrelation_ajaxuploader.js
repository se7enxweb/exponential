/*
 * The upload dialog of the object relation field (content/datatype/edit/ezobjectrelation_ajaxuploader.tpl):
 * Exponential UI's expAjaxUploader on the .simple-relation-upload-new buttons; the object just created goes into the
 * relation. The texts are data-text-* attributes of the button.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/en/us/features/6.0/content-security-policy.md).
 */
(function () {
    if (!window.Exp || !window.Exp.ready) { return; }
    Exp.ready(function ($) {
        if (!$.fn.expAjaxUploader) { return; }
        // the texts of the dialog: data-text-* of the first button (each button carries the same)
        var button = $('.simple-relation-upload-new').first();
        var text = function (name) { return button.attr('data-text-' + name) || ''; };
        $('.simple-relation-upload-new').expAjaxUploader({
            open: {
                action: 'ezajaxuploader::uploadform::ezobjectrelation'
            },
            upload: {
                action: 'ezajaxuploader::upload::ezobjectrelation?ContentType=html',
                form: 'form.ajaxuploader-upload'
            },
            location: {
                action: 'ezajaxuploader::preview::ezobjectrelation',
                form: 'form.ajaxuploader-location',
                browse: 'div.ajaxuploader-browse',
                required: text('required')
            },
            preview: {
                form: 'form.ajaxuploader-preview',

                // this is the uploader: the object just created goes into the relation
                callback: function () {
                    var box = $('#ezobjectrelation_browse_' + this.conf.target.ObjectRelationsAttributeId),
                        table = box.find('table.list').first(),
                        tds = table.find('tbody').first().children().last().children(),
                        result = this.lastMetaData;

                    table.removeClass('hide');
                    tds.eq(0).text(result.object_info.name);
                    tds.eq(1).text(result.object_info.class_name);
                    tds.eq(2).text(result.object_info.section_name);
                    tds.eq(3).text(result.object_info.published);

                    box.find('input[name*=_data_object_relation_id_]').first().val(result.object_info.id);
                    box.find('.ezobject-relation-remove-button').first().removeClass('button-disabled').addClass('button').prop('disabled', false);
                    box.find('.ezobject-relation-no-relation').addClass('hide');
                    box.find('.ezobject-relation-add-button').first().addClass('button-disabled').prop('disabled', true);
                    box.find('.simple-relation-upload-new').first().addClass('button-disabled').prop('disabled', true);

                    this.close();
                }
            },
            validationErrorText: text('validation'),
            parseJSONErrorText: text('parse-json'),
            title: text('title')
        });
    });
})();
