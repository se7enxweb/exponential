# What a new installation comes with: the default extension distribution

This page is for anyone who installs Exponential and wants to know which extensions arrive with it, and how to add or
drop one. `composer.json` of Exponential does more than list PHP requirements: it decides which extensions arrive with
`composer create-project`. From December 2023 the maintainers built that list package by package.

## See the current list

```bash
grep -n '"se7enxweb/' composer.json
```

The first block of the output is `suggest`, the second `require`. The current `require` and `suggest` sections of
`composer.json` are the authority; the timeline below tells you when each piece joined.

## How it works

- Every extension is a Composer package of the vendor `se7enxweb`. The version constraints use `~`, which lets the
  last position float: `~6.0.8` accepts `6.0.9` and `6.0.10` but not `6.1.0`.
- `require` is installed with the product. `suggest` is only a hint printed by Composer; you install a suggested
  package yourself with `composer require se7enxweb/<name>`.
- The PHP requirement is `^8.1 || ^8.2 ...` (see [PHP 8 support](../../bc/6.0/php8.md)).
- In December 2023 the first release already switched the sources from third-party package names (`...-ls` and
  `...-ls-extension`) to `se7enxweb/<name>`. In January 2024 each package was renamed so that no Composer `replace`
  trick is needed.

## Add an extension

1. Install it:

   ```bash
   composer require se7enxweb/ezflow
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all --allow-root-user
   ```

2. Activate it in `settings/override/site.ini.append.php`:

   ```ini
   [ExtensionSettings]
   ActiveExtensions[]=ezflow
   ```

An installation made from a site package activates the extensions of its package for you.

## Leave one out

1. Remove it from `ActiveExtensions` and clear the caches.
2. Run `composer remove se7enxweb/<name>`.

If you installed with Git checkouts of the extensions instead of Composer, do not run `composer update`: it replaces
the checkouts.

## Timeline of the distribution (root `composer.json`)

| When | Release line | Change |
|---|---|---|
| 14-23 Dec 2023 | 6.0.0 | Repository branded as a 7x product; requirements point to `se7enxweb`; `ezgmaplocation`, `ezstarrating`, `ezwt` set to versions that install cleanly |
| 7 Jan 2024 | 6.0.1 | `bcgooglesitemaps` (sitemaps), `swark`, `owsimpleoperator` (extra template operators), `bcwebsitestatistics` (web statistics and Google Analytics); `ezmultiupload` raised to a release with important fixes |
| 23-29 Jan 2024 | 6.0.1 | `ezpaypal`, `ezodf`, `ezautosave`, `ezie`, `ezflow`, `ezdemo` and `ezwebin` renamed or added as first-class packages; `ezoracle` and `ezauthorize` suggested; `xrowmetadata` suggested |
| Feb 2024 | 6.0.2 | `bccie`, `xrowextract` (content export) added |
| Mar 2024 | 6.0.3 | `enhancedezbinaryfile`, `enhancedselection2` and `birthday` datatype extensions added; `bcurlaliaswithdash` suggested; `xrowmetadata` becomes a requirement |
| 11 Aug 2024 | 6.0.4 | `recaptcha` and `hcaptcha` anti-spam extensions added |
| 31 Aug - 5 Sep 2024 | 6.0.4 | `sevenx_valkey` (Valkey/Redis support) added, then moved to `suggest` in the same release so installs without a Redis server do not fail |
| 1 and 18 Oct 2024 | 6.0.5, 6.0.6 | `ezownerchange` and `git_manager` suggested |
| 2 Nov 2024 | 6.0.6 | `ezupdate` added |
| 13 May 2025 | 6.0.9 | `ezprestapi` 1.2 added: create, read, update and delete content through a REST API |
| 13 Aug 2025 | 6.0.10 | `sevenx_themes_simple` added so the default theme is always distributed |

Some entries moved between `suggest` and `require` later: today `ezoracle` and `git_manager` are in `require`, while
`ezauthorize`, `ezownerchange`, `bcurlaliaswithdash` and `sevenx_valkey` are still only suggestions. Since then the
list grew with the layout system, the newsletter, the tags extension and others.

## Related pages

- [Extensions, themes and packages](extensions/README.md), [extension list: sort, inspect and download](extension-list-and-downloads.md), [loading order and safe saving](extension-loading-order.md)
- [Extension metadata: `ezinfo.php` and `extension.xml`](../../specifications/6.0/extension-metadata.md)
- [The product is called Exponential](rebranding-to-exponential.md), [PHP 8 support](../../bc/6.0/php8.md)
- Changelogs that changed the extension set: [6.0.0](../../changelogs/6.0/6.0.0.md), [6.0.1](../../changelogs/6.0/6.0.1.md), [6.0.2](../../changelogs/6.0/6.0.2.md), [6.0.3](../../changelogs/6.0/6.0.3.md), [6.0.4](../../changelogs/6.0/6.0.4.md), [6.0.5](../../changelogs/6.0/6.0.5.md), [6.0.6](../../changelogs/6.0/6.0.6.md), [6.0.9](../../changelogs/6.0/6.0.9.md), [6.0.10](../../changelogs/6.0/6.0.10.md)
- History: [December 2023](../../history/2023/2023-12.md), [January 2024, first half](../../history/2024/2024-01a.md), [January 2024, second half](../../history/2024/2024-01b.md), [February 2024](../../history/2024/2024-02.md), [March 2024](../../history/2024/2024-03.md), [June 2024](../../history/2024/2024-06.md)
