---
title: Patterns in extension/birthday
audience: developer
category: standards
order: 405
summary: Every class, view, fetch function, operator and plugin point of extension/birthday with its design patterns.
keywords: extension/birthday, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/birthday

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/birthday` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (4)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `birthdayInfo` | class | [Value object / struct](value-object.md) | `extension/birthday/ezinfo.php:6` | The extension's information for the about page and the upgrade checks. |
| `eZBirthday` | class | – | `extension/birthday/datatypes/ezbirthday/ezbirthday.php:3` | – |
| `eZBirthdayCsvHandler` | class extends XrowBaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/birthday/classes/parsers/ezbirthdaycsvhandler.php:29` | CSV export handler for the ezbirthday datatype. |
| `eZBirthdayType` | class extends eZDataType | [Datatype (Strategy for content fields)](datatype.md) | `extension/birthday/datatypes/ezbirthday/ezbirthdaytype.php:3` | – |

## INI plugin points (1)

| Setting | Class | Set in |
|---|---|---|
| `[ezbirthday] HandlerClass` | `eZBirthdayCsvHandler` | `extension/birthday/settings/csv.ini.append.php:19` |
