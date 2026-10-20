# hcaptcha: hCaptcha datatype

This page is for site owners who want to keep spam out of forms with hCaptcha instead of Google's reCAPTCHA. `hcaptcha`
adds an **hCaptcha** datatype, the counterpart of [recaptcha](recaptcha.md). Add the attribute to a content class, and
the form asks the visitor to solve the challenge before it is accepted: in content edit, user registration and
information collection. The hCaptcha project's switching guide is at docs.hcaptcha.com/switch.

The extension is based on an original solution by an earlier contributor and was released as 1.0 on 11 August 2024.
**No Composer package is needed for hCaptcha** (unlike recaptcha).

## Set it up

1. Activate the extension (`ActiveExtensions[]=hcaptcha`).
2. Sign up at the hCaptcha dashboard and create a site to get your keys.
3. Put them in `settings/override/hcaptcha.ini.append.php`:

```ini
[Keys]
PublicKey=<your site key>
PrivateKey=<your secret key>
[Display]
Theme=white
OverrideLang=
[PublishSettings]
NewObjectsOnly=false
```

4. Clear the caches, then add an **hCaptcha** attribute to the class.

The settings mean the same as in [recaptcha](recaptcha.md#set-it-up): per-host keys as arrays, `Theme`,
`OverrideLang` and `NewObjectsOnly`.

## Settings

| File | Block | Key | Default | Meaning |
|---|---|---|---|---|
| `hcaptcha.ini` | `Keys` | `PublicKey`, `PublicKey[<host>]` | placeholder text `Enter your Public Key here` | The site key; one per host when you run several sites |
| `hcaptcha.ini` | `Keys` | `PrivateKey`, `PrivateKey[<host>]` | placeholder text `Enter your Private Key here` | The secret key, used on the server only |
| `hcaptcha.ini` | `Display` | `Theme` | `white` | Widget theme |
| `hcaptcha.ini` | `Display` | `OverrideLang` | empty | Language code of the widget (`en`, `de`, `fr`, ...); empty works it out from the object language |
| `hcaptcha.ini` | `PublishSettings` | `NewObjectsOnly` | `false` | Check only new objects, not re-edits |

The comments of the shipped INI file still speak of Google's service (it was copied from recaptcha). The code talks
to hCaptcha: the widget script is loaded from `js.hcaptcha.com`, and the answer is verified on the server at
`https://hcaptcha.com/siteverify` by `classes/hcaptchalib.php`. Check:

```bash
grep -n hcaptcha.com extension/hcaptcha/classes/hcaptchalib.php
```

## Limit: the form overrides are recaptcha's

`design/standard/override/templates/full/feedback_form.tpl` and `request_form.tpl` are the same files as in
[recaptcha](recaptcha.md#the-v3-form-overrides). They read `ezini( 'Keys', 'PublicKey', 'recaptcha.ini' )` and load
Google's `recaptcha/api.js`, **not** hCaptcha's. If both extensions are active and you use these form classes, the forms
use the reCAPTCHA settings; the hCaptcha datatype has its own template. Check:

```bash
grep -n 'ezini\|google' extension/hcaptcha/design/standard/override/templates/full/feedback_form.tpl
```

To use hCaptcha on those forms, remove the overrides from your design, or override them in your own extension.

## What changed

| Version | Date | Change |
|---|---|---|
| | 6 August 2024 | The repository began as a copy of recaptcha, so its first commits are recaptcha's. One of them added `google/recaptcha` to `composer.json`; the mass update that followed removed that dependency again. `composer.json` now requires only PHP 7.2 or later and the legacy installer. |
| 1.0 | 11 August 2024 | The internals were reworked for the hCaptcha API, tested with Exponential 6; funding metadata. |
| 1.1 | 30 September 2026 | The about page names the extension "hCaptcha extension for Exponential"; the description names Exponential. |
| 1.2 | 2 October 2026 | `extension.xml`, which the extension lacked, was added, and the website in `ezinfo.php`, so the about page and the upgrade checks show version, license and website. |

## Related pages

- [recaptcha](recaptcha.md)
- [Extension metadata specification](../../../specifications/6.0/extension-metadata.md)
- [Chronicle](../../../history/extensions/hcaptcha.md) and [release notes](../../../changelogs/extensions/hcaptcha.md)
- [Change ledger](../../../history/ledger/hcaptcha.md)
- [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
- Months: [2024-08](../../../history/extensions/months/2024-08.md), [2026-10](../../../history/extensions/months/2026-10.md) (all extensions)
