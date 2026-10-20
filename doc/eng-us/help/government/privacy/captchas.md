---
title: CAPTCHAs and privacy
audience: government, project-manager, admin
category: public-sector
order: 230
summary: For public bodies protecting forms from spam - what the optional recaptcha (Google reCAPTCHA) and hcaptcha (hCaptcha) extensions do, the data they send to the provider, consent and notice implications, accessibility concerns, and the form token as the first line of defense.
keywords: CAPTCHA, reCAPTCHA, hCaptcha, recaptcha extension, hcaptcha extension, spam, forms, third party, consent, ePrivacy, accessibility, ezformtoken, information collection, registration, public sector
type: help
---
# CAPTCHAs and privacy

This page is for project managers, administrators and data protection officers. Two optional extensions add a CAPTCHA
datatype to forms: `recaptcha` (Google reCAPTCHA, v2, with a v3 template for the feedback form) and `hcaptcha`
([recaptcha](../../../features/6.0/extensions/recaptcha.md), [hcaptcha](../../../features/6.0/extensions/hcaptcha.md)).
Both load a script from the provider, so the visitor's browser talks to a third party.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [How they work](#how-they-work)
2. [Privacy implications](#privacy-implications)
3. [Accessibility](#accessibility)
4. [Deciding](#deciding)
5. [Related pages](#related-pages)

## How they work

1. Activate the extension; put your keys in `settings/override/recaptcha.ini.append.php` or
   `hcaptcha.ini.append.php` (keep the secret out of version control).
2. Add the CAPTCHA attribute to a class: a contact form class, or the user class for registration.
3. The form shows the provider's widget; the server checks the answer with the provider before accepting the form.

`[PublishSettings] NewObjectsOnly=true` checks only new objects (registration yes, profile edits no)
([Spam protection for forms](../../creators/website-creators/spam-protection-for-forms.md)).

## Privacy implications

- The project's statement: consent or notice is needed "in most readings of art. 5(3)"; mention the provider in your
  privacy notice ([Privacy standards](../../../standards/privacy.md#third-party-services-in-shipped-extensions)).
- The provider receives browser and interaction data of every visitor who opens a protected form, including people
  contacting a public body about personal matters.
- Under a Content Security Policy the provider's hosts must be allowed on that siteaccess.

## Accessibility

CAPTCHAs are a known barrier for people with visual, cognitive or motor impairments. If you use one, test it with the
keyboard and a screen reader, and offer another contact route (e-mail, phone) next to the form. The accessibility
section of this help covers forms.

## Deciding

| Option | Privacy | Accessibility | Spam protection |
|---|---|---|---|
| Form token only (`ezformtoken`, active by default) | nothing leaves the site | no barrier | stops forged cross-site posts, not determined bots |
| Form token plus your own server-side checks | nothing leaves the site | no barrier | moderate |
| request-shield in front, with its browser check for form pages | nothing leaves the site (separate library) | invisible check | good against floods |
| hCaptcha or reCAPTCHA | third party | possible barrier | strong |

Start with the options that keep data on your site; add a CAPTCHA only on forms with real abuse.

## Related pages

- [Spam protection for forms](../../creators/website-creators/spam-protection-for-forms.md)
- [Third-party extensions and privacy](third-party-extensions.md)
- [Cookie banners and consent](cookie-banners-and-consent.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
