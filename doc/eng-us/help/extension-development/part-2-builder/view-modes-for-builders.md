---
title: View modes for builders
audience: developer, designer, app-creator
category: extension-development
order: 120
summary: A view mode is one way of drawing content - full page, line in a list, embed in rich text. The modes the kernel ships, how a page and a list choose one, adding your own mode, and which modes are cached.
keywords: view mode, viewmode, full, line, embed, embed-inline, listitem, node_view_gui, content_view_gui, custom view mode, CachedViewModes, node/view
type: help
---
# View modes for builders

Part 2, chapter 12. The same article is drawn differently on its own page, in a list of news, and when an editor
embeds it in another article. Each of these is a **view mode**, and each has its own template
`node/view/<mode>.tpl`, which overrides can replace per class. This chapter shows the modes and how to add your own.

## The modes the kernel ships

The standard design has one template per mode in `design/standard/templates/node/view/`:

| Mode | Used for |
|---|---|
| `full` | the page of a node: `/content/view/full/<node>` and every URL alias |
| `line` | one entry in a list |
| `listitem` | a short list entry |
| `embed` | an object embedded in rich text as a block |
| `embed-inline` | an object embedded inside a line of text |
| `sitemap`, `search`, `thumb`, `text`, `text_linked`, `toolline`, `plain` | special lists and outputs |
| `print`, `pdf`, `execute_pdf` | print and PDF output |

Your design and your extensions can add more.

## How a page chooses its mode

The address carries it: `/content/view/<mode>/<node>`. A URL alias is the full view. In templates, lists choose the
mode of each item:

```text
{def $children = fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id, 'limit', 10 ) )}
{foreach $children as $child}
    {node_view_gui content_node=$child view='line'}
{/foreach}
```

`node_view_gui` draws a node in a mode, through the override system, so a `line` override for articles applies here
automatically. `content_view_gui content_object=$object view='embed'` does the same for an object without a location.

## Adding your own view mode

Say a front page should show teasers with a picture: a mode called `teaser`.

1. Create the template for the mode, in your design:
   `extension/mysite_design/design/mysite/templates/node/view/teaser.tpl`

   ```text
   <div class="teaser">
       <h3><a href={$node.url_alias|ezurl}>{$node.name|wash}</a></h3>
   </div>
   ```

2. Give classes their own teaser where needed, with overrides on `Source=node/view/teaser.tpl` (see
   [Override conditions](override-conditions.md)).
3. Use it: `{node_view_gui content_node=$child view='teaser'}`.
4. Clear the template override cache (new files): `php bin/php/ezcache.php --clear-id=template-override,template`.

No setting is needed for a mode used only from templates. Two lists mention modes for other purposes:

| Setting | Purpose |
|---|---|
| `content.ini [VersionView] ViewModes[]` | the modes the version preview of the administration offers (`content/versionview`) |
| `content.ini [embed] AvailableViewModes[]` and `[embed-inline]` | the modes the online editor offers for embeds |

## Caching and view modes

Full pages are kept in the **view cache**. Which modes are cached is set in `site.ini [ContentSettings]`:

```ini
CachedViewModes=full;sitemap;pdf
```

A mode you add for pages that people open by address (for example `/content/view/print/<node>`) can be added there.
Lists drawn inside a cached page are part of that page's cache. The rules for clearing the view cache are in
[Caching and templates](caching-and-templates.md).

## Variables in a view mode template

| Variable | What it is |
|---|---|
| `$node` | the node being drawn |
| `$object` | `$node.object`, in `content_view_gui` the object |
| `$view_parameters` | the unordered parameters of the address (`/(offset)/20`) in a full view |
| `$persistent_variable` | values a full view template sets for the pagelayout |

## Related pages

- Previous: [Override conditions: class, node, section, view mode and more](override-conditions.md)
- Next: [Fetching content in templates](fetch-for-builders.md)
- [Version preview in other view modes](../../../features/6.0/version-preview-view-modes.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
