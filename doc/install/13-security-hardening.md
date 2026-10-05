# 13. Security hardening for production

A default Exponential 6.0 installation is built to be safe without extra work: the rewrite rules hand out only listed
files, security headers go out on every page, the session cookie is `HttpOnly` and `SameSite`, debug output is off,
forms carry a token against cross-site request forgery and an audit log records who did what. This chapter walks
through each of those layers, says which setting controls it and how to check it, and lists what an administrator
should still decide before a site goes live: secrets in `settings/override`, the administration siteaccess, password
rules, HTTPS-only cookies, file permissions, mail consent and keeping the installation up to date. It is written as a
checklist you can work through from top to bottom; a one-page summary is at the end.

[Previous: 12. Troubleshooting](12-troubleshooting.md) ·
[Contents](README.md)

---

## Contents of this chapter

1. [The layers at a glance](#131-the-layers-at-a-glance)
2. [What the web server must never hand out](#132-what-the-web-server-must-never-hand-out)
3. [Secrets in settings/override](#133-secrets-in-settingsoverride)
4. [Debug output and error display](#134-debug-output-and-error-display)
5. [The administration siteaccess](#135-the-administration-siteaccess)
6. [Passwords and sign-in](#136-passwords-and-sign-in)
7. [Sessions and cookies](#137-sessions-and-cookies)
8. [Form tokens against cross-site request forgery](#138-form-tokens-against-cross-site-request-forgery)
9. [Security headers and HTTPS](#139-security-headers-and-https)
10. [Behind a proxy: trusted headers](#1310-behind-a-proxy-trusted-headers)
11. [File permissions and ownership](#1311-file-permissions-and-ownership)
12. [Velocity-specific hardening](#1312-velocity-specific-hardening)
13. [Mail: consent and suppression](#1313-mail-consent-and-suppression)
14. [The audit log](#1314-the-audit-log)
15. [Keeping up to date](#1315-keeping-up-to-date)
16. [Go-live checklist](#1316-go-live-checklist)
17. [References](#1317-references)

Settings are changed in overrides, never in the shipped files: `settings/override/<file>.ini.append.php` for the whole
installation, `settings/siteaccess/<siteaccess>/<file>.ini.append.php` for one siteaccess. Clear the INI cache after a
change (`php bin/php/ezcache.php --clear-tag=ini`), and under Velocity restart the server or run
`exp:velocity deploy` ([chapter 8](08-serving-the-site.md#8314-deploying-a-change-expvelocity-deploy)).

---

## 13.1 The layers at a glance

```
  visitor
     |
  [ TLS, HSTS ]                        Velocity [HTTPSSettings] / Apache mod_ssl / nginx ssl     13.9
     |
  [ what may be served as a file ]     .htaccess_root / Velocity's list / derived nginx block   13.2
     |
  [ security headers ]                 site.ini [HTTPHeaderSettings] SecurityHeaders[],          13.9
     |                                 velocity.ini ResponseHeaders[]
  [ request rules, siteaccess login ]  requestrules.ini, [SiteAccessSettings] RequireUserLogin   13.5
     |
  [ session cookie, form token ]       site.ini [Session], ezformtoken                          13.7, 13.8
     |
  [ roles and policies ]               Roles and policies in the administration                 13.5
     |
  [ audit ]                            audit.ini, exp:audit                                      13.14
     |
  files on disk: owner, group, modes   [FileSettings] *Permissions, chown/chmod                 13.11
```

| Layer | Default | What you decide |
|---|---|---|
| Rewrite rules | strict list, PHP below the root refused | keep them; merge new rules after upgrades |
| Secrets | generated into `settings/override` | file modes, backups, never in version control |
| Debug output | off | keep it off, or limit it by IP |
| Admin siteaccess | sign-in required | own host name, network restriction, request rules |
| Passwords | 10 characters, bcrypt | character rules, lockout after failed sign-ins |
| Session cookie | `HttpOnly`, `SameSite=Lax`, `Secure` over HTTPS | `CookieSecure=true` on an HTTPS-only site |
| Form tokens | active (setup wizard) | keep `ezformtoken` active; adapt custom AJAX |
| Security headers | five headers on every page | HSTS once HTTPS-only; frame-ancestors for editors |
| Audit | on | cron, alert recipients, key backup |

---

## 13.2 What the web server must never hand out

The document root of Exponential is the installation root, so `settings/`, `var/`, `vendor/`, the kernel sources and
the SQLite database lie inside it. Safety comes from the routing, not from the directory layout: **only a fixed list
of asset paths is served as files; everything else is passed to `index.php`.** The list is `.htaccess_root`, and
Velocity's three engines implement the same list (chapter 8, section 8.2).

What the shipped rules guarantee (`.htaccess_root` and `doc/specifications/6.0/security-defaults-2026-09.md`,
section 4):

| Request | Answer | Rule |
|---|---|---|
| a dot file or directory anywhere: `/.git/config`, `/.env`, `/extension/x/.htaccess` | 404 | `RewriteRule (^|/)\.(?!well-known(/|$)) - [R=404,L]` |
| `/.well-known/...` | reachable (ACME challenges) | the same rule's exception |
| an existing `.php`, `.phtml`, `.phar` below the root: `/extension/x/foo.php`, `/var/storage/packages/.../x.php` | 404 | `RewriteRule ^.+/[^/]+\.(php[0-9]?|phtml|phar)$ - [R=404,L]` |
| `/settings/site.ini`, `/var/site/storage/original/application/x.pdf`, `/composer.json` | handled by `index.php` (a page or 404), never the file | not on the list |
| package store `var/storage/packages/` | only preview images (png, jpg, gif, webp; not SVG, which can carry script) | `^var/storage/packages/.+\.(png|jpe?g|gif|webp)$` |
| stored originals | only image files below `storage/original/image`; other uploads only through `content/download`, which checks permissions | `^var/([^/]+/)?storage/original/image/.+\.(png|jpe?g|gif|webp|svg)$` |

What you do:

1. **Apache:** `cp .htaccess_root .htaccess` and leave the security rules in it. Do not delete rules to make
   something work; find the cause instead. If you keep your own `.htaccess` or virtual host rules, compare them with
   `.htaccess_root` after every upgrade and merge new rules.
2. **nginx:** use the derived server block of [chapter 8, section 8.6](08-serving-the-site.md#86-nginx-with-php-fpm)
   or an equivalent that keeps the first two rules (dot files, scripts below the root) ahead of every static location.
3. **Velocity:** nothing to do; `FollowSymlinks=disabled` (the default) also refuses files reached through a symbolic
   link that leads out of the document root.
4. **Never serve the installation with a generic configuration** such as nginx's "serve the file if it exists, else
   `index.php`" or Caddy's `php_server`: those hand out every file that exists, including `settings/*.ini` and the
   SQLite database.

Check from outside:

```bash
for p in /.git/config /.env /settings/site.ini /composer.json /kernel/classes/ezini.php /var/log/error.log; do
  printf '%-32s %s\n' "$p" "$(curl -s -o /dev/null -w '%{http_code}' https://example.com$p)"
done
```

Each line must show `404` (or `200` with Exponential's page, never the file's content). For the `.ini` path, look at the
body once: it must be an HTML page, not INI text.

---

## 13.3 Secrets in settings/override

Several files in `settings/override/` hold secrets. They are written by the installer or generated on first use:

| File | Secret | Consequence if lost or leaked |
|---|---|---|
| `site.ini.append.php` | database user and password (`[DatabaseSettings]`) | database access |
| `audit.ini.append.php` | audit signing key and pseudonym key | archives signed with the key can no longer be verified; pseudonyms can be reversed |
| `mailpreferences.ini.append.php` | the mail preferences site secret | every unsubscribe and preference link stops working, every suppression entry stops matching |
| `velocity.ini.append.php` | `[DashboardSettings] Token` when set | remote access to the server's figures and `/Q/phpinfo` |
| `config.env.php` in the root | machine settings such as the environment name | (git-ignored) |
| `var/log/initial-admin-password` | the generated first administrator password of a kickstart or console install | the administrator account |

What you do:

1. **Change the generated administrator password** and delete `var/log/initial-admin-password` afterwards; the console
   installer says so when it prints the password.
2. **Never commit `settings/override/`** to version control and never copy it into a public place. Keep the files
   readable by the site's user only where nothing else needs them (`chmod 640`, owner the site's user or root, group
   the site's group).
3. **Back them up**, as secrets, together with the database: without the audit and mail preference files, old
   archives and links stop working.
4. **Copy them with every move** of the installation to another machine.
5. The administration and `exp:ini` mask secret values when they show settings; still, give `setup/setup` (settings
   views) only to administrators.

---

## 13.4 Debug output and error display

Debug output shows SQL, template names, file paths and timing: useful in development, a gift to an attacker in
production.

`settings/site.ini [DebugSettings]` as shipped:

```ini
[DebugSettings]
DebugOutput=disabled        # master switch: no debug report on pages
AlwaysLog[]=error           # errors are still written to var/<site>/log/error.log
ScriptDebugOutput=disabled  # command-line scripts
DebugByIP=disabled          # when enabled: only the addresses in DebugIPList[] get debug
DebugIPList[]
DebugByUser=disabled        # when enabled: only the users in DebugUserIDList[]
DebugRedirection=disabled
DisplayDebugWarnings=disabled
```

and `[TemplateSettings] Debug=disabled` (no template names in the HTML).

For production keep `DebugOutput=disabled`. If you must look at a live problem, restrict debug to your own address
and remove the entry again:

```ini
# settings/override/site.ini.append.php
[DebugSettings]
DebugOutput=enabled
DebugByIP=enabled
DebugIPList[]
DebugIPList[]=203.0.113.10
DebugIPList[]=2001:db8::/48
```

Entries may be single addresses or CIDR ranges of both families; a plain IPv6 address means exactly that address
(see [the debug bar](../bc/6.0/debug-bar.md), which also documents labels and expiry dates on entries). The debug bar's
Settings tab can change these values; it needs the `setup/setup` policy.

`DebugByIP` trusts the address Exponential determines for the visitor. If `ClientIpByCustomHTTPHeader` is set
(section 13.10) and the proxy does not overwrite that header, a visitor can claim any address, including one in your
list.

**PHP itself:** in the `php.ini` of PHP-FPM set `display_errors=Off`, `log_errors=On` and `expose_php=Off`.
FrankenPHP reads no machine `php.ini`; Velocity ships `display_errors=Off` and `log_errors=On` for it in
`[FrankenPHPSettings] IniOptions[]`. The `qbix` engine runs command-line PHP with the machine's CLI `php.ini` plus
`[PHPSettings] IniOptions[]`; add the same there if your CLI `php.ini` displays errors:

```ini
# settings/override/velocity.ini.append.php
[PHPSettings]
IniOptions[]=display_errors=Off
IniOptions[]=log_errors=On
```

(Without an empty `IniOptions[]` line first, these are added to the shipped list.) The engine's own error pages show
an uncaught error's file, line and trace only with its `debug` option, which is off by default.

---

## 13.5 The administration siteaccess

The administration is a siteaccess of its own (usually `admin`, or the name chosen at install time). The installer
writes `[SiteAccessSettings] RequireUserLogin=true` into its `site.ini.append.php`, so every page of it asks for a
sign-in, and access to each function is decided by the role system (`setup/*`, `content/*`, `audit/*` and so on).

Hardening beyond that, from the strongest to the simplest:

1. **Give it its own host name** (for example `admin.example.com`, matched by `[SiteAccessSettings] HostMatchType`)
   rather than a path below the public site, so it can be restricted as a unit.
2. **Restrict the network.** With Apache, on the administration's virtual host:

   ```apache
   <Location />
       Require ip 203.0.113.0/24 2001:db8::/48
   </Location>
   ```

   or HTTP basic authentication in front of it (`AuthType Basic` with `mod_auth_basic`). With nginx: `allow` and
   `deny`. Velocity has no HTTP authentication of its own; restrict the administration host with the firewall, a VPN
   or a reverse proxy in front, or keep it on a separate Velocity bound to an internal address.
3. **Request rules** (`settings/requestrules.ini`) decide before a module view runs: keep anonymous visitors off system
   URLs such as `/content/view/full/123`, require a sign-in for a section, refuse requests from a network. They are
   tied to role policies, so exemptions are made in the role editor. See
   [Request rules](../bc/6.0/view_full_security.md).
4. **Least privilege.** Give editors the roles they need, not Administrator. Policies that matter for security:
   `setup/setup` (settings), `setup/managecache`, `audit/read` and `audit/manage`, `mailpreferences/administrate` and
   `mailpreferences/export`, `user/login` per siteaccess.
5. **The setup wizard must stay closed.** After installation `settings/override/site.ini.append.php` carries
   `[SiteAccessSettings] CheckValidity=false`. While it is `true` (the shipped default), every request is sent to the
   setup wizard. Never set it back on a live site.

---

## 13.6 Passwords and sign-in

### Length and character rules

`settings/site.ini`:

```ini
[UserSettings]
MinPasswordLength=10          # bytes; 3 when the setting is missing
GeneratePasswordLength=16
HashType=php_default          # bcrypt today; never plaintext or the md5_* types
UpdateHash=true               # re-hash older hashes at the next sign-in

[PasswordSettings]            # every rule off by default
RequireLowercase=disabled
RequireUppercase=disabled
RequireDigit=disabled
RequireSymbol=disabled
MinCharacterClasses=0         # at least this many of the four kinds above
ForbidLogin=disabled          # must not contain the login name
ForbidCurrentPassword=disabled
EndOtherSessions=enabled      # after a change, sign the user's other sessions out
ChangeNotificationMail=enabled
StrengthMeter=enabled
GenerateButton=enabled
```

A reasonable production policy, in line with current guidance that favours length over composition rules:

```ini
# settings/override/site.ini.append.php
[UserSettings]
MinPasswordLength=12

[PasswordSettings]
ForbidLogin=enabled
ForbidCurrentPassword=enabled
MinCharacterClasses=2
```

The rules apply to every form that sets a password (`user/password`, registration, the user account in `content/edit`
and the administration, `bin/php/resetuserpassword.php`), checked by `expPasswordPolicy`
(`kernel/classes/exppasswordpolicy.php`).

**`EndOtherSessions=enabled`** keeps the stamp of the password in each session: after a change, every other session of
the user (other browsers, other devices, a stolen session) is signed out on its next request, whatever the session
handler. Keep it enabled. Accounts without a password of their own (LDAP, SSO) are not checked.

**`ChangeNotificationMail=enabled`** mails "your password was changed" to the account (category Account security,
which nobody can switch off; the mail never contains the password). It is how a user learns of a change they did not
make.

### Failed sign-ins

```ini
[UserSettings]
MaxNumberOfFailedLogin=0      # 0: no lockout
TrustedIPList[]               # addresses never locked out
ShowMessageIfExceeded=false
```

Set `MaxNumberOfFailedLogin` (for example `10`) to lock an account after that many failed attempts until an
administrator unlocks it. Weigh this: a lockout also lets anyone lock a known account. The audit's alert rules
`brute_force` (20 failures from one network in 5 minutes) and `brute_force_user` (10 for one account in 15 minutes)
report such attacks either way (section 13.14).

The sign-in itself does not reveal which accounts exist: an unknown name costs the same hash computation as a wrong
password, and "forgot password" gives the same answer for an unknown address as for a known one.

---

## 13.7 Sessions and cookies

`settings/site.ini [Session]`; the three cookie keys are commented out in the shipped file and their defaults apply
while they are unset:

| Key | Default | Meaning |
|---|---|---|
| `CookieSecure` | `auto` | `Secure` whenever the request itself came over HTTPS; `true` forces it |
| `CookieHttponly` | `true` | scripts cannot read the session cookie |
| `CookieSameSite` | `Lax` | `Lax`, `Strict`, `None` (only with `Secure`) or empty |
| `SessionTimeout` | `259200` | seconds a session lasts (3 days) |
| `RememberMeTimeout` | empty | the "Remember me" lifetime; empty disables it |
| `SessionNameHandler`, `SessionNamePerSiteAccess` | `default`, `enabled` | `custom` gives every siteaccess its own session cookie, so a sign-in on the public site is not a sign-in in the administration |

For a site that is served only over HTTPS:

```ini
# settings/override/site.ini.append.php
[Session]
CookieSecure=true
CookieHttponly=true
CookieSameSite=Lax
SessionTimeout=28800
```

`CookieSecure=true` makes sure the cookie is never sent over plain HTTP, even on a request that wrongly arrives
without TLS. Use `Strict` only if no other site links into signed-in pages. A shorter `SessionTimeout` limits how long
a stolen session stays useful.

Separate session cookies per siteaccess:

```ini
[Session]
SessionNameHandler=custom
SessionNamePrefix=eZSESSID
SessionNamePerSiteAccess=enabled
```

With `custom`, Velocity's response cache derives its "personal, do not cache" cookie from `SessionNamePrefix`
automatically.

In PHP's own configuration, `session.use_strict_mode=1` makes PHP refuse session ids it did not create.

---

## 13.8 Form tokens against cross-site request forgery

The `ezformtoken` extension adds a per-session token to every HTML form and checks it on every POST
(`extension/ezformtoken`, event listeners `request/input`, `response/output` and `session/regenerate`). A forged form
on another site cannot know the token. The setup wizard activates it on every new installation.

- **Keep it in `[ExtensionSettings] ActiveExtensions[]`.** Check: `php bin/php/console exp:ini where
  site.ini/ExtensionSettings/ActiveExtensions` (every file that sets it, and the value in effect) or Setup >
  Extensions.
- **Custom AJAX code** that sends POST requests must send the token too; the kernel's own scripts and ezjscore do.
  The extension's `README.rst` shows how.
- A refused token answers 403 with the "form expired" page, and no token is written to the log
  ([Form expired page](../features/6.0/form-expired-page.md)).
- A page carrying a token is sent `Cache-Control: private`, so no shared cache stores a signed-in page with its token.
  A reverse proxy must not override that (section 13.10).

---

## 13.9 Security headers and HTTPS

### Headers on every page

`settings/site.ini [HTTPHeaderSettings]`:

| Header | Default |
|---|---|
| `X-Content-Type-Options` | `nosniff` |
| `X-Frame-Options` | `SAMEORIGIN` |
| `Content-Security-Policy` | `frame-ancestors 'self'` |
| `Referrer-Policy` | `strict-origin-when-cross-origin` |
| `Permissions-Policy` | `camera=(), microphone=(), payment=(), usb=()` |
| `Strict-Transport-Security` | empty (off; only ever sent over HTTPS) |

An empty value in an override drops a header; header names and values that could split the response are ignored. To
let a trusted editor host frame the administration:

```ini
[HTTPHeaderSettings]
SecurityHeaders[Content-Security-Policy]=frame-ancestors 'self' https://edit.example.com
```

Pages answered from the kernel's HTTP cache carry the same headers.

**Velocity** sends four of them (all but the content security policy) on what it answers itself (static files, resized images, its own error pages,
redirects, 304 answers) through `[ServerSettings] ResponseHeaders[]`, and adds them to script answers that lack them
(`ResponseHeadersOnScripts=enabled`). **Apache and nginx** send nothing extra on static files by themselves; add them
there if you want them (Apache `mod_headers`: `Header always set X-Content-Type-Options "nosniff"`; nginx:
`add_header X-Content-Type-Options "nosniff" always;`).

A full content security policy (`script-src`, `style-src`) is not shipped: designs and extensions use inline scripts.
Build one per site with the browser's console and a report-only header before enforcing it.

### HTTPS everywhere

1. Serve every name of the site over HTTPS: Velocity `[HTTPSSettings]` or the engine's ACME support, Apache `mod_ssl`,
   nginx `ssl` ([chapter 8](08-serving-the-site.md#839-https-served-by-velocity-itself)).
2. Redirect plain HTTP to HTTPS (Apache and nginx: section 8.5 and 8.6). For individual subtrees Exponential has its
   own `[SSLZoneSettings]`.
3. Set `[Session] CookieSecure=true` (section 13.7).
4. Turn HSTS on, starting short and raising it once everything works:
   - Velocity: `[HTTPSSettings] HSTSMaxAge` (300 shipped), `HSTSIncludeSubDomains`, `HSTSPreload`;
   - Apache or nginx: `SecurityHeaders[Strict-Transport-Security]=max-age=31536000` in `site.ini`, sent by the kernel
     only over HTTPS.

   A browser remembers HSTS for the whole period and refuses plain HTTP to that host, even after you lower the value.
   Raise it to a year only when every name is served over HTTPS for good.
5. Keep certificates renewed and watched (`exp:velocity ssl show --json`, `daysLeft`; or your ACME client's timer).

Check:

```bash
curl -sI https://example.com/ | grep -i -E 'strict-transport|x-content-type|x-frame|referrer|permissions|content-security'
curl -sI https://example.com/user/login | grep -i set-cookie     # Secure; HttpOnly; SameSite=Lax
```

---

## 13.10 Behind a proxy: trusted headers

Exponential reads two pieces of information from request headers that a client can also send itself
(`lib/ezutils/classes/ezsys.php`):

- **`X-Forwarded-Proto: https`** (then `X-Forwarded-Port`, then `X-Forwarded-Server`) makes `eZSys::isSSLNow()` treat
  the request as HTTPS when the connection itself was not. This affects generated URLs and `CookieSecure=auto`. A proxy
  that terminates TLS must set the header; when no proxy is in front, the web server should remove any such header a
  client sends (Apache: `RequestHeader unset X-Forwarded-Proto`; nginx in front of PHP-FPM:
  `fastcgi_param HTTP_X_FORWARDED_PROTO "";`), or set `CookieSecure=true` so the cookie does not depend on it.
- **`[HTTPHeaderSettings] ClientIpByCustomHTTPHeader`** (default `false`). Set to `X-Forwarded-For`, Exponential takes
  the **first** (left-most) address of the header as the visitor's address, and that is the part a client controls.
  The address is used by `DebugByIP`, `[UserSettings] TrustedIPList` (exemption from the sign-in lockout), request
  rules by network, the audit log and the consent log. Set it only when the proxy **replaces** the header for every
  request (nginx `proxy_set_header X-Forwarded-For $remote_addr;`), and make sure the application server is reachable
  only through the proxy (Velocity `Host=127.0.0.1`, or a firewall).

Caching proxies: never let a proxy cache responses that set a cookie or carry `Cache-Control: private`. Purge tags are
not public by default (`httpcache.ini [HttpCacheSettings] TagHeader=disabled`); name the one header your purging proxy
reads only when you have one.

---

## 13.11 File permissions and ownership

Principles (the procedure is in [chapter 8, section 8.7](08-serving-the-site.md#87-file-permissions-and-ownership)):

- **One user writes.** PHP-FPM, Velocity workers, cronjobs and console scripts run as the site's user (or share one
  group). Never let workers run as root (`[ServerSettings] AllowRootWorkers=disabled`, the default).
- **Only what must be written is writable**: `var/` and `settings/` (and, while packages are installed from the
  administration, `design/` and `extension/`). The code (`kernel/`, `lib/`, `vendor/`, `index.php`) can belong to
  another user and be read-only for the site's user, so a compromised PHP process cannot change it.
- **No world-writable files.** The shipped `[FileSettings]` values are `0777` and `0666`; set the hardened values that
  `site.ini` recommends:

  ```ini
  # settings/override/site.ini.append.php
  [FileSettings]
  StorageDirPermissions=0770
  StorageFilePermissions=0660
  TemporaryPermissions=0770
  LogFilePermissions=0660
  ```

  and fix existing files once: `find var -type d -exec chmod 2770 {} +` and `find var -type f -exec chmod 0660 {} +`
  (with the site's group on everything).
- **Secrets** in `settings/override` (section 13.3): `0640` or stricter.
- **Logs** name visitors: Velocity writes its own with `0640` in a `0750` directory (`[LogSettings] FileMode`,
  `DirMode`), the response cache with the same modes.
- **Symbolic links** out of the document root are refused by Velocity (`FollowSymlinks=disabled`); with Apache use
  `Options SymLinksIfOwnerMatch` instead of `FollowSymLinks` if users other than the site's could place links.
- **`TemporaryDir`** (`/tmp/` shipped) is shared with every user of the machine; consider a directory of the site's
  own, for example below `var/`.

---

## 13.12 Velocity-specific hardening

| Topic | Setting or action |
|---|---|
| Bind address | `[ServerSettings] Host=127.0.0.1` unless Velocity faces visitors directly; the server has no HTTP authentication of its own |
| Worker user | `User`, `Group` set to the site's user; never root |
| Symbolic links | `FollowSymlinks=disabled` |
| Dashboard and figures | `[DashboardSettings] Token` empty and `Remote=disabled` keep `/Q/dashboard`, `/Q/stats`, `/Q/metrics` local; if you set a token, generate a long random one (`openssl rand -hex 24`) and keep it secret |
| `/Q/phpinfo` | local only; shows the process environment |
| Control panel `/Q/panel` | local only, password protected; set the password at once on a machine reachable from the internet; its store must not be writable by group or others |
| HTTPS | a CA certificate, HSTS raised only when everything is HTTPS (section 13.9) |
| Configuration files | `/etc/vc/sites-available/<site>.conf` is `0600` (certificate paths, token); keep it so |
| Response cache | personal pages are never cached; if you rename the session cookie, check `[CacheSettings] SkipCookies` stays derived or lists it |
| Updates | engine releases fix security issues too (section 13.15); `exp:velocity deploy` after an update |

---

## 13.13 Mail: consent and suppression

Since 6.0.15 every mail passes the **mail gate** (`expMailGate`) before it reaches the transport:

- **Essential** categories (`security`, `orders`, `legal`, `admin`) always go out.
- **Optional** categories go only to recipients who switched them on, with a footer, a one-click unsubscribe link and
  `List-Unsubscribe` headers; one mail per recipient, so nobody sees other addresses.
- Mail without a category is sent and counted in `var/<vardir>/log/mailgate.jsonl`, so you can find code that should
  declare one.

What to set up for production ([administrator's guide](../guides/mail-preferences-administrator.md)):

1. The tables exist: `php bin/php/console exp:mail:status` reports five times `ok`.
2. The footer names the sender: `mailpreferences.ini [FooterSettings] OrganisationName` and `OrganisationAddress`.
   Without them optional mail goes out without a postal address, which many jurisdictions require.
3. The site secret in `settings/override/mailpreferences.ini.append.php` is backed up (section 13.3).
4. **The suppression list** blocks optional mail to hard bounces, complaints, "stop all" requests and legal requests.
   Only a salted hash of each address is stored, so the list cannot be read. Manage it in Setup > E-mail preferences or
   on the console:

   ```bash
   php bin/php/console exp:mail:suppression check --email=<address>
   php bin/php/console exp:mail:suppression add --email=<address> --reason=legal --note="<no personal data>"
   php bin/php/console exp:mail:suppression lift --email=<address>
   ```

   Configure the bounce reader (`[BounceSettings]`) so hard bounces are added automatically.
5. **The consent log** records every change of a person's preferences with source and wording. It holds addresses and
   IP addresses: give `mailpreferences/export` to few people, delete exported CSV files when the request they answer is
   done, and let the `mailpreferences` cronjob part apply the retention (`[ConsentSettings] RetentionDays`, 1095 days).
6. Data protection requests: `exp:mail:preferences export --email=<address>` (written with mode `0600`) and
   `exp:mail:preferences erase --email=<address> --yes`.

---

## 13.14 The audit log

The audit records sign-ins and failed sign-ins, content moved, hidden or removed, role and setting changes, prices,
commands and cronjobs, and what the audit itself did. **It is on in every installation by default.** Each record is a
line of JSON in a file per channel and day (`var/<site>/log/audit/<channel>-<YYYY-MM-DD>.jsonl`), and each line carries
the hash of the line before it, so a changed, removed or inserted line shows.

```bash
php bin/php/console exp:audit status                           # enabled, directory, key, each channel intact or broken
php bin/php/console exp:audit verify                           # checks the hash chains
php bin/php/console exp:audit tail --channel=access --lines=20
```

For production:

1. **Run the cronjob part.** It is in the `frequent` group: `php runcronjobs.php frequent` every few minutes, as the
   site's user. The audit dashboard warns when it has not run for an hour.
2. **Decide who gets alert mail**: by default the site's `AdminEmail`; check with
   `php bin/php/console exp:audit alerts recipients`. The shipped rules include `brute_force`, `brute_force_user` and
   `admin_role_granted`.
3. **Back up `settings/override/audit.ini.append.php`** as a secret (signing and pseudonym keys).
4. **Give `audit/read` only to the roles that need it**; only the Administrator role holds `audit/read` and
   `audit/manage` by default.
5. **Privacy.** `[AuditPrivacySettings]` decides per field what is kept: addresses truncated to /24 and /48, the
   session hashed, e-mail addresses hashed, passwords and tokens never recorded, personal fields pseudonymised after
   `PseudonymiseAfterDays` (90). Adjust them to your data protection requirements; section 4.12 and 6.4 of the audit
   guide describe a strict profile.
6. **Forward it** to a central log or SIEM with a sink (`[AuditSink_syslog]`, `[AuditSink_webhook]`), so a compromised
   machine cannot silently rewrite its own history. Run `exp:audit verify` regularly.

Full guide: [Audit](../bc/6.0/audit.md).

---

## 13.15 Keeping up to date

- **Follow the releases** of Exponential and of every extension you use: they are Composer packages, so
  `composer outdated` lists what is newer. Read the changelog and the upgrade notes before updating
  ([chapter 11](11-upgrading.md)).
- **Update in a copy first**, then on the live site: `composer update <package>`, the database updates the release
  names, `php bin/php/console exp:velocity deploy` (or autoloads, cache clear and a PHP-FPM reload by hand).
- **Check file consistency** after an update: Setup > Upgrade check ("Check file consistency" compares the files with
  the release's `share/filelist.md5`; "Check database consistency" the schema). A modified kernel file you did not
  change deserves a look.
- **PHP, the database server, the operating system, Velocity's engine and FrankenPHP's binary** need updates as well.
  FrankenPHP's version and checksums are pinned in `[FrankenPHPSettings] Version` and `Sha256[]`; change them together.
- **Review your own extensions** for the patterns fixed in the kernel (SQL built from request data without
  `escapeString()` or integer casts, shell commands without `escapeshellarg()`, output without escaping); see
  [the 6.0.13 hardening notes](../bc/6.0/hardening.md). Extensions generated by the RAD wizards before their fix should
  be regenerated ([RAD tools: security](../bc/6.0/rad-security.md)).
- **Report a vulnerability** to `security@exponential.earth`, not to the public issue tracker.

---

## 13.16 Go-live checklist

- [ ] Rewrite rules: `.htaccess` is a current copy of `.htaccess_root` (or the derived nginx block, or Velocity);
      `/.git/config`, `/settings/site.ini`, `/composer.json` do not return the file.
- [ ] `settings/override/` not in version control, backed up as a secret, mode `0640` or stricter.
- [ ] The generated administrator password changed; `var/log/initial-admin-password` deleted.
- [ ] `[DebugSettings] DebugOutput=disabled`; PHP `display_errors=Off`.
- [ ] `[SiteAccessSettings] CheckValidity=false`; the administration on its own host, restricted by network or a second
      authentication; least-privilege roles.
- [ ] Password policy decided (`MinPasswordLength`, `[PasswordSettings]`); `EndOtherSessions=enabled`.
- [ ] `[Session] CookieSecure=true` on an HTTPS-only site; `HttpOnly` and `SameSite` left on.
- [ ] `ezformtoken` active.
- [ ] HTTPS on every name; HTTP redirected; HSTS raised once stable; certificate renewal watched.
- [ ] Security headers present on pages (and on static files if you want them there).
- [ ] Behind a proxy: `X-Forwarded-For` replaced by the proxy before `ClientIpByCustomHTTPHeader` is set; client-sent
      `X-Forwarded-Proto` removed when there is no proxy.
- [ ] One user (or one group) writes `var/` and `settings/`; no world-writable files; workers never root.
- [ ] Velocity: `Host` as intended, dashboard token and panel password set or the views kept local.
- [ ] Mail: `exp:mail:status` ok, footer filled in, bounce reader configured.
- [ ] Audit: `exp:audit verify` intact, `frequent` cronjob group running, alert recipients checked, keys backed up.
- [ ] An update routine: who watches releases, how updates are tested, who receives security mail.

---

## 13.17 References

In this repository:

- [Security defaults of September 2026](../specifications/6.0/security-defaults-2026-09.md): headers, session
  cookie, sign-in, served files, caching of personal pages
- [Security hardening of 6.0.13](../bc/6.0/hardening.md), [Datatype and input hardening](../specifications/6.0/datatype-input-hardening.md),
  [RAD tools: security](../bc/6.0/rad-security.md)
- [Request rules](../bc/6.0/view_full_security.md)
- [Changing your password](../features/6.0/modern-password-change.md), [user password change notes](../bc/6.0/user-password-change.md),
  [Form expired page](../features/6.0/form-expired-page.md)
- [The Exp Debug bar](../bc/6.0/debug-bar.md)
- [Audit](../bc/6.0/audit.md)
- [E-mail preferences: administrator's guide](../guides/mail-preferences-administrator.md),
  [upgrade notes](../bc/6.0/mail-preferences.md)
- [HTTP cache](../bc/6.0/httpcache.md)
- Velocity: [HTTP/2 and security](../specifications/6.0/velocity-http2-and-security.md),
  [HTTPS and certificates](../features/6.0/velocity-https-certificates.md),
  [control panel](../features/6.0/velocity-control-panel.md), [engines](../bc/6.0/velocity-engines.md)
- [Installing in one command](../features/6.0/install-in-one-command.md) (the generated administrator password)
- The settings: [`settings/site.ini`](../../settings/site.ini), [`settings/velocity.ini`](../../settings/velocity.ini),
  [`settings/audit.ini`](../../settings/audit.ini), [`settings/requestrules.ini`](../../settings/requestrules.ini);
  the rules: [`.htaccess_root`](../../.htaccess_root)
- [Chapter 8: Serving the site](08-serving-the-site.md)

External:

- OWASP: [Top Ten](https://owasp.org/www-project-top-ten/),
  [Session Management Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Session_Management_Cheat_Sheet.html),
  [Cross-Site Request Forgery Prevention](https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html),
  [Authentication Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Authentication_Cheat_Sheet.html),
  [Password Storage Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Password_Storage_Cheat_Sheet.html),
  [HTTP Security Response Headers](https://cheatsheetseries.owasp.org/cheatsheets/HTTP_Headers_Cheat_Sheet.html),
  [Content Security Policy](https://cheatsheetseries.owasp.org/cheatsheets/Content_Security_Policy_Cheat_Sheet.html),
  [Secure Headers Project](https://owasp.org/www-project-secure-headers/)
- NIST [SP 800-63B, Digital Identity Guidelines: Authentication](https://pages.nist.gov/800-63-3/sp800-63b.html)
- PHP: [Security](https://www.php.net/manual/en/security.php),
  [session configuration](https://www.php.net/manual/en/session.configuration.php),
  [display_errors](https://www.php.net/manual/en/errorfunc.configuration.php#ini.display-errors),
  [password_hash](https://www.php.net/manual/en/function.password-hash.php)
- MDN: [Set-Cookie](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Set-Cookie),
  [Strict-Transport-Security](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Strict-Transport-Security),
  [Content-Security-Policy](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy)
- HSTS: [RFC 6797](https://www.rfc-editor.org/rfc/rfc6797); cookies: [RFC 6265](https://www.rfc-editor.org/rfc/rfc6265)
- Apache: [Security tips](https://httpd.apache.org/docs/2.4/misc/security_tips.html),
  [mod_headers](https://httpd.apache.org/docs/2.4/mod/mod_headers.html),
  [mod_authz_host (Require ip)](https://httpd.apache.org/docs/2.4/mod/mod_authz_host.html)
- nginx: [add_header](https://nginx.org/en/docs/http/ngx_http_headers_module.html#add_header),
  [access module (allow, deny)](https://nginx.org/en/docs/http/ngx_http_access_module.html)
- Let's Encrypt: [documentation](https://letsencrypt.org/docs/)
- Exponential Velocity engine: <https://github.com/se7enxweb/exponential-velocity> (its `docs/security.md`,
  `docs/https.md`, `docs/panel.md`)

[Previous: 12. Troubleshooting](12-troubleshooting.md) ·
[Contents](README.md)
