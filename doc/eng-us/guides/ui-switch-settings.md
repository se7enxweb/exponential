# UI switch settings: the interface language choice

This guide is for the administrator of an installation. At the end you know every setting of the UI switch, can offer
another interface language, limit the choice to some siteaccesses or switch it off, add the toolbar entry to an
existing installation, and give the choice to a role. What users see is in
[the feature page](../features/6.0/ui-switch.md); how code adds a choice of its own is in
[the provider API](../bc/6.0/ui-switch.md).

Add `--allow-root-user` when you run a command as root.

## 1. What the system does, in one paragraph

A signed-in user who holds the policy `uiswitch/language` chooses the language of the administration interface in
the right toolbar, after Bookmarks. The choice is kept in the session for that siteaccess and that user, and applied
early in every request: it sets `site.ini [RegionalSettings] Locale` in memory for that user's requests only, so the
translations, date and number formats and the page language follow it. Content languages are never changed. The
choices come from **providers** listed in `settings/uiswitch.ini`; the language provider is the first one.

## 2. The settings: settings/uiswitch.ini

Never edit `settings/uiswitch.ini` itself. Put changes in `settings/override/uiswitch.ini.append.php` (every
siteaccess), in `settings/siteaccess/<siteaccess>/uiswitch.ini.append.php` (one siteaccess) or in an extension's
`settings/uiswitch.ini.append.php`.

### [UISwitchSettings]

| Setting | Default | Meaning |
|---|---|---|
| `Enabled` | `true` | `true` or `false`. Off: no choice is offered or applied anywhere, and stored choices are ignored. |
| `SiteAccessList[]` | empty | The siteaccesses where the choices work. Empty: every siteaccess whose design list (`SiteDesign`, `AdditionalSiteDesignList[]`) contains a design whose name starts with `admin` (admin, admin2, admin3, admin4, admin4l; the editor and adminui siteaccesses list them as fallbacks). A public site never qualifies by default. |
| `ProviderList[]` | `language` | The providers, in toolbar order. Each needs a group `[UISwitch_<identifier>]` with `Class=`, and gets the policy function `uiswitch/<identifier>`. |

### [UISwitch_language]

| Setting | Default | Meaning |
|---|---|---|
| `Class` | `expUISwitchLanguageProvider` | The provider class. |
| `Enabled` | `true` | `false` switches this provider off and leaves the others working. |
| `GroupList[]` | `english`, `major`, `regional` | The groups of the list, in order. A group identifier is `[a-z][a-z0-9_]*`; a group with nothing to offer is left out. Empty, with `AllLanguages=disabled`: no groups, the choice is the list of buttons of #506. |
| `GroupLabel[<group>]` | *English*, *Major world languages*, *Regional variants* | The heading of a group, translated in the context `kernel/uiswitch/language`. Missing: the identifier. |
| `GroupValues_<group>[]` | 5, 19 and 10 locales | The locales of a group, in the order shown. Why the shipped ones are what they are: [the feature page](../features/6.0/ui-switch-languages.md#why-the-default-ranking-is-what-it-is). |
| `AllLanguages` | `enabled` | `enabled`: every locale of `share/locale` (without format variants such as `eng-GB@euro`) is offered and listed A–Z after the groups as *All languages (A–Z)*. `disabled`: only the groups and `AllowedValues[]`; the offered locales that no group names are listed A–Z as *More languages (A–Z)*. Missing: `disabled`. |
| `ListUntranslated` | `enabled` | `enabled`: locales without a translation of the interface (`share/translations/<locale>/translation.ts`) are offered too, marked *no translation yet*: English texts, their own date and number formats. `disabled`: English (`eng-*`) and the translated languages only. Missing: `disabled`. |
| `AllowedValues[]` | empty | More locales to offer outside the groups: a format variant (`eng-GB@euro`), or the whole list when there are no groups and `AllLanguages=disabled`. |
| `RememberPreference` | `disabled` | `enabled`: the choice is also kept in the user's preferences (`exp_uiswitch_language`) and comes back at the next sign-in. `disabled`: it ends with the session, at logout. |
| `Title` | (none) | Not used by the language provider, whose title is always *Interface language*; other providers show it as the name of their choice. |

A locale is offered only when its code is a locale code (`eng-GB`, `nor-NO@intl`) and `share/locale/<locale>.ini`
exists (with `ListUntranslated=disabled` also a translation, except for English); anything else in the settings is
left out silently. The siteaccess's own `Locale` is always offered, and choosing it clears the choice. These are also
the only values `uiswitch/set` accepts. A provider with fewer than two values to offer does not appear in the
toolbar.

With groups or `AllLanguages=enabled` the choice is a list box with a heading per group and an *Apply* button; each
entry shows the language's own name, its name in the current interface language and how complete its translation is
("Deutsch (Deutschland) — German (Germany) (99%)", "Kiswahili (Kenya) — Swahili (Kenya) (no translation yet)"). How
the names, the A–Z order and the percentages are made: [the feature page](../features/6.0/ui-switch-languages.md).

## 3. Examples

### Only on some siteaccesses

```ini
<?php /* #?ini charset="utf-8"?

[UISwitchSettings]
SiteAccessList[]
SiteAccessList[]=admin
SiteAccessList[]=editor
*/ ?>
```

The empty `SiteAccessList[]` line first empties the list before the entries are added.

### Change the groups

A list in an override replaces the default list when it starts with an empty line (`GroupValues_major[]`). A
company working in Europe could put its own languages first:

```ini
<?php /* #?ini charset="utf-8"?

[UISwitch_language]
GroupList[]
GroupList[]=ours
GroupList[]=english
GroupLabel[ours]=Our languages
GroupValues_ours[]
GroupValues_ours[]=ger-DE
GroupValues_ours[]=fre-FR
GroupValues_ours[]=ita-IT
*/ ?>
```

A new heading needs its translation in the context `kernel/uiswitch/language` (an extension's `translations/`), or
it is shown as written. Without the empty lines, the entries are added to the default ones.

### Offer only a few languages

No groups and no A–Z list of every locale: the choice is the list of buttons, with the siteaccess's own language and
the ones named:

```ini
<?php /* #?ini charset="utf-8"?

[UISwitch_language]
GroupList[]
AllLanguages=disabled
AllowedValues[]
AllowedValues[]=eng-GB
AllowedValues[]=ger-DE
*/ ?>
```

### Only translated languages

```ini
<?php /* #?ini charset="utf-8"?

[UISwitch_language]
ListUntranslated=disabled
*/ ?>
```

The groups then show only English and the languages with `share/translations/<locale>/translation.ts`.

### Add a language

French needs two files, both shipped:

- `share/locale/fre-FR.ini` (the locale: date and number formats, the name *Français (France)*);
- `share/translations/fre-FR/translation.ts` (the translation of the interface).

With the shipped settings French is already offered (*Major world languages*, and A–Z). A locale that
`share/locale` has but no group names is offered through `AllLanguages=enabled`; to rank it, add it to a group:

```ini
<?php /* #?ini charset="utf-8"?

[UISwitch_language]
GroupValues_major[]=swe-SE
*/ ?>
```

A new translation (`share/translations/<locale>/translation.ts`) shows its percentage at the next page view: the
catalogue of names and percentages is cached under the cache directory (`uiswitch/`) and built again by itself when a
locale or translation file changes.

### Keep the choice after logout

```ini
<?php /* #?ini charset="utf-8"?

[UISwitch_language]
RememberPreference=enabled
*/ ?>
```

The choice is then stored in the user's preferences too, and applied again when nothing is stored in the session.
Choosing the siteaccess's own language clears the remembered value as well.

### Switch it off

Everywhere:

```ini
<?php /* #?ini charset="utf-8"?

[UISwitchSettings]
Enabled=false
*/ ?>
```

Only the language provider, leaving any provider an extension adds:

```ini
<?php /* #?ini charset="utf-8"?

[UISwitch_language]
Enabled=false
*/ ?>
```

Only on one siteaccess: the same lines in `settings/siteaccess/<siteaccess>/uiswitch.ini.append.php`.

After any change of `uiswitch.ini`, clear the INI cache (`php bin/php/ezcache.php --clear-tag=ini`) and reload
PHP-FPM or restart Velocity.

## 4. The toolbar on an existing installation

New installations get the toolbar entry from the installer (admin, editor and adminui siteaccesses). An installation
set up before has to add it once.

### admin4, the editor siteaccess and AdminUI: toolbar.ini

In `settings/siteaccess/<admin siteaccess>/toolbar.ini.append.php` (and the same for the editor and adminui
siteaccesses), add the tool to the list of available tools and put it right after `Tool[]=admin_bookmarks` in
`[Toolbar_admin_right]`:

```ini
[Tool]
AvailableToolArray[]
AvailableToolArray[]=setup_link
AvailableToolArray[]=admin_current_user
AvailableToolArray[]=admin_bookmarks
AvailableToolArray[]=admin_ui_switch
AvailableToolArray[]=admin_clear_cache
AvailableToolArray[]=admin_quick_settings

[Toolbar_admin_right]
Tool[]
Tool[]=admin_clear_cache
Tool[]=admin_bookmarks
Tool[]=admin_ui_switch
Tool[]=admin_current_user
Tool[]=admin_preferences
```

Keep the other lines of your file as they are; only the two `admin_ui_switch` lines are new. The tool's template is
`design/admin4/templates/toolbar/full/admin_ui_switch.tpl`, so the siteaccess's design list must contain admin4
(admin4l, editor and adminui list it as a fallback). A siteaccess on the classic designs alone (admin, admin2,
admin3) has no template for the tool; leave it out there.

### admin4l with the right column from a layout

When the admin siteaccess draws its right column from the explayouts admin layout, the toolbar list is not used.
Open the admin layout in the layout editor, add the block **Admin: interface choices (language)** (`admin_ui_switch`)
to the right zone after the Bookmarks block, and publish the layout. New installations get it from the admin layouts
fragment of explayouts.

### AdminUI side bar

The AdminUI side bar shows the dropdown *Interface* when `exp_adminui.ini` lists the plugin, as it does by default:

```ini
[MenuPlugins]
Enabled[]=uiswitch
```

An installation whose override replaces `Enabled[]` has to add the line there, after `Enabled[]=bookmarks`.

### Clear the caches

The toolbar's list of tools is compiled into the pagelayout's compiled template, so a change of `toolbar.ini` is only
seen after the compiled templates are rebuilt:

```bash
php bin/php/ezcache.php --clear-tag=ini
php bin/php/ezcache.php --clear-tag=template
```

Then reload PHP-FPM, or run `./console exp:velocity deploy` on Velocity.

## 5. Policies

The choice is shown only to a user who holds `uiswitch/<provider>`, for the language `uiswitch/language`. The policy
has no limitations: a role either has it or not.

- **Administrator** has it through its policy on every module.
- **Editor** has it on new installations (clean data and the multi-site installers).

To give it to a role:

1. Open *User accounts > Roles and policies* and the role, then *Edit*.
2. *New policy*, module **uiswitch**, function **language**.
3. There are no limitations to choose; save the policy and the role.

The policy is checked in every request; when it is removed, a user's stored choice stops working at the next page
and is dropped from the session. A provider an extension adds gets its own function in the same module
(`uiswitch/<identifier>`), listed as soon as `ProviderList[]` names it.

## 6. Checking it

- Sign in as a user with the policy and open a page of the admin siteaccess: the box *Interface* is after Bookmarks.
- `<admin url>/uiswitch/state` answers with JSON: the siteaccess, and for each provider the user may use the
  identifier, the title, the current value and the values with their labels.
- After a change, the audit console lists an `access.uiswitch.change` event.

## Related pages

- [The interface language](../features/6.0/ui-switch.md): what users see.
- [The interface language list](../features/6.0/ui-switch-languages.md): the groups, the A–Z order, translation
  coverage and why the default ranking is what it is.
- [The UI switch provider API](../bc/6.0/ui-switch.md): adding a choice from an extension.
- [Roles and policies](roles-and-policies.md), [Caches](caches.md), [Content languages](content-languages.md).
