---
title: Adobe Experience Manager and Exponential
audience: admin, developer, project-manager
category: install-upgrade
order: 47
summary: Moving content between Adobe Experience Manager and Exponential 6 - content packages, the Sling GET servlet JSON, the Assets HTTP API, Content Fragments over GraphQL, mapping pages, components, assets and fragments, vanity URLs and redirects, and what has to be rebuilt.
keywords: Adobe Experience Manager, AEM, content package, Package Manager, Sling, .json, infinity, Assets HTTP API, Content Fragments, GraphQL, JCR, components, DAM, migration
type: install
---
# Adobe Experience Manager and Exponential

AEM stores content in a JCR repository: pages are trees of nodes with components, assets live in the DAM, and
structured content can be Content Fragments. All of it can be read, but the component structure of pages is
specific to each AEM project and needs its own mapping.

[Migration hub](README.md) · [Checklist](checklist.md) · [Mapping](content-model-mapping.md) · [Tools](exponential-tools.md)

## 1. What AEM can export

According to Adobe's and Apache Sling's documentation:

| Source | What it gives you | Notes |
|---|---|---|
| **Package Manager content packages** | a ZIP of repository content in "vault serialization", defined by filters | the right tool for a full, frozen copy; Adobe's guideline is that a package should transfer in about 60 seconds, so split large trees |
| **Sling default GET servlet** | any node as JSON (`<path>.json`, with a depth selector or `.infinity`) | results capped by `json.maximumresults` (1000 by default in AEM); often closed on publish instances |
| **Assets HTTP API** (`/api/assets`) | folders, asset metadata, renditions | returns only a subset of metadata; binary upload calls are deprecated on Cloud Service |
| **Content Fragments** | the AEM GraphQL API (persisted queries recommended); Adobe recommends the Content Fragment Management OpenAPI over the Assets API for fragments | |

Authentication details of the Assets API were not verified for this page. Templates, designs and component code are
not content: they are rebuilt.

## 2. Mapping to Exponential

| AEM | Exponential 6 |
|---|---|
| Page (`cq:Page` with `jcr:content`) | object in the tree; the page path is the tree |
| Page properties (title, description, tags, vanity URL) | `title`, `xrowmetadata`, `eztags`, custom URL alias |
| Text components (rich text HTML) | `ezxmltext` (converted) |
| Image components (DAM references) | embeds or relations to image objects |
| Other components (teasers, lists, carousels) | Exponential Layouts blocks, or attributes and child objects |
| Experience Fragments | shared layouts or included objects |
| Content Fragment models and fragments | classes and objects |
| DAM assets and metadata | image/file/media objects with their metadata |
| Language copies (`/content/site/en`, `/de`) | translations of the same object (match the copies by a key) |
| Tags (`cq:tags`) | `eztags` |

## 3. Step by step

1. Inventory the templates and components in use per site section (component resource types and counts).
2. Export a frozen copy (content packages per section) or read through the JSON servlet on an author copy.
3. Write one extractor per component type; images become references to imported assets.
4. Import assets with metadata, then pages per language, with remote ids from the JCR path or `jcr:uuid`.
5. Rebuild page structures as Exponential Layouts or templates; components without a counterpart get a decision.
6. Redirects from page paths, `.html` addresses and vanity URLs ([checklist, 4](checklist.md#4-urls-and-redirects)).

## 4. From Exponential to AEM

Export with xrowextract ([leaving Exponential](leaving-exponential.md)); create pages and fragments through AEM's
own APIs or a content package built from the export, and assets through the DAM's upload. Redirect handling on the
AEM side (vanity URLs, dispatcher rules) was not verified for this page.

## 5. Checklist

- [ ] Components inventoried; one extractor per component type.
- [ ] Assets with metadata and renditions copied; pages per language extracted.
- [ ] Imported with remote ids; language copies attached as translations.
- [ ] Page structures rebuilt; vanity URLs and `.html` paths redirected.

## Sources

- Assets HTTP API: <https://experienceleague.adobe.com/en/docs/experience-manager-cloud-service/content/assets/admin/mac-api-assets>
- Package Manager: <https://experienceleague.adobe.com/en/docs/experience-manager-cloud-service/content/implementing/developer-tools/package-manager>
- Sling default GET servlets: <https://sling.apache.org/documentation/bundles/rendering-content-default-get-servlets.html>

This page is part of the Exponential documentation and is free documentation under the GNU Free Documentation License
1.3 or any later version ([the license of this documentation](../../README-LICENSE.md)).
