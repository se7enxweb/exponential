---
title: Hands-on workshop outline
audience: advocate
category: advocacy
order: 43
summary: Outlines for a 90-minute and a half-day hands-on Exponential workshop - install, edit and publish, build a page with layouts, define a content class, override a template, write an extension with the RAD tools, send a fix - with prerequisites, exercises, checkpoints and facilitator notes.
keywords: workshop, hands-on, training, tutorial, install, content class, template override, extension, RAD tools, layouts, pull request, exercises, facilitator, participants
type: help
---
# Hands-on workshop outline

This page is for advocates who want people to **do** things with Exponential, not just watch. It gives two formats, a
90-minute introduction and a half-day builder workshop, with exercises that each end in something participants can
check. Every exercise links the documentation page it follows, so participants can continue alone.

## Contents

1. [Before the workshop](#before-the-workshop)
2. [Format A: 90-minute introduction](#format-a-90-minute-introduction)
3. [Format B: half-day builder workshop](#format-b-half-day-builder-workshop)
4. [Facilitator notes](#facilitator-notes)
5. [Handout](#handout)

## Before the workshop

**Participants bring** a laptop with PHP 8.1 or later, Composer and Git, and a GitHub account for the last exercise.
Check the PHP requirement on [Operating system support](../os-support.md#what-the-operating-system-has-to-provide).

**You prepare**:

- a tested install path for Linux, macOS and Windows (or one shared container image you built yourself);
- a fallback: a few spare laptops or a pre-installed copy per participant on a machine you control;
- the handout below, printed or as a link;
- a helper for every eight to ten participants.

> **Warning**
> Each participant installs their own copy. Never let a group share one administrator account or one installation
> that holds real data ([Live demo safety](live-demo-safety.md)).

## Format A: 90-minute introduction

| Time | Exercise | Checkpoint | Follows |
|---|---|---|---|
| 0:00 | Welcome, what Exponential is (5 min) | | [The story in two minutes](the-story-of-exponential-in-two-minutes.md) |
| 0:05 | Install with one command on SQLite | The administration opens; password changed | [Install in one command](../../features/6.0/install-in-one-command.md) |
| 0:25 | Tour of the administration | Everyone found the "?" help button | [A tour of the administration](../admin-tour.md) |
| 0:35 | Create, edit, preview and publish an article; compare versions | Two versions visible in **Manage versions** | [Creating and editing content](../editing-content.md), [Publishing, versions and the trash](../publishing-and-versions.md) |
| 0:55 | Add a language and translate the article | The article has two language badges | [Languages](../languages.md) |
| 1:10 | Build a page in the layouts editor | A block added and published | [The layouts editor](../layouts-editor.md) |
| 1:25 | Where to go next; community channels | Everyone has the links | [The community channels](community-channels.md) |

## Format B: half-day builder workshop

Format A's first three exercises, then:

| Time | Exercise | Checkpoint | Follows |
|---|---|---|---|
| 0:40 | Define a content class "Event" with a title, a date and a text | An Event can be created | [The content model and editing content](../../guides/content-model-and-editing.md) |
| 1:10 | Find which template draws a page; make a change with an override you can undo | The page shows the change | [Templates and design](../../guides/templates-and-design.md) |
| 1:50 | Break | | |
| 2:00 | Write a first extension with the RAD tools, enable it, see it in the survey | The extension appears in **Setup > RAD** | [RAD tools](../../features/6.0/rad-tools.md), [Extensions, section 6](../../guides/extensions.md#6-build-your-own-extension) |
| 2:50 | Install as Git clones; make a small documentation fix; open a pull request to your fork or the project | A pull request link | [Develop with Git](../develop-with-git.md), [Improving the help](improving-the-help.md) |
| 3:30 | Wrap-up: what participants built; next steps | | [The advocacy ladder](the-advocacy-ladder.md) |

The pull request exercise is optional and only with each participant's own GitHub account. Do not ask participants
to open pull requests "for practice" to the project with no real change; a real, small fix (a typo, a clearer
sentence) is a better first contribution.

## Facilitator notes

- **Start installing early.** The install step takes the longest when a laptop lacks a PHP extension; helpers walk the
  room. [Check a machine in two minutes](../os-support.md#check-a-machine-in-two-minutes) helps.
- **Use invented content.** "Annual Bike Day", not a real customer's event.
- **Pair people.** Faster participants help slower ones; it builds community.
- **Write questions down.** Answer later with sources; post the answers in the forum afterwards.
- **Accessibility.** Large fonts on the projector, materials readable by screen reader, breaks every hour
  ([Accessible slides and visuals](accessible-slides-and-visuals.md)).
- **Collect feedback** with three questions: what worked, what was hard, what next. No personal data needed.

## Handout

```text
EXPONENTIAL WORKSHOP                                               <date>, <place>

Install            ./console exp:install --dry-run   then   ./console exp:install
First sign-in      change the generated password; delete var/log/initial-admin-password
Help               the "?" in the header of every page; Help center: /exphelp/dashboard
Exercises          (list with the help page of each)
Build              Setup > Classes   |   Templates and design guide   |   Setup > RAD
Contribute         Develop with Git  |   Edit on GitHub on every help page
Ask                share.exponential.earth/forums   |   t.me/exponentialcms
Report a bug       Help > Report an error   (never passwords or personal data)
Security problems  privately, as SECURITY.md describes
```

## Related pages

- [Proposing a contribution sprint](proposing-a-contribution-sprint.md)
- [Writing a tutorial that works](writing-a-tutorial-that-works.md)
- [Event checklists](event-checklists.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
