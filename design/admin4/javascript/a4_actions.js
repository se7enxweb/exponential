/*
 * admin4: the small actions of the pages, declared in data-a4-* attributes instead of on...= attributes, so a page
 * works under a Content Security Policy without 'unsafe-inline' (doc/en/us/features/6.0/content-security-policy.md). One
 * listener per event on the document handles every element, also those a script adds later.
 *
 *   data-a4-confirm="Question?"          on a button, link or input: the click goes on only when confirmed; on a
 *                                        form: it is submitted only when confirmed
 *   data-a4-discard-confirm="Question?"  the Discard button of an edit form: confirmed here, so the form's
 *                                        DiscardConfirm field is set to 0 (the page that asks again is skipped)
 *   data-a4-toggle-checkboxes="Name[]"   a click inverts every enabled checkbox of that name in the element's form
 *                                        (data-a4-form="formname" names another form); ezjs_toggleCheckboxes()
 *   data-a4-check-all="#selector"        a click checks every checkbox matching the selector (unchecks with
 *                                        data-a4-check-value="0")
 *   data-a4-dismiss                      a click hides the element's parent (a notice's close button);
 *                                        data-a4-dismiss="#id" hides that element instead
 *   data-a4-submit-on-change             a change submits the element's form
 *   data-a4-navigate-on-change           a change goes to the element's value (a select of addresses)
 *   data-a4-reset-field="id"             a change sets the field with that id to 0
 *   data-a4-focus                        the page focuses this field when it is shown (and selects its text)
 */
(function () {
    'use strict';

    function closest(el, selector) {
        while (el && el.nodeType !== 1) el = el.parentNode;
        return el && el.closest ? el.closest(selector) : null;
    }

    function toggleCheckboxes(form, name) {
        if (!form || !form.elements) return;
        for (var i = 0; i < form.elements.length; i++) {
            var box = form.elements[i];
            if (box.type === 'checkbox' && box.name === name && !box.disabled) box.checked = !box.checked;
        }
    }

    function formOf(el) {
        var name = el.getAttribute('data-a4-form');
        if (name) return document.forms[name] || document.getElementById(name);
        return el.form || closest(el, 'form');
    }

    document.addEventListener('click', function (e) {
        var el = closest(e.target, '[data-a4-confirm], [data-a4-discard-confirm], [data-a4-toggle-checkboxes], [data-a4-check-all], [data-a4-dismiss]');
        if (!el || el.tagName === 'FORM') return;

        if (el.hasAttribute('data-a4-discard-confirm')) {
            var form = formOf(el);
            var discard = form ? form.querySelector('[name="DiscardConfirm"]') : null;
            if (discard) discard.value = '0';
            if (!window.confirm(el.getAttribute('data-a4-discard-confirm'))) e.preventDefault();
            return;
        }
        if (el.hasAttribute('data-a4-confirm')) {
            if (!window.confirm(el.getAttribute('data-a4-confirm'))) {
                e.preventDefault();
                e.stopImmediatePropagation();
            }
            return;
        }
        if (el.hasAttribute('data-a4-toggle-checkboxes')) {
            e.preventDefault();
            toggleCheckboxes(formOf(el), el.getAttribute('data-a4-toggle-checkboxes'));
            return;
        }
        if (el.hasAttribute('data-a4-check-all')) {
            var boxes = document.querySelectorAll(el.getAttribute('data-a4-check-all')), on = el.getAttribute('data-a4-check-value') !== '0';
            for (var b = 0; b < boxes.length; b++) boxes[b].checked = on;
            return;
        }
        if (el.hasAttribute('data-a4-dismiss')) {
            var target = el.getAttribute('data-a4-dismiss') ? document.querySelector(el.getAttribute('data-a4-dismiss')) : el.parentNode;
            if (target) target.hidden = true;
            return;
        }
    });

    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (form && form.hasAttribute && form.hasAttribute('data-a4-confirm') && !window.confirm(form.getAttribute('data-a4-confirm'))) {
            e.preventDefault();
        }
    });

    document.addEventListener('change', function (e) {
        var el = e.target;
        if (!el || !el.hasAttribute) return;
        if (el.hasAttribute('data-a4-submit-on-change')) {
            var form = formOf(el);
            if (form) form.submit();
        } else if (el.hasAttribute('data-a4-navigate-on-change')) {
            if (el.value && window.a4SafeURL && window.a4SafeURL(el.value, 'a4_actions')) window.location.href = el.value;
        }
        if (el.hasAttribute('data-a4-reset-field')) {
            var field = document.getElementById(el.getAttribute('data-a4-reset-field'));
            if (field) field.value = 0;
        }
    });

    function focusField() {
        var field = document.querySelector('[data-a4-focus]');
        if (!field) return;
        field.focus();
        if (field.select && /^(text|search|url|email|)$/.test(field.type || '')) field.select();
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', focusField);
    else focusField();

    window.a4Actions = { toggleCheckboxes: toggleCheckboxes };
})();
