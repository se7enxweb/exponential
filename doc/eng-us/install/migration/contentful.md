---
title: Contentful and Exponential
audience: admin, developer, project-manager
category: install-upgrade
order: 46
summary: Moving content between Contentful and Exponential 6 - contentful space export and import, the Content Delivery API, what the export leaves out, mapping content types, entries, assets, locales and rich text, and keeping a headless front end while the content source changes.
keywords: Contentful, contentful-cli, space export, space import, Content Delivery API, Content Management API, entries, assets, locales, rich text, headless, migration
type: install
---
# Contentful and Exponential

Contentful is a headless content platform: it stores content and serves it over APIs, and a separate front end
renders the site. Moving to Exponential either replaces both (Exponential renders the site) or replaces only the
content source (the front end reads Exponential instead, see [JavaScript and Node.js apps](javascript-apps.md)).

[Migration hub](README.md) · [Checklist](checklist.md) · [Mapping](content-model-mapping.md) · [Tools](exponential-tools.md)

## 1. What Contentful can export

According to Contentful's documentation and the READMEs of its official CLI tools:

| Tool | What it gives you | Limits |
|---|---|---|
| **`contentful space export`** (Contentful CLI) | one JSON file with content types, entries, assets, locales, tags, webhooks, roles, editor interfaces, releases; options `--space-id`, `--management-token`, `--export-dir`, `--content-file`, `--download-assets`, `--include-drafts`, `--skip-content`, `--skip-roles`, `--skip-webhooks`, `--content-model-only` | no space memberships; webhook credentials must be re-added by hand; only active releases |
| **Content Delivery API** | published entries and assets as JSON, `skip`/`limit`/`total`/`items`; `limit` default 100, maximum 1000; cursor pagination is also documented | published content only |
| **Content Management API** | read and write access including drafts | used by the CLI |

`contentful space export --download-assets --include-drafts` gives you everything a migration needs in one run.

## 2. Mapping to Exponential

| Contentful | Exponential 6 |
|---|---|
| Content type with fields | class with attributes (Symbol/Text to `ezstring`/`eztext`, Integer/Number, Boolean, Date to `ezdatetime`, Location to `ezgmaplocation`, JSON object to `eztext` or a decision) |
| Entry | object; `sys.id` as remote id (`contentful-<id>`) |
| Reference / references | `ezobjectrelation` / `ezobjectrelationlist` |
| Asset | image/file object |
| Rich Text (a JSON document of nodes) | `ezxmltext`: convert the node tree (paragraphs, headings, lists, hyperlinks, embedded entries and assets) to the simplified input |
| Locales | translations of the same object; fallback locales to Exponential's language fallbacks |
| Tags | `eztags` |
| Slug field | the node's URL alias |
| No tree | Contentful is flat: decide the content tree (by type, by a parent reference, or by the front end's routes) |

## 3. Step by step

1. `contentful space export --space-id <id> --management-token <token> --download-assets --include-drafts --export-dir <dir>`.
2. Design the tree and the classes from the content types; write the mapping table.
3. Convert the JSON: entries per type with their fields per locale, Rich Text to XmlText, references to remote ids.
4. Import assets, then entries without references, then a second pass for references; translations per locale.
5. If a headless front end stays: point it at Exponential's REST API or `expservices`
   ([JavaScript and Node.js apps](javascript-apps.md)); otherwise build TPL templates.
6. Redirects from the front end's routes ([checklist, 4](checklist.md#4-urls-and-redirects)).

## 4. From Exponential to Contentful

Export with xrowextract ([leaving Exponential](leaving-exponential.md)), build the content model in Contentful, and
write a `contentful space import` file (or use the Management API). Contentful's own notes: the source and target
need the same content model and default locale, `space import` does not do content transformations ("please use
contentful-migration"), roles and memberships are not imported, and imported content shows the token owner as author.
Redirects belong to the front end or its host.

## 5. Checklist

- [ ] Space exported with assets and drafts.
- [ ] Tree designed; classes created; Rich Text converter tested.
- [ ] Assets, entries, references, locales imported with `contentful-<id>` remote ids.
- [ ] Front end repointed or templates built; redirects tested.

## Sources

- Contentful export tool: <https://github.com/contentful/contentful-export>
- Contentful import tool: <https://github.com/contentful/contentful-import>
- CLI import and export tutorial: <https://www.contentful.com/developers/docs/tutorials/cli/import-and-export/>

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../help/partners/partners-start-here.md) and [How to apply](../../help/partners/how-to-apply.md).
<!-- exp-partner-block:end -->

This page is part of the Exponential documentation and is free documentation under the GNU Free Documentation License
1.3 or any later version ([the license of this documentation](../../README-LICENSE.md)).
