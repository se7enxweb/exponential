---
title: Running an Exponential training course
audience: partner
category: partnership
order: 450
summary: A practical guide for partners running an Exponential training course - goals, audience, environment for participants, agenda templates for editors, administrators and developers, exercises, material licensing, evaluation and follow-up.
keywords: training course, agenda, exercises, workshop, editors, administrators, developers, environment, evaluation, material, GFDL
type: help
---
# Running an Exponential training course

This page is for trainers at partner companies. It walks through preparing, running and following up a course.

## 1. Define goal and audience

Write one sentence: "After this course, [audience] can [task] in Exponential without help." Everything else follows
from it. Keep audiences separate: editors, administrators and developers need different courses.

## 2. Prepare the environment

- One installation per participant, or one shared installation with one account per participant.
- Use the current release of the line the participants will work with.
- SQLite is the default database and needs no database server, which makes training installations simple
  ([Database support](../database-support.md)).
- Use test accounts only; never real customer data or the customer's live site.
- Reset the environment between courses.

## 3. Agenda templates

### Editors, one day

| Time | Topic | Help page |
|---|---|---|
| 09:00 | Signing in, the administration | [Accounts and sign-in](../accounts-and-sign-in.md), [Admin tour](../admin-tour.md) |
| 10:00 | Creating and editing content | [Editing content](../editing-content.md) |
| 11:30 | Images and media | [Images and media](../media-and-uploads.md) |
| 13:30 | Publishing and versions | [Publishing and versions](../publishing-and-versions.md) |
| 14:30 | Languages | [Languages](../languages.md) |
| 15:30 | Layouts | [The layouts editor](../layouts-editor.md) |
| 16:30 | Finding help, keyboard shortcuts | [Searching](../searching.md), [Keyboard shortcuts](../keyboard-shortcuts.md) |

### Administrators, one day

| Topic | Source |
|---|---|
| Requirements and installation methods | `doc/install/02-requirements.md` to `07-console-install.md` |
| Serving the site, engines | `doc/install/08-serving-the-site.md` |
| Security hardening | [Security hardening](../security-hardening.md) |
| Passwords and accounts | [Change the admin password](../change-the-admin-password.md) |
| Upgrading | `doc/install/11-upgrading.md` |
| When something goes wrong | [When something goes wrong](../when-something-goes-wrong.md) |

### Developers, two days

| Topic | Source |
|---|---|
| Git workflow | [Develop with Git](../develop-with-git.md) |
| Extensions by hand and with the RAD tools | [Extensions](../../guides/extensions.md) |
| Releasing an extension | [Extensions, "Release it"](../../guides/extensions.md#7-release-it) |
| Contributing back | CONTRIBUTING.md, [Contributing code back](contributing-code-back.md) |

## 4. Exercises

Make each exercise a real task with a visible result: "Publish an article in two languages with an image", "Add a
setting to an extension and read it in a template". Give a solution sheet afterwards.

## 5. Material and licenses

Material adapted from the help stays under the GFDL 1.3 or later: keep the notices and pass on the same freedoms
([Free documentation](../free-documentation.md)). Your own original slides may be under any license, but a free
license helps the community and earns points as published material.

## 6. Evaluation and follow-up

```text
Course evaluation
1. Can you now <goal task> without help?   yes / partly / no
2. What was most useful? ______________
3. What was missing? ______________
4. Pace: too slow / right / too fast
```

- Send participants the links to the help pages used in the course.
- Log the course for points ([Logging your partner activity](logging-partner-activity.md)).
- Turn repeated questions into improvements of the documentation
  ([Contributing documentation and translations](contributing-docs-and-translations.md)).

## Related pages

- [Training partners](training-partners.md)
- [Student and education partners](student-and-education-partners.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
