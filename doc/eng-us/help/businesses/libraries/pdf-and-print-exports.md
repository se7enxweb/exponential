---
title: PDF and print exports
audience: businesses, admin, editor
category: business-solutions
order: 60
summary: How a library or archive turns part of its catalog into a PDF with Exponential's PDF export - what an export contains, generated once or on the fly, which stored PDFs are public, footers and fonts for any language, and good uses such as acquisition lists and collection guides.
keywords: PDF export, printed catalog, acquisition list, collection guide, handbook, pdf/list, generated once, on the fly, footer text, TrueType font, print, library
type: help
---
# PDF and print exports

This page is for library and archive staff who want a printable or downloadable version of part of the catalog: a list
of new acquisitions, a guide to a collection, a reading list. It explains what Exponential's PDF export does, the one
choice that matters most (stored or on the fly), and what becomes public. The full guide for administrators is
[PDF exports](../../../guides/pdf-exports.md).

> **Warning**
> **`.htaccess` slows down every request.** When `.htaccess` files are enabled (`AllowOverride` anything but `None`), let alone
> used, Apache looks for them and reads and evaluates them on every request, in every directory of the path, and
> page loading times go up drastically ([measured](../../../guides/setup-files-from-the-console.md#measured-htaccess-against-rules-in-the-virtual-host)). The only real fix is to switch the feature off for
> the virtual host: `AllowOverride None` in the HTTPS virtual host, and the rules in the virtual host's own
> configuration as mod_rewrite rules. That is far faster in production speed tests and well worth the time it takes
> to optimize a production configuration. Switch over right away: copy the rules of the
> [example HTTPS virtual host](../../../examples/web-servers/apache-virtual-host.md) into your virtual host (with the static cache, also
> [`staticcache_offload.conf`](../../../examples/staticcache_offload.conf)), set `AllowOverride None`, run `apachectl configtest`, reload Apache and
> test the site. The same rules for other servers: [nginx](../../../examples/web-servers/nginx.md), [LiteSpeed](../../../examples/web-servers/litespeed.md),
> [Caddy](../../../examples/web-servers/caddy.md), [IIS](../../../examples/web-servers/iis.md), [Velocity](../../../examples/web-servers/velocity.md) (built in); where virtual host rules go in a control
> panel: [Plesk](../../hosting/panels/plesk.md), [cPanel](../../hosting/panels/cpanel.md), [DirectAdmin](../../hosting/panels/directadmin.md), [ISPConfig](../../hosting/panels/ispconfig.md).
> **On LiteSpeed or OpenLiteSpeed,** the server's own page cache can also answer anonymous visitors and crawlers without PHP: [Page caching with LiteSpeed and OpenLiteSpeed](../../../guides/litespeed-page-cache.md).

## Contents

1. [What a PDF export is](#what-a-pdf-export-is)
2. [Good uses in a library](#good-uses-in-a-library)
3. [Generated once or on the fly](#generated-once-or-on-the-fly)
4. [What is public](#what-is-public)
5. [Making an export](#making-an-export)
6. [Footer, front page and fonts](#footer-front-page-and-fonts)
7. [When the PDF is empty or wrong](#when-the-pdf-is-empty-or-wrong)
8. [Other ways to print](#other-ways-to-print)
9. [Export checklist](#export-checklist)

## What a PDF export is

"A **PDF export** makes one PDF of a **source node**: its name as the first heading, then its attributes. A **tree**
export adds the nodes below it, at every level, but only those of the **classes** you tick"
([PDF exports, In short](../../../guides/pdf-exports.md#in-short)). The pages are **Setup > PDF export**
(`/pdf/list`, policy `pdf/edit`) ([PDF export](../../../features/6.0/pdf-export.md)).

## Good uses in a library

| Use | Source node | Classes to tick |
|---|---|---|
| A collection guide | The collection (a container) | The collection class and the record classes |
| A list of this month's acquisitions | A folder that holds them, or a monthly folder | Folder and record classes |
| A reading list | A folder of related records | Folder and record classes |
| A handbook for volunteers or staff | A documentation folder | Folder and documentation page classes |

"A node of an unticked class is left out *with everything below it*, so tick the folders that hold the content as
well as the articles" ([PDF exports, Creating or editing an export](../../../guides/pdf-exports.md#3-creating-or-editing-an-export)).

## Generated once or on the fly

| | Generated once | Generated on the fly |
|---|---|---|
| When the PDF is made | When the export is saved, and again with **Regenerate** | Anew for every download |
| Content | As it was then | Always current |
| Speed | Fast to download | A large tree takes a while every time |
| Stored file | `var/<site>/storage/pdf/<file name>` | None |
| Who can download | Anybody with the public address | Users with the `pdf/edit` policy, through `/pdf/edit/<id>/generate` |

Source: [PDF exports, In short and Opening the page](../../../guides/pdf-exports.md#1-opening-the-page).

## What is public

> **Warning**
> "A stored PDF is public: the web server serves `var/<site>/storage/pdf/<file name>` to anybody, signed in or not."
> Use an export generated on the fly "for content that is not for everybody", and "do not give an export a file name
> you would not want guessed" ([PDF exports](../../../guides/pdf-exports.md#1-opening-the-page)).

So a stored PDF suits a public acquisitions list; a list that includes restricted records should be generated on the
fly, or should not include those records. The public path is opened by one rewrite rule in the shipped `.htaccess`
files; a server that keeps its rules elsewhere needs the same path there (same page). Nothing else in storage becomes
public: other uploads stay behind `content/download` and its permissions.

## Making an export

1. **Setup > PDF export > New PDF export.**
2. **Title**: the name in the list (not printed in the PDF).
3. **Content**: choose the source node with **Browse**; choose **Node** or **Tree**, and for a tree tick the classes.
4. **Frontpage**: an optional first page with a large and a smaller line of text.
5. **Footer text**: the line beside the page number.
6. **Export type** and **File name**: generate once or on the fly; letters, digits, `.`, `_` and `-`.
7. **OK**, then **Download** from the list to check the result.

Source: [PDF exports, Creating or editing an export](../../../guides/pdf-exports.md#3-creating-or-editing-an-export).
For a monthly list generated once, press **Regenerate** after the month's records are published, or schedule that as a
routine task for a named person.

## Footer, front page and fonts

- **Footer**: tick **Show footer** and type your wording, for example the library's name; every page carries it
  instead of "Exponential PDF export" ([PDF export](../../../features/6.0/pdf-export.md#make-an-export-with-your-own-footer)).
- **Any language**: "The bundled fonts hold one single-byte character set. For Unicode text, point `Font` at a
  `.ttf` file" ([PDF export, Print any language](../../../features/6.0/pdf-export.md#print-any-language-use-a-truetype-font)).
  A catalog with titles in several scripts needs this setting.

## When the PDF is empty or wrong

| Symptom | Likely cause |
|---|---|
| The PDF has only the first page of content | A tree without classes, or the folders between the source and the records not ticked |
| Records are missing | Their folder's class was not ticked, so the folder was skipped with everything below it |
| A warning "Source missing" | The source node was removed; choose another |
| Wrong characters | No TrueType font configured for Unicode text |
| Old content | A stored export not regenerated since the change: press Regenerate, or switch to on the fly |
| A large tree times out on the fly | Switch it to generate once and regenerate it outside busy hours |

Sources: the warnings of the list in [PDF exports, The list](../../../guides/pdf-exports.md#2-the-list) and its
[Problems](../../../guides/pdf-exports.md#5-problems) section.

## Other ways to print

- Readers can print any page from their browser; a print stylesheet in your design controls how it looks
  ([Stylesheets and scripts](../../creators/website-creators/stylesheets-and-scripts.md)).
- Documents written in Writer can be exported back to Writer with `ezodf` ([ezodf](../../../features/6.0/extensions/ezodf.md)).
- A feed of new records serves readers who prefer a feed reader ([Feeds for new acquisitions](feeds-for-new-acquisitions.md)).

## Export checklist

- [ ] Each export has a clear title, source node and, for a tree, the right classes ticked (folders included).
- [ ] Stored exports contain only public material and have file names you are happy to be guessed.
- [ ] Restricted material only in exports generated on the fly, or not at all.
- [ ] Footer wording set; a TrueType font configured if titles use non-Latin scripts.
- [ ] A named person regenerates stored exports on a schedule.
- [ ] The public PDF address tested while logged out.

## Related pages

- [Libraries and catalogs](README.md)
- [PDF exports (guide)](../../../guides/pdf-exports.md)
- [PDF export (feature)](../../../features/6.0/pdf-export.md)
- [Files and digital collections](files-and-digital-collections.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
