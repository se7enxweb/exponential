---
title: Building a site without writing code
audience: developer, designer, app-creator
category: extension-development
order: 210
summary: A complete small site built with Part 2's tools only - a settings extension and a design extension, classes, overrides for full and line views, a menu in a cache-block, a members area - as an ordered plan with checks.
keywords: site building, no code, design extension, settings extension, overrides, classes, pagelayout, menu, members area, plan, checklist, project
type: help
---
# Building a site without writing code

Part 2, chapter 21. This chapter puts the whole part together: a small company site with news, events and a
members-only area, built from settings, classes, templates and overrides, with no PHP at all. It is a plan you can
follow on your development installation; each step links the chapter that explains it.

## The site

- A front page with the three newest news items and the next events.
- News articles and events, each with its own full view and list view.
- A members area that only signed-in members can read.
- A menu of the top folders on every page.

## Step 1: two extensions

```bash
mkdir -p extension/mysite_settings/settings/siteaccess/mysite
mkdir -p extension/mysite_design/settings extension/mysite_design/design/mysite/templates \
         extension/mysite_design/design/mysite/override/templates extension/mysite_design/design/mysite/stylesheets
```

Give both an `ezinfo.php` and `extension.xml` ([ezinfo.php](../part-3-extension-developer/ezinfo-php.md)), register the
design in `extension/mysite_design/settings/design.ini.append.php`:

```ini
[ExtensionSettings]
DesignExtensions[]=mysite_design
```

and activate both, the settings extension first ([Settings that live in an extension](extension-settings-for-builders.md)).

## Step 2: the siteaccess uses your design

In `extension/mysite_settings/settings/siteaccess/mysite/site.ini.append.php`:

```ini
[DesignSettings]
SiteDesign=mysite
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=base
```

Check with the design chain script of [The design chain](the-design-chain.md): your design comes first.

## Step 3: the content model

In the administration, create the classes ([Content classes for builders](content-classes-for-builders.md)):

| Class | Identifier | Attributes |
|---|---|---|
| News article | `news_article` | `title` (text line), `intro` (rich text), `body` (rich text), `image` (image) |
| Event | `event` | `title`, `intro`, `start_date` (date and time), `location` (text line) |

Use the existing Folder class for containers. Create a folder "News", a folder "Events" and a folder "Members".

## Step 4: views

Overrides in `extension/mysite_settings/settings/siteaccess/mysite/override.ini.append.php`:

```ini
[full_news_article]
Source=node/view/full.tpl
MatchFile=full/news_article.tpl
Subdir=templates
Match[class_identifier]=news_article

[line_news_article]
Source=node/view/line.tpl
MatchFile=line/news_article.tpl
Subdir=templates
Match[class_identifier]=news_article

[full_event]
Source=node/view/full.tpl
MatchFile=full/event.tpl
Subdir=templates
Match[class_identifier]=event

[full_frontpage]
Source=node/view/full.tpl
MatchFile=full/frontpage.tpl
Subdir=templates
Match[node]=2
```

Write the four templates in `extension/mysite_design/design/mysite/override/templates/`
([Template overrides: the basics](template-overrides-basics.md)). The front page fetches the newest news
([Fetching content in templates](fetch-for-builders.md)):

```text
{def $news = fetch( 'content', 'tree', hash( 'parent_node_id', 2,
                                             'class_filter_type', 'include',
                                             'class_filter_array', array( 'news_article' ),
                                             'sort_by', array( 'published', false() ),
                                             'limit', 3 ) )}
{foreach $news as $item}
    {node_view_gui content_node=$item view='line'}
{/foreach}
{undef $news}
```

## Step 5: the menu

In your design's `templates/pagelayout.tpl` (start from a copy of the pagelayout of the design you fall back to), put
the menu in a `cache-block` ([Caching and templates](caching-and-templates.md)):

```text
{cache-block keys=array( $module_result.node_id )}
<nav>
{foreach fetch( 'content', 'list', hash( 'parent_node_id', 2,
                                       'class_filter_type', 'include',
                                       'class_filter_array', array( 'folder' ),
                                       'sort_by', array( 'priority', true() ) ) ) as $item}
    <a href={$item.url_alias|ezurl}>{$item.name|wash}</a>
{/foreach}
</nav>
{/cache-block}
```

## Step 6: styles

Put your CSS in `extension/mysite_design/design/mysite/stylesheets/site.css` and load it through the design's settings
(`design.ini [StylesheetSettings]`), never with `style=""` or a `<style>` block; see
[Theme assets, the packer and CSP](../part-6-theme-developers/theme-assets-the-packer-and-csp.md).

## Step 7: the members area

Follow the worked plan in [Sections, roles and access for builders](sections-roles-and-access-for-builders.md):
section `members`, assigned to the Members folder, a role with `content/read` limited to that section, assigned to the
Members user group.

## Step 8: check

- [ ] Every page renders without errors in the debug output.
- [ ] The used-templates table shows your overrides on news, events and the front page.
- [ ] The members area is denied when signed out and readable as a member.
- [ ] Publishing a news item updates the front page (the view cache is cleared on publish).
- [ ] Both extensions are Git repositories with their first commit; `settings/override/` is not in either.

## What you built, and what is next

A real site, under version control, with no code. When a requirement appears that settings and templates cannot meet
(an import from another system, a field type that does not exist, a page that computes something), it is time for
[Part 3](../part-3-extension-developer/README.md); the next chapter helps you decide.

## Related pages

- Previous: [Cronjobs for builders](cronjobs-for-builders.md)
- Next: [When settings are not enough](when-settings-are-not-enough.md)
- [Templates and design](../../../guides/templates-and-design.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
