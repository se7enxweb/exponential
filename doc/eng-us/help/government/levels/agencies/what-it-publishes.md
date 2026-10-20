---
title: What an agency publishes
audience: government, project-manager, website-creator
category: public-sector
order: 20
summary: The content regulators, inspectorates and executive agencies typically publish - guidance, decisions and enforcement, registers, programs, data and corporate information - and what each needs from Exponential.
keywords: agency website, regulator, guidance, enforcement decisions, registers, programs, grants, statistics, open data, corporate information, scope
type: help
---
# What an agency publishes

This page is for project managers and website creators at a regulator, inspectorate or executive agency who define
the scope of a site. It lists what agencies typically publish (general public-sector practice) and what each kind of
content needs from the system.

## Contents

1. [The content](#the-content)
2. [What the CMS must do](#what-the-cms-must-do)
3. [Registers and data](#registers-and-data)
4. [Questions to settle](#questions-to-settle)

## The content

| Content | Audience | Shape and rhythm |
|---|---|---|
| Guidance: rules explained, how to comply, codes of practice | regulated parties, professionals | long pages and documents; versioned; effective dates |
| Decisions and enforcement: notices, sanctions, outcomes of inspections | public, press, regulated parties | per case; may name organizations |
| Registers: licensed or registered parties | public, businesses | lists that change daily, often from a database |
| Programs: what the agency funds or delivers, how to apply | applicants | calls with deadlines |
| Reports and statistics | professionals, researchers, oversight | periodic, often PDF plus data files |
| Consultations on new guidance | regulated parties | fixed periods, responses |
| News, alerts and warnings (for example about a scam or a product) | public | urgent, short-lived |
| Corporate information: board, annual report, budget, how to complain | everyone | yearly |

## What the CMS must do

| Need | Exponential | Read |
|---|---|---|
| Guidance with versions and "what changed" | versions, compare, new draft from an old version | [Versions of an object](../../../../guides/content-history.md) |
| A status per document (in force, superseded, withdrawn) | object states, shown in templates and usable in roles | [Object states](../../../../guides/object-states.md) |
| Legal review before publishing | an *Approve* event with the legal team as approvers | [Workflows](../../../../guides/workflows.md) |
| Subscribers told about changes | notifications on a subtree; RSS | [Notifications](../../../../guides/notifications-administrator.md), [RSS feeds](../../../../guides/rss-feeds.md) |
| Separate entry points for the public and professionals | two folders or two siteaccesses over shared content | [Multisite setup](../../../creators/website-creators/multisite-setup.md) |
| Consultations | the information collector | [Collected information](../../../../guides/collected-information.md) |
| Warnings on the front page | a folder or a layouts block that the front page shows | [The layouts editor](../../../layouts-editor.md) |

## Registers and data

Registers usually live in the agency's case or licensing system. Exponential is not a register database; the usual
patterns are:

| Pattern | When |
|---|---|
| Link to the register's own search | the register is large or changes constantly |
| Publish a periodic export as a File object (CSV, with a date in the name) | the register is published as open data |
| Import entries as content objects through the REST API or a script | the register is small and changes rarely ([REST API v1 and v2](../../../../guides/rest-api-v1-and-v2.md)) |

For open data publication, describe datasets with a standard vocabulary such as [DCAT 3](https://www.w3.org/TR/vocab-dcat-3/);
in the EU the Open Data Directive, [Directive (EU) 2019/1024](https://eur-lex.europa.eu/eli/dir/2019/1024/oj), sets
rules for re-use. Exponential does not generate DCAT catalogs; that needs a template or a separate catalog.

## Questions to settle

- [ ] Which guidance is legally binding, and who signs off each change?
- [ ] Must earlier versions of guidance stay publicly available, or only on request?
- [ ] Which enforcement decisions are published, for how long, and with which names removed?
- [ ] Which registers are published, and from which system?

## Related pages

- [Agencies](README.md)
- [Guidance for regulated parties](guidance-for-regulated-parties.md)
- [Content model for agencies](content-model.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
