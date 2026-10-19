---
title: "Lesson 2.4: The redirect to the visitor's language"
audience: trainer, admin, developer
category: training
order: 24
summary: Learners send visitors of an address without a language segment to the right language with DefaultHostUriMatchMapItems, check the 302 answer and its cache headers with curl, and switch the redirect off. 50 minutes.
keywords: multisite and site packages, DefaultHostUriMatchMapItems, DefaultHostUriRedirect, Accept-Language, 302, language redirect, Vary, Cache-Control private, DefaultAccess, curl
type: help
---
# Lesson 2.4: The redirect to the visitor's language

When a site has a siteaccess per language, the bare address `example.com/` belongs to none of them. Exponential can
choose the siteaccess and the language segment for that address by host and by the languages the browser accepts,
and send the browser on to `example.com/de`. This lesson sets the redirect up for the lab host of lesson 2.3, reads
the answer with `curl`, and explains what it means for caches.

## At a glance

| | |
|---|---|
| Course | [Multisite and site packages](../README.md) |
| Module | [Module 2: Matching requests to siteaccesses](README.md) |
| Duration | 50 minutes (20 presenting, 30 exercise) |
| Level | Advanced |
| Audience | Administrators and developers |
| Prerequisites | [Lesson 2.3](host-and-path-and-port-matching.md): `club.localhost/de` reaches `site_b_ger` |

## Learning objectives

After this lesson, learners can:

- write `DefaultHostUriMatchMapItems[]` entries (`host;uri;siteaccess[;method[;language]]`);
- predict which entry applies for a given `Accept-Language` header;
- check the redirect with `curl` and read its `Location`, `Vary` and `Cache-Control` headers;
- switch the redirect off with `DefaultHostUriRedirect=disabled` and say what happens instead.

## Trainer's notes

### How to present it

Source: [Siteaccess by host prefix, and the redirect to the language](../../../../features/6.0/siteaccess-host-and-language-matching.md#the-redirect-to-the-language).

1. **When it applies** (4 minutes). Only where no probe of `MatchOrder` matched, so where `DefaultAccess` would
   answer: typically the address without siteaccess or language segment. An address a probe matches (`/de/...`,
   `/admin/...`) is never sent on.
2. **The entry** (5 minutes). `DefaultHostUriMatchMapItems[]=host;uri;siteaccess[;method[;language]]`. The first
   entry for the host whose language the browser accepts wins, the languages of `Accept-Language` tried from the
   most wanted; when the browser wants none of them, the first entry for the host without a language applies; when
   there is none, `DefaultAccess` answers. `de` also takes `de-CH`. An entry whose siteaccess is not in
   `AvailableSiteAccessList[]` is ignored and logged.
3. **The answer** (4 minutes). A GET or HEAD request gets a 302, once, to the same address with the segment, the
   query kept: `example.com/news/an-article?page=2` goes to `example.com/de/news/an-article?page=2`. The `Location` is
   always a path on the host that was asked for, never a whole URL. A POST is answered at the address itself.
4. **Caches** (4 minutes). When the answer depends on the browser, it is sent with `Vary: Accept-Language` and
   `Cache-Control: private`, so neither a proxy nor Velocity's response cache keeps it. Every page behind the redirect
   has one address per language and is cached as usual. Pages the static cache or the HTTP cache kept before an
   entry was added are still served without the redirect: clear those caches after changing the setting.
5. **Without the redirect** (3 minutes). `DefaultHostUriRedirect=disabled`, or no probe that would take the target
   address: the page is shown at the address itself in the chosen siteaccess, and its links carry the segment.

### Common questions

**Is there a cookie that remembers the choice?** No. A visitor who switches language follows links that carry the
other segment; those addresses are matched by a probe and never sent on again.

**Does the REST interface redirect too?** No. Only the web page request asks for the language default; the REST
interface, scripts and extensions get the probes and then `DefaultAccess`, as before.

**Does it work under Velocity?** Yes; the redirect is returned like a module redirect and works the same behind
Apache and Velocity, on any port.

**What if nothing is set?** Nothing changes: `settings/site.ini` ships no entry.

### Pitfalls

- **Testing in a browser only.** Browsers remember redirects and send their own languages. Test with `curl` and an
  explicit `Accept-Language` header.
- **An entry for a host a probe already matches.** If an entry with an empty path in `HostUriMatchMapItems[]` takes
  `/` for the host (as `club.localhost;;site_b` in lesson 2.3), the default never applies there. The exercise uses a
  second host for that reason.
- **A segment no probe takes.** The redirect needs a probe that reaches the target; otherwise the page is shown in
  place. Check the target address on its own first.

## Hands-on exercise

Use your training installation from lesson 2.3. Replace `http://127.0.0.1:8087` with your lab's address.

1. Give the German and the English site a path on a new host, `lang.localhost`, with no entry for `/` itself:

   ```bash
   php bin/php/console exp:ini add 'site.ini/SiteAccessSettings/HostUriMatchMapItems[]' 'lang.localhost;de;site_b_ger' global
   php bin/php/console exp:ini add 'site.ini/SiteAccessSettings/HostUriMatchMapItems[]' 'lang.localhost;en;site_b' global
   ```

2. Add the defaults for that host, German for German browsers and English for everybody else:

   ```bash
   php bin/php/console exp:ini add 'site.ini/SiteAccessSettings/DefaultHostUriMatchMapItems[]' 'lang.localhost;de;site_b_ger;;de' global
   php bin/php/console exp:ini add 'site.ini/SiteAccessSettings/DefaultHostUriMatchMapItems[]' 'lang.localhost;en;site_b' global
   php bin/php/ezcache.php --clear-tag=ini
   ```

3. Check the target addresses first:
   `curl -s -H 'Host: lang.localhost' http://127.0.0.1:8087/de | grep -i '<title>'` (and the same for `/en`).
4. Ask as a German browser:

   ```bash
   curl -sI -H 'Host: lang.localhost' -H 'Accept-Language: de-DE,de;q=0.9,en;q=0.8' http://127.0.0.1:8087/ \
     | grep -iE '^(HTTP|location|vary|cache-control)'
   ```

5. Ask as an English and as a French browser (`Accept-Language: en` and `fr`). Note the `Location` each time.
6. Ask for a deeper address with a query: `.../News?x=1` with the German header. Note the `Location`.
7. Ask for `/de` itself with the German header and confirm that no redirect comes.
8. Switch the redirect off:
   `php bin/php/console exp:ini set site.ini/SiteAccessSettings/DefaultHostUriRedirect disabled global`, clear the INI
   cache, and repeat step 4. Note the status and the page title.
9. Switch it on again (`enabled`) and clear the INI cache.

## Expected result

- Step 4: `302` with a `Location` path ending in `/de` on the same host, plus `Vary: Accept-Language` and
  `Cache-Control: private`, because the answer depends on the browser.
- Step 5: the English and the French browser are both sent to `/en` (the entry without a language).
- Step 6: the `Location` keeps the path and the query after the segment (`/de/News?x=1`).
- Step 7: `200` and the German page, no redirect.
- Step 8: `200` at `/` itself, showing the German site.
- The trainer checks step 4 and step 8 on the projector with one learner's lab.

## Quiz

1. In which situation does `DefaultHostUriMatchMapItems[]` apply at all?
2. Entries: `example.com;de;site_de;;de` then `example.com;en;site_en`. A browser sends `Accept-Language: fr, en;q=0.5`.
   Where is it sent? A. `/de` B. `/en` C. nowhere D. `/fr`
3. Why is the redirect sent with `Cache-Control: private`?
4. True or false: the `Location` of the redirect can point to another host.
5. What happens with `DefaultHostUriRedirect=disabled`?

<details>
<summary>Answers</summary>

1. When no probe of `MatchOrder` matched the address, where `DefaultAccess` would otherwise answer.
2. **B.** No entry for French, and the `de` entry is not accepted, so the first entry for the host without a
   language applies.
3. Because the answer depends on the browser's language; proxies and Velocity's response cache must not keep it for
   other visitors.
4. **False.** It is always a path on the host that was asked for, never a whole URL.
5. The page is shown at the address itself in the chosen siteaccess, and its links carry the segment.

</details>

## Further reading

- [Siteaccess by host prefix, and the redirect to the language](../../../../features/6.0/siteaccess-host-and-language-matching.md):
  the full rules, the caches and how it works.
- [Velocity response cache](../../../../features/6.0/velocity-response-cache.md).
- [Content languages](../../../../guides/content-languages.md).
- [Language switchers and addresses per language](../../../creators/website-creators/language-switcher-and-language-urls.md).

## Related pages

- [Module 2: Matching requests to siteaccesses](README.md)
- [Multisite and site packages: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
