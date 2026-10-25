---
title: Patterns in kernel/classes/role
audience: developer
category: standards
order: 511
summary: Every class, view, fetch function, operator and plugin point of kernel/classes/role with its design patterns.
keywords: kernel/classes/role, design patterns, classes, extension points
type: catalogue
---
# Patterns in kernel/classes/role

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `kernel/classes/role` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (3)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expRoleGrantCheck` | class | [Factory and static factory methods](factory.md) | `kernel/classes/role/exprolegrantcheck.php:37` | Whether a user who edits roles may grant a policy: only one that their own access covers (site.ini [RoleSettings] PreventPrivilegeEscalation). |
| `expRolePage` | class | [Utility (static helper)](utility.md) | `kernel/classes/role/exprolepage.php:19` | What the role pages (role/list, role/view, role/edit) work out besides the role itself: the rows of the role list with their search and order, what e… |
| `expRolePolicySentence` | class | – | `kernel/classes/role/exprolepolicysentence.php:27` | A policy of a role in words, for the role pages (role/view, role/edit): "May read content in section Standard, of the classes Article and Folder". |
