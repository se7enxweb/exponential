---
title: Demo scripts for evaluation panels
audience: government, project-manager, sales-marketing, partner
category: public-sector
order: 60
summary: A scripted demonstration that every bidder offering Exponential (or any CMS) performs in the same order, built from the documented demo scripts of the help, with the tasks to show, the evidence each task produces and the screens that must not reveal personal data.
keywords: demo script, scripted demonstration, vendor demo, evaluation demo, CMS demo, tender, evaluator, editor tour, administrator tour, install demo
type: help
---
# Demo scripts for evaluation panels

This page is for procurement officers who write the demonstration part of a tender, and for partners who prepare a
demonstration of Exponential for an evaluation panel. A scripted demo asks every bidder to show the same tasks in the
same order, so the panel compares answers to the requirements instead of sales presentations. The steps reuse the
documented demo scripts of the press section of this help, which name the exact buttons and the source of every
statement.

## Contents
1. [Rules for the scripted demo](#rules-for-the-scripted-demo)
2. [Segment 1 - Installation](#segment-1---installation)
3. [Segment 2 - Editor tasks](#segment-2---editor-tasks)
4. [Segment 3 - Administrator tasks](#segment-3---administrator-tasks)
5. [Segment 4 - Accessibility](#segment-4---accessibility)
6. [Segment 5 - Exit](#segment-5---exit)
7. [Screens that need care](#screens-that-need-care)
8. [Evaluator note sheet](#evaluator-note-sheet)

## Rules for the scripted demo

- Send the script to all bidders at the same time, with the time allowed per segment.
- The bidder uses a demo installation with **invented content and invented accounts** only. Building such an
  installation is described in [Setting up a demo installation](../../../press/setting-up-a-demo-installation.md).
- An evaluator may ask the presenter to repeat a step, or to do it with the keyboard only.
- Everything shown must exist in the version offered. Features shown from another product or a future release are
  recorded as "not shown".
- Record the session if your rules allow, so the consensus meeting can check a minute of the demo.

## Segment 1 - Installation

Source script: [Demo script: install from zero](../../../press/demo-script-install-from-zero.md).

1. Show the requirements of the machine used ([Requirements](../../../../install/02-requirements.md)).
2. Run the one-command installation on SQLite: `./console exp:install`
   ([Installing in one command](../../../../features/6.0/install-in-one-command.md)).
3. Show that the generated administrator password is printed once and written to `var/log/initial-admin-password`,
   then change it at the first sign-in.

Evidence: the time the installation took, and whether the panel could repeat it in the proof of concept.

## Segment 2 - Editor tasks

Source scripts: [Demo script: editor tour](../../../press/demo-script-editor-tour.md) and
[five-minute overview](../../../press/demo-script-five-minute-overview.md).

1. Create a news page with a title, a body, an image with alternative text, and save it as a draft.
2. Preview it, including at phone width.
3. Send it for approval (if the tender asks for approvals; [Workflows](../../../../guides/workflows.md)).
4. Publish it, then edit it again and compare the two versions ([Versions](../../../../guides/content-history.md)).
5. Translate it into a second language; if you need a right-to-left language, show one
   ([Right to left](../../../../features/6.0/right-to-left.md)).
6. Find the page again with the search.

## Segment 3 - Administrator tasks

Source script: [Demo script: administrator tour](../../../press/demo-script-administrator-tour.md).

1. Show a content type (**Setup > Classes**) and explain which fields it has.
2. Show the role an editor has (**User accounts**, roles) and limit it to one section
   ([Roles and policies](../../../../guides/roles-and-policies.md)).
3. Show the audit trail of the actions of segment 2 (`/audit/dashboard`, then the console;
   [Audit trail](../../../../features/6.0/audit-trail.md)).
4. Show two-step sign-in setup without finishing it ([Two-step sign-in](../../../../guides/two-factor-login.md)).
5. Show **Setup > Upgrade check** and **Setup > System information** (top only).
6. Show maintenance mode ([Maintenance mode](../../../../guides/maintenance-mode.md)).

## Segment 4 - Accessibility

1. Repeat steps 1 and 4 of segment 2 with the keyboard only. The admin4 design has a skip link as first Tab stop
   and visible focus ([Accessibility standards](../../../../standards/accessibility.md)).
2. Ask the bidder to show the public page with the keyboard and at 200% zoom. Public designs are not audited by the
   project: what you see is the bidder's own design work.
3. Ask where an editor enters alternative text and what happens when it is empty: nothing forces editors to fill it,
   as the accessibility page states under its known gaps.

## Segment 5 - Exit

1. Make a site archive: `./console ext:xrowextract:archive --set=everything --format=zip --output=<dir>`.
2. Open its `manifest.json` and one class CSV ([Migrating away from Exponential](../../../../install/migration/leaving-exponential.md)).
3. Show that the archive holds no password hashes.

## Screens that need care

The administrator tour lists them: System information (paths, host names), Log files, the audit console, user lists,
two-step setup (QR code) and anything below `settings/override/`. On a demo installation with invented data they are
safe; on a copy of a real site they are not. Never accept a demo on a copy of a production site with real personal
data. More: [Keeping secrets off screen](../../../press/keeping-secrets-off-screen.md).

## Evaluator note sheet

```text
Bidder <name>     Segment <n>     Step <n>     Minute <mm:ss>
Requirement(s) shown: <ids>
Shown as asked: yes / partly / no / not shown
Keyboard only: yes / no / not tried
Notes (facts only):
```

## Related pages
- [Evaluation: overview](README.md)
- [Procurement: overview](../README.md)
- [Press: setting up a demo installation](../../../press/setting-up-a-demo-installation.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
