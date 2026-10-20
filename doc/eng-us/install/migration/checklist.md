---
title: The common migration checklist
audience: admin, developer, project-manager
category: install-upgrade
order: 6
summary: The steps every migration to, within or away from Exponential shares - inventory, freeze, content mapping, URLs and redirects, search engine metadata, rehearsals, testing, cut-over, rollback and sign-off - as checklists for the project manager and the team.
keywords: migration checklist, inventory, content freeze, mapping, redirects, 301, SEO, rehearsal, cutover, go-live, rollback, sign-off, acceptance
type: install
---
# The common migration checklist

Every migration on these pages follows the same phases. The path guides add what is specific to one source or target;
this page is the frame they all fit into. Copy the checklists into your project's tracker and tick them off.

[Migration hub](README.md) · Tools: [Exponential's import and export tools](exponential-tools.md) · Mapping: [Mapping content to Exponential](content-model-mapping.md)

## Contents

- [1. Inventory: know what you have](#1-inventory-know-what-you-have)
- [2. Decide the scope and the owner of each part](#2-decide-the-scope-and-the-owner-of-each-part)
- [3. Map the content model](#3-map-the-content-model)
- [4. URLs and redirects](#4-urls-and-redirects)
- [5. Search engine metadata](#5-search-engine-metadata)
- [6. Build the import and rehearse it](#6-build-the-import-and-rehearse-it)
- [7. Test](#7-test)
- [8. Cut-over](#8-cut-over)
- [9. Rollback](#9-rollback)
- [10. After go-live](#10-after-go-live)
- [11. Risks that recur](#11-risks-that-recur)

## 1. Inventory: know what you have

- [ ] **Every address of the old site.** Crawl it (a sitemap alone misses orphan pages), export the server's access
      log of the last months, and list the addresses that search engines and other sites link to. This list drives the
      redirects in [4](#4-urls-and-redirects).
- [ ] **Every content type** and how many items of each, in each language.
- [ ] **Media**: images, documents, videos; where they are stored; total size; which are still referenced.
- [ ] **Users and groups**: how many, which are editors, which are site members; whether passwords can move (they
      usually cannot, see [3](#3-map-the-content-model)).
- [ ] **Forms, comments, ratings, shop orders, newsletters**: data that is not "content" but has to go somewhere, or
      be archived.
- [ ] **Integrations**: single sign-on, search, analytics, payment, mail, feeds, APIs that other systems call.
- [ ] **Custom code**: plugins, modules, extensions, templates, scheduled jobs.
- [ ] **Legal and retention**: what must be kept, what must not be moved (personal data you no longer need is best
      left behind), who signs off.

## 2. Decide the scope and the owner of each part

| Part | Decide | Typical owner |
|---|---|---|
| Content to move | all, or a cut-off date, or only what still has traffic | content owner, editors |
| Design | carry over, refresh, or new | design, marketing |
| Addresses | keep identical, or new structure with redirects | SEO, developer |
| Users | move accounts, or ask users to register again | administrator, data protection |
| Features | which old features are needed on day one | project manager |
| Freeze | from when editors stop changing the old site, and how late changes are caught up | project manager, editors |

## 3. Map the content model

Write the mapping down as a table per content type before writing any import code. The columns that matter:

| Source type | Source field | Exponential class | Attribute (identifier) | Datatype | Conversion | Notes |
|---|---|---|---|---|---|---|
| e.g. Post | `post_title` | `article` | `title` | `ezstring` | none | required |
| | `post_content` | `article` | `body` | `ezxmltext` | HTML to XmlText | images become embeds |
| | categories | location | parent node | | one main location, others as extra locations | |

- [ ] One class per source type, or fewer classes with a type selection: decide per type.
- [ ] Every field mapped to an attribute or consciously dropped.
- [ ] Rich text: decide how HTML becomes XmlText (or RichText on the Symfony platform) and what happens to inline
      styles, scripts and iframes (dropped, or turned into custom tags or embeds).
- [ ] Taxonomy: locations (a tree), keywords (`ezkeyword`), tags (`eztags`), or relations.
- [ ] Media: a separate image/file object per asset, related or embedded, so it can be reused.
- [ ] Languages: one object with translations, not one object per language.
- [ ] Users: the password hash types of the source are rarely readable by the target. Plan a reset (a mail with a
      link) unless the guide of your path says the hashes carry over.
- [ ] Every object gets a **remote id** that names its source (`wp-post-1234`), so re-running the import updates
      instead of duplicating, and so redirects can be generated from it.

The detail per concept is in [Mapping content to Exponential](content-model-mapping.md).

## 4. URLs and redirects

- [ ] For every old address, decide: same address on the new site, a 301 redirect to its new address, or a
      deliberate 410/404 (gone).
- [ ] Generate the redirect table from the import: remote id to new node, old address to new address.
- [ ] In Exponential, keep an old address in one of three ways (all documented in
      [URLs and aliases](../../guides/urls-and-aliases.md)):
  - **The node's own URL alias**: when the old path can be the new path (`about-us/team`). Set the object's name or
    a custom alias so the alias matches.
  - **A custom URL alias on the node** that redirects (301) to the node's main address: one per old address that
    differs.
  - **A URL wildcard** (`news/*` to `articles/{1}`) for whole groups that changed in a regular way.
- [ ] Addresses with query strings (`?p=123`, `index.php?id=5`) or file extensions the alias system cleans: handle
      them in the web server or a small redirect module before Exponential, from the same table.
- [ ] Test every redirect automatically (status and destination), not by sampling.
- [ ] Keep redirects for at least a year; search engines and old links need them that long.

## 5. Search engine metadata

- [ ] Titles and meta descriptions: map them to attributes. `xrowmetadata` (required in `composer.json`) adds a
      metadata datatype with title, keywords and description, and an XML sitemap cronjob.
- [ ] Canonical addresses, `hreflang` for translations, `robots` rules: reproduce in the pagelayout.
- [ ] An XML sitemap on the new site (`xrowmetadata` or `bcgooglesitemaps`), submitted after go-live.
- [ ] Structured data (JSON-LD) the old site had: list it and rebuild it in templates.
- [ ] Analytics and verification tags.

## 6. Build the import and rehearse it

- [ ] Use the tools that exist first ([Exponential's import and export tools](exponential-tools.md)); write a script
      only for what they cannot do.
- [ ] Make the import **repeatable**: it reads a frozen export, matches on remote id, and updates what is already
      there.
- [ ] Log every item: created, updated, skipped (and why).
- [ ] Rehearse on a copy of the target at least twice. Time each run; the real cut-over window follows from it.
- [ ] Count after each run: items per type and language, media files, users, redirects. Compare with the inventory.

## 7. Test

| Test | How |
|---|---|
| Counts | per type and language, against the inventory |
| Samples | the 50 most visited pages side by side, old and new |
| Rich text | headings, lists, tables, links, images, embedded media in a dozen complex items |
| Links | the link checker of the new site ([URLs and aliases](../../guides/urls-and-aliases.md#2-how-links-are-checked)) and `./console exp:contentcheck` for broken relations and missing files |
| Redirects | every row of the redirect table, automatically |
| Search | the site search finds new items; the search index is rebuilt (`./console exp:updatesearchindex`) |
| Permissions | editors can edit, anonymous visitors cannot see unpublished items |
| Performance | the main page types under load ([benchmarking](../../guides/benchmarking.md)) |
| Accessibility | the templates, with an automatic checker and by keyboard |

## 8. Cut-over

- [ ] Announce the freeze to editors; stop changes on the old site.
- [ ] Final export, final import, counts, redirects test.
- [ ] Lower the DNS time to live a day before; switch DNS or the proxy.
- [ ] Clear caches and warm the most visited pages (`./console exp:warm`, `./console exp:preload`).
- [ ] Watch the logs (`./console exp:explogs`) and the 404 list for the first hours; add redirects for what appears.
- [ ] Submit the sitemap; check the search console of each search engine for crawl errors.

## 9. Rollback

- [ ] Keep the old site running, read-only, until sign-off. Rollback is switching DNS or the proxy back.
- [ ] Keep a backup of the target taken just before the final import, so a failed final import can be repeated.
- [ ] Decide in advance what triggers a rollback (for example: more than a set share of key pages broken, sign-in
      not working) and who decides.
- [ ] Changes made on the new site after go-live are lost on a rollback unless they are recorded; keep the window
      short.

## 10. After go-live

- [ ] 404 report daily for two weeks, then weekly.
- [ ] Search ranking and traffic compared with before.
- [ ] Old system archived (export plus database dump), then switched off at the agreed date.
- [ ] Documentation of what was mapped, for the next migration.

## 11. Risks that recur

| Risk | Sign | Counter-measure |
|---|---|---|
| Lost addresses | traffic drops, 404s | a complete address inventory and automatic redirect tests |
| Broken rich text | odd markup, lost images | a conversion that is tested on the worst items first |
| Duplicates after re-runs | the same item twice | remote ids and update-on-match |
| Users locked out | sign-in failures | plan password resets and tell users before |
| Late content changes lost | editors complain | a short freeze, and a delta import for the last days |
| Underestimated templates | design not ready | start templates early, against the first rehearsal's content |
| Personal data moved without need | | move only what you need; document the legal basis |

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../help/partners/partners-start-here.md) and [How to apply](../../help/partners/how-to-apply.md).
<!-- exp-partner-block:end -->

This page is part of the Exponential documentation and is free documentation under the GNU Free Documentation License
1.3 or any later version ([the license of this documentation](../../README-LICENSE.md)).
