---
title: Patterns in kernel/classes/contentfinder
audience: developer
category: standards
order: 488
summary: Every class, view, fetch function, operator and plugin point of kernel/classes/contentfinder with its design patterns.
keywords: kernel/classes/contentfinder, design patterns, classes, extension points
type: catalogue
---
# Patterns in kernel/classes/contentfinder

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `kernel/classes/contentfinder` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (3)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expContentFinder` | class | – | `kernel/classes/contentfinder/expcontentfinder.php:28` | The content finder (module contentfinder, doc/eng-us/features/6.0/content-finder.md): finds content objects by an id or a path and describes each with what… |
| `expContentFinderAction` | class | [Utility (static helper)](utility.md) | `kernel/classes/contentfinder/expcontentfinderaction.php:33` | The actions of the content finder (doc/eng-us/features/6.0/content-finder.md, "Actions"): move an object to the trash, delete it permanently, purge it from… |
| `expContentFinderInput` | class | [Utility (static helper)](utility.md) | `kernel/classes/contentfinder/expcontentfinderinput.php:28` | What the content finder (module contentfinder, doc/eng-us/features/6.0/content-finder.md) makes of its one search field: the kinds of id or path the text ca… |
