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

## The v3 form overrides

The extension ships two template overrides in `design/standard/override/templates/full/`: `feedback_form.tpl` and `request_form.tpl` (6 August 2024). They replace the default
feedback and request forms of the classic design with a Tailwind-styled form whose submit button is a reCAPTCHA **v3** button: it carries `class="g-recaptcha"`,
`data-action="submit"` and `data-sitekey="{ezini( 'Keys', 'PublicKey', 'recaptcha.ini' )}"`, so the **site key comes from `recaptcha.ini [Keys] PublicKey`** (since the same day's
refactor, which replaced a key written into the template), and the script `https://www.google.com/recaptcha/api.js` is loaded by the template. The collected information is posted to
`content/action` as usual. These are visual and behavioural overrides: they apply to every object shown with the `feedback_form` and `request_form` classes under the design that
lists this extension, so remove or copy them if your site uses its own form markup. Note that the v3 button only produces a token in the browser; the datatype's own server-side
check (the `google/recaptcha` package) belongs to the **datatype** attribute, not to these overrides.

## Related

* [Chronicle](../../../history/extensions/recaptcha.md) and [release notes](../../../changelogs/extensions/recaptcha.md)
* [Change ledger](../../../history/ledger/recaptcha.md)
* [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
* [Month: 2024-08 (all extensions)](../../../history/extensions/months/2024-08.md)
* [Month: 2024-09 (all extensions)](../../../history/extensions/months/2024-09.md)
* [Month: 2026-09 (all extensions)](../../../history/extensions/months/2026-09.md)
