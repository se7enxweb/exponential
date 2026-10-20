---
title: Template path comments
audience: designer, developer
category: templates
order: 750
summary: Seeing in the page source which template wrote which markup: switching on ShowTemplatePathComments, reading the START and STOP comments and the override source in brackets, the Exp debug bar toggle, and why it must stay off in production.
keywords: template path comments, ShowTemplatePathComments, START STOP comments, which template, override source, debug templates, page source, debug bar
type: help
---
# Template path comments

This page is for designers who look at a page and need the template behind a piece of it. With template path comments
on, every rendered template is wrapped in two HTML comments naming its file. The feature page is
[Template path comments](../../../features/6.0/template-path-comments.md).

## Contents

1. [Switch it on](#switch-it-on)
2. [Read it](#read-it)
3. [Cautions](#cautions)

## Switch it on

On a test siteaccess:

```ini
# settings/siteaccess/<test siteaccess>/site.ini.append.php
[TemplateSettings]
ShowTemplatePathComments=enabled
```

then clear the template cache (`php bin/php/ezcache.php --clear-tag=template`), because compiled templates carry the
check. The [Exp debug bar](../../../features/6.0/exp-debug-bar.md) can toggle the same setting.

## Read it

```html
<!-- START design/media/templates/pagelayout/footer.tpl -->
...the footer markup...
<!-- STOP design/media/templates/pagelayout/footer.tpl -->
```

When an override answered, the requested template is named in brackets:

```html
<!-- START design/media/templates/node/view/full/article.tpl (node/view/full.tpl) -->
```

"the file `article.tpl` answered the request `node/view/full.tpl`". In the browser's element inspector, the comments
appear around the elements, so you can climb from any element to its template.

## Cautions

- Development only: the comments add markup to every template, break JSON, XML and RSS output, and reveal file paths.
  Keep the setting `disabled` on live siteaccesses (its comment in `settings/site.ini` says the same).
- Cached output keeps the comments it was rendered with: clear content and template caches after switching.

## Related pages

- [Template debugging](template-debugging.md)
- [Debugging a design](../theme-design/debugging-a-design.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
