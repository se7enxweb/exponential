---
title: Patterns in kernel/classes/user
audience: developer
category: standards
order: 509
summary: Every class, view, fetch function, operator and plugin point of kernel/classes/user with its design patterns.
keywords: kernel/classes/user, design patterns, classes, extension points
type: catalogue
---
# Patterns in kernel/classes/user

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `kernel/classes/user` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (7)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expRegistrationAdminAction` | class | [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `kernel/classes/user/expregistrationadminaction.php:23` | The signed links of the administrator's "New user registered" mail (design:user/registrationfeedback.tpl) and the actions behind them (user/registrat… |
| `expUnactivatedUsers` | class | [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `kernel/classes/user/expunactivatedusers.php:22` | The users who registered and never activated their account, for the page user/unactivated: how many there are, a page of them searched by login, e-ma… |
| `expUserAccountOverview` | class | [Factory and static factory methods](factory.md) | `kernel/classes/user/expuseraccountoverview.php:22` | The account page of a user (user/edit, user/edit/), described for a person: who the account is (name, login, e-mail, groups, roles), its state (enabl… |
| `expUserAccountStatus` | class | [Utility (static helper)](utility.md) | `kernel/classes/user/expuseraccountstatus.php:21` | The account status the administration marks on user objects: enabled, disabled (Setup › Users, ezuser_setting.is_enabled = 0) or locked (more failed… |
| `expUserActivation` | class | [Utility (static helper)](utility.md) | `kernel/classes/user/expuseractivation.php:25` | What follows the enabling of an account, for user/activate (the link the user clicks) and the Activate button of user/unactivated. |
| `expUserDeactivation` | class | [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `kernel/classes/user/expuserdeactivation.php:36` | "Deactivate and move" and "Reactivate and move back": instead of removing a user account (users never go to the trash, so removing one cannot be undo… |
| `expUserHygiene` | class | [Factory and static factory methods](factory.md) | `kernel/classes/user/expuserhygiene.php:31` | The accounts the page user/hygiene ("Check user accounts") lists: every user account, or those below a user group, narrowed by one filter (never sign… |
