---
title: Drupal and Exponential
audience: admin, developer, project-manager
category: install-upgrade
order: 41
summary: Moving a Drupal site to Exponential 6 and back - JSON:API, the Migrate API and Views Data Export, mapping content types, fields, taxonomy, media, paragraphs, users and path aliases, redirects, and the way back through Drupal's Migrate API.
keywords: Drupal, JSON:API, Migrate API, migrate_drupal, Drush, Views Data Export, content types, paragraphs, taxonomy, media, path alias, pathauto, redirect, migration
type: install
---
# Drupal and Exponential

[Migration hub](README.md) · [Checklist](checklist.md) · [Mapping](content-model-mapping.md) · [Tools](exponential-tools.md)

## Contents

- [1. What Drupal can export](#1-what-drupal-can-export)
- [2. Mapping Drupal to Exponential](#2-mapping-drupal-to-exponential)
- [3. From Drupal to Exponential, step by step](#3-from-drupal-to-exponential-step-by-step)
- [4. Addresses and redirects](#4-addresses-and-redirects)
- [5. From Exponential to Drupal](#5-from-exponential-to-drupal)
- [6. Risks and checklist](#6-risks-and-checklist)
- [Sources](#sources)

## 1. What Drupal can export

Drupal has no single "export the site" file. According to drupal.org:

| Tool | What it gives you | Notes |
|---|---|---|
| **JSON:API** (core module) | every entity at `/jsonapi/{entity_type_id}/{bundle_id}[/{uuid}]`, media type `application/vnd.api+json` | read-only by default; at most 50 resources per request, follow the `next` link; a page can hold fewer items because of access checks |
| **Migrate API** (core `migrate`, `migrate_drupal`; Drush `migrate:*` commands) | a framework for moving content *into* Drupal; its source plugins read older Drupal databases | useful in the other direction ([5](#5-from-exponential-to-drupal)) |
| **Views Data Export** (contributed module) | CSV, XLS, JSON or XML from any view, in batches | needs a sort on a unique id in batch mode |
| The database | everything, in Drupal's entity and field tables (`node_field_data`, `node__<field>` ...) | reading it directly is possible but couples you to the schema version |
| `sites/default/files` | the managed files | copy it |

JSON:API is the most practical source: it delivers each content type with its fields, relationships (taxonomy,
media, authors) and the path alias, in a documented format, without writing Drupal code.

## 2. Mapping Drupal to Exponential

| Drupal | Exponential 6 |
|---|---|
| Content type (node bundle) | class |
| Field | attribute (text to `ezstring`/`eztext`, formatted text to `ezxmltext`, image to `ezimage` or a relation, entity reference to `ezobjectrelationlist`, list to `ezselection`, date to `ezdatetime`, link to `ezurl`) |
| Body (text with summary) | `body` and `intro` (`ezxmltext`), HTML converted |
| Paragraphs (contributed) | child objects of small classes, or Exponential Layouts blocks for landing pages |
| Taxonomy vocabulary and terms | folders, or `eztags` (hierarchy, synonyms) |
| Media entities and files | image/file/media objects below media folders |
| Menu (menu links) | the content tree and sort order |
| Block content | layout blocks, or objects shown by templates |
| Users, roles | user objects in groups; roles rebuilt. Drupal's password hashes are its own format; plan resets |
| Path alias (`/about-us`) | the node's URL alias, or a redirecting custom alias |
| Language and translations | translations of the same object |
| Revisions | not imported; the current revision becomes version 1 |
| Webform submissions | archived; forms rebuilt with information collection |
| Metatag (contributed) | `xrowmetadata` |

## 3. From Drupal to Exponential, step by step

1. **Inventory** content types and fields (Structure > Content types > Manage fields), vocabularies, media types,
   languages, contributed modules that store content (Paragraphs, Webform, Metatag, Redirect).
2. **Enable JSON:API** on a copy of the site, read-only (its default), and an account with read access to everything
   you migrate.
3. **Create the classes** and the tree in Exponential.
4. **Export**: a script pages through `/jsonapi/node/<bundle>` for each type (with `include=` for the relationships
   you need), then taxonomy, media, files and users, and writes JSON with remote ids such as `drupal-node-<uuid>`.
   Copy `sites/default/files`.
5. **Import** in dependency order: files and media, taxonomy, then nodes, then translations; relations resolved by
   remote id. xrowextract's importer with `--map`, or the [script skeleton](exponential-tools.md#4-a-custom-import-script-the-skeleton).
6. **Redirects** from the path aliases and the Redirect module's table ([4](#4-addresses-and-redirects)).
7. Rebuild search and aliases, test, rehearse, cut over ([checklist](checklist.md)).

## 4. Addresses and redirects

- **Path aliases** (`path_alias` entities, exported with each node as its `path` field) become node aliases when the
  tree matches, or redirecting custom aliases otherwise.
- **`/node/123`**, the system path, is linked to as well: add an alias or a web-server redirect for every node id.
- **The Redirect module's** existing redirects: carry them over as aliases or web-server rules.
- **`/taxonomy/term/45`**: to the folder or tag view.
- **Language prefixes** (`/de/...`): Exponential siteaccesses per language, with aliases per translation.

## 5. From Exponential to Drupal

- Export with xrowextract (CSV/JSON per class with URL alias, remote id and attributes) and copy the storage
  ([leaving Exponential](leaving-exponential.md)).
- On the Drupal side, the **Migrate API** is the documented way in: write migrations whose source reads the
  xrowextract files (CSV or JSON source plugins come from contributed modules; check the one you choose), and run them
  with Drush (`migrate:import`, `migrate:rollback`, `migrate:status`; Drush 10.4 and later include these commands).
- XmlText becomes HTML (render or transform); the tree becomes menu links or taxonomy; aliases become path aliases.
- Users: resets.

## 6. Risks and checklist

| Risk | Counter-measure |
|---|---|
| Paragraphs and nested entities | design child classes or layouts first; test the deepest nesting |
| 50 items per JSON:API page | page the whole collection; count against the inventory |
| Access-filtered pages | export with an account that sees everything |
| `/node/<id>` links in text | resolve them to the new nodes in a second pass |

- [ ] Content types, fields, vocabularies, media inventoried; mapping table written.
- [ ] JSON:API export complete (counts match); files copied.
- [ ] Import in dependency order with remote ids; translations attached to the same objects.
- [ ] Path aliases, `/node/<id>` and Redirect module entries redirected and tested.
- [ ] Metatag data in `xrowmetadata`; sitemap; analytics.

## Sources

- JSON:API pagination: <https://www.drupal.org/docs/core-modules-and-themes/core-modules/jsonapi-module/pagination>
- JSON:API security considerations (read-only mode): <https://www.drupal.org/docs/core-modules-and-themes/core-modules/jsonapi-module/security-considerations>
- Upgrading with Drush (Migrate): <https://www.drupal.org/docs/upgrading-drupal/upgrade-using-drush>
- Views Data Export: <https://www.drupal.org/project/views_data_export>

This page is part of the Exponential documentation and is free documentation under the GNU Free Documentation License
1.3 or any later version ([the license of this documentation](../../README-LICENSE.md)).
