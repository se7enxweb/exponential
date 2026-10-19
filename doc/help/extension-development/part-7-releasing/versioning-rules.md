---
title: Versioning rules
audience: developer, designer, app-creator
category: extension-development
order: 10
summary: What the numbers of an extension's version mean, the rule that the last position keeps counting (1.0.9 is followed by 1.0.10), when to raise a higher position, and how Composer reads the numbers.
keywords: version, versioning, semantic versioning, major, minor, patch, 1.0.10, last position, composer constraint, tilde, caret, tag name
type: help
---
# Versioning rules

Part 7, chapter 1. A version number tells users how much changed and whether it is safe to update. This chapter
gives the rules this project follows for its extensions. They are close to [Semantic Versioning](https://semver.org),
with one point that people get wrong often enough to deserve its own section.

## The positions

| Position | Raise it when | Example |
|---|---|---|
| Last (patch) | you fix something, add translations, change texts, and nothing that worked before stops working | 1.0.8 to 1.0.9 |
| Middle (minor) | you add a feature or an API, and old code still works | 1.0.9 to 1.1.0 |
| First (major) | something that worked before stops working: a removed setting, a renamed template, a higher PHP minimum that drops users | 1.4.2 to 2.0.0 |

Some packages of this project have four positions (`0.0.4.10`); the rule is the same, applied to the last one.

## The last position keeps counting

After `.9` comes `.10`. Not the next middle position.

```text
1.0.8  ->  1.0.9  ->  1.0.10  ->  1.0.11
```

There is no "running out" of digits: each position is an integer. Raising a higher position is a decision about what
the release means, never a consequence of reaching nine. This project released `v0.0.4.10` after `v0.0.4.9` on
22 September 2026, and both Packagist and GitHub order it correctly: Packagist compares each position as a number,
and GitHub Releases orders by the date of the tag.

The one place where `.10` causes trouble is sorting tags by name, which puts `1.0.10` before `1.0.8`. That is a
sorting problem, solved in [Choosing the next version number](choosing-the-next-version.md).

## Tags and versions

| | Format | Example |
|---|---|---|
| Git tag | the version, usually with a `v` in front | `v1.0.10` |
| `ezinfo.php`, `extension.xml`, `composer.json` | the version without `v` | `1.0.10` |

Some repositories tag without the `v` (`4.1.5`). Pick one style per repository and keep it; look at the existing tags
before the first release of an adopted extension.

## How users' constraints read your numbers

Users write constraints in their `composer.json`. Your numbering decides what those constraints pick up:

| Constraint | Means | Picks up |
|---|---|---|
| `~1.0.9` | at least 1.0.9, below 1.1.0 | 1.0.10, 1.0.11: your fixes |
| `^1.0` | at least 1.0.0, below 2.0.0 | every 1.x: fixes and features |
| `1.0.9` | exactly that | nothing new |

So a breaking change released as `1.0.10` reaches everyone with `~1.0.9` or `^1.0`, without warning. That is why the
first position must move when something breaks.

## What is not a version

`dev-master`, `dev-main`, `//autogentag//` and an empty string are not versions. Never write them into `ezinfo.php`
or `extension.xml` ([Bumping ezinfo.php and extension.xml](bumping-ezinfo-and-extension-xml.md)).

## Related pages

- Previous: [Part 7: Releasing your extension](README.md)
- Next: [Choosing the next version number](choosing-the-next-version.md)
- [Semantic Versioning](https://semver.org)
- [Versions and constraints](https://getcomposer.org/doc/articles/versions.md) on getcomposer.org
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
