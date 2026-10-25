---
title: Patterns in kernel/private/classes/services
audience: developer
category: standards
order: 532
summary: Every class, view, fetch function, operator and plugin point of kernel/private/classes/services with its design patterns.
keywords: kernel/private/classes/services, design patterns, classes, extension points
type: catalogue
---
# Patterns in kernel/private/classes/services

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `kernel/private/classes/services` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (8)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `Exponential\Service\ContentJobDetails` | class | [Utility (static helper)](utility.md) | `kernel/private/classes/services/contentjobdetails.php:17` | What the content job pages show about a job beyond its progress: who started it (and on whose behalf), the times, the duration and an estimate of the… |
| `Exponential\Service\DraftsCleanup` | class | [Utility (static helper)](utility.md) | `kernel/private/classes/services/draftscleanup.php:22` | Removing drafts older than their configured lifetime: one service for the two cronjob parts that did it with the same code and different settings, cr… |
| `Exponential\Service\MailPreferencesPage` | class | [Utility (static helper)](utility.md) | `kernel/private/classes/services/mailpreferencespage.php:27` | What the views of the mailpreferences module share: the preference page (signed in, by a personal link, or an administrator for a user), the boxes on… |
| `Exponential\Service\ReverseRelationCheck` | class | [Utility (static helper)](utility.md) | `kernel/private/classes/services/reverserelationcheck.php:22` | Which other objects point at the content a removal or a hide is about to take away: the reverse relations (ezcontentobject_link) of the objects that… |
| `Exponential\Service\SessionGarbageCollector` | class | [Utility (static helper)](utility.md) | `kernel/private/classes/services/sessiongarbagecollector.php:20` | Removing expired sessions: one service for the sessions view (setup/session, "Remove timed out sessions"), the command (bin/php/ezsessiongc.php) and… |
| `Exponential\Service\Trash` | class | [Utility (static helper)](utility.md) | `kernel/private/classes/services/trash.php:23` | Emptying the trash: one service for the trash view (content/trash), the command (bin/php/trashpurge.php) and the cronjob part (cronjobs/trashpurge.ph… |
| `Exponential\Service\TrashList` | class | [Utility (static helper)](utility.md) | `kernel/private/classes/services/trashlist.php:27` | What the trash view (content/trash) shows about each item, its filters and its summary line. |
| `Exponential\Service\TrashRecord` | class | [Utility (static helper)](utility.md) | `kernel/private/classes/services/trashrecord.php:30` | The file where who moved an object to the trash, and from where, was kept before the trash rows had the columns trashed_by and trashed_via. |
