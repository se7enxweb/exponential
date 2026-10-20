---
title: An implementation project playbook
audience: partner
category: partnership
order: 360
summary: A phase-by-phase playbook for partners delivering an Exponential project - discovery, content model, environment, build, content migration, quality gates, launch, handover and maintenance - with deliverables, checklists and the help pages behind each phase.
keywords: playbook, implementation, project, phases, discovery, content model, build, migration, launch, handover, maintenance, quality gates, deliverables
type: help
---
# An implementation project playbook

This page is for project leads in implementation partners. It is a playbook, not a method you must follow: adapt it
to your contracts. The project managers' section of the help has the planning detail
([Planning an Exponential project](../leadership/planning-an-exponential-project.md)).

## The phases

| Phase | Deliverables | Help |
|---|---|---|
| 1. Discovery | Goals, audiences, languages, sites, integrations, constraints | [Planning an Exponential project](../leadership/planning-an-exponential-project.md), [Multisite and multilingual planning](../leadership/multisite-and-multilingual-planning.md) |
| 2. Content model | Classes, attributes, sections, roles, workflows | [Content modeling workshops](../leadership/content-modeling-workshops.md) |
| 3. Environment | Hosting, PHP, database, engine; Git repository | [Choosing hosting, database and OS](../leadership/choosing-hosting-database-and-os.md), [Develop with Git](../develop-with-git.md) |
| 4. Build | Designs, templates, layouts, extensions | [The layouts editor](../layouts-editor.md), [Extensions](../../guides/extensions.md), [Estimating and scoping with extensions](../leadership/estimating-and-scoping-with-extensions.md) |
| 5. Content migration | Import scripts or manual entry, redirects | `doc/eng-us/install/` migration chapters |
| 6. Quality gates | Accessibility, security, performance, browsers, languages | [Quality gates and acceptance](../leadership/quality-gates-and-acceptance.md), [Accessibility, security and compliance checklists](../leadership/accessibility-security-and-compliance-checklists.md) |
| 7. Launch | DNS, TLS, caches, monitoring, backups | [Security hardening](../security-hardening.md) |
| 8. Handover | Editor training, documentation, credentials handed over securely | [Getting started](../getting-started.md), [Editing content](../editing-content.md) |
| 9. Maintenance | Release updates, security fixes, PHP upgrades | [Keeping customer sites current](keeping-customers-current.md), [Release and upgrade planning](../leadership/release-and-upgrade-planning.md) |

## Partner-specific checks per phase

- **Discovery:** Is there an existing extension for each requirement? Search before you estimate
  ([Extensions](../../guides/extensions.md#2-find-an-extension)).
- **Environment:** Use a currently supported PHP (8.0 to 8.5 for 6.0.15; Velocity needs 8.1 or later,
  `doc/eng-us/install/02-requirements.md`).
- **Build:** Keep generic improvements separate from customer code, so they can go upstream
  ([Contributing code back](contributing-code-back.md)).
- **Quality:** Run the customer's site in light and dark mode and at high zoom if you changed the administration.
- **Launch:** Remove or protect installer files and change every default password
  ([Default administrator password](../default-administrator-password.md)).
- **Handover:** Tell the customer that the software is GPL and that they may move to another partner at any time
  ([Licensing for partners](licensing-for-partners.md)).

## Risks to manage

| Risk | Mitigation | Page |
|---|---|---|
| Unsupported extension in the critical path | Choose maintained extensions; budget to maintain the rest | [Risk management](../leadership/risk-management.md) |
| Scope growth | Change requests in writing | [Project templates](../leadership/project-templates.md) |
| Upgrade debt after launch | A maintenance contract with release updates | [Keeping customer sites current](keeping-customers-current.md) |
| Knowledge in one head | Git history, documentation, pairing | [Running a team on Git](../leadership/running-a-team-on-git.md) |

## After the project

- Ask for permission to publish a case study and to name the customer ([Writing a case study](writing-a-case-study.md)).
- Log the project for points ([Logging your partner activity](logging-partner-activity.md)).
- Add the "Powered by Exponential" badge if the customer agrees ([Showing "Powered by Exponential"](powered-by-exponential.md)).

## Related pages

- [Implementation partners](implementation-partners.md)
- [Project managers: start here](../leadership/project-managers-start-here.md)
- [Testing releases before your customers do](testing-releases.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
