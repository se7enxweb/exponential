---
title: Accessible forms
audience: designer
category: theme-design
order: 480
summary: Forms a design draws (search, sign-in, feedback and information collection, user registration, edit pages): labels tied to fields, the ids the datatype edit templates give fields, required marks, error summaries and messages, and autocomplete.
keywords: forms, accessible forms, label, for, id, ezcoa, required fields, error message, validation, aria-describedby, aria-invalid, autocomplete, search form, feedback form, information collection
type: help
---
# Accessible forms

This page is for designers who style or override forms: the search box, sign-in and registration pages, feedback forms
(information collection) and content edit pages. It answers: how fields get their labels, how errors are shown, and
what the kernel's templates already provide.

## Contents

1. [Labels](#labels)
2. [Field ids from the datatype templates](#field-ids-from-the-datatype-templates)
3. [Required fields](#required-fields)
4. [Errors](#errors)
5. [Autocomplete and input types](#autocomplete-and-input-types)
6. [The search form](#the-search-form)

## Labels

Every field needs a visible `<label>` tied to it with `for`/`id`, or wrapping it. Placeholder text is not a label.
Groups of radio buttons or checkboxes go in a `<fieldset>` with a `<legend>`.

```tpl
<label for="search-text">{'Search'|i18n( 'design/mytheme' )}</label>
<input id="search-text" type="search" name="SearchText" autocomplete="off">
```

## Field ids from the datatype templates

Fields of content classes are drawn by datatype templates in `design/standard/templates/content/datatype/`:

- **Edit pages** use `content/datatype/edit/`. The text line template gives its input
  `id="ezcoa-<class attribute id>_<attribute identifier>"` (`edit/ezstring.tpl`), so a label can point to it.
- **Feedback forms** (information collection) use `content/datatype/collect/`. The shipped `collect/ezstring.tpl`
  prints its input **without an id**, so a `<label for>` has nothing to point to.

For an accessible feedback form, override the collect templates of the datatypes your form uses in your design
(`templates/content/datatype/collect/ezstring.tpl` and so on) and give each input an id, for example the same
`ezcoa-` scheme; then print the labels in your form template:

```tpl
{def $field = $node.data_map.email}
<label for="ezcoa-{$field.contentclassattribute_id}_{$field.contentclass_attribute_identifier}">
    {$field.contentclass_attribute.name|wash}
</label>
{attribute_view_gui attribute=$field}
{undef $field}
```

When you override a datatype template, keep the id scheme so labels in other templates still match.

## Required fields

- Mark required fields visibly (an asterisk with a legend explaining it, or the word "required") and with the `required`
  attribute or `aria-required="true"`.
- admin4 shows a mark next to the label with the word in `.a4-sr-only` for screen readers and one hint above the form
  (`.a4-required-mark`, `.a4-required-hint` in `components.css`).

## Errors

The kernel validates on submit and gives the template a validation result: `content/edit_validation.tpl` and
`content/collectedinfo_validation.tpl` print the list of problems. In your design:

- Show a summary at the top of the form, with a heading, each item linking to its field; move focus to the summary
  (admin4's `.a4-error-summary` is focused on load with `role="alert"`).
- Show the message next to the field too, linked with `aria-describedby`, and mark the field `aria-invalid="true"`.
- Keep what the visitor typed.
- Do not rely on color alone: add an icon or the word "Error".

## Autocomplete and input types

Use the right `type` (`email`, `tel`, `url`, `number`, `search`) and `autocomplete` tokens (`name`, `email`,
`username`, `current-password`, `new-password`), so phones show the right keyboard and password managers work. The
sign-in and password templates of the user module are where `current-password` and `new-password` matter most.

## The search form

A header search box needs a label (visible or `.sr-only`), a submit button with a name, and the `role="search"`
landmark on the `<form>`:

```tpl
<form role="search" action={'/content/search'|ezurl} method="get">
    <label class="sr-only" for="site-search">{'Search'|i18n( 'design/mytheme' )}</label>
    <input id="site-search" type="search" name="SearchText">
    <button type="submit">{'Search'|i18n( 'design/mytheme' )}</button>
</form>
```

## Related pages

- [Forms in templates](../templates/forms-in-templates.md)
- [Keyboard and focus](keyboard-and-focus.md)
- [Datatype edit templates](../templates/datatype-edit-templates.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
