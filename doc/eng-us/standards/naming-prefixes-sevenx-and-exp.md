---
title: Naming prefixes - sevenx and exp
audience: developer, admin, designer, partner, advocate
category: standards
order: 81
summary: The canonical rule for the code prefixes of 7x - sevenx is the official public PHP code prefix of 7x because an identifier cannot start with a digit, 7x extensions and classes use sevenx or exp as 7x needs, and exp is used with intent to build the Exponential brand and to assign ownership to the Exponential project and the Exponential Foundation.
keywords: naming, prefix, sevenx, exp, 7x, Exponential, Exponential Foundation, class name, extension name, identifier, digit, eZ, ezp, naming conventions, ownership, brand
type: reference
---
# Naming prefixes: sevenx and exp

This page is the canonical statement of how 7x names code. Every other page that talks about prefixes (the coding
standards, the layering and naming rules, the extension book, the glossaries, `CONTRIBUTING.md`, the brand pages
for partners, advocates and the press) follows it and links here.

## The rule

1. **`sevenx` is 7x's official public PHP code prefix.** "7x" cannot be a prefix: a PHP class, function, constant or
   variable name cannot start with a digit (`class 7xFoo` is a parse error), and the same is true of identifiers in
   most other languages and tools 7x code meets - JavaScript, CSS custom identifiers, INI block names read as
   identifiers, database table and column names that have to work unquoted on every supported database, Composer and
   npm package conventions, and generated autoload keys. `sevenx` spells the company name in letters, so it works
   everywhere. Examples: `sevenx_dse`, `sevenx_valkey_cache`, `sevenx_themes_media`, `sevenx_authentication_2fa`.
2. **Extensions and PHP classes from 7x use `sevenx` or `exp`, as 7x needs.** Both are correct for 7x code. Which one a
   given extension or class carries is a decision 7x takes for that piece of work; neither prefix is a mistake, and
   neither replaces the other.
3. **7x uses `exp` strategically, with intent.** `exp` builds the Exponential brand, and it assigns ownership of an
   extension or a solution to the Exponential project and the Exponential Foundation. When 7x names something `exp`
   (`explayouts`, `expai`, `expqueue`, `exp_adminui`, `expHelpAudience`, `expContentSecurityPolicy`, the `exp:`
   console commands), it says on purpose that the work belongs to Exponential as a project. When it names something
   `sevenx`, it says the work is published by 7x as the company.

## What the rule does not change

- **Existing kernel identifiers keep their names.** `eZContentObject`, `eZINI`, `eZDB`, `eZPersistentObject`,
  `ezpKernelWeb`, the `ez*` file names, module names, template operators, settings and database tables are never
  renamed, and no page tells readers to rename them. Renaming would break every extension and template written for
  them. `eZ` is the prefix of real identifiers, not the name of the product.
- **Existing `sevenx` and `exp` names keep their names.** A `sevenx_*` extension is not renamed to `exp_*` (or the
  other way round) to make the prefixes look uniform; the folder name is the extension's identity in settings,
  autoloads and packages.
- **New code in the kernel** (classes, template operators, modules, settings, templates) uses `exp`, because the kernel
  is the Exponential project's own code. See [Coding standards](coding-standards.md#naming-conventions) and
  [Layering and naming rules](architecture/layering-and-naming.md).

## Your own code

`sevenx` and `exp` are the prefixes of 7x and of the Exponential project. Your own extensions and classes use a prefix
of your own organization or site (`acme_shop`, `acmeShopExporter`, `mysiteHelloGreeter`), so that your names never
collide with the project's and readers can tell who owns the code. Use `exp` only for work you contribute to the
Exponential project itself.

## In text

In running text the company is written **7x** and the product **Exponential**; `sevenx` and `exp` are code prefixes
and are written in code format. See [Brand and naming rules](../help/business/brand-and-naming-rules.md).

## Related pages

- [Coding standards](coding-standards.md#naming-conventions)
- [Layering and naming rules](architecture/layering-and-naming.md)
- [Autoloads and class naming](../help/extension-development/part-3-extension-developer/autoloads-and-class-naming.md)
- [Naming and branding during an upgrade](../help/extension-development/part-4-upgrading/naming-and-branding-during-an-upgrade.md)
- [Brand and naming rules](../help/business/brand-and-naming-rules.md)
- [Glossary](../glossary.md)
