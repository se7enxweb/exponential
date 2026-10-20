---
title: Accessible forms
audience: government, website-creator, designer
category: public-sector
order: 30
summary: How to make public forms built with Exponential's information collector accessible - labels tied to fields, instructions, required marks, error messages that point to fields, keyboard use, CAPTCHA alternatives - with the relevant WCAG 2.2 criteria and how to test.
keywords: accessible forms, WCAG, labels, instructions, required fields, error identification, error suggestion, aria-describedby, aria-invalid, keyboard, CAPTCHA, testing
type: help
---
# Accessible forms

This page is for website creators and designers who build the templates of public forms. A form that cannot be
completed with a keyboard or a screen reader excludes people from a public service. Exponential's administration forms
were built and tested against WCAG 2.2 AA criteria (labels, error summaries, `aria-invalid`), with status **Partly
complies**; the public form templates are your design's, and are not audited
([Accessibility standards](../../../../standards/accessibility.md)). The patterns of the administration are a good
model.

## Contents

1. [The criteria that matter](#the-criteria-that-matter)
2. [Labels and instructions](#labels-and-instructions)
3. [Errors](#errors)
4. [Keyboard and focus](#keyboard-and-focus)
5. [CAPTCHA and accessibility](#captcha-and-accessibility)
6. [Testing a form](#testing-a-form)

## The criteria that matter

From [WCAG 2.2](https://www.w3.org/TR/WCAG22/):

| Criterion | What it asks |
|---|---|
| 1.3.1 Info and relationships | labels programmatically tied to fields; groups of radio buttons in a fieldset with a legend |
| 3.3.1 Error identification | errors described in text |
| 3.3.2 Labels or instructions | a visible label and needed instructions |
| 3.3.3 Error suggestion | how to fix the error |
| 3.3.7 Redundant entry (new in 2.2) | do not ask again for what was already given in the same process |
| 3.3.8 Accessible authentication (minimum) (new in 2.2) | no cognitive test as the only way to sign in |
| 2.1.1 Keyboard | everything works without a mouse |
| 4.1.2 Name, role, value | every control has an accessible name |

## Labels and instructions

The collector attribute's view template renders the input. In your form template, give every input a visible label
tied to it with `for` and `id`; the shipped ezwebin template shows the pattern
([Forms with the information collector](../../../creators/website-creators/forms-with-the-information-collector.md#the-form-template)).

| Practice | Why |
|---|---|
| The label is the attribute's name, written for visitors ("Your e-mail address") | the class attribute name is shown |
| Instructions before the field, tied with `aria-describedby` | screen readers read them with the field |
| Required fields marked in text ("required"), not only with color or an asterisk | 1.4.1 Use of color |
| Formats explained ("Date as DD/MM/YYYY") | 3.3.2 |
| `autocomplete` attributes on name and e-mail | helps people with motor and memory difficulties |

## Errors

When validation fails, Exponential shows the form again with the validation result (`collectedinfo_validation.tpl`
in the shipped template). Make the message:

- appear at the top as a summary that links to each field, and receive focus;
- appear next to each field, tied with `aria-describedby`, with `aria-invalid="true"` on the field;
- say how to fix it ("Enter an e-mail address like name@example.com").

The admin4 design does exactly this for its own forms (`tests/tests/design/AdminFormErrorsTest.php` holds it), so its
templates are a working reference.

## Keyboard and focus

- The form can be filled and sent with Tab, Shift+Tab, Space and Enter.
- Focus is always visible.
- No time limit, or a way to extend it; a form token that expires shows a page that says nothing was saved and how to
  try again ([CSRF form tokens](csrf-form-tokens.md)).

## CAPTCHA and accessibility

Visual puzzles exclude people. If you use a CAPTCHA ([Spam protection and third parties](spam-protection-and-third-parties.md)),
choose a mode with an accessible alternative, and offer another channel (phone, e-mail). Often the form token plus a
simple honeypot field in your template is enough for low-volume public forms.

## Testing a form

- [ ] Fill and send it with the keyboard only.
- [ ] Fill it with a screen reader: every field announces its label, instructions and required state.
- [ ] Send it empty: the error summary is announced and links to the fields.
- [ ] Zoom to 200% and use a 320 px wide window: nothing is cut off.
- [ ] Run an automated checker as a first pass ([Testing accessibility](../../../creators/website-creators/testing-accessibility.md)).

## Related pages

- [Building forms](README.md)
- [Information collector basics](information-collector-basics.md)
- [Accessibility for site builders](../../../creators/website-creators/accessibility-for-site-builders.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
