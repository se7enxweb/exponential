---
title: Provisioning: overview
audience: hosting
category: hosting
order: 10
summary: The provisioning pages for hosting companies: one-click installers built on exp:install, the kickstarter and the setup wizard, site packages, automating new customer sites, and several sites on one installation.
keywords: provisioning, one-click installer, exp:install, kickstarter, kickstart.ini, setup wizard, site package, automation, multisite, siteaccess, new customer
type: help
---
# Provisioning: overview

This section is for hosting companies that create Exponential sites for customers: by hand, from a control panel's
one-click installer, or from their own automation. It answers: which installer fits which offer, how each one is
driven, what it installs, and how to make it repeatable and safe.

## Pages in this section

| Page | What it gives you |
|---|---|
| [One-click installers](one-click-installers.md) | the three front ends of the one installer, and how a host builds a one-click offer on them |
| [exp:install](exp-install.md) | the console install: options, passwords, dry run, exit status, for scripts and panels |
| [The kickstarter](kickstarter.md) | installs from a reviewed `kickstart.ini`; repeatable installs, staging copies |
| [The setup wizard on shared hosting](setup-wizard-on-shared-hosting.md) | the browser installer for customers without shell access |
| [Site packages](site-packages.md) | what an installation puts on the site, and where packages come from |
| [Automating provisioning](automating-provisioning.md) | a provisioning pipeline: account, database, code, install, server, checks |
| [Multisite on one installation](multisite-on-one-installation.md) | several sites from one installation through siteaccesses |

## The short version

> One installer, three front ends ([Chapter 4](../../../install/04-choosing-an-install-method.md#41-one-installer-three-front-ends)):
> every method runs the same steps and ends in `CreateSites`, so they install the same things.

| Situation | Use |
|---|---|
| A panel or script creates the site, unattended | `exp:install` |
| The same site built again and again (staging, training, a reviewed template) | the kickstarter |
| The customer installs it in the browser, without a shell | the setup wizard |

## Related pages

- [Hosting companies: start here](../hosting-companies-start-here.md)
- [Chapter 4: Choosing an install method](../../../install/04-choosing-an-install-method.md)
- [First sign-in and default passwords](../security/first-sign-in-and-default-passwords.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
