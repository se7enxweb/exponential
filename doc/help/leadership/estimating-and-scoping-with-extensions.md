---
title: Estimating and scoping with extensions
audience: project-manager, developer
category: leadership
order: 50
summary: Scope an Exponential project by checking what the kernel and its extensions already do: an inventory by area, effort drivers as questions and a scoping worksheet. No hours or prices.
keywords: estimate, estimation, scoping, scope, extensions, inventory, effort, worksheet, build or reuse, RAD, custom extension, composer, packages, se7enxweb, project manager, developer
type: help
---
# Estimating and scoping with extensions

This page is for project managers and developers who scope and estimate an Exponential project together. It answers:
what already exists as part of the kernel or as an extension, which questions drive the effort of a project, and how
to record the scope so that an estimate can be made and checked later. It deliberately gives **no hour figures and
no prices**: the sources contain none, and every project differs. What it gives you is the inventory, the questions
and a worksheet.

## Contents

1. [Reuse first: how extensions work](#reuse-first-how-extensions-work)
2. [The inventory: what ships with Exponential](#the-inventory-what-ships-with-exponential)
3. [Finding what is on your installation](#finding-what-is-on-your-installation)
4. [Effort drivers as questions](#effort-drivers-as-questions)
5. [Building your own extension](#building-your-own-extension)
6. [The scoping worksheet](#the-scoping-worksheet)
7. [Estimating with the team](#estimating-with-the-team)
8. [Keeping the scope under control](#keeping-the-scope-under-control)

## Reuse first: how extensions work

An extension "is a folder under `extension/` that adds settings, templates, modules, content types, commands or
cronjobs to Exponential without touching the kernel" ([Extensions](../../guides/extensions.md)). Facts that matter for
scoping:

| Fact | Consequence for the plan | Source |
|---|---|---|
| An extension does nothing until it is switched on (`ActiveExtensions[]`) | Many extensions are already on disk; switching one on is configuration, not development | [Extensions, section 1](../../guides/extensions.md) |
| `composer.json` requires 66 `se7enxweb/*` packages, so every installation brings them; many are inactive until switched on | Check the inventory before you plan to build | [Who is 7x?](../who-is-7x.md#the-extension-ecosystem), [Default extension distribution](../../features/6.0/default-extension-distribution.md) |
| The order of `ActiveExtensions[]` is the order of priority for settings and templates | Several extensions changing the same thing need a decision on the order | [Loading order](../../features/6.0/extension-loading-order.md) |
| Each extension has its own repository, releases and changelog | Plan updates per extension, not only per kernel release | [Who is 7x?](../who-is-7x.md#the-extension-ecosystem) |
| Each shipped extension has a page saying what it is for, how to set it up and its settings | Read the page before you estimate | [Extensions, themes and packages](../../features/6.0/extensions/README.md) |

## The inventory: what ships with Exponential

The extension catalogue groups the extensions that have their own page
([Extensions, themes and packages](../../features/6.0/extensions/README.md)). Use this table to mark, for each
requirement, whether an extension covers it.

| Area | Extensions with a page | What they cover |
|---|---|---|
| Content and datatypes | `birthday`, `enhancedezbinaryfile`, `enhancedselection2`, `exp_enhanced_link`, `ezgmaplocation`, `ezstarrating`, `eztags`, `hcaptcha`, `ngclasslist`, `recaptcha`, `xrowmetadata` | Extra field types, tags, ratings, map locations, spam protection, metadata and sitemaps |
| Editing and page building | `expai`, the `explayouts` suite, `ezautosave`, `ezflow`, `ezie`, `ezmultiupload`, `ezodf`, `ezoe`, `ezwt` | Writing assistant, layouts, autosave, page flows, image editor, multiple upload, OpenDocument import and export, the online editor, website toolbar |
| Designs and themes | `ezdemo`, `ezwebin`, `ezwebin-ezpackage`, `sevenx_themes_admin_classic`, `sevenx_themes_simple` | Demo and website designs, the simple theme |
| Shop, newsletter and visitors | `bccie`, `bcgooglesitemaps`, `bcwebsitestatistics`, `cjw_newsletter`, `ezpaypal`, `ezpm` | Collected information export, sitemaps, statistics, newsletter, PayPal, private messages |
| Data exchange and APIs | `ezprestapi`, `ezxmlexport`, `nxc_powercontent`, `syndication`, `xrowextract` | REST API v1 and v2, XML export, content from code, syndication, export and import |
| Administration, security and operations | `AdminAid`, `ezjscore`, `ezmbpaex`, `expqueue`, `ezupdate`, `git_manager`, `sevenx_authentication_2fa`, `sevenx_dse`, `sevenx_valkey_cache` | Admin helpers, password expiry, job queue, updates, Git dashboard and backups, two-factor login, database source editor, Redis and Valkey cache |
| Template helpers | `owsimpleoperator`, `swark` | Extra template operators and workflow events |

Besides these, the kernel itself covers much that other systems need extensions for: versions, workflows,
multisite, languages, the shop, the audit trail, content jobs and the help
([What Exponential is](what-exponential-is.md#what-the-kernel-does)). Check the kernel first, then the extensions,
then build.

> **Warning**
> Two extensions for the same job are not always equal. For page building the catalogue lists both `ezflow` (pages,
> zones and blocks) and Exponential Layouts. On MongoDB, `eztags` "is not usable" and `ezflow` scheduling is untested
> ([MongoDB limits](../database-support.md#mongodb-limits)). Under an enforced Content Security Policy, extension
> menu items with inline scripts do nothing until they are converted
> ([Content Security Policy](../../features/6.0/content-security-policy.md)). Each of these is a scoping question.

## Finding what is on your installation

- **In the administration**: **Setup > Extensions** (`/setup/extensions`) lists every extension on disk, active ones
  first, with description, license, project page and dependencies; **Details** adds author, designs and downloads
  ([The Extensions page](../../guides/extensions-page.md),
  [Extension list](../../features/6.0/extension-list-and-downloads.md)).
- **From a terminal**: `./console exp:ini list site.ini/ExtensionSettings --allow-root-user` shows the active ones in
  loading order ([Extensions, section 1](../../guides/extensions.md)).
- **In `composer.json`**: `grep -n '"se7enxweb/' composer.json` lists `suggest` and `require`
  ([Default extension distribution](../../features/6.0/default-extension-distribution.md)).
- **Beyond your disk**: the Composer packages `se7enxweb/<name>` on Packagist; the vendor se7enxweb has 219 packages
  there ([Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#the-extension-ecosystem)).

Ask a developer to produce this list in the first week; it is the baseline of your scope.

## Effort drivers as questions

Effort in an Exponential project depends on answers to the questions below. Ask them in discovery and record the
answers in the worksheet. A "yes" does not mean "expensive"; it means "estimate this line separately".

### Product and platform

- Is this a new site, or an upgrade or migration from an older installation? From how far back
  ([Upgrading](../../guides/upgrading.md), chapters 14 to 17 of the installation book)?
- Does the project need Symfony beside the kernel (Platform Legacy or Nexus), or only the kernel?
- Which database? Are all planned extensions usable on it ([Database support](../database-support.md))?
- Which web server and hosting model ([Choosing hosting, database and operating system](choosing-hosting-database-and-os.md))?
- How many environments (development, test, staging, production)?

### Content

- How many content classes, and how many of them already exist in the site package you start from
  ([4.3 Site packages](../../install/04-choosing-an-install-method.md#43-site-packages-what-is-installed))?
- Which field types are needed beyond the 35 datatypes of the shipped list
  ([Content model, section 1](../../guides/content-model-and-editing.md))?
- How much content is migrated, and from where? Is there a tool for that source (packages, `xrowextract`, REST)?
- How much content is entered by hand, and by whom?
- How many languages, and who translates?

### Sites and design

- How many sites (siteaccesses) and designs?
- Is there an existing design system or theme to start from (`sevenx_themes_simple`, the media theme, `ezwebin`)?
- Which page types do editors arrange themselves with layouts, and which are fixed templates?
- Do the designs have to work right to left?

### Workflow and permissions

- How many roles beyond Administrator, Editor and Anonymous?
- Is there an approval step, and for which content?
- Are object states needed (review, legal clearance, embargo)?
- Is there a members-only area or an intranet?

### Integration

- Which systems exchange data with the site, in which direction, how often?
- Is the REST API enough, or is an extension of your own needed?
- Single sign-on, payment providers, newsletters, analytics?

### Quality and compliance

- Which accessibility standard applies to the public sites
  ([Checklists](accessibility-security-and-compliance-checklists.md))?
- Must the Content Security Policy be enforced, on the administration, on the sites, or both?
- Which data protection rules apply, and who answers requests?
- Which quality gates must pass before acceptance ([Quality gates and acceptance](quality-gates-and-acceptance.md))?

### Operation

- Who operates the site after launch, and who applies updates?
- How long must logs and the audit trail be kept? (The shipped retention of the audit trail is 90 live days and 730
  archive days for the main channels: [Audit trail](../../features/6.0/audit-trail.md).)
- Is there a support contract, and with whom?

## Building your own extension

When nothing fits, the work is an extension of your own. What the help documents, so that developers can estimate it:

- A smallest working extension by hand, and generated ones: **Setup > RAD** (`/setup/rad`) lists the extension points,
  offers wizards that write a working extension and "lists every file it would write under `extension/<name>` before
  writing any of them" ([RAD tools](../../features/6.0/rad-tools.md), [Extensions, section 6](../../guides/extensions.md)).
- Settings, content and cronjobs of your own ([Extensions, section 6.4](../../guides/extensions.md)).
- Releasing it with a version number in `ezinfo.php` and `extension.xml` and a tag that is never moved
  ([Extensions, "Release it"](../../guides/extensions.md#7-release-it)).
- Templates of the administration follow the styling rules: no `<style>` block or `style=""` attribute
  (`CONTRIBUTING.md`, [Styling the administration](../../bc/6.0/admin4-styling.md)).

Line items to include for each extension of your own:

- [ ] Design and build
- [ ] Settings and their documentation
- [ ] Policies (who may use it) and their place in the role plan
- [ ] Translation strings
- [ ] Tests
- [ ] Release and update routine
- [ ] Documentation for editors and administrators (it can live in the help: [The help system](../../features/6.0/help-system.md), "Help from extensions")

## The scoping worksheet

Copy this into a spreadsheet or your project notebook. One row per requirement.

```text
Scoping worksheet for <project name>                        version <n>, <date>

| # | Requirement (need)        | Covered by           | Kind                     | Questions open           | Owner   | Estimate  |
|---|---------------------------|----------------------|--------------------------|--------------------------|---------|-----------|
| 1 | <need>                    | kernel / <extension> / build | configure / template / extension / content / migration | <questions> | <name> | <by team> |
| 2 | Editors approve news      | kernel: workflows    | configure                | Who approves? Cronjob?   | <name>  | <by team> |
| 3 | German site               | kernel: languages    | configure + content      | Who translates?          | <name>  | <by team> |
| 4 | Tag pages                 | eztags               | configure + template     | Database is not MongoDB? | <name>  | <by team> |
| 5 | CRM export every night    | build: extension     | extension                | Format? Direction?       | <name>  | <by team> |

Kinds:
  configure  = settings and roles only
  template   = design and template work
  extension  = new code in an extension of our own
  content    = classes, content entry, translation
  migration  = moving data from another system
```

Add three summary lines under the table:

- **Assumptions**: everything the estimate takes for granted (for example "the database is PostgreSQL").
- **Exclusions**: what is out of scope, by name.
- **Open questions**: with the date they must be answered.

## Estimating with the team

1. **Walk the worksheet with a developer and the lead editor**, row by row. The project manager reads the need; the
   developer names the kind; the editor says what "done" means for it.
2. **Let the people who do the work estimate it.** The project manager records, challenges assumptions and keeps the
   exclusions list.
3. **Estimate unknowns as a spike.** A row with open questions gets a small, time-boxed investigation first; the real
   estimate follows its result.
4. **Prove the riskiest rows on the test installation early**: an extension on the chosen database, an import of real
   content, a page under the target Content Security Policy.
5. **Record the version of the worksheet** the estimate is based on. When the scope changes, the estimate changes with
   it.

## Keeping the scope under control

- **One list.** The worksheet is the scope. A change request is a new row with its own estimate.
- **Reuse is a decision, not an accident.** When a developer proposes building something, ask which extension was
  checked first and why it did not fit.
- **Configuration is not free.** Switching on an extension needs settings, roles, translations, tests and
  documentation like any other change.
- **Contribute fixes upstream.** A fix to an extension sent back as a pull request does not have to be carried
  through every later update ([Working with the community](working-with-the-community.md)).

## Related pages

- [Extensions: find, install, configure, build and release](../../guides/extensions.md)
- [Extensions, themes and packages](../../features/6.0/extensions/README.md)
- [The default extension distribution](../../features/6.0/default-extension-distribution.md)
- [Planning an Exponential project](planning-an-exponential-project.md)
- [Risk management for Exponential projects](risk-management.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
