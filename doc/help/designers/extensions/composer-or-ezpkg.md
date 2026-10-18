---
title: "Composer package or .ezpkg: which to ship"
audience: designer, project-manager
category: theme-extensions
order: 440
summary: A decision table for shipping a theme as a Composer package, as an Exponential package (.ezpkg), or both - by who installs it, what it carries, how it is updated and where it is found.
keywords: composer or ezpkg, distribution format, decision, packaging, Packagist, ezpkg, update, install method
type: help
---
# Composer package or .ezpkg: which to ship

This page is for designers deciding how to hand out a theme. There is no single right answer; the three published
7x themes are Composer packages, and one of them carries `.ezpkg` files inside it.

## The decision table

| Question | Composer package | `.ezpkg` |
|---|---|---|
| Who installs it? | Developers and hosts who run `composer` | Administrators who work in **Setup > Packages** |
| What does it carry? | Files: the theme extension | Files **and** database content: classes, objects, a site style |
| How is it updated? | `composer update yourvendor/mytheme` | Installing the next package version |
| Where is it found? | Packagist, GitHub | Wherever you put the file; listed on project pages |
| Version history? | Tags and releases in Git | Version and changelog inside the package |
| Dependencies? | `require` installs them | Package dependencies, installed by hand |

## Three common answers

1. **Composer only**: a theme that works with the content classes the site already has. Simplest to maintain.
2. **Composer with `.ezpkg` inside**: the theme needs its own classes. Ship them in `packages/` and explain the import
   in `INSTALL.md`, as `sevenx_themes_simple` does with 20 class packages.
3. **`.ezpkg` only**: a color scheme for an existing design ([The site style package](site-style-package.md)), or
   an audience that does not use Composer.

## Step by step

1. Answer the first two rows of the table for your theme.
2. Pick one of the three answers.
3. Write the install steps for it in the README and try them on a test installation
   ([Testing a Composer install](testing-a-composer-install-locally.md),
   [Installing your package on a test site](installing-your-package-on-a-test-site.md)).

## Related pages

- [What an Exponential package is](what-an-exponential-package-is.md)
- [composer.json for a theme](composer-json-for-a-theme.md)
- [Choosing where to publish](../distribution/choosing-where-to-publish.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
