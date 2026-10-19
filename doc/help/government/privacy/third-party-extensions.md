---
title: Third-party extensions and privacy
audience: government, admin, project-manager
category: public-sector
order: 210
summary: For public bodies - the extensions installed with Exponential that send visitor or content data to third parties when activated (analytics, CAPTCHAs, maps, social login, AI providers), that none is active by default, and the checks to make before activating one.
keywords: third-party services, extensions, bcwebsitestatistics, Google Analytics, recaptcha, hcaptcha, ezgmaplocation, Google Maps, social login, expai, AI providers, data transfer, processor, ActiveExtensions, privacy impact, public sector
type: help
---
# Third-party extensions and privacy

This page is for administrators, project managers and data protection officers. Composer installs several extensions
with Exponential, but they are **not active** until a site activates them: `settings/site.ini [ExtensionSettings]
ActiveExtensions[]` ships with `xrowmetadata` only, and the setup wizard adds `ezjscore`, `ezoe` and `ezformtoken`
([Privacy standards: Third-party services](../../../standards/privacy.md#third-party-services-in-shipped-extensions)).
Some of them send data to a third party once active. Whether a site package activates any of them is something to check
on your installation.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [The extensions](#the-extensions)
2. [Checking what is active](#checking-what-is-active)
3. [Before activating one](#before-activating-one)
4. [Related pages](#related-pages)

## The extensions

| Extension | Third party | Data | Page in this section |
|---|---|---|---|
| `bcwebsitestatistics` | Google Analytics / Google Tag Manager | visitors' page views; shop orders if `OrderSubmit` is on | Analytics |
| `recaptcha` | Google reCAPTCHA | the visitor's browser and interaction on protected forms | CAPTCHAs |
| `hcaptcha` | hCaptcha | as above | CAPTCHAs |
| `ezgmaplocation` | Google Maps | the editor's map lookups; visitors' map views in your templates | Maps and embeds |
| `sevenx_authentication_2fa` social login | the chosen identity provider | sign-in through that provider | Social login |
| `expai` (Exponential Intelligence) | the AI provider configured | content, and images for alternative text, under its data policy | (below) |
| `cjw_newsletter` statistics | none (on-site) | per-person opens and clicks, only with the "Newsletter statistics" consent | Consent records for mail |

`expai` is installed by `composer.json` and active only where a site activates it; its data policy decides which
content may go to which provider and region ([Exponential Intelligence](../../../features/6.0/extensions/expai.md)).
Involve your DPO before activating it.

## Checking what is active

```bash
./console exp:ini where site.ini/ExtensionSettings/ActiveExtensions --allow-root-user
```

or **Setup > Extensions**. Look also at `ActiveAccessExtensions[]` in each siteaccess.

## Before activating one

1. Purpose and legal basis: why the service is needed, on what basis.
2. The provider's role (processor or independent controller) and terms; a processing agreement where needed.
3. Transfers outside your jurisdiction, where your law restricts them.
4. Consent before loading, where needed (see "Cookie banners and consent").
5. The privacy notice updated.
6. The Content Security Policy: the provider's hosts added with `AdditionalSources[]` only on the siteaccess that needs
   them.
7. Accessibility of what the service shows (CAPTCHAs and maps are common barriers).
8. A record in your inventory with the date of activation.

## Related pages

- [Privacy standards](../../../standards/privacy.md)
- [Extensions](../../../guides/extensions.md)
- [Controller, processor and the software](controller-processor-and-exponential.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
