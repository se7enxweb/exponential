---
title: Patterns in lib/ezpdf
audience: developer
category: standards
order: 549
summary: Every class, view, fetch function, operator and plugin point of lib/ezpdf with its design patterns.
keywords: lib/ezpdf, design patterns, classes, extension points
type: catalogue
---
# Patterns in lib/ezpdf

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `lib/ezpdf` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (5)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `Cezpdf` | class extends Cpdf | – | `lib/ezpdf/classes/class.ezpdf.php:18` | – |
| `Cpdf` | class | – | `lib/ezpdf/classes/class.pdf.php:18` | – |
| `eZPDF` | class | [Template operator (Interpreter plugin)](template-operator.md) | `lib/ezpdf/classes/ezpdf.php:22` | – |
| `eZPDFTable` | class extends Cezpdf | – | `lib/ezpdf/classes/class.ezpdftable.php:18` | – |
| `eZPDFTrueTypeFont` | class | – | `lib/ezpdf/classes/eztruetypefont.php:24` | Reads a TrueType font well enough to embed it in a pdf as a composite font: the character to glyph mapping, the advance widths, the descriptor the pd… |

## Template operators (1)

| Operators | Class | Registered in |
|---|---|---|
| pdf | `eZPDF` | `lib/ezpdf/classes/eztemplateautoload.php:14` |
