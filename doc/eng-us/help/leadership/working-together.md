---
title: Working together: project managers, sales, editors, administrators and developers
audience: project-manager, sales-marketing, editor, admin, developer
category: leadership
order: 200
summary: How the five roles around an Exponential site hand work to each other: a handoff table, meeting cadences, the shared artefacts, and which part of the administration each role owns, with the policies behind it.
keywords: collaboration, handoff, roles, RACI, meetings, cadence, stand-up, review, artefacts, ownership, policies, project manager, sales, marketing, editor, administrator, developer, team
type: help
---
# Working together: project managers, sales, editors, administrators and developers

This page is for everyone who works on an Exponential site with other people: project managers, sales and marketing,
editors, administrators and developers. It answers four questions. Who hands what to whom? When do we meet, and
about what? Which documents and records do we share? And who owns which part of the administration?

Exponential itself already draws many of the lines between the roles: what a person sees and may do in the
administration is decided by **roles** made of **policies** ([Roles, policies and unactivated users](../../guides/roles-and-policies.md)).
This page builds the team's way of working on top of those lines, so that the process and the permissions say the
same thing.

## Contents

1. [The five roles at a glance](#the-five-roles-at-a-glance)
2. [The handoffs](#the-handoffs)
3. [Who owns what in the administration](#who-owns-what-in-the-administration)
4. [Shared artefacts](#shared-artefacts)
5. [Meeting cadences](#meeting-cadences)
6. [Worked example: from a sales promise to a published page](#worked-example-from-a-sales-promise-to-a-published-page)
7. [Worked example: an error on the live site](#worked-example-an-error-on-the-live-site)
8. [When roles disagree](#when-roles-disagree)
9. [Checklists per role](#checklists-per-role)

## The five roles at a glance

| Role | Works mostly in | Answers for | Starts reading at |
|---|---|---|---|
| **Project manager** | The plan, the issue tracker, the help | Scope, schedule, acceptance, risks | [Project managers: start here](project-managers-start-here.md) |
| **Sales and marketing** | Customer conversations, demos, the public story | What is promised, and that it is true | [Sales and marketing: start here](../business/sales-and-marketing-start-here.md) |
| **Editor** | **Dashboard**, **Content structure**, **Media**, **Layouts** | The content and its quality | [Getting started](../getting-started.md), [A tour of the administration](../admin-tour.md) |
| **Administrator** | **Users**, **Setup**, the server | Accounts, access, operation, security | [Operating a site](../../guides/operating-a-site.md), [Security and audit](../../guides/security-and-audit.md) |
| **Developer** | Git clones, extensions, templates, settings | The code, its tests and its releases | [Develop with Git](../develop-with-git.md) |

One person may hold several roles on a small project. That is fine; the handoffs below still happen, they just
happen inside one head. Write them down anyway: the next person will need them.

> **Note**
> "Role" on this page means a job on the team. In the administration a **role** is also a technical object, a set of
> policies assigned to users or groups. A new installation has, among others, the roles **Administrator** (which
> holds every policy), **Editor** and **Anonymous** ([Roles, policies and unactivated users](../../guides/roles-and-policies.md)).
> Map your team roles onto such roles early; [Planning an Exponential project](planning-an-exponential-project.md)
> shows how.

## The handoffs

A handoff is the moment work moves from one role to another. Most project trouble starts at a handoff where
something was assumed instead of written down. The table lists the handoffs that happen on almost every
Exponential project, with what must travel along.

| From | To | What is handed over | What must travel with it | Where it is recorded |
|---|---|---|---|---|
| Sales | Project manager | A customer need or a promise made in a demo | The exact wording promised, the feature it relies on and the help page that describes it | The project's issue tracker or charter ([Project templates](project-templates.md)) |
| Project manager | Developer | A scoped piece of work | The need, the acceptance criteria, the content classes and roles involved | An issue; later a branch and a pull request |
| Project manager | Editor | A content task (new section, migration, translation) | The content model, the deadline, who approves | The issue and, in the administration, the folder or section it belongs to |
| Editor | Project manager | "Content is ready" | Which items, in which languages, whether they are published or waiting for approval | **My drafts** and **My pending items** ([My drafts and my pending items](../../guides/drafts-and-pending.md)) |
| Developer | Administrator | A change to deploy | The branch or tag, any settings to change, any database update, which caches to clear | The pull request description and the changelog ([Deploying](../../guides/deploying.md)) |
| Administrator | Editor | A new account or new access | The user, the group, the role and its limitation (section or subtree) | **Users > Roles and policies**; the change is recorded in the audit trail ([The audit trail](../../features/6.0/audit-trail.md)) |
| Editor or visitor | Administrator | "Something went wrong" | The error reference (`ERR-...`), the time, the address, the steps | [When something goes wrong](../when-something-goes-wrong.md) |
| Administrator | Developer | A bug that is in the code, not the setup | The reference, the log lines, the system information report | [Finding an error by its reference](../../guides/finding-an-error-by-its-reference.md), [System information](../../guides/system-information.md) |
| Developer | The community | A fix or improvement for everyone | A pull request to the project repository | [Develop with Git](../develop-with-git.md#contribute-a-change-with-a-pull-request) |
| Project manager | Sales and marketing | A finished milestone, a launch, a story to tell | What was delivered, which features it used, what the customer agreed may be said | [Writing case studies and content marketing](../business/case-studies-and-content-marketing.md) |

### Rules that make handoffs work

1. **Link, do not paraphrase.** When a promise rests on a feature, link the help page of that feature. The help is
   the shared source of truth for what the product does; every page is in the installation's own `doc/` directory
   and shown at `/exphelp/dashboard` ([The help system](../../features/6.0/help-system.md)).
2. **Name the role and the policy.** "The editor can do it" is not enough. Say which role, with which policy and
   which limitation. If nobody can name it, the administrator decides before the work starts.
3. **One owner per handoff.** The sender owns the handoff until the receiver has confirmed it, in writing.
4. **Never send secrets along.** Passwords, keys and personal data of customers or users do not belong in issues,
   chat or e-mail. The help's **Report an error** page and **Setup > System information** produce summaries that
   leave them out ([When something goes wrong](../when-something-goes-wrong.md)).

## Who owns what in the administration

The administration shows each person only what their roles allow: "Admin links already follow permissions, so a user
never sees a link to something that would be refused" ([Security and audit](../../guides/security-and-audit.md)).
The table below lists the places a team usually divides between its roles, the policy that opens each one where the
documentation names it, and who holds it in a **new** installation. The last column is a suggestion for your team.

| Place in the administration | Policy | Holder in a new installation | Suggested team owner | Read more |
|---|---|---|---|---|
| **Dashboard**, **Content structure**, **Media** | `content/*` policies of the role | Depends on the role | Editors | [A tour of the administration](../admin-tour.md) |
| **Layouts** tab and the layouts editor | Layouts policies of the role | Depends on the role | Editors, with a designer | [The layouts editor](../layouts-editor.md) |
| **Users > Roles and policies** | the `role` module | Administrator | Administrator | [Roles, policies and unactivated users](../../guides/roles-and-policies.md) |
| **Users > Unactivated users** | `user/activation` | Administrator | Administrator | same |
| **Users > Check user accounts** | `user/hygiene` | Administrator | Administrator | [Check user accounts](../../features/6.0/user-hygiene.md) |
| **Audit** tab | `audit/read`, `audit/manage` | "Only Administrator holds them in a new installation" | Administrator; `audit/read` for an auditor | [The audit trail](../../features/6.0/audit-trail.md) |
| **Setup > Log files** and the error digest | `setup/logs`, `setup/managelogs`, `setup/unmasklogs` | "Administrator only in a new installation" | Administrator | [The log viewer](../../features/6.0/log-viewer.md) |
| **Setup > Cronjobs** | `setup/managecronjobs` | Administrator | Administrator | [Cronjobs console](../../features/6.0/cronjobs-console.md) |
| **Setup > System information** | `setup/system_info` | Administrator | Administrator; shared with developers as a report | [System information](../../guides/system-information.md) |
| **Setup > Upgrade check** | (administration page `/setup/systemupgrade`) | Administrator | Administrator, with the developer before an upgrade | [The upgrade check](../../guides/upgrade-check.md) |
| **Setup > Content finder** | `contentfinder/search` and others | "No role but Administrator" | Administrator; can be given to senior editors | [Content finder](../../features/6.0/content-finder.md) |
| Maintenance mode | (administration page and `bin/php/maintenance.php`) | Administrator | Administrator, on the project manager's go | [Maintenance mode](../../guides/maintenance-mode.md) |
| The **Intelligence** tab (Exponential Intelligence) | `expai/write`, `expai/vector` and others | "held only by the Administrator role in a new installation" | Editors, once the project decides to use it | [Exponential Intelligence](../../features/6.0/extensions/expai.md) |
| The help (`/exphelp/dashboard`) | `exphelp/read` | Administrator and Editor | Everyone on the team | [The help system](../../features/6.0/help-system.md) |
| Code, templates, settings files | (outside the administration: Git) | not applicable | Developers | [Develop with Git](../develop-with-git.md) |

Sources for the quoted holders: the pages linked in the last column and the README section "Exponential
Intelligence" (`README.md`).

> **Tip**
> Give project managers and sales an account with a **read** role that includes `exphelp/read`, and with
> `audit/read` if they report on activity ([Measuring a project](kpis-from-logs-and-audit.md)). They can then follow
> the work in the real system instead of in screenshots. Make a new role for that; "never edit the Administrator role
> to give a colleague a bit more; make a new role" ([Security and audit](../../guides/security-and-audit.md)).

### Owning a place means

- You decide **who else** gets the policy for it, and you ask the administrator to assign it.
- You **watch** it: the editor watches **My pending items**, the administrator watches the error digest and the
  audit alerts, the developer watches the CI checks of the pull requests.
- You are the person others ask first. Write your name next to each row in your project's RACI
  ([Project templates](project-templates.md)).

## Shared artefacts

These are the documents and records the whole team shares. Most of them already exist in the product or the
repository; the team only has to agree to use them.

| Artefact | What it is | Kept where | Owner | Read by |
|---|---|---|---|---|
| The content model | The content classes, their attributes, sections and object states | In the administration (**Setup > Classes**, sections); described in a workshop document | Project manager with a developer | Everyone |
| The role matrix | Which team role gets which administration role, policy and limitation | A table in the charter; the real roles in **Users > Roles and policies** | Administrator | Project manager, editors |
| The help | The product's own documentation, free to copy and adapt | `doc/` of the installation, at `/exphelp/dashboard` | The project (everyone may improve it) | Everyone |
| The issue tracker | Bugs, tasks and stories | Your project's tracker; bugs in the software in the public trackers ([When something goes wrong](../when-something-goes-wrong.md)) | Project manager | Everyone |
| Branches and pull requests | Every code change, reviewed | Git; the project repository on GitHub | Developers | Project manager, administrator |
| The changelog | What changed in each release | `doc/changelogs/` ([Changelogs](../../../changelogs/README.md)); your own for your site | Developers | Everyone |
| Workflows | Approval before publishing and other processes | **Setup > Workflows** ([Workflows](../../guides/workflows.md)) | Administrator, on the project manager's decision | Editors |
| Layouts and rules | Which page gets which arrangement of blocks | **Layouts** tab ([The layouts editor](../layouts-editor.md)) | Editors | Project manager, sales (for demos) |
| The audit trail | Who did what, tamper-evident | **Audit** tab ([The audit trail](../../features/6.0/audit-trail.md)) | Administrator | Auditor, project manager |
| System information report | What the site runs on, without secrets | **Setup > System information**, *Copy report as text* | Administrator | Developers, support |
| Upgrade check report | File and database consistency before an upgrade | **Setup > Upgrade check**, downloadable | Administrator | Developer, project manager |

> **Warning**
> Settings with secrets (database passwords, provider keys) live in `settings/override/`. They are never pasted
> into an issue, a chat or a shared document. Exponential Intelligence's README says the same about its provider key:
> "never into a committed file".

## Meeting cadences

The product does not prescribe meetings; these are suggestions that fit the handoffs above. Keep each meeting
short and end it with written decisions in the tracker.

| Meeting | When | Who | Agenda | Input from the product |
|---|---|---|---|---|
| **Daily check-in** (10 minutes) | Every working day during build and launch | Project manager, developers, lead editor | Blockers, handoffs due today | Open pull requests and their CI status ([Continuous integration](../../bc/6.0/ci.md)) |
| **Content stand-up** (15 minutes) | Twice a week during content entry | Project manager, editors | What is drafted, pending, published | **My drafts**, **My pending items**, the dashboard numbers ([A tour of the administration](../admin-tour.md#the-dashboard)) |
| **Weekly operations review** (30 minutes) | Weekly, from launch on | Administrator, project manager, a developer | Errors, security events, cronjobs, backups | The error digest ([The log viewer](../../features/6.0/log-viewer.md)), audit alerts, the weekly checklist of [Operating a site](../../guides/operating-a-site.md) |
| **Sprint or milestone review** (60 minutes) | Every two to four weeks | Everyone, including sales | What was delivered, shown in the real administration | A demo on your own installation ([Demo scripts](../business/demo-scripts.md)) |
| **Release planning** (45 minutes) | When a new Exponential 6.0.x release or an extension release appears | Project manager, administrator, developer | Upgrade or not, when, with what checks | The changelog, the BC notes, the upgrade check ([Release and upgrade planning](release-and-upgrade-planning.md)) |
| **Pipeline sync** (30 minutes) | Every two weeks | Sales and marketing, project manager | Promises in flight, demo requests, stories to tell | [Positioning and value proposition](../business/positioning-and-value-proposition.md) |
| **Community hour** (60 minutes) | Monthly | Anyone who wants to | Bugs to report, fixes to send back, help pages to improve | [Becoming a leader in the Exponential community](becoming-a-community-leader.md) |

### A template for meeting notes

```text
Meeting:      <name>            Date: <yyyy-mm-dd>
Present:      <roles, not personal data you do not need>
Decisions:
  1. <decision>  - owner: <role>  - due: <date>  - recorded in: <issue link>
Handoffs agreed:
  - <from role> -> <to role>: <what>, with <what must travel along>
Open questions:
  - <question>  - who answers: <role>
Product pages referred to:
  - <help page or administration page>
```

## Worked example: from a sales promise to a published page

A prospect asks during a demo: "Can our editors build campaign pages themselves, without a developer?"

1. **Sales** answers only what the help says: Exponential Layouts lets editors arrange blocks in zones, and rules
   decide which pages get a layout, "so editors rearrange pages without touching templates" (`README.md`,
   "Exponential Layouts"). Sales notes the promise and links [The layouts editor](../layouts-editor.md).
2. **Sales hands over** to the project manager: the promise in the customer's words, the link, and the question the
   customer really has (campaign pages per period).
3. **The project manager** checks the rule conditions in the README ("siteaccess, content class, query parameter,
   time") and writes an issue: "Campaign layout by time rule", with acceptance criteria.
4. **The developer** checks that the Layouts extensions are active on the project's installation (they are required
   in `composer.json`, but each site switches them on, see `README.md`, "How to enable it"), adds any block type the
   design needs, and opens a pull request.
5. **The administrator** deploys, and gives the campaign editors a role with the Layouts policies they need.
6. **The editor** builds the layout as a draft, previews it, and publishes it ([A tour of the administration](../admin-tour.md#in-action-the-layouts-editor)).
7. **The project manager** accepts it against the criteria and tells sales it is delivered; **marketing** asks the
   customer whether it may be told as a story.

## Worked example: an error on the live site

1. An **editor** gets *Something went wrong on our side* with a reference `ERR-...`. They note the reference, the
   time, the address and the steps, and tell the administrator ([When something goes wrong](../when-something-goes-wrong.md)).
2. The **administrator** finds the request by its reference ([Finding an error by its reference](../../guides/finding-an-error-by-its-reference.md))
   and reads the log around it in **Setup > Log files**.
3. If it is the site's own setup, the administrator fixes it and tells the editor. If it is in the software, the
   administrator hands it to a **developer** with the log lines and the system information report.
4. The **developer** files it in the right public tracker, or sends the fix as a pull request; the help's
   **Report an error** page (`/exphelp/report`) prepares the issue.
5. The **project manager** records the incident, its time to resolution and the follow-up in the operations review.
6. If it is a **security** problem, nobody files it publicly: it goes privately to the address in `SECURITY.md`.

## When roles disagree

| Disagreement | Who decides | On what basis |
|---|---|---|
| Sales promised something the product does not document | Project manager, with sales | The help page. If no page says it, it is not promised; it becomes a scoped change or is withdrawn. |
| An editor needs access the administrator will not give | Administrator, with the project manager | Least privilege: a new role with a limitation, never a change to the Administrator role ([Security and audit](../../guides/security-and-audit.md)) |
| A developer wants to change the kernel instead of an extension | Developer lead, with the project manager | Changes to the kernel go back to the project as pull requests; site-specific work goes into the site's own extension ([Extensions](../../guides/extensions.md)) |
| Launch date versus open defects | Project manager | The acceptance checklist ([Quality gates and acceptance](quality-gates-and-acceptance.md)) |
| Whether to upgrade now | Administrator and project manager | The changelog, the BC notes and the upgrade check ([Release and upgrade planning](release-and-upgrade-planning.md)) |

## Checklists per role

### Project manager

- [ ] Every team role is mapped to an administration role, with its limitations, in the charter.
- [ ] Every open handoff has an owner and a date in the tracker.
- [ ] Every promise made to the customer links a help page.
- [ ] The meeting cadence is agreed and in everyone's calendar.

### Sales and marketing

- [ ] I can name the help page behind every claim I make.
- [ ] I hand each promise to the project manager in writing, the same day.
- [ ] I demo only on an installation my team controls ([Demo scripts](../business/demo-scripts.md)).
- [ ] I write the product names as the brand rules say ([Brand and naming rules](../business/brand-and-naming-rules.md)).

### Editor

- [ ] I know which sections and subtrees my role covers.
- [ ] I keep unfinished work as drafts and check **My pending items** before each content stand-up.
- [ ] When an error appears, I pass on the reference, the time, the address and the steps.

### Administrator

- [ ] Roles follow the role matrix; no one got extra access by editing the Administrator role.
- [ ] The `frequent` cronjob group runs, so the audit trail indexes and alerts ([The audit trail](../../features/6.0/audit-trail.md)).
- [ ] The weekly checklist of [Operating a site](../../guides/operating-a-site.md) is done and reported.

### Developer

- [ ] Every change is a branch and a pull request; commit subjects start with `Added:`, `Updated:`, `Fixed:`,
      `Removed:` or `Renamed:` (`CONTRIBUTING.md`).
- [ ] Every deploy note names settings, database updates and caches.
- [ ] Fixes that help everyone go back to the project ([Working with the community](working-with-the-community.md)).

## Related pages

- [Project managers: start here](project-managers-start-here.md)
- [Sales and marketing: start here](../business/sales-and-marketing-start-here.md)
- [Project templates: charter, RACI, checklists, launch and handover](project-templates.md)
- [Running a team on Git](running-a-team-on-git.md)
- [The leadership reading path](leadership-reading-path.md)
- [Roles, policies and unactivated users](../../guides/roles-and-policies.md)
- [A tour of the administration](../admin-tour.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
