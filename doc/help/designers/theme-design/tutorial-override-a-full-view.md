---
title: "Tutorial: override a full view"
audience: designer
category: theme-design
order: 780
summary: Step by step, give one content class its own full page template in your design: find the class identifier and fields, write the override rule, write the template with the fields, check which template is used, and clear the right caches.
keywords: tutorial, override full view, full view template, class template, override.ini, node/view/full.tpl, MatchFile, class_identifier, article template, custom page template
type: help
---
# Tutorial: override a full view

This tutorial is for designers who want one kind of page, say events, to look different from the default. You will
add an override rule and a template in your design. Allow 30 minutes. It assumes a design extension of your own
([Tutorial: create a theme extension](tutorial-create-a-theme-extension.md)).

## Contents

1. [Step 1: find the class and its fields](#step-1-find-the-class-and-its-fields)
2. [Step 2: the override rule](#step-2-the-override-rule)
3. [Step 3: the template](#step-3-the-template)
4. [Step 4: clear caches and look](#step-4-clear-caches-and-look)
5. [Step 5: refine](#step-5-refine)

## Step 1: find the class and its fields

In the administration, **Setup > Classes**, open the class (for example "Event"). Note its **identifier** (`event`) and
the identifiers of the fields you want to show (`title`, `short_description`, `text`, `from_time`, `image` ...).
Identifiers, not names, are what templates use.

## Step 2: the override rule

In your extension's `settings/siteaccess/<your siteaccess>/override.ini.append.php` (or `settings/override.ini.append.php`
for every siteaccess with the extension):

```ini
<?php /* #?ini charset="utf-8"?
[mytheme_full_event]
Source=node/view/full.tpl
MatchFile=full/event.tpl
Subdir=templates
Match[class_identifier]=event
*/ ?>
```

- `Source`: the template being replaced: the full view of any node.
- `MatchFile`: your template, relative to `override/templates/` of a design.
- `Match[...]`: the condition; here the class.
- The section name must be unique across all override files: prefix it with your theme.

## Step 3: the template

`design/mytheme/override/templates/full/event.tpl`:

```tpl
<article class="event">
    <header class="event-header">
        <h1>{$node.name|wash}</h1>
        {if $node.data_map.from_time.has_content}
            <p class="event-date">
                <time datetime="{$node.data_map.from_time.content.timestamp|datetime( 'custom', '%Y-%m-%dT%H:%i' )}">
                    {$node.data_map.from_time.content.timestamp|l10n( 'shortdatetime' )}
                </time>
            </p>
        {/if}
    </header>
    {if $node.data_map.image.has_content}
        {attribute_view_gui attribute=$node.data_map.image image_class='large'}
    {/if}
    {if $node.data_map.text.has_content}
        <div class="event-text">{attribute_view_gui attribute=$node.data_map.text}</div>
    {/if}
</article>
```

The full view template receives `$node` (the node shown), `$view_parameters` and `$language_code`
([Full view](../templates/full-view.md)). Test `has_content` before printing optional fields.

## Step 4: clear caches and look

```bash
php bin/php/ezcache.php --clear-tag=ini
php bin/php/ezcache.php --clear-id=template-override,content
```

Open an event. With `[TemplateSettings] ShowTemplatePathComments=enabled` on your test siteaccess, the page source
shows `override/templates/full/event.tpl` around the content. If the default template is still used, see
[Debugging a design](debugging-a-design.md), "My new template or override is ignored".

## Step 5: refine

- Add a stylesheet only for events with `{ezcss_require( array( 'event.css' ) )}` at the top of the template.
- Add the line view for event lists the same way (`Source=node/view/line.tpl`, `MatchFile=line/event.tpl`).
- Check the page at 375 px, in dark mode and with the keyboard.

## Related pages

- [Override basics](../templates/override-ini-basics.md)
- [Node templates by class](../templates/node-templates-by-class.md)
- [View modes](../templates/view-modes.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
