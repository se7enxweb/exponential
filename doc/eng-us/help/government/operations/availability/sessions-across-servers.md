---
title: Sessions across servers
audience: government, hosting, admin
category: public-sector
order: 110
summary: For IT teams running an Exponential site on several web servers. Where sessions are kept (PHP files or the database), how to keep editors signed in when a load balancer moves them between servers, what a failed node means for signed-in staff, and the session settings that matter.
keywords: sessions, session handler, ezpSessionHandlerDB, ezpSessionHandlerPHP, sticky sessions, session affinity, CookieAlwaysAddToHttpResponse, SessionTimeout, Setup > Sessions, several web servers, signed-in editors
type: help
---
# Sessions across servers

This page is for infrastructure teams in public bodies that run an Exponential site on more than one web server. It
answers where sessions are kept, the two ways to keep signed-in editors signed in across servers, what happens to them
when a server fails, and which session settings matter. The references are
[Sessions](../../../../guides/sessions.md) and [Several web nodes](../../../hosting/operations/several-web-nodes.md).

## Contents

1. [Where sessions are kept](#where-sessions-are-kept)
2. [Two ways to work across servers](#two-ways-to-work-across-servers)
3. [What a failed server means](#what-a-failed-server-means)
4. [Settings that matter](#settings-that-matter)
5. [Checklist](#checklist)

## Where sessions are kept

A session keeps one browser signed in. `site.ini [Session] Handler` decides where it is kept:

| Handler | Where | Several servers |
|---|---|---|
| empty (default): `ezpSessionHandlerPHP` | PHP's own session storage, usually files on the server | each server knows only its own sessions |
| `ezpSessionHandlerDB` | the `ezsession` table of the site's database | every server sees every session |

Anonymous visitors usually have no session at all; this matters for editors and other signed-in users.

## Two ways to work across servers

**Sessions in the database.** Set the handler in `settings/override/site.ini.append.php`:

```ini
[Session]
Handler=ezpSessionHandlerDB
```

Every node reads the same table, so the load balancer may send a user to any node. A further benefit for public
bodies: **Setup > Sessions** can then list and remove sessions, for example to sign out a suspected compromised
account at once ([Sessions](../../../../guides/sessions.md)). With the default handler that page cannot list or remove
sessions one by one; it shows who signed in recently instead.

**Keep each user on one node.** A balancer that routes by the session cookie keeps a user on the node that holds the
session. `[Session] CookieAlwaysAddToHttpResponse=enabled` sends the session cookie with every response once a session
runs, for such a balancer; those responses go out with `Cache-Control: private`
([Security and audit](../../../../guides/security-and-audit.md)).

| | Sessions in the database | Session affinity |
|---|---|---|
| Balancer configuration | none | routing by cookie |
| A node fails | users stay signed in | its users are signed out |
| Setup > Sessions lists and removes sessions | yes | no |
| Load on the database | one more table in use | none extra |

## What a failed server means

With affinity, a failed node signs its users out: editors lose unsaved form input of the moment, but drafts that were
stored stay in the database ([My drafts and my pending items](../../../../guides/drafts-and-pending.md)). With sessions
in the database, the next request goes to another node and the user carries on. For a site where editors must keep
working during an incident, choose the database handler.

## Settings that matter

| Setting (`site.ini [Session]`) | Default | Meaning |
|---|---|---|
| `SessionTimeout` | `259200` (three days) | a session ends this many seconds after its last use |
| `CookieSecure` | `auto` | `Secure` cookie over HTTPS; behind a balancer that terminates TLS it needs `X-Forwarded-Proto` from a trusted proxy |
| `CookieHttponly` | `true` | the cookie is not readable by scripts |
| `CookieSameSite` | `Lax` | not sent with cross-site requests |

A shorter `SessionTimeout` for the administration may be a requirement of your security policy; set it in the
administration siteaccess's settings. Logging out ends the session for good under every handler, and a password
change ends other sessions (`[PasswordSettings] EndOtherSessions=enabled`)
([Security standards](../../../../standards/security.md)).

## Checklist

- [ ] With several servers: `ezpSessionHandlerDB`, or session affinity agreed with the network team.
- [ ] The trade-off for a failed node is documented.
- [ ] `TrustedProxies[]` lists the balancer, so the `Secure` flag is right behind TLS termination.
- [ ] `SessionTimeout` matches your security policy.

## Related pages

- [Availability](README.md) index of this folder
- [Several web servers behind a load balancer](several-web-servers-behind-a-load-balancer.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
