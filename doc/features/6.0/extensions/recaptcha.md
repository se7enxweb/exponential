# recaptcha: Google reCAPTCHA datatype

`recaptcha` adds a **reCAPTCHA** datatype to keep spam out of content edit forms, user registration and information collection (contact and
feedback forms). It was refactored in August 2024 for reCAPTCHA v2, PHP 8 and the eZ 5 datatype changes, using the `google/recaptcha` Composer
package, and ships a reCAPTCHA **v3** pure-JavaScript `feedback_form` template override.

## Set it up

1. Activate the extension (`ActiveExtensions[]=recaptcha`), run `composer` for the `google/recaptcha` dependency as part of installing the package.
2. Sign up at the Google reCAPTCHA admin for your own keys.
3. Put them in `settings/override/recaptcha.ini.append.php`, one pair for a single site or per host name for several:

```ini
[Keys]
PublicKey=<your site key>
PrivateKey=<your secret key>
# several sites:
#PublicKey[www.example.com]=<site key>
#PrivateKey[www.example.com]=<secret key>
[Display]
Theme=white
OverrideLang=
[PublishSettings]
NewObjectsOnly=false
```

4. Add a **reCAPTCHA** attribute to the class (a contact form class, or the user class for registration). Clear caches.

| Block | Key | Default | Meaning |
|---|---|---|---|
| Keys | `PublicKey`, `PrivateKey` | placeholders | Your keys; arrays keyed by host name for multi-site setups |
| Display | `Theme` | `white` | `red`, `white`, `blackglass`, `clean`, `custom`; an unsupported value falls back to `red` |
| Display | `OverrideLang` | empty | Force the widget language (en, nl, fr, de, pt, ru, es, tr); empty derives it from the content language |
| PublishSettings | `NewObjectsOnly` | `false` | Check only new objects, so `user/register` is protected but `user/edit` is not (or comments are protected when added but not when edited) |

Keep the secret key out of version control.

## What changed

* 1.4 to 1.4.2 (August and September 2024): v2 support, keys in the ini file, README and changelog in Markdown, a fix for starting the extension in
  the class editor from a Composer installation, German validation messages translated to English for general use.
* 1.4.3 to 1.4.5 (27 to 30 September 2026): the extension states its version, license and website; every visible text is a translation string with
  German; the description names Exponential.

[hcaptcha](hcaptcha.md) is the same extension for the hCaptcha service.

## Related

* [Chronicle](../../../history/extensions/recaptcha.md) and [release notes](../../../changelogs/extensions/recaptcha.md)
