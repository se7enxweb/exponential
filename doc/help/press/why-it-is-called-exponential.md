---
title: Why it is called Exponential
audience: press, advocate
category: press-media
order: 8
summary: The facts behind the product name - the announcement of 11 August 2025, the rename in the repository the next day, the first release under the name, what changed and what did not, and how to write about it without speculation.
keywords: name, rename, rebranding, Exponential, eZ Publish, Ibexa, August 2025, 6.0.10, trademark, Exponential CMS, why the name
type: help
---
# Why it is called Exponential

This page is for journalists who explain the product's name. It sticks to what the sources say. Several
reporters have been tempted to fill the gaps with a story; this page tells you where the gaps are.

## Contents

1. [What happened](#what-happened)
2. [The words of the announcement](#the-words-of-the-announcement)
3. [What changed and what did not](#what-changed-and-what-did-not)
4. [How to write about it](#how-to-write-about-it)
5. [What the sources do not say](#what-the-sources-do-not-say)

## What happened

| Date | Event | Source |
|---|---|---|
| 11 August 2025 | The project website announces the new name | <https://exponential.earth/about/project> |
| 12 August 2025 | The README, `composer.json` and `lib/version.php` change in the repository; one commit renames the product in 332 files of `bin/`, `doc/`, `design/` and the translations | [August 2025](../../history/2025/2025-08.md) |
| 28 August 2025 | 6.0.10, the first release named Exponential | [The 6.0.x releases](../who-is-7x.md#the-60x-releases) |

## The words of the announcement

From <https://exponential.earth/about/project>, quoted on [Who is 7x?](../who-is-7x.md#why-the-product-is-called-exponential):

> "At the specific request of Ibexa we are changing this projects name to 'Exponential' or 'Exponential (CMS)'
> effective as of August, 11th 2025."

The same page and the README carry the notice: "This project is not associated with the original eZ Publish
software or its original developer, eZ Systems or Ibexa".

For the platform line the README adds that it was rebranded "to avoid any trademark issues"
([Brand and naming rules](../business/brand-and-naming-rules.md#what-the-sources-do-not-decide)).

## What changed and what did not

| Changed | Did not change |
|---|---|
| The product name in the installer, administration, command line, translations, documentation and package server | PHP class names such as `eZContentObject` and `eZINI` |
| The README and the package description | Database tables such as `ezcontentobject` |
| The constant `EDITION` in `lib/version.php`, which reads `Exponential` | Script names such as `bin/php/ezcache.php` |
| | Extension package names such as `ezwebin` and `eztags` |

The rename "touched text, not identifiers" ([The product is called Exponential](../../features/6.0/rebranding-to-exponential.md)).
That is why readers still see `eZ` in code: it is a prefix of real identifiers, and renaming them would break every
site and extension.

## How to write about it

```text
Good:   "The project renamed the product Exponential in August 2025, at the request of Ibexa, according to the
         project website."
Good:   "Exponential, as the software has been called since August 2025, ..."
Avoid:  "Exponential, formerly known as eZ Publish, the CMS by Ibexa ..."   (suggests a link to Ibexa)
Avoid:  "eZ Publish rebrands as Exponential"                                 (eZ Publish was not the actor)
Avoid:  "after a trademark dispute"                                          (no source says there was one)
```

Name the old product in the past tense only ([Writing about the eZ Publish years](writing-about-the-ez-publish-years.md)).

## What the sources do not say

- Why Ibexa asked, or how the request was made.
- Whether there was any legal procedure. Do not write "dispute", "lawsuit" or "forced".
- Who chose the word "Exponential" or what it is meant to evoke.

If your story needs any of these, ask 7x and the Foundation and attribute their answer
([Questions to ask 7x](questions-to-ask-7x.md)).

## Related pages

- [Not associated with eZ Systems or Ibexa](not-associated-with-ez-systems-or-ibexa.md)
- [Brand and naming for media](brand-and-naming-for-media.md)
- [The product is called Exponential](../../features/6.0/rebranding-to-exponential.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
