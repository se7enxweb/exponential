---
title: "Designing forms"
audience: theme-designer
category: theme-design
order: 22
summary: How theme designers design the public forms of an Exponential site - feedback and contact forms built from content classes, field anatomy, required fields, errors, the confirmation page, captchas, and what the template must keep - with links to the accessible-forms and template pages.
keywords: theme designer, forms, contact form, feedback form, information collection, fields, labels, errors, validation, required, captcha, confirmation, form design
type: help
---
# Designing forms

This page is for theme designers who design the forms visitors fill in: contact and feedback forms, newsletter
sign-up, sign-in and registration. In Exponential, a public feedback form is usually a **content class** whose fields
collect information, so editors can create a form without a developer, and the fields come out of the datatype
templates. That shapes what a form design can and cannot do. The accessibility rules are in
[Accessible forms](../designers/theme-design/accessible-forms.md); the template mechanics in
[Forms in templates](../designers/templates/forms-in-templates.md).

## Contents

1. [Where forms come from](#where-forms-come-from)
2. [Anatomy of a field](#anatomy-of-a-field)
3. [Layout](#layout)
4. [Required and optional](#required-and-optional)
5. [Errors](#errors)
6. [After sending](#after-sending)
7. [Spam protection](#spam-protection)
8. [What the template must keep](#what-the-template-must-keep)
9. [A form checklist](#a-form-checklist)

## Where forms come from

| Form | Source | Design freedom |
|---|---|---|
| Feedback or contact form | a content class with information-collecting fields; editors create the form object | style every field type once; the order and labels come from the class |
| Sign-in, registration, password | the user module's templates | markup and styles; field names fixed ([Designing login and user pages](designing-login-and-user-pages.md)) |
| Search | the search module | see [Designing search results](designing-search-results.md#the-form) |
| Newsletter sign-up | the newsletter extension, where installed | see [Designing newsletter emails](designing-newsletter-emails.md) |

Because editors can build feedback forms, design **each field type** (text line, text area, selection, checkbox,
email) as a component, not one specific form.

## Anatomy of a field

Every field has, from top to bottom:

1. a **label**, always visible (a placeholder is not a label);
2. an optional **hint** (format, length), linked to the field;
3. the **control**;
4. room for an **error message**, shown only when needed, without pushing the layout around more than necessary.

Draw the field in its states: default, focus, filled, error, disabled ([Designing interactive states](designing-interactive-states.md)).
The control's border needs 3:1 contrast against the background, in light and dark
([Color contrast](../designers/theme-design/color-contrast.md#the-targets)).

## Layout

- One column. Labels above fields. Multi-column forms are misread and break at zoom.
- Field width hints at the expected length (a postal code field is short), but full width on small screens.
- Group related fields with a visible group heading (a `fieldset` with `legend` in markup).
- One primary button, with a verb that says what happens ("Send message"), at the end of the form.

## Required and optional

Mark whichever is rarer, and say what the mark means at the top of the form. Required is a property of the class
field, set by whoever edits the class, so the design must handle both for every field type.

## Errors

When a form is sent with errors, the page shows the form again:

- a **summary** at the top, with a heading and a link to each field in error;
- a **message** at each field, in words that say how to fix it;
- the visitor's input kept in the fields;
- error color from the status tokens, plus an icon and the text, never color alone.

The kernel's validation messages are printed by the templates; their wording can be changed through translation
files, not by editing kernel files ([Translations in a theme](../designers/extensions/translations-in-a-theme.md)).

## After sending

Design the confirmation page: a heading that says it worked, what happens next, and a way to continue. On feedback
forms the result page is a separate template (`content/collectedinfo/` in the kernel design), which your design can
override. The mail sent to the site owner is a template too; plain and short is best.

## Spam protection

Captcha extensions exist for public forms (hcaptcha and recaptcha are among the extensions of the demonstration
installation, each with its own field type and form templates). They load third-party scripts, which affects privacy,
performance and the Content Security Policy; decide with the client whether one is needed and design the widget's
place, not its inside ([External resources and CDNs](../designers/theme-design/external-resources-and-cdns.md)).

## What the template must keep

- The field names and hidden inputs the module reads, and the form token the ezformtoken extension adds to forms
  that post (a page that drops it gets refused).
- The ids the datatype templates generate, so labels stay connected
  ([Accessible forms](../designers/theme-design/accessible-forms.md#field-ids-from-the-datatype-templates)).
- No inline styles or `on*=` handlers; field states are styled from classes and ARIA attributes.

## A form checklist

- [ ] Every field type drawn in all states, light and dark.
- [ ] Labels visible; hints connected; one column.
- [ ] Error summary and per-field messages drawn.
- [ ] Confirmation page drawn.
- [ ] Works at 200% zoom and with the keyboard alone.
- [ ] Autocomplete attributes for personal data fields agreed with the developer.

## Related pages

- [Accessible forms](../designers/theme-design/accessible-forms.md)
- [Forms in templates](../designers/templates/forms-in-templates.md)
- [Datatype edit templates](../designers/templates/datatype-edit-templates.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
