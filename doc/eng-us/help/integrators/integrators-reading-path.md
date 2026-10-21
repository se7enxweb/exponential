---
title: Integrators' reading path
audience: integrator, developer, project-manager
category: integration
order: 540
summary: The whole integrators' section in one reading order, in six stages from orientation to running projects, with a short version for each role and an estimate of the reading time per stage.
keywords: reading path, reading order, learning path, curriculum, integrator, onboarding, stages, role, start
type: help
---
# Integrators' reading path

This page puts every page of the integrators' section in one order. Read it front to back in your first weeks, or
take the short path for your role. Times are rough reading times, not including trying things out.

## Contents

1. [Stage 1: orientation](#stage-1-orientation)
2. [Stage 2: the surfaces](#stage-2-the-surfaces)
3. [Stage 3: hooks and systems around the site](#stage-3-hooks-and-systems-around-the-site)
4. [Stage 4: patterns and building](#stage-4-patterns-and-building)
5. [Stage 5: quality and operations](#stage-5-quality-and-operations)
6. [Stage 6: projects and business](#stage-6-projects-and-business)
7. [Short paths by role](#short-paths-by-role)

## Stage 1: orientation

About one hour.

1. [Integrators: start here](integrators-start-here.md)
2. [What integrators do with Exponential](what-integrators-do-with-exponential.md)
3. [Integration surfaces at a glance](integration-surfaces-at-a-glance.md)
4. [Choosing an integration surface](choosing-an-integration-surface.md)
5. [Choosing credentials for an integration](choosing-credentials-for-an-integration.md)

## Stage 2: the surfaces

About three hours.

6. [The REST API for integrators](rest-api-for-integrators.md)
7. [OAuth applications for integrations](oauth-applications-for-integrations.md)
8. [Personal API keys for integrations](personal-api-keys-for-integrations.md)
9. [Remote services with expservices](remote-services-with-expservices.md)
10. [ezjscore server functions for integrations](ezjscore-server-functions-for-integrations.md)
11. [RSS, Atom and OPML feeds](rss-atom-and-opml-feeds.md)
12. [Syndication between installations](syndication-between-installations.md)
13. [WebDAV and SOAP](webdav-and-soap.md)
14. [Packages for import and export](packages-import-and-export.md)
15. [Bulk data exchange with xrowextract](bulk-data-exchange-with-xrowextract.md)
16. [CLI scripts and ezexec](cli-scripts-and-ezexec.md)
17. [Cronjobs for integrations](cronjobs-for-integrations.md)

## Stage 3: hooks and systems around the site

About three hours.

18. [Workflows and triggers as integration hooks](workflows-and-triggers-as-hooks.md)
19. [Event listeners for integrations](event-listeners-for-integrations.md)
20. [Outgoing webhooks from the audit trail](outgoing-webhooks-from-the-audit-trail.md)
21. [Mail and notifications integration](mail-and-notifications-integration.md)
22. [Forms and collected information](forms-and-collected-information.md)
23. [Login handlers - LDAP, textfile and your own](login-handlers-ldap-and-textfile.md)
24. [Single sign-on and social login](single-sign-on-and-social-login.md)
25. [Search engine integration](search-engine-integration.md)
26. [Payment gateways and the shop](payment-gateways-and-the-shop.md)
27. [AI providers, the job queue and MCP](ai-job-queue-and-mcp.md)

## Stage 4: patterns and building

About three hours, more with the code.

28. [Integration patterns: push and pull, batch and real time](integration-patterns-push-pull-batch-real-time.md)
29. [Idempotent imports keyed by remote_id](idempotent-imports-keyed-by-remote-id.md)
30. [Mapping content classes to external data](mapping-content-classes-to-external-data.md)
31. [Writing an import script](writing-an-import-script.md)
32. [Writing an export or an outbound sync](writing-an-export-or-outbound-sync.md)
33. [Migrating content from another CMS](migrating-content-from-another-cms.md)
34. [Building an integration as an extension](building-an-integration-as-an-extension.md)

## Stage 5: quality and operations

About two and a half hours.

35. [Error handling and logging](error-handling-and-logging.md)
36. [Security for integrations](security-for-integrations.md)
37. [Service accounts, roles and policies](service-accounts-roles-and-policies.md)
38. [Caching after imports](caching-after-imports.md)
39. [Velocity and performance for integrations](velocity-and-performance-for-integrations.md)
40. [Testing integrations](testing-integrations.md)
41. [Monitoring integrations](monitoring-integrations.md)
42. [Upgrades without breaking integrations](upgrades-without-breaking-integrations.md)

## Stage 6: projects and business

About two hours.

43. [Integration project playbook](integration-project-playbook.md)
44. [Estimating integration work](estimating-integration-work.md)
45. [Statements of work for integrations](statements-of-work-for-integrations.md)
46. [Handover of an integration](handover-of-an-integration.md)
47. [Contributing integrations back](contributing-integrations-back.md)
48. [Integrators and the partner program](integrators-and-the-partner-program.md)
49. Worksheets: [Discovery worksheet](integration-discovery-worksheet.md), [Interface specification template](interface-specification-template.md), [Security checklist](integration-security-checklist.md), [Go-live checklist](integration-go-live-checklist.md)
50. Reference: [FAQ](integrators-faq.md), [Glossary](integrators-glossary.md)

## Short paths by role

| Role | Pages (numbers above) |
|---|---|
| Solution architect | 1-5, 28-30, 18, 20, 36 |
| Integration developer | 1, 3, 16-18, 29-32, 34, 35, 40 |
| Developer of an external app | 1, 5-9, 37 |
| Identity engineer | 1, 23, 24, 36, 37 |
| Operations engineer | 1, 17, 35, 38, 39, 41, 42, 46 |
| Project lead or account manager | 1, 2, 43-46, 48, 49 |

Next, for developers: [Extension development: the book](../extension-development/README.md) and
[Develop with Git](../develop-with-git.md).

## Related pages

- [Integrators: start here](integrators-start-here.md)
- [Partner reading path](../partners/partner-reading-path.md)
- [Reading paths of the extension book](../extension-development/reading-paths.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
