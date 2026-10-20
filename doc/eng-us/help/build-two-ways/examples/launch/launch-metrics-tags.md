---
title: "Add an analytics tag at launch"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1227
summary: The theme has a Google Tag Manager include (parts/google_tag_manager_code_script.tpl and _noscript.tpl); measurement code belongs in templates, not in HTML snippet blocks.
keywords: analytics, tag manager, tracking, template, launch
type: help
---
# Add an analytics tag at launch

Measurement runs on every page from launch.

*Life cycle: Launch, example 28 of 33. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. An HTML snippet block would only cover the layouts that have it.

## With templates

1. The media page layout includes `design:parts/google_tag_manager_code_script.tpl` and `parts/google_tag_manager_code_noscript.tpl`; override them in your extension to add the site's code.

## Which to choose

Templates, so every page has it once.

## Check

- The tag appears once per page.

## Related pages

- [Your own design extension](../../your-own-design-extension.md)
- [Launch examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
