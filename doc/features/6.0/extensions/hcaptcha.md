# hcaptcha: hCaptcha datatype

`hcaptcha` adds an **hCaptcha** datatype, the hCaptcha counterpart to [recaptcha](recaptcha.md): add the attribute to a content class and the
form asks the visitor to solve the challenge before it is accepted, in content edit, user registration and information collection. hCaptcha
offers a service comparable to Google's reCAPTCHA; the project's switching guide is at docs.hcaptcha.com/switch. The extension is based on an
original solution by an earlier contributor and was released as 1.0 on 11 August 2024.

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

The settings have the same meaning as in [recaptcha](recaptcha.md#set-it-up): per host keys as arrays, `Theme`, `OverrideLang` and `NewObjectsOnly` (only
new objects are checked). Clear caches, then add an **hCaptcha** attribute to the class.

| File | Block | Key | Default | Meaning |
|---|---|---|---|---|
| `hcaptcha.ini` | `Keys` | `PublicKey`, `PublicKey[<host>]` | placeholder text `Enter your Public Key here` | The site key; one per host when you run several sites |
| `hcaptcha.ini` | `Keys` | `PrivateKey`, `PrivateKey[<host>]` | placeholder text `Enter your Private Key here` | The secret key, used on the server only |
| `hcaptcha.ini` | `Display` | `Theme` | `white` | Widget theme |
| `hcaptcha.ini` | `Display` | `OverrideLang` | empty | Language code of the widget (`en`, `de`, `fr`, ...); empty works it out from the object language |
| `hcaptcha.ini` | `PublishSettings` | `NewObjectsOnly` | `false` | Check only new objects, not re-edits |

The comments of the shipped ini file still speak of Google's service (it was copied from [recaptcha](recaptcha.md)); the code talks to hCaptcha: the widget script is loaded from
`js.hcaptcha.com` and the answer is verified server side at `https://hcaptcha.com/siteverify` by `classes/hcaptchalib.php`. Check with
`grep -n hcaptcha.com extension/hcaptcha/classes/hcaptchalib.php`.

## What changed

* 1.0 (11 August 2024): the internals were reworked for the hCaptcha API, tested with Exponential 6, funding metadata.
* 6 August 2024: the repository began as a copy of [recaptcha](recaptcha.md), so its first commits are recaptcha's: one of them added `google/recaptcha` to `composer.json`; the
  mass update of the internals that followed (see above) removed that dependency again. `composer.json` of the current extension requires only PHP 7.2 or later and
  the legacy installer, so **no Composer package is needed for hCaptcha** (unlike recaptcha).
* 1.1 (30 September 2026): the about page names the extension "hCaptcha extension for Exponential"; the description names Exponential.
* 1.2 (2 October): `extension.xml`, which the extension lacked, was added, and the website in `ezinfo.php`, so the about page and the upgrade checks show
  the extension's version, license and website.

## Related

* [Chronicle](../../../history/extensions/hcaptcha.md) and [release notes](../../../changelogs/extensions/hcaptcha.md)
* [Change ledger](../../../history/ledger/hcaptcha.md)
* [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
* [Extension metadata specification](../../../specifications/6.0/extension-metadata.md)
* [Month: 2024-08 (all extensions)](../../../history/extensions/months/2024-08.md)
* [Month: 2026-10 (all extensions)](../../../history/extensions/months/2026-10.md)
