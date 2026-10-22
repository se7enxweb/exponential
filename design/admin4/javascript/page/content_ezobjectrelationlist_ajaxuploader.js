/*
 * The upload dialog of the object relation list field (content/datatype/edit/ezobjectrelationlist_ajaxuploader.tpl):
 * Exponential UI's expAjaxUploader on the .relation-upload-new buttons; the object just created is added to the
 * relation list. The texts are data-text-* attributes of the button.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/eng-us/features/6.0/content-security-policy.md).
 */
(function () {
    if (!window.Exp || !window.Exp.ready) { return; }
    Exp.ready(function ($) {
        if (!$.fn.expAjaxUploader) { return; }
        // the texts of the dialog: data-text-* of the first button (each button carries the same)
        var button = document.querySelector('.relation-upload-new');
        var text = function (name) { return (button && button.getAttribute('data-text-' + name)) || ''; };
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
                    var box = document.getElementById('ezobjectrelationlist_browse_' + this.conf.target.ObjectRelationsAttributeId),
                        table = box && box.querySelector('table.list'),
                        tbody = table && table.querySelector('tbody'),
                        last = tbody && tbody.lastElementChild,
                        tr, tds, priority, button,
                        result = this.lastMetaData;
                    // as jQuery's text(): undefined sets nothing
                    var setText = function (td, value) { if (td && value !== undefined) { td.textContent = value; } };

                    if (table && !table.classList.contains('hide')) {
                        // table is visible, clone the last line
                        if (last) {
                            tr = last.cloneNode(true);
                            tbody.appendChild(tr);
                            if ( last.classList.contains('bgdark') ) {
                                tr.classList.remove('bgdark');
                                tr.classList.add('bglight');
                            } else {
                                tr.classList.remove('bglight');
                                tr.classList.add('bgdark');
                            }
                        }
                    } else if (table) {
                        // table is hidden, no related object yet
                        // the only line in the table is the "template line"
                        tr = last;
                        table.classList.remove('hide');
                    }
                    tds = tr ? tr.children : [];
                    if (tds[0]) {
                        Array.prototype.forEach.call(tds[0].querySelectorAll('input'), function (input) { input.value = result.object_info.id == null ? '' : result.object_info.id; });
                    }
                    setText(tds[1], result.object_info.name);
                    setText(tds[2], result.object_info.class_name);
                    setText(tds[3], result.object_info.section_name);
                    setText(tds[4], result.object_info.published);
                    priority = tds[5] && tds[5].querySelector('input');
                    if (priority) {
                        priority.value = parseInt(priority.value, 10) + 1;
                    }

                    button = box && box.querySelector('.ezobject-relation-remove-button');
                    if (button) {
                        button.classList.remove('button-disabled');
                        button.classList.add('button');
                        button.disabled = false;
                    }
                    if (box) {
                        Array.prototype.forEach.call(box.querySelectorAll('.ezobject-relation-no-relation'), function (el) { el.classList.add('hide'); });
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
