---
title: Patterns in extension/ezdemo
audience: developer
category: standards
order: 440
summary: Every class, view, fetch function, operator and plugin point of extension/ezdemo with its design patterns.
keywords: extension/ezdemo, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/ezdemo

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/ezdemo` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (5)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `eZArchive` | class | [Template operator (Interpreter plugin)](template-operator.md) | `extension/ezdemo/autoloads/ezarchive.php:28` | – |
| `ezdemoInfo` | class | [Value object / struct](value-object.md) | `extension/ezdemo/ezinfo.php:12` | – |
| `eZKeywordList` | class | [Template operator (Interpreter plugin)](template-operator.md) | `extension/ezdemo/autoloads/ezkeywordlist.php:28` | – |
| `eZPageData` | class | [Template operator (Interpreter plugin)](template-operator.md) | `extension/ezdemo/autoloads/ezpagedata.php:40` | – |
| `eZTagCloud` | class | [Template operator (Interpreter plugin)](template-operator.md) | `extension/ezdemo/autoloads/eztagcloud.php:28` | – |

## Template operators (4)

| Operators | Class | Registered in |
|---|---|---|
| ezkeywordlist | `eZKeywordList` | `extension/ezdemo/autoloads/eztemplateautoload.php:30` |
| ezarchive | `eZArchive` | `extension/ezdemo/autoloads/eztemplateautoload.php:33` |
| eztagcloud | `eZTagCloud` | `extension/ezdemo/autoloads/eztemplateautoload.php:36` |
| ezpagedata, ezpagedata_set, ezpagedata_append | `eZPageData` | `extension/ezdemo/autoloads/eztemplateautoload.php:39` |
