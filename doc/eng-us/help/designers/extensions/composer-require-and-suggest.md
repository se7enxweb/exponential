---
title: "require and suggest: what a theme depends on"
audience: designer, developer
category: theme-extensions
order: 320
summary: Decide what goes into require and what into suggest in a theme's composer.json - PHP versions, the installer plugin, layouts or site API extensions - with the real choices of sevenx_themes_media and ezwebin, and version constraints that do not break users.
keywords: composer require, composer suggest, dependencies, version constraints, php version, tilde, caret, sevenx_themes_media, ezwebin
type: help
---
# require and suggest: what a theme depends on

This page is for designers deciding what their theme's `composer.json` should pull in.

## The rule of thumb

| Put it in `require` when | Put it in `suggest` when |
|---|---|
| The theme fails without it (a fatal error, a blank page) | The theme works, but a feature is missing without it |
| It is the installer plugin that places the theme | It is content or data the user may already have |
| It is the PHP version range you test | It is an alternative the user may prefer |

## Real choices

- `sevenx_themes_media` requires only PHP (`^8.0 || ^8.1 || ^8.2 || ^8.3 || ^8.4`) and **suggests**
  `se7enxweb/explayouts` (renders the layout structure its templates are written for), `se7enxweb/expsite-data-media`
  (the content model and data it renders) and `se7enxweb/expsite-api`.
- `ezwebin` **requires** `se7enxweb/ezpublish-legacy-installer` and `se7enxweb/expui`, and suggests three smaller
  extensions with a `~5.3` constraint.

Both are reasonable: the media theme leaves the choice of data to the site; ezwebin cannot draw without expui.

## Version constraints

| Constraint | Allows | Use for |
|---|---|---|
| `^1.2` | `>=1.2.0 <2.0.0` | Libraries that follow semantic versioning |
| `~1.2.3` | `>=1.2.3 <1.3.0` | Packages that only add fixes in the last position |
| `~0.0.4.9` | `>=0.0.4.9 <0.0.5.0` (so `0.0.4.10` is included) | Four-position versions where only the last moves |
| `*` | anything | Only for the installer plugin, if at all |

The tilde floats the last position given, so a series that counts `.9`, `.10`, `.11` stays inside it
([After .9 comes .10](../distribution/only-the-last-position-counts.md)).

## Step by step

1. List what your templates and operators call that is not in the kernel.
2. Sort each into `require` or `suggest` with the table above. A `suggest` entry's text says what it adds.
3. Write the narrowest PHP range you actually test.
4. Run `composer validate` and install the theme into a clean test installation to see that `require` is enough.
5. In `extension.xml`, declare the extensions you override or rely on for the loading order
   ([Declaring dependencies](dependencies-in-extension-xml.md)).

## Related pages

- [composer.json for a theme](composer-json-for-a-theme.md)
- [Version constraints your users write](../distribution/version-constraints-for-users.md)
- [PHP version compatibility](../lifecycle/php-version-compatibility.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
