---
title: Naming and branding during an upgrade
audience: developer, designer, app-creator
category: extension-development
order: 210
summary: What to rename when you upgrade an old extension and what never to touch: product names in texts, new code with exp, kernel identifiers, folder names, settings - and how to keep credit for the original authors.
keywords: naming, branding, rename, Exponential, exp prefix, eZ identifiers, folder name, translation context, credit, original author, upgrade
type: help
---
# Naming and branding during an upgrade

Part 4, chapter 21. An old extension speaks of the product under the name of its old releases, uses an `ez` prefix
for everything, and may carry the name of a company that no longer maintains it. When you upgrade it, some of those
names should change and many must not. Renaming the wrong thing breaks sites; renaming nothing leaves users
confused. This chapter draws the line.

## The rule in one sentence

Rename what **people read**; never rename what **code or settings refer to**.

| Kind of name | Rename? | Why |
|---|---|---|
| Product name in interface texts, README, documentation, `ezinfo.php` `Name` | yes: "Exponential" | that is what the product is called |
| The extension's folder name | no | settings (`ActiveExtensions[]`), templates (`design:`), other extensions and `composer.json` refer to it |
| Existing class names, function names, template operator names | no | other code calls them; a renamed operator breaks every template that uses it |
| Kernel identifiers such as `eZContentObject`, `eZINI`, `eZDB` | never | they are the kernel's real names |
| INI file names, sections and settings | no | sites have overrides for them |
| Database tables and columns | no | renaming needs an upgrade script and breaks other readers |
| Translation contexts (`'extension/myoldext'`) | no | existing translations are keyed by them |
| New classes, files, operators, settings you add | use `exp` for Exponential project code, your own prefix otherwise | the convention for new code; see [Naming prefixes: sevenx and exp](../../../standards/naming-prefixes-sevenx-and-exp.md) |

## Product names in texts

Search the extension for the old product name in what people read:

```bash
grep -rniE 'ez ?publish' --include='*.tpl' --include='*.md' --include='*.txt' --include='*.ts' extension/myoldext/ | grep -v '^\S*:\s*[0-9]*:\s*//'
```

Change it in:

- interface texts in templates (and update the translations that translate them);
- the README and the documentation;
- `ezinfo.php` and `extension.xml` (`Name`, the summary);
- the `description` of `composer.json`;
- error messages your PHP code shows to users.

Leave it where it is history: a changelog entry of 2012 says what the product was called in 2012, and a license
header that names the old product as the work it was written for stays as written (it is part of the license notice).

> **Note**
> Do not change text inside a string that is an identifier: a `composer.json` `type` of `ezpublish-legacy-extension`
> or an `extra` key `ezpublish-legacy-extension-name` are names the Composer installer looks for. They stay exactly
> as they are.

## The ez prefix and new code

Old extensions name everything with `ez` because the kernel did. In Exponential 6 the convention for **new** code is
`exp` (or, for 7x's own extensions, `sevenx`): a new module, class, template operator or setting is `exp...`, never
`ez...`; your own extension uses your own prefix. The reasons are in [Naming prefixes: sevenx and exp](../../../standards/naming-prefixes-sevenx-and-exp.md). Existing names keep their
prefix: renaming `ezmyoperator` to `expmyoperator` breaks every template written for it.

If you must offer a new name (because the old one collides, or is misleading), add the new one and keep the old one
working:

- a template operator: register both names in `operatorList()`, and say in the changelog which is deprecated;
- a class: keep the old class as a subclass of the new one;
- a setting: read the new one, fall back to the old one.

Remove the old name only in a major release, announced in advance.

## Real examples

se7enxweb/explayouts_ui_api, commit 2a95edc, renamed a text people see: the application edition in the logo tooltip
became "Exponential Layouts". Its later commit 8273a03 went further and renamed the editor's page tags, globals and
session key from the upstream project's prefix to `explayouts` names, because those were the extension's own
internals, used by nobody else, and the editor was changed as a whole in that commit.

se7enxweb/expchangeclass is a fork of an older extension: it has a new name for the package
(`se7enxweb/expchangeclass`, `Name` "Exponential Change Class") and keeps the original's name in its description
("A hardened fork of eZChangeclass") and in its `Includes` credit. Its folder name is new as well, because it was
published as a new extension, not as an upgrade in place: a site moving to it changes its `ActiveExtensions[]`.

## When you fork, the name is a promise

If you publish an upgraded copy of someone else's extension:

- **Use a name that does not pretend to be the original.** A different package name (your account) is required by
  Packagist anyway; a different display name ("... for Exponential 6", or a new name) tells users who maintains it.
- **Keep the original author's name** in the copyright lines, in `composer.json` `authors` and in an `Includes` entry
  of `ezinfo.php`. Removing it is not "rebranding", it is taking credit for someone else's work.
- **Do not use another company's logo or trademark** for your copy.

The ethics of this are a chapter of their own: [Rebranding ethically](../part-5-extend-fork-contribute/rebranding-ethically.md).

## Checklist

- [ ] Interface texts, README and metadata say "Exponential".
- [ ] Folder name, class names, operator names, settings, tables and translation contexts are unchanged.
- [ ] New code you added uses `exp` (project code) or your own prefix.
- [ ] Any new name for an old one keeps the old one working, with a deprecation note.
- [ ] The original authors are named in the copyright lines, `composer.json` and `ezinfo.php`.

## Related pages

- Previous: [Context menus and admin integration](context-menus-and-admin-integration.md)
- Next: [Tests and CI for an upgraded extension](tests-and-ci-for-an-upgraded-extension.md)
- [Attribution and copyright notices](../part-5-extend-fork-contribute/attribution-and-copyright-notices.md)
- [Translations in an extension](../part-3-extension-developer/translations-in-an-extension.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
