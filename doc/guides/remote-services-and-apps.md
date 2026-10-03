# Remote services and apps: drive the admin from scripts, apps and portals

This guide is for developers who want to drive Exponential from scripts, apps or another front end. In about 20 minutes you call Exponential as an HTTP API from the shell and from Python, create a personal API token for a script, browse the service catalogue, and open a ready-made portal front end that runs on the same services. No build step, no extra server.

`expservices` turns the admin's functions into over a thousand named services (content, users, roles, caches, cronjobs, shop, feeds, media, tags, layouts, the audit and more), served by the `ezjscore` call interface under `/ezjscore/call/`. A service is called by name, `exp<domain>::<method>`, and answers JSON.

You need: an Exponential 6.0.15 installation with the extension `expservices` active (it is part of the default distribution; check with step 1), `curl`, and for step 5 an account that may create tokens. The examples use `https://example.com`.

## 1. Check that it answers (1 minute)

```bash
curl -s 'https://example.com/ezjscore/call/expsession::whoami?ContentType=json'
```

Expected: an envelope that says you are anonymous:

```
{"error_text":"","content":{"ok":true,"data":{"id":10,"login":null,"name":"Anonymous User",...,"anonymous":true,"groups":[]},"meta":[]}}
```

The outer `error_text` and `content` come from the `ezjscore` call interface; the shell and Python clients unwrap them. Inside `content` every answer has one shape: `{"ok":true,"data":...,"meta":{...}}`, or `{"ok":false,"error":{"code":403,"message":"..."}}`. Lists are paged and `meta` carries `total`, `offset`, `limit`, `count` and `has_more`.

If you get 403 on every call, the master switch is off: `./console exp:ini get expservices.ini/Services/Enabled --allow-root-user` must say `enabled`. If the extension is missing, add `ActiveExtensions[]=expservices` in `settings/override/site.ini.append.php`, then run `php bin/php/ezpgenerateautoloads.php -e` and `php bin/php/ezcache.php --clear-all --allow-root-user`.

## 2. Use the shell client (3 minutes)

The extension ships a client in bash and curl, `extension/expservices/bin/expservices-client.sh` (it needs `jq` or `python3` to print JSON).

```bash
export EXPSERVICES_URL=https://example.com
cd extension/expservices/bin
./expservices-client.sh whoami
./expservices-client.sh call expsystem::version
```

Expected for the second call:

```
{
  "version": "6.0.15stable",
  "major": 6,
  "minor": 0,
  "release": 15,
  "state": "stable",
  "php": "8.5.11"
}
```

Read a node and the children of the content root (arguments follow the service name and are joined with `::`; for `children` they are node id, sort, order, limit, offset):

```bash
./expservices-client.sh call expnode::get 2
./expservices-client.sh call expnode::children 2 name asc 5 0
./expservices-client.sh --meta call expnode::children 2 name asc 5 0 >/dev/null   # prints total, offset, limit, has_more
```

Exit status: 0 ok, 1 service error (`error 401: ...` on stderr), 2 transport fault.

## 3. Find the service you need (3 minutes)

The catalogue lists every service with its summary, access rule, arguments and an example call.

```bash
./expservices-client.sh catalog                 # everything, about 1100 entries
./expservices-client.sh catalog session         # one domain
./expservices-client.sh catalog node | grep -E '"method"|"summary"'
```

Each entry has `access`: `public`, `user` (signed in) or `[module, function]` (a policy, the same ones the admin checks), and `write`: true for services that change something. In the admin, **Setup > RAD** (`/setup/rad`) lists the same catalogue next to the other extension points. Per-domain tables are in the reference: [Backend services over ezjscore](../bc/6.0/backend_ezjscore_services.md).

Rules that hold for every service: 401 without login, 403 without the policy; a write needs POST and a form token and records `service.<domain>.<method>` in the audit; large operations are routed to [content jobs](../features/6.0/content-jobs.md).

## 4. Sign in and make a write (5 minutes)

Services that read content marked public work anonymously; anything else needs a user. Sign in once (the client keeps the session cookie in `~/.cache/expservices/cookies.txt`):

```bash
./expservices-client.sh login YOUR_LOGIN        # asks for the password; or set EXPSERVICES_PASSWORD
./expservices-client.sh whoami
```

Expected: your user, no longer anonymous. Writes go through `post`, which fetches the form token and sends it as field and header; arguments are `name=value` pairs:

```bash
./expservices-client.sh catalog cache | grep -E '"method"|"write"'     # find a write
./expservices-client.sh post expsession::tokenCreate name='my first script' expires_in_days=30
```

A failing policy answers `error 403: ...`, a bad argument `error 422` or `error 400` with the reason.

## 5. A personal API token for scripts and apps (3 minutes)

A session cookie expires and needs a form token for every write. A token does not. The `tokenCreate` call above answers once with the token (`expt_` followed by 48 hex characters); only its SHA-256 hash is stored, so copy it now.

```bash
export EXPSERVICES_TOKEN=expt_PASTE_THE_TOKEN_HERE
./expservices-client.sh whoami                  # signed in as the token's user, no login, no cookie
```

The token acts as its user with the same policies, for one request at a time. Writes still need POST but no form token. It is sent as `Authorization: Bearer <token>`, or as `X-Exp-Token: <token>` where a proxy does not pass the first header to PHP (the shipped clients send both). Manage tokens with `expsession::tokenList` (id, name, hint, created, last used, expiry, never the token) and `expsession::tokenRevoke` (POST `id`):

```bash
./expservices-client.sh call expsession::tokenList
./expservices-client.sh post expsession::tokenRevoke id=7
```

An unknown, revoked or expired token, or one of a disabled user, answers 401 on every service so a client notices that its token died; a token cannot create tokens. Create, revoke and refusals are in the audit trail as `access.expservices.token.create`, `.revoke` and `.failed`:

```bash
./console exp:audit tail --channel=access --name='access.expservices.*' --lines=5 --allow-root-user
```

Store the token in your platform's secret store (keychain, keystore, a CI secret), never in source code or a URL.

If `tokenCreate` answers a database error on an existing installation, the table `expservices_token` is missing; create it from `extension/expservices/sql/<engine>/schema.sql` (`mysql`, `postgresql` or `sqlite`) with your database client.

## 6. The same from Python (2 minutes)

The Python client uses only the standard library:

```bash
python3 extension/expservices/bin/expservices_client.py --url https://example.com call expsystem::version
```

As a module:

```python
import sys
sys.path.insert(0, "extension/expservices/bin")
from expservices_client import Client, ServiceError, TransportError

c = Client("https://example.com", api_token="expt_PASTE_THE_TOKEN_HERE")
print(c.call("expsystem::version")["version"])          # 6.0.15stable
try:
    print(c.call("expnode::get", 2)["name"])
except ServiceError as e:                                # the envelope's error: 401 login, 403 policy, 422 data
    print(e.code, e)
except TransportError as e:                              # network or JSON fault
    print("retry later:", e)
```

Clients for Kotlin and Android, Swift and iOS, Objective-C, Qt and GTK are written out in the reference: [clients chapter](../bc/6.0/backend_ezjscore_services.md#clients).

## 7. Open a portal built on the services (3 minutes)

Four sample front ends live in `extension/expservices/design/` and render news, shop, forums, media, feeds, search and login in the browser from the services alone:

| Design | Siteaccess | Address | What it is |
|---|---|---|---|
| `expportal_jquery` | `portaljq` | `/portaljq/` | The reference: small modules, jQuery 4, one script per feature |
| `expportal_reactive` | `portalreactive` | `/portalreactive/` | The same features with components, a store and one-way data flow |
| `expportal_react` | `portalreact` | `/portalreact/` | React 18 with React Bootstrap, hash routes |
| `expportal_wireframe` | `portalwireframe` | `/portalwireframe/` | Grey-box screens that name the service feeding each block |

If your installation has those siteaccesses (check `ls settings/siteaccess`), open `https://example.com/portaljq/` and click through News, Shop and Forums. Expected: pages render without a build step; the browser's network tab shows calls to `/ezjscore/call/exp...`.

To add one, create `settings/siteaccess/portaljq/site.ini.append.php` with `[DesignSettings] SiteDesign=expportal_jquery`, `[SiteSettings] DefaultPage=user/login` and `IndexPage=user/login` (the module answers 200 to anonymous visitors and the pagelayout replaces its output) and `[SiteAccessSettings] RequireUserLogin=false`, then register the siteaccess and clear the INI cache:

```bash
./console exp:ini add 'site.ini/SiteAccessSettings/AvailableSiteAccessList[]' portaljq override --allow-root-user
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
```

Reverse it with `exp:ini rem` for the same entry and by removing the directory. A Velocity server needs a restart to see a new siteaccess; Apache with PHP-FPM does not.

Point the portals at your content roots in `extension/expservices/settings/expportal.ini` block `[Portal]`: `NewsNode`, `ShopNode`, `ForumsNode` (default `2`), `MediaNode` (default `43`), `PageSize` (default `12`), `SiteTitle`.

## Settings

| File | Block | Key | Default | Scope |
|---|---|---|---|---|
| `expservices.ini` | `Services` | `Enabled` | `enabled` (`disabled`: every service answers 403) | installation |
| `expservices.ini` | `Paging` | `DefaultLimit`, `MaxLimit` | `25`, `200` | installation |
| `expservices.ini` | `Writes` | `RequireToken` | `enabled` (switch off for tests only) | installation |
| `expservices.ini` | `Writes` | `Audit` | `enabled` | installation |
| `expservices.ini` | `Community` | `ForumClass`, `ForumContainerClass`, `TopicClass`, `ReplyClass`, `CommentClass`, `PollClass`, `ReviewClass`, `TopicStickyAttribute` | `forum`, `forums`, `forum_topic`, `forum_reply`, `comment`, `poll`, `review`, `sticky` | installation |

Change one with `./console exp:ini set expservices.ini/Paging/DefaultLimit 50 override --allow-root-user` and clear the INI cache.

## Related pages

- Write a service of your own: one class extending `expServiceBase`, one `[ezjscServer_exp<domain>]` block, and it appears in the catalogue and in Setup > RAD. See [remote services (feature)](../features/6.0/remote-services-expservices.md) and [the services specification](../specifications/6.0/expservices.md).
- Secure what you expose: roles, policies, audit and debug: [security and audit](security-and-audit.md).
- Everything about the call interface, domain by domain: [Backend services over ezjscore](../bc/6.0/backend_ezjscore_services.md); the extension that carries it: [ezjscore](../features/6.0/extensions/ezjscore.md).
- How it came about: [October 2026 chronicle](../history/2026/2026-10.md), [6.0.15 changelog](../changelogs/6.0/6.0.15.md).
- Other guides: [Extensions](extensions.md) (build the extension that carries your service), [Glossary](../glossary.md).
