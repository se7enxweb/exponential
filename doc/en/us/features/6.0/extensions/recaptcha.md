# recaptcha: Google reCAPTCHA datatype

This page is for site owners who want to keep spam out of forms. `recaptcha` adds a **reCAPTCHA** datatype that
protects content edit forms, user registration and information collection (contact and feedback forms). It was
refactored in August 2024 for reCAPTCHA v2, PHP 8 and the eZ 5 datatype changes, using the `google/recaptcha` Composer
package. It also ships a reCAPTCHA **v3** pure-JavaScript `feedback_form` template override.

## Set it up

1. Activate the extension (`ActiveExtensions[]=recaptcha`). Installing the package with Composer brings the
   `google/recaptcha` dependency.
2. Sign up at the Google reCAPTCHA admin for your own keys.
3. Put them in `settings/override/recaptcha.ini.append.php`: one pair for a single site, or one per host name for
   several. Keep the secret key out of version control.

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

4. Add a **reCAPTCHA** attribute to the class (a contact form class, or the user class for registration).
5. Clear the caches and open the form: the challenge shows above the submit button.

## Settings

| File | Block | Key | Default | Meaning |
|---|---|---|---|---|
| `recaptcha.ini` | `Keys` | `PublicKey`, `PrivateKey` | placeholders | Your keys; arrays keyed by host name for multi-site setups |
| `recaptcha.ini` | `Display` | `Theme` | `white` | `red`, `white`, `blackglass`, `clean`, `custom`; an unsupported value falls back to `red` |
| `recaptcha.ini` | `Display` | `OverrideLang` | empty | Force the widget language (en, nl, fr, de, pt, ru, es, tr); empty derives it from the content language |
| `recaptcha.ini` | `PublishSettings` | `NewObjectsOnly` | `false` | Check only new objects, so `user/register` is protected but `user/edit` is not (or comments are protected when added but not when edited) |

## The v3 form overrides

The extension ships two template overrides in `design/standard/override/templates/full/`: `feedback_form.tpl` and
`request_form.tpl` (6 August 2024). They replace the default feedback and request forms of the classic design with a
Tailwind-styled form whose submit button is a reCAPTCHA **v3** button:

- the button carries `class="g-recaptcha"`, `data-action="submit"` and
  `data-sitekey="{ezini( 'Keys', 'PublicKey', 'recaptcha.ini' )}"`, so the **site key comes from
  `recaptcha.ini [Keys] PublicKey`** (since the same day's refactor, which replaced a key written into the template);
- the template loads the script `https://www.google.com/recaptcha/api.js`;
- the collected information is posted to `content/action` as usual.

They apply to every object shown with the `feedback_form` and `request_form` classes under the design that lists this
extension. Remove or copy them if your site uses its own form markup.

**Limit:** the v3 button only produces a token in the browser. The server-side check (the `google/recaptcha` package)
belongs to the **datatype** attribute, not to these overrides.

## What changed

| Version | Date | Change |
|---|---|---|
| 1.4 to 1.4.2 | August and September 2024 | v2 support, keys in the INI file, README and changelog in Markdown, a fix for starting the extension in the class editor from a Composer installation, German validation messages translated to English for general use. |
| 1.4.3 to 1.4.5 | 27 to 30 September 2026 | The extension states its version, license and website; every visible text is a translation string with German; the description names Exponential. |

[hcaptcha](hcaptcha.md) is the same extension for the hCaptcha service.

## Related pages

- [hcaptcha](hcaptcha.md)
- [Chronicle](../../../history/extensions/recaptcha.md) and [release notes](../../../changelogs/extensions/recaptcha.md)
- [Change ledger](../../../history/ledger/recaptcha.md)
- [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
- Months: [2024-08](../../../history/extensions/months/2024-08.md), [2024-09](../../../history/extensions/months/2024-09.md), [2026-09](../../../history/extensions/months/2026-09.md) (all extensions)
