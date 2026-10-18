---
title: View results, templates and the pagelayout
audience: app-creator, developer
category: app-creation
order: 120
summary: What a view returns and how it reaches the browser - the $Result array, templates in your extension's design, the pagelayout around it, alternative pagelayouts, JSON and file answers, and translations of view texts.
keywords: Result, content, path, pagelayout, eZTemplate, template, design, JSON response, download, i18n, ezpI18n, ui_context, app creator
type: help
---
# View results, templates and the pagelayout

This page is for application creators who render pages from views. A view returns a `$Result`; the kernel puts its
`content` into the siteaccess's pagelayout. HTML belongs in templates, not in PHP.

## Contents

1. [The result array](#the-result-array)
2. [Templates of your extension](#templates-of-your-extension)
3. [The pagelayout around it](#the-pagelayout-around-it)
4. [Answers that are not pages](#answers-that-are-not-pages)
5. [Translated texts](#translated-texts)

## The result array

| Key | Meaning |
|---|---|
| `content` | the HTML of the view, usually `$tpl->fetch( 'design:<path>.tpl' )` |
| `path` | breadcrumb items: `array( 'text' => ..., 'url' => ... )`; the last one with `'url' => false` |
| `pagelayout` | another pagelayout template for this view, or `false` for none |

## Templates of your extension

Put templates in `extension/<ext>/design/standard/templates/<ext>/...` (the `standard` design is in every chain) and
register the design extension:

```ini
# extension/expdemo/settings/design.ini.append.php
<?php /* #?ini charset="utf-8"?
[ExtensionSettings]
DesignExtensions[]=expdemo
*/ ?>
```

```php
$tpl = eZTemplate::factory();
$tpl->setVariable( 'items', $items );
$Result['content'] = $tpl->fetch( 'design:expdemo/list.tpl' );
```

Sites override your templates in their own designs, which is what makes an extension reusable
([Template overrides](../website-creators/template-overrides.md)). For the administration, templates go in the admin
designs and follow the admin styling rules: no inline CSS, shared components and tokens
([admin4 styling](../../../bc/6.0/admin4-styling.md)).

## The pagelayout around it

The siteaccess's `pagelayout.tpl` receives `$module_result` with your `content` and `path`
([The pagelayout](../website-creators/the-pagelayout.md)). Pass `'pagelayout' => false` to send the view's output alone,
or name a template for a minimal layout. The admin designs pick their frame from `default_navigation_part` and
`ui_context` of the view.

## Answers that are not pages

For JSON, send the answer and end the request without the pagelayout:

```php
header( 'Content-Type: application/json; charset=utf-8' );
echo json_encode( array( 'ok' => true, 'data' => $data ) );
eZExecution::cleanExit();
```

Prefer the existing JSON layers for APIs (JSON endpoints with ezjscore,
REST API). Under Velocity, `cleanExit()` ends the request by an exception that the server handles; do
not wrap it in a `catch ( Exception $e )`, or the page is appended to your output.

## Translated texts

In templates: `{'Latest items'|i18n( 'extension/expdemo' )}`. In PHP: `ezpI18n::tr( 'extension/expdemo', 'Latest items' )`.
Translations live in `extension/<ext>/translations/<locale>/translation.ts`, registered with
`site.ini [RegionalSettings] TranslationExtensions[]=<ext>` ([Internationalization](../../../standards/internationalization.md)).

## Related pages

- [Custom modules and views](custom-modules-and-views.md)
- [Template functions, includes and cache blocks](template-functions-and-includes.md)
- [Templates and design](../../../guides/templates-and-design.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
