---
title: Stopping spam
audience: local-business, businesses, customer
category: business-solutions
order: 52
summary: Keeping spam out of a local business's contact and request forms on Exponential - form tokens that are on by default, adding reCAPTCHA or hCaptcha (both need keys from the provider), which to choose, privacy notes, and what to do when spam still gets through.
keywords: spam, CAPTCHA, reCAPTCHA, hCaptcha, ezformtoken, form token, API keys, bots, contact form spam, privacy, local business
type: help
---
# Stopping spam

This page is for the owner of a local business whose contact form fills up with junk, or who wants to prevent it.
Exponential has two layers of protection that work together
([Spam protection for forms](../../creators/website-creators/spam-protection-for-forms.md)).

## Contents

1. [Layer 1: form tokens](#layer-1-form-tokens)
2. [Layer 2: a CAPTCHA](#layer-2-a-captcha)
3. [reCAPTCHA or hCaptcha](#recaptcha-or-hcaptcha)
4. [Keys and who holds them](#keys-and-who-holds-them)
5. [When spam still gets through](#when-spam-still-gets-through)
6. [Checklist](#checklist)

## Layer 1: form tokens

The `ezformtoken` extension adds a secret token to every form and refuses posts without it. It stops forged posts
sent from other sites. It is shipped with the kernel and works without any action from you; your builder sets the
site's own secret (`site.ini [HTMLForms] Secret`). It does not stop bots that load your page and fill in the form
like a person.

## Layer 2: a CAPTCHA

A CAPTCHA asks the visitor to prove they are human, usually with a checkbox or a small puzzle. Two extensions in the
standard `composer.json` add a CAPTCHA field to a content class, so it appears on your contact form, your registration
form or your comment form:

| | `recaptcha` | `hcaptcha` |
|---|---|---|
| Provider | Google reCAPTCHA | hCaptcha |
| Needs | a site key and a secret key from Google | a site key and a secret key from hCaptcha |
| Settings | `settings/override/recaptcha.ini.append.php` | `settings/override/hcaptcha.ini.append.php` |
| Read | [recaptcha](../../../features/6.0/extensions/recaptcha.md) | [hcaptcha](../../../features/6.0/extensions/hcaptcha.md) |

The `composer.json` description of `hcaptcha` names it as an alternative "for users who can't or won't use the Google
reCAPTCHA solution".

## reCAPTCHA or hCaptcha

| If you | Choose |
|---|---|
| Already have a Google account for maps or analytics and are fine with Google | either; reCAPTCHA keeps one provider |
| Prefer not to send visitor data to Google | hCaptcha |
| Are unsure | ask your builder which they have tested; both work the same way in Exponential |

Either way, the CAPTCHA loads a script from the provider. Mention the provider in your privacy note. If the site sends
a Content Security Policy, the builder allows the provider's host.

## Keys and who holds them

The keys come from an account with the provider. Create it in the business's name and give the builder access, or let
the builder create it and hand it over ([Working with a local partner or freelancer](../getting-started/working-with-a-local-partner-or-freelancer.md#what-you-keep-yourself)).
The secret key is kept out of version control and out of e-mails.

## When spam still gets through

| Symptom | Try |
|---|---|
| Spam through the contact form | check the CAPTCHA field is on the class and shows when signed out |
| Spam user registrations | CAPTCHA on the user class and e-mail verification (`site.ini [UserSettings] VerifyUserType=email`) ([Member registration and areas](../../creators/website-creators/member-registration-and-areas.md)) |
| Spam comments | CAPTCHA on the comment class; restrict who may comment ([Visitor permissions](../../creators/website-creators/visitor-permissions.md)) |
| A pile of spam submissions | delete them in bulk in **Setup > Collected information** |

Do not publish your e-mail address as a plain text link on every page if spam by mail is a problem; the form is the
better route.

## Checklist

- [ ] Form tokens active with the site's own secret (ask the builder once).
- [ ] CAPTCHA chosen and on every public form.
- [ ] Keys in an account the business controls.
- [ ] Privacy note mentions the CAPTCHA provider.
- [ ] Form tested signed out, on a phone.

## Related pages

- [A contact form for your business](a-contact-form-for-your-business.md)
- [Security basics for businesses](../../businesses/security/security-basics-for-businesses.md)
- [Security hardening](../../security-hardening.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
