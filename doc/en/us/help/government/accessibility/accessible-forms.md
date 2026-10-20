---
title: Accessible forms
audience: government, designer, developer, editor
category: public-sector
order: 210
summary: For designers and developers who build public forms with Exponential's information collector, registration and newsletter subscription - labels, required fields, error messages, CAPTCHAs, timeouts and confirmation pages, with what the shipped templates do and what you must add.
keywords: forms, accessible forms, information collector, contact form, labels, required fields, error messages, validation, CAPTCHA, reCAPTCHA, hCaptcha, form token, timeout, WCAG 3.3.1, 3.3.2, 3.3.8, public sector
type: help
---
# Accessible forms

This page is for the designers and developers who build the forms a public body offers (contact, feedback, complaints,
registration, newsletter subscription) and the editors who set them up. Forms are where barriers hurt most: a person
who cannot send a form cannot complete the service. WCAG 2.2 covers forms under 1.3.1, 3.3.1, 3.3.2, 3.3.7, 3.3.8 and
4.1.2 (<https://www.w3.org/TR/WCAG22/>).

## Contents

1. [How forms are built in Exponential](#how-forms-are-built-in-exponential)
2. [Labels and instructions](#labels-and-instructions)
3. [Errors](#errors)
4. [CAPTCHAs](#captchas)
5. [Time limits and expired forms](#time-limits-and-expired-forms)
6. [After sending](#after-sending)
7. [Related pages](#related-pages)

## How forms are built in Exponential

Public forms are usually content objects whose class has **Information collector** attributes; the full view
template draws the form, the kernel validates each attribute and stores a collection
([Forms with the information collector](../../creators/website-creators/forms-with-the-information-collector.md)).
The form's markup is therefore in **your** templates (the full view of the form class and the datatype templates),
which the project has not audited for the public designs.

## Labels and instructions

- Every field has a visible `<label for>` that names it; the ezwebin example template shows the pattern of labeling a
  collector attribute with its class attribute name.
- Required fields are marked in text ("required"), not only with an asterisk or a color.
- Instructions (format of a date, what a reference number looks like) are given before the field and linked with
  `aria-describedby`.
- Related fields (an address) are grouped with `fieldset` and `legend`.
- Use the right input types and `autocomplete` values for name, e-mail and address so browsers can fill them in.

## Errors

The kernel validates and the template `content/collectedinfo_validation.tpl` lists the problems. Make sure your
version of it:

- appears at the top of the form, takes the focus, and links to each field;
- names the field and says how to fix the problem;
- keeps what the visitor typed;
- marks the fields with `aria-invalid="true"` and points `aria-describedby` at the message.

The admin4 administration does exactly this for its own forms (`tests/tests/design/AdminFormErrorsTest.php`); use it
as a model.

## CAPTCHAs

The optional extensions `recaptcha` (Google reCAPTCHA) and `hcaptcha` (hCaptcha) add a CAPTCHA attribute to a class
([Spam protection for forms](../../creators/website-creators/spam-protection-for-forms.md)). A CAPTCHA is a barrier
for many people and a third-party service for privacy (see the privacy section of this help). Prefer the form token
(`ezformtoken`, active by default) and server-side checks; use a CAPTCHA only where abuse is real, test it with the
keyboard and a screen reader, and offer another contact route next to the form.

## Time limits and expired forms

- A form token belongs to the session; a refused token shows the "form expired" page
  ([Form expired page](../../../features/6.0/form-expired-page.md)). Check that the page is clear and lets the visitor
  go back without losing work where possible.
- Sessions end after `[Session] SessionTimeout` (three days by default). If you shorten it for security, think of
  people who need long to fill in a form (WCAG 2.2.1 Timing adjustable).

## After sending

- Use a "thank you" page that says what happens next and how long it takes (`Display=redirect` with a `RedirectURL`
  in `collect.ini`, so a reload does not send again).
- If a confirmation mail is sent, write it in plain text with the reference and the contact.

## Related pages

- [Forms with the information collector](../../creators/website-creators/forms-with-the-information-collector.md)
- [Building a contact form](../../creators/website-creators/building-a-contact-form.md)
- [Accessibility for site builders: Forms](../../creators/website-creators/accessibility-for-site-builders.md#forms)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
