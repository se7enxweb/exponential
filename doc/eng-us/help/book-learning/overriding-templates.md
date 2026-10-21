---
title: "Overriding templates"
audience: builder, designer, developer
category: books
order: 440
summary: How the template system finds a template (override rules first, then the designs in order), the syntax of override.ini.append.php, and the match keys.
keywords: override, override.ini, template override, cascading, Source, MatchFile, Match, class_identifier, node, section, depth, design, Learning book
type: help
---
# Overriding templates

The template system chooses a file in two steps. This is the idea of the whole chapter, and it has not changed.

1. **The override rules** are looked at first (`override.ini`). If a rule matches the template being asked for, with its
   conditions, the template file it names is used.
2. If no rule matches, the file is looked for in the **designs**, in order: the siteaccess's design, the extension designs,
   then `standard`. The first file found is used. (This is the cascade.)

So you change a template without touching the original: put a more specific one in your design, or add a rule that
sends one class, one section or one node to a file of its own.

## The syntax

A rule is a block in an `override.ini.append.php` of your siteaccess or an extension:

```ini
[full_article]
Source=node/view/full.tpl
MatchFile=full/article.tpl
Subdir=templates
Match[class_identifier]=article
```

- The block name (`full_article`) is any unique name.
- `Source` is the template that is being asked for.
- `MatchFile` is the file to use instead, below `<design>/override/templates/` of a design, for example `extension/mysite_design/design/mysite/override/templates/full/article.tpl` (for `Subdir=templates`).
- `Match[...]` are the conditions. All must be true.

The book's keys were the class, the section, the node, the object and the depth. Today there are more, listed in
[Override conditions](../extension-development/part-2-builder/override-conditions.md): `class_identifier` (use this one
rather than the numeric `class`), `class_group`, `node`, `object`, `parent_node`, `parent_class_identifier`,
`section_identifier` (rather than `section`), `state_identifier`, `depth`, `url_alias`, `layout`, and others.
Where several rules match, a `Priority` decides.

## A worked example

The full view of every article should be different from the standard one:

1. Copy `design/standard/templates/node/view/full.tpl` to `override/templates/full/article.tpl` in your design.
2. Add the `[full_article]` block above.
3. Clear the override cache: a new rule or a new file needs `php bin/php/ezcache.php --clear-id=template-override --allow-root-user`
   (see [Which cache do I clear?](../manual/which-cache-do-i-clear.md)).
4. Load an article page. `TemplateSettings ShowUsedTemplates=enabled` (for you only, on a development installation)
   lists the template that was used, and the override that picked it.

**Design > Template editor** in the administration lists the rules in the order they are tried and lets you reorder them.

More: [Template overrides: the basics](../extension-development/part-2-builder/template-overrides-basics.md) and
[Resources and overrides](../template-language/resources-and-overrides.md).

Next: [common template tasks](common-template-tasks.md).

## Source and license

Adapted from chapter 3, "Displaying Content with eZ publish Templates", of *Learning eZ publish: Building Content Management Solutions* by Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding and Ben Pirt (additional material by Bruce Morrison), second edition, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x and Graham
Brookins. Licensed under the GNU Free Documentation License, version 1.3 or any later version, with no Invariant
Sections, no Front-Cover Texts and no Back-Cover Texts ([the license](../../LICENSE-GFDL-1.3.txt)).
Changes: rearranged and shortened; syntax, operators, settings and examples updated to Exponential 6 (foreach, current settings, no inline style or script).
