---
title: Risk management for Exponential projects
audience: project-manager
category: leadership
order: 110
summary: A risk register for Exponential projects: the usual risks of building and running a site, each with likelihood, impact and a mitigation that points to a real feature, page or command of the product.
keywords: risk, risk register, risk management, likelihood, impact, mitigation, backup, upgrade, security, audit, vendor lock-in, dependencies, skills, deployment, configuration drift, contingency
type: help
---
# Risk management for Exponential projects

This page is for project managers who keep a risk register. It lists the risks that come up again and again when
an organization builds, launches and runs a site with Exponential, and for each one names a **mitigation that the
product or its documentation already provides**: a feature, a page in the administration, a command or a procedure.

The likelihood and impact columns are a **starting point** for your own judgment, not measurements. The sources do
not publish incident statistics; your project's own situation decides the real values. Every mitigation, on the
other hand, links to the page that describes it.

## Contents

1. [How to use this register](#how-to-use-this-register)
2. [The scales](#the-scales)
3. [The risk register](#the-risk-register)
4. [Risks in more detail](#risks-in-more-detail)
5. [Risk review rhythm](#risk-review-rhythm)
6. [A blank register to copy](#a-blank-register-to-copy)

## How to use this register

1. **Copy** the register into your project's own document (the blank form is at the end of this page).
2. **Remove** what does not apply: a site without a shop drops the commerce risks.
3. **Score** each risk for your project: likelihood and impact, with a reason.
4. **Assign** an owner from your team for each risk and each mitigation.
5. **Review** the register at the rhythm below, and whenever a gate is passed.

Risks that are specific to your organization (budget, staff turnover, procurement, legal) are not on this page. Add
them; this page only covers what the product and its documentation can speak to.

## The scales

| Score | Likelihood | Impact |
|---|---|---|
| 1 Low | Unlikely during the project | Inconvenience; no visitor or editor notices |
| 2 Medium | Could well happen once | Editors or visitors are affected for a while; work is redone |
| 3 High | Expected unless something is done | Data loss, a security incident, a missed launch, or a site that cannot be maintained |

**Priority** = likelihood x impact. Treat 6 and 9 first.

## The risk register

The values in the L (likelihood) and I (impact) columns are suggested defaults for a typical project.

| ID | Risk | L | I | Mitigation in the product | Where it is described |
|---|---|---|---|---|---|
| R01 | Data is lost: a failed upgrade, a mistaken deletion, a broken disk | 2 | 3 | Database and files backed up together in maintenance mode; a restore tested on another machine; git_manager's backup manager for teams without a shell | [Operating a site, "Back up and restore"](../../guides/operating-a-site.md#6-back-up-and-restore), [10.9](../../install/10-after-installing.md#109-backups-and-restore), [git_manager](../../features/6.0/extensions/git_manager.md) |
| R02 | Content removed by mistake | 2 | 2 | Removed content goes to the trash first; the trash records who removed it and where; the audit trail records every removal | [The trash view](../../bc/6.0/trash.md), [The audit trail](../../features/6.0/audit-trail.md) |
| R03 | An upgrade breaks the site | 2 | 3 | Rehearsal on a copy; the upgrade check before and after; verification commands; the rule "never run an installer over an existing site" | [Release and upgrade planning](release-and-upgrade-planning.md), [11.7](../../install/11-upgrading.md#117-verify-the-upgrade), [The upgrade check](../../guides/upgrade-check.md) |
| R04 | A behavior change surprises users after an upgrade (for example accounts that can no longer sign in) | 2 | 2 | Read the changelog and behavior change notes of each period passed; the upgrade chapter lists the changes people meet most often | [11.6 step 3](../../install/11-upgrading.md#step-3-read-the-checklist-of-each-period-you-pass) |
| R05 | A known vulnerability in a release you run | 2 | 3 | Security fixes are published as the next 6.0.x release; follow releases; `composer audit`; file consistency check after every update | [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md), [Security hardening](../security-hardening.md#outdated-dependencies-and-the-supply-chain) |
| R06 | A security problem you found is disclosed publicly before it is fixed | 1 | 3 | Report privately as SECURITY.md says, never in a public tracker | [When something goes wrong, "Security problems"](../when-something-goes-wrong.md#security-problems) |
| R07 | Brute force or credential stuffing on the sign-in | 2 | 3 | Failed sign-in handling, two-step sign-in, one account per person; audit alert rule for brute force | [Security hardening](../security-hardening.md#brute-force-and-credential-stuffing-on-the-sign-in), [Two-factor login](../../guides/two-factor-login.md), [Security and audit](../../guides/security-and-audit.md) |
| R08 | Someone gets more access than intended | 2 | 3 | Roles built from limited policies; `PreventPrivilegeEscalation` (on by default) stops users granting what they do not hold; the role list marks full access and role-changing roles | [Roles and policies](../../guides/roles-and-policies.md) |
| R09 | A change nobody can explain ("who did this?") | 2 | 2 | The audit trail, on by default: content, access, system and commerce channels, hash-chained, verifiable | [The audit trail](../../features/6.0/audit-trail.md) |
| R10 | Cross-site scripting through templates or extensions | 2 | 3 | Content Security Policy, report-only on the administration by default, reports collected in `csp.log`; switch to enforce step by step | [Content Security Policy](../../features/6.0/content-security-policy.md) |
| R11 | Debug output or error details shown to visitors | 2 | 2 | System information health checks flag debug output, development settings and `display_errors` | [System information, "Health checks"](../../guides/system-information.md#3-health-checks), [13.4](../../install/13-security-hardening.md#134-debug-output-and-error-display) |
| R12 | Settings changed in the admin are lost at the next deployment, or differ between servers | 2 | 2 | `ManagedByDeployment`: the configuration of a siteaccess comes from version control only | [ManagedByDeployment](../../features/6.0/managed-by-deployment.md) |
| R13 | A change goes live with stale caches, and the old version is served | 2 | 2 | `exp:velocity deploy` runs autoloads, caches, PHP reload and Velocity restart in the right order and stops at the first failure | [Deploying, section 7](../../guides/deploying.md#7-ship-a-php-change-with-one-command), [11.6](../../install/11-upgrading.md#116-from-any-60x-to-today) |
| R14 | Cronjobs stop running and nobody notices (no indexing, no notifications, no audit alerts) | 2 | 2 | System information's cronjob check; the audit dashboard warns when its cronjob part has not run for an hour; the Cronjobs page shows recent runs | [Cronjobs](../../guides/cronjobs.md), [System information](../../guides/system-information.md#3-health-checks) |
| R15 | Logs fill the disk | 1 | 2 | Log rotation; log health against a disk budget (default 256 MB) | [Log viewer, "Log health"](../../features/6.0/log-viewer.md#log-health), [Log rotation](../../features/6.0/log-rotation.md) |
| R16 | Broken links, missing files or unused assets after a content migration | 2 | 2 | Content checks: broken relations, missing files, unreferenced objects, storage files | [Content checks](../../features/6.0/content-checks.md) |
| R17 | A required PHP version is not available from the host | 2 | 2 | Check the PHP table early; PHP 8.0 is the oldest supported, 8.1 to 8.5 supported | [PHP versions](../who-is-the-exponential-foundation.md#php-versions), [Requirements](../../install/02-requirements.md) |
| R18 | An extension does not load on the PHP of the server | 2 | 2 | `exp:checkclasses` reports every class PHP refuses to load | [11.6 step 4](../../install/11-upgrading.md#step-4-check-your-own-extensions) |
| R19 | A third-party extension is unsafe or unmaintained | 2 | 2 | Review its code before installing; extensions are Git repositories with visible history | [Security hardening](../security-hardening.md#outdated-dependencies-and-the-supply-chain), [Extensions](../../guides/extensions.md) |
| R20 | Lock-in: the site cannot move to another host, database or team | 1 | 3 | GPL v2 or later code, full Git history, a choice of databases and operating systems, self-hosting | [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md), [Database support](../database-support.md), [OS support](../os-support.md) |
| R21 | Too few people with Exponential skills | 2 | 2 | See [Skills availability](#skills-availability) below: an honest answer, not a feature | [Develop with Git](../develop-with-git.md), [Community places](../who-is-the-exponential-foundation.md#community-places) |
| R22 | Knowledge stays with one person ("bus factor") | 2 | 3 | Work in Git with reviewed pull requests; documentation in the repository; the handover document | [Running a team on Git](running-a-team-on-git.md), [Project templates](project-templates.md) |
| R23 | A release of your own extension is published with a mistake | 2 | 2 | Check before tagging; a published tag is never moved; the fix is the next version | [Extensions, "Release it"](../../guides/extensions.md#7-release-it) |
| R24 | Personal data ends up in logs, reports or bug reports | 2 | 2 | Logs mask addresses and secrets; the system report leaves out passwords and keys; the bug report advice says what to leave out | [Request logs, "Privacy"](../../features/6.0/request-logs.md#privacy), [Log viewer](../../features/6.0/log-viewer.md), [When something goes wrong](../when-something-goes-wrong.md#a-good-bug-description) |
| R25 | Test mail reaches real people during development | 2 | 2 | The mail file transport writes mail to files; System information notes `Transport=file` | [10.4](../../install/10-after-installing.md#104-mail-the-file-transport-and-real-sending), [System information](../../guides/system-information.md#3-health-checks) |
| R26 | The site is slower after a change | 2 | 1 | `exp:benchmark` with a saved baseline exits 1 on a regression | [Benchmarking](../../guides/benchmarking.md#9-comparing-runs-baselines-and-regressions) |
| R27 | Editors cannot work during maintenance, or visitors see errors | 2 | 1 | Maintenance mode: a 503 page that is never cached, with a message and an end time | [Maintenance mode](../../features/6.0/maintenance-mode.md) |
| R28 | Accessibility requirements are not met | 2 | 2 | The admin4 design's tested WCAG rules; right-to-left support; your own checks for your own templates | [Quality gates and acceptance](quality-gates-and-acceptance.md#accessibility-checks), [Right to left](../../features/6.0/right-to-left.md) |
| R29 | A writing assistant sends content to an outside service against policy | 1 | 2 | Exponential Intelligence is inactive until activated; it writes drafts only; a data policy per provider (region `us`, `eu` or `local`) | [expai](../../features/6.0/extensions/expai.md) |
| R30 | The administration is reachable by anyone on the internet | 2 | 2 | The administration on its own host, restricted by network or VPN; request-shield in front | [Security hardening](../security-hardening.md#checklist), [13.5](../../install/13-security-hardening.md#135-the-administration-siteaccess) |

## Risks in more detail

### Data loss (R01, R02)

The product's own guidance is blunt: "Test a restore on another server at least once; a backup you have never
restored is a hope" ([Operating a site](../../guides/operating-a-site.md#6-back-up-and-restore)). Make "restore
tested" a line in every launch and upgrade checklist, with a date and a name.

A backup covers **both** places where Exponential keeps state: the database, and the files (`var/`,
`settings/override/`, `settings/siteaccess/`, your extensions and designs). The files in `settings/override/` hold
database passwords, so the backup itself must be protected ([Security hardening](../security-hardening.md#backups-and-recovery)).

### Upgrades (R03, R04, R17, R18)

Most upgrade risk is known in advance, because the project writes it down: each changelog opens with an "Upgrading"
section, and the behavior change notes say what to check. The mitigation is a planning one: read before you
estimate. See [Release and upgrade planning](release-and-upgrade-planning.md).

### Security (R05 to R11, R30)

The help page [Security hardening](../security-hardening.md) walks through the threats one by one and ends with a
checklist; the installation book's [13.16 Go-live checklist](../../install/13-security-hardening.md#1316-go-live-checklist)
goes setting by setting. Put both into your launch gate. For watching after launch, the same page lists which log
or tab records what ("Watching: logs, the request classifier and the audit trail").

### Lock-in (R20)

These properties are documented and checkable:

- The code is GPL v2 or later ([LICENSE.md](https://github.com/se7enxweb/exponential/blob/main/LICENSE.md),
  [COPYRIGHT.md](https://github.com/se7enxweb/exponential/blob/main/COPYRIGHT.md)); "Using Exponential under the
  terms of the GNU GPL is free of charge" (README, quoted on [Who is 7x?](../who-is-7x.md#open-source-licenses)).
- The full Git history is public, and the recommended installation is Git clones ([Develop with Git](../develop-with-git.md)).
- Several databases and operating systems are supported ([Database support](../database-support.md),
  [OS support](../os-support.md)).
- The documentation is free under the GNU FDL ([Free documentation](../free-documentation.md)).

What the product cannot remove for you: lock-in to your own custom code. Keep it in your own Git repositories, with
documentation, and the handover is possible.

### Skills availability

An honest answer for your register: the sources do not state how many developers or agencies work with Exponential.
What they do document:

- The project's code base goes back to 1999, and the documentation, the training books and the encyclopedia of the
  earlier years remain available under free licenses ([Who is 7x?](../who-is-7x.md#the-community)).
- The community has a forum, a Telegram chat and issue trackers ([Community places](../who-is-the-exponential-foundation.md#community-places)).
- 7x offers services for new sites, migrations and support of existing sites ([Who is 7x?](../who-is-7x.md#how-to-get-involved)).
- The help pages are written so that a user can grow into a developer ([Develop with Git](../develop-with-git.md)).

Mitigations you control: budget time for onboarding, pair a new developer with an experienced one on the first
tasks, and keep the project's own knowledge in Git and in documentation.

### Knowledge concentration (R22)

Make reviews part of the work (every change is a pull request that a second person reads), keep settings in version
control, and keep a handover document from the first week, not the last ([Project templates](project-templates.md)).

## Risk review rhythm

| When | What to review | Who |
|---|---|---|
| Project start | Score every risk; assign owners | Project manager with the team |
| Each gate | Risks of the next phase; close what is past | Project manager |
| Before go-live | R01, R03, R05 to R11, R13, R14, R30 in particular | Project manager, administrator |
| Weekly after go-live | The weekly checklist of [Operating a site](../../guides/operating-a-site.md#10-a-weekly-checklist) | Administrator |
| Each upgrade | R03, R04, R17, R18 | Administrator, developer |

## A blank register to copy

```text
Risk register: <project name>          Version: <date>          Owner: <role>

Scales: Likelihood 1 low / 2 medium / 3 high; Impact 1 low / 2 medium / 3 high; Priority = L x I

| ID  | Risk                     | L | I | P | Mitigation (feature, page, procedure) | Owner  | Due     | Status |
|-----|--------------------------|---|---|---|---------------------------------------|--------|---------|--------|
| R01 | <risk>                   |   |   |   | <mitigation, link>                    | <role> | <date>  | open   |
| R02 |                          |   |   |   |                                       |        |         |        |

Closed risks:
| ID | Closed on | Why |
```

## Related pages

- [Release and upgrade planning](release-and-upgrade-planning.md), [Quality gates and acceptance](quality-gates-and-acceptance.md)
- [Accessibility, security and compliance checklists](accessibility-security-and-compliance-checklists.md)
- [Security hardening](../security-hardening.md), [Security and audit](../../guides/security-and-audit.md)
- [Operating a site](../../guides/operating-a-site.md)
- [Measuring a project: KPIs from the product's own logs and audit trail](kpis-from-logs-and-audit.md)
- [Project templates](project-templates.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
