---
title: Planning an Exponential project
audience: project-manager
category: leadership
order: 30
summary: The phases of an Exponential project from discovery to operation, project roles mapped to the real roles and policies, and a requirements table linking each need to the feature that answers it.
keywords: project plan, phases, discovery, content model, build, migration, content entry, QA, launch, operate, roles, policies, requirements, RACI, planning, project manager
type: help
---
# Planning an Exponential project

This page is for project managers who plan a website or application project on Exponential 6. It answers: which
phases a project goes through, what each phase delivers and how you know it is done, how the people in your project
map to the roles and policies of the administration, and how to turn requirements into the features and help pages
that answer them. It contains no hours or prices; your team estimates those with
[Estimating and scoping with extensions](estimating-and-scoping-with-extensions.md).

## Contents

1. [The phases at a glance](#the-phases-at-a-glance)
2. [Phase 1: discovery](#phase-1-discovery)
3. [Phase 2: content model](#phase-2-content-model)
4. [Phase 3: build](#phase-3-build)
5. [Phase 4: content migration and entry](#phase-4-content-migration-and-entry)
6. [Phase 5: quality assurance](#phase-5-quality-assurance)
7. [Phase 6: launch](#phase-6-launch)
8. [Phase 7: operate](#phase-7-operate)
9. [People and the roles of the administration](#people-and-the-roles-of-the-administration)
10. [Requirements gathering: need, feature, page](#requirements-gathering-need-feature-page)
11. [A worked example](#a-worked-example)

## The phases at a glance

| Phase | Main question | Deliverable | Led by | Exit check |
|---|---|---|---|---|
| 1. Discovery | What do we build, for whom, on what? | Charter, requirement list, platform decision | Project manager | Sponsor signs the charter |
| 2. Content model | What types of content exist, and who may do what with them? | Class list, section and state plan, role plan | Project manager with editors and developers | Workshop results approved |
| 3. Build | Does the site do what was agreed? | Classes, templates, extensions, settings in Git | Developers | Quality gates pass |
| 4. Content migration and entry | Is the content in, complete and correct? | Migrated and entered content | Editors, developers for imports | Content checks clean |
| 5. Quality assurance | Is it accessible, secure and fast enough? | Test reports, signed acceptance list | Project manager with testers | Acceptance signed |
| 6. Launch | Can we switch it on safely? | Go-live checklist done, site live | Administrator | Smoke test passed |
| 7. Operate | Does it stay healthy and up to date? | Operations routine, update plan | Administrator | Handover accepted |

The phases overlap in practice: content entry often starts while the build is still running, and a first
accessibility check belongs in the build, not only in QA. Keep the exit checks anyway; they are what a steering
committee asks about.

## Phase 1: discovery

**Goal.** A charter that names the product, the scope, the people, the platform and the risks.

Steps:

1. **Choose the product of the family.** Exponential 6, a Platform distribution, or Exponential Basic
   ([What Exponential is](what-exponential-is.md#choosing-a-product-for-a-project)).
2. **New site or upgrade?** A site built on an older line is upgraded or migrated; the help has a chapter for each
   starting point ([Upgrading](../../guides/upgrading.md), chapters
   [14](../../install/14-migrating-from-4x.md), [15](../../install/15-migrating-from-5x-legacy.md) and
   [16](../../install/16-migrating-from-ez-platform-and-ibexa.md) of the installation book).
3. **Count sites and languages.** One installation can serve several sites and languages
   ([Planning multisite and multilingual projects](multisite-and-multilingual-planning.md)).
4. **Decide the platform** with your administrator
   ([Choosing hosting, database and operating system](choosing-hosting-database-and-os.md)).
5. **Collect requirements** in the table of [this page's requirements section](#requirements-gathering-need-feature-page).
6. **List the extensions** you will use and the ones you will build
   ([Estimating and scoping with extensions](estimating-and-scoping-with-extensions.md)).
7. **Start the risk register** ([Risk management](risk-management.md)).
8. **Write the charter** from [Project templates](project-templates.md).

Checklist:

- [ ] A demonstration installation exists for the team to look at (`./console exp:install`,
      [Installing Exponential in one command](../../features/6.0/install-in-one-command.md)).
- [ ] The sponsor has seen the administration, not only slides.
- [ ] The product, the database, the web server and the install method are written in the charter.
- [ ] The source of the content (old site, spreadsheets, new writing) is known.
- [ ] Accessibility, security and data protection requirements are listed
      ([Checklists](accessibility-security-and-compliance-checklists.md)).

## Phase 2: content model

**Goal.** An agreed list of content classes, their attributes, where content lives in the tree, which sections and
states exist, and who may do what.

The method is on [Running content modeling workshops](content-modeling-workshops.md). The terms come from
[The content model and editing content](../../guides/content-model-and-editing.md):

| Decide | Product concept | Read |
|---|---|---|
| The types of content | Content classes and their attributes (datatypes) | [Content model, section 1](../../guides/content-model-and-editing.md) |
| Where content lives | The content tree; one object can have several locations | same |
| Which groups of content have different permissions | Sections | [Sections](../../guides/sections.md) |
| Which stages content passes through | Object states | [Object states](../../guides/object-states.md) |
| Who approves before publishing | Workflows with an Approve event | [Workflows](../../guides/workflows.md) |
| Which languages | Content languages per siteaccess | [Content languages](../../guides/content-languages.md) |
| What editors arrange themselves | Layouts, zones and blocks | [The layouts editor](../layouts-editor.md) |

Exit check: the class list, the section plan and the role plan are written down and approved by the lead editor
and the lead developer.

## Phase 3: build

**Goal.** A working site in version control, built with the team's quality gates.

What developers build, and where the help explains it:

| Work | Read |
|---|---|
| Install the kernel and extensions as Git clones | [Develop with Git](../develop-with-git.md) |
| Classes, attributes and objects | [The content model and editing content](../../guides/content-model-and-editing.md) |
| Templates and the design | [Templates and design](../../guides/templates-and-design.md) |
| Extensions of your own, generated or by hand | [Extensions](../../guides/extensions.md), [RAD tools](../../features/6.0/rad-tools.md) |
| Integrations | [REST API v1 and v2](../../guides/rest-api-v1-and-v2.md) |
| Serving and deploying | [Deploying](../../guides/deploying.md), [Chapter 8](../../install/08-serving-the-site.md) |

Project manager checklist:

- [ ] Every change goes through Git, on a branch, with a pull request ([Running a team on Git](running-a-team-on-git.md)).
- [ ] Settings that must not be changed from the administration are versioned; `ManagedByDeployment` can keep the
      administration from writing them ([Configuration managed by the deployment](../../features/6.0/managed-by-deployment.md)).
- [ ] Secrets in `settings/override` stay out of version control ([Chapter 13](../../install/13-security-hardening.md)).
- [ ] The quality gates are agreed and run ([Quality gates and acceptance](quality-gates-and-acceptance.md)).
- [ ] A test or staging installation exists besides development.

## Phase 4: content migration and entry

**Goal.** All content in, in the right classes, languages, sections and states.

| Source of content | Tools the help documents |
|---|---|
| Typed in by editors | [Creating and editing content](../editing-content.md), [Images, media and uploads](../media-and-uploads.md) (many files at once) |
| Spreadsheets or files | `xrowextract`: export, import and move content as files ([xrowextract](../../features/6.0/extensions/xrowextract.md)) |
| Another installation | Packages: classes, content and extensions moved as `.ezpkg` files ([Packages](../../guides/packages.md)) |
| An older Exponential site | The upgrade and migration chapters ([Upgrading](../../guides/upgrading.md)) |
| An external system | REST API ([REST API v1 and v2](../../guides/rest-api-v1-and-v2.md)) |
| Writing help | Exponential Intelligence writes drafts that editors review ([expai](../../features/6.0/extensions/expai.md)) |

Large removes, copies and moves run as background content jobs, in batches that resume after an interruption
([Content jobs](../../features/6.0/content-jobs.md)).

Checklist:

- [ ] A content inventory with an owner per area.
- [ ] A test import on the test installation before the real one.
- [ ] Content checks run after the import: unreferenced objects, broken relations, storage files
      ([Content checks](../../features/6.0/content-checks.md)).
- [ ] Alternative text for images: image fields can require it
      ([Content model, section 9](../../guides/content-model-and-editing.md)).
- [ ] Translations complete for every language a siteaccess shows.

## Phase 5: quality assurance

Use [Quality gates and acceptance](quality-gates-and-acceptance.md) for the gates and
[Accessibility, security and compliance checklists](accessibility-security-and-compliance-checklists.md) for the
reviews. Add a performance check: the `exp:benchmark` command times pages
([Benchmark](../../features/6.0/benchmark.md)), and section 2.10 of the requirements chapter explains why the
documentation gives few hard numbers ([Memory, CPU and disk](../../install/02-requirements.md#210-memory-cpu-and-disk)).

## Phase 6: launch

The administrator works through the go-live checklist of the installation book
([13.16 Go-live checklist](../../install/13-security-hardening.md#1316-go-live-checklist)) and the hardening
checklist of this help ([Security hardening](../security-hardening.md#checklist)). As project manager you check
that each line has a name and a date, and you prepare:

- [ ] A launch window and a rollback plan (backup taken **and** restored once).
- [ ] Maintenance mode for the switch, if content must freeze ([Maintenance mode](../../features/6.0/maintenance-mode.md)).
- [ ] The people who watch the logs on launch day ([Log viewer](../../features/6.0/log-viewer.md)).
- [ ] The announcement to editors: what changes for them, and where help is.

## Phase 7: operate

A site is never finished. Hand over with:

- [ ] The weekly checklist of [Operating a site](../../guides/operating-a-site.md) and the first-week checklist of
      [chapter 10](../../install/10-after-installing.md#1015-a-checklist-for-the-first-week).
- [ ] Cronjobs running, including the `frequent` group that the audit trail and workflows need
      ([Cronjobs](../../guides/cronjobs.md), [Audit trail](../../features/6.0/audit-trail.md)).
- [ ] An update routine: who watches releases, how updates are tested
      ([Release and upgrade planning](release-and-upgrade-planning.md)).
- [ ] The handover template of [Project templates](project-templates.md).

## People and the roles of the administration

A **role** is a set of **policies**; a policy allows one module or one function, possibly only in some sections,
classes, subtrees, languages or siteaccesses. A role does nothing until it is assigned to users or user groups
([Roles, policies and unactivated users](../../guides/roles-and-policies.md)).

What a new installation brings, according to the sources:

| Role | What the sources say it has |
|---|---|
| **Administrator** | Every policy ("full access"); the only role with `audit/read` and `audit/manage` in a new installation ([Audit trail](../../features/6.0/audit-trail.md)) |
| **Editor** | Editing rights, `exphelp/read` to read the help ([The help system](../../features/6.0/help-system.md)), `ezjscore/call` for `ezjscnode` and `oauth/authorize` on new installations ([Roles and policies](../../guides/roles-and-policies.md)) |
| **Anonymous** | What visitors who are not signed in may do; `content/read` and `content/pdf` limited to the section Standard ([Sections](../../guides/sections.md)) |

Map the people of your project to roles. Prefer user groups to single users: every member of a group, now and later,
gets the role ([Roles and policies, section 5](../../guides/roles-and-policies.md)).

| Project role | Typical role in the administration | Policies to discuss | Notes |
|---|---|---|---|
| Sponsor | none, or a read-only role | `content/read` | Often needs only the site and reports |
| Project manager | Editor, or a role of your own | `content/read`, `exphelp/read` | Reading the audit trail needs `audit/read`, which only Administrator has by default; ask for it on purpose |
| Lead editor | Editor plus approval rights | `content/*` in the agreed sections, approval in the workflow | Usually the approver of the Approve event |
| Editor | Editor | `content/create`, `content/edit`, limited by section or class | Limit by section or subtree where teams own areas |
| Translator | A role of your own | `content/translate` or `content/edit` per language | The language limitation is in the policy ([Content languages](../../guides/content-languages.md)) |
| Administrator | Administrator | full access | One personal account per person; `admin` retired as a sign-in ([Security hardening](../security-hardening.md)) |
| Developer | Administrator on development and test; often none on production | | Changes reach production through Git, not the administration |
| Tester | A copy of the role being tested | the role under test | Test with the real roles, not with Administrator |

> **Warning**
> A policy over every module gives full access, and a policy over the `role` module lets its users change roles and
> so give themselves any access. The role list marks the first in red and the second in amber. With
> `PreventPrivilegeEscalation=enabled` (the default) nobody can grant more than they hold
> ([Roles and policies](../../guides/roles-and-policies.md)).

## Requirements gathering: need, feature, page

Write each requirement as a need, then find the feature that answers it. If no feature answers it, it is build work
or an extension ([Estimating and scoping with extensions](estimating-and-scoping-with-extensions.md)).

| Need | The feature that answers it | Read |
|---|---|---|
| Editors write, a lead editor approves before anything goes live | Workflow with an Approve event, connected by a trigger | [Workflows, section 3](../../guides/workflows.md) |
| Go back to an earlier text | Versions of an object | [Publishing, versions and the trash](../publishing-and-versions.md), [Content history](../../guides/content-history.md) |
| Different teams own different areas | Sections, and roles limited by section or subtree | [Sections](../../guides/sections.md) |
| Content passes stages (draft, review, legal clearance) | Object states with policies limited by state | [Object states](../../guides/object-states.md) |
| A members-only area | A section, a role for members | [Sections, 13.1 A members area](../../guides/sections.md) |
| Editors arrange landing pages themselves | Exponential Layouts | [The layouts editor](../layouts-editor.md) |
| Several sites from one installation | Siteaccesses, multisite settings | [Multisite and multilingual planning](multisite-and-multilingual-planning.md) |
| Content in several languages | Content languages, `SiteLanguageList` per siteaccess | [Content languages](../../guides/content-languages.md) |
| Right-to-left languages | `dir="rtl"` in content and administration | [Right to left](../../features/6.0/right-to-left.md) |
| Help writing drafts and translations | Exponential Intelligence | [expai](../../features/6.0/extensions/expai.md) |
| Classify content with tags | `eztags` | [eztags](../../features/6.0/extensions/eztags.md) |
| A newsletter | `cjw_newsletter` | [cjw_newsletter](../../features/6.0/extensions/cjw_newsletter.md) |
| Selling products | The built-in shop, PayPal through `ezpaypal` | [ezpaypal](../../features/6.0/extensions/ezpaypal.md), [Order receipts](../../features/6.0/order-receipts.md) |
| Notifications of new content to subscribers | Notifications | [Notifications for administrators](../../guides/notifications-administrator.md) |
| Respect what people chose to receive by mail | The mail gate, consent log and suppression list | [13.13 Mail: consent and suppression](../../install/13-security-hardening.md#1313-mail-consent-and-suppression) |
| Two-step sign-in for staff | `sevenx_authentication_2fa` | [Two-step sign-in](../../guides/two-factor-login.md) |
| Know who changed what | The audit trail | [Audit trail](../../features/6.0/audit-trail.md) |
| Other systems read or write content | REST API v1 and v2 | [REST API](../../guides/rest-api-v1-and-v2.md) |
| PDF versions of content | PDF export | [PDF export](../../features/6.0/pdf-export.md) |
| RSS feeds | RSS feeds | [RSS feeds](../../guides/rss-feeds.md) |
| Search on the site | The built-in search | [Searching](../searching.md) |
| Spam protection on forms | `recaptcha`, `hcaptcha` | [Extensions](../../features/6.0/extensions/README.md) |
| SEO metadata and sitemaps | `xrowmetadata`, `bcgooglesitemaps` | [xrowmetadata](../../features/6.0/extensions/xrowmetadata.md) |
| Clean up unused images and broken links | Content checks | [Content checks](../../features/6.0/content-checks.md) |

## A worked example

A regional organization wants one public site in English and German, a members area, and an approval step for
news. The planning, step by step:

1. **Product.** Exponential 6; no Symfony requirement.
2. **Sites and languages.** One public siteaccess per language (`site_en`, `site_de`) and the administration, as
   chapter 10 describes ([10.14 Multi-language sites](../../install/10-after-installing.md#1014-multi-language-sites)).
3. **Platform.** A single machine is enough for the expected editorial team, so SQLite is considered; the administrator
   checks the limits listed on [Database support](../database-support.md) before deciding.
4. **Content model.** Workshop results: classes Article, News, Event, Folder; a section "Members area"; a state group
   "review" is not needed because the approval workflow covers it.
5. **Roles.** Editors (Editor role limited to section Standard), Members (a new role, `content/read` on section
   Members area), Approvers (lead editors).
6. **Requirements.** Members area: [Sections 13.1](../../guides/sections.md). Approval:
   [Workflows 3](../../guides/workflows.md). German: [Content languages 11](../../guides/content-languages.md).
7. **Risks.** Translation capacity; the workflow cronjob must run, "without that cronjob, approved content is never
   published" ([Workflows](../../guides/workflows.md)).

## Related pages

- [Project managers: start here](project-managers-start-here.md)
- [Project templates](project-templates.md)
- [Running content modeling workshops](content-modeling-workshops.md)
- [Working together](working-together.md)
- [Roles, policies and unactivated users](../../guides/roles-and-policies.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
