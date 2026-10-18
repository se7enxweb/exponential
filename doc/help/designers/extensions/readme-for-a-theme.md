---
title: "Writing the README of a theme"
audience: designer
category: theme-extensions
order: 100
summary: The sections a theme's README.md needs - what it is, a screenshot, requirements, install, switch on, configure, license and support - in the order a reader looks for them, with a template to copy.
keywords: README, README.md, documentation, install instructions, requirements, screenshot, support, template
type: help
---
# Writing the README of a theme

This page is for designers preparing a theme repository. The README is the first page people see on GitHub and on
Packagist. It decides whether they try the theme.

## The sections, in order

1. **Name and one sentence**: what the theme is and for which sites.
2. **A screenshot** of the front page ([Screenshots for a theme](../distribution/screenshots-for-a-theme.md)).
3. **Requirements**: the Exponential versions and PHP versions you tested, and any extension it needs.
4. **Install**: with Composer, and by copying the folder.
5. **Switch it on**: the settings lines, the autoload and cache commands.
6. **Configure**: its own settings file and the values worth changing.
7. **What is inside**: the design names, the siteaccess settings, operators, packages.
8. **License** and third-party notices.
9. **Support and issues**: where to report a problem, and how to report a security issue privately.
10. **Changelog**: a link to `CHANGELOG.md`.

## A template to copy

````markdown
# My Theme

A responsive theme for Exponential 6 sites with articles, events and a blog.

![The front page in light mode](doc/screenshots/front-light.png)

## Requirements

- Exponential 6.0 (tested with 6.0.15), PHP 8.1 to 8.5

## Install

```bash
composer require yourvendor/mytheme
```

## Switch it on

Add `ActiveExtensions[]=mytheme` to `[ExtensionSettings]` and `SiteDesign=mytheme` to the siteaccess, then:

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all
```

## License

GNU General Public License v2.0 or later; see LICENSE. Fonts: see design/mytheme/fonts/LICENSE.

## Support

Issues: https://github.com/yourvendor/mytheme/issues. Security problems: see SECURITY.md.
````

## Step by step

1. Copy the template into `README.md` at the theme's root.
2. Replace every value with what is true for your theme. Write only versions you tested.
3. Run every command in the README on a test installation and correct what does not work.
4. Read it on GitHub after the first push; fix broken images and links.

## Good practice

- Write in American English and call the product Exponential.
- Keep install commands copyable: no prompts, no placeholders inside a command without saying so.
- Never paste credentials, hostnames of real customers or personal data into a README.

## Related pages

- [A README that explains and sells](../distribution/readme-that-explains-and-sells.md)
- [INSTALL.md and CHANGELOG.md](install-and-changelog-files.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
