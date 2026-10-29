---
title: Giving demos as an advocate
audience: advocate
category: advocacy
order: 40
summary: How a community advocate prepares and gives a demonstration of Exponential - on an installation you control, with a goal, a short path through the product, sources ready and a recovery plan - with a preparation checklist and three ready demo paths.
keywords: demo, demonstration, show, presentation, advocate, installation, preparation, checklist, editors, layouts, languages, help, recovery
type: help
---
# Giving demos as an advocate

This page is for advocates who show Exponential to colleagues, user groups, classes or friends. A good demo shows two
or three things well, on an installation you control, and ends with where people can go next. The click-by-click
scripts of 5, 15 and 45 minutes are on [Demo scripts](../business/demo-scripts.md); this page adapts them to the
advocate's situation.

## Contents

1. [The three rules](#the-three-rules)
2. [Build your demo installation](#build-your-demo-installation)
3. [Choose a goal and a path](#choose-a-goal-and-a-path)
4. [Three ready demo paths](#three-ready-demo-paths)
5. [The preparation checklist](#the-preparation-checklist)
6. [Ending the demo](#ending-the-demo)

## The three rules

1. **Your installation, your data.** Never demo on a customer's site, an employer's production site or a shared
   installation whose settings belong to someone else ([Live demo safety](live-demo-safety.md)).
2. **Less is more.** Two or three features shown well beat ten features rushed.
3. **Every claim has a source.** Keep the links open in a second window.

## Build your demo installation

One command installs a complete site on SQLite
([Installing Exponential in one command](../../features/6.0/install-in-one-command.md)):

```bash
./console exp:install --dry-run
./console exp:install --apply
```

Then:

- change the generated administrator password and delete `var/log/initial-admin-password`
  ([The default administrator password](../default-administrator-password.md));
- create a demo editor account with the Editor role;
- add a second content language (**Setup > Languages**);
- create a folder with a few items of invented content.

A full checklist is in [Demo scripts, Before any demo](../business/demo-scripts.md#before-any-demo-the-setup-checklist).

## Choose a goal and a path

| Audience | Goal | Path |
|---|---|---|
| Editors | "I could work with this." | Editing path |
| Designers | "Pages can be built visually." | Layouts path |
| Multilingual teams | "Our languages are covered." | Languages path |
| Developers | "I could extend this." | Editing path, then **Setup > RAD** and a template override ([RAD tools](../../features/6.0/rad-tools.md)) |
| Mixed group | "It is mature, open and well documented." | Editing path, then the help |

## Three ready demo paths

### Editing path (5 minutes)

1. The dashboard: "Your drafts and pending items."
2. Open a folder, **Edit**, change the short name.
3. **Preview** at phone width.
4. **Send for publishing**; then **Manage versions**, **Compare**.
5. The "?" in the header.

### Layouts path (5 minutes)

1. **Layouts** tab, **Open** a layout.
2. **Add a block**, drag a **Title** block into a zone.
3. Move it to another zone; point at a linked zone.
4. **Discard**.

### Languages path (4 minutes)

1. Open an item; point at the language badges.
2. Translate based on the existing language; publish.
3. **Interface**: choose Hebrew or Arabic; the administration turns around.
4. Back to English.

Sources for what you say: [The layouts editor](../layouts-editor.md), [Languages](../languages.md),
[Publishing, versions and the trash](../publishing-and-versions.md).

## The preparation checklist

```text
ONE DAY BEFORE
[ ] Demo installation runs; admin password changed; initial password file deleted
[ ] Demo editor account; second language; a folder with invented items
[ ] At least one layout exists
[ ] Links open in a second window: talking points, sources, channels
[ ] Tried the whole path once, timed

TEN MINUTES BEFORE
[ ] Signed in as the account you will use
[ ] No drafts left from the last run
[ ] Browser: private window, zoom readable, notifications off, other tabs closed
[ ] Nothing on screen with real personal data, keys or passwords
```

## Ending the demo

Finish with three things on one slide or card:

```text
Try it:          ./console exp:install --apply        (one command, SQLite)
Learn:           the help in the admin ("?"), doc.exponential.earth
Ask and share:   share.exponential.earth/forums   t.me/exponentialcms
```

Then answer questions. If you do not know: "I will find the source and post it."

**Demos the audience signs in to.** Run them in demo mode (`EXP_DEMO_MODE=1`): the administrator password you show cannot be changed by a participant. Rebuild or reinstall afterwards, since demo mode protects nothing else. [Live demo safety](live-demo-safety.md), [Demo mode FAQ](../demo-mode-faq.md).

## Related pages

- [Live demo safety](live-demo-safety.md)
- [Lightning talk script](lightning-talk-script.md)
- [Demo scripts](../business/demo-scripts.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
