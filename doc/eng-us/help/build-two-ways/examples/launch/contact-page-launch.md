---
title: "The contact page"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1217
summary: Contact (node 185, class Contact form) is a form page; give it a layout with a map and text around the form.
keywords: contact page, contact form, map, layout, launch
type: help
---
# The contact page

A contact page with address, map and form.

*Life cycle: Launch, example 18 of 33. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. **Layouts > Layout mappings > New mapping**; **Link layout**: *a contact layout with a Map block and a Full view block*; **Priority**: a priority above the general mappings.
2. Target: **Location** Contact (node 185), **Add target**.
3. **Add mapping**.

## With templates

1. The form is the full view of `ng_contact_form`; recipient and success text are fields of the page (`recipient`, `success_text`).

## Which to choose

Layout for what surrounds the form, content for the form's settings.

## Check

- Sending the form shows the success text (on a test copy, with mail not sent).

## Related pages

- [Which layout does a page get?](../../layout-mappings.md)
- [Launch examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
