---
title: "Version constraints your users write"
audience: designer, developer, admin
category: theme-distribution
order: 220
summary: Recommend a Composer version constraint for your theme in its README - tilde, caret, exact pins and dev-main - what each lets an update bring, and how your numbering makes them safe.
keywords: version constraint, composer require, tilde, caret, pin, dev-main, update, recommended constraint, composer.json root
type: help
---
# Version constraints your users write

This page is for designers who tell users how to require their theme. The constraint in the user's `composer.json`
decides which of your future releases `composer update` will install.

## The common constraints

| Constraint | Allows | Good for |
|---|---|---|
| `~1.0.24` | `>=1.0.24 <1.1.0` | Getting every fix of the `1.0` line, nothing bigger |
| `^1.0` | `>=1.0.0 <2.0.0` | Getting every release that keeps compatibility, if you move the middle position only for compatible additions |
| `1.0.24` | Exactly that | Sites that update only after their own review |
| `dev-main` | The newest commit on `main` | Testing unreleased work, never production |

The tilde floats the last position given: `~1.0.24` accepts `1.0.25` and also `1.0.100`. Because only the last
position counts up in normal releases ([After .9 comes .10](only-the-last-position-counts.md)), a tilde constraint
receives every normal release and never a deliberate step.

## What to recommend

Write one line in your README, matched to how you number releases. For example:

```bash
composer require yourvendor/mytheme:~1.0.24
```

and explain: "updates bring fixes and improvements of the 1.0 line; 1.1 will be announced with its changes".

## When you move a higher position

Say so in the release notes and the README: what changed, and that users must change their constraint to receive it
(`~1.1.0`). Users with `~1.0.24` stay on the `1.0` line until they decide.

## Step by step: check a constraint

```bash
composer why-not yourvendor/mytheme 1.1.0
```

Expected for `~1.0.24`: a line saying the root requires `~1.0.24`, which does not allow `1.1.0`.

## Related pages

- [require and suggest](../extensions/composer-require-and-suggest.md)
- [Breaking changes and the next major version](../lifecycle/breaking-changes.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
