---
title: Give a language its own address
audience: government, admin
category: public-sector
order: 50
summary: Task for administrators: create a siteaccess for a language and reach it by a path (/fr/) or a host (fr.example.gov), with the RegionalSettings that make it show that language first.
keywords: language address, language siteaccess, path, host, URIMatchType, HostMatchMapItems, SiteLanguageList, Locale
type: help
---
# Give a language its own address

This task is for administrators. Background: [A siteaccess per language](../../languages/a-siteaccess-per-language.md)
and [Language addresses: path or host](../../languages/language-addresses-path-or-host.md).

## Before you start

- The content language exists (see "Add a new content language").
- You decided between a path and a host.

## Steps

1. Copy `settings/siteaccess/site` to `settings/siteaccess/fr` (a path siteaccess is reached by its name).
2. In `settings/siteaccess/fr/site.ini.append.php` set:

   ```ini
   [RegionalSettings]
   Locale=fre-FR
   ContentObjectLocale=fre-FR
   SiteLanguageList[]
   SiteLanguageList[]=fre-FR
   SiteLanguageList[]=eng-US
   TextTranslation=enabled

   [SiteSettings]
   SiteName=<name of the site in French>
   ```

3. In `settings/override/site.ini.append.php` add `AvailableSiteAccessList[]=fr` and `RelatedSiteAccessList[]=fr`.
4. Matching: for a path, `MatchOrder=uri` with `URIMatchType=element` (shipped); for a host,
   `HostMatchMapItems[]=fr.example.gov;fr` with `MatchOrder=host` or `host;uri`, plus DNS, web server and certificate.
5. Clear caches: `php bin/php/ezcache.php --clear-tag=ini,content`; with Velocity also `./console exp:velocity cache clear --apply`.

## Check

- `https://www.example.gov/fr/` (or the host) answers, signed out, with `<html lang="fr-FR">`.
- **Setup > Languages** lists `fr` with its `SiteLanguageList` below the language cards.

## Problems

| Problem | Fix |
|---|---|
| The address shows the default site | the siteaccess is not in `AvailableSiteAccessList[]`, or matching order |
| Pages are hidden on the new site | they have no translation in the listed languages and are not always available |

## Related pages

- [10.2 Siteaccesses and site addresses](../../../../install/10-after-installing.md#102-siteaccesses-and-site-addresses)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
