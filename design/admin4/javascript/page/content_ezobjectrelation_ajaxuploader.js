/*
 * The upload dialog of the object relation field (content/datatype/edit/ezobjectrelation_ajaxuploader.tpl):
 * Exponential UI's expAjaxUploader on the .simple-relation-upload-new buttons; the object just created goes into the
 * relation. The texts are data-text-* attributes of the button.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/eng-us/features/6.0/content-security-policy.md).
 */
(function () {
    if (!window.Exp || !window.Exp.ready) { return; }
    Exp.ready(function ($) {
        if (!$.fn.expAjaxUploader) { return; }
        // the texts of the dialog: data-text-* of the first button (each button carries the same)
        var button = document.querySelector('.simple-relation-upload-new');
        var text = function (name) { return (button && button.getAttribute('data-text-' + name)) || ''; };
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
                    var box = document.getElementById('ezobjectrelation_browse_' + this.conf.target.ObjectRelationsAttributeId),
                        table = box && box.querySelector('table.list'),
                        tbody = table && table.querySelector('tbody'),
                        tr = tbody && tbody.lastElementChild,
                        tds = tr ? tr.children : [],
                        result = this.lastMetaData,
                        first = function (selector) { return box && box.querySelector(selector); },
                        input, button;
                    // as jQuery's text(): undefined sets nothing
                    var setText = function (td, value) { if (td && value !== undefined) { td.textContent = value; } };

                    if (table) {
                        table.classList.remove('hide');
                    }
                    setText(tds[0], result.object_info.name);
                    setText(tds[1], result.object_info.class_name);
                    setText(tds[2], result.object_info.section_name);
                    setText(tds[3], result.object_info.published);

                    input = first('input[name*=_data_object_relation_id_]');
                    if (input) {
                        input.value = result.object_info.id == null ? '' : result.object_info.id;
                    }
                    button = first('.ezobject-relation-remove-button');
                    if (button) {
                        button.classList.remove('button-disabled');
                        button.classList.add('button');
                        button.disabled = false;
                    }
                    if (box) {
                        Array.prototype.forEach.call(box.querySelectorAll('.ezobject-relation-no-relation'), function (el) { el.classList.add('hide'); });
                    }
                    button = first('.ezobject-relation-add-button');
                    if (button) {
                        button.classList.add('button-disabled');
                        button.disabled = true;
                    }
                    button = first('.simple-relation-upload-new');
                    if (button) {
                        button.classList.add('button-disabled');
                        button.disabled = true;
                    }

                    this.close();
                }
            },
            validationErrorText: text('validation'),
            parseJSONErrorText: text('parse-json'),
            title: text('title')
        });
    });
})();
