---
title: Node templates by class
audience: designer, developer
category: templates
order: 560
summary: Organizing a design's templates per content class and view mode: the override rule pattern, a folder layout by view mode, a naming scheme, a checklist of the class and mode pairs a site needs, and how the media and starter designs do it.
keywords: class templates, template per class, override per class, class_identifier, full/article.tpl, line/article.tpl, view mode folders, template organization, override naming
type: help
---
# Node templates by class

This page is for designers who organize the view templates of a whole site. The usual pattern: one override rule and
one template for each pair of **content class** and **view mode** that needs its own look.

## Contents

1. [The pattern](#the-pattern)
2. [Folder layout and names](#folder-layout-and-names)
3. [Which pairs a site needs](#which-pairs-a-site-needs)
4. [How the shipped designs do it](#how-the-shipped-designs-do-it)

## The pattern

```ini
[mytheme_full_article]
Source=node/view/full.tpl
MatchFile=full/article.tpl
Subdir=templates
Match[class_identifier]=article

[mytheme_line_article]
Source=node/view/line.tpl
MatchFile=line/article.tpl
Subdir=templates
Match[class_identifier]=article
```

## Folder layout and names

```
design/mytheme/override/templates/
├── full/article.tpl      full/folder.tpl      full/event.tpl
├── line/article.tpl      line/event.tpl
├── card/article.tpl
└── embed/image.tpl       embed/video.tpl
```

- Folder = view mode; file = class identifier.
- Section names: `<theme>_<mode>_<class>`; unique across all settings files.
- One rule per pair; no giant switch in a generic template ([Switch and case](switch-case.md)).

## Which pairs a site needs

| Class | full | line | card | embed |
|---|---|---|---|---|
| article | yes | yes | yes | |
| folder | yes | yes | | |
| image | | | | yes |
| ... | | | | |

Fill a table like this from the site's content model; every "yes" is one rule and one template. Classes without a rule
use the default `node/view/<mode>.tpl`, so make the defaults decent.

## How the shipped designs do it

- The media design keeps its rules in `settings/siteaccess/site/override.ini.append.php` (and `bold/`) of its
  extension, with templates in `override/templates/full/`, `line/`, `embed/`, `content/` and more.
- The starter design keeps them in `settings/siteaccess/cjw_starter/override.ini.append.php`; each `full/<class>.tpl`
  includes `content/full/<class>.tpl`, so layout blocks can draw parts of the same content.
- The super design shows the minimal case: one rule, `full/article.tpl`.

## Related pages

- [Override basics](override-ini-basics.md)
- [View modes](view-modes.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
