---
title: "Story: the REST API"
audience: press
category: press-media
order: 78
summary: A feature story about Exponential's REST content API - one extension serving versions 1 and 2, reads at both versions and writes at v2, permission checks on every call, OAuth2 tokens and personal API keys - for coverage of headless and integration use, with sources and the claims to avoid.
keywords: story, REST API, headless, API, integration, OAuth2, API keys, v1, v2, ezprestapi, mobile apps, developers
type: help
---
# Story: the REST API

This page is for technical journalists who cover headless content management and integrations. Exponential serves its
content over a REST API, and the 6.0.15 line put the two API versions into one extension.

## Contents

1. [The story in one paragraph](#the-story-in-one-paragraph)
2. [Facts you can print](#facts-you-can-print)
3. [What not to claim](#what-not-to-claim)
4. [Angles and headline ideas](#angles-and-headline-ideas)

## The story in one paragraph

The README lists "A headless CRUD REST API" among the main features. In Exponential 6.0.15, versions 1 and 2 of the
REST content API come from one extension, `ezprestapi`: reads answer at v1 and v2, writes (create, update, delete) at
v2 only, and "every read and write checks the user's policies ... whatever the request was authenticated with".
Clients authenticate with an OAuth2 token or a personal API key ([REST API v1 and v2](../../guides/rest-api-v1-and-v2.md)).
REST v2 with write support was the headline of 6.0.9 in a 7x press release of 2025
([Earlier press releases and coverage](earlier-press-releases-and-coverage.md)).

## Facts you can print

| Fact | Source |
|---|---|
| Reads at `/api/ezp/v1/...` and `/api/ezp/v2/...`; writes at v2 only | [REST API v1 and v2, In short](../../guides/rest-api-v1-and-v2.md#in-short) |
| Permission checks on every call (`content/read`, `content/create`, `content/edit`, `content/remove`) | same |
| OAuth2 tokens or personal API keys | same; [Personal API keys](../../guides/api-keys.md) |
| The old `ezprestapiprovider` extension is deprecated; clients see no change | same |
| The guide's statements were checked on the demonstration server on 15 October 2026 | [REST API v1 and v2](../../guides/rest-api-v1-and-v2.md) |

## What not to claim

| Do not say | Say instead |
|---|---|
| "GraphQL in Exponential 6" | GraphQL belongs to the Symfony line (Exponential Platform DXP) ([Story: Exponential Platform DXP](story-exponential-platform-dxp.md)) |
| "Anyone can write through the API" | Writes need authentication and the user's permissions |
| "The API is new" | The API has existed for years; 6.0.15 unifies the versions and adds checks |

## Angles and headline ideas

- "Headless with permissions: every API call checks the user's rights."
- "One API extension, two versions, no breaking change."

## Related pages

- [Story: editing content](story-editing-content.md)
- [Story: security](story-security.md)
- [Talking points for developers](../advocates/talking-points-for-developers.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
