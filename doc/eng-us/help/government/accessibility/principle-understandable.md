---
title: The understandable principle
audience: government, editor, designer, developer
category: public-sector
order: 320
summary: WCAG principle 3, understandable, for a public body running Exponential - readable language, predictable pages and input assistance, with plain-language practice for editors and form and error rules for developers.
keywords: understandable, WCAG principle 3, readable, language of page, plain language, predictable, consistent navigation, input assistance, error identification, labels, error suggestion, redundant entry, accessible authentication, 3.1, 3.2, 3.3
type: help
---
# The understandable principle

This page is for everyone on a public website team. The third WCAG principle says that information and the operation
of the interface must be understandable (<https://www.w3.org/TR/WCAG22/>). For public bodies it overlaps with plain
language duties: people must understand what the government tells them and asks of them.

## Contents

1. [3.1 Readable](#31-readable)
2. [3.2 Predictable](#32-predictable)
3. [3.3 Input assistance](#33-input-assistance)
4. [Related pages](#related-pages)

## 3.1 Readable

| Role | Does |
|---|---|
| Developers | `<html lang>` per siteaccess; `lang` on passages from another language |
| Editors | plain language: short sentences, common words, active voice, terms explained; abbreviations written out at first use |
| Administrators | one siteaccess and one translation per language ([Content languages](../../../guides/content-languages.md)) |

Admin4 writes `lang` from the siteaccess locale. For plain language, the US federal guidance at
<https://www.plainlanguage.gov/> is a free reference. See "Language of the page and its parts".

## 3.2 Predictable

| Role | Does |
|---|---|
| Designers | the same navigation in the same place on every page; the same function with the same name |
| Developers | no change of context on focus or on selection alone (a select that submits itself) |
| Editors | link to documents and new windows with a warning in the text |

Shared headers and footers keep navigation consistent ([Shared header and footer](../../creators/website-creators/shared-header-and-footer.md)).

## 3.3 Input assistance

| Role | Does |
|---|---|
| Developers | errors identified in text, linked from a summary that takes focus; suggestions for fixing; values kept |
| Designers | visible labels and instructions before fields |
| Editors | clear field descriptions in form classes; a confirmation page that says what happens next |
| Administrators | sign-in that works with password managers; consider what two-step sign-in asks of users (3.3.8) |

Admin4: error summary and field messages (`AdminFormErrorsTest.php`). See "Accessible forms".

## Related pages

- [Accessible forms](accessible-forms.md)
- [Accessible content for editors](accessible-content-for-editors.md)
- [Mapping WCAG 2.2 AA to Exponential](wcag-2-2-mapping.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
