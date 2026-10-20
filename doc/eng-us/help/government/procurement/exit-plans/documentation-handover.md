---
title: Documentation handover at exit
audience: government, project-manager, admin, partner
category: public-sector
order: 60
summary: What documentation a supplier must hand over so another party can run an Exponential site - system report, content model, roles, settings, integrations, custom code, operations runbooks and editorial guides - with the administration pages and commands that produce much of it automatically.
keywords: documentation handover, knowledge transfer, system documentation, content model documentation, runbook, settings, roles, custom code, README, exit, transition
type: help
---
# Documentation handover at exit

This page is for project managers who define what documentation a supplier delivers, during the contract and at its
end, and for administrators who check it. Much of what a new supplier needs can be produced from the installation
itself; the rest is knowledge only the old supplier has. Both belong in the handover.

## Contents
1. [What can be produced from the site](#what-can-be-produced-from-the-site)
2. [What only the supplier knows](#what-only-the-supplier-knows)
3. [The product documentation](#the-product-documentation)
4. [A handover checklist](#a-handover-checklist)
5. [Pitfalls](#pitfalls)

## What can be produced from the site

| Document | How to produce it | Documentation |
|---|---|---|
| System report: versions, PHP, database, extensions, health checks | **Setup > System information**, `/setup/info/report` downloads it as a text file; or `./console exp:system:info --json` | [System information](../../../../guides/system-information.md) |
| Content model: classes and their attributes | **Setup > Classes**, each class page; or the site archive's typed manifest | [Class groups](../../../../guides/class-groups.md), [Data export formats](data-export-formats.md) |
| Roles, policies and assignments | **User accounts** > roles; each role's page | [Roles and policies](../../../../guides/roles-and-policies.md) |
| Sections and object states | **Setup > Sections**, **Setup > States** | [Sections](../../../../guides/sections.md), [Object states](../../../../guides/object-states.md) |
| Workflows and triggers | **Setup > Workflows**, **Setup > Triggers** | [Workflows](../../../../guides/workflows.md) |
| Settings and where each value comes from | the settings page; or `exp:ini` | [The settings page](../../../../guides/settings-page.md), [exp:ini](../../../../features/6.0/exp-ini-command.md) |
| Active extensions and their order | **Setup > Extensions** | [The Extensions page](../../../../guides/extensions-page.md) |
| Scheduled jobs | **Setup > Cronjobs** | [Cronjobs](../../../../guides/cronjobs.md) |
| URL aliases and wildcards | **URL translator**, **URL wildcards** | [URLs and aliases](../../../../guides/urls-and-aliases.md) |

The system report leaves out passwords, keys and server paths, so it can be shared with a new supplier; settings
files cannot (see [Keys and secrets handover](keys-and-secrets-handover.md)).

## What only the supplier knows

Require written documentation, kept current during the contract, of:

- **Custom code**: each custom extension and design, its purpose, its templates and overrides, how to build and
  deploy it, in a README in its Git repository.
- **Integrations**: each connected system, direction, protocol, credentials location (not the credentials), contacts.
- **Deployment**: how a change reaches production, environments, the deploy commands
  ([Deploying](../../../../guides/deploying.md)).
- **Operations**: backup schedule and location, monitoring, cron entries, log rotation, known recurring problems and
  their fixes (runbooks in the style of [Hosting runbooks](../../../hosting/runbooks/README.md)).
- **Decisions**: why the content model is shaped as it is, accessibility choices of the design, security settings
  changed from the defaults.
- **Editorial guides** written for your editors ([Handing over to editors](../../../creators/website-creators/handing-over-to-editors.md)).

## The product documentation

The product documentation does not need handing over: it ships with every installation in `doc/` and is shown as
help in the administration ([Help system](../../../../features/6.0/help-system.md)). It is free documentation under
the GNU FDL 1.3 or later, so your own manuals may copy and adapt it ([Free documentation](../../../free-documentation.md)).
Your handover documents only need to cover what is specific to your site.

## A handover checklist

```text
[ ] System report (Setup > System information > report), dated
[ ] Content model: classes and attributes, with their purpose
[ ] Roles, policies, assignments; sections; states; workflows and triggers
[ ] Settings changed from the defaults, and why
[ ] Active extensions, versions, order; custom extensions with READMEs in Git
[ ] Integrations described; credentials locations listed
[ ] Deployment procedure and environments
[ ] Operations: backups, monitoring, cron, runbooks
[ ] Editorial guides
[ ] Open issues and known bugs
[ ] Walk-through session with the receiving party held on <date>
```

## Pitfalls

- Documentation written only at the end, from memory. Require it as a deliverable at each milestone.
- Credentials pasted into documents. Document where they are, never what they are.
- A handover with no walk-through: book a session where the receiving party asks questions.

## Related pages
- [Exit plans: overview](README.md)
- [Exit plan template](exit-plan-template.md)
- [Transition to another provider](transition-to-another-provider.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
