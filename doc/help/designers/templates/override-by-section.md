---
title: Override by section
audience: designer, developer
category: templates
order: 610
summary: Giving an area of the site its own templates with sections: section_identifier (portable) versus section id, combining with class rules, a section-specific stylesheet, and section classes on the body from the pagelayout.
keywords: override section, section_identifier, Match section, site area design, section templates, members area, intranet section, section styling
type: help
---
# Override by section

This page is for designers who give an area of the site (a members area, a press room, a campaign) its own look.
Sections group content across the tree; editors assign them.

## Contents

1. [The rule](#the-rule)
2. [Combining with classes](#combining-with-classes)
3. [Styling by section without overrides](#styling-by-section-without-overrides)

## The rule

```ini
[mytheme_full_press_article]
Source=node/view/full.tpl
MatchFile=full/press_article.tpl
Subdir=templates
Match[section_identifier]=press
Match[class_identifier]=article
Priority=10
```

Use `section_identifier` (set when the section is created) rather than `section` (its id), which differs between
installations.

## Combining with classes

A section rule alone replaces the template for every class in the section, folders and images included. Usually combine
it with `class_identifier`, and give it a lower `Priority` than the plain class rule so it is checked first.

## Styling by section without overrides

Often only colors or a header differ. Then no override is needed: the pagelayout reads the section from
`$module_result.content_info` and sets a class:

```tpl
{def $section_class = ''}
{if and( is_array( $module_result.content_info ), is_set( $module_result.content_info.section_identifier ) )}
    {set $section_class = concat( 'section-', $module_result.content_info.section_identifier )}
{/if}
<body class="{$section_class|wash}">
```

and the stylesheet does the rest (`.section-press { --site-accent: ... }`). The ezwebin design's page data adds a
`section_id_<n>` class to its page wrapper the same way.

## Related pages

- [Override match keys](override-match-keys.md)
- [Pagelayout variables](../theme-design/pagelayout-variables.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
