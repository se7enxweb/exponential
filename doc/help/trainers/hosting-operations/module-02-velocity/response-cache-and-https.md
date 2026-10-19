---
title: "Lesson 2.4: The response cache and HTTPS certificates"
audience: trainer, hosting, admin
category: training
order: 24
summary: Learners prove a response-cache hit, explain what the cache never stores and how to clear it, then switch on HTTPS with a certificate pair and read it with exp:velocity ssl show. 45 minutes.
keywords: hosting and operations, Velocity, response cache, X-Cache, DefaultTtl, NotFoundSeconds, cache clear, cache stats, HTTPS, certificate, ssl show, Let's Encrypt, ACME, HSTS, self-signed
type: help
---
# Lesson 2.4: The response cache and HTTPS certificates

Two things Velocity does that a traditional PHP-FPM setup leaves to other tools: answering repeat anonymous pages from
its own response cache, and serving HTTPS with its own certificates. Support staff meet both every week ("my edit does
not show", "the certificate warning"), so this lesson gives learners the checks and the answers.

## At a glance

| | |
|---|---|
| Course | [Hosting and operations](../README.md) |
| Module | [Module 2: Velocity](README.md) |
| Duration | 45 minutes (20 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | System administrators and hosting support staff |
| Prerequisites | [Lesson 2.3](workers-and-pools.md); `openssl` on the learner's machine |

## Learning objectives

After this lesson, learners can:

- show a response-cache hit with two requests and read `X-Cache` and `Age`;
- list what the response cache never stores, including any page for a signed-in visitor;
- clear the response cache and say when a restart is not needed;
- switch on HTTPS with a certificate pair and read the served certificate with `exp:velocity ssl show`;
- explain the self-signed stand-in, live certificate swaps and the risk of a long HSTS period.

## Trainer's notes

### How to present it

Source: [Velocity response cache](../../../hosting/performance/velocity-response-cache.md),
[Velocity HTTPS certificates](../../../hosting/servers/velocity-https-certificates.md) and
[Chapter 8, section 8.3.9](../../../../install/08-serving-the-site.md#839-https-served-by-velocity-itself).

1. **What the cache does** (4 minutes). Rendered anonymous pages are answered inside the server process before a
   worker is chosen: no fork, no PHP, no database. Conditional requests get `304`; an expired page is still served
   while one request renders the replacement (`StaleWhileRevalidate`, 60 s).
2. **What it never stores** (3 minutes). Signed-in visitors are never served from it; requests with the session
   cookie or an `Authorization` header; responses that set a cookie or say `no-store` or `private`; anything but
   `GET`; non-200 answers (a 404 only when `NotFoundSeconds` is above 0).
3. **Freshness** (3 minutes). `DefaultTtl` (30 s) is how old a page a visitor may see after an edit. Clear with
   `exp:velocity cache clear`; after a template change no restart is needed. The browser navigation cache means a
   published change can appear on the second view.
4. **HTTPS** (6 minutes). With a pair of PEM files: set `[HTTPSSettings] Certificate`, `Key` and `Enabled true`,
   restart. The pair is checked before use; a pair that fails is never shown, a self-signed certificate stands in.
   A renewed file is swapped in live within a minute. Let's Encrypt and other sources are set in a snippet in
   `conf-available/`; port 80 must reach the server for the HTTP-01 challenge.
5. **HSTS and backups** (4 minutes). A browser that has seen `Strict-Transport-Security` refuses plain HTTP for the
   whole period; raise `HSTSMaxAge` to a year only when every name is on HTTPS for good. Back up the `ssl/` directory;
   only the ACME account keys cannot be made again.

### Common questions

**A customer says signed-in users see an old page.** Not the response cache: signed-in visitors never get it. Look at
the view cache or the HTTP cache.

**Why are 404s not cached by default?** `NotFoundSeconds=0` sends every 404 to PHP, so `404.log` and the security log
see each one. Keep it at 0 where security monitoring reads those logs.

**How early should I worry about expiry?** A 90-day certificate is renewed when about 30 days are left; fewer than 14
days left means renewal has been failing for two weeks. Alert on `daysLeft` below 14.

**Does the shipped configuration redirect HTTP to HTTPS?** `velocity.ini` has no setting that redirects one to the
other; both listen at once. Make the site's own addresses HTTPS ones before raising HSTS.

### Pitfalls

- **Testing while signed in.** A browser with an administration session never sees a cache hit. Use `curl`.
- **ACME in class.** Let's Encrypt needs public DNS and port 80 from the internet. Use a self-made pair in class, as
  in the exercise, and show ACME on the projector only.
- **HSTS on a training host name.** Do not set `HSTSMaxAge` above its shipped value on a name learners reuse.

## Hands-on exercise

Use your own training installation, served by Velocity on the shipped ports (8088 and 8080).

1. Request the front page twice, without signing in:

   ```bash
   curl -sI http://127.0.0.1:8088/ | grep -iE 'x-cache|age'
   curl -sI http://127.0.0.1:8088/ | grep -iE 'x-cache|age'
   ```

2. Show the cache's state: `php bin/php/console exp:velocity cache stats`.
3. Clear it: `php bin/php/console exp:velocity cache clear`, then repeat step 1 once.
4. Make a self-signed pair for the exercise in a directory of your installation:

   ```bash
   mkdir -p var/training-tls
   openssl req -x509 -newkey rsa:2048 -nodes -days 30 -subj "/CN=training.example.invalid" \
     -keyout var/training-tls/key.pem -out var/training-tls/cert.pem
   ```

5. Point Velocity at the pair and switch HTTPS on:

   ```bash
   php bin/php/console exp:velocity config set HTTPSSettings Certificate var/training-tls/cert.pem
   php bin/php/console exp:velocity config set HTTPSSettings Key var/training-tls/key.pem
   php bin/php/console exp:velocity config set HTTPSSettings Enabled true
   php bin/php/console exp:velocity restart
   ```

6. Read `php bin/php/console exp:velocity status` and find the HTTPS address.
7. Show the served certificate: `php bin/php/console exp:velocity ssl show`.
8. Request the site over HTTPS, accepting the self-made certificate: `curl -skI https://127.0.0.1:8080/ | head -1`.
9. Switch HTTPS off again with `config set HTTPSSettings Enabled false` and restart.

## Expected result

- Step 1: the second answer carries `X-Cache: HIT` and an `Age` header.
- Step 3: the first request after clearing is not a hit; the next one is again.
- Step 7 lists the certificate for `training.example.invalid` with its expiry date and days left.
- Step 8 prints `HTTP/2 200` or a redirect.
- The trainer checks by asking for the days left that `ssl show` printed.

## Quiz

1. Which header shows that a page came from the response cache?
2. A signed-in editor sees an old page. Is the response cache the cause?
3. What does Velocity serve when the configured certificate has expired?
   A. nothing, HTTPS stops B. the expired certificate C. a self-signed stand-in D. plain HTTP on port 443
4. A renewal tool replaces the certificate file. What must the operator do?
5. Which file in the `ssl/` directory cannot be made again and must be backed up?

<details>
<summary>Answers</summary>

1. `X-Cache: HIT`, together with an `Age` header.
2. No. Signed-in visitors are never served from it; look at the view cache or the HTTP cache.
3. **C.** A pair that fails the check is never shown; the self-signed certificate stands in until the real one is usable
   ([Velocity HTTPS certificates](../../../hosting/servers/velocity-https-certificates.md#what-you-can-rely-on)).
4. Nothing: a changed, valid pair is swapped in live within `watchInterval` (60 seconds), without a restart.
5. The ACME account keys (`acme/account-<ca>.pem`).

</details>

## Further reading

- [Velocity response cache](../../../hosting/performance/velocity-response-cache.md): settings and support answers.
- [Velocity HTTPS certificates](../../../hosting/servers/velocity-https-certificates.md): every certificate source.
- [Velocity HTTPS and certificates](../../../../features/6.0/velocity-https-certificates.md): the feature page.
- [Certificate expiry](../../../hosting/runbooks/certificate-expiry.md): the runbook.
- [TLS and HTTPS](../../../hosting/security/tls-and-https.md): HTTPS for hosts in general.

## Related pages

- [Module 2: Velocity](README.md)
- [Hosting and operations: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
