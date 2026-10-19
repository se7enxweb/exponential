---
title: Intranet planning
audience: businesses, project-manager, admin
category: business-solutions
order: 10
summary: What an organization decides before building an intranet on Exponential - who it is for, how it is structured, who owns which part, how staff sign in, and which questions decide cost and effort.
keywords: intranet planning, staff portal, requirements, structure, owners, departments, sign-in, governance, scope, budget, RequireUserLogin, siteaccess
type: help
---
# Intranet planning

This page is for managers and project leads who are about to start an intranet. It lists the decisions that shape
the project, explains which Exponential feature answers each one, and links to the pages where the work is
described. Read it together with the people who will own the intranet once it is live.

## Contents

1. [Start with the purpose](#start-with-the-purpose)
2. [The decisions that shape the project](#the-decisions-that-shape-the-project)
3. [How an intranet is built in Exponential](#how-an-intranet-is-built-in-exponential)
4. [Owners and governance](#owners-and-governance)
5. [Questions that decide effort](#questions-that-decide-effort)
6. [A planning worksheet](#a-planning-worksheet)
7. [Related pages](#related-pages)

## Start with the purpose

An intranet that nobody owns becomes a store of outdated files. Before choosing features, write down in one or two
sentences what the intranet is for. Typical purposes are:

- one place for company news and announcements;
- the current version of policies, handbooks and forms;
- department pages that each department keeps up to date itself;
- a document library that replaces mail attachments and shared drives;
- a starting point for internal tools and links.

Each purpose leads to a page of this section. Keep the list short for the first release; Exponential lets you add
sections, classes and roles later without rebuilding the site
([Designing your content model](../../creators/website-creators/designing-your-content-model.md)).

## The decisions that shape the project

| Decision | Options | Where it is explained |
|---|---|---|
| Who may open the intranet | all staff; staff and some contractors | [Departments, roles and sections](departments-roles-and-sections.md) |
| How staff sign in | local accounts; company directory (LDAP); two-step sign-in on top | [Two-factor and sign-in](two-factor-and-sign-in.md), [Directory sign-in and LDAP](directory-sign-in-and-ldap.md) |
| Who may publish | a central team; every department for its own area | [Departments, roles and sections](departments-roles-and-sections.md) |
| Whether texts are approved first | publish at once; approval before publishing; review stages | [Approval workflows](approval-workflows.md) |
| What happens to documents | files as content objects with permission-checked downloads | [Documents and file libraries](documents-and-file-libraries.md) |
| How staff find things | built-in search; Solr for large collections | [Intranet search](intranet-search.md) |
| How staff hear about changes | notifications, digests, a newsletter | [Notifications for staff](notifications-for-staff.md) |
| Where it is reachable | only from the office network; from anywhere with a sign-in | [Restricting the administration: network restrictions](../../hosting/security/restricting-the-administration.md#network-restrictions) |
| Languages | one; several | Multilingual |

## How an intranet is built in Exponential

An intranet is a siteaccess, the Exponential term for one site with its own settings and address
([10.2 Siteaccesses and site addresses](../../../install/10-after-installing.md#102-siteaccesses-and-site-addresses)).
Three things make it private:

1. **Every request needs a sign-in.** `site.ini [SiteAccessSettings] RequireUserLogin=true` sends every visitor who
   is not signed in to the login page. Only the views in `AnonymousAccessList[]` stay open, such as the forgotten
   password page ([What visitors may see](../../creators/website-creators/visitor-permissions.md#siteaccess-settings)).
2. **Only staff may sign in there.** The policy `user/login` can be limited by siteaccess (`kernel/user/module.php`
   defines the `SiteAccess` limitation for `login`), so a role for staff can allow sign-in on the intranet and a
   public member role cannot.
3. **Content is grouped by permission.** Sections and roles decide which group reads and edits what
   ([Sections](../../../guides/sections.md)).

An intranet can live in its own installation or next to a public site in the same installation. One installation
with several siteaccesses shares the content tree, the users and the operations work; separate installations keep
everything apart. Both shapes are described in
[Setting up several sites in one installation](../../creators/website-creators/multisite-setup.md) and in the
[Multisite](../multisite/README.md) section.

> **Tip**
> If the intranet and the public site share one installation, make sure the public siteaccess's Anonymous role
> cannot read the intranet's sections. The default Anonymous role reads Section Standard only
> ([Sections, part 2](../../../guides/sections.md#2-the-sections-of-a-new-installation)); put intranet content in
> sections of its own.

## Owners and governance

Plan the people before the pages:

| Role in the organization | What they do on the intranet | Typical Exponential role |
|---|---|---|
| Intranet owner | decides structure, approves new areas, reviews access | Administrator, or an editor role with wide limits |
| Department editor | keeps the department's pages and documents current | Editor, assigned with a Section limitation |
| Approver | checks texts before they go live | member of the approver group of an Approve event |
| All staff | read, search, follow items | a Staff role with `content/read` for the staff sections |
| IT or the host | backups, updates, mail, sign-in integration | server access; no content role needed |

The [project templates](../../leadership/project-templates.md) and
[working together](../../leadership/working-together.md) pages in the leadership section help write this down.

## Questions that decide effort

These questions change the size of the project. Answer them early and give the answers to whoever estimates the work
([Estimating and scoping with extensions](../../leadership/estimating-and-scoping-with-extensions.md)).

- How many departments, and do they each need a separate area?
- Must accounts come from an existing directory, or can staff get local accounts?
- Must every text be approved, or only some areas?
- How many documents exist today, in which formats, and who owns them?
- Do documents need full-text search inside the files?
- Must the intranet be reachable from outside the office network?
- Which languages are needed?
- Which existing tools must be linked or embedded?

## A planning worksheet

Copy this list into the project brief (Project brief template).

- [ ] Purpose of the intranet in two sentences.
- [ ] List of user groups and their size.
- [ ] Map of areas (sections) and which groups read and edit each.
- [ ] Sign-in decision: local, directory, two-step.
- [ ] Publishing decision: direct, approval, review states.
- [ ] Document inventory and migration plan.
- [ ] Search decision: built-in or Solr.
- [ ] Notification decision: which items staff follow by default.
- [ ] Hosting and backups decided ([Reliability](../reliability/README.md)).
- [ ] Named owner and a date for the first access review (Access reviews).

## Related pages

- [Intranets](README.md)
- [Intranet rollout checklist](intranet-rollout-checklist.md)
- [Planning an Exponential project](../../leadership/planning-an-exponential-project.md)
- [Content modeling workshops](../../leadership/content-modeling-workshops.md)
- Worked example: an intranet
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
