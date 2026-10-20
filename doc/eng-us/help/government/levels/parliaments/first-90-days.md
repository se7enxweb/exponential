---
title: The first 90 days for parliaments
audience: government, project-manager, admin
category: public-sector
order: 60
summary: A 90-day plan for a parliament or legislative assembly starting with Exponential - map the source systems of the record, build the model and imports, migrate members and current business, launch outside sitting weeks - with checks.
keywords: project plan, 90 days, parliament, legislature, source systems, import, members, bills, votes, launch, recess, checks
type: help
---
# The first 90 days for parliaments

This page is for project managers and administrators who start a parliament site on Exponential. Plan the launch for
a recess, never during a sitting week. It assumes an installation exists ([Getting started](../../../../guides/getting-started.md)).

## Contents

1. [Days 1 to 30: sources and rules](#days-1-to-30-sources-and-rules)
2. [Days 31 to 60: model and imports](#days-31-to-60-model-and-imports)
3. [Days 61 to 90: migration and launch](#days-61-to-90-migration-and-launch)
4. [Risks](#risks)

## Days 1 to 30: sources and rules

| Step | Output |
|---|---|
| Map each kind of record to its source system and owner (bills, votes, transcripts, video) | a source map |
| Decide for each: copied into the CMS, or linked to the source | a decision per record type |
| Agree publication times (for example votes within a set time after the sitting) | service levels |
| Agree the address scheme (session, number) for record items | a URL rule ([URL aliases and nice URLs](../../../creators/website-creators/url-aliases-and-nice-urls.md)) |
| Agree data reuse: formats and license | a data policy |

## Days 31 to 60: model and imports

| Step | Check |
|---|---|
| Create Member, Sitting, Bill and Vote record classes ([Content model](content-model.md)) | test objects with relations show on each other's pages |
| Create sections and roles ([Structure](structure-sections-and-roles.md)) | a clerk publishes a vote at once; communications cannot edit it |
| Build imports from source systems where content is copied (REST API or scripts) | an import run creates the expected objects on the test installation ([REST API v1 and v2](../../../../guides/rest-api-v1-and-v2.md)) |
| Raise the version history limit for record classes if required | `VersionHistoryClass[<class id>]` in an override |
| RSS exports for agendas, bills and votes | feeds open in a reader ([RSS feeds](../../../../guides/rss-feeds.md)) |
| Load test the pages of a busy vote day with caches on | a measured result |

## Days 61 to 90: migration and launch

| Step | Read |
|---|---|
| Migrate members, current and former, with dates | [Content model](content-model.md) |
| Migrate the current session's business; link older sessions to the old archive | [Redirects and moved content](../../../creators/website-creators/redirects-and-moved-content.md) |
| Train clerks on the publishing of each record type | [Agendas, bills and voting records](agendas-bills-and-voting-records.md) |
| Accessibility test of record pages and the video page | [Testing accessibility](../../../creators/website-creators/testing-accessibility.md) |
| Launch in a recess | [Launch checklist](../../../creators/website-creators/launch-checklist.md) |

Checks at launch:

- [ ] Every current member has a page; former members of the current term too.
- [ ] A test vote record publishes within the agreed time, with counts and the official record.
- [ ] Old record addresses redirect or point to the archive.

## Risks

| Risk | Response |
|---|---|
| Website and source system diverge | one direction of data flow, documented; corrections made in the source first |
| Imports fail silently | an import report checked by a clerk after each run |
| A peak brings the site down | caches, static pages for the busiest pages, a rehearsed maintenance page ([Maintenance mode](../../../../guides/maintenance-mode.md)) |

## Related pages

- [Parliaments](README.md)
- [Agendas, bills and voting records](agendas-bills-and-voting-records.md)
- [Planning your website](../../../creators/website-creators/planning-your-website.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
