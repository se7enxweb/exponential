---
title: Symfony applications and Exponential
audience: developer, admin, project-manager
category: install-upgrade
order: 50
summary: Moving content between a custom Symfony application and Exponential 6 - Doctrine entities to content classes, a console command that exports, running a Symfony app next to Exponential, choosing an Exponential Platform distribution when the team wants to stay on Symfony, and the way back.
keywords: Symfony, Doctrine, entities, console command, Twig, bundle, Exponential Platform, Platform Legacy, legacy bridge, REST, migration
type: install
---
# Symfony applications and Exponential

A custom Symfony application keeps its content in Doctrine entities (or another store) and renders it with Twig.
Moving it to Exponential is a content migration from your own database; staying on Symfony while gaining a content
repository is a choice of Exponential Platform distribution.

[Migration hub](README.md) · [Checklist](checklist.md) · [Mapping](content-model-mapping.md) · [Tools](exponential-tools.md)

## 1. First decide: Exponential 6, or a Symfony-based Exponential Platform

| Your team and app | Target |
|---|---|
| The app is mostly content (pages, articles, media); the team accepts TPL and INI | Exponential 6, this guide |
| The team wants to stay on Symfony and Twig, and needs a content repository | [Exponential Platform DXP](exponential-platform-dxp.md) (Symfony 7.4) or [Nexus](exponential-platform-nexus.md) |
| Both: Symfony controllers for the app, the legacy admin and TPL for content | [Platform Legacy](exponential-platform-legacy.md) |
| The app has business logic beyond content (bookings, accounts) | keep the app; move only its content, and link the two (REST, shared sign-in) |

## 2. From a Symfony application to Exponential 6

1. **Map the entities**: each content-like entity becomes a class; its columns attributes; Doctrine relations
   object relations or tree positions; files (VichUploader, Flysystem, plain paths) image or file objects
   ([mapping](content-model-mapping.md)).
2. **Export from the app itself**: a Symfony console command in the app that loads the entities through Doctrine
   (in batches, clearing the entity manager between batches) and writes JSON rows with remote ids
   (`app-<entity>-<id>`), the rendered or raw rich text, and the route of each item (from the router, so the old
   addresses are exact).
3. **Import** on the Exponential server with the [script skeleton](exponential-tools.md#4-a-custom-import-script-the-skeleton)
   or xrowextract's importer.
4. **Twig to TPL**: the templates are rewritten; [17.5](../17-migration-reference.md#175-templates-tpl-and-twig) maps
   the constructs.
5. **Users**: Symfony's `PasswordHasher` with the `auto`, `bcrypt` or `sodium` algorithms writes standard PHP
   `password_hash()` values (`$2y$...`, `$argon2id$...`), which Exponential can verify as type 7
   ([mapping, 6](content-model-mapping.md#6-users-groups-roles-and-passwords)); test with a few accounts. Other
   hashers mean resets.
6. **Routes**: every route that served content becomes a node alias or a redirect.

## 3. Running a Symfony app next to Exponential

When the app keeps its non-content features:

- Serve both under one domain with the web server routing paths to each, or on subdomains.
- Let the app read content from Exponential over REST with a personal API key
  ([REST API v1 and v2](../../guides/rest-api-v1-and-v2.md)).
- Sign-in: keep them separate, or use the same identity provider for both.

## 4. From Exponential to a Symfony application

Export with xrowextract to JSON ([leaving Exponential](leaving-exponential.md)) and load it with a Doctrine
command; or, for a Symfony team, move to an Exponential Platform distribution of the same content model, which keeps
the content repository and its tooling.

## 5. Checklist

- [ ] Target chosen (Exponential 6, DXP, Nexus, Platform Legacy, or app plus content site).
- [ ] Entity mapping written; export command in the app; routes exported from the router.
- [ ] Imported with remote ids; templates rewritten; password hashes tested.
- [ ] Redirects for every content route; REST access for the remaining app.

This page is part of the Exponential documentation and is free documentation under the GNU Free Documentation License
1.3 or any later version ([the license of this documentation](../../README-LICENSE.md)).
