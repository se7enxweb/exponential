# Siteaccess by host prefix, and the redirect to the language

Read this page if your site answers under several host names (live, test, local) or has its languages under their
own path (`/ger`, `/eng`), and the start page `example.com/` should lead visitors to the right language. Two
settings of `site.ini [SiteAccessSettings]` do it without a patch of `eZSiteAccess::match()`.

## In short

| | |
|---|---|
| `HostMatchMethod` | How the host map (`HostMatchType=map`) compares a host: `strict` (default, as before), `start`, `end` or `part`. With `start`, `www.example.com.test.local` reaches the siteaccess of `www.example.com`. |
| `HostMatchMapItems[]=host;siteaccess;method` | The same per entry. |
| `DefaultHostUriMatchMapItems[]` | Where the default siteaccess would apply (no probe of `MatchOrder` matched), the siteaccess and language segment chosen by host and by the browser's language. |
| The redirect | `/` goes to `/ger`, `/news/an-article` to `/ger/news/an-article` (302, once, the address and its query kept). |
| `DefaultHostUriRedirect` | `enabled` (default) or `disabled` (the page is shown at the address itself). |
| Caches | Only the redirect depends on the browser; every page behind it has one address per language. No cookie. |

## The host map by the start of a host

Before, `MatchOrder=host` with `HostMatchType=map` only took a host that was listed exactly. A test host needed
entries of its own. Now the comparison can be chosen, with the methods that `HostUriMatchMapItems` already knows:

| Method | Matches when the host ... | Example for `www.example.com` |
|---|---|---|
| `strict` | is the listed host (default) | `www.example.com` |
| `start` | begins with it | `www.example.com.test.local` |
| `end` | ends with it | `preview.www.example.com` |
| `part` | contains it | `a.www.example.com.b` |

```ini
[SiteAccessSettings]
MatchOrder=host
HostMatchType=map
# For every entry
HostMatchMethod=start
HostMatchMapItems[]=www.example.com;site
# Or for one entry, the others stay strict
HostMatchMapItems[]=admin.example.com;admin;start
```

`start` and `part` also take hosts you did not mean (`www.example.com.anything`). Use them where the web server
only answers for your own hosts. The static cache only knows `strict`, as for `host_uri`.

## The redirect to the language

### The rule

When no probe of `MatchOrder` matches an address, the default siteaccess (`DefaultAccess`) answers. That is the
address without siteaccess or language segment: `example.com/`, `example.com/contact`. With
`DefaultHostUriMatchMapItems[]` you name the siteaccess and the segment for this case instead:

```ini
DefaultHostUriMatchMapItems[]=host;uri;siteaccess[;method[;language]]
```

| Field | Meaning |
|---|---|
| `host` | The host, compared by `method`. Empty with `part` matches any host. |
| `uri` | The segment the siteaccess is reached under, for example `ger`. |
| `siteaccess` | The siteaccess. |
| `method` | `strict`, `start`, `end` or `part`; empty or `default` means `HostUriMatchMethodDefault`. |
| `language` | Optional. The entry only applies when the browser accepts this language; `de` also takes `de-CH`. |

The first entry for the host whose language the browser accepts wins: the languages of `Accept-Language` are tried
from the most wanted (by `q`; `q=0` does not count). When the browser wants none of them, the first entry for the
host without a language applies.

The browser is then sent on (302) to the same address with the segment, once:

| Asked for | Sent to |
|---|---|
| `example.com/` | `example.com/ger` |
| `example.com/contact` | `example.com/ger/contact` |
| `example.com/news/an-article?page=2` | `example.com/ger/news/an-article?page=2` |
| `example.com/ger/...`, `example.com/admin/...` | not sent on: a probe matched |

The address stays as it was asked for: a URL alias stays one and is not turned into `content/view/full/...`. Only
GET and HEAD requests are sent on; a POST to an address without segment is answered there.

### One language under `/ger`, siteaccesses matched by the URL

The usual setup of an installation: `MatchOrder=uri`, the siteaccesses `site`, `ger`, `admin` under their names.
`example.com/` is to show the German site:

```ini
[SiteAccessSettings]
MatchOrder=uri;host
DefaultHostUriMatchMapItems[]=;ger;ger;part
```

The empty host with `part` makes it apply on every host (local, test, live). `/admin` and `/ger/...` are matched by
the URL and stay where they are.

### Several languages, chosen by the browser

German and English on one host, matched by host and segment:

```ini
[SiteAccessSettings]
MatchOrder=host_uri
HostUriMatchMapItems[]=www.example.com;de;site_de
HostUriMatchMapItems[]=www.example.com;en;site_en
DefaultHostUriMatchMapItems[]=www.example.com;de;site_de;;de
DefaultHostUriMatchMapItems[]=www.example.com;en;site_en
```

A browser that wants German (`de-DE,de;q=0.9,en;q=0.8`) goes from `/` to `/de`; any other goes to `/en`. Who
switches to the other language follows its links, which all carry its segment, the start page of that language
(`/en`) too. Those addresses are matched by `HostUriMatchMapItems` and never sent on again, so the choice holds
without a cookie.

### When there is no redirect

The redirect needs a probe that takes the address with the segment for the chosen siteaccess; the kernel checks that
(`eZSiteAccess::reachesSiteAccess()`), else the redirect would come back to the default. When there is no such probe,
for requests other than GET and HEAD, or with

```ini
DefaultHostUriRedirect=disabled
```

the page is shown at the address itself in the chosen siteaccess, and its links carry the segment.

## Caches

Every page behind the redirect has one address per language and is cached as any other. When the host has more
than one language variant, the redirect itself depends on the browser: it is sent with `Vary: Accept-Language` and
`Cache-Control: private`, so neither a proxy nor Velocity's response cache (which keys by address) keeps it, and the
role-aware HTTP cache does not store it. The same holds for a page shown at the address itself. With one language
variant the redirect is the same for everyone and is sent like any other.

## Check it

```bash
curl -sI -H 'Accept-Language: de' https://example.com/ | grep -iE '^(HTTP|location|vary|cache-control)'
curl -sI https://example.com/contact | grep -i '^location'
curl -sI https://example.com/admin | grep -i '^HTTP'
```

The first two answer `302` with the language segment in `Location`, the third as before.

## How it works

- `eZSiteAccess::match()` runs the probes of `MatchOrder`. When none matched, it asks
  `eZSiteAccess::matchDefaultHostUri()` (with `acceptedLanguages()` for the header) and marks the siteaccess for
  the redirect when `reachesSiteAccess()` finds a probe for the target address. Both probes with host methods use
  `eZSiteAccess::hostMatches()`.
- `ezpKernelWeb::run()` sends the redirect to `ezpKernelWeb::languageRedirectURI()` through `eZHTTPTool::redirect()`,
  as a module redirect is sent, so it works under Velocity as well.
- Tests: `eZSiteAccessMatchTest` (no database).

## Related pages

- [Translations and languages](translations-and-languages.md)
- [Content languages](../../guides/content-languages.md)
- [Velocity response cache](velocity-response-cache.md)
