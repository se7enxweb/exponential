---
title: Government tasks
audience: government, editor, admin, project-manager
category: public-sector
order: 10
summary: The index of the step-by-step task pages for public bodies - publishing, compliance, sites and languages, operations, people and permissions, procurement and training - and how a task page is built.
keywords: tasks, how to, step by step, procedures, publish, agenda, minutes, notice, alert, accessible form, department subsite, translate, archive, two-step sign-in, CSP, backup, restore, maintenance mode, roles, permissions, approval, procurement, training, public sector, government
type: help
---
# Government tasks

This is the index of the task pages of the government section: short, concrete procedures for the work a public
body does with Exponential, from "publish a council meeting agenda" to "remove a leaver" or "write an exit plan".
It is for editors, site administrators, IT operations staff, project managers and procurement officers.

Back to the start of the government help: [Governments: start here](../government-start-here.md).

## Contents

1. [How a task page is built](#how-a-task-page-is-built)
2. [Task groups](#task-groups)
3. [Finding a task](#finding-a-task)
4. [Before you start any task](#before-you-start-any-task)
5. [Related pages](#related-pages)

## How a task page is built

Every task page has the same parts, so you can scan it quickly:

| Part | What it tells you |
|---|---|
| Who and when | the role that does the task and the moment it is done |
| Before you start | the rights, information and decisions you need |
| Steps | numbered steps with the labels the administration shows, or the commands to run |
| Result | how you check that the task worked |
| Pitfalls | the mistakes people make and how to avoid them |
| Related pages | the background pages and the source documentation |

The steps use the labels of the admin4 administration design. Other designs (the classic administration, admin3)
show the same functions with a different layout; the module addresses such as `/content/dashboard` or
`/user/hygiene` are the same in every design.

## Task groups

| Group | For | Examples |
|---|---|---|
| Publishing (`tasks/publishing/`) | editors | publish a council meeting agenda, publish minutes after approval, publish a public notice, correct a published notice |
| [Compliance](compliance/README.md) | administrators, security and data protection officers | turn on two-step sign-in, enforce the Content Security Policy, set log retention, archive last year's notices |
| [Sites and languages](sites-and-languages/README.md) | administrators, website creators | translate a page into the second official language, set up a department subsite, add a right-to-left language |
| [Operations](operations/README.md) | IT operations | maintenance mode for an upgrade, restore a backup to a test server, check that cronjobs run |
| [People and permissions](people-and-permissions/README.md) | administrators | create a department editor role, remove a leaver, set up an approval workflow |
| [Procurement and training](procurement-and-training/README.md) | project managers, procurement officers, training coordinators | write an accessibility requirement, run a proof of concept, plan editor training |

Groups shown with their folder name in plain text are being written and will be linked here as they are published.

## Finding a task

- **Search the help** for the verb and the object: "publish agenda", "translate page", "remove leaver". The help
  search ranks titles and headings above body text, and task titles start with the verb.
- **Filter by audience**: the government audience lists every page of this section; the editor and admin
  audiences list the tasks for those roles across the whole help.
- **Start from a topic**: each section of the government help links the tasks that belong to it.

## Before you start any task

1. **Work on a test site first** when the task changes settings, roles or workflows. A copy of production with its
   own database is the safe place to rehearse ([Install in one command](../../../features/6.0/install-in-one-command.md)
   gives you a test installation quickly).
2. **Check your rights.** A task in the administration fails with an access-denied page when your role lacks the
   policy; ask the person who manages roles ([Roles and policies](../../../guides/roles-and-policies.md)).
3. **Know where settings go.** Site-specific settings belong in `settings/override/` or the siteaccess folder, never
   in the shipped `settings/*.ini` files ([Settings page](../../../guides/settings-page.md)).
4. **Clear the right caches** after a settings or template change ([Caches](../../../guides/caches.md)).
5. **Record what you changed** in your change log; the audit trail records who did what in the administration
   ([Audit trail](../../../features/6.0/audit-trail.md)).

## Related pages

- [Governments: start here](../government-start-here.md)
- [Editing content](../../editing-content.md) and [Publishing and versions](../../publishing-and-versions.md)
- [Operating a site](../../../guides/operating-a-site.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
