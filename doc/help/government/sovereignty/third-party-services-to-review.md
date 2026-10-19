---
title: Third-party services to review
audience: government, admin, project-manager
category: public-sector
order: 60
summary: A review routine for every optional service that sends data outside the organization (analytics, CAPTCHA, maps, social sign-in, AI providers, embeds, CDNs), with what Exponential ships for each and the decisions a public body records before activating one.
keywords: third-party services, processors, analytics, Google Analytics, reCAPTCHA, hCaptcha, Google Maps, social login, AI provider, embeds, CDN, data protection, review, sovereignty
type: help
---
# Third-party services to review

This page is for administrators, data protection officers and project managers who decide whether to switch on an
optional service that involves another organization. Exponential installs several such extensions but leaves them
inactive; the decision and the paperwork are yours ([Privacy standards](../../../standards/privacy.md#third-party-services-in-shipped-extensions)).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [The services in question](#the-services-in-question)
2. [The review routine](#the-review-routine)
3. [Alternatives that keep data in-house](#alternatives-that-keep-data-in-house)
4. [After activation](#after-activation)

## The services in question

| Service | Extension or source | Data that leaves | Alternative inside Exponential |
|---|---|---|---|
| Web analytics | `bcwebsitestatistics` (Google Analytics / Tag Manager) | visitor requests, device data | server logs, request logs ([Request logs](../../../features/6.0/request-logs.md)) |
| CAPTCHA | `recaptcha`, `hcaptcha` | visitor interaction data | the form token (`ezformtoken`) and server-side checks ([Spam protection](../../creators/website-creators/spam-protection-for-forms.md)) |
| Maps | `ezgmaplocation` with an API key | visitor's map requests, editor's address searches | coordinates and addresses as text, without a map |
| Social sign-in | `sevenx_authentication_2fa` login handlers | sign-in events | local accounts with two-factor sign-in ([Two-factor login](../../../guides/two-factor-login.md)) |
| AI writing assistant | `expai` | content sent to the model provider | a local open-weight model through Ollama ([expai](../../../features/6.0/extensions/expai.md)) |
| Embedded video, social posts, fonts | your design or editors' embeds | visitor requests to the host of the embed | self-hosted files |

The `cjw_newsletter` statistics count opens and clicks on your own site, not at a third party; they need the
recipient's prior consent to that category ([Privacy standards](../../../standards/privacy.md)).

## The review routine

For each service, before activation, record:

1. **Purpose**: what problem it solves; whether an in-house alternative is enough.
2. **Data**: which personal data leaves, of whom (visitors, staff), to which country.
3. **Legal basis and consent**: consent needed? Exponential ships no cookie consent banner; a site that adds analytics,
   embeds or advertising must add one ([Privacy standards](../../../standards/privacy.md)).
4. **Contract**: processing agreement or terms; the provider's sub-processors.
5. **Security**: Content Security Policy entries the service needs; for `ezgmaplocation` its README lists the
   Google hosts to allow.
6. **Exit**: what happens to the site and the data if you switch it off.
7. **Owner**: who reviews it again, and when.

Keep the record with your register of processing activities.

## Alternatives that keep data in-house

- Analytics from server-side logs instead of a script on every page.
- No CAPTCHA unless abuse appears; then prefer server-side measures.
- Local models for AI assistance where the content must not leave the organization; `expai` records a region
  (`us`, `eu`, `local`) and retention per provider, and **Setup > EI: Summary** gives a page for the data protection
  officer.

## After activation

- Mention the service in the privacy notice.
- Check that the browser loads it only where intended (developer tools; CSP reports).
- Re-review when the provider changes its terms, and at least yearly.

## Related pages

- [No required external services](no-required-external-services.md)
- [Privacy standards](../../../standards/privacy.md)
- [Content Security Policy](../../../features/6.0/content-security-policy.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
