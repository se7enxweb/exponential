---
title: "Project templates: charter, RACI, checklists, launch and handover"
audience: project-manager
category: leadership
order: 140
summary: Copyable fill-in templates for an Exponential project: project charter, RACI matrix, requirements checklist, launch checklist and handover document, each tied to the real features and pages of the product.
keywords: template, charter, project charter, RACI, responsibility matrix, requirements, checklist, launch, go-live, handover, hand-off, documentation, fill-in, form, project management
type: help
---
# Project templates: charter, RACI, checklists, launch and handover

This page is for project managers who want to start an Exponential project with good paperwork instead of a blank
page. It holds five fill-in templates. Copy a block into your own document or ticket system, replace every
`<placeholder>`, and delete what does not apply.

The templates are generic where they must be (your goals, your people, your dates) and specific where the product
gives you something to check: each line that names a feature points to the page that describes it, so the person who
fills it in knows where to look.

## Contents

1. [How to use the templates](#how-to-use-the-templates)
2. [Project charter](#project-charter)
3. [RACI matrix](#raci-matrix)
4. [Requirements checklist](#requirements-checklist)
5. [Launch checklist](#launch-checklist)
6. [Handover document](#handover-document)

## How to use the templates

- **Copy** the text block; it is plain text so that it pastes anywhere.
- **Fill in** every `<placeholder>`. Leave nothing as a placeholder in a signed document; write "not applicable"
  instead.
- **Name roles, not only people.** People change during a project; roles stay. Use the role names of your
  organization, and keep a separate list of who holds each role.
- **Keep versions.** Put each filled template under version control next to the project's code, or in your document
  system with a date.
- **Do not paste secrets.** Passwords, keys and host credentials never go into these documents. Write where they are
  kept, not what they are.

## Project charter

The charter is the agreement at the start: why the project exists, what it delivers, and how everyone will know it
is done. Fill it in during discovery ([Planning an Exponential project](planning-an-exponential-project.md)) and have
it signed before the build starts.

```text
PROJECT CHARTER
Project name:            <project name>
Version / date:          <version>, <date>
Sponsor:                 <role of the person who funds and decides>
Project manager:         <role>
Product owner:           <role of the person who accepts the result>

1. Purpose
   Why this project exists, in two or three sentences:
   <purpose>

2. Goals and measures of success
   | Goal                         | Measure                          | Target   | Where it is measured          |
   |------------------------------|----------------------------------|----------|-------------------------------|
   | <goal>                       | <measure>                        | <target> | <page, log or report>         |
   (For measures the product can report itself, see "Measuring a project: KPIs from the
    product's own logs and audit trail".)

3. Scope
   In scope:
   - Sites (siteaccesses):            <list>
   - Languages:                       <list of locales, e.g. eng-US, ger-DE, ara-SA>
   - Content classes (first model):   <list or "see content model document">
   - Features:                        <multisite, layouts editor, shop, newsletter, REST API, ...>
   - Extensions:                      <list, own and third-party>
   - Content migration:               <source, volume, method>
   Out of scope:
   - <item>

4. Platform decisions
   Exponential version:     <release tag, e.g. v6.0.14, or main with reason>
   PHP version:             <version>
   Database:                <MySQL/MariaDB | PostgreSQL | SQLite | MongoDB | Oracle>
   Operating system:        <OS>
   Web server / engine:     <Exponential Velocity | Apache with PHP-FPM | FrankenPHP | nginx with PHP-FPM>
   Hosting:                 <self-hosted / provider>
   Code in Git:             <repositories, site branch name>
   Configuration from version control (ManagedByDeployment): <yes/no per siteaccess>

5. Deliverables
   - <deliverable>          Owner: <role>   Due: <date>

6. Milestones and gates
   | Gate                     | Date    | Checks                                  | Owner  |
   |--------------------------|---------|-----------------------------------------|--------|
   | G1 Change ready          | ongoing | <definition of done>                    | <role> |
   | G2 Staging ready         | <date>  | <checks>                                | <role> |
   | G3 Acceptance            | <date>  | <acceptance checklist signed>           | <role> |
   | G4 Live                  | <date>  | <launch checklist done>                 | <role> |

7. Quality requirements
   Accessibility:           <standard and level, e.g. WCAG 2.x AA; how it is checked>
   Security:                <hardening checklist, CSP mode, two-step sign-in for administrators>
   Performance:             <how measured, e.g. exp:benchmark baseline>
   Privacy:                 <questions answered with the organization's data protection contact>

8. Assumptions and constraints
   - <assumption>

9. Risks (top five; full register separate)
   - <risk>: <mitigation>

10. Communication
    Weekly status: <day, audience>   Steering: <rhythm>   Channel: <tool>

11. Contribution to the open source project
    Policy: <link to the team contribution policy>   Time reserved: <hours per sprint>

Signatures:  Sponsor <name/role, date>   Product owner <name/role, date>   Project manager <name/role, date>
```

## RACI matrix

R = Responsible (does the work), A = Accountable (one per row, decides), C = Consulted, I = Informed.

The rows name the real areas of the administration and the real tasks of an Exponential project. Who may do a task
in the product is set by **roles and policies** (**Users > Roles and policies**,
[Roles and policies](../../guides/roles-and-policies.md)); this matrix is the organizational side. Keep them in
agreement: someone who is "R" for a task needs a role whose policies allow it, and nobody should hold the
Administrator role only for convenience.

```text
RACI MATRIX: <project name>          Version: <date>

Columns:  PM = project manager   PO = product owner   ED = editors   AD = administrator
          DEV = developers       DES = designer       SM = sales and marketing   HOST = hosting provider

| Task                                                        | PM | PO | ED | AD | DEV | DES | SM | HOST |
|-------------------------------------------------------------|----|----|----|----|-----|-----|----|------|
| Project charter and plan                                    | A  | C  | I  | C  | C   | I   | C  | I    |
| Requirements and scenarios                                  | R  | A  | C  | C  | C   | C   | C  |      |
| Content model (classes, attributes)                         | R  | A  | C  |    | R   | C   |    |      |
| Sections, object states and workflows                       | C  | A  | C  | R  | R   |     |    |      |
| Roles and policies                                          | C  | A  | C  | R  | C   |     |    |      |
| Siteaccesses, languages and URLs                            | C  | A  |    | R  | R   |     | C  |      |
| Design and templates                                        | C  | A  | C  |    | R   | R   | C  |      |
| Layouts (layouts editor)                                    | I  | A  | R  |    | C   | C   | C  |      |
| Extensions: choice and installation                         | C  | A  |    | R  | R   |     |    |      |
| Own extensions: build, test, release                        | I  | I  |    | C  | R/A |     |    |      |
| Content migration                                           | A  | C  | R  | C  | R   |     |    |      |
| Server, PHP, database, web server                           | I  | I  |    | A  | C   |     |    | R    |
| Deployments (staging, production)                           | I  | I  |    | R/A| C   |     |    | C    |
| Cronjobs                                                    | I  |    |    | R/A| C   |     |    |      |
| Backups and restore tests                                   | I  | I  |    | R/A|     |     |    | C    |
| Security hardening and CSP                                  | I  | C  |    | R/A| R   |     |    | C    |
| Audit trail: alerts, recipients, review                     | I  | C  |    | R/A|     |     |    |      |
| Accessibility checks                                        | C  | A  | C  |    | R   | R   |    |      |
| Acceptance                                                  | R  | A  | R  | C  | C   | C   | C  |      |
| Launch go/no-go                                             | R  | A  | I  | C  | C   | I   | I  | C    |
| Launch announcement                                         | C  | A  | I  |    |     |     | R  |      |
| Editor training                                             | A  | C  | I  | C  | R   |     |    |      |
| Upgrades                                                    | A  | I  | I  | R  | R   |     |    | C    |
| Bug reports to the Exponential project                      | I  |    | C  | R  | R   |     |    |      |
| Handover document                                           | R/A| C  | I  | R  | R   |     |    |      |
```

Pages for the rows: [Content model and editing](../../guides/content-model-and-editing.md),
[Sections](../../guides/sections.md), [Object states](../../guides/object-states.md),
[Workflows](../../guides/workflows.md), [Roles and policies](../../guides/roles-and-policies.md),
[Templates and design](../../guides/templates-and-design.md), [The layouts editor](../layouts-editor.md),
[Extensions](../../guides/extensions.md), [Deploying](../../guides/deploying.md), [Cronjobs](../../guides/cronjobs.md),
[Operating a site](../../guides/operating-a-site.md), [Security and audit](../../guides/security-and-audit.md),
[Upgrading](../../guides/upgrading.md), [When something goes wrong](../when-something-goes-wrong.md).

## Requirements checklist

Go through it with the product owner in discovery. Each line is a question; the last column names the page where
the team finds how Exponential answers it. Mark each line **Need**, **Nice to have** or **No**.

```text
REQUIREMENTS CHECKLIST: <project name>          Date: <date>     With: <roles>

Mark: N = need, H = nice to have, - = no

Content
[ ] _ Structured content types of our own (classes with fields)           -> guides/content-model-and-editing.md
[ ] _ Versions, drafts, preview, going back to an older version            -> help/publishing-and-versions.md
[ ] _ Approval before publishing / scheduled publishing                    -> guides/workflows.md
[ ] _ Editors build pages from blocks themselves                           -> help/layouts-editor.md
[ ] _ Rich text editing                                                    -> help/editing-content.md
[ ] _ Images and files, uploads in bulk                                    -> help/media-and-uploads.md
[ ] _ Tags / keywords                                                      -> features/6.0/extensions/eztags.md
[ ] _ Search                                                               -> help/searching.md
[ ] _ RSS feeds / podcasts                                                 -> guides/rss-feeds.md
[ ] _ PDF export                                                           -> guides/pdf-exports.md
[ ] _ Help writing and translating content (Exponential Intelligence)      -> features/6.0/extensions/expai.md

Sites and languages
[ ] _ Several sites from one installation (siteaccesses)                   -> leadership/multisite-and-multilingual-planning.md
[ ] _ Content in several languages: <list>                                 -> guides/content-languages.md, languages.md
[ ] _ Right-to-left languages                                              -> features/6.0/right-to-left.md
[ ] _ Administration in a language other than English: <language>          -> languages.md

Users and access
[ ] _ Editor roles with limited rights                                     -> guides/roles-and-policies.md
[ ] _ Visitor registration                                                 -> guides/roles-and-policies.md
[ ] _ Two-step sign-in                                                     -> guides/two-factor-login.md
[ ] _ Record of who did what (audit trail)                                 -> features/6.0/audit-trail.md

Commerce and communication
[ ] _ Shop: products, basket, orders                                       -> features/6.0/store-dashboard.md
[ ] _ Newsletters                                                          -> features/6.0/extensions/cjw_newsletter.md
[ ] _ Notifications by e-mail                                              -> guides/notifications-administrator.md
[ ] _ E-mail preferences and consent                                       -> features/6.0/mail-preferences.md

Integration
[ ] _ REST API for apps or other systems                                   -> guides/rest-api-v1-and-v2.md
[ ] _ API keys                                                             -> guides/api-keys.md
[ ] _ Remote services                                                      -> guides/remote-services-and-apps.md

Operations
[ ] _ Database: <engine>                                                   -> help/database-support.md
[ ] _ Operating system: <OS>                                               -> help/os-support.md
[ ] _ Web server / Velocity                                                -> guides/deploying.md
[ ] _ Configuration only from version control                              -> features/6.0/managed-by-deployment.md
[ ] _ Maintenance page during work                                         -> features/6.0/maintenance-mode.md
[ ] _ Backups and restore                                                  -> guides/operating-a-site.md
[ ] _ Monitoring and health checks                                         -> guides/system-information.md

Quality
[ ] _ Accessibility level: <level>                                         -> leadership/accessibility-security-and-compliance-checklists.md
[ ] _ Content Security Policy enforced                                     -> features/6.0/content-security-policy.md
[ ] _ Security hardening checklist                                         -> help/security-hardening.md
[ ] _ Performance measured                                                 -> guides/benchmarking.md

Open questions (answer with the organization's own experts)
[ ] Privacy: which personal data is stored, for how long, who may see it?
[ ] Legal: accessibility obligations, imprint, consent requirements?
[ ] Hosting: where must data be stored?
```

The paths are relative to `doc/` in the repository; in this help each one is a page you can open from the search.
The links: [content model and editing](../../guides/content-model-and-editing.md),
[publishing and versions](../publishing-and-versions.md), [workflows](../../guides/workflows.md),
[layouts editor](../layouts-editor.md), [editing content](../editing-content.md),
[media and uploads](../media-and-uploads.md), [eztags](../../features/6.0/extensions/eztags.md),
[searching](../searching.md), [RSS feeds](../../guides/rss-feeds.md), [PDF exports](../../guides/pdf-exports.md),
[expai](../../features/6.0/extensions/expai.md), [multisite and multilingual planning](multisite-and-multilingual-planning.md),
[content languages](../../guides/content-languages.md), [languages](../../languages.md),
[right to left](../../features/6.0/right-to-left.md), [two-step sign-in](../../guides/two-factor-login.md),
[audit trail](../../features/6.0/audit-trail.md), [store dashboard](../../features/6.0/store-dashboard.md),
[cjw_newsletter](../../features/6.0/extensions/cjw_newsletter.md),
[notifications](../../guides/notifications-administrator.md), [mail preferences](../../features/6.0/mail-preferences.md),
[REST API](../../guides/rest-api-v1-and-v2.md), [API keys](../../guides/api-keys.md),
[remote services](../../guides/remote-services-and-apps.md), [database support](../database-support.md),
[OS support](../os-support.md), [deploying](../../guides/deploying.md),
[ManagedByDeployment](../../features/6.0/managed-by-deployment.md), [maintenance mode](../../features/6.0/maintenance-mode.md),
[operating a site](../../guides/operating-a-site.md), [system information](../../guides/system-information.md),
[compliance checklists](accessibility-security-and-compliance-checklists.md),
[Content Security Policy](../../features/6.0/content-security-policy.md), [security hardening](../security-hardening.md),
[benchmarking](../../guides/benchmarking.md).

## Launch checklist

The checklist combines the go-live checklist of the installation book, the first-week checklist and the security
checklist of the help. Where a line comes from a page, the page is named after the arrow.

```text
LAUNCH CHECKLIST: <project name>          Launch date: <date>     Go/no-go: <role>

Two weeks before
[ ] Acceptance signed                                                    -> Quality gates and acceptance
[ ] Launch window and rollback decision owner agreed
[ ] Editors trained; each has a personal account with the right role     -> Roles and policies
[ ] Content migration finished; content checks show nothing unexplained  -> Content checks
[ ] Security hardening checklist worked through                          -> Security hardening, Checklist
[ ] Go-live checklist of the installation book worked through            -> 13.16 Go-live checklist
[ ] CSP reports reviewed; mode decided (report-only / enforce)           -> Content Security Policy
[ ] Performance baseline saved (var/benchmark/)                          -> Benchmarking; 10.15
[ ] Announcement prepared                                                -> Sales and marketing

One day before
[ ] Backup of database and files taken and restored on another machine   -> Operating a site, Back up and restore
[ ] Upgrade check (files and database) run and saved                     -> The upgrade check
[ ] System information: no failure; warnings explained                   -> System information
[ ] Crontab ready: frequent, the default part, infrequent, cache_cleanup -> 10.15, Cronjobs

Launch
[ ] Maintenance mode on if the switch needs it                           -> Maintenance mode
[ ] Final deploy done; every step PASS (exp:velocity deploy)             -> Deploying, section 7
[ ] Administrator password changed; var/log/initial-admin-password gone  -> 10.15
[ ] Debug output off, display_errors=Off                                 -> 13.4
[ ] HTTPS everywhere; secure cookies                                     -> Security hardening, Checklist
[ ] AdminEmail and mail transport set; a test mail arrived in a real inbox -> 10.4, 10.15
[ ] SiteURL set per siteaccess                                           -> System information, Health checks
[ ] Search index complete                                                -> 10.8
[ ] Maintenance mode off
[ ] Smoke test: front page, every siteaccess, every language, sign-in, publish a test item

First week
[ ] Cron logs grow; System information shows recent cronjob runs        -> Cronjobs
[ ] Audit: frequent group runs; alert recipients are real people; chains intact -> Audit trail
[ ] Error digest read daily; security.log looked at                      -> Log viewer, Request logs
[ ] Logs rotate                                                          -> 10.15
[ ] Lessons learned recorded; handover document updated
```

Pages: [Quality gates and acceptance](quality-gates-and-acceptance.md), [Roles and policies](../../guides/roles-and-policies.md),
[Content checks](../../features/6.0/content-checks.md), [Security hardening](../security-hardening.md#checklist),
[13.16 Go-live checklist](../../install/13-security-hardening.md#1316-go-live-checklist),
[Content Security Policy](../../features/6.0/content-security-policy.md), [Benchmarking](../../guides/benchmarking.md),
[10.15 A checklist for the first week](../../install/10-after-installing.md#1015-a-checklist-for-the-first-week),
[Sales and marketing: start here](../business/sales-and-marketing-start-here.md),
[Operating a site](../../guides/operating-a-site.md#6-back-up-and-restore), [The upgrade check](../../guides/upgrade-check.md),
[System information](../../guides/system-information.md#3-health-checks), [Cronjobs](../../guides/cronjobs.md),
[Maintenance mode](../../features/6.0/maintenance-mode.md), [Deploying](../../guides/deploying.md#7-ship-a-php-change-with-one-command),
[13.4](../../install/13-security-hardening.md#134-debug-output-and-error-display),
[10.4](../../install/10-after-installing.md#104-mail-the-file-transport-and-real-sending),
[10.8](../../install/10-after-installing.md#108-search-indexing-and-image-handling),
[The audit trail](../../features/6.0/audit-trail.md), [Log viewer](../../features/6.0/log-viewer.md),
[Request logs](../../features/6.0/request-logs.md).

## Handover document

The handover document lets a new team, or the customer's own staff, run and change the site without asking the
people who built it. Start it in the first week and keep it current; a handover written in the last week is a
memory exercise.

```text
HANDOVER DOCUMENT: <site name>          Version: <date>     Prepared by: <role>     Received by: <role>

1. Overview
   Purpose of the site:          <one paragraph>
   Sites (siteaccesses):         <name: address, language(s), design>
   Administration address:       <address of the admin siteaccess>
   Owners:                       Product owner <role>, technical owner <role>

2. Versions (check: Setup > System information; Setup > Extensions)
   Exponential:                  <version as System information shows it>
   PHP:                          <version>        Database: <engine and version>
   Web server / engine:          <Velocity / Apache with PHP-FPM / ...>
   Extensions:                   <name: version, own or third-party, repository>

3. Code and repositories
   Kernel branch:                <branch name>, based on <tag or main>
   Own repositories:             <list with purpose>
   Local patches still carried:  <list with reason and upstream issue, or "none">
   Release owner per repository: <role>
   How to deploy:                <command and order, e.g. exp:velocity deploy>

4. Configuration
   Settings in version control:  <where>     ManagedByDeployment: <per siteaccess>
   Where secrets are kept:       <place, never the values>
   Audit keys:                   settings/override/audit.ini.append.php backed up to <place>

5. Content model
   Classes and their purpose:    <list or link>
   Sections, object states:      <list>
   Workflows:                    <list>
   Languages:                    <list, main language>
   Layouts:                      <shared layouts, rules>

6. Users and access
   Roles and what they are for:  <role: purpose, assigned to which groups>
   Administrators:               <roles of people, personal accounts only>
   Two-step sign-in:             <who must use it>
   Break-glass procedure:        <where it is written>

7. Operations
   Cronjobs:                     <crontab lines or groups, schedule>
   Backups:                      <what, when, where, retention>; last restore test: <date>
   Logs:                         <rotation, who reads the error digest, how often>
   Audit:                        <alert recipients (roles), sinks, verify schedule>
   Monitoring:                   <checks and who receives alerts>
   Mail:                         <transport, sender>

8. Quality
   Tests:                        <suites or test files that cover own code>
   Performance baseline:         <file and date>
   CSP:                          <mode per siteaccess>
   Accessibility:                <what was checked, when, result>

9. Known issues and decisions
   | Issue / decision | Status | Upstream link | Owner |

10. Upgrades
   Next planned upgrade:         <target version, date>
   Notes to read:                <changelogs and behavior change notes since the running version>

11. Where to get help
   This help (Help in the administration), the forums, the issue trackers   -> When something goes wrong
   Security problems: privately, as SECURITY.md describes

Sign-off:  Handed over by <role, date>   Received by <role, date>
```

Pages for the sections: [System information](../../guides/system-information.md),
[Extensions page](../../guides/extensions-page.md), [Running a team on Git](running-a-team-on-git.md),
[ManagedByDeployment](../../features/6.0/managed-by-deployment.md), [The audit trail](../../features/6.0/audit-trail.md),
[Roles and policies](../../guides/roles-and-policies.md), [Cronjobs](../../guides/cronjobs.md),
[Operating a site](../../guides/operating-a-site.md), [Release and upgrade planning](release-and-upgrade-planning.md),
[When something goes wrong](../when-something-goes-wrong.md).

## Related pages

- [Planning an Exponential project](planning-an-exponential-project.md), [Project managers: start here](project-managers-start-here.md)
- [Risk management for Exponential projects](risk-management.md): a register to copy
- [Quality gates and acceptance](quality-gates-and-acceptance.md): definition of done and acceptance checklist
- [Working together](working-together.md): handoffs between roles
- [Measuring a project: KPIs from the product's own logs and audit trail](kpis-from-logs-and-audit.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
