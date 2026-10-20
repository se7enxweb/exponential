---
title: One installation, many sites
audience: businesses, project-manager, admin
category: business-solutions
order: 20
summary: How Exponential turns one installation into several websites - siteaccesses, addresses by path, host or port, a root folder per site, the administration's view of all sites, and what the business has to arrange with DNS and certificates.
keywords: siteaccess, multisite, host matching, path, port, RootNode, IndexPage, HostMatchMapItems, DNS, HTTPS, certificate, brands, business
type: help
---
# One installation, many sites

This page is for managers and project leads who want to understand the model behind several sites on one
Exponential installation well enough to make decisions and talk to the technical team. It explains what a siteaccess
is, how a visitor's address reaches the right brand, and which parts of a launch belong to the business rather than
to the developers.

## Contents

1. [The siteaccess in one paragraph](#the-siteaccess-in-one-paragraph)
2. [How visitors reach a brand](#how-visitors-reach-a-brand)
3. [Each brand's start page and content root](#each-brands-start-page-and-content-root)
4. [What adding a brand involves](#what-adding-a-brand-involves)
5. [What the business must arrange](#what-the-business-must-arrange)
6. [Test hosts and staging](#test-hosts-and-staging)

## The siteaccess in one paragraph

A siteaccess is "one way into the installation": a name, a set of settings in `settings/siteaccess/<name>/`, and a
rule for which requests it answers
([10.2 Siteaccesses and site addresses](../../../install/10-after-installing.md#102-siteaccesses-and-site-addresses)).
Every public brand, every language of a brand and every administration is a siteaccess. They share the code and,
usually, the database; they differ in name, address, language list, design and start page.

## How visitors reach a brand

The installer chose one of three layouts, and they can be changed later:

| Layout | Example | What the sources say |
|---|---|---|
| URL (path) | `/admin`, `/de` | "one host name for everything; simplest behind any web server" |
| Host | `admin.example.com` | "a host name per siteaccess; best for production and for HTTPS certificates per host" |
| Port | `:8081` | "one port per siteaccess; useful for local work" |

Quoted from [Planning multisite and multilingual projects](../../leadership/multisite-and-multilingual-planning.md#how-visitors-reach-a-site).
For separate brands, a host per brand (`www.brand-a.example`, `www.brand-b.example`) is the usual choice: each brand
keeps its own domain, analytics and certificate. A path per brand fits microsites that live under the main domain.

> **Tip**
> Paths and hosts can be mixed. The main brand can use its own host, while campaign microsites use paths below it.

## Each brand's start page and content root

Each siteaccess names the page it opens on (`IndexPage`) and the node it treats as the root of its content tree
(`content.ini [NodeSettings] RootNode`). For separate brands, the usual setup is one folder per brand under the root,
each siteaccess using its folder as its root
([Step by step: a second site](../../creators/website-creators/multisite-setup.md#step-by-step-a-second-site)).

For the business that means:

- Each brand has its own menu and its own start page.
- Editors work in the brand's folder; roles can keep them there.
- Content outside a brand's folder can still be linked or shown there on purpose (see
  [Sharing content across brands](shared-content-across-brands.md)).

## What adding a brand involves

The technical steps, summarized from the website creators' guide:

| Step | Who | What happens |
|---|---|---|
| 1. Content | Editor or site builder | A folder for the brand is created under the root |
| 2. Siteaccess | Developer | A settings directory for the brand: name, address, start page, design |
| 3. Register and match | Developer or administrator | The brand is added to the list of siteaccesses and its host or path is mapped to it |
| 4. Web server and DNS | Hosting | The host name is added to DNS, the web server or Velocity, and the certificate |
| 5. Clear caches and check | Administrator | The INI cache is cleared and the new address is tested |
| 6. Tell the administration | Administrator | The brand is added to the admin's `SiteList[]` so previews and links can use it |

The full steps with the exact settings are on
[Setting up several sites in one installation](../../creators/website-creators/multisite-setup.md#step-by-step-a-second-site).

## What the business must arrange

- [ ] **Domain names**: registered and pointing to the server.
- [ ] **Certificates**: every host name the brand uses is covered by HTTPS
      ([Going live: hosts, HTTPS and siteaccesses](../../creators/website-creators/going-live-hosts-https.md)).
- [ ] **Analytics and search console accounts** per brand, if you measure brands separately.
- [ ] **Legal pages** per brand (imprint, privacy notice, terms), written by the brand owner.
- [ ] **A brand owner** who approves content and the launch.

## Test hosts and staging

A setting called `HostMatchMethod` lets the host map compare by the start, end or part of a host. With `start`, a test
host such as `www.example.com.test.local` reaches the same siteaccess as `www.example.com`, without extra entries
([Siteaccess by host prefix, and the redirect to the language](../../../features/6.0/siteaccess-host-and-language-matching.md)).
The same page warns that `start` and `part` also accept hosts you did not mean, so they belong where the web server
only answers for your own hosts. Ask your technical team which method your installation uses.

> **Note**
> Shipped `site.ini` uses `HostMatchMethod=strict`: without a change, nothing behaves differently from before.

## Related pages

- [Planning several brands](multi-brand-planning.md)
- [Settings, logs and caches per site](per-site-settings-and-logs.md)
- [Setting up several sites in one installation](../../creators/website-creators/multisite-setup.md)
- [Multisite on one installation (for hosts)](../../hosting/provisioning/multisite-on-one-installation.md)
- [Chapter 10: After installing](../../../install/10-after-installing.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
