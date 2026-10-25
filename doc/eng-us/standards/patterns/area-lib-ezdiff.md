---
title: Patterns in lib/ezdiff
audience: developer
category: standards
order: 539
summary: Every class, view, fetch function, operator and plugin point of lib/ezdiff with its design patterns.
keywords: lib/ezdiff, design patterns, classes, extension points
type: catalogue
---
# Patterns in lib/ezdiff

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `lib/ezdiff` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (10)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `eZDiff` | class | – | `lib/ezdiff/classes/ezdiff.php:20` | – |
| `eZDiffContainerObject` | class extends eZDiffContent | – | `lib/ezdiff/classes/ezdiffcontainerobject.php:18` | – |
| `eZDiffContainerObjectEngine` | class extends eZDiffEngine | [Strategy / handler](strategy-handler.md) | `lib/ezdiff/classes/ezdiffcontainerobjectengine.php:21` | – |
| `eZDiffContent` | class | – | `lib/ezdiff/classes/ezdiffcontent.php:21` | – |
| `eZDiffEngine` | class | [Strategy / handler](strategy-handler.md) | `lib/ezdiff/classes/ezdiffengine.php:21` | – |
| `eZDiffMatrix` | class | – | `lib/ezdiff/classes/ezdiffmatrix.php:21` | – |
| `eZDiffTextEngine` | class extends eZDiffEngine | [Strategy / handler](strategy-handler.md) | `lib/ezdiff/classes/ezdifftextengine.php:21` | – |
| `eZDiffXMLTextEngine` | class extends eZDiffEngine | [Strategy / handler](strategy-handler.md) | `lib/ezdiff/classes/ezdiffxmltextengine.php:18` | – |
| `eZTextDiff` | class extends eZDiffContent | – | `lib/ezdiff/classes/eztextdiff.php:21` | – |
| `eZXMLTextDiff` | class extends eZDiffContent | – | `lib/ezdiff/classes/ezxmltextdiff.php:18` | – |
