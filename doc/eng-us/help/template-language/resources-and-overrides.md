---
title: Template resources and overrides
audience: developer, designer, app-creator
category: development
order: 420
summary: How a template name becomes a file - the design: and file: resources, the ordered design bases of a siteaccess (site design, additional designs, standard design, each with its extension designs first), override.ini rules with their match keys and order, and how include and the view functions use them - with the real resolution on the site siteaccess.
keywords: template resource, design:, file:, eZTemplateDesignResource, design bases, SiteDesign, AdditionalSiteDesignList, StandardDesign, DesignExtensions, override.ini, match keys, override order, include, node_view_gui
type: help
---
# Template resources and overrides

A template is always named through a **resource**: `design:node/view/full.tpl`, `file:var/...`. The resource turns
the name into a file. For `design:` that involves the designs of the siteaccess, the designs of every extension and
the override rules of `override.ini`. This page shows how, with the resolution the installation's `site` siteaccess performs.

## Contents

1. [Resources](#resources)
2. [The design bases](#the-design-bases)
3. [Finding a file without overrides](#finding-a-file-without-overrides)
4. [Overrides](#overrides)
5. [Who sets the keys](#who-sets-the-keys)
6. [Include and the view functions](#include-and-the-view-functions)
7. [Related pages](#related-pages)

## Resources

| Resource | Class | Finds |
|---|---|---|
| `design:` | `eZTemplateDesignResource` (`kernel/common/eztemplatedesignresource.php`) | a file below `templates/` (or `override/templates/`) of the design bases, after the override rules |
| `file:` | `eZTemplateFileResource` (`lib/eztemplate/classes/eztemplatefileresource.php`) | a path, absolute or relative to the installation root |

`eZTemplate::factory()` registers the design resource; the file resource is always there and is the default: a name
without a resource is read as a file path. The examples of these pages use `file:` to render templates kept outside the design
folders; site code uses `design:` so that designs and overrides apply.

## The design bases

`eZTemplateDesignResource::allDesignBases()` builds the ordered list of folders to search
(`findDesignBase()`):

1. the designs, in this order: `[DesignSettings] SiteDesign`, then `AdditionalSiteDesignList[]`, then
   `StandardDesign`;
2. for each design, first `extension/<name>/design/<design>` of every extension in `design.ini [ExtensionSettings]
   DesignExtensions[]` (in that order), then the kernel's `design/<design>`; folders that do not exist are skipped.

With `DesignLocationCache=enabled` the list is stored in a cache file (cleared with the `design_base` cache id). A
read-only CLI script printed the list for the installation's `site` siteaccess (`eZTemplateDesignResource::allDesignBases()`):

```text
siteaccess:               site
SiteDesign:               media
AdditionalSiteDesignList: ezwebin, standard, base
StandardDesign:           standard
DesignExtensions:         50 extensions

design bases, in search order (49):
  1  extension/sevenx_alpha_settings/design/media
  2  extension/sevenx_themes_media/design/media
  3  extension/sevenx_authentication_2fa/design/media
  4  design/media
  5  extension/cjw_newsletter/design/ezwebin
  6  extension/ezautosave/design/ezwebin
  7  extension/ezwebin/design/ezwebin
  8  design/ezwebin
  9  extension/expui/design/standard
 10  extension/ezjscore/design/standard
 11  extension/xrowmetadata/design/standard
 12  extension/ezmultiupload/design/standard
 13  extension/cjw_newsletter/design/standard
 14  extension/ezodf/design/standard
 15  extension/swark/design/standard
 16  extension/bcgooglesitemaps/design/standard
 17  extension/bcwebsitestatistics/design/standard
 18  extension/bccie/design/standard
 19  extension/xrowextract/design/standard
 20  extension/enhancedezbinaryfile/design/standard
 21  extension/enhancedselection2/design/standard
 22  extension/ezupdate/design/standard
 23  extension/git_manager/design/standard
 24  extension/syndication/design/standard
 25  extension/ngclasslist/design/standard
 26  extension/explayouts/design/standard
 27  extension/explayouts_content_browser_ui/design/standard
 28  extension/explayouts_ui/design/standard
 29  extension/explayouts_ui_api/design/standard
 30  extension/exp_enhanced_link/design/standard
 31  extension/ezownerchange/design/standard
 32  extension/recaptcha/design/standard
 33  extension/powercontent/design/standard
 34  extension/sevenx_dse/design/standard
 35  extension/sevenx_authentication_2fa/design/standard
 36  extension/cjw_themes_jumper/design/standard
 37  extension/cjw_tmv/design/standard
 38  extension/birthday/design/standard
 39  extension/exp_oe_tiptap/design/standard
 40  extension/ezstarrating/design/standard
 41  extension/ezgmaplocation/design/standard
 42  extension/ezautosave/design/standard
 43  extension/ezie/design/standard
 44  extension/eztags/design/standard
 45  extension/ezoe/design/standard
 46  extension/ezwt/design/standard
 47  design/standard
 48  extension/enhancedezbinaryfile/design/base
 49  design/base

```

The first base that has the file wins, so `extension/sevenx_alpha_settings/design/media` can replace any template of
the media theme, and the media theme any template of ezwebin or of the standard design.

## Finding a file without overrides

`eZTemplateDesignResource::fileMatch()` tries `<base>/templates/<path>` in base order:

```text
resolution of design:<path> without overrides (first existing file wins):
  design:pagelayout.tpl
    -> extension/sevenx_themes_media/design/media/templates/pagelayout.tpl  (2 places tried)
  design:node/view/full.tpl
    -> extension/ezwebin/design/ezwebin/templates/node/view/full.tpl  (7 places tried)
  design:node/view/line.tpl
    -> extension/ezwebin/design/ezwebin/templates/node/view/line.tpl  (7 places tried)
  design:content/datatype/view/ezstring.tpl
    -> design/standard/templates/content/datatype/view/ezstring.tpl  (47 places tried)
```

Including a design template from a template uses the same walk. Here the standard design's view template of the
`ezstring` datatype, found in the 47th base:

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 96 ) )}
[{include uri='design:content/datatype/view/ezstring.tpl' attribute=$node.data_map.title}]
```

Output:

```text
[
Why Should You Choose Strength Training?]
```

## Overrides

`override.ini` (of the siteaccess and of every extension) has one group per rule. The rule for articles on a development installation,
from `extension/sevenx_themes_media/settings/siteaccess/site/override.ini.append.php`:

```ini
[node/view/full#ng_article]
Source=node/view/full.tpl
MatchFile=node/view/full/full_ng_article.tpl
Subdir=templates
Match[class_identifier]=ng_article
Priority=1
```

The design resource collects the rules per `Source` (`eZTemplateDesignResource::overrideArray()`); the `MatchFile` is
looked up in `<base>/override/templates/` of the design bases, in base order. When a template is fetched, the rules
for its name are tried in order and **the first rule whose conditions all match wins**; a rule without conditions
is a default that a later matching rule can still replace. A condition `url_alias` matches when the page's URL alias
starts with the given value; any other key must equal the key's value (or be one of its values when the key is an
array, such as `class_group`). Rules are taken in the order of the merged `override.ini`, or by a numeric `Priority`
in the group when one is given (lower first).

On a development installation, `node/view/full.tpl` has 57 rules; the first ones:

```text
override rules for design:node/view/full.tpl: 57 (default file extension/ezwebin/design/ezwebin/templates/node/view/full.tpl)
  [node/view/full#showcase_s1_tour] extension/sevenx_alpha_settings/design/media/override/templates/node/view/full/showcase_s1_tour.tpl  if {"remote_id":"showcase-s1-tour"}
  [node/view/full#frontpage] extension/sevenx_themes_media/design/media/override/templates/node/view/full/full_frontpage.tpl  if {"class_identifier":"frontpage"}
  [node/view/full#ng_article] extension/sevenx_themes_media/design/media/override/templates/node/view/full/full_ng_article.tpl  if {"class_identifier":"ng_article"}
  [node/view/full#ng_audio] extension/sevenx_themes_media/design/media/override/templates/node/view/full/full_ng_audio.tpl  if {"class_identifier":"ng_audio"}
  [node/view/full#ng_banner] extension/sevenx_themes_media/design/media/override/templates/node/view/full/full_ng_banner.tpl  if {"class_identifier":"ng_banner"}
  [node/view/full#ng_blog_post] extension/sevenx_themes_media/design/media/override/templates/node/view/full/full_ng_blog_post.tpl  if {"class_identifier":"ng_blog_post"}
  [node/view/full#ng_category] extension/sevenx_themes_media/design/media/override/templates/node/view/full/full_ng_category.tpl  if {"class_identifier":"ng_category"}
  [node/view/full#ng_contact_form] extension/sevenx_themes_media/design/media/override/templates/node/view/full/full_ng_contact_form.tpl  if {"class_identifier":"ng_contact_form"}
```

The same script let the design resource itself pick the file for given keys (`eZTemplate::loadURIRoot()`, which
loads the template without rendering it):

```text
design:node/view/full.tpl with keys class_identifier=ng_article
  -> extension/sevenx_themes_media/design/media/override/templates/node/view/full/full_ng_article.tpl
design:node/view/full.tpl with keys class_identifier=folder
  -> extension/ezwebin/design/ezwebin/override/templates/full/folder.tpl
design:node/view/full.tpl with keys class_identifier=ng_article;remote_id=showcase-s1-tour
  -> extension/sevenx_alpha_settings/design/media/override/templates/node/view/full/showcase_s1_tour.tpl
```

The third case matches two rules; `showcase_s1_tour` comes first, so it wins over the class rule. The match keys
and their meaning are listed on [Override match keys](../designers/templates/override-match-keys.md), the ordering
rules on [Override ordering](../designers/templates/override-ordering.md).

## Who sets the keys

- `content/view` sets the node's keys (`object`, `node`, `parent_node`, `class`, `class_identifier`, `section`,
  `url_alias`, `depth`, `viewmode`, `state`, `persistent_variable` and more) before it fetches the view template
  (`eZNodeviewfunctions::generateNodeViewData()`), and again from `content_info` on a view cache hit.
- `node_view_gui`, `content_view_gui` and `attribute_view_gui` set the keys of the item they render
  (`kernel/common/ezobjectforwarder.php`), so an override of `node/view/line.tpl` can match the class of each child
  in a list.
- `layout/set` adds `layout`; error pages add `error_type` and `error_number`.
- A view or extension can add its own with `eZTemplateDesignResource::instance()->setKeys()`.

## Include and the view functions

- `{include uri='design:parts/menu.tpl'}` takes a full resource name and passes its other parameters as variables;
  it does not add keys, but the keys already set for the page apply to the included name too.
- The `*_view_gui` functions build the name themselves from their rule in `kernel/common/eztemplateautoload.php`:
  `node_view_gui` uses `node/view/<view>.tpl`, `attribute_view_gui` `content/datatype/view/<datatype>.tpl`, and each
  runs the template in a namespace of its own (`NodeView`, `ContentAttribute` ...).
- A missing file logs a warning and outputs nothing (see [Errors and debugging](errors-and-debugging.md)).

## Related pages

- [Override ini basics](../designers/templates/override-ini-basics.md)
- [Override match keys](../designers/templates/override-match-keys.md)
- [Override ordering](../designers/templates/override-ordering.md)
- [Include](../designers/templates/include.md)
- [Template caches](template-caches.md) (the override and design base caches)
- [Module view templates](module-view-templates.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
