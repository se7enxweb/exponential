# Template path comments: see which template wrote which markup

When a page looks wrong, the first question is: which `.tpl` file produced this
piece of HTML? With template path comments switched on, every rendered template
is wrapped in two HTML comments that name it:

```html
<!-- START design/media/templates/pagelayout/footer.tpl -->
...the footer markup...
<!-- STOP design/media/templates/pagelayout/footer.tpl -->
```

Open the page source in the browser, search for the markup you care about and
read the nearest `START` above it. It is the same help the Symfony and Twig
debug output gives. Added on 30 July 2026 (`58cc4bd4af`); improved on 5 and 6
August (`d2f4e1830d`, `da9ef35eff`, `8b31fd9801`, `08dfef7659`).

## Switch it on

File `settings/site.ini` (put the change in `settings/override/site.ini.append.php`
or in the siteaccess you are debugging), block `[TemplateSettings]`:

| Key | Default | Scope | Meaning |
|---|---|---|---|
| `ShowTemplatePathComments` | `disabled` | global or siteaccess | `enabled` wraps every rendered template in START and STOP comments. Any other value is off. |

```ini
[TemplateSettings]
ShowTemplatePathComments=enabled
```

Or toggle it in the [Exp Debug bar](exp-debug-bar.md) under the templates group
(setting "Template path comments"), which writes the same key.

It works with the template compiler on or off, and for `{include}` as well as
for the page's main template. Clear the template cache after changing it
(`php bin/php/ezcache.php --clear-tag=template --allow-root-user`) because
compiled templates carry the check.

## The override source

Exponential resolves a template name such as `node/view/full.tpl` to a file by
the rules of `override.ini`. The comment names the file that really ran, and,
when it differs from the name that was asked for, the original in brackets:

```html
<!-- START design/media/templates/node/view/full/article.tpl (node/view/full.tpl) -->
```

Read it as "the file `article.tpl` answered the request `node/view/full.tpl`".
Since 6 August (`8b31fd9801`) the bracketed text is the `Source` of the matching
`override.ini` block. A matched override that cannot be traced back falls back to
the template name. The same placement is added to warnings of the template
compiler (`08dfef7659`): a warning now says in which template and at which line
the problem sits, not only that one exists.

## Limits and cautions

- **Development only.** The comments add markup to every template's output. They
  break JSON, XML and RSS output and anything that parses the HTML strictly. Keep
  the key `disabled` in production and on any siteaccess that serves feeds.
- The comment text replaces `--` by `- -` and removes `>` so a path can never
  end the comment early.
- Cached output keeps the comments it was rendered with; clear the content and
  template caches when you switch the key.
- A request from one siteaccess no longer reads another siteaccess's override list
  (a later fix, see the [6.0.15 changelog](../../changelogs/6.0/6.0.15.md)).

## Related

- [Debug output improvements](debug-output-improvements.md)
- [Template editor](template-editor-overrides.md): create and order the overrides these comments reveal.
- Month pages: [July 2026](../../history/2026/2026-07.md), [August 2026](../../history/2026/2026-08.md).
