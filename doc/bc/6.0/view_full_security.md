# Request rules: securing `content/view/full` and every other module view

Exponential 6.0.15. Settings in `settings/requestrules.ini`. Code in
`kernel/private/classes/requestrules/`. Command `bin/php/ezrequestrules.php`.
Tests in `tests/tests/kernel/classes/requestrules/`.

**The request rules** decide what happens to a request just before its module
view runs. A rule says *when* (conditions such as "typed as a system URL",
"full view", "the user lacks a policy", "below node 864", "from this network")
and *what* (redirect to the URL alias, not found, access denied, sign in
first, redirect, rewrite, log). Rules are written in INI, can be switched on
per siteaccess, can be extended in PHP by any extension, and are tied to role
policies, so who is exempt is decided in the role editor, not in a file.

They were made for one question that settings could not answer before:

> *How do I stop anonymous visitors from opening pages by their system URL,
> `/content/view/full/123`, while the same page at its URL alias,
> `/Fit-Healthy/Some-Article`, keeps working for everyone?*

The answer is one rule, shipped ready to use and switched on with two lines.

Read this page if you want to keep visitors off system URLs, protect a section, or run any check before a module view;
and before you upgrade, if you rely on `[SiteAccessRules]` or on `PolicyOmitList[]`.

## In short

| | |
|---|---|
| What changed | New request rules engine (`settings/requestrules.ini`, `kernel/private/classes/requestrules/`), asked before every module view runs. New policy function `content/view_system_url`. New command `bin/php/ezrequestrules.php` (list, check, explain). Three rules ship defined and off. |
| Who is affected | Nobody until a rule is listed in `RuleList[]`. Then: every module view is asked, including `PolicyOmitList[]` views. `[SiteAccessRules]` keeps working and is checked first. Roles with `content/*` include the new policy function. |
| How to check | `php bin/php/ezrequestrules.php -s <siteaccess> --check` |
| How to fix | Nothing to fix. To use it, follow the [Quick start](#1-quick-start); remove any template redirect you added in `full.tpl` for the same purpose. |


## Contents

1. [Quick start](#1-quick-start)
2. [Why settings and policies alone could not do it](#2-why-settings-and-policies-alone-could-not-do-it)
3. [How a request is decided](#3-how-a-request-is-decided)
4. [Writing rules](#4-writing-rules)
5. [Reference: conditions](#5-reference-conditions)
6. [Reference: actions](#6-reference-actions)
7. [Reference: facts](#7-reference-facts)
8. [The policy function `content/view_system_url`](#8-the-policy-function-contentview_system_url)
9. [Recipes](#9-recipes)
10. [Extending the rules in PHP](#10-extending-the-rules-in-php)
11. [Using the engine from PHP](#11-using-the-engine-from-php)
12. [Checking, explaining and testing](#12-checking-explaining-and-testing)
13. [Caches](#13-caches)
14. [Exponential Velocity and persistent workers](#14-exponential-velocity-and-persistent-workers)
15. [Performance](#15-performance)
16. [Security notes](#16-security-notes)
17. [What the rules do not see](#17-what-the-rules-do-not-see)
18. [Compatibility and upgrading](#18-compatibility-and-upgrading)
19. [Troubleshooting](#19-troubleshooting)
20. [Files](#20-files)


## 1. Quick start

**Goal:** anonymous visitors (and anyone else without a particular policy)
who type `/content/view/full/<node>` are sent, with a `301`, to the node's URL
alias. A node without an alias of its own answers `404`. Administrators and
anyone you grant the policy keep using system URLs.

**Step 1.** Switch the shipped rule on for each public siteaccess, in
`settings/siteaccess/<siteaccess>/requestrules.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?

[RequestRuleSettings]
RuleList[]
RuleList[]=system_url_full_view_to_alias

*/ ?>
```

Leave admin and editor siteaccesses alone: editors use system URLs there on
purpose.

**Step 2.** Grant the policy `content` / `view_system_url` to the roles that
should keep system URLs on the public siteaccesses, such as an Editor role (see
[section 8](#8-the-policy-function-contentview_system_url)). Administrators
already have it through `content/*` or `*/*`.

**Step 3.** Deploy and check:

```bash
./console exp:velocity deploy --no-autoload --allow-root-user   # clears the INI and page caches, restarts Velocity
php bin/php/ezrequestrules.php -s site --check --allow-root-user
php bin/php/ezrequestrules.php -s site --uri=content/view/full/2 --allow-root-user
curl -sk -o /dev/null -w '%{http_code} %{redirect_url}\n' https://www.example.com/content/view/full/2
```

Without Velocity, the INI and page caches still need clearing:
`php bin/php/ezcache.php --clear-tag=ini --allow-root-user`, then
`--clear-id=content,exphttpcache`.

What it looks like on a reference installation (2026-10-02), on Apache and Velocity alike:

| Request | Anonymous | Administrator |
|---|---|---|
| `/content/view/full/2` | `301` to `/websites`, `Cache-Control: private, no-store` | `200` |
| `/content/view/full/2/(offset)/10?x=1` | `301` to `/websites/(offset)/10?x=1` | `200` |
| `/fit-healthy` (the alias) | `200`, cached as before | `200` |
| `/` (front page, `IndexPage=/content/view/full/89`) | `200` | `200` |
| `/content/view/line/2` (another view mode) | `200` | `200` |
| `/content/view/full/999999` (no such node) | `404` | `404` |

That is all most sites need. The rest of this guide explains the mechanism
completely: what can be decided and how, how to extend it, and how to tell
what it is doing.


## 2. Why settings and policies alone could not do it

Before the request rules existed there were four tools, and none could
express "this view, reached this way, for these users":

| Tool | What it does | Why it is not the answer |
|---|---|---|
| Role policy `content/read`, limited by Class, Section, Owner, Group, Node, Subtree, State | Decides **which content** a user may read | Not **which view mode** or **which URL**. Take `content/read` away and the content disappears everywhere: at its alias, in lists, in fetches on other pages. |
| `site.ini [SiteAccessRules] Rules[]` (`access;disable`, `module;content/view`) | Switches a module or module/view off for a siteaccess | It applies to **every user**. It is checked **after** the URL alias is translated, so switching off `content/view` takes every alias down with it: the whole site answers `404`. And it cannot tell `full` from `line`. |
| `site.ini [SiteAccessSettings] RequireUserLogin` + `AnonymousAccessList[]` | Login for a whole siteaccess, with exemptions | All or nothing per siteaccess. |
| A redirect in a `full.tpl` template | Works on one design | The full view has already been built, and perhaps cached, when the template runs. It belongs to one design, not the system. |

The deciding fact is that a URL alias **is** `content/view/full/<node>`: the
kernel translates `/Fit-Healthy` into `content/view/full/89` before anything
checks access. The kernel knew what the visitor typed (it keeps the address
before translating it), but nothing used that. The request rules use it: the
fact `requested_via` is `system`, `alias`, `wildcard` or `index`.


## 3. How a request is decided

```
 request
   |
   |-- served from a page cache?  (role-aware HTTP cache early exit, Velocity response cache)
   |       yes -> the stored page. Rule-dependent pages are never stored: section 13
   |
   v  the kernel (ezpKernelWeb::dispatchLoop)
   1. address typed kept                 typed_uri, e.g. "Fit-Healthy" or "content/view/full/123"
   2. URL alias / wildcard translation   "Fit-Healthy" -> "content/view/full/89"; requested_via = alias
      a moved alias                      -> 301 to its new address (no rule is asked)
   3. [SiteAccessRules]                  module or view switched off -> not found
   4. the module and view exist?         no -> not found
   5. role policies                      not allowed -> access denied / sign in
   6. REQUEST RULES                      the first rule whose conditions all match decides
   7. the module view runs               (unless step 6 decided otherwise)
```

What follows from that order:

- **Rules only ever take access away or send the visitor elsewhere.** They are
  asked after the policies allowed the view, so no rule can open a view a
  policy closed. Even `Action=allow` only means "stop asking further rules".
- **Rules see the translated request.** A rule about `content/view` applies to
  aliases too. Use `requested_via` to tell them apart.
- **The first rule that decides, decides.** Order matters. Put exceptions
  (`allow`) before the wider rules they are exceptions to.
- **A rule that matches but cannot act passes on.** `redirect_to_alias` on a
  node without an alias cannot act: the rule's `FallbackAction` runs, and
  without one the next rule is asked.
- **No rule matched? The request runs as it always has.** An empty `RuleList`
  costs about 2.6 µs per request ([section 15](#15-performance)).
- **Every answer a rule gives, other than `allow`, is sent with
  `Cache-Control: private, no-store`.** It depends on who asked, so no browser
  or shared cache may keep it. A browser that kept a `301` would go on
  redirecting a visitor after they signed in.


## 4. Writing rules

### 4.1 Where settings go

Never edit `settings/requestrules.ini`. Override it like any INI file:

| File | Scope |
|---|---|
| `settings/override/requestrules.ini.append.php` | every siteaccess |
| `settings/siteaccess/<name>/requestrules.ini.append.php` | one siteaccess (overrides the above) |
| `extension/<ext>/settings/requestrules.ini.append.php` | from an active extension (the extension's rules, handlers and providers) |

`RuleList[]` on its own line **empties the list** before the items that
follow, so a siteaccess can replace the list it inherited. Leave that line out
to add to it.

### 4.2 Anatomy of a rule

```ini
[RequestRuleSettings]
RuleList[]=members_area                    # switched on: asked, in this order

[Rule-members_area]                        # the rule: [Rule-<name>]
Description=Anonymous visitors of the members area sign in first.
Conditions[subtree]=864                    # ALL conditions must match
Conditions[user]=anonymous
Action=login                               # what happens then
ActionArgs[...]=...                        # arguments of the action, if it has any
FallbackAction=notfound                    # optional: when the action cannot act
FallbackArgs[...]=...
```

A rule group that exists but is not in `RuleList[]` (or given by a rule
provider) is **off**. The shipped rules are written that way: defined, and
off until a siteaccess lists them.

### 4.3 Condition values

| Form | Meaning | Example |
|---|---|---|
| `value` | matches this value | `Conditions[view_mode]=full` |
| `a,b,c` | matches **any** of them (spaces around commas are ignored) | `Conditions[siteaccess]=site, bold` |
| `!a,b` | matches **none** of them: the whole condition turned around | `Conditions[policy]=!content/view_system_url` |
| `pat*` | a shell pattern, where the condition says it takes patterns (`*`, `?`, `[a-z]`) | `Conditions[uri]=content/view/*` |
| `name:argument` in the key | a condition that needs a name | `Conditions[param:NodeID]=2`, `Conditions[header:X-Requested-With]=XMLHttpRequest` |

Matching is case-insensitive. A condition with an empty value (or only `!`)
never matches, and `--check` reports it.

### 4.4 Several rules

```ini
RuleList[]
RuleList[]=robots_may_see_node_2      # 1. exception first
RuleList[]=system_url_full_view_to_alias   # 2. the general rule

[Rule-robots_may_see_node_2]
Conditions[node]=2
Conditions[requested_via]=system
Action=allow
```

The rules are asked in the order of `RuleList[]`, followed by the rules from
`RuleProviders[]`, in their order.

### 4.5 Switching everything off

```ini
[RequestRuleSettings]
Enabled=false
```

No rule is asked, and the kernel behaves exactly as it did before this
feature.


## 5. Reference: conditions

**Stable** means the answer is the same for every request that one stored page
answers: the cache keys hold what the condition reads. A rule with a
non-stable condition keeps the pages it could decide out of the shared caches
([section 13](#13-caches)). Prefer stable conditions where you can.

| Condition | Reads (fact) | Values | Patterns | Stable | Example |
|---|---|---|---|---|---|
| `requested_via` | `requested_via` | `system` (typed as module/view), `alias` (a URL alias), `wildcard` (a URL wildcard), `index` (the front page, `IndexPage`) | no | yes | `Conditions[requested_via]=system` |
| `uri` | `typed_uri`: the address typed, without siteaccess, slashes or query | addresses | yes | yes | `Conditions[uri]=content/view/*` |
| `resolved_uri` | `resolved_uri`: the system address that runs | addresses | yes | yes | `Conditions[resolved_uri]=content/view/full/89` |
| `module` | `module` | module names | no | yes | `Conditions[module]=content,shop` |
| `module_view` | `module`/`view` | module/view | yes | yes | `Conditions[module_view]=content/*` |
| `view_mode` | `view_mode`: the `ViewMode` of `content/view` | view modes | no | yes | `Conditions[view_mode]=full` |
| `param:<Name>` | `params[<Name>]`: a view parameter by its name in the module's `module.php` | values | yes | yes | `Conditions[param:NodeID]=2,43` |
| `node` | `node_id`: the node of `content/view` | node ids | no | yes | `Conditions[node]=2` |
| `subtree` | `path_ids`: the node and its ancestors | node ids | no | yes | `Conditions[subtree]=864` |
| `class` | `class_identifier` of the node | class identifiers | no | yes | `Conditions[class]=article,blog_post` |
| `section` | `section_id`, `section_identifier` of the object | ids or identifiers | no | yes | `Conditions[section]=members` |
| `state` | `state_identifiers`: `group/state` of the object | identifiers | yes | yes | `Conditions[state]=ez_lock/locked` |
| `user` | `is_anonymous` | `anonymous`, `logged_in` | no | yes | `Conditions[user]=anonymous` |
| `role` | `role_names`: roles assigned to the user or their groups | role names | no | yes | `Conditions[role]=Partner` |
| `policy` | `has_access`: `eZUser::hasAccessTo()`; limited access counts | module/function | no | yes | `Conditions[policy]=!content/view_system_url` |
| `siteaccess` | `siteaccess` | siteaccess names | yes | yes | `Conditions[siteaccess]=site,bold*` |
| `method` | `method` | `GET`, `POST`, `HEAD`, ... | no | yes | `Conditions[method]=!POST` |
| `scheme` | `scheme` | `http`, `https` | no | yes | `Conditions[scheme]=http` |
| `host` | `host`: the host name, no port | host names | yes | yes | `Conditions[host]=staging.*` |
| `user_id` | `user_id`: the user's object id | ids | no | **no** | `Conditions[user_id]=14` |
| `user_login` | `user_login` | logins | yes | **no** | `Conditions[user_login]=svc-*` |
| `group` | `group_ids`: user group object ids of the user | ids | no | **no** | `Conditions[group]=12` |
| `header:<Name>` | a request header; `*` = sent at all | values | yes | **no** | `Conditions[header:X-Requested-With]=XMLHttpRequest` |
| `ip` | `client_ip` | addresses and CIDR networks, IPv4 and IPv6 | no | **no** | `Conditions[ip]=10.0.0.0/8, 2001:db8::/32` |
| `fact:<name>` | any fact, including an extension's | values | yes | **no** | `Conditions[fact:customer_tier]=gold` |

`user_id`, `user_login` and `group` are not stable because the role-aware
HTTP cache shares one stored page between all users with the same roles.

Conditions about the node (`node`, `subtree`, `class`, `section`, `state`)
never match a request that is not about a node. Their facts are `null` then,
and a negated one (`!article`) does not match either.


## 6. Reference: actions

| Action | Answer | Arguments | Cannot act (falls back) when |
|---|---|---|---|
| `allow` | the view runs; **no later rule is asked** | none | never |
| `notfound` | the "not found" page, HTTP `404` (kernel error 3). Says nothing about whether the page exists | none | never |
| `forbidden` | the "access denied" page, HTTP `403` (kernel error 1), as a missing policy gives | none | never |
| `login` | anonymous: `302` to `user/login`, and back to the page after signing in. Signed in: "access denied" | none | the request is a sign-in view itself (`user/login`, `logout`, `register`, `activate`, `success`, `forgotpassword`, `password`), so no rule can make a loop |
| `redirect` | HTTP redirect | `to` (required; [placeholders](#placeholders)), `status` `301`, `302` (default), `303`, `307`, `308` | `to` is empty |
| `redirect_to_alias` | HTTP redirect to the URL alias of the node, with the view's user parameters (`/(offset)/10`); the kernel keeps the query string | `status` (default `301`) | not about a node, or the node has no alias of its own |
| `rewrite` | the kernel runs another address instead, without a redirect; the visitor keeps the address they typed. The rules are asked again for the new address, at most `MaxRewrites` (3) times, and then "not found" | `to` (required; placeholders) | `to` is empty |
| `log` | writes a line to `var/log/requestrules.log`; **always passes on** (to the fallback, or the next rule) | `message` (placeholders; default: the address) | always: that is its purpose |

A redirect target can be a path inside the siteaccess (`/about-us`), a system
address (`/content/view/full/2`, sent as that node's alias, as every kernel
redirect is), or an absolute URL. An absolute URL on another host must be
listed in `site.ini [SiteSettings] AllowedRedirectHosts[]`; otherwise the
kernel refuses it with `403`.

### Placeholders

`to` and `message` can use facts: `{node_id}`, `{typed_uri}`, `{view_mode}`,
`{siteaccess}`, `{user_login}`, any scalar fact `{name}`, a view parameter
`{param:NodeID}`, a header `{header:Host}`. An unknown placeholder, or one
whose fact is not a single value, becomes empty.

### `log` with a fallback: audit, then act

```ini
Action=log
ActionArgs[message]=refused {typed_uri} for {user_login}
FallbackAction=notfound
```

This writes the line, then answers "not found": one rule both records and
refuses.


## 7. Reference: facts

Facts are what conditions and actions read. The kernel defines them for every
request (`ezpRequestRuleKernel::context()`). Most are **resolved only when a
rule reads them**: a rule about the address never loads the node, and the node
is loaded at most once however many rules ask.

| Fact | Type | Example | Cost |
|---|---|---|---|
| `typed_uri` | string | `content/view/full/2`, `fit-healthy`, `` (front page) | free |
| `requested_via` | string | `system`, `alias`, `wildcard`, `index` | free |
| `resolved_uri` | string | `content/view/full/89` | free |
| `module`, `view` | string | `content`, `view` | free |
| `params` | array | `{"ViewMode":"full","NodeID":"89"}` | free |
| `view_mode` | string\|null | `full` | free |
| `node_id` | int\|null | `89` | free |
| `user_parameters` | array | `{"offset":"10"}` | free |
| `user_parameters_uri` | string | `/(offset)/10` | free |
| `rewrites` | int | rewrites so far in this request | free |
| `siteaccess`, `method` | string | `site`, `GET` | free |
| `scheme`, `host`, `client_ip`, `headers` | string / array | `https`, `www.example.com`, `10.1.2.3`, lower-case names | on use |
| `user` | eZUser | the current user | free |
| `user_id` | int | `10` (anonymous) | free |
| `is_anonymous`, `user_login`, `group_ids`, `role_names` | | | on use |
| `has_access` | callable( module, function ) : bool | | on use |
| `node`, `object` | eZContentObjectTreeNode / eZContentObject | | on use, one fetch |
| `url_alias`, `path_ids`, `class_identifier` | from the node | `fit-healthy`, `[1,2,89]`, `frontpage` | on use |
| `section_id`, `section_identifier`, `state_identifiers` | from the object | `1`, `standard`, `["ez_lock/not_locked"]` | on use |
| `rules_vary_by_request` | bool | set by the engine ([section 13](#13-caches)) | |

An extension adds facts with a fact provider ([10.3](#103-a-fact-provider)).
A resolver that throws is written to the debug output and gives `null`, so one
broken fact never breaks a page.


## 8. The policy function `content/view_system_url`

A new function of the `content` module. It has no limitations and is checked
by no view. It exists so that **who is exempt** from a system-URL rule is a
role decision:

```ini
Conditions[policy]=!content/view_system_url     # the rule applies to users WITHOUT it
```

- **Administrators** have it through `content/*` (all functions of the content
  module) or `*/*`.
- **Anonymous** does not have it unless you add it, so leave it out.
- **Editors** who work on the public siteaccess with system URLs need it: in
  the admin, open *Roles and policies*, then the role (e.g. *Editor*), *New
  policy*, module *content*, function *view_system_url*, *Grant full access*.

The same pattern works with any policy, including your own module's
functions: `Conditions[policy]=!mymodule/premium`.


## 9. Recipes

Each recipe is complete: put the group in `requestrules.ini.append.php` and
add the name to `RuleList[]`. Node ids, class identifiers and addresses are
examples. Check yours with `bin/php/ezrequestrules.php`.

### 9.1 System URLs to the alias (shipped: `system_url_full_view_to_alias`)

```ini
[Rule-system_url_full_view_to_alias]
Conditions[requested_via]=system
Conditions[module_view]=content/view
Conditions[view_mode]=full
Conditions[policy]=!content/view_system_url
Action=redirect_to_alias
ActionArgs[status]=301
FallbackAction=notfound
```

### 9.2 Strict: no system URL of any view mode (shipped: `system_url_content_view_notfound`)

```ini
[Rule-system_url_content_view_notfound]
Conditions[requested_via]=system
Conditions[module_view]=content/view
Conditions[policy]=!content/view_system_url
Action=notfound
```

Mind the views your design fetches by URL (for example an AJAX call to
`content/view/line/<node>`). Exempt them first ([9.11](#911-an-exception-before-a-wider-rule)),
or keep 9.1.

### 9.3 Audit first, then enforce (shipped: `system_url_full_view_audit`)

Put the audit rule **before** the rule it previews. It logs and passes on:

```ini
RuleList[]
RuleList[]=system_url_full_view_audit
```

```
$ tail var/log/requestrules.log
[ Oct 02 2026 06:01:12 ] [] [system_url_full_view_audit] GET system URL content/view/full/2 (node 2) by anonymous ip=10.1.2.3
```

When the log shows only what you expect, add the enforcing rule after it, or
replace it.

### 9.4 A members area: sign in first

```ini
[Rule-members_area]
Description=Below node 864, anonymous visitors sign in first and come back.
Conditions[subtree]=864
Conditions[user]=anonymous
Action=login
```

Policies still decide what members may read; this rule only sends anonymous
visitors to the login page instead of a bare "access denied".

### 9.5 An intranet section, only from the office networks

```ini
[Rule-intranet_from_office_only]
Conditions[section]=intranet
Conditions[ip]=!10.0.0.0/8, 192.168.0.0/16, 2001:db8:42::/48
Action=notfound
```

Behind a load balancer, set `site.ini [HTTPHeaderSettings] ClientIpByCustomHTTPHeader=X-Forwarded-For`
so `client_ip` is the visitor, not the balancer. Because `ip` varies by
request, intranet pages are not stored in the shared caches.

### 9.6 Never show a class to anonymous visitors

```ini
[Rule-internal_memos_hidden]
Conditions[class]=internal_memo
Conditions[user]=anonymous
Action=notfound
```

### 9.7 Move a whole branch: old addresses to new ones

```ini
[Rule-old_news_to_archive]
Conditions[subtree]=1200
Conditions[requested_via]=alias
Action=redirect
ActionArgs[to]=/archive/node/{node_id}
ActionArgs[status]=308
```

For addresses that no longer resolve to any node, use URL wildcards
(`content/urlwildcards`). Rules only see addresses that reach a module view
([section 17](#17-what-the-rules-do-not-see)).

### 9.8 Maintenance: everyone but administrators sees the maintenance page

```ini
RuleList[]
RuleList[]=maintenance_admins_through
RuleList[]=maintenance_page

[Rule-maintenance_admins_through]
Conditions[role]=Administrator
Conditions[module]=content,user,ezjscore
Action=allow

[Rule-maintenance_page]
Conditions[module]=content
Conditions[node]=!555
Action=rewrite
ActionArgs[to]=content/view/full/555
```

Node 555 is the maintenance page. `user/login` keeps working, so
administrators can still sign in.

### 9.9 A view mode only for scripts (AJAX)

```ini
[Rule-ajax_view_only_by_xhr]
Conditions[view_mode]=ajax
Conditions[header:X-Requested-With]=!XMLHttpRequest
Action=notfound
```

A header is never authentication. This keeps casual visitors and crawlers off
a fragment view; it does not protect data.

### 9.10 A view that must be posted, never opened

```ini
[Rule-action_only_by_post]
Conditions[module_view]=content/action
Conditions[method]=!POST
Action=notfound
```

### 9.11 An exception before a wider rule

```ini
RuleList[]
RuleList[]=line_view_for_the_menu_script
RuleList[]=system_url_content_view_notfound

[Rule-line_view_for_the_menu_script]
Conditions[module_view]=content/view
Conditions[view_mode]=line
Conditions[header:X-Requested-With]=XMLHttpRequest
Action=allow
```

### 9.12 A staging host behind a login

```ini
[Rule-staging_requires_login]
Conditions[host]=staging.*
Conditions[user]=anonymous
Conditions[module]=!user
Action=login
```

### 9.13 Partner pages for partners, a teaser for everyone else

```ini
[Rule-partner_pages]
Conditions[subtree]=900
Conditions[role]=!Partner,Administrator
Action=redirect
ActionArgs[to]=/become-a-partner
ActionArgs[status]=302
```

### 9.14 Retire a page but keep its address

```ini
[Rule-retired_offer_shows_successor]
Conditions[node]=123
Action=rewrite
ActionArgs[to]=content/view/full/456
```

The visitor stays on the old address and sees node 456.

### 9.15 Different rules per siteaccess

`settings/siteaccess/site/requestrules.ini.append.php`:

```ini
[RequestRuleSettings]
RuleList[]
RuleList[]=system_url_full_view_to_alias
```

`settings/siteaccess/bold_ger/requestrules.ini.append.php`:

```ini
[RequestRuleSettings]
RuleList[]
RuleList[]=system_url_content_view_notfound
```

The same rules can also live in one file with `Conditions[siteaccess]=...`.

### 9.16 HTTPS only for signed-in users

```ini
[Rule-signed_in_over_https]
Conditions[scheme]=http
Conditions[user]=logged_in
Action=redirect
ActionArgs[to]=https://{host}/{typed_uri}
ActionArgs[status]=308
```

(Add the host to `AllowedRedirectHosts[]` if it is not the current one; it
is, here.)


## 10. Extending the rules in PHP

An extension adds **conditions**, **actions**, **facts** and **rules**. Each is a
small class named in the extension's
`extension/<ext>/settings/requestrules.ini.append.php` and found by the
autoloader. Run `php bin/php/ezpgenerateautoloads.php -e` after adding the
classes.

The examples build one extension, `myshop`, that keeps premium articles for
paying customers.

### 10.1 A condition

```php
<?php
// extension/myshop/classes/myshopcustomertiercondition.php
/**
 * customer_tier: the tier of the signed-in customer, from the fact
 * 'customer_tier' (myShopFactProvider): Conditions[customer_tier]=gold,silver
 */
class myShopCustomerTierCondition implements ezpRequestRuleCondition
{
    public function matches( ezpRequestContext $context, array $values, $argument )
    {
        $tier = $context->get( 'customer_tier' );     // resolved on first use
        foreach ( $values as $value )
        {
            if ( strcasecmp( $value, (string)$tier ) === 0 )
                return true;
        }
        return false;
    }
}
```

Rules for a condition:

- **Read only facts.** Never touch the database directly in `matches()`. Put
  that in a fact, so it is loaded once, lazily, and can be given in tests.
- **Never change anything.** A condition may be asked several times, or not
  at all.
- **`$values` is never empty, and the "!" is already handled.** Answer "does
  any value match?".
- **Stable?** Add `implements ezpRequestRuleCondition, ezpRequestRuleStableCondition`
  only when the answer depends on nothing but the cache keys (address,
  siteaccess, host, scheme, the node, the user's roles and policies). A
  customer tier is per user, so not stable: leave it out, and pages it could
  decide stay out of the shared caches.

A condition that compares one fact can be just this:

```php
class myShopRegionCondition extends ezpRequestFactCondition { protected $fact = 'region'; protected $patterns = true; }
```

### 10.2 An action

```php
<?php
// extension/myshop/classes/myshoppaywallaction.php
/**
 * paywall: shows the paywall page (design:myshop/paywall.tpl) in place of the
 * article, with HTTP 402. ActionArgs[template]=design:myshop/paywall.tpl
 */
class myShopPaywallAction implements ezpRequestRuleAction
{
    public function decide( ezpRequestContext $context, array $arguments, ezpRequestRule $rule )
    {
        $node = $context->get( 'node' );
        if ( !$node instanceof eZContentObjectTreeNode )
            return null;                                   // cannot act: fallback / next rule

        $tpl = eZTemplate::factory();
        $tpl->setVariable( 'node', $node );
        header( 'HTTP/1.1 402 Payment Required' );
        return ezpRequestRuleResult::moduleResult( array(
            'content' => $tpl->fetch( isset( $arguments['template'] ) ? $arguments['template'] : 'design:myshop/paywall.tpl' ),
            'path' => array( array( 'text' => $node->attribute( 'name' ), 'url' => false ) ),
            'node_id' => $node->attribute( 'node_id' ),
        ) );
    }
}
```

An action **decides**. It returns one of the `ezpRequestRuleResult` answers
and the kernel sends it:

| Constructor | Answer |
|---|---|
| `ezpRequestRuleResult::allow()` | run the view; ask no further rule |
| `::notFound()`, `::forbidden()`, `::login()` | as the built-in actions |
| `::redirect( $location, $status )` | redirect; `$status` 301/302/303/307/308 |
| `::rewrite( $uri )` | run `$uri` instead |
| `::moduleResult( array )` | this module result **is** the page, inside the pagelayout |
| `null` | cannot act here: the fallback runs, or the next rule |

### 10.3 A fact provider

```php
<?php
// extension/myshop/classes/myshopfactprovider.php
class myShopFactProvider implements ezpRequestFactProvider
{
    public function defineFacts( ezpRequestContext $context )
    {
        // A resolver: runs only if a rule reads 'customer_tier', at most once
        $context->define( 'customer_tier', function ( ezpRequestContext $c )
        {
            $user = $c->get( 'user' );
            return $user && !$c->get( 'is_anonymous' ) ? myShopCustomer::tierOf( $user->id() ) : 'none';
        } );
        $context->define( 'is_premium', function ( ezpRequestContext $c )
        {
            $object = $c->get( 'object' );
            return $object ? myShopPremium::isPremium( $object->attribute( 'id' ) ) : false;
        } );
    }
}
```

Facts can be read by built-in conditions too: `Conditions[fact:is_premium]=1`.

### 10.4 A rule provider (rules from a database, another file, an API)

```php
<?php
// extension/myshop/classes/myshoprulesprovider.php
class myShopRulesProvider implements ezpRequestRuleProvider
{
    public function rules()
    {
        $rules = array();
        foreach ( myShopEmbargo::fetchActive() as $embargo )        // e.g. rows of a table
        {
            $rules[] = new ezpRequestRule(
                'embargo_' . $embargo->attribute( 'id' ),
                array( 'node' => (string)$embargo->attribute( 'node_id' ), 'policy' => '!myshop/embargo_bypass' ),
                'notfound',
                array(), null, array(),
                'Embargoed until ' . date( 'c', $embargo->attribute( 'until' ) )
            );
        }
        return $rules;
    }
}
```

Providers run once per siteaccess and process (the engine is kept; see
[section 14](#14-exponential-velocity-and-persistent-workers)). Rules that
change during the day belong in a **fact** (`Conditions[fact:embargoed]=1`,
read per request), not in a provider.

### 10.5 Registering it all

```ini
<?php /* #?ini charset="utf-8"?
# extension/myshop/settings/requestrules.ini.append.php

[RequestRuleSettings]
ConditionHandlers[customer_tier]=myShopCustomerTierCondition
ActionHandlers[paywall]=myShopPaywallAction
FactProviders[]=myShopFactProvider
RuleProviders[]=myShopRulesProvider
RuleList[]=premium_paywall

[Rule-premium_paywall]
Description=Premium articles for gold and silver customers; everyone else sees the paywall.
Conditions[class]=article
Conditions[fact:is_premium]=1
Conditions[customer_tier]=!gold,silver
Action=paywall
ActionArgs[template]=design:myshop/paywall.tpl
*/ ?>
```

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezrequestrules.php -s site --check --allow-root-user
```

### 10.6 Testing your extension's rules without a database

The engine takes its settings as an array, and a context is just facts:

```php
$engine = new ezpRequestRuleEngine( array(
    'rules' => array( 'premium_paywall' => array(
        'Conditions' => array( 'class' => 'article', 'fact:is_premium' => '1', 'customer_tier' => '!gold,silver' ),
        'Action' => 'paywall' ) ),
    'condition_handlers' => array( 'customer_tier' => 'myShopCustomerTierCondition' ),
    'action_handlers' => array( 'paywall' => new myShopPaywallActionStub() ),
) );
$result = $engine->evaluate( new ezpRequestContext( array(
    'class_identifier' => 'article', 'is_premium' => true, 'customer_tier' => 'free',
) ) );
$this->assertSame( ezpRequestRuleResult::MODULE_RESULT, $result->type );
$this->assertSame( array(), $engine->validate() );
```

`tests/tests/kernel/classes/requestrules/ezpRequestRuleEngineTest.php` shows
every built-in condition and action tested this way.


## 11. Using the engine from PHP

```php
$engine = ezpRequestRuleEngine::instance();     // the current siteaccess's rules

// Add a rule at run time (idempotent by name: adding it again replaces it)
$engine->addRule( new ezpRequestRule( 'tmp_block', array( 'node' => '123' ), 'notfound' ) );
$engine->addRule( new ezpRequestRule( 'tmp_exception', array( 'node' => '123', 'role' => 'Editor' ), 'allow' ), 'tmp_block' );  // before tmp_block
$engine->removeRule( 'tmp_block' );

// Decide about any request, e.g. in a custom module or a script
$context = ezpRequestRuleKernel::context( 'content', 'view', array( 'full', '123' ),
                                          array( 'typed_uri' => 'content/view/full/123', 'via' => 'system' ),
                                          eZUser::currentUser() );
$result = $engine->evaluate( $context );       // ezpRequestRuleResult, or null: no rule decides
if ( $result ) echo $result->rule, ': ', $result->describe();

// Why
foreach ( $engine->explain( $context ) as $row )
    printf( "%s %s %s\n", $row['decides'] ? 'DECIDES' : ( $row['matched'] ? 'matched' : 'no' ), $row['rule'], json_encode( $row['conditions'] ) );

// Problems in the settings, as --check prints them
print_r( $engine->validate() );

ezpRequestRuleEngine::resetInstance();          // re-read requestrules.ini on the next instance()
```

Rules added with `addRule()` live as long as the engine: one PHP-FPM request,
or the life of a Velocity worker. Use them for tests and scripts. Rules that
must always apply belong in settings or a rule provider.


## 12. Checking, explaining and testing

### 12.1 `bin/php/ezrequestrules.php`

```bash
php bin/php/ezrequestrules.php -s site --list   --allow-root-user   # the rules, in order
php bin/php/ezrequestrules.php -s site --check  --allow-root-user   # problems; exit code 1 when any (for CI)
php bin/php/ezrequestrules.php -s site --uri=content/view/full/2 --allow-root-user
php bin/php/ezrequestrules.php -s site --uri=content/view/full/2 --user=admin --allow-root-user
php bin/php/ezrequestrules.php -s site --uri=fit-healthy --ip=10.1.2.3 --header=X-Requested-With:XMLHttpRequest --method=POST --allow-root-user
```

`--uri` translates the address the way the kernel does (alias, wildcard,
front page), so the explanation is the one a real request gets. It also says
when the policies would refuse the view before any rule is asked.

Real output (reference installation, 2026-10-02):

```
Address  /content/view/full/2  ->  content/view/full/2  (requested via system)
User     anonymous (anonymous)  siteaccess site

DECIDES system_url_full_view_to_alias
        yes  requested_via = system
        yes  module_view = content/view
        yes  view_mode = full
        yes  policy = !content/view_system_url
        -> redirect 301 to /websites

Result   decided by the rule marked DECIDES
Facts the rules read:
  node_id              2
  requested_via        'system'
  typed_uri            'content/view/full/2'
  url_alias            'websites'
  ...
```

`--check` catches:

- a rule in `RuleList[]` without its group;
- a rule without conditions or action;
- unknown condition and action names;
- empty values;
- `param`, `header` and `fact` without their `:name`;
- policy values that are not module/function;
- `redirect` and `rewrite` without `to`;
- statuses that are not redirects;
- handler and provider classes that do not exist or implement the wrong
  interface.

**Run it in your deployment pipeline.** A rule with a typo never matches; it
does not fail the site, so `--check` is how a typo is found.

### 12.2 At run time

- **Debug output** (`DebugOutput=enabled`): each decision is a notice,
  `Request rule 'name': redirect 301 to /x for content/view/full/2`.
  Unknown conditions and actions are errors.
- **`$GLOBALS['ezpRequestRuleDecision']`:** the `ezpRequestRuleResult` that
  decided this request, or `null`. Use it in a pagelayout operator, or a
  response listener.
- **`Action=log`:** `var/log/requestrules.log`.

### 12.3 Tests

```bash
php vendor/bin/phpunit tests/tests/kernel/classes/requestrules/    # 21 tests: every condition, action, the shipped file
```

The same 21 tests run with the rest of the kernel tests
(`--testsuite kernel-classes`).

By use, over HTTP, against a running site: anonymous and administrator, every
action, Apache and Velocity. Verified 2026-10-02 on a reference installation, all PASS on both
servers:

| Check | Apache | Velocity |
|---|---|---|
| anonymous system URL → 301 to the alias, `private, no-store` | PASS | PASS |
| user parameters and query kept | PASS | PASS |
| alias, front page, line view left alone | PASS | PASS |
| administrator keeps the system URL (200) | PASS | PASS |
| `allow` before the shipped rule | PASS | PASS |
| `forbidden` 403, `notfound` 404 | PASS | PASS |
| `redirect` 307 to a system target, sent as its alias | PASS | PASS |
| `rewrite` keeps the address (200) | PASS | PASS |
| a rewrite loop stops after `MaxRewrites` (404) | PASS | PASS |
| `log` writes its line and passes on | PASS | PASS |
| `login` → 302 to user/login, and back to the page after signing in | PASS | PASS |
| unaffected pages are still served from the page caches (`X-Exp-Cache: HIT`) | PASS | PASS |


## 13. Caches

Two caches answer requests **before the kernel runs**, so before any rule is
asked: the role-aware HTTP cache (its early exit in `config.php`) and Velocity's
response cache. The rules are safe with them because of three guarantees:

1. **No answer a rule gives other than `allow` is stored.** It is sent with
   `Cache-Control: private, no-store`, which Velocity, proxies and browsers
   honour, and the HTTP cache refuses it (`X-Exp-Cache: BYPASS (request rule)`).
   A redirect for anonymous visitors can therefore never be served to an
   editor, and a browser does not keep a `301` that would still apply after
   signing in.
2. **A page whose rules read only stable conditions is cached as before.**
   Two requests that one stored page answers have the same address,
   siteaccess, host, scheme and permission context, so the rules decide the
   same for both. The shipped rules are all stable: they do not reduce caching
   at all (on the reference installation, `/fit-healthy` stays a `HIT`).
3. **A page that a rule with a request-varying condition could decide is
   never stored.** If every stable condition of such a rule matches, another
   request for the same page with a different header, address or user could be
   decided otherwise. So the page is sent `private, no-store` even when the
   view runs, and the context fact `rules_vary_by_request` is `true`. Stable
   conditions are asked first, so a rule like
   `module_view=content/view` + `header:X-Test=deny` only affects
   `content/view` pages.

**After changing rules, clear the page caches.** A page stored before a rule
existed is served without asking it. `exp:velocity deploy` does this;
otherwise run `php bin/php/ezcache.php --clear-id=content,exphttpcache --allow-root-user`
and `./console exp:velocity cache clear`.

The content **view cache** (`ViewCaching`) stores a node's rendered view, not
the response. It runs after the rules and is unaffected by them.


## 14. Exponential Velocity and persistent workers

- **The engine is kept per siteaccess** for the life of the worker:
  `requestrules.ini` is read once per siteaccess, rule providers run once, and
  handlers are created once. The context is new for every request.
- **Per-request state is reset by the kernel** at the start of every request
  (`$GLOBALS['ezpRequestRuleNoStore']`, `$GLOBALS['ezpRequestRuleDecision']`,
  the rewrite counter). One request's decision can never leak into the next.
- **`addRule()` is idempotent by name**, so code that adds a rule on every
  request does not pile rules up in a long-running worker.
- **A settings change reaches Velocity only after a restart.** Workers keep the
  INI they loaded. Deploy with `./console exp:velocity deploy --no-autoload --allow-root-user`,
  which clears the INI cache, restarts Velocity and clears the page caches, in
  that order. PHP-FPM reads settings again once the INI cache is cleared.
- **New condition, action or provider classes** need the extension autoloads
  regenerated (`exp:velocity deploy` does it), and kernel classes need
  `--kernel`.


## 15. Performance

Measured on a reference installation (PHP 8.5, 20 000 evaluations each, without the kernel;
the cost includes building the context):

| Rules | µs per request |
|---|---|
| none (the default) | 2.6 |
| the shipped rule, an alias it does not apply to | 9.7 |
| the shipped rule deciding (system URL, reads the node's alias) | 23.3 |
| 20 rules, none applies | 110.5 |

A rendered page costs about 280 ms on the same server, so even 20 rules are
0.04 % of it. A page served from a cache never reaches the rules at all.
The figures come from evaluating each case 20 000 times in a loop with a
prepared context, the way the unit tests build one.

Keep rules cheap by ordering conditions from cheap to expensive within a rule.
Every condition must match, and evaluation stops at the first one that does
not. Address, module, view and user conditions cost nothing; node conditions
load the node once.


## 16. Security notes

- **Rules add restrictions; policies grant access.** Never rely on a rule to
  protect data that a policy allows. The rule may be switched off, listed in
  the wrong siteaccess, or bypassed by a view nobody thought of (section 17).
  Protect data with `content/read` limitations; use rules for *how* allowed
  content may be reached.
- **Fails safe for the site, loud for the operator.** An unknown condition
  never matches and an unknown action never decides, so a typo cannot take
  the site down. Run `--check` to catch typos.
- **Headers and client addresses come from the client.** `header:*` is a
  convenience, never authentication. `ip` is only as trustworthy as
  `ClientIpByCustomHTTPHeader`: set it only behind a proxy that overwrites
  that header.
- **Redirects stay on allowed hosts.** An absolute target on another host must
  be in `AllowedRedirectHosts[]`; the kernel refuses anything else.
  Placeholders can carry what the visitor sent (`{typed_uri}`, `{host}`,
  `{header:...}`); the allowed-hosts check applies to the result all the same,
  so a target built from them cannot leave your hosts.
- **`notfound` gives nothing away.** It is the same page as for a node that
  does not exist. Use it rather than `forbidden` when the existence of a page
  is itself confidential.
- **No answer depending on who asked is cached** (section 13).


## 17. What the rules do not see

- **Addresses that never reach a module view:**
  - a module that does not exist ("not found" before any rule);
  - a moved URL alias (the kernel redirects to the new address first);
  - views switched off by `[SiteAccessRules]`;
  - views the policies refuse.

  To catch addresses that do not exist, use URL wildcards.
- **Pages served from a page cache.** By design, as section 13 explains.
- **The query string.** No fact holds it (use a fact provider if you need
  it). User parameters (`/(offset)/10`) are facts.
- **Other entry points:** the REST API kernel, `index_treemenu.php`, static
  files and image aliases served by the web server, and CLI scripts.
- **Templates.** A node embedded in another page (`node_view_gui`, `fetch`)
  is not a request; `content/read` governs it.


## 18. Compatibility and upgrading

- **Nothing changes until a rule is listed.** `RuleList[]` is empty in
  `settings/requestrules.ini`; the three shipped rules are defined and off.
- **`[SiteAccessRules]` keeps working exactly as before**, and is checked before
  the request rules (section 3, step 3).
- **`PolicyOmitList[]` views are asked too.** The rules run for every module
  view that runs, whether its policy is checked or omitted.
- **The policy function `content/view_system_url`** appears in the role editor;
  existing roles are unaffected. Roles with `content/*` include it.
- **Class names start with `ezp`**, as the kernel's other private classes do;
  they are stable API from 6.0.15 on: `ezpRequestRuleEngine`,
  `ezpRequestRule`, `ezpRequestRuleResult`, `ezpRequestContext`,
  `ezpRequestRuleCondition`, `ezpRequestRuleStableCondition`,
  `ezpRequestRuleAction`, `ezpRequestRuleProvider`,
  `ezpRequestFactProvider`, `ezpRequestFactCondition`, `ezpRequestRuleKernel`.
  Built-in condition and action classes may gain siblings; their names and
  behaviour stay.
- **The template-redirect workaround can go.** Remove a redirect you put in
  `full.tpl` for this purpose once the rule is on.


## 19. Troubleshooting

| Symptom | Cause | Fix |
|---|---|---|
| The rule does nothing | not in `RuleList[]` for this siteaccess; INI cache; Velocity not restarted; page served from a cache stored before the rule | `--list -s <sa>`; `exp:velocity deploy --no-autoload`; clear `content,exphttpcache` |
| `--uri` says DECIDES, the browser says 200 | the browser or a cache kept an older answer | try `curl -sk -o /dev/null -w '%{http_code}'`; clear the page caches |
| The whole site answers 404 | a rule without a narrowing condition (`module_view` / `requested_via`), e.g. only `Conditions[policy]=!...` | `--uri=` on an alias shows which rule decides; narrow it |
| Editors are redirected away from system URLs on the public site | their role lacks `content/view_system_url` | grant it (section 8) |
| `ERR_TOO_MANY_REDIRECTS` | a `redirect` whose target matches the same rule (for example `to` the same address) | add a condition the target does not meet, or use `redirect_to_alias`, which never redirects to itself |
| Rewrites end in 404 | the rewritten address matched again; `MaxRewrites` reached | exclude the target (`Conditions[node]=!555`) |
| Pages of one section are no longer cached | a rule there uses `header`, `ip`, `user_id`, `user_login`, `group`, `fact` or a custom condition without the stable mark | expected (section 13); move it to a stable condition if you can |
| `--check`: "is not a class implementing" | the class is missing from the autoloads, or implements the wrong interface | `php bin/php/ezpgenerateautoloads.php -e` |
| A decision is not in `var/log/requestrules.log` | only `Action=log` writes there | enable debug output, or add a log rule in front |


## 20. Files

| File | What |
|---|---|
| `settings/requestrules.ini` | settings, the three shipped rules (off) |
| `kernel/private/classes/requestrules/ezprequestruleengine.php` | the engine: rules, handlers, `evaluate()`, `explain()`, `validate()` |
| `kernel/private/classes/requestrules/ezprequestrule.php` | one rule; parsing of keys and values |
| `kernel/private/classes/requestrules/ezprequestcontext.php` | the facts of a request, lazily resolved |
| `kernel/private/classes/requestrules/ezprequestruleresult.php` | the decisions |
| `kernel/private/classes/requestrules/ezprequestruleinterfaces.php` | `ezpRequestRuleCondition`, `ezpRequestRuleStableCondition`, `ezpRequestRuleAction`, `ezpRequestRuleProvider`, `ezpRequestFactProvider` |
| `kernel/private/classes/requestrules/ezprequestruleconditions.php` | the built-in conditions |
| `kernel/private/classes/requestrules/ezprequestruleactions.php` | the built-in actions |
| `kernel/private/classes/requestrules/ezprequestrulekernel.php` | the kernel side: facts of a real request, the response for a decision |
| `kernel/private/classes/ezpkernelweb.php` | records how the request was reached; asks the rules before the view runs |
| `kernel/private/classes/httpcache/ezphttpcachelistener.php` | does not store a page the rules keep out of shared caches |
| `kernel/content/module.php` | the policy function `content/view_system_url` |
| `bin/php/ezrequestrules.php` | list, check, explain |
| `tests/tests/kernel/classes/requestrules/ezpRequestRuleEngineTest.php` | 21 unit tests |

## Related pages

- [Request rules (feature)](../../features/6.0/request-rules.md)
- [Behaviour changes of 1 and 2 October 2026](behaviour-changes-2026-10.md)
- [HTTP cache](httpcache.md)
- [Security hardening](hardening.md)
- [Security and audit guide](../../guides/security-and-audit.md)
