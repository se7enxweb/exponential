# The product is called Exponential

This page is for anyone upgrading from a release before 6.0.10, and for site builders who want their own templates to
use the current name. From version 6.0.10 (August 2025) the software calls itself **Exponential**. The installer, the
admin interface, the command line scripts, the translations, the documentation and the package server all use the new
name. Only text that a person reads changed; every identifier stayed, so no site and no extension breaks.

## Find the old name on your own site

Your overrides, translations and custom templates are yours, so they were not renamed. List them:

```bash
grep -rIlE 'eZ[[:space:]]+Publish' settings/override settings/siteaccess design extension/*/design extension/*/settings 2>/dev/null
```

The output is a list of files. Replace the product name in the text of those files; leave class names and paths alone.

## Upgrade checklist

1. Pull the 6.0.10 or later release and clear the caches:

   ```bash
   php bin/php/ezcache.php --clear-all --allow-root-user
   ```

2. If you pinned the package server URL in your own `package.ini` override, remove the pin or point it at the
   Exponential package server (see the table below). The old address still answers for old installs.
3. Reload PHP-FPM so the new `lib/version.php` is seen. The constant `EDITION` reads `Exponential`; the class is
   `ExponentialSDK` from 6.0.15.
4. If you maintain your own file list for the upgrade check, regenerate it (see
   [File consistency check](file-consistency-check.md)).

## What changed

| Where | Before | Now |
|---|---|---|
| `lib/version.php`, `EDITION` | the old product name | `Exponential` (constant `EDITION`; the class itself was renamed to `ExponentialSDK` only in the 6.0.15 line, the old class name still works) |
| Composer package | the old vendor and product name | `se7enxweb/exponential` and `se7enxweb/exponential-legacy-installer` |
| Setup wizard | old name in headings, text and links | "Exponential" everywhere |
| Admin login and top bar | old logo and copyright | Exponential logo and the updated copyright text |
| `bin/`, `design/`, `doc/`, `share/translations/` | old name in help texts, templates and translation files | renamed in one sweep of 332 files (13 August 2025, `837c7b835d`), followed by clean-up commits |
| `settings/package.ini` | package server at the old domain | `RemotePackagesIndexURL` and `RemotePackagesIndexURLBase` point to the Exponential package server (first moved to a brand domain in January 2025; the old address redirects transparently, according to the commit message of `5fb85b97f0`; not re-tested here) |
| Standard design logo | old logo image | the Exponential logo (April 2026) |

The footer of the default theme prints a "Powered by" line. The theme `sevenx_themes_simple` (required by
`composer.json` since 1.0.4) restructured `page_footer.tpl` so that line is easy to remove: copy the template into your
own design extension and delete the block.

## What did not change, on purpose

The rename touched text, not identifiers. These stay exactly as they were:

- PHP classes with the `eZ` prefix (`eZContentObject`, `eZINI`, `eZDB`, `eZPersistentObject`) and their file names;
- the `ezpublish-legacy` style paths and database table names (`ezcontentobject` and the rest);
- template operators, fetch functions, INI block and key names;
- the Composer packages of the extensions (`se7enxweb/ezwebin` and so on).

So a template you wrote in 2012 keeps working.

## Related pages

- [A clean installation that fits shared hosting and says Exponential](clean-install-defaults.md)
- [About and Copyright pages](about-and-package-pages.md), [package licenses and Semantic Versioning](package-licenses-and-versions.md)
- [Default extension distribution](default-extension-distribution.md), [file consistency check](file-consistency-check.md)
- [Changelog 6.0.10](../../changelogs/6.0/6.0.10.md)
- History: [August 2025](../../history/2025/2025-08.md), [July 2025](../../history/2025/2025-07.md), [January 2025](../../history/2025/2025-01.md)
