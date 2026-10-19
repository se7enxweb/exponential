---
title: Transition from Exponential to another system
audience: government, project-manager, admin, developer
category: public-sector
order: 80
summary: How a public body moves its content from Exponential to a different content management system - the seven-step export, rich text, files, users, addresses and the guides per target system - and how to run the old and new sites side by side until sign-off.
keywords: migration away, change of system, leaving Exponential, export, WordPress, Drupal, Joomla, Contentful, site builder, rich text, redirects, parallel running, decommissioning
type: help
---
# Transition from Exponential to another system

This page is for the project manager of a migration away from Exponential, for example after a new tender chose
another system. The documentation treats this as a normal, supported path: "Your content is yours"
([Migrating away from Exponential](../../../../install/migration/leaving-exponential.md)). This page puts the
documented steps into a project plan for a public body.

## Contents
1. [Plan the project](#plan-the-project)
2. [The documented export in seven steps](#the-documented-export-in-seven-steps)
3. [Content that needs transformation](#content-that-needs-transformation)
4. [Guides per target system](#guides-per-target-system)
5. [Running both sites side by side](#running-both-sites-side-by-side)
6. [Decommissioning the old site](#decommissioning-the-old-site)

## Plan the project

| Decision | Questions |
|---|---|
| Scope | all content, or only current content? What goes to an archive instead? |
| Content model mapping | which class becomes which content type of the target? ([Content model mapping](../../../../install/migration/content-model-mapping.md)) |
| Freeze or delta | freeze editing during the move, or export twice and move the changes? |
| Users | which accounts move; how they get passwords on the target |
| Addresses | every public address redirected ([URL redirects after exit](url-redirects-after-exit.md)) |
| Records | what the records rules require you to keep from the old system |

## The documented export in seven steps

From [Leaving Exponential, section 2](../../../../install/migration/leaving-exponential.md#2-the-complete-export-step-by-step):

1. **Freeze** editing, or note the time for a later delta export.
2. **Site archive** with xrowextract, every class, in the format the target reads best (CSV for most importers, JSON
   for scripts).
3. **Storage**: copy `var/<site>/storage/` (on a database cluster, `exp:clusterize -u` first).
4. **Database dump**, as an archive you can return to.
5. **Layouts**, if the target can use them.
6. **Address list** for redirects.
7. **Check** the archive: open the manifest, count rows per class against the administration's counts, open a sample
   of files.

## Content that needs transformation

- **Rich text** (`ezxmltext`) is stored as Exponential's XML. Either render it to HTML (the site's templates, or REST
  v2 with `OutputFormat=xhtml`) or transform the XML in a script; embedded images become image tags or links to the
  exported files ([section 3](../../../../install/migration/leaving-exponential.md#3-rich-text)).
- **Relations and embeds**: matched by remote id or object id on the target.
- **Translations**: the archive has one row per object per language.
- **Forms**: export submissions separately with `ext:bccie:export`.

## Guides per target system

The migration hub has a section "from Exponential to ..." for several targets:

| Target | Guide |
|---|---|
| WordPress | [WordPress and Exponential, section 5](../../../../install/migration/wordpress.md#5-from-exponential-to-wordpress) |
| Drupal | [Drupal](../../../../install/migration/drupal.md) |
| Joomla | [Joomla](../../../../install/migration/joomla.md) |
| Contentful | [Contentful](../../../../install/migration/contentful.md) |
| Site builders | [Site builders](../../../../install/migration/site-builders.md) |
| Static HTML archive | [Static HTML](../../../../install/migration/static-html.md) |
| Other members of the family | [Platform Legacy](../../../../install/migration/exponential-platform-legacy.md), [Nexus](../../../../install/migration/exponential-platform-nexus.md), [DXP](../../../../install/migration/exponential-platform-dxp.md) |

For a target not listed, the site archive's CSV and manifest are the general starting point.

## Running both sites side by side

- Keep the old site **read-only** until the new one is signed off; the documentation's checklist says so.
- Maintenance mode or restricted roles can stop editing on the old site
  ([Maintenance mode](../../../../guides/maintenance-mode.md), [Roles and policies](../../../../guides/roles-and-policies.md)).
- Compare samples of pages on both sites before switching DNS.
- Install the redirect table on the new system before the switch.

## Decommissioning the old site

- [ ] Final export and database dump archived under your records rules.
- [ ] A static archive of the old public site kept if your rules require one: `exp:makestaticcache` writes static
      pages, as the migration course explains
      ([Leaving Exponential, lesson](../../../trainers/migration/module-08-leaving-and-capstone/leaving-exponential.md)).
- [ ] Audit trail exported with its signed manifest and kept for its retention period.
- [ ] Personal data handled as agreed ([Personal data at exit](personal-data-at-exit.md)).
- [ ] Servers, cron jobs, certificates and accounts removed; deletion confirmed in writing.

## Related pages
- [Exit plans: overview](README.md)
- [Migrating away from Exponential](../../../../install/migration/leaving-exponential.md)
- [Migration checklist](../../../../install/migration/checklist.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
