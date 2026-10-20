---
title: Switch Velocity to Let's Encrypt
audience: government, admin, hosting
category: public-sector
order: 250
summary: Step by step - move an Exponential Velocity site from a manually installed certificate to automatic Let's Encrypt certificates, trying the staging directory first, after checking your organization's certificate policy.
keywords: Let's Encrypt, ACME, Velocity, automatic certificates, letsencrypt-staging, Q.web.https, mode letsencrypt, domains, http-01, dns-01, certificate policy
type: help
---
# Switch Velocity to Let's Encrypt

This task is for administrators who want certificates that renew themselves. Many public bodies have a certificate
policy (an approved authority, organization-validated certificates for some domains); check it first, because
Let's Encrypt issues domain-validated certificates. Reference:
[Velocity HTTPS and certificates](../../../../features/6.0/velocity-https-certificates.md).

## Contents
1. [Before you start](#before-you-start)
2. [Try the staging directory](#try-the-staging-directory)
3. [Switch to production](#switch-to-production)
4. [Check it worked](#check-it-worked)
5. [Pitfalls](#pitfalls)
6. [Related pages](#related-pages)

## Before you start

- Your certificate policy allows an ACME CA for this domain.
- DNS points the domain to this server, and port 80 of this server is reachable from the internet (for the default
  `http-01` challenge). Wildcard names need `dns-01` with a hook program.
- A contact mailbox for expiry notices (a team address).

## Try the staging directory

In the site's Velocity configuration (for example `/etc/vc/sites-enabled/<site>.conf`), under `Q.web.https`:

```json
{ "Q": { "web": { "https": {
  "port": 443,
  "mode": "letsencrypt",
  "acme": { "email": "webteam@example.gov", "domains": ["example.gov", "www.example.gov"],
            "directory": "letsencrypt-staging" }
} } } }
```

Restart Velocity (`./console exp:velocity restart --allow-root-user`) and check with `qbixconsole ssl:show`. Staging
certificates are untrusted by browsers, but its rate limits are generous: use it until issuance works.

## Switch to production

Change `"directory"` to `"letsencrypt"` (or remove it; that is the default) and restart. Renewal then happens in the
background when a third of the lifetime is left.

## Check it worked

- `qbixconsole ssl:show` lists the certificate, its issuer and expiry, and no `acme.lastError`.
- From outside: `openssl s_client -connect www.example.gov:443 -servername www.example.gov </dev/null 2>/dev/null | openssl x509 -noout -issuer -dates`.
- Back up Velocity's `ssl/` directory, which holds the ACME account key.

## Pitfalls

| Symptom | Fix |
|---|---|
| `Invalid response from .../.well-known/acme-challenge/...` | port 80 does not reach this server; open it, proxy the path, or set `acme.webroot` for the server that owns port 80 |
| A self-signed certificate is served | issuance failed; Velocity keeps HTTPS up with a self-signed one until the real one is usable |
| Clock warnings | keep NTP running |

## Related pages
- [Renew an HTTPS certificate in Velocity](renew-an-https-certificate-in-velocity.md)
- [Velocity for public-sector teams](../../operations/hosting/velocity-for-public-sector-teams.md)
- [TLS and HTTPS (hosting)](../../../hosting/security/tls-and-https.md)
- [Velocity control panel](../../../../features/6.0/velocity-control-panel.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
