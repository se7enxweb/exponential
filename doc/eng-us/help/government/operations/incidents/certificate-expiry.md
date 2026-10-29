---
title: Certificate expiry
audience: government, admin, hosting
category: public-sector
order: 140
summary: Runbook for browser warnings about a public website's HTTPS certificate - see what is served, renew under Velocity (ssl show and renew, automatic Let's Encrypt renewal) or under Apache, nginx or a panel, and prevent it with monitoring and certificate ownership.
keywords: certificate expiry, expired certificate, HTTPS, TLS, SSL, browser warning, Let's Encrypt, ACME, renewal, Velocity, self-signed, HSTS, openssl, incident
type: help
---
# Certificate expiry

This runbook is for administrators when visitors get a browser warning about a public website's certificate. Citizens
are taught to stop at such warnings, so an expired certificate on a government site is effectively an outage. The
detailed runbook is [Certificate expiry (hosting)](../../../hosting/runbooks/certificate-expiry.md).

## Contents
1. [See what is served](#see-what-is-served)
2. [Under Velocity](#under-velocity)
3. [Under Apache, nginx or a panel](#under-apache-nginx-or-a-panel)
4. [Do not switch to plain HTTP](#do-not-switch-to-plain-http)
5. [Prevent it](#prevent-it)
6. [Related pages](#related-pages)

## See what is served

```bash
openssl s_client -connect www.example.gov:443 -servername www.example.gov </dev/null 2>/dev/null \
  | openssl x509 -noout -subject -issuer -dates
```

A `notAfter` in the past means expired; an issuer equal to the subject under Velocity means Velocity fell back to a
self-signed certificate because the configured one is missing, expired or does not match its key.

## Under Velocity

Velocity verifies a certificate before using it and lets a self-signed certificate stand in until the real one is
usable ([Velocity HTTPS and certificates](../../../../features/6.0/velocity-https-certificates.md)).

```bash
./console exp:velocity ssl show --allow-root-user
./console exp:velocity ssl renew --apply www.example.gov --allow-root-user
```

The running server picks up a renewal within a minute, without a restart. With Let's Encrypt, renewal runs in a
background job with growing pauses after a failure; if it keeps failing, check that port 80 is reachable from the
internet and DNS points to the server. With your own files, put the new pair in place and check with `ssl show`
([Renew an HTTPS certificate in Velocity](../../tasks/operations/renew-an-https-certificate-in-velocity.md)).

## Under Apache, nginx or a panel

Renew with the tool that issued the certificate (an ACME client such as certbot, the panel, or your organization's
certificate service), install it, reload the web server, check again from outside.

## Do not switch to plain HTTP

Browsers that saw `Strict-Transport-Security` refuse plain HTTP for the remembered time; Velocity sends `HSTSMaxAge=300`
by default, a site header may set a year. Renew instead.

## Prevent it

- Name an owner for each certificate (often the infrastructure team or a central certificate service) and record the
  expiry date.
- Monitor expiry from outside ([External uptime monitoring](../monitoring/external-uptime-monitoring.md)).
- Prefer automatic renewal (Let's Encrypt or your organization's ACME service) where your policy allows; some
  government domains require certificates from a specific authority, then plan manual renewal well ahead.

## Related pages
- [Switch Velocity to Let's Encrypt](../../tasks/operations/switch-velocity-to-lets-encrypt.md)
- [TLS and HTTPS (hosting)](../../../hosting/security/tls-and-https.md)
- [Velocity HTTPS certificates (hosting)](../../../hosting/servers/velocity-https-certificates.md)
- [Site down](site-down.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
