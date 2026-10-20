---
title: Renew an HTTPS certificate in Velocity
audience: government, admin, hosting
category: public-sector
order: 240
summary: Step by step - see which certificate Exponential Velocity serves and when it expires, renew it (automatically for Let's Encrypt, by command, or by replacing your own files), and check that the running server picked it up without a restart.
keywords: Velocity, HTTPS, certificate, renew, ssl show, ssl renew, qbixconsole ssl:show, Let's Encrypt, ACME, HTTPSSettings, Certificate, Key, expiry
type: help
---
# Renew an HTTPS certificate in Velocity

This task is for administrators of sites served by Exponential Velocity. Velocity checks a certificate before use,
watches the files, renews ACME certificates in the background and swaps a renewed certificate in without a restart
([Velocity HTTPS and certificates](../../../../features/6.0/velocity-https-certificates.md)).

## Contents
1. [Before you start](#before-you-start)
2. [See what is served](#see-what-is-served)
3. [Renew](#renew)
4. [Check it worked](#check-it-worked)
5. [Related pages](#related-pages)

## Before you start

- Shell access as root (or the user Velocity runs as).
- Know the certificate's source: your own files (`velocity.ini [HTTPSSettings] Certificate` and `Key`), or a mode
  such as `letsencrypt` in the site's Velocity configuration (`Q.web.https`).

## See what is served

```bash
./console exp:velocity ssl show --allow-root-user
qbixconsole ssl:show
```

Watch the days left: below 14 for a CA certificate means renewal has been failing for two weeks; `acme.lastError` says
why. From outside:

```bash
openssl s_client -connect www.example.gov:443 -servername www.example.gov </dev/null 2>/dev/null | openssl x509 -noout -dates
```

## Renew

| Source | Do |
|---|---|
| Let's Encrypt or another ACME CA | nothing normally: renewal runs in a background job when a third of the lifetime is left (`renewAt` 0.33), with growing pauses after failures. To force it: `./console exp:velocity ssl renew www.example.gov --allow-root-user` |
| Your own files (from your organization's certificate service) | put the new `fullchain.pem` and `privkey.pem` in place at the configured paths; Velocity checks the files every `watchInterval` (60 seconds) and presents a valid new pair to the next connection |

Renewal failing with "Invalid response from .../.well-known/acme-challenge/...": port 80 does not reach this server;
open it, proxy that path to Velocity, or use the `webroot` option.

## Check it worked

- `ssl show` lists the new expiry date.
- From outside, `openssl` shows the new `notAfter`.
- No restart was needed; if the old certificate is still served after a few minutes, read Velocity's error log
  (`./console exp:velocity status --allow-root-user` names it).

Back up Velocity's `ssl/` directory: the ACME account keys in it cannot be made again.

## Related pages
- [Switch Velocity to Let's Encrypt](switch-velocity-to-lets-encrypt.md)
- [Certificate expiry](../../operations/incidents/certificate-expiry.md)
- [Velocity HTTPS certificates (hosting)](../../../hosting/servers/velocity-https-certificates.md)
- [Deploying, section 6](../../../../guides/deploying.md#6-https)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
