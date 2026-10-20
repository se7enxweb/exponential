---
title: "Lesson 2.1: Matching by URL path"
audience: trainer, admin, developer
category: training
order: 21
summary: Learners explain how MatchOrder and the URI probe choose a siteaccess from the first part of the path, what DefaultAccess does when nothing matches, and how PathPrefix shortens the addresses of a site. 50 minutes.
keywords: multisite and site packages, MatchOrder, uri matching, URIMatchType, element, map, URIMatchMapItems, DefaultAccess, RemoveSiteAccessIfDefaultAccess, PathPrefix, PathPrefixExclude
type: help
---
# Lesson 2.1: Matching by URL path

Every request is matched to one siteaccess before anything else happens. The simplest rule, and the one a default
installation uses, takes the first part of the path: `/site_b/...` belongs to `site_b`. This lesson explains that
rule, the fallback when nothing matches, and `PathPrefix`, which lets a site hide its own folder from its addresses.

## At a glance

| | |
|---|---|
| Course | [Multisite and site packages](../README.md) |
| Module | [Module 2: Matching requests to siteaccesses](README.md) |
| Duration | 50 minutes (20 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Administrators and developers |
| Prerequisites | [Module 1](../module-01-siteaccesses-for-several-sites/README.md): the siteaccesses `site_b` and `site_b_ger` work |

## Learning objectives

After this lesson, learners can:

- explain `MatchOrder` as a list of probes tried in order, the first match winning;
- describe the URI match types `element`, `map`, `text` and `regexp`;
- predict which siteaccess answers an address, including one that no probe matches (`DefaultAccess`);
- set `PathPrefix` so that the pages of a site are addressed without the site's folder name.

## Trainer's notes

### How to present it

Sources: the comments of `settings/site.ini [SiteAccessSettings]` and
[10.2 Siteaccesses and site addresses](../../../../install/10-after-installing.md#102-siteaccesses-and-site-addresses).

1. **Probes in order** (5 minutes). `MatchOrder` lists the probes: `uri`, `host`, `host_uri`, `port` and
   `servervar`. The shipped value is `uri;host;port`; the installer writes its own. The first probe that finds an
   available siteaccess wins; a probe that finds a name that is not in `AvailableSiteAccessList[]` does not count, and
   the next probe is tried.
2. **The URI probe** (6 minutes). `URIMatchType` chooses how:

   | Value | Meaning |
   |---|---|
   | `element` (shipped) | the path element `URIMatchElement` (1, the first) is the siteaccess name |
   | `map` | `URIMatchMapItems[]=uri;siteaccess` entries map a path element to a siteaccess |
   | `text` | a pre or post sub-string of the path |
   | `regexp` | a regular expression and the part it extracts |
   | `disabled` | no URI matching |

   With `element`, the matched element is removed from the path the kernel sees, so `/site_b/news` is the page
   `news` of `site_b`.
3. **When nothing matches** (3 minutes). `[SiteSettings] DefaultAccess` answers. `RemoveSiteAccessIfDefaultAccess=enabled`
   leaves the name of the default siteaccess out of the links it builds; the comment in `settings/site.ini` says that
   changing it requires clearing the cache. Lesson 2.4 shows a better tool for the start page: the redirect to the
   language.
4. **`PathPrefix`** (6 minutes). A site whose root is a folder `Site B` would otherwise have addresses such as
   `/site_b/Site-B/an-article`. `PathPrefix=Site-B` in that siteaccess hides the prefix: `/site_b/an-article`.
   `PathPrefixExclude[]` lists paths (for example `media`, `users`) that keep their full address. The comment in
   `settings/site.ini` adds that a `PathPrefix` also needs the `PathPrefix` setting in `logfile.ini`.

### Common questions

**Is the name in the address the siteaccess name?** With `element`, yes. With `map`, the address part and the
siteaccess name can differ, for example `URIMatchMapItems[]=de;site_b_ger`.

**Can the main site have no prefix at all?** Yes: make it the `DefaultAccess`, so `/` and every address that no
probe matches go to it, and consider the redirect of lesson 2.4.

**Is URL matching good for production?** It works behind any web server with one host name. Host matching (lesson
2.2) is the usual choice for production with several brands.

**Why does `/admin` still work after I changed `URIMatchType`?** The administration is often reached by another
probe or another entry; read `MatchOrder` and the map with `exp:ini where`.

### Pitfalls

- **Global changes on a shared lab.** `MatchOrder` and `URIMatchType` live in the global override. On a shared
  training server one learner's change breaks everybody's addresses. This lesson's changes are per siteaccess.
- **The wrong alias for `PathPrefix`.** The value is the URL alias of the folder as the browser shows it (case and
  dashes included), not its name.
- **Stale pages.** After a `PathPrefix` change, clear the INI and content caches, or old links stay in cached pages.

## Hands-on exercise

Use your training installation from module 1 (URL matching).

1. Read the probes and the URI rule in effect:

   ```bash
   php bin/php/console exp:ini get site.ini/SiteAccessSettings/MatchOrder
   php bin/php/console exp:ini get site.ini/SiteAccessSettings/URIMatchType
   php bin/php/console exp:ini get site.ini/SiteSettings/DefaultAccess
   ```

2. Predict, then check with `curl -sI` and in the browser, which siteaccess answers each address (use your lab's
   address): `/site_b`, `/site_b_ger`, `/admin`, `/no_such_site`, `/`.
3. For `/no_such_site`, explain what answered: the probe found a name that is not available, so the default
   siteaccess took the request and treated `no_such_site` as a page address.
4. In the administration, below the folder *Site B*, create and publish a **Folder** named `News`.
5. Open `/site_b` in the browser and follow the link to *News*, or open the folder *Site B* on the main site. Note
   the URL alias of *Site B* in the address (for example `Site-B`).
6. Set the prefix for `site_b` and clear the caches:

   ```bash
   php bin/php/console exp:ini set site.ini/SiteAccessSettings/PathPrefix Site-B siteaccess:site_b
   php bin/php/ezcache.php --clear-tag=ini
   php bin/php/ezcache.php --clear-id=content
   ```

7. Open `/site_b/News`. Then open `/site_b/Site-B/News` and compare.
8. Look at the link to *News* on the start page of `/site_b`: does it still carry `Site-B`?
9. Undo the change if your trainer asks you to:
   `php bin/php/console exp:ini rem site.ini/SiteAccessSettings/PathPrefix siteaccess:site_b`, then clear the caches
   again.

## Expected result

- Step 2: `/site_b` and `/site_b_ger` answer with their own site names, `/admin` shows the sign-in page,
  `/no_such_site` answers from the default siteaccess (usually a "not found" page there), and `/` shows the default
  siteaccess's start page.
- Step 7: `/site_b/News` shows the folder *News*; the links on `site_b`'s pages no longer contain `Site-B`.
- The trainer checks by asking each learner to state, before pressing Enter, which siteaccess will answer
  `/site_b_ger/News`, and then confirming it in the browser.

## Quiz

1. What does `MatchOrder=uri;host;port` mean?
2. With `URIMatchType=element`, which siteaccess answers `/site_b/news`?
   A. `site` B. `site_b` C. `news` D. the `DefaultAccess`
3. A probe finds the name `shop`, which is not in `AvailableSiteAccessList[]`. What happens next?
4. What does `PathPrefix` change?
5. Which setting lists paths that keep their full address despite a `PathPrefix`?

<details>
<summary>Answers</summary>

1. The kernel tries the URI probe first, then the host probe, then the port probe; the first that finds an available
   siteaccess wins (`settings/site.ini`).
2. **B.** The first path element names the siteaccess, and the kernel then serves `news` in it.
3. The probe does not count; the next probe is tried, and when none matches, `DefaultAccess` answers.
4. It hides that part from the start of URL aliases for the siteaccess, so the site's own folder does not appear in
   its addresses (comment in `settings/site.ini`).
5. `PathPrefixExclude[]`.

</details>

## Further reading

- [10.2 Siteaccesses and site addresses](../../../../install/10-after-installing.md#102-siteaccesses-and-site-addresses).
- [URL aliases and nice URLs](../../../creators/website-creators/url-aliases-and-nice-urls.md): how aliases are made.
- [URLs and aliases](../../../../guides/urls-and-aliases.md): the administrator's reference.
- [Going live: hosts, HTTPS and siteaccesses](../../../creators/website-creators/going-live-hosts-https.md).

## Related pages

- [Module 2: Matching requests to siteaccesses](README.md)
- [Multisite and site packages: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
