---
title: Domain names and HTTPS
audience: individual, website-creator, customer
category: personal-sites
order: 1635
summary: What an individual arranges outside Exponential to give a site its own address and the lock in the browser - buying a domain from a registrar, pointing DNS at the server, telling Exponential its address, getting a certificate from the host or through Velocity's Let's Encrypt support, mail records, and renewals.
keywords: domain name, registrar, DNS, A record, AAAA record, CNAME, nameserver, HTTPS, TLS, certificate, Let's Encrypt, Velocity, SiteURL, siteaccess, host, mail, SPF, renewal, individual
type: help
---
# Domain names and HTTPS

This page is for individuals who want their site at an address of their own, with the lock in the browser.

> **Note**
> Domain names and DNS are **outside Exponential**. Exponential cannot buy, register or renew a domain, and it does
> not manage DNS. You arrange them with a registrar (and, for DNS, with whoever hosts your DNS). Exponential only needs
> to be told the address it is reached at.

## Contents

1. [The pieces](#the-pieces)
2. [Choosing and buying a domain](#choosing-and-buying-a-domain)
3. [Pointing the domain at your site](#pointing-the-domain-at-your-site)
4. [Telling Exponential its address](#telling-exponential-its-address)
5. [HTTPS](#https)
6. [Mail for your domain](#mail-for-your-domain)
7. [Renewals](#renewals)
8. [Checklist](#checklist)

## The pieces

| Piece | Who provides it | Exponential's part |
|---|---|---|
| The domain name | A registrar, rented yearly | None |
| DNS records (where the name points) | The registrar, the host or a DNS service | None |
| The certificate for HTTPS | The host, or Velocity with Let's Encrypt | Velocity can obtain and renew it |
| The site's address in the settings | You | `SiteURL` and siteaccess host settings |

## Choosing and buying a domain

- Short, easy to spell and to say aloud.
- Buy it in your own name, with an address you will still read in ten years; for a club, in the club's name.
- Keep the registrar account safe: a strong password and two-step sign-in if offered. Losing the domain loses the
  site and every mail address on it.
- Many registrars offer privacy for the owner's contact details; consider it for a personal or family site.

## Pointing the domain at your site

At the registrar or DNS service, set:

| Record | Points to | For |
|---|---|---|
| `A` | The IPv4 address of your server or hosting | `example.com` |
| `AAAA` | The IPv6 address, if the host has one | `example.com` |
| `CNAME` or another `A` | The same server | `www.example.com` |

On shared hosting the host often tells you to use their nameservers instead; then they set the records. Changes can
take a while to be seen everywhere.

Decide which name is the main one (`www.example.com` or `example.com`) and redirect the other to it.

## Telling Exponential its address

Exponential builds links, mails and feeds from its configured address. When you go live, map the host names to
siteaccesses and set the site URL ([Going live: hosts, HTTPS and siteaccesses](../../creators/website-creators/going-live-hosts-https.md)).
If you installed with `exp:install`, the `--url`, `--access=host`, `--host` and `--admin-host` options set this from
the start ([Choosing an install method](choosing-an-install-method.md#options-that-matter-for-a-real-site)).

A separate address for the administration (for example `admin.example.com`) keeps editing apart from the public site.

## HTTPS

Every site with a sign-in needs HTTPS; today every site should have it.

| Served by | Certificate |
|---|---|
| Shared hosting | Usually included by the host, often from Let's Encrypt; switch it on in the panel |
| Apache or nginx on your server | A certificate tool on the server, or one from your provider |
| Velocity | Velocity obtains Let's Encrypt certificates itself when port 80 is reachable and DNS points to the server, renews them in the background, and serves a self-signed certificate until a real one is usable ([Velocity HTTPS and certificates](../../../features/6.0/velocity-https-certificates.md)) |

After HTTPS works, use one scheme only: redirect `http://` to `https://` and set the site URL with `https://`
([Going live, HTTPS](../../creators/website-creators/going-live-hosts-https.md#https)).

## Mail for your domain

If the site sends mail from your domain (registration, password reset, newsletters), receiving servers check DNS
records such as SPF and DKIM for your domain. Your host or mail provider tells you which records to add. Without
them, your site's mail often lands in spam ([Newsletter mail delivery](../../businesses/newsletters/newsletter-mail-delivery.md)).

## Renewals

- Domains expire. Turn on automatic renewal and keep the payment method current.
- Certificates from Let's Encrypt are short-lived; your host or Velocity renews them. Check the lock once a month.
- Put both dates in your calendar ([Running on a small budget](running-on-a-small-budget.md#your-yearly-cost-worksheet)).

## Checklist

- [ ] Domain bought in the right name, registrar account protected.
- [ ] `A` (and `AAAA`) records for the main name and `www`.
- [ ] One main name; the other redirects.
- [ ] Exponential's host settings and site URL use the real address.
- [ ] HTTPS works on every page; `http://` redirects.
- [ ] Mail records added if the site sends mail from your domain.
- [ ] Renewals automatic and in your calendar.

## Related pages

- [Setup for individuals](README.md)
- [Going live: hosts, HTTPS and siteaccesses](../../creators/website-creators/going-live-hosts-https.md)
- [Launch checklist for individuals](../checklists/launch-checklist-for-individuals.md)
- [Velocity HTTPS and certificates](../../../features/6.0/velocity-https-certificates.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
