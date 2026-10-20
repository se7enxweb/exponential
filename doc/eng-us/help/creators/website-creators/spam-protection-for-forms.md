---
title: "Spam protection for forms: CAPTCHA and form tokens"
audience: website-creator, admin
category: website-creation
order: 470
summary: Protect contact forms, registration and comments against spam and forged posts - form tokens with ezformtoken, reCAPTCHA or hCaptcha datatypes, keys per host, and other measures.
keywords: spam, CAPTCHA, recaptcha, hcaptcha, ezformtoken, form token, CSRF, registration spam, comment spam, bots, website creator
type: help
---
# Spam protection for forms: CAPTCHA and form tokens

This page is for website creators who keep spam and forged submissions out of forms. Two layers work together: a
**form token** proves a post came from your own page, and a **CAPTCHA** asks the visitor to prove they are human.

## Contents

1. [Form tokens](#form-tokens)
2. [CAPTCHA: reCAPTCHA or hCaptcha](#captcha-recaptcha-or-hcaptcha)
3. [Which forms to protect](#which-forms-to-protect)
4. [Other measures](#other-measures)

## Form tokens

The `ezformtoken` extension adds a secret token to every POST form and refuses posts without it, which stops forged
cross-site posts (CSRF). A refused post gets a 403 and one line in `var/log/warning.log`
(`site.ini [HTMLForms]`, `RefusalLogCollapseSeconds` collapses repeats). Set your own
`[HTMLForms] Secret` in `settings/override/site.ini.append.php`; the shipped value says to change it. When a
visitor's form has expired, they see a friendly page instead of an error
([Form expired page](../../../features/6.0/form-expired-page.md)).

## CAPTCHA: reCAPTCHA or hCaptcha

| | `recaptcha` | `hcaptcha` |
|---|---|---|
| Provider | Google reCAPTCHA (v2; a v3 template for the feedback form) | hCaptcha |
| Extra Composer package | `google/recaptcha` | none |
| Settings | `settings/override/recaptcha.ini.append.php` | `settings/override/hcaptcha.ini.append.php` |
| Docs | [recaptcha](../../../features/6.0/extensions/recaptcha.md) | [hcaptcha](../../../features/6.0/extensions/hcaptcha.md) |

Both work the same way:

1. Activate the extension and get keys from the provider.
2. Put them in the override file (per host as arrays for several sites):

   ```ini
   [Keys]
   PublicKey=<your site key>
   PrivateKey=<your secret key>
   [PublishSettings]
   NewObjectsOnly=false
   ```

   Keep the secret key out of version control.
3. Add the CAPTCHA attribute to the class: a contact form class, or the user class for registration.
4. Clear caches and test the form logged out.

`NewObjectsOnly=true` checks only new objects (registration yes, editing a profile no).

The CAPTCHA widget loads a script from the provider: allow its host in the site's Content Security Policy when you
enforce one ([Content Security Policy](../../../features/6.0/content-security-policy.md)), and mention the provider
in your privacy note.

## Which forms to protect

| Form | Protect with |
|---|---|
| Contact and feedback forms | token (automatic) and CAPTCHA |
| User registration (`user/register`) | token and CAPTCHA on the user class |
| Comments | token and CAPTCHA on the comment class |
| Forms for logged-in users only | token is usually enough |

## Other measures

- Require e-mail verification for registrations (`site.ini [UserSettings] VerifyUserType=email`)
  ([Member registration](member-registration-and-areas.md)).
- Limit what anonymous visitors may create (`content/create` for comments only where wanted).
- Remove spam collections in **Setup > Collected information** in bulk.

## Related pages

- [Forms with the information collector](forms-with-the-information-collector.md)
- [Security hardening](../../security-hardening.md)
- [Visitor permissions: the Anonymous role](visitor-permissions.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
