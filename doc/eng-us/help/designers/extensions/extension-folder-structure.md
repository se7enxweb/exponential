---
title: "The folder structure of a theme extension"
audience: designer, developer
category: theme-extensions
order: 40
summary: Every folder and file a theme extension can hold, what reads it, and which ones are required, shown on a complete tree and compared with three published themes.
keywords: folder structure, directory layout, design folder, settings folder, autoloads, translations, share, packages, modules, classes, extension tree
type: help
---
# The folder structure of a theme extension

This page is for designers who want to know where each file of a theme goes. How the folders below `design/<name>/`
are organized in detail is explained in [Design directory layout](../theme-design/design-directory-layout.md).

## The complete tree

```
extension/mytheme/
├── ezinfo.php                      metadata for the About page (required)
├── extension.xml                   packaged metadata, read first (required)
├── composer.json                   when the theme is a Composer package
├── LICENSE                         the license text (required for publishing)
├── README.md                       what it is and how to switch it on
├── INSTALL.md, CHANGELOG.md        install steps and history (recommended)
├── design/
│   └── mytheme/                    the design (its name is the SiteDesign value)
│       ├── templates/              pagelayout.tpl and the templates
│       ├── override/templates/     templates chosen by override rules
│       ├── stylesheets/
│       ├── javascript/
│       ├── images/
│       └── fonts/
├── settings/
│   ├── design.ini.append.php       DesignExtensions[]=mytheme (required)
│   ├── site.ini.append.php         optional
│   ├── override.ini.append.php     override rules (optional)
│   ├── mytheme.ini                 a settings file of your own (optional)
│   └── siteaccess/<siteaccess>/    settings for one siteaccess (optional)
├── autoloads/                      template operators (optional)
├── classes/                        PHP classes (optional)
├── modules/                        modules with views (optional)
├── translations/<locale>/translation.ts
├── share/                          demo images, files, database seed data
└── packages/                       .ezpkg files shipped with the theme
```

## What reads each part

| Part | Read by | Page |
|---|---|---|
| `ezinfo.php`, `extension.xml` | Setup > About, Setup > Extensions, the upgrade checks | [ezinfo.php](ezinfo-php.md), [extension.xml](extension-xml.md) |
| `design/<name>/` | The template system, once `DesignExtensions[]` names the extension | [Registering the design](registering-the-design.md) |
| `settings/*.ini.append.php` | The settings system, for every active extension | [Settings files in an extension](settings-files-in-an-extension.md) |
| `settings/siteaccess/<sa>/` | The settings system, only while that siteaccess runs | [Siteaccess settings inside a theme](siteaccess-settings-in-an-extension.md) |
| `autoloads/`, `classes/` | The autoload array, after `php bin/php/ezpgenerateautoloads.php -e` | [Template operators in a theme](template-operators-in-a-theme.md) |
| `translations/` | The translation system when `TranslationExtensions[]` names the extension | [Translations in a theme](translations-in-a-theme.md) |
| `share/` | Installers and your own scripts | [The share folder](share-folder-assets.md) |
| `packages/` | Whoever imports them with ezpm or Setup > Packages | [Bundling content class packages](bundling-content-class-packages.md) |

## Three published themes compared

| Folder | `sevenx_themes_simple` | `cjw_themes_jumper` | `sevenx_themes_media` |
|---|---|---|---|
| `design/` | `simple` | `starter`, `standard` | `media` |
| `settings/siteaccess/` | yes | `cjw_starter` | yes |
| `classes/`, `modules/` | - | yes | yes |
| `translations/` | yes | `ger-DE` | yes |
| `packages/` | 20 content class packages | - | - |
| `share/` | - | `content-images`, `content-files` | database schema and data |
| `CHANGELOG.md` | - | yes | - |

Read the trees yourself: `ls extension/cjw_themes_jumper` on an installation that has it, or browse the repository on
GitHub.

## Step by step: create the skeleton by hand

1. `mkdir -p extension/mytheme/design/mytheme/{templates,stylesheets,javascript,images} extension/mytheme/settings`
2. Write `settings/design.ini.append.php` ([Registering the design](registering-the-design.md)).
3. Write `ezinfo.php` and `extension.xml`.
4. Add `LICENSE` and `README.md`.
5. Check: `php -l extension/mytheme/ezinfo.php` and `xmllint --noout extension/mytheme/extension.xml`.

Or let [the design extension wizard](design-extension-wizard.md) write all of it.

## Related pages

- [Tutorial: create a theme extension](../theme-design/tutorial-create-a-theme-extension.md)
- [What a theme extension is](what-a-theme-extension-is.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
