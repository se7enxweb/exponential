---
title: Redirecting to the visitor's language
audience: government, admin, website-creator
category: public-sector
order: 70
summary: How the start page of a multilingual public site can send each visitor to their language once, by host and browser language, with DefaultHostUriMatchMapItems, without cookies, and how to keep the choice under the visitor's control.
keywords: language redirect, Accept-Language, browser language, DefaultHostUriMatchMapItems, DefaultHostUriRedirect, start page, language selection, no cookie, 302
type: help
---
# Redirecting to the visitor's language

This page is for administrators and site builders whose site has its languages under their own path (`/fr`, `/en`)
and who want `www.example.gov/` to lead each visitor to a language. Exponential does this with
`site.ini [SiteAccessSettings] DefaultHostUriMatchMapItems[]`
([Siteaccess by host prefix, and the redirect to the language](../../../features/6.0/siteaccess-host-and-language-matching.md)).

## Contents

1. [How it decides](#how-it-decides)
2. [Setting it up](#setting-it-up)
3. [Showing instead of redirecting](#showing-instead-of-redirecting)
4. [Respecting the visitor's choice](#respecting-the-visitors-choice)
5. [Checking](#checking)

## How it decides

When no rule of `MatchOrder` matches an address (for example `/` or `/contact` without a language segment), the
entries of `DefaultHostUriMatchMapItems[]` are tried. The first entry for the host whose language the browser
accepts wins, trying the browser's languages from the most wanted; if none matches, the first entry for the host
without a language applies. The redirect is a 302, once, keeping the rest of the address and the query: `/` goes to
`/fr`, `/news/an-article` to `/fr/news/an-article`. No cookie is set, and every page behind the redirect has one
address per language, which caches handle well.

## Setting it up

```ini
# settings/override/site.ini.append.php
[SiteAccessSettings]
DefaultHostUriMatchMapItems[]=www.example.gov;fr;site_fr;strict;fr
DefaultHostUriMatchMapItems[]=www.example.gov;en;site_en
```

The format is `host;uri;siteaccess[;method[;language]]`. Here a browser that accepts French goes to `/fr`; every other
browser goes to `/en`, the entry without a language. `fr` also takes `fr-CA`. An entry whose siteaccess is not in
`AvailableSiteAccessList` is ignored and logged.

For two official languages with equal standing, decide which one is the default for browsers that accept neither,
and write that decision down.

## Showing instead of redirecting

`DefaultHostUriRedirect=disabled` shows the page at the address itself instead of redirecting. Some bodies prefer a
neutral start page that offers all official languages side by side; that is a page of its own in a siteaccess for
`/`, not this setting.

## Respecting the visitor's choice

A browser's language is a guess. Always offer the language switcher on every page, so a visitor whose browser is set
to another language reaches the one they want in one step (see "The language switcher"). Because the redirect only
applies to addresses without a language segment, links to `/fr/...` or `/en/...` are never redirected.

## Checking

```bash
curl -sI -H 'Accept-Language: fr-CA,fr;q=0.9' https://www.example.gov/ | grep -i '^location'
curl -sI -H 'Accept-Language: de' https://www.example.gov/ | grep -i '^location'
```

The first should point to `/fr`, the second to `/en`.

## Related pages

- [Language addresses: path or host](language-addresses-path-or-host.md)
- [Language switchers and addresses per language](../../creators/website-creators/language-switcher-and-language-urls.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
