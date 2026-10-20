---
title: Redirect the start page to the visitor's language
audience: government, admin
category: public-sector
order: 60
summary: Task for administrators: make www.example.gov/ send each visitor once to the language their browser asks for, with DefaultHostUriMatchMapItems, and test it with curl.
keywords: language redirect, start page, Accept-Language, DefaultHostUriMatchMapItems, DefaultHostUriRedirect, browser language
type: help
---
# Redirect the start page to the visitor's language

This task is for administrators of a site whose languages live under paths (`/en`, `/fr`). Background:
[Redirecting to the visitor's language](../../languages/redirecting-to-the-visitors-language.md).

## Before you start

- One siteaccess per language, reached by a path segment (see "Give a language its own address").
- A decision which language browsers that ask for none of yours get.

## Steps

1. In `settings/override/site.ini.append.php`:

   ```ini
   [SiteAccessSettings]
   DefaultHostUriMatchMapItems[]=www.example.gov;fr;fr;strict;fr
   DefaultHostUriMatchMapItems[]=www.example.gov;en;en
   ```

   Format: `host;uri;siteaccess[;method[;language]]`. The entry without a language is the default.
2. Clear the INI cache: `php bin/php/ezcache.php --clear-tag=ini`.

## Check

```bash
curl -sI -H 'Accept-Language: fr-CA,fr;q=0.9' https://www.example.gov/ | grep -i '^location'
curl -sI -H 'Accept-Language: de' https://www.example.gov/ | grep -i '^location'
```

The first points to `/fr`, the second to `/en`. Addresses with a language segment are never redirected.

## Problems

| Problem | Fix |
|---|---|
| No redirect | an entry's siteaccess is not in `AvailableSiteAccessList` (ignored and logged), or another `MatchOrder` probe matched `/` |
| You want a neutral language choice page instead | `DefaultHostUriRedirect=disabled` and a page of your own |

## Related pages

- [Siteaccess by host prefix, and the redirect to the language](../../../../features/6.0/siteaccess-host-and-language-matching.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
