---
title: Web server choices for public-sector sites
audience: government, hosting, admin
category: public-sector
order: 60
summary: For IT teams that decide what serves an Exponential site in a public body. Velocity, FrankenPHP and Apache or nginx with PHP-FPM compared, the questions that decide between them, and how to fit the choice to your organization's approved software list.
keywords: web server, Velocity, qbix, FrankenPHP, Apache, nginx, PHP-FPM, LiteSpeed, IIS, approved software, standard platform, HTTPS, response cache, decision
type: help
---
# Web server choices for public-sector sites

This page is for infrastructure and application teams in public bodies who choose what serves an Exponential site.
It answers which servers and engines the project supports, what each one means for operations, and how to choose
when your organization keeps a list of approved server software. The facts come from
[Deploying](../../../../guides/deploying.md), [Chapter 8: Serving the site](../../../../install/08-serving-the-site.md)
and the hosting page [Choosing a web server](../../../hosting/servers/choosing-a-web-server.md).

## Contents

1. [The three setups](#the-three-setups)
2. [The choice in one table](#the-choice-in-one-table)
3. [Questions that decide it](#questions-that-decide-it)
4. [Approved software lists](#approved-software-lists)
5. [Running two side by side](#running-two-side-by-side)
6. [Checklist](#checklist)

## The three setups

| Setup | Choose it when |
|---|---|
| Apache (or nginx) with PHP-FPM | the server already has a web server, or your organization's standard is a classic web server |
| Exponential Velocity (the `qbix` engine) | you want the fastest engine, persistent workers, a built-in response cache and no web server to configure |
| FrankenPHP | you want one verified binary with TLS and HTTP/2 and no PHP on the machine to maintain |

Velocity is the recommended engine for every stage; FrankenPHP is the production-ready alternative
([Deploying](../../../../guides/deploying.md)). The setups can run side by side on different ports, so trying one never
takes the others down.

## The choice in one table

From [Choosing a web server](../../../hosting/servers/choosing-a-web-server.md):

| | Velocity | FrankenPHP | Apache + PHP-FPM | nginx + PHP-FPM |
|---|---|---|---|---|
| PHP | 8.0 or later | built into the binary | 8.0 or later | 8.0 or later |
| Separate web server needed | no | no (Caddy is built in) | yes | yes |
| Own HTTPS and certificates | yes, including ACME | yes | no (`mod_ssl` and a certificate tool) | no |
| Application kept in memory between requests | yes | no | no | no |
| Response cache in front of PHP | yes | not yet | no | no |
| Rewrite rules | built in | generated | `.htaccess_root` | written by you |

PHP's built-in server is for development only: it has no TLS and must never face visitors.

## Questions that decide it

| Question | If the answer is | Then |
|---|---|---|
| Does your organization allow a long-lived application process on its servers? | no | Apache or nginx with PHP-FPM |
| Is the machine's PHP 8.0 (for example the stock PHP of RHEL 9)? | yes | any engine; Velocity runs on 8.0 from its release 0.0.4.54 |
| Must the web tier be on your approved software list? | yes | see the next section |
| Does a central team terminate TLS on a load balancer or reverse proxy? | yes | any engine behind it, with trusted proxies set ([Several web servers behind a load balancer](../availability/several-web-servers-behind-a-load-balancer.md)) |
| Do anonymous visitors make most of the traffic? | yes | Velocity's response cache answers them without PHP ([Velocity response cache and HTTP cache](../availability/velocity-response-cache-and-http-cache.md)) |

## Approved software lists

Many public bodies only run server software that has passed an internal assessment. Practical consequences:

- **Apache and nginx** are on most such lists already. The rules Exponential needs are in `.htaccess_root`; for nginx
  they are translated rule by rule on [nginx with PHP-FPM](../../../hosting/servers/nginx-with-php-fpm.md).
- **Velocity** is a PHP application server shipped with Exponential, free software like the rest. Submit it to the
  assessment as part of the application, with its documentation:
  [Velocity web server](../../../../features/6.0/velocity-web-server.md) and
  [Velocity for public-sector teams](velocity-for-public-sector-teams.md).
- **FrankenPHP** is downloaded as a pinned release whose SHA-256 checksum is verified before it runs
  ([Deploying](../../../../guides/deploying.md)); `install --engine=frankenphp --check` re-hashes an installed
  binary. Note that its embedded PHP reads no machine `php.ini` ([FrankenPHP](../../../hosting/servers/frankenphp.md)).
- **Other servers** (LiteSpeed, Caddy with PHP-FPM, IIS, lighttpd) are tier 3, expected to work
  ([Operating system support](../../../os-support.md)). If your standard is one of them, record the risk.

Whatever you choose, keep the security rules of the shipped configuration. Do not remove rules from `.htaccess` to
make something work ([Deploying](../../../../guides/deploying.md)).

## Running two side by side

During a migration, or while an assessment of Velocity runs, both can serve the same installation on different ports:
a traditional server for visitors and Velocity on a test port for measurements. The shapes are described on
[Velocity beside Apache or nginx](../../../hosting/servers/velocity-beside-apache-or-nginx.md). After any PHP change,
reload the right PHP-FPM and restart Velocity; `exp:velocity deploy` does both in the right order:

```bash
./console exp:velocity deploy --dry-run --allow-root-user
```

## Checklist

- [ ] The setup is chosen and recorded in your hosting decision record.
- [ ] It is on your approved software list, or an assessment is under way.
- [ ] PHP version matches the engine (8.0 or later, Velocity included).
- [ ] HTTPS is handled by the engine, the web server or a central proxy, and documented.
- [ ] The deploy procedure names the right PHP-FPM service or Velocity.

## Related pages

- [Hosting](README.md) index of this folder
- [Apache with PHP-FPM](../../../hosting/servers/apache-with-php-fpm.md), [Velocity for hosts](../../../hosting/servers/velocity-for-hosts.md), [FrankenPHP](../../../hosting/servers/frankenphp.md)
- [Hosting decision record](hosting-decision-record.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
