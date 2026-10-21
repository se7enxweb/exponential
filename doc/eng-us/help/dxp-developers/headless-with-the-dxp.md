---
title: Headless with the DXP
audience: dxp-developer
category: dxp-development
order: 28
summary: Planning a headless project on Exponential Platform DXP - which API for which client, authentication per client, CORS, caching, previews, images and the content model as an API contract - with a checklist for the first release.
keywords: headless, decoupled, REST API, GraphQL, JWT, CORS, mobile app, JavaScript front end, API contract, preview, image variations, caching, checklist
type: help
---
# Headless with the DXP

This page is for developers and architects who use the DXP as a content back end for other clients: a single-page
application, a mobile app, a static site generator, a kiosk. Editors keep working in `/adminui/`; your clients read
(and sometimes write) through the APIs.

## Contents

1. [Which API for which client](#which-api-for-which-client)
2. [The content model is your API](#the-content-model-is-your-api)
3. [Authentication per client](#authentication-per-client)
4. [Images](#images)
5. [Caching](#caching)
6. [Previews and links](#previews-and-links)
7. [First-release checklist](#first-release-checklist)

## Which API for which client

| Client needs | Choose | Why |
|---|---|---|
| Exactly the fields of a screen, few round trips | GraphQL | One query, the fields you ask for |
| Create and update content, upload files | REST | Full create, update and delete on the repository |
| A server-rendered site | Twig in the DXP itself | No API at all |

Both can be used side by side.

## The content model is your API

Content type identifiers and field identifiers become GraphQL types and fields, and appear in REST responses. Agree
them with client developers before editors fill content, version changes like an API, and regenerate the GraphQL
schema in the same change ([GraphQL](graphql-and-graphiql.md#the-schema)).

## Authentication per client

- Public, read-only content: anonymous requests, limited by the Anonymous role.
- Apps and services: one user per client, a role with only the policies it needs, JWT tokens
  ([JWT authentication](jwt-authentication.md)).
- Browsers on another origin: allow exactly their origins with `CORS_ALLOW_ORIGIN`
  ([The REST API, CORS](the-rest-api.md#cors)).

## Images

Clients should request variations, never originals. Define variations for the sizes your clients show
([Image variations](image-variations.md)); include the width and height in what you send.

## Caching

Anonymous `GET` requests go through the HTTP cache, which the DXP purges when content changes
([HTTP cache and Varnish](http-cache-and-varnish.md)). Authenticated requests are personal; cache them in the client,
not in a shared proxy.

## Previews and links

A decoupled front end needs its own routes for content. Decide early how a location maps to a client URL (for example
by the URL alias the API returns) and how editors preview drafts; the DXP's preview in `/adminui/` renders with the
site's Twig templates, not with your client.

## First-release checklist

- [ ] The API prefix is read from configuration in every client ([Where it lives](the-rest-api.md#where-it-lives)).
- [ ] Every client has its own user and a minimal role.
- [ ] JWT keys are generated on every environment and backed up.
- [ ] CORS lists only your origins.
- [ ] The GraphQL schema is committed and matches the content types.
- [ ] Image variations exist for every size a client shows.
- [ ] Anonymous responses are cached and purged on publish.

## Related pages

- [Headless with the REST API (for organizations)](../businesses/mobile-and-headless/headless-with-the-rest-api.md)
- [The REST API](the-rest-api.md)
- [GraphQL and GraphiQL](graphql-and-graphiql.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
