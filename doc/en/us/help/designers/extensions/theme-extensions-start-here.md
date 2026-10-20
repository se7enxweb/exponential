---
title: "Theme extensions: start here"
audience: designer, developer
category: theme-extensions
order: 10
featured: true
summary: The map of everything about packing a design into a theme extension - folder structure, ezinfo.php and extension.xml, the settings a theme ships, the RAD design wizard, Composer packages and Exponential packages (.ezpkg) - with a suggested reading order.
keywords: theme extension, design extension, extension structure, ezinfo.php, extension.xml, composer.json, ezpkg, package wizard, RAD, design wizard, packaging, start here
type: help
---
# Theme extensions: start here

A design you build for Exponential becomes useful to other people when it lives in a **theme extension**: one folder
under `extension/` that holds the templates, stylesheets, scripts, images, settings and the small metadata files
that tell an installation who the theme is. This section shows how to build that folder correctly and how to pack it
as a Composer package or as an Exponential package (`.ezpkg`).

How the design itself is made (templates, overrides, stylesheets, dark mode, accessibility) is explained in
[Theme design](../theme-design/design-extensions.md) and [Templates](../templates/templates-overview.md). This
section is about the container around it.

## A reading order

1. Understand the idea: [What a theme extension is](what-a-theme-extension-is.md),
   [Naming your theme extension](naming-your-theme-extension.md),
   [The folder structure of a theme extension](extension-folder-structure.md).
2. Generate a skeleton: [The design extension wizard](design-extension-wizard.md), then
   [From the wizard's output to the first commit](from-wizard-output-to-first-commit.md).
3. Describe it: [ezinfo.php](ezinfo-php.md), [extension.xml](extension-xml.md),
   [Keeping every version field in sync](keeping-version-fields-in-sync.md).
4. Wire it in: [Registering the design](registering-the-design.md),
   [Activating a theme](activating-a-theme-extension.md), [Loading order](loading-order-for-themes.md).
5. Pack it: [composer.json for a theme](composer-json-for-a-theme.md) or
   [the package creator wizard](package-creator-wizard-step-by-step.md);
   [Composer package or .ezpkg](composer-or-ezpkg.md) helps you choose.
6. Check it: [Checklist: the theme extension is ready](checklist-theme-extension-ready.md).

Then continue with [Distributing a theme](../distribution/distribution-start-here.md).

## Every page of this section

| Page | What it answers |
|---|---|
| [What a theme extension is](what-a-theme-extension-is.md) | Why a design belongs in an extension |
| [Naming your theme extension](naming-your-theme-extension.md) | Which names are allowed and which are wise |
| [The folder structure of a theme extension](extension-folder-structure.md) | Which folder holds what |
| [ezinfo.php](ezinfo-php.md) | The metadata the About page reads |
| [extension.xml](extension-xml.md) | The packaged description, read first |
| [Keeping every version field in sync](keeping-version-fields-in-sync.md) | Where a version number lives |
| [Copyright and author lines](copyright-and-author-lines.md) | Who to name and how |
| [The LICENSE file of a theme](license-file.md) | Which file, which text |
| [Writing the README of a theme](readme-for-a-theme.md) | What a reader needs first |
| [INSTALL.md and CHANGELOG.md](install-and-changelog-files.md) | Install steps and history |
| [Registering the design](registering-the-design.md) | `DesignExtensions[]` |
| [The design settings a theme ships](design-settings-a-theme-ships.md) | `SiteDesign`, `AdditionalSiteDesignList[]`, stylesheet lists |
| [Settings files in an extension](settings-files-in-an-extension.md) | `.ini.append.php` files |
| [Siteaccess settings inside a theme](siteaccess-settings-in-an-extension.md) | `settings/siteaccess/<name>/` |
| [Giving a theme its own settings file](your-own-ini-file.md) | A `mytheme.ini` |
| [Activating a theme](activating-a-theme-extension.md) | `ActiveExtensions[]` and `ActiveAccessExtensions[]` |
| [Loading order and which theme wins](loading-order-for-themes.md) | Priority between extensions |
| [Declaring dependencies in extension.xml](dependencies-in-extension-xml.md) | `<dependencies>` |
| [Template operators in a theme](template-operators-in-a-theme.md) | PHP helpers for templates |
| [Translations in a theme](translations-in-a-theme.md) | `translations/<locale>/translation.ts` |
| [The share folder](share-folder-assets.md) | Demo images, files and seed data |
| [Bundling content class packages](bundling-content-class-packages.md) | `.ezpkg` files inside a theme |
| [The design extension wizard](design-extension-wizard.md) | `/setup/designextension` |
| [The design wizard's parts](design-wizard-parts-reference.md) | Every checkbox of the wizard |
| [Downloading the wizard's result as a zip](design-wizard-download-as-zip.md) | Files without writing them |
| [From the wizard's output to the first commit](from-wizard-output-to-first-commit.md) | The first hour |
| [Putting a theme under Git](putting-a-theme-under-git.md) | One repository per theme |
| [A .gitignore for themes](gitignore-for-themes.md) | What never goes in |
| [composer.json for a theme](composer-json-for-a-theme.md) | The package description |
| [The Composer installer and the folder name](composer-installer-and-extension-name.md) | `ezpublish-legacy-extension-name` |
| [require and suggest](composer-require-and-suggest.md) | Dependencies of a theme |
| [Testing a Composer install](testing-a-composer-install-locally.md) | Before publishing |
| [What an Exponential package is](what-an-exponential-package-is.md) | `.ezpkg` explained |
| [The site style package](site-style-package.md) | Two stylesheets and images |
| [Exporting an extension as a package](extension-export-package.md) | The extension export |
| [The secret check](package-secret-check.md) | Why an export is refused |
| [The package creator wizard](package-creator-wizard-step-by-step.md) | `/package/create` |
| [Choosing a license in the package wizard](package-licenses-in-the-wizard.md) | The license list |
| [Classic XML and JSON packages](classic-and-json-package-formats.md) | The two formats |
| [Exporting and importing with ezpm](exporting-with-ezpm.md) | The command line |
| [Validating a package](validating-a-package.md) | Before you share it |
| [Installing your package on a test site](installing-your-package-on-a-test-site.md) | A safe trial |
| [Composer package or .ezpkg](composer-or-ezpkg.md) | Which to ship |
| [Checklist: the theme extension is ready](checklist-theme-extension-ready.md) | The final check |
| [Troubleshooting theme extensions](troubleshooting-theme-extensions.md) | Symptoms and fixes |

## Related pages

- [Extensions: find, install, configure, build and release](../../../guides/extensions.md)
- [Packages](../../../guides/packages.md)
- [Distributing a theme: start here](../distribution/distribution-start-here.md)
- [The maintenance life cycle: start here](../lifecycle/lifecycle-start-here.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
