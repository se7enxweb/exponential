---
title: Velocity beside Apache or nginx
audience: hosting, admin
category: hosting
order: 120
summary: The shapes in which Velocity and a traditional web server share a machine or an installation: a proxy in front of Velocity, both serving the same files during a migration, and A/B comparison, with the trust, ownership, session and deploy rules each shape needs.
keywords: Velocity, Apache, nginx, reverse proxy, ProxyPass, proxy_pass, X-Forwarded-Proto, X-Forwarded-For, TrustedProxies, Q.webserver.proxy.trusted, side by side, migration, file ownership, sessions, PhpFpmService, deploy, Let's Encrypt behind proxy
type: help
---
# Velocity beside Apache or nginx

This page is for system administrators at hosting companies whose machines already run Apache or nginx and who add
Velocity to them. It answers: which shapes work, what each needs (trusted proxies, one user for the files, shared
sessions, one deploy), and how to check that a forwarded header cannot be forged. The sources are
[Chapter 8, sections 8.7 and 8.8](../../../install/08-serving-the-site.md#88-behind-a-reverse-proxy) and
[Forwarded headers are trusted only from configured proxies](../../../bc/6.0/trusted-proxies.md).

## Contents

1. [The three shapes](#the-three-shapes)
2. [Shape 1: a proxy in front of Velocity](#shape-1-a-proxy-in-front-of-velocity)
3. [Who trusts the proxy](#who-trusts-the-proxy)
4. [Shape 2: both serving the same installation](#shape-2-both-serving-the-same-installation)
5. [Shape 3: side by side for a comparison](#shape-3-side-by-side-for-a-comparison)
6. [Certificates behind a proxy](#certificates-behind-a-proxy)
7. [Checks](#checks)
8. [Checklist](#checklist)

## The three shapes

| Shape | When | What Velocity binds |
|---|---|---|
| 1. Apache or nginx in front of Velocity | one public address carries several services; several customers' Velocity instances behind one front server; Velocity must run as an ordinary user on a high port | `Host=127.0.0.1` (shipped), a high port |
| 2. Velocity and Apache/nginx serving the same files | a migration from PHP-FPM to Velocity, with the old server left in place on the same files | its own ports |
| 3. Both on their own ports for a test | comparing engines on the customer's own pages | its own ports |

Velocity does not need a proxy for TLS; it serves HTTPS itself. A proxy is still the right shape for the cases above
or when a load balancer or CDN already terminates TLS.

## Shape 1: a proxy in front of Velocity

**nginx in front of Velocity** (derived example from chapter 8):

```nginx
location / {
    proxy_pass http://127.0.0.1:8088;
    proxy_set_header Host              $host;
    proxy_set_header X-Forwarded-Proto $scheme;
    # appending is safe: Exponential reads the list from the right
    proxy_set_header X-Forwarded-For   $proxy_add_x_forwarded_for;
    proxy_http_version 1.1;
}
```

**Apache in front of Velocity** (derived example; needs `mod_proxy`, `mod_proxy_http` and `mod_headers`):

```apache
ProxyPreserveHost On
ProxyPass        / http://127.0.0.1:8088/
ProxyPassReverse / http://127.0.0.1:8088/
RequestHeader set X-Forwarded-Proto "https"
```

For several customers on one machine, give each customer's Velocity its own port and its own worker user, and one
front-server virtual host per customer pointing at that port. The proxy passes the original `Host` header; Velocity
does not use `X-Forwarded-Host`.

## Who trusts the proxy

Two lists, because Velocity decides before any PHP of the application runs:

| List | Where | Default | Decides |
|---|---|---|---|
| `Q.webserver.proxy.trusted` | the engine's configuration (a snippet in `/etc/vc/conf-available/`) | `127.0.0.1`, `::1` | under Velocity: the visitor's address and whether the request is HTTPS |
| `[HTTPHeaderSettings] TrustedProxies[]` | `settings/override/site.ini.append.php` | `127.0.0.1`, `::1` | under Apache or nginx with PHP-FPM: which forwarded headers the kernel believes |

A proxy on the same machine is covered by both defaults. A proxy on another address must be added. For Velocity:

```bash
cat > /etc/vc/conf-available/reverse-proxy.conf <<'EOF'
{ "Q": { "webserver": { "proxy": { "trusted": ["127.0.0.1", "::1", "192.0.2.10", "10.0.0.0/8"] } } } }
EOF
php bin/php/console exp:velocity conf enable reverse-proxy --allow-root-user
php bin/php/console exp:velocity restart --allow-root-user
```

From a trusted proxy, Velocity takes the visitor's address from `X-Forwarded-For` (read from the right) and hands it
to the kernel as `REMOTE_ADDR`, so `ClientIpByCustomHTTPHeader` is not needed under Velocity. It treats a plain
connection as HTTPS when a trusted proxy sends `X-Forwarded-Proto: https`, `CloudFront-Forwarded-Proto: https` or
`CF-Visitor: {"scheme":"https"}`.

> **Warning**
> Reading the forwarded protocol only from trusted proxies arrived in Exponential Velocity **0.0.4.44**. Up to and
> including 0.0.4.43, a visitor on a plain listener could make a request look like HTTPS. Check with
> `composer show se7enxweb/exponential-velocity`; on an older engine keep Velocity's plain port unreachable from
> outside whenever a proxy is in front ([Chapter 8, section 8.8](../../../install/08-serving-the-site.md#88-behind-a-reverse-proxy)).

## Shape 2: both serving the same installation

When Apache (PHP-FPM) and Velocity serve the same installation, they share `var/`, the caches and the sessions
([Chapter 8, One user, or one group](../../../install/08-serving-the-site.md#872-one-user-or-one-group)):

- **One user.** Give the PHP-FPM pool and Velocity's workers the same user (`[ServerSettings] User` and `Group`), or
  a common group with group-writable, setgid directories and the `[FileSettings]` permissions of chapter 8. Test with
  both running and look at the owner of new files in `var/` after a few requests.
- **One deploy.** `exp:velocity deploy` reloads the PHP-FPM that serves the site
  (`[DeploySettings] PhpFpmService=auto` finds it) and restarts Velocity, then clears the rendered-output caches. Do
  not set `PhpFpmService=disabled` in this shape.
- **Sessions.** With the role-aware HTTP cache on, both servers must keep sessions in the same directory: the PHP-FPM
  pool's `session.save_path` and Velocity's `[PHPSettings] IniOptions[]=session.save_path=0;0660;<dir>`
  ([HTTP cache](../../../bc/6.0/httpcache.md#switching-it-on)).
- **Caches.** Clearing a cache in Setup > Caches reaches both, because the files are shared. Two things do not:
  Velocity keeps settings in memory (restart it after clearing INI caches) and answers pages from its own response
  cache (`exp:velocity cache clear`) ([Caches](../../../guides/caches.md#4-velocity)).
- **OPcache and APCu** belong to each server's own processes.

## Shape 3: side by side for a comparison

Each engine has its own port, pid file and logs, so Velocity can run next to the existing server for a test. Then
measure both back to back:

```bash
php bin/php/console exp:benchmark --compare=https://www.example.com,https://www.example.com:8080 --allow-root-user
php bin/php/console exp:benchmark --compare=https://www.example.com,https://www.example.com:8080 --cold --runs=30 --allow-root-user
```

Compare only rows with the same `cache` answer: a cache hit against a render is not a comparison of engines
([Benchmarking a hosted site](../performance/benchmarking-a-hosted-site.md)).

## Certificates behind a proxy

The proxy owns port 80, so Velocity's ACME HTTP-01 challenge cannot reach it directly. Either let the proxy obtain
the certificate, or forward `/.well-known/acme-challenge/` to Velocity, or set the engine's `acme.webroot` to a
directory the proxy serves ([Velocity HTTPS certificates](velocity-https-certificates.md)).

## Checks

A forwarded header sent by a visitor must change nothing. Against the site directly (no proxy):

```bash
curl -s -o /dev/null -w '%{redirect_url}\n' -H 'X-Forwarded-Proto: https' http://example.com/user/logout
# expected: http://example.com/   -- an https:// address here means the header was believed
```

Through the proxy, absolute links and redirects must carry `https://`, and the debug bar's "Is my address listed?"
shows the address the kernel uses for the visitor.

| Symptom | Cause | Fix |
|---|---|---|
| redirects and absolute links go to `http://` | the proxy is not trusted, or sends no `X-Forwarded-Proto` | list it; set the header in the proxy |
| every visitor has the proxy's address | the proxy is not trusted (or, under PHP-FPM, `ClientIpByCustomHTTPHeader` not set) | list the proxy; set the header setting |
| a redirect loop after switching to HTTPS | the proxy speaks HTTPS to the visitor and plain HTTP to the server, and the server does not know | as the first row |
| root-owned files appear in `var/` | a script or a server runs as root | run everything as the site's user |

## Checklist

- [ ] Velocity bound to `127.0.0.1` when a proxy is in front; its plain port not reachable from outside.
- [ ] Engine 0.0.4.44 or later; the proxy listed in `Q.webserver.proxy.trusted` (and `TrustedProxies[]` for PHP-FPM).
- [ ] The proxy sets `X-Forwarded-Proto` and passes `Host`; the curl check passes.
- [ ] In shape 2: one user for both servers; `deploy --dry-run` shows the FPM reload and the Velocity restart.
- [ ] Certificate route decided (proxy, forwarded challenge or `acme.webroot`).

## Related pages

- [Velocity for hosts](velocity-for-hosts.md)
- [Varnish and reverse proxies](../performance/varnish-and-reverse-proxies.md)
- [Apache with PHP-FPM](apache-with-php-fpm.md)
- [nginx with PHP-FPM](nginx-with-php-fpm.md)
- [Forwarded headers are trusted only from configured proxies](../../../bc/6.0/trusted-proxies.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
