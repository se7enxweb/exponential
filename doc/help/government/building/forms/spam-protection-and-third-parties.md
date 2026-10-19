---
title: Spam protection and third parties
audience: government, website-creator, admin, project-manager
category: public-sector
order: 40
summary: How to keep spam out of public forms built with Exponential - form tokens, the reCAPTCHA and hCaptcha datatypes and what sending visitor data to their providers means for a public body, and measures that need no third party.
keywords: spam, CAPTCHA, recaptcha, hcaptcha, third party, privacy, data transfer, Content Security Policy, honeypot, VerifyUserType, rate limiting, request-shield
type: help
---
# Spam protection and third parties

This page is for website creators, administrators and project managers who protect public forms against spam. Two
CAPTCHA extensions ship with Exponential, and both use an outside provider. For a public body, sending visitors' data
to an outside provider is a decision for the data protection officer, not only a technical choice. This page sets out
the options with and without third parties. The general guide is
[Spam protection for forms](../../../creators/website-creators/spam-protection-for-forms.md).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Layers of protection](#layers-of-protection)
2. [The CAPTCHA extensions](#the-captcha-extensions)
3. [What a third-party CAPTCHA implies](#what-a-third-party-captcha-implies)
4. [Measures without a third party](#measures-without-a-third-party)
5. [Choosing](#choosing)

## Layers of protection

| Layer | Stops | Third party |
|---|---|---|
| Form token (`ezformtoken`) | forged posts from other sites (CSRF); simple scripts that post without loading the form | no ([CSRF form tokens](csrf-form-tokens.md)) |
| CAPTCHA datatype | automated submissions | yes |
| Moderation | what got through | no |
| Request filtering in front of the site | floods and known bad clients | no, if you run it ([Security hardening: request-shield](../../../security-hardening.md#a-shield-in-front-request-shield)) |

## The CAPTCHA extensions

| | `recaptcha` | `hcaptcha` |
|---|---|---|
| Provider | Google reCAPTCHA (v2; a v3 template for the feedback form) | hCaptcha |
| Extra package | `google/recaptcha` (Composer) | none |
| Keys in | `settings/override/recaptcha.ini.append.php` | `settings/override/hcaptcha.ini.append.php` |
| Read | [recaptcha](../../../../features/6.0/extensions/recaptcha.md) | [hcaptcha](../../../../features/6.0/extensions/hcaptcha.md) |

Both add a datatype: add the attribute to the form class (or to the user class for registration), and the form
requires the challenge. The default installation activates both extensions; hCaptcha needs its keys set before it
works ([Extensions it activates](../../../site-packages/sevenx-multisite/extensions-it-activates.md)). Keep the secret
keys out of version control.

## What a third-party CAPTCHA implies

| Implication | Consequence |
|---|---|
| The widget loads a script from the provider in the visitor's browser | the provider receives the visitor's address and browser data |
| The provider may set cookies or similar | consent rules may apply (in the EU the [ePrivacy Directive](https://eur-lex.europa.eu/eli/dir/2002/58/oj)) |
| The provider becomes a recipient of personal data | your privacy notice must say so; under the [GDPR](https://eur-lex.europa.eu/eli/reg/2016/679/oj), assess the legal basis and any transfer outside the EU |
| A Content Security Policy must allow the provider's host | ([Content Security Policy](../../../../features/6.0/content-security-policy.md)) |
| Accessibility | visual challenges exclude people; check the provider's accessible mode ([Accessible forms](accessible-forms.md#captcha-and-accessibility)) |
| Availability | if the provider is unreachable, forms may not be sendable |

Exponential itself loads no third-party scripts in the administration and ships no cookie banner
([Privacy standards](../../../../standards/privacy.md)); adding a CAPTCHA to public forms changes that for those pages.

## Measures without a third party

| Measure | How |
|---|---|
| Form tokens | on by default with `ezformtoken`; set your own `[HTMLForms] Secret` |
| A honeypot field | a field hidden from people (not with `display:none` alone; off-screen with a label saying "leave empty") that bots fill; your template or a small extension rejects posts where it is filled |
| E-mail verification for accounts | `site.ini [UserSettings] VerifyUserType=email` ([Member registration](../../../creators/website-creators/member-registration-and-areas.md)) |
| Signed-in forms only | where the audience has accounts anyway |
| Limit what anonymous visitors may create | the Anonymous role ([Visitor permissions](../../../creators/website-creators/visitor-permissions.md)) |
| Rate limits and filters in front | request-shield, web server or Velocity rules |
| Bulk removal of spam | **Setup > Collected information**, **Remove selected** |

A honeypot needs a small amount of development; it is not shipped as a datatype.

## Choosing

| Situation | Suggestion |
|---|---|
| A low-volume contact form | token, honeypot, moderation |
| Public registration of accounts | token, e-mail verification; a CAPTCHA only if abuse continues |
| A high-volume form that is attacked | add a CAPTCHA after the privacy assessment, with an alternative channel |

## Related pages

- [Building forms](README.md)
- [CSRF form tokens](csrf-form-tokens.md)
- [Spam protection for forms](../../../creators/website-creators/spam-protection-for-forms.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
