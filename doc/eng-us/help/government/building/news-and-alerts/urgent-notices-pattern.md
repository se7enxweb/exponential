---
title: The urgent notices pattern
audience: government, website-creator, admin, editor
category: public-sector
order: 30
summary: How to show urgent notices on every page or the front page of an Exponential site without a built-in alert feature - a notices folder the template lists when it is not empty, a short notice class with an end time hidden by hide.php, a role for publishing at any hour, and cache refresh.
keywords: urgent notice, alert banner, site-wide notice, front page, notices folder, hide.php, HideSettings, cache block, view cache, static cache, layouts block, on-call
type: help
---
# The urgent notices pattern

This page is for website creators, administrators and editors who need to put an urgent notice in front of every
visitor: a service disruption, severe weather, an office closure. Exponential has no built-in alert banner. This page
describes a pattern built from things that exist: a folder, a small class, a template list, the `hide.php` cronjob,
roles and caches.

## Contents

1. [The parts](#the-parts)
2. [The Notice class](#the-notice-class)
3. [The template](#the-template)
4. [Ending notices automatically](#ending-notices-automatically)
5. [Caches](#caches)
6. [Alternative: a layouts block](#alternative-a-layouts-block)

## The parts

| Part | What |
|---|---|
| A folder "Notices" | outside the menus, in a section Anonymous may read |
| A Notice class | headline, text, severity, end time |
| A template list in the pagelayout or front page | shows notices when there are any, nothing otherwise |
| A role | lets the on-call team create and hide notices, and nothing else |
| `hide.php` | hides notices after their end time |

## The Notice class

| Attribute | Identifier | Datatype | Required |
|---|---|---|---|
| Headline | `title` | Text line | yes |
| Text | `text` | Text block | yes |
| Severity | `severity` | Selection: information, warning | yes |
| More information | `link` | Object relation (a page with details) | no |
| Hide after | `hide_date` | Date and time | no |

Short and plain: a notice is one or two sentences and a link to details.

## The template

In the pagelayout (for every page) or the front page template:

```text
{def $notices = fetch( 'content', 'list', hash(
        'parent_node_id', $notices_node_id,
        'class_filter_type', 'include',
        'class_filter_array', array( 'notice' ),
        'sort_by', array( 'published', false() ) ) )}
{if $notices|count}
<section class="site-notices" aria-label="{'Urgent notices'|i18n( 'design/<your design>' )}">
  {foreach $notices as $n}
    <p class="notice notice-{$n.data_map.severity.class_content.options[$n.data_map.severity.value[0]].name|downcase|wash}">
      <strong>{$n.name|wash}</strong> {$n.data_map.text.content|wash}
    </p>
  {/foreach}
</section>
{/if}
{undef $notices}
```

Hidden notices are not listed for visitors, because fetches respect visibility (`ShowHiddenNodes=false`). Show the
severity as a word as well as a color. Do not use `role="alert"` for notices present when the page loads; it is meant
for messages that appear during use.

## Ending notices automatically

```ini
# settings/override/content.ini.append.php
[HideSettings]
RootNodeList[]=<node id of the Notices folder>
HideDateAttributeList[notice]=hide_date
```

Clear the INI cache. `hide.php` runs in the global cronjob part; a notice is hidden at the first run after its time
([Cronjobs](../../../../guides/cronjobs.md)). Ending a notice early: **Hide / unhide** in its menu.

## Caches

| Cache | Effect on notices | Do |
|---|---|---|
| View cache of content pages | a notice in the pagelayout is outside the view cache of content | fine |
| Cache blocks in the pagelayout | a cached notice list stays until the block expires | give the block a short expiry or a subtree expiry on the Notices folder ([View cache and cache blocks](../../../creators/website-creators/view-cache-and-cache-blocks.md)) |
| Static cache | static pages keep the old notice until refreshed | add the main pages to `AlwaysUpdateArray[]`, or regenerate |
| Velocity response cache, CDN | copies until their lifetime ends | clear: `./console exp:velocity cache clear --apply`; CDN purge |

Rehearse: publish a test notice and time how long until an anonymous visitor sees it everywhere.

## Alternative: a layouts block

With Exponential Layouts, a shared layout (header, for example) can hold a block editors fill with a notice and empty
afterwards ([The layouts editor](../../../layouts-editor.md)). It needs no template work but no automatic end time;
someone must remove it.

## Related pages

- [News and alerts](README.md)
- [Archive and expiry](archive-and-expiry.md)
- [Alerts, availability, maintenance mode and static cache](../../levels/emergency-services/alerts-availability-maintenance-and-static-cache.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
