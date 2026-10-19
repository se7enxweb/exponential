---
title: "Plan your design extension"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1011
summary: One extension for everything site-specific: templates, stylesheets, settings, translations - named, versioned and activated in the right place.
keywords: design extension, mysite, version control, activation order, planning
type: help
---
# Plan your design extension

Site changes never go into the theme or the kernel.

*Life cycle: Plan, example 12 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [With templates](#with-templates)
2. [Which to choose](#which-to-choose)
3. [Check](#check)

## With templates

1. Name it (these pages use `mysite`), give it `settings/design.ini.append.php` with `DesignExtensions[]=mysite`, and the design folder of the site (`design/media/`).
2. Plan to place it first in `ActiveExtensions[]`: its templates then win over the theme's and its `explayouts.ini` is read after Layouts' own.
3. Keep it in its own git repository.

## Which to choose

Even a site that is built mostly in the Layouts editor needs this extension for its few templates and styles.

## Check

- `./console exp:ini get design.ini/ExtensionSettings/DesignExtensions -s site` lists it after the theme.

## Related pages

- [Your own design extension](../../your-own-design-extension.md)
- [Plan examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
