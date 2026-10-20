---
title: Exporting with the REST API
audience: government, developer, admin
category: public-sector
order: 150
summary: For developers moving content to a records system, an archive or an open data portal - the read routes of Exponential's REST API (v1 and v2), authentication with OAuth2 tokens or personal API keys, permission checks, and a pattern for a repeatable export.
keywords: REST API, export, JSON, v1, v2, content/node, content/object, fields, children, OAuth2, API keys, permissions, records system, archive, integration, ezprestapi, public sector
type: help
---
# Exporting with the REST API

This page is for developers who build a repeatable export from an Exponential site into a records system, an archive
or another platform. The REST API answers under `/api/ezp/<version>` with JSON; every content route needs a user by
default and checks that user's permissions ([REST API v1 and v2](../../../guides/rest-api-v1-and-v2.md)).

## Contents

1. [Read routes](#read-routes)
2. [Authentication](#authentication)
3. [A repeatable export](#a-repeatable-export)
4. [Limits](#limits)
5. [Related pages](#related-pages)

## Read routes

Relative to `/api/ezp/<version>` (v1 and v2):

| Route | Returns |
|---|---|
| `GET /content/node/<id>` | one node (`ResponseGroups=Metadata,Locations,Fields`, `OutputFormat=xhtml` for rendered text) |
| `GET /content/node/<id>/list(/offset/<n>)(/limit/<n>)(/sort/<key>(/asc\|desc))` | the children, paged |
| `GET /content/node/<id>/childrenCount` | the number of children |
| `GET /content/node/<id>/fields`, `/field/<identifier>` | the object's fields |
| `GET /content/object/<id>`, `.../fields`, `.../field/<identifier>` | the same by object id |

Writes (create, delete) exist only in v2; an export needs none.

## Authentication

`rest.ini [Authentication] RequireAuthentication=enabled` is the default. Use OAuth2 tokens or a **personal API key**
of a dedicated export user ([API keys](../../../guides/api-keys.md)). Give that user a role with read access limited to
what the export needs (sections, subtrees); the API asks the user's policies the way the content module does. Answers:
`401` without a valid token or key, `403` when the user may not.

## A repeatable export

1. Choose the root node(s) of what to export (for example the notices folder).
2. Walk the tree with `/list` in pages (`/offset/<n>/limit/<n>`), fetching `/fields` for each object.
3. Store each object's JSON together with its node id, object id, modification date and the export date.
4. Files of file and image fields: check what the field JSON returns for them in your version; where it gives no
   address you can fetch, copy the files from `var/storage` in a server-side export.
5. On the next run, compare modification dates and export only what changed.
6. Log each run; keep the export user's key in your secret store.

## Limits

- The API returns the current published version; earlier versions are not exposed by these routes (use the versions
  page or a database export for history).
- `listAtom` answers 500 (a known issue in the guide).
- Large exports load the site: run them off-peak and page with modest limits.

## Related pages

- [REST API v1 and v2](../../../guides/rest-api-v1-and-v2.md)
- [API keys](../../../guides/api-keys.md)
- [Remote services and apps](../../../guides/remote-services-and-apps.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
