---
title: Spam protection for volunteer forms
audience: non-profit, website-creator, admin, businesses
category: business-solutions
order: 30
summary: Keeping spam out of a non-profit's volunteer and contact forms on Exponential - the ezformtoken form token that is active by default, choosing between the hcaptcha and recaptcha datatypes, keys in override files, the privacy and Content Security Policy consequences of a CAPTCHA, its accessible alternative, e-mail verification for registrations and cleaning up spam in bulk.
keywords: spam, CAPTCHA, hCaptcha, reCAPTCHA, hcaptcha, recaptcha, ezformtoken, form token, CSRF, privacy, Content Security Policy, accessibility, VerifyUserType, bulk removal, volunteer form
type: help
---
# Spam protection for volunteer forms

This page is for the website creator and the coordinator who sees the spam. A public form attracts automated
submissions within days. Spam wastes the coordinator's time and hides real applications, so protect every public form
before launch. The complete reference is [Spam protection for forms](../../creators/website-creators/spam-protection-for-forms.md).

## Contents

1. [Layer 1: the form token](#layer-1-the-form-token)
2. [Layer 2: a CAPTCHA](#layer-2-a-captcha)
3. [Choosing hCaptcha or reCAPTCHA](#choosing-hcaptcha-or-recaptcha)
4. [Privacy and Content Security Policy](#privacy-and-content-security-policy)
5. [Accessibility](#accessibility)
6. [Layer 3: registration and cleanup](#layer-3-registration-and-cleanup)
7. [Checklist](#checklist)

## Layer 1: the form token

The `ezformtoken` extension adds a secret token to every POST form and refuses posts without it, which stops forged
cross-site posts. It is active by default. Set your own `[HTMLForms] Secret` in
`settings/override/site.ini.append.php`; the shipped value says to change it. A visitor whose form expired sees a
friendly page instead of an error ([Form expired page](../../../features/6.0/form-expired-page.md)).

The token stops forged posts, not robots that fill in the form like a person. For that you need layer 2.

## Layer 2: a CAPTCHA

Both shipped CAPTCHA extensions are datatypes. You add a CAPTCHA attribute to the form class, and the form asks the
visitor to solve the challenge before it is accepted, in content edit, user registration and information collection
([hcaptcha](../../../features/6.0/extensions/hcaptcha.md)).

Setup, the same for both:

1. Activate the extension and get keys from the provider.
2. Put them in the override file:

   ```ini
   [Keys]
   PublicKey=<your site key>
   PrivateKey=<your secret key>
   [PublishSettings]
   NewObjectsOnly=false
   ```

3. Add the CAPTCHA attribute to the volunteer form class (and to the user class, if volunteers register).
4. Clear caches and test the form logged out.

Keep the secret key out of version control. Source: [Spam protection for forms](../../creators/website-creators/spam-protection-for-forms.md#captcha-recaptcha-or-hcaptcha).

## Choosing hCaptcha or reCAPTCHA

| | `hcaptcha` | `recaptcha` |
|---|---|---|
| Provider | hCaptcha | Google reCAPTCHA (v2; a v3 template for the feedback form) |
| Extra Composer package | None | `google/recaptcha` |
| Settings file | `settings/override/hcaptcha.ini.append.php` | `settings/override/recaptcha.ini.append.php` |

Both are installed through `composer.json`. Compare the providers' own terms and privacy statements; this help does
not rank them.

## Privacy and Content Security Policy

- The CAPTCHA widget loads a script from the provider, and the provider receives data about the visitor. Mention it
  in your privacy notice ([Captchas](../../government/privacy/captchas.md)).
- If your site sends a Content Security Policy, allow the provider's host
  ([Content Security Policy](../../../features/6.0/content-security-policy.md)).

## Accessibility

Every CAPTCHA is an obstacle for some people. Both shipped CAPTCHA services offer an accessible alternative
([Accessibility for site builders, Forms](../../creators/website-creators/accessibility-for-site-builders.md#forms)).
Also:

- Offer another way to apply: an e-mail address or phone number next to the form.
- Test the CAPTCHA with the keyboard and a screen reader before launch
  ([Accessible forms](../../government/accessibility/accessible-forms.md)).

## Layer 3: registration and cleanup

- If volunteers register accounts, require e-mail verification (`site.ini [UserSettings] VerifyUserType=email`)
  ([Member registration and areas](../../creators/website-creators/member-registration-and-areas.md)).
- Limit what anonymous visitors may create.
- Remove spam submissions in bulk: in **Setup > Collected information**, tick and **Remove selected**; the form
  stays ([Collected information](../../../guides/collected-information.md)).

| Form | Protect with |
|---|---|
| Volunteer and contact forms | Token (automatic) and CAPTCHA |
| User registration | Token and CAPTCHA on the user class |
| Forms for signed-in volunteers only | Token is usually enough |

## Checklist

- [ ] Own `[HTMLForms] Secret` set.
- [ ] CAPTCHA chosen, keys in the override file, attribute on the class.
- [ ] Provider named in the privacy notice; CSP allows its host.
- [ ] Accessible alternative tested; another way to apply offered.
- [ ] E-mail verification on for registrations.
- [ ] Coordinator knows how to remove spam in bulk.

## Related pages

- [Volunteers](README.md)
- [Volunteer sign-up form, step by step](volunteer-sign-up-form-step-by-step.md)
- [recaptcha](../../../features/6.0/extensions/recaptcha.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
