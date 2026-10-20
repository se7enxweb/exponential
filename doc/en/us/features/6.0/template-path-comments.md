# Template path comments: see which template wrote which markup

This page is for template developers who need to find the `.tpl` file behind a piece of HTML. With template path
comments switched on, every rendered template is wrapped in two HTML comments that name it, the same help the Symfony
and Twig debug output gives. Added on 30 July 2026 (`58cc4bd4af`); improved on 5 and 6 August (`d2f4e1830d`,
`da9ef35eff`, `8b31fd9801`, `08dfef7659`).

```html
<!-- START design/media/templates/pagelayout/footer.tpl -->
...the footer markup...
<!-- STOP design/media/templates/pagelayout/footer.tpl -->
```

## Switch it on

1. In `settings/override/site.ini.append.php`, or in the siteaccess you are debugging, set:

   ```ini
   [TemplateSettings]
   ShowTemplatePathComments=enabled
   ```

   Or toggle it in the [Exp Debug bar](exp-debug-bar.md) under the templates group (setting "Template path comments",
   `settings/debugbar.ini [Setting_template_path_comments]`), which writes the same key.

2. Clear the template cache, because compiled templates carry the check:

   ```bash
   php bin/php/ezcache.php --clear-tag=template --allow-root-user
   ```

3. Open the page source in the browser, search for the markup you care about, and read the nearest `START` above it.

It works with the template compiler on or off, and for `{include}` as well as for the page's main template.

| File | Block | Key | Default | Scope | Meaning |
|---|---|---|---|---|---|
| `settings/site.ini` | `TemplateSettings` | `ShowTemplatePathComments` | `disabled` | global or siteaccess | `enabled` wraps every rendered template in START and STOP comments. Any other value is off. |

## Read the override source

Exponential resolves a template name such as `node/view/full.tpl` to a file by the rules of `override.ini`. The
comment names the file that really ran and, when it differs from the name that was asked for, the original in
brackets:

```html
<!-- START design/media/templates/node/view/full/article.tpl (node/view/full.tpl) -->
```

Read it as "the file `article.tpl` answered the request `node/view/full.tpl`". Since 6 August (`8b31fd9801`) the
bracketed text is the `Source` of the matching `override.ini` block. A matched override that cannot be traced back
falls back to the template name.

The same placement is added to warnings of the template compiler (`08dfef7659`): a warning now says in which template
and at which line the problem sits, not only that one exists.

## Limits and cautions

- **Development only.** The comments add markup to every template's output. They break JSON, XML and RSS output and
  anything that parses the HTML strictly. Keep the key `disabled` in production and on any siteaccess that serves
  feeds.
- The comment text replaces `--` by `- -` and removes `>`, so a path can never end the comment early.
- Cached output keeps the comments it was rendered with. Clear the content and template caches when you switch the
  key.
- A request from one siteaccess no longer reads another siteaccess's override list (a later fix, see the
  [6.0.15 changelog](../../changelogs/6.0/6.0.15.md)).

## Related pages

- [Exp Debug bar](exp-debug-bar.md), [debug bar reference](../../bc/6.0/debug-bar.md)
- [Template editor: create, order and edit overrides](template-editor-overrides.md)
- [6.0.15 changelog](../../changelogs/6.0/6.0.15.md)
- History: [July 2026](../../history/2026/2026-07.md), [August 2026](../../history/2026/2026-08.md)
