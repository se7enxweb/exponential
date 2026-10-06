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
| Without the settings | Nothing changes: `site.ini` ships `HostMatchMethod=strict` and no `DefaultHostUriMatchMapItems[]` entry. |

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

Every method, in the host map, in `HostUriMatchMapItems` and in `DefaultHostUriMatchMapItems`, compares host names
as DNS does:

- without regard to case and to the dot of a fully qualified name (`WWW.Example.com.` is `www.example.com`);
- without the port of the request (`www.example.com:8080`, as on a second web server), unless the entry names a
  port itself;
- a name written in Unicode in the settings (`münchen.example`) as the ASCII form a browser sends
  (`xn--mnchen-3ya.example`), when PHP has the intl extension;
- `end` means the end: `example.com` takes `www.example.com` and `example.com.example.com`, not
  `example.com.evil`. An empty host matches every host with `start` and `part`, none with `strict` and `end`.

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
| `language` | Optional. The entry only applies when the browser accepts this language; `de` also takes `de-CH`, and `de_DE` is read as `de-DE`. |

The first entry for the host whose language the browser accepts wins: the languages of `Accept-Language` are tried
from the most wanted (by `q`; `q=0` does not count, a `q` above 1 counts as 1, the first 32 languages of the header
are read and anything that is not a language tag is skipped). When the browser wants none of them, the first entry
for the host without a language applies; when there is none, `DefaultAccess` answers as before. An entry whose
siteaccess is not in `AvailableSiteAccessList` is ignored and logged.

The browser is then sent on (302) to the same address with the segment, once:

| Asked for | Sent to |
|---|---|
| `example.com/` | `example.com/ger` |
| `example.com/contact` | `example.com/ger/contact` |
| `example.com/news/an-article?page=2` | `example.com/ger/news/an-article?page=2` |
| `example.com/ger/...`, `example.com/admin/...` | not sent on: a probe matched |

The address stays as it was asked for: a URL alias stays one and is not turned into `content/view/full/...`. Only
GET and HEAD requests are sent on; a POST to an address without segment is answered there.

The `Location` is always a path on the host that was asked for, never a whole URL, so a forged `Host` header is
never written into it and the redirect works the same behind Apache and Velocity, on any port. The path, which the
kernel keeps decoded, is encoded again: `%0D%0A` cannot split the header, `%2F%2F` or a backslash cannot turn the
target into an address on another host, empty and dot segments (`//`, `..`) are dropped, and the query keeps its
encoding (bytes outside printable ASCII are encoded). The path starts with the index file and the segment of the
entry, not with the address the kernel would build for links: with `RemoveSiteAccessIfDefaultAccess=enabled` that
would leave out the segment of the default siteaccess and send the browser back to where it came from. A target that
equals the address asked for is never sent.

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

Every page behind the redirect has one address per language and is cached as any other. The answer at the address
without segment depends on the browser when an entry for the host names a language and the entries lead to more
than one siteaccess or segment, or when no entry applies without a language (then a German browser is sent to
`/ger` and any other gets `DefaultAccess` at the same address). Such an answer, the redirect, a page shown at the
address itself and the page of `DefaultAccess` for the other browsers alike, is sent with `Vary: Accept-Language`
(added to a `Vary` that `[HTTPHeaderSettings]` already sends) and `Cache-Control: private`, so neither a proxy nor
Velocity's response cache (which keys by address and keeps only `200` answers) keeps it, and the role-aware HTTP
cache does not store it. Its early exit, which finds the siteaccess before the kernel runs, does not serve a page
for an address where an entry for the host applies. When every entry leads to the same siteaccess and segment, the
redirect is the same for everyone and is sent like any other.

Pages the static cache or the HTTP cache kept before you added an entry are still served at the old address
without the redirect: clear those caches after changing the setting.

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
- `ezpKernelWeb::run()` sends the redirect to `ezpKernelWeb::languageRedirectLocation()` (built by
  `languageRedirectURI()`) and returns it as an `ezpKernelRedirect`, as a module redirect is returned, so it works
  under Velocity as well.
- `ezpHttpCacheContract::resolveSiteAccess()`, which the HTTP cache's early exit uses, knows `HostMatchMethod`, the
  method of a host map entry and the hosts of `DefaultHostUriMatchMapItems`.
- While `reachesSiteAccess()` asks where the target would lead, `RedirectOnNormalize` does not send anything.
- Tests: `eZSiteAccessMatchTest` and `ezpHttpCacheContractTest` (no database).

## Related pages

- [Translations and languages](translations-and-languages.md)
- [Content languages](../../guides/content-languages.md)
- [Velocity response cache](velocity-response-cache.md)
