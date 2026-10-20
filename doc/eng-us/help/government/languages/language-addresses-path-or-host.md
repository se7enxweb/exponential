---
title: "Language addresses: path or host"
audience: government, admin, website-creator
category: public-sector
order: 60
summary: How to give each language of a public site its own address in Exponential (a path such as /fr/, a host such as fr.example.gov, or both), the matching settings for each, and how to choose for a public body.
keywords: language URL, address per language, path, host, subdomain, MatchOrder, URIMatchType, HostMatchMapItems, HostUriMatchMapItems, HostMatchMethod, siteaccess matching
type: help
---
# Language addresses: path or host

This page is for administrators and site builders who decide how the language versions of a site are addressed.
Exponential decides which siteaccess answers a request by matching the address; the settings are in
`site.ini [SiteAccessSettings]` ([Language switchers and addresses per language](../../creators/website-creators/language-switcher-and-language-urls.md),
[10.2 Siteaccesses and site addresses](../../../install/10-after-installing.md#102-siteaccesses-and-site-addresses)).

## Contents

1. [The three shapes](#the-three-shapes)
2. [Choosing for a public body](#choosing-for-a-public-body)
3. [Path per language](#path-per-language)
4. [Host per language](#host-per-language)
5. [Test hosts](#test-hosts)
6. [After the change](#after-the-change)

## The three shapes

| Shape | Example | Set up with |
|---|---|---|
| Path per language | `www.example.gov/fr/` | `MatchOrder=uri`, `URIMatchType=element`, siteaccess named `fr` or a map |
| Host per language | `fr.example.gov`, or a separate domain per language | `MatchOrder=host`, `HostMatchMapItems[]=fr.example.gov;site_fr` |
| Host and path | `www.example.gov/fr` on one host, other hosts elsewhere | `host_uri` with `HostUriMatchMapItems[]` |

## Choosing for a public body

| Consideration | Path | Host |
|---|---|---|
| Certificates and DNS | one host | one name per language |
| Equal standing of official languages | the default language often has no prefix; give every language a prefix to avoid ranking one first | each language has its own name |
| Analytics and logs | one host, filter by path | separated by host |
| Moving later | changing scheme needs redirects | same |

Whichever you choose, use it everywhere and do not change it after launch without a redirect plan.

## Path per language

```ini
# settings/override/site.ini.append.php
[SiteAccessSettings]
AvailableSiteAccessList[]=en
AvailableSiteAccessList[]=fr
RelatedSiteAccessList[]=en
RelatedSiteAccessList[]=fr
MatchOrder=uri
URIMatchType=element
URIMatchElement=1
```

With siteaccesses named `en` and `fr`, `/en/...` and `/fr/...` reach them. Keep the entries the installer wrote for
the administration.

## Host per language

```ini
[SiteAccessSettings]
MatchOrder=host
HostMatchType=map
HostMatchMapItems[]=en.example.gov;site_en
HostMatchMapItems[]=fr.example.gov;site_fr
```

Add each host to DNS, the web server or Velocity, and the certificate
([Going live: hosts, HTTPS and siteaccesses](../../creators/website-creators/going-live-hosts-https.md)).

## Test hosts

`HostMatchMethod=start` lets a test host such as `fr.example.gov.test.local` reach the same siteaccess as
`fr.example.gov`; methods `strict` (default), `start`, `end` and `part` exist, globally or per entry. Use `start` and
`part` only where the web server answers for your own hosts alone
([Siteaccess by host prefix](../../../features/6.0/siteaccess-host-and-language-matching.md)).

## After the change

Clear the INI cache (`php bin/php/ezcache.php --clear-tag=ini`) and, with Velocity, its response cache. Open each
language address signed out and check `<html lang="...">` on the page.

## Related pages

- [Redirecting to the visitor's language](redirecting-to-the-visitors-language.md)
- [The language switcher](the-language-switcher.md)
- [Setting up several sites](../../creators/website-creators/multisite-setup.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
