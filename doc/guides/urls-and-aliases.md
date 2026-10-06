# Links, URL aliases, wildcards and search statistics

This guide teaches the four pages of the administration interface that look after addresses: the **link list**
(which external and internal links your content uses and whether they work), the **global URL aliases** (addresses
of your choosing for module views), the **URL wildcards** (one rule for a whole group of addresses) and the
**search statistics** (what visitors search for and whether they find it).

It is written for administrators and editors who keep a site's links and addresses in order. Every page, field and
rule below was checked against the code of this repository on 6 October 2026; the files are listed under
[References](#references).

[Guides](README.md) · Related: [Paging in the admin](../features/6.0/admin-list-paging.md) ·
[Cronjobs](cronjobs.md) · [Operating a site](operating-a-site.md)

## In short

- **Setup > Link management** (`/url/list`) lists every address that published content links to, once, with its
  state (valid, invalid, never checked), when it was checked and changed, and which objects use it. Search by any
  part of the address, show one state, order by address, last check or last change, and mark ticked links valid or
  invalid by hand.
- The **link check** (`linkcheck.php`, part `infrequent`) requests http and https links alike, judges links to
  content by their node or object, never requests addresses on a private network and waits between requests to
  one host. Its settings are in `cronjob.ini [linkCheckSettings]`.
- **Setup > URL translator** (`/content/urltranslator`) makes and removes **global URL aliases**: `login` for
  `user/login`, for example. Each alias shows its whole path, its destination and what that resolves to now; an
  alias to a module that no longer exists is marked.
- **Setup > URL wildcards** (`/content/urlwildcards`) makes and removes **wildcards**: `news/*` to `articles/{1}`.
  The page refuses a destination that uses a `{n}` the pattern has no `*` for, and **Try an address** shows which
  wildcard an address matches and where it leads, without changing anything.
- **Setup > Search statistics** (`/search/stats`) shows every recorded search phrase, how often it was searched for
  and the average number of results. It says when no siteaccess records searches (the default), filters the
  phrases that found nothing, orders by count, phrase or fewest results, and runs a phrase again with one click.
- Every list is paged, every search and filter is kept in the address (so it survives paging and can be
  bookmarked), and every removal of more than the ticked rows asks in place first.

## Contents

- [1. The link list](#1-the-link-list)
- [2. How links are checked](#2-how-links-are-checked)
- [3. Global URL aliases](#3-global-url-aliases)
- [4. URL wildcards](#4-url-wildcards)
- [5. Search statistics](#5-search-statistics)
- [6. Addresses and parameters of the pages](#6-addresses-and-parameters-of-the-pages)
- [7. When something does not behave as expected](#7-when-something-does-not-behave-as-expected)
- [For developers](#for-developers)
- [References](#references)

## 1. The link list

Open **Setup > Link management**. The page starts with five figures:

| Figure | What it counts | Click to |
|---|---|---|
| Links in published content | addresses that at least one published version links to | show all |
| Valid | of those, marked valid | show the valid ones |
| Invalid | marked invalid (red when there are any) | show the invalid ones |
| Never checked | never tried by the link check | show them |
| Last link check | the time of the most recent check of any link | – |

Below them, **Find a link** searches any part of the address (`example.org`, `/about`, `mailto:`), without regard to
upper and lower case; `%` and `_` are matched as themselves. **Show** picks the list (All, Valid, Invalid, Never
checked) and **Order** sorts it: Address A to Z (the default), Last checked (most recent first) or Last modified.

Each link is a card with:

- the address, which opens the link's own page (`/url/view/<id>`) with every object version that uses it;
- badges: **Valid** or **Invalid**, **Never checked**, and how the link check treats the address (see section 2);
- **View**, **Edit** (change the address in one place for every object that uses it) and **Open**, which opens the
  address in a new window. Open is offered only for web, mail and site addresses, never for `javascript:` or
  `data:`, so a bad link in content cannot run in the admin;
- **Checked** and **Modified** times;
- **Used by**: up to three published objects that link to it, each linked to its main node, and "and N more",
  which leads to the link's page.

To mark links by hand, tick them (or **Select all on this page**) and press **Mark selected valid** or **Mark
selected invalid**. The page says how many were marked. Marking changes only the state; nothing is removed and no
content changes. The next link check tests the links again and may change the state back.

The page size is chosen under the list (10, 25 or 50, remembered per user). The sizes on offer are the setting
`ItemsPerPageList_url_list[]` in `admininterface.ini [PaginationSettings]`, like every admin list
([paging](../features/6.0/admin-list-paging.md)).

## 2. How links are checked

The link check is the cronjob script `linkcheck.php` in the `infrequent` part (`cronjob.ini`). It runs from the
shell only, never in a web request (also not under Velocity). Run the whole part, or the link check alone:

```bash
php runcronjobs.php -s <your public siteaccess> infrequent
php runcronjobs.php -s <your public siteaccess> --script=linkcheck.php
```

Run it as the user the web server runs as, like the other cronjobs. It checks the links checked longest ago first
and prints one line per link with the result and why, then a summary such as:

```
All links have been checked! 189 valid, 72 invalid, 89 not decided, 0 skipped (checked recently or over MaxURLsPerRun), 48 changed, 725 requests, 566.7 s
```

A link changes state only when the check decides; **not decided** keeps the state it had. The time of the check is
recorded for every link it looked at. The badge on each card says how its kind is treated:

| Badge | Address | What the check does |
|---|---|---|
| (none) | `http:`, `https:`, `ftp:` | requests it: HEAD, then GET when HEAD fails, following redirects up to `MaxRedirects`, with TLS certificates verified. A 2xx answer is valid; 404, 410, other errors, a bad certificate, an unknown host or too many redirects are invalid; 429 and 503 (try later) and 401, 403 (the site refuses automated requests) are not decided |
| E-mail | `mailto:` | valid when the domain has a mail server (MX record) |
| On this site | a path such as `/about` or `/admin/content/dashboard` | without the query, the fragment and a leading siteaccess name: valid when it is the front page, a module view or a URL alias, else tried on the addresses of `SiteURL[]` |
| Link to content | `ezlocation://`, `eznode://`, `ezobject://` (links in rich text) | valid while the node or object exists, is published and is not hidden; invalid otherwise. No request is sent |
| Not tested | `file:` | names a file on a computer, not a page: never tested, the state is kept |
| Other address | any other scheme (`tel:`, `javascript:` ...) | looked up as a path of this site, as it always was, so usually invalid |

**Private addresses are never requested.** Before every request, also after a redirect, the host is resolved; when
an address is on a private, loopback, link-local or otherwise reserved network (10.0.0.0/8, 127.0.0.0/8,
169.254.0.0/16, 192.168.0.0/16, `::1`, `fe80::/10` ...), the link is not decided and nothing is sent. Otherwise a
link in content could make the server reach inside its own network. The request then goes to the address that was
checked, so the name cannot point elsewhere a moment later. An intranet that should be tested is listed in
`AllowedPrivateHosts[]`; the site's own `SiteURL[]` addresses are always allowed.

**One host is not hammered.** Requests to the same host wait `HostDelay` milliseconds, and a link checked less than
`RecheckInterval` seconds ago is skipped, so running the part twice in a row costs nothing.

The settings, in `cronjob.ini [linkCheckSettings]` (override them in `settings/override/cronjob.ini.append.php`):

| Key | Default | Meaning |
|---|---|---|
| `Timeout` | `15` | seconds one request may take |
| `ConnectTimeout` | `5` | seconds to connect |
| `MaxRedirects` | `5` | redirects followed before the link counts as broken |
| `HostDelay` | `1000` | milliseconds between two requests to the same host |
| `RecheckInterval` | `72000` | seconds before a checked link is checked again; `0` checks every link on every run |
| `MaxURLsPerRun` | `0` | links checked in one run (the ones checked longest ago first); `0` checks all |
| `UserAgent` | `Exponential Link Validator (+https://exponential.earth)` | the User-Agent of the requests |
| `AllowedPrivateHosts[]` | empty | hosts, addresses or IPv4 ranges (`10.1.0.0/16`) on a private network that may be tested |
| `SiteURL[]` | empty | the site's own addresses, tried for a path that is no alias |

On the 350 links of a demonstration site the first run took about nine and a half minutes and sent 725 requests:
most of the time is the per-host delay between links to the same few hosts.

## 3. Global URL aliases

A global URL alias gives a module view, or another alias, an address of your choosing. Open **Setup > URL
translator**. The aliases of content (the addresses of nodes) are not here: they are on the **URL aliases** tab of
each node.

### Create one

| Field | What to enter |
|---|---|
| New URL alias | the address visitors will use, without the host and without a leading slash: `login`, `campaign/autumn`. Characters that are not allowed are changed, and the page says to what |
| Destination | a module view (`user/login`, `content/search`) or an existing address of content (`about-us`). `content/view/full/<node ID>` makes an alias of that node instead, which is then listed on the node's URL aliases tab, not here |
| Language | the alias works in siteaccesses that show this language |
| Include in other languages | makes it work in every language |
| Alias should redirect to its destination | checked (the default): visitors get a 301 redirect to the destination's address. Unchecked: the destination is shown under the alias |

Press **Create**. When the alias cannot be created, the message says why, the field is marked, and what you typed
stays in the form:

| Message | Means |
|---|---|
| Text is missing for the URL alias | the alias field is empty |
| Text is missing for the URL alias destination | the destination is empty |
| The specified destination URL does not exist in the system | the destination is neither a module view nor an existing address |
| The URL alias already exists, and it points to ... | the address is taken; both links lead to what it is now |
| The specified language code is not valid | the language does not exist |

### Read the list

Each alias shows its whole path (each part links to what it is), its kind (**Redirect** or **Direct**), the
**Destination**, what it **Resolves to** (the view and module, or a node), its language and whether it is always
available. **Module not found** marks an alias whose module no longer exists, usually because an extension was
switched off: visitors get an error page there.

**Find an alias** searches the last segment of each alias (`login` in `campaign/login`) and its destination, **Show**
picks All, Redirecting or Direct. Both are conditions of the database query, so a page costs one count and one
page query however many aliases there are.

### Remove

Tick aliases and press **Remove selected**; the browser asks once more. **Remove all** opens a confirmation in
place that says how many aliases go; aliases of content nodes are kept. A removed alias stops working at once.

## 4. URL wildcards

A wildcard sends a group of addresses elsewhere with one rule. Each `*` in the pattern matches any text, and `{1}`,
`{2}` ... in the destination put that text back. Wildcards are only consulted when no URL alias matches, and they
are tried in the order they were created; the first that matches wins.

| Pattern | Destination | `news/2026/october` becomes |
|---|---|---|
| `news/*` | `articles/{1}` | `articles/2026/october` |
| `news/*/*` | `archive/{2}/{1}` | `archive/october/2026` |
| `old-blog/*` | `https://blog.example.org/{1}` (redirecting) | a redirect to the other site |

The pattern matches from the start of the address, without regard to case; text after the matched part is kept.

### Create one

Enter **New URL wildcard** (the pattern) and **Destination**, check **Redirecting URL** to answer with a 301
redirect (visitors see the destination address), or leave it unchecked to show the destination under the address
asked for. The page refuses:

- an empty pattern or destination;
- a pattern that exists already (the message names where the old one leads);
- a destination with a placeholder the pattern has no `*` for, such as `{2}` with one `*`. Without this check the
  wildcard would send visitors to an address with a part missing.

### Try an address

**Try an address** takes an address such as `news/2026/october` and says which wildcard it matches, what it becomes,
whether visitors are redirected, and what the result resolves to on this site (an alias, a module view, another
site, or nothing, which means an error page). It changes nothing; the address is kept in the page's address, so it
can be sent to a colleague.

### Find and remove

**Find a wildcard** searches pattern and destination, **Show** picks All, Redirecting or Direct (both in the
database query, in the order the wildcards are tried). Removing works as
for aliases: **Remove selected** asks once, **Remove all** asks in place. Every change empties the wildcard cache.

## 5. Search statistics

Open **Setup > Search statistics**.

**Searches are only recorded where switched on.** A search counts when the siteaccess the visitor searches in has

```ini
# settings/siteaccess/<public siteaccess>/site.ini.append.php
[SearchSettings]
LogSearchStats=enabled
```

It is `disabled` by default. The page says which siteaccesses record searches, or warns that none does; what is
listed was then recorded earlier.

The figures count the **different phrases**, all **searches**, the **phrases that found nothing** (click to list
them) and the share of all searches that found nothing.

**Find a phrase** searches the phrases; **Show** picks all phrases or only those that found nothing; **Order** sorts
by **Most searched** (the default), **Phrase A to Z** or **Fewest results** (average results, lowest first). The
bar beside each count compares it with the most searched phrase on the page. **Search now** runs the phrase in the
administration search, to see what it finds today.

A phrase searched for often that finds nothing points to content that is missing, or to words the content does not
use: add the content, or the words (a synonym in the text or in the keywords).

**Reset statistics** opens a confirmation in place that says how many phrases go, and removes all of them for good.

Phrases are what visitors typed. They can contain names or other personal data; treat the page accordingly and
reset it when the numbers are no longer needed.

## 6. Addresses and parameters of the pages

| Page | Address | Parameters |
|---|---|---|
| Link list | `/url/list/<all\|valid\|invalid\|unchecked>` | `(sort)/checked`, `(sort)/modified`, `(offset)/<n>`, `?q=<text>` |
| Global aliases | `/content/urltranslator` | `(kind)/redirect`, `(kind)/direct`, `(offset)/<n>`, `?q=<text>` |
| Wildcards | `/content/urlwildcards` | `(kind)/...`, `(offset)/<n>`, `?q=<text>`, `?test=<address>` |
| Search statistics | `/search/stats` | `(sort)/phrase`, `(sort)/fewest`, `(show)/none`, `(offset)/<n>`, `?q=<text>` |

Unknown values fall back to the defaults. The forms, their field names and their buttons (`SetValid`,
`SetInvalid`, `URLSelection[]`, `NewAliasButton`, `RemoveAliasButton`, `RemoveAllAliasesButton`, `ElementList[]`,
`NewWildcardButton`, `RemoveWildcardButton`, `RemoveAllWildcardsButton`, `WildcardIDList[]`,
`ResetSearchStatsButton`) are the ones the pages always had, and every POST form carries the form token.

## 7. When something does not behave as expected

| You see | Why, and what to do |
|---|---|
| Most links say **Never checked** | the link check has not run on them: run the `infrequent` cronjob part |
| A link that works in the browser stays as it was after a run | the site answered 401, 403, 429 or 503 to the link check (refused or busy), which decides nothing; the line of the run says which |
| A link to an intranet page is never tested | its host is on a private network; add it to `AllowedPrivateHosts[]` |
| A link to content is **Invalid** | its node or object was removed, is hidden or is not published; the line of the run names the node or object |
| A link to `/admin/...` or an extension's module is **Invalid** | the module is only active in that siteaccess: run the part with `-s` of that siteaccess, or ignore the state |
| A run takes long | most of it is `HostDelay` between links to the same host; lower it carefully, or set `MaxURLsPerRun` to spread the links over several runs |
| The search statistics stay empty | no siteaccess has `LogSearchStats=enabled`; the page says so at the top |
| A new alias "was modified by the system" | characters not allowed in an address were changed; the message shows the result |
| A new alias to a node does not appear in the list | aliases of nodes are on the node's URL aliases tab |
| A wildcard does nothing | a URL alias matches the address first, or an older wildcard matches it; use **Try an address** |
| The wildcard is refused with "The destination uses {2}" | the pattern has fewer `*` than the destination needs: add a `*` or remove the placeholder |

## For developers

- The views are `\Exponential\View\Kernel\Url\ListView`, `\Exponential\View\Kernel\Search\Stats`,
  `\Exponential\View\Kernel\Content\UrlaliasGlobal` and `\Exponential\View\Kernel\Content\UrlaliasWildcard`
  (`kernel/private/classes/views/...`). Their static helpers (modes, orders, the search text, usage, the
  link kind, alias destinations, wildcard matching and placeholders) are tested without a database in
  `tests/tests/kernel/classes/expUrlAdminPagesTest.php`.
- `eZURL::fetchList()` / `fetchListCount()` take `last_checked` (`never`, `checked`), `search` (with
  `only_published`) and `sort` (`address`, `checked`, `modified`, `id`), on every database including MongoDB.
- `eZURLAliasQuery` has the properties `search` (the last segment or the action contains the text) and `redirects`
  (`alias_redirects`); `eZURLWildcard::fetchFilteredList()` and `fetchFilteredListCount()` search pattern and
  destination and filter by type. Both are conditions of the SQL query, or a `$match` on MongoDB.
- `expLinkCheck` (`kernel/classes/explinkcheck.php`) decides whether a link works: `check( $url )` answers
  `valid`, `invalid` or `unknown` with a reason. The request (`curlFetch()`), the DNS lookup, the content, alias
  and MX lookups, the clock and sleeping are callables given to the constructor, which is how
  `tests/tests/kernel/classes/expLinkCheckTest.php` tests every decision without a network or a database.
- Every variable the templates had is still set; new ones: `url_summary`, `url_usage`, `url_checks`, `url_search`,
  `url_sort`, `url_feedback`, `limit`, `limit_choices` (url/list); `search_stats_*`, `search_total_count`
  (search/stats); `alias_list`, `alias_count`, `alias_total_count`, `alias_info`, `alias_search`, `alias_kind`,
  `alias_form` (urltranslator); `wildcards_total_count`, `wildcard_search`, `wildcard_kind`, `wildcard_test`,
  `wildcard_test_result` (urlwildcards). The templates fall back to the old variables when a view does not set them.
- The look is `design/admin{,4}/templates/url/exp_style.tpl` and `search/exp_style.tpl`, scoped to the page; no
  shared stylesheet changed.

## References

- `kernel/private/classes/views/url/list.php`, `kernel/private/classes/views/search/stats.php`,
  `kernel/private/classes/views/content/urlalias_global.php`, `kernel/private/classes/views/content/urlalias_wildcard.php`
- `kernel/classes/datatypes/ezurl/ezurl.php` (`handleList()`, `listOrderSQL()`, `searchLikePattern()`)
- `kernel/private/classes/cronjobs/linkcheck.php` (the link check part), `kernel/classes/explinkcheck.php` (its decisions),
  `settings/cronjob.ini [linkCheckSettings]`, `kernel/classes/ezurlwildcard.php` (matching, `fetchFilteredList()`),
  `kernel/classes/ezurlaliasquery.php` (`search`, `redirects`)
- `design/admin4/templates/url/list.tpl`, `search/stats.tpl`, `content/urlalias_global.tpl`,
  `content/urlalias_wildcard.tpl` and their copies in `design/admin`
- `settings/site.ini [SearchSettings] LogSearchStats`, `settings/admininterface.ini [PaginationSettings]`
- [Changelog 6.0.15](../changelogs/6.0/6.0.15.md#6-october-2026-links-url-aliases-wildcards-and-search-statistics)
