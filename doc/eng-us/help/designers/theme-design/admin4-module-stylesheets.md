---
title: admin4 module stylesheets
audience: designer, developer
category: theme-design
order: 560
summary: Where CSS goes when the shared components are not enough: the kernel's module stylesheets (setup, content, people, structure, workflow, help) loaded through AdminComponentCSSFileList, and an extension's own admin stylesheet in BackendCSSFileList, with naming and token rules.
keywords: module stylesheets, admin css, AdminComponentCSSFileList, BackendCSSFileList, modules/setup.css, a4-module classes, extension admin css, admin stylesheet, where css goes
type: help
---
# admin4 module stylesheets

This page is for designers and developers who need CSS for an admin page that the [admin4 components](admin4-components.md)
cannot express. It answers: which file it goes in, how that file is loaded, and how to name the classes.

## Contents

1. [Kernel pages: the module stylesheets](#kernel-pages-the-module-stylesheets)
2. [Extension pages: your own stylesheet](#extension-pages-your-own-stylesheet)
3. [Rules for the CSS itself](#rules-for-the-css-itself)
4. [After a change](#after-a-change)

## Kernel pages: the module stylesheets

The kernel's admin pages share one bundle. `settings/design.ini [StylesheetSettings] AdminComponentCSSFileList[]`
lists:

```ini
AdminComponentCSSFileList[]=components.css
AdminComponentCSSFileList[]=modules/setup.css
AdminComponentCSSFileList[]=modules/content.css
AdminComponentCSSFileList[]=modules/people.css
AdminComponentCSSFileList[]=modules/structure.css
AdminComponentCSSFileList[]=modules/workflow.css
AdminComponentCSSFileList[]=modules/help.css
```

| File | For the modules |
|---|---|
| `modules/setup.css` | setup |
| `modules/content.css` | content |
| `modules/people.css` | role, section, state, user, notification, collaboration |
| `modules/structure.css` | class, pdf, rss, package |
| `modules/workflow.css` | workflow, trigger, audit, shop, settings, infocollector, search, url, visual, contentfinder |
| `modules/help.css` | the help pages |

Each exists in `design/admin4/stylesheets/modules/` and `design/admin/stylesheets/modules/`. admin4's
`page_head_style.tpl` loads the list after the design's own stylesheets and before `BackendCSSFileList`.

## Extension pages: your own stylesheet

An extension does **not** add to `AdminComponentCSSFileList`. It keeps its stylesheet in its own admin design folder
and adds it to `BackendCSSFileList[]`:

```ini
# extension/myext/settings/design.ini.append.php
<?php /* #?ini charset="utf-8"?
[ExtensionSettings]
DesignExtensions[]=myext

[StylesheetSettings]
BackendCSSFileList[]=myext-admin.css
*/ ?>
```

```
extension/myext/design/admin4/stylesheets/myext-admin.css
```

It loads after the components, so the extension has the last word on its own markup. See
[Tutorial: add an admin module stylesheet](tutorial-add-an-admin-module-stylesheet.md).

## Rules for the CSS itself

- Only what the components cannot do.
- Class names `.a4-<module>-*` for the kernel's module files; an extension uses one prefix of its own.
- Only tokens for colors, radius, spacing, fonts and z-index; no hard-coded values (the test checks the module files).
- A dark value for every color token you add; normally you add none.
- Logical properties (`margin-inline-start`), so right-to-left works.

## After a change

Deploy with the packer step (`./console exp:velocity deploy --packer`), which clears the template-block cache that holds
the packed link, and hard-reload. Check the page light and dark, at 1440 px and at 960 px with scale 2.

## Related pages

- [admin4 components](admin4-components.md)
- [The admin styling rules test](admin-styling-rules-test.md)
- [Loading stylesheets](loading-stylesheets.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
