---
title: The se7enxweb extension catalogue
audience: user, developer, designer, app-creator
category: extension-development
order: 80
summary: The 65 extensions the kernel's composer.json requires, grouped by purpose, with repository, license and documentation: the first place to look for ideas and examples.
keywords: catalogue, catalog, se7enxweb, extensions list, packages, composer require, github, layouts, datatypes, operators, designs, security, newsletter, ideas, examples
type: help
---
# The se7enxweb extension catalogue

Part 1, chapter 8. The kernel of Exponential does not come alone: its `composer.json` requires **66 packages of the
se7enxweb organization**, 65 extensions and the Composer installer that puts extensions into `extension/`. It
suggests a dozen more. This chapter lists them by what they do. For a developer they are two things at once: building
blocks you can use, and working, maintained examples of every kind of extension this book teaches.

The list was taken from the kernel's `composer.json` on the day this page was written. To see the current one:

```bash
grep -o '"se7enxweb/[^"]*"' composer.json | sort -u
```

Repositories are on GitHub under [github.com/se7enxweb](https://github.com/se7enxweb), packages on Packagist under
[packagist.org/packages/se7enxweb](https://packagist.org/packages/se7enxweb/). The license column is what GitHub
detects in each repository; where it says GPL-2.0, read the extension's own `LICENSE` and `ezinfo.php` for "or any
later version". Pages in the documentation exist for many of them; the column "Doc" links them.

## Page building: Exponential Layouts

A visual page builder ported to Exponential from the upstream layouts packages. Thirteen packages that show how a large
feature is split into a domain core, an API, a user interface and pluggable query handlers.

| Package | What it does | License | Doc |
|---|---|---|---|
| [explayouts](https://github.com/se7enxweb/explayouts) | Layouts, zones, blocks and rules: storage, resolver, renderer | GPL-2.0 | |
| [explayouts_core](https://github.com/se7enxweb/explayouts_core) | Core domain layer (layout, rule, collection, block and zone services) | GPL-2.0 | |
| [explayouts_api](https://github.com/se7enxweb/explayouts_api) | PHP API adapters between content and Layouts | GPL-2.0 | |
| [explayouts_standard](https://github.com/se7enxweb/explayouts_standard) | Standard block handlers (title, text, button, image ...) | GPL-2.0 | |
| [explayouts_ui](https://github.com/se7enxweb/explayouts_ui) | Administration user interface for Layouts | GPL-2.0 | [page](../../../features/6.0/extensions/explayouts_ui.md) |
| [explayouts_ui_api](https://github.com/se7enxweb/explayouts_ui_api) | JSON API and single page editor shell | GPL-2.0 | [page](../../../features/6.0/extensions/explayouts_ui_api.md) |
| [explayouts_site_api](https://github.com/se7enxweb/explayouts_site_api) | Bridge between Layouts and the site API | GPL-2.0 | |
| [explayouts_relation_list_query](https://github.com/se7enxweb/explayouts_relation_list_query) | Relation list collection query handler | GPL-2.0 | |
| [explayouts_tags_query](https://github.com/se7enxweb/explayouts_tags_query) | Tags collection query handler | GPL-2.0 | |
| [explayouts_content_browser](https://github.com/se7enxweb/explayouts_content_browser) | Content browser back end | GPL-2.0 | |
| [explayouts_content_browser_core](https://github.com/se7enxweb/explayouts_content_browser_core) | Content browser core services | GPL-2.0 | |
| [explayouts_content_browser_ui](https://github.com/se7enxweb/explayouts_content_browser_ui) | Server-rendered content browser user interface | GPL-2.0 | |
| [expquery_translator](https://github.com/se7enxweb/expquery_translator) | Search query parser and node filter, ported from a query translator library | GPL-2.0 | |

## Site framework

| Package | What it does | License | Doc |
|---|---|---|---|
| [expsite_api](https://github.com/se7enxweb/expsite_api) | Simple PHP services over the content model for site code | GPL-2.0 | |
| [expsite_core](https://github.com/se7enxweb/expsite_core) | Site helpers: breadcrumb path, redirect, mail | GPL-2.0 | |
| [expsite_app](https://github.com/se7enxweb/expsite_app) | A holder for one project's design overrides and customizations | GPL-2.0 | |
| [expsite_data_media](https://github.com/se7enxweb/expsite_data_media) | Site data for the media site | GPL-2.0 | |
| [expsite_installer](https://github.com/se7enxweb/expsite_installer) | One-command site bootstrap | GPL-2.0 | |

## Administration and editing

| Package | What it does | License | Doc |
|---|---|---|---|
| [exp_adminui](https://github.com/se7enxweb/exp_adminui) | An alternative administration interface (AdminUI) | GPL-2.0 | [feature](../../../features/6.0/exp-adminui.md) |
| [expui](https://github.com/se7enxweb/expui) | The JavaScript API of the admin: jQuery 4 and the `Exp` namespace, replacing YUI | GPL-2.0 | |
| [ezautosave](https://github.com/se7enxweb/ezautosave) | Saves drafts while editors type | GPL-2.0-or-later | [page](../../../features/6.0/extensions/ezautosave.md) |
| [ezie](https://github.com/se7enxweb/ezie) | Image editor inside the editing interface | GPL-2.0-or-later | [page](../../../features/6.0/extensions/ezie.md) |
| [ezmultiupload](https://github.com/se7enxweb/ezmultiupload) | Upload many files at once by drag and drop | GPL-2.0-or-later | [page](../../../features/6.0/extensions/ezmultiupload.md) |
| [ezwt](https://github.com/se7enxweb/ezwt) | The website toolbar: edit from the public site | GPL-2.0-or-later | [page](../../../features/6.0/extensions/ezwt.md) |
| [expchangeclass](https://github.com/se7enxweb/expchangeclass) | Change the content class of an object or a whole class | GPL-2.0 | |
| [ezownerchange](https://github.com/se7enxweb/ezownerchange) | Reassign the owner of content | GPL-2.0 | |
| [powercontent](https://github.com/se7enxweb/powercontent) | Content creation tools for templates and code | GPL-2.0 | |
| [sevenx_dse](https://github.com/se7enxweb/sevenx_dse) | Database source editor in the administration | GPL-2.0 | [page](../../../features/6.0/extensions/sevenx_dse.md) |
| [git_manager](https://github.com/se7enxweb/git_manager) | Git checkouts, branches and backups from the browser | GPL-2.0 | [page](../../../features/6.0/extensions/git_manager.md) |
| [ezupdate](https://github.com/se7enxweb/ezupdate) | Package manager: Composer, Packagist and package servers in the admin | GPL-2.0 | [page](../../../features/6.0/extensions/ezupdate.md) |

## New kinds of fields (datatypes)

| Package | What it does | License | Doc |
|---|---|---|---|
| [eztags](https://github.com/se7enxweb/eztags) | Taxonomy: tags as a datatype, with modules and fetch functions | GPL-2.0 | [page](../../../features/6.0/extensions/eztags.md) |
| [birthday](https://github.com/se7enxweb/birthday) | A date stored as YYYY-MM-DD | GPL-2.0-or-later | [page](../../../features/6.0/extensions/birthday.md) |
| [enhancedezbinaryfile](https://github.com/se7enxweb/enhancedezbinaryfile) | An extended file field | GPL-2.0-or-later | [page](../../../features/6.0/extensions/enhancedezbinaryfile.md) |
| [enhancedselection2](https://github.com/se7enxweb/enhancedselection2) | An extended selection field | GPL-2.0-only | [page](../../../features/6.0/extensions/enhancedselection2.md) |
| [exp_enhanced_link](https://github.com/se7enxweb/exp_enhanced_link) | A link field to content or URLs | GPL-2.0-or-later | [page](../../../features/6.0/extensions/exp_enhanced_link.md) |
| [ngclasslist](https://github.com/se7enxweb/ngclasslist) | A field that stores a list of content classes | GPL-2.0 | [page](../../../features/6.0/extensions/ngclasslist.md) |
| [ezgmaplocation](https://github.com/se7enxweb/ezgmaplocation) | A map location field | GPL-2.0-or-later | [page](../../../features/6.0/extensions/ezgmaplocation.md) |
| [ezstarrating](https://github.com/se7enxweb/ezstarrating) | Star ratings | GPL-2.0-or-later | [page](../../../features/6.0/extensions/ezstarrating.md) |

## Users and security

| Package | What it does | License | Doc |
|---|---|---|---|
| [sevenx_authentication_2fa](https://github.com/se7enxweb/sevenx_authentication_2fa) | Two-factor sign-in and social login handlers | GPL-2.0-or-later | [page](../../../features/6.0/extensions/sevenx_authentication_2fa.md) |
| [ezmbpaex](https://github.com/se7enxweb/ezmbpaex) | Password expiry and validation | GPL-2.0 | [page](../../../features/6.0/extensions/ezmbpaex.md) |
| [hcaptcha](https://github.com/se7enxweb/hcaptcha) | hCaptcha as a datatype | GPL-2.0 | [page](../../../features/6.0/extensions/hcaptcha.md) |
| [recaptcha](https://github.com/se7enxweb/recaptcha) | reCAPTCHA as a datatype | GPL-2.0 | [page](../../../features/6.0/extensions/recaptcha.md) |

## Search, SEO and statistics

| Package | What it does | License | Doc |
|---|---|---|---|
| [bcgooglesitemaps](https://github.com/se7enxweb/bcgooglesitemaps) | XML sitemaps | GPL-2.0-or-later | [page](../../../features/6.0/extensions/bcgooglesitemaps.md) |
| [bcwebsitestatistics](https://github.com/se7enxweb/bcwebsitestatistics) | Page view and order statistics | GPL-2.0-or-later | [page](../../../features/6.0/extensions/bcwebsitestatistics.md) |
| [xrowmetadata](https://github.com/se7enxweb/xrowmetadata) | Meta data and sitemaps for search engines | GPL-2.0-or-later | [page](../../../features/6.0/extensions/xrowmetadata.md) |

## Import, export and integration

| Package | What it does | License | Doc |
|---|---|---|---|
| [bccie](https://github.com/se7enxweb/bccie) | Export of collected information | GPL-2.0 | [page](../../../features/6.0/extensions/bccie.md) |
| [ezodf](https://github.com/se7enxweb/ezodf) | Import and export of office documents | GPL-2.0-or-later | [page](../../../features/6.0/extensions/ezodf.md) |
| [xrowextract](https://github.com/se7enxweb/xrowextract) | Content import and export (CSV, XML, JSON, packages) | GPL-2.0 | [page](../../../features/6.0/extensions/xrowextract.md) |
| [syndication](https://github.com/se7enxweb/syndication) | Content synchronization between installations | GPL-2.0 | [page](../../../features/6.0/extensions/syndication.md) |
| [ezprestapi](https://github.com/se7enxweb/ezprestapi) | REST API v2 content provider | GPL-2.0 | [page](../../../features/6.0/extensions/ezprestapi.md) |
| [cjw_tmv](https://github.com/se7enxweb/cjw_tmv) | Tools for an external web service integration | GPL-2.0 | |
| [expqueue](https://github.com/se7enxweb/expqueue) | A database-backed job queue | GPL-2.0-or-later | [page](../../../features/6.0/extensions/expqueue.md) |

## Communication, commerce, intelligence, databases

| Package | What it does | License | Doc |
|---|---|---|---|
| [cjw_newsletter](https://github.com/se7enxweb/cjw_newsletter) | Newsletters | GPL-2.0 | [page](../../../features/6.0/extensions/cjw_newsletter.md) |
| [ezpaypal](https://github.com/se7enxweb/ezpaypal) | PayPal payment gateway for the shop | GPL-2.0 | [page](../../../features/6.0/extensions/ezpaypal.md) |
| [expai](https://github.com/se7enxweb/expai) | Exponential Intelligence: AI-assisted content creation | GPL-2.0-or-later | [page](../../../features/6.0/extensions/expai.md) |
| [ezoracle](https://github.com/se7enxweb/ezoracle) | Oracle database driver | GPL-2.0-or-later | |

## Template tools

| Package | What it does | License | Doc |
|---|---|---|---|
| [owsimpleoperator](https://github.com/se7enxweb/owsimpleoperator) | Write template operators as plain methods; expose permitted PHP functions | GPL-3.0-or-later | [page](../../../features/6.0/extensions/owsimpleoperator.md) |
| [swark](https://github.com/se7enxweb/swark) | Many template operators and a few workflow events | GPL-2.0-or-later | [page](../../../features/6.0/extensions/swark.md) |
| [ezurlmultisite](https://github.com/se7enxweb/ezurlmultisite) | URL operators for links across sites | GPL-2.0 | |

## Designs and site packages

| Package | What it does | License | Doc |
|---|---|---|---|
| [sevenx_themes_simple](https://github.com/se7enxweb/sevenx_themes_simple) | The default design theme "Simple" | GPL-2.0 | [page](../../../features/6.0/extensions/sevenx_themes_simple.md) |
| [sevenx_themes_media](https://github.com/se7enxweb/sevenx_themes_media) | The media site theme | GPL-2.0 | |
| [cjw_themes_jumper](https://github.com/se7enxweb/cjw_themes_jumper) | A converted media-site design theme | GPL-2.0 | |
| [ezwebin](https://github.com/se7enxweb/ezwebin) | The classic website interface design | GPL-2.0-or-later | [page](../../../features/6.0/extensions/ezwebin.md) |
| [ezflow](https://github.com/se7enxweb/ezflow) | Page blocks and zones, the older page builder | GPL-2.0-or-later | [page](../../../features/6.0/extensions/ezflow.md) |
| [ezdemo](https://github.com/se7enxweb/ezdemo) | The demo site design | GPL-2.0-or-later | [page](../../../features/6.0/extensions/ezdemo.md) |

## The installer

| Package | What it does |
|---|---|
| [exponential-legacy-installer](https://github.com/se7enxweb/exponential-legacy-installer) | The Composer installer for packages of type `ezpublish-legacy-extension`: it puts each one into `extension/<name>` |

## Suggested, not required

The kernel's `composer.json` also suggests (installs only when you ask) these extensions, among others:
`ezfind` (Solr search), `ezauthorize` (payment gateway), `ezsi` (edge side includes), `ezscriptmonitor` (long
running scripts), `bcurlaliaswithdash`, `sevenx_valkey` and `sevenx_valkey_cache` (Valkey and Redis), `adminaid`,
`ezpm` (private messages). Read the `suggest` block of `composer.json` for the current list and one line on each.

Extensions that ship *inside* the kernel's own repository are not in either list: `ezjscore`, `ezoe`, `ezformtoken`
and `expservices`. They are always there.

## How to use this catalogue as a developer

| You want to learn | Read |
|---|---|
| A small template operator | `str_replace` (in the kernel tree), then `owsimpleoperator` |
| A datatype | `birthday` (small), then `eztags` (large) |
| A module with views and policies | `ezownerchange`, `expchangeclass` |
| Workflow events | `swark` (`eventtypes/event/`) |
| ezjscore ajax functions | `ezstarrating`, `ezautosave` |
| A design extension | `sevenx_themes_simple` |
| A large feature split into packages | the Layouts packages |
| Modern JavaScript without inline scripts | `expui` |

[Learning from an extension, step by step](learning-from-an-extension-step-by-step.md) takes two of them apart.

## Related pages

- Previous: [Finding the extensions installed on your site](finding-the-extensions-on-your-site.md)
- Next: [Finding ideas on GitHub](finding-ideas-on-github.md)
- [The extension catalogue in the documentation](../../../features/6.0/extensions/README.md)
- [What a new installation comes with](../../../features/6.0/default-extension-distribution.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
