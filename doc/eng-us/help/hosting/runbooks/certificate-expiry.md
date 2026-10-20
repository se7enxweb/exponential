---
title: "Runbook: certificate expiry"
audience: hosting, admin
category: hosting
order: 80
summary: What to do when visitors see HTTPS warnings or a certificate is about to expire: check what is served, under Velocity with exp:velocity ssl show and renew, under Apache or nginx with your certificate tool or panel, and prevent a repeat.
keywords: runbook, certificate, TLS, HTTPS, expiry, expired, renew, Let's Encrypt, ACME, self-signed, Velocity, exp:velocity ssl show, ssl renew, HSTS, Apache, nginx, panel
type: help
---
# Runbook: certificate expiry

For on-call staff when visitors see a certificate warning, or monitoring reports a certificate close to expiry.

## Contents

1. [See what is served](#see-what-is-served)
2. [Under Velocity](#under-velocity)
3. [Under Apache, nginx or a panel](#under-apache-nginx-or-a-panel)
4. [After the incident](#after-the-incident)

## See what is served

From outside, for the domain the customer reported:

```bash
openssl s_client -connect www.example.com:443 -servername www.example.com </dev/null 2>/dev/null \
  | openssl x509 -noout -subject -issuer -dates
```

| You see | Meaning |
|---|---|
| `notAfter` in the past | expired |
| an issuer equal to the subject, or a warning about a self-signed certificate | under Velocity: the configured certificate is missing, expired or does not match its key, and Velocity fell back to a self-signed one |
| a certificate for another name | the wrong virtual host or site answers; check the name-based configuration |

## Under Velocity

Velocity checks a certificate before using it: "A broken or expired certificate never reaches a visitor: the pair is
verified (parses, not expired, key belongs to it) before use, and a self-signed certificate stands in until the real
one is usable again" ([Velocity HTTPS and certificates](../../../features/6.0/velocity-https-certificates.md)).

1. Show what it serves:

   ```bash
   php bin/php/console exp:velocity ssl show --allow-root-user
   ```

2. Renew:

   ```bash
   php bin/php/console exp:velocity ssl renew www.example.com --allow-root-user
   ```

   "The running server picks a renewal up within a minute, no restart" (`exp:velocity --help`).
3. With Let's Encrypt, renewal normally needs nothing: certificates "renew in a background job with growing pauses
   (5 minutes doubling up to a day) after a failure". If it keeps failing, check that port 80 of the server is
   reachable from the internet and that DNS points to it.
4. With your own files (`[HTTPSSettings] Certificate` and `Key`), put the new pair in place and check again with
   `ssl show`.

## Under Apache, nginx or a panel

- Renew with the tool that issued the certificate (certbot or another ACME client, or the panel's certificate
  feature), then reload the web server.
- Check that the renewal job runs: its timer or cron entry, its log.
- See [TLS certificates in panels](../panels/tls-certificates-in-panels.md) and [TLS and HTTPS](../security/tls-and-https.md).

> **Note**
> HSTS: Velocity sends `Strict-Transport-Security` with `HSTSMaxAge=300` by default; "a browser that has seen it
> refuses plain HTTP to this host name ... for that long". Do not try to work around an expired certificate by
> switching the site to plain HTTP; renew it.

## After the incident

- [ ] Verify from outside with `openssl s_client` again.
- [ ] Add or fix certificate expiry monitoring, with a warning well before the end
      ([Health checks and uptime](../operations/health-checks-and-uptime.md)).
- [ ] Note in the site record which source issues the certificate and where it renews.

## Related pages

- [Velocity HTTPS certificates](../servers/velocity-https-certificates.md)
- [Velocity will not start](velocity-will-not-start.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
