---
title: A template override
audience: developer
category: examples
order: 30
summary: Give the full view of one content class its own template with an override rule in override.ini.append.php, kept in an extension design, with the match conditions, the priority and the cache clears it needs.
keywords: template override, override.ini, override.ini.append.php, Source, MatchFile, Subdir, Match, class_identifier, node, viewmode, Priority, design, DesignExtensions, template-override cache, eztemplatecheck
type: example
---
# A template override

An override replaces one template with another when conditions hold: *the full view of a Note*, *the line view below
node 42*, *everything in section 3*. You give the class **Note** (from [Create a content class](content-class.md)) a
full view of its own, kept in the example extension so that it is under version control and survives an upgrade.

Prerequisites: the extension skeleton of [An extension skeleton](extension-skeleton.md), switched on, with
`DesignExtensions[]=expexample` in its `design.ini.append.php`; the name of the design of your site (here `mysite`,
the siteaccess `site`).

## 1. How the template is chosen

For `node/view/full.tpl` the kernel collects match keys from the page being shown and tries the override rules of
the siteaccess in order. The keys a node view offers:

| Key | Value |
|---|---|
| `object`, `node`, `parent_node` | ids of the object, the node and its parent |
| `class_identifier`, `parent_class_identifier` | the class of the object, and of the parent's object |
| `viewmode` | `full`, `line`, `embed` ... |
| `section`, `state`, `state_identifier` | the section id, the object state ids and `<group>/<state>` identifiers |
| `remote_id`, `node_remote_id`, `parent_node_remote_id` | remote ids |
| `depth`, `url_alias`, `class_group`, `navigation_part_identifier` | the rest |

Every `Match[...]` of a rule must hold. The first rule that matches wins; rules with a `Priority` come first, the lowest
number first.

## 2. The template

Create it in the override directory of the design, inside the extension:

```bash
mkdir -p extension/expexample/design/mysite/override/templates/full
```

`extension/expexample/design/mysite/override/templates/full/note.tpl`:

```tpl
{* The full view of a Note. $node is the node shown. *}
<article class="content-view-full class-note">
    <h1>{$node.data_map.title.content|wash}</h1>

    {if $node.data_map.body.has_content}
        <div class="note-body">
            {attribute_view_gui attribute=$node.data_map.body}
        </div>
    {/if}

    <p class="note-meta">
        {'Last changed %date'|i18n( 'extension/expexample', '', hash( '%date', $node.object.modified|l10n( 'shortdatetime' ) ) )}
    </p>
</article>
```

No `<style>` block and no `style=""` attribute: classes only, styled in the design's style sheets (an administration
template must follow the admin styling rules, and the administration's Content Security Policy refuses inline
script).

## 3. The rule

`extension/expexample/settings/siteaccess/site/override.ini.append.php` holds rules for the siteaccess `site` only,
which is what a site extension wants. For every siteaccess, use `extension/expexample/settings/override.ini.append.php`;
for a rule of one installation that is not in an extension, `settings/siteaccess/site/override.ini.append.php`.

```ini
<?php /* #?ini charset="utf-8"?

[full_note]
Source=node/view/full.tpl
MatchFile=full/note.tpl
Subdir=templates
Match[class_identifier]=note

*/ ?>
```

- The block name (`full_note`) must be unique among all rules of the siteaccess; a later file with the same block
  name replaces the rule.
- `Source` is the template replaced, `MatchFile` the new one relative to `override/templates/`, `Subdir` is always
  `templates`.
- Narrow it with more conditions: `Match[section]=1` (and the class is Note), `Match[parent_node]=42`.
- A second rule for the line view: `Source=node/view/line.tpl`, `MatchFile=line/note.tpl`.

An extension's per-siteaccess settings in `settings/siteaccess/<name>/` are read when the extension is in
`ActiveExtensions[]`; see [Per-site settings inside extensions](../../features/6.0/multi-site-ini-overrides.md).

## 4. Clear the caches and check

A new template path or a new rule needs the override and template caches cleared once:

```bash
php bin/php/eztemplatecheck.php -ssite extension/expexample/design/mysite/override/templates/full/note.tpl --allow-root-user
php bin/php/ezcache.php --clear-id=template-override --allow-root-user
php bin/php/ezcache.php --clear-tag=template --allow-root-user
php bin/php/ezcache.php --clear-tag=content --allow-root-user
```

Expected from the first command: `Template file valid: ...`. Then open a Note on the site. With
`site.ini [TemplateSettings] ShowUsedTemplates=enabled` (or the template path comments) the list of used templates names
`full/note.tpl` for `node/view/full.tpl`. Later edits to the same file need only `--clear-tag=template` (or nothing,
while the template cache is off). Under Velocity, also clear its response cache: `./console exp:velocity cache clear`.

Checked for this page (2026-10-18): `bin/php/eztemplatecheck.php` reports the template valid (syntax); the match keys
are those `eZNodeviewfunctions::generateNodeViewData()` sets.

## 5. The same without files

**Design > Template Editor** creates the same rule and file for you: open `node/view/full.tpl`, press
**New override**, choose the class. It writes `settings/siteaccess/<siteaccess>/override.ini.append.php` (not your
extension) and keeps a copy before writing. See [Template editor](../../features/6.0/template-editor-overrides.md).

## Common mistakes

| Symptom | Cause |
|---|---|
| The page shows the old template | the `template-override` cache; or another rule earlier in the order matches first |
| The page is empty, with the layout around it | `MatchFile` names a file that is not in `override/templates/` of a design the siteaccess uses |
| The rule works for every siteaccess, also the administration | it is in `settings/override.ini.append.php` of the extension instead of `settings/siteaccess/<name>/` |
| Two extensions' rules fight | the same block name; or the order of `ActiveExtensions[]` decides: [Extension loading order](../../features/6.0/extension-loading-order.md) |

## Derived from

- `kernel/classes/eznodeviewfunctions.php` (the match keys of a node view)
- `doc/guides/templates-and-design.md`, sections 4 and 5
- `design/standard/templates/node/view/full.tpl`

## Related pages

- [Cookbook](README.md)
- [Templates and design](../../guides/templates-and-design.md), [Template editor](../../features/6.0/template-editor-overrides.md)
- [Template operators](../template-operators/README.md) and [fetch functions](../fetch-functions/README.md)
- [A custom template operator](custom-template-operator.md), [Caching](caching.md)
- The coding rules and the pattern catalogue: [the standards](../../standards/README.md)
- [Free documentation](../../help/free-documentation.md): this page is free to copy, translate and improve.
