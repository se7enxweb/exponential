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

## What changed

* 1.0 (11 August 2024): the internals were reworked for the hCaptcha API, tested with Exponential 6, funding metadata.
* 1.1 (30 September 2026): the about page names the extension "hCaptcha extension for Exponential"; the description names Exponential.
* 1.2 (2 October): `extension.xml`, which the extension lacked, was added, and the website in `ezinfo.php`, so the about page and the upgrade checks show
  the extension's version, license and website.

## Related

* [Chronicle](../../../history/extensions/hcaptcha.md) and [release notes](../../../changelogs/extensions/hcaptcha.md)
