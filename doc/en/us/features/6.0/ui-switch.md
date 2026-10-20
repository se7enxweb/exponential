# Interface language and the UI switch

A signed-in user of the administration can choose the language the administration itself is shown in, without
changing the siteaccess and without touching the content. The choice sits in the right toolbar, right after
Bookmarks, under the heading *Interface*. It is the first choice of a small framework, the **UI switch**, which
extensions can extend with choices of their own (see [the provider API](../../bc/6.0/ui-switch.md)).

## Where it is

| Place | What you get |
|---|---|
| admin4 and admin4l, right toolbar | The box *Interface* after Bookmarks, with the choice *Interface language*: a button that shows the current language and opens the list of languages |
| The editor siteaccess | The same box, after Bookmarks (new installations; existing ones, see [the settings guide](../../guides/ui-switch-settings.md)) |
| admin4l with the right column from a layout | The block *Admin: interface choices (language)* in the admin layout's right zone, after Bookmarks |
| AdminUI side bar | The dropdown *Interface* after the bookmarks dropdown (`exp_adminui.ini [MenuPlugins] Enabled[]=uiswitch`) |
| `uiswitch/state` | JSON of what the signed-in user may choose on this siteaccess, for scripts |

The box is not shown while editing or browsing, on a public
siteaccess, to a user without the policy, or when the provider has fewer than two languages to offer.

The switch works on every siteaccess whose design list contains an administration design (`admin`, `admin2`,
`admin3`, `admin4`, `admin4l`; the `editor` and `adminui` designs fall back to them), or on the siteaccesses
listed in `uiswitch.ini [UISwitchSettings] SiteAccessList[]`. The toolbar template ships for admin4, which admin4l,
the editor and AdminUI use as well.

## Using it

1. Open the list box under *Interface language*. It shows the language in effect, for example *English (United
   Kingdom)*. The list starts with the most needed languages in groups (*English*, *Major world languages*,
   *Regional variants*), then every language A–Z; typing the first letters of a name jumps to it.
2. Choose a language. Each entry gives the language's own name, its name in the current interface language and how
   complete the translation is (*Deutsch (Deutschland) — German (Germany) (99%)*); a language without a translation
   says *no translation yet* and shows English texts with its own date and number formats.
3. Press *Apply*. The page you were on loads again, in the chosen language.

The list, its order and the percentages: [the interface language list](ui-switch-languages.md). An installation whose
settings have no groups and no A–Z list shows the languages as a list of buttons instead.

Choosing the siteaccess's own language again clears the choice.

### What changes

The choice sets the siteaccess's `site.ini [RegionalSettings] Locale` for your requests only, in memory:

- the translations of the administration interface (menus, buttons, messages, the toolbar itself);
- date, time and number formats, which follow the locale;
- the page language: the `lang` attribute of the `<html>` element and the `Content-Language` header.

### What does not change

- **Content languages.** `SiteLanguageList[]`, `ContentObjectLocale` and the languages content is edited and shown in
  stay what the siteaccess says. A user with German as interface language still edits the English translation of an
  article when that is what they open.
- Other users. The choice belongs to you alone.
- Other siteaccesses. A choice made on `admin` is not applied on `editor`; each keeps its own.
- The settings files. Nothing is written to disk.

### How long it lasts

The choice is kept in your session, for that siteaccess and that user. It ends when the session ends, at logout or
when the session expires. When another user signs in on the same browser, they start with the siteaccess's language.

An administrator can make the choice come back at the next sign-in: with `RememberPreference=enabled` the choice is
also kept in the user's preferences. It is off by default.

## Who gets it

The choice is shown to users who hold the policy **`uiswitch/language`**. It has no limitations.

- **Administrator** holds it through its policy on every module (`*/*`).
- **Editor** gets it on new installations: the clean data and the multi-site installers give the role
  `uiswitch/language`.
- Any other role: grant `uiswitch/language` in *Roles and policies* (see
  [the settings guide](../../guides/ui-switch-settings.md#5-policies)).

The policy is checked again in every request. When it is taken away, the user's stored choice is no longer applied
and is dropped from the session.

## Keyboard

The button follows the header menus of admin4:

| Key | On the button | In the list |
|---|---|---|
| Enter or Space | opens or closes the list | chooses the focused language |
| Down arrow | opens the list and moves to the first language | moves to the next language |
| Up arrow | | moves to the previous language; from the first one, back to the button |
| Home, End | | first, last language |
| Escape | closes the list | closes the list and returns the focus to the button |
| Tab | | leaving the list closes it |

Only one header menu or interface list is open at a time. The current language carries `aria-current="true"`, the
button names the list with `aria-expanded` and `aria-controls`, and each language's name has its own `lang`, so a
screen reader pronounces it correctly.

## Without JavaScript

The button stays hidden and the list of languages is shown open. Each language is a submit button of an ordinary
form, so the choice works the same way.

## Security

- A change is accepted only as a **POST** to `uiswitch/set` (a GET is answered with 405).
- The kernel checks the **form token** of every POST before the view runs.
- A request the browser marks as coming from another site (`Sec-Fetch-Site: cross-site`) is refused with 403.
- The value must be one of the provider's **allow-list** exactly (no trimming, no case folding), or the request is
  refused with 400. A provider the user may not use is refused with 403, a siteaccess without the switch with 404.
- After the change the browser goes back only to a **local path** of the site (`/content/view/full/2`); anything else
  (`//host`, a scheme, a backslash, `..` segments, control characters) goes to the dashboard.
- Nothing from the request reaches a file name, a path or a settings name before the allow-list accepted it.
- Every change is recorded in the audit trail as **`access.uiswitch.change`** (channel `access`, severity info),
  with the value and siteaccess before and after. See [the audit catalogue](../../bc/6.0/audit.md).
- A page rendered with a choice is never stored in the role-aware HTTP page cache, and the content view cache,
  `{cache-block}` and compiled templates keep it apart from the pages of users without a choice.

## Exponential Velocity

Velocity workers serve many requests in one process. The choice is applied at the start of each request and
everything it touched (the translator, the default locale, the translation cache) is reset at the start of the next,
so a worker never carries one user's language into another user's page. A worker started before the feature was
deployed simply shows no choice until `./console exp:velocity restart` (part of `exp:velocity deploy`).

## Related pages

- [UI switch settings](../../guides/ui-switch-settings.md): every setting, adding a language, the toolbar on existing installations.
- [The UI switch provider API](../../bc/6.0/ui-switch.md): how an extension adds its own choice.
- [Content languages](../../guides/content-languages.md): the languages content is written in.
- [Roles and policies](../../guides/roles-and-policies.md).
