---
title: Interface specification template
audience: integrator, developer, project-manager
category: integration
order: 490
summary: A template for specifying one integration flow with Exponential - purpose, parties, surface and endpoints, authentication, data mapping and keys, timing and volume, error handling, security, monitoring, test and acceptance cases, and change history - with notes on what to fill in.
keywords: interface specification, interface control document, ICD, API specification, template, data mapping, endpoints, authentication, error handling, SLA, monitoring, acceptance cases, change log
type: help
---
# Interface specification template

This page is for integrators who document an integration flow. One specification per flow is the contract between
the teams: developers build from it, testers test against it, operators run it, and the next integrator understands
it. Copy the template below; the notes after it say what belongs in each section.

## Contents

1. [The template](#the-template)
2. [Notes per section](#notes-per-section)
3. [Keeping it alive](#keeping-it-alive)

## The template

```text
INTERFACE SPECIFICATION   <flow name>                     version <n>   date <date>   status draft / agreed / live

1  Purpose
   One paragraph: what moves, why, for whom.

2  Parties
   Source system: <name, version, owner, contact>
   Target system: <name, version, owner, contact>
   Exponential installation: <host>, siteaccess <name>, version <6.0.x>

3  Surface and endpoints
   Surface: <REST /api/ezp/v2/... | expservices exp<domain>::<method> | cronjob part <name> | workflow event on <trigger>
            | file via <SFTP/S3/...> | RSS export /rss/feed/<address> | audit webhook>
   Endpoints / paths / files: ...
   Direction and initiator: ...

4  Authentication and authorization
   Credential: <personal API key of svc-... | OAuth application ... | expservices token | basic | none>
   Service account and role: <login>, role <name> (policies and limitations)
   Where the secret is stored on each side: ...
   Rotation: every <n> days; next <date>

5  Data
   5.1 Objects / records and the content class: <class identifier>
   5.2 Key: remote_id scheme <prefix>-<type>-<source id>
   5.3 Mapping table: source field | attribute | datatype | transformation | owner | required
   5.4 Removals: <remove | hide | state | keep>
   5.5 Languages, locations, sections, relations: ...
   5.6 Personal data: <none | fields> ; retention ...

6  Timing and volume
   Schedule / trigger: ...    Acceptable delay: ...
   Volume: total ..., per day ..., peak ...
   Full and delta runs: ...

7  Error handling
   Transient errors: retries <n>, backoff <...>, dead letter <where>
   Invalid records: skipped and reported to <who>
   Fatal conditions: <list>, run stops, alert <who>
   Safety stop: <rule>

8  Security
   Transport: HTTPS / SFTP ...; certificate checks on
   Input validation: ...
   Signatures: <HMAC header, secret location> or n/a

9  Monitoring and operations
   Log: var/log/<file>.log, summary line format
   Checks and thresholds: ...
   Runbook section: ...

10 Tests and acceptance
   Test data: ...
   Acceptance cases: AC-1 ..., AC-2 ...

11 Change history
   <version> <date> <author> <change>
```

## Notes per section

| Section | Fill in |
|---|---|
| 3 Surface | The exact route, service or part name, so a reader can find the code; why this surface ([Choosing an integration surface](choosing-an-integration-surface.md)) |
| 4 Authentication | The credential type and the role; never the secret ([Choosing credentials](choosing-credentials-for-an-integration.md), [Service accounts](service-accounts-roles-and-policies.md)) |
| 5 Data | The mapping table from [Mapping classes to external data](mapping-content-classes-to-external-data.md#a-mapping-table) and the key scheme from [Idempotent imports](idempotent-imports-keyed-by-remote-id.md#a-key-scheme) |
| 6 Timing | Agreed with the data owner, not with the developer |
| 7 Errors | The three kinds from [Error handling and logging](error-handling-and-logging.md#three-kinds-of-errors) |
| 9 Monitoring | The plan from [Monitoring integrations](monitoring-integrations.md#a-monitoring-plan) |
| 10 Acceptance | Cases in business language with real records, as on [Testing integrations](testing-integrations.md#acceptance-with-the-customer) |

## Keeping it alive

- Store the specification in the integration extension's repository (`doc/`), next to the code it describes.
- Every change of the flow changes the specification in the same pull request.
- Raise the version and add a line to the change history; the statement of work refers to a version.
- At handover, the specifications are part of the package ([Handover](handover-of-an-integration.md)).

## Related pages

- [Integration discovery worksheet](integration-discovery-worksheet.md)
- [Statements of work for integrations](statements-of-work-for-integrations.md)
- [Documenting your extension](../extension-development/part-3-extension-developer/documenting-your-extension.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
