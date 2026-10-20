---
title: Siteaccess operators
audience: developer, designer, app-creator
category: development
order: 360
summary: The kernel operators that know the siteaccesses of an installation - siteaccess_url, siteaccess_menu, siteaccess_menu_key and language_switcher - with their parameters, the settings behind them, what they return, how they compile, and examples rendered on alpha with three different siteaccesses.
keywords: siteaccess_url, siteaccess_menu, siteaccess_menu_key, language_switcher, siteaccess, AdminSiteAccessMenu, FrontSiteAccess, TranslationSA, LanguageSwitcherClass, switchlanguage, ezpSiteAccessURL, ezpSiteAccessMenu
type: help
---
# Siteaccess operators

Three classes registered in `kernel/common/eztemplateautoload.php` give templates the addresses and names of the
other siteaccesses of the installation.

| Operator | Class and file | Work done by |
|---|---|---|
| `siteaccess_url` | `ezpSiteAccessURLOperator`, `kernel/common/ezpsiteaccessurloperator.php` | `ezpSiteAccessURL` (`kernel/private/classes/ezpsiteaccessurl.php`) |
| `siteaccess_menu`, `siteaccess_menu_key` | `ezpSiteAccessMenuOperator`, `kernel/common/ezpsiteaccessmenuoperator.php` | `ezpSiteAccessMenu` (`kernel/private/classes/ezpsiteaccessmenu.php`) |
| `language_switcher` | `ezpLanguageSwitcherOperator`, `kernel/private/eztemplate/ezplanguageswitcheroperator.php` | the class of `[RegionalSettings] LanguageSwitcherClass` |

The examples ran on the command line, so the scheme is `http` and the host comes from the settings.

## Contents

1. [The operators](#the-operators): [siteaccess_url](#siteaccess_url), [siteaccess_menu](#siteaccess_menu),
   [siteaccess_menu_key](#siteaccess_menu_key), [language_switcher](#language_switcher)
2. [Compiled templates](#compiled-templates)

## The operators

### siteaccess_url

Generated page: [siteaccess_url](../../examples/template-operators/siteaccess_url.md).

| Parameter | Type | Required | Default |
|---|---|---|---|
| `siteaccess` | string | no | empty |

- **Input:** none.
- **Returns:** the front page address of a siteaccess, ending in `/`, as a string. Without a name it is the front
  page of the site the current siteaccess belongs to (`ezpSiteAccessURL::front()`): `[SiteSettings] FrontSiteAccess`
  of the current siteaccess (an administration names its site there), else `DefaultAccess`. A siteaccess that is not
  in `AvailableSiteAccessList[]` gives an empty string.

The address is worked out from the scheme, host and port of the request and the way the target siteaccess is matched
(`MatchOrder`, `URIMatchType`, `HostMatchMapItems`, `SiteURL`), so it works for URI, host and port matching alike. It
is not quoted and not escaped.

```tpl
1 front: {siteaccess_url()}
2 bold: {siteaccess_url( 'bold' )}
3 starter_ger: {siteaccess_url( 'starter_ger' )}
4 not available: {siteaccess_url( 'nosuchsite' )|get_type}
```

Output on alpha:

```text
1 front: http://alpha.se7enx.com/site/
2 bold: http://alpha.se7enx.com/bold/
3 starter_ger: http://alpha.se7enx.com/starter_ger/
4 not available: string[0]
```

### siteaccess_menu

Generated page: [siteaccess_menu](../../examples/template-operators/siteaccess_menu.md).

| Parameter | Type | Required | Default |
|---|---|---|---|
| `user` | object | no | `null` |

- **Input:** none.
- **Returns:** a hash with `enabled` (bool), `front` (the item of the site the globe button opens) and `groups` (a list
  of hashes with `identifier`, `site` or `admin`, and `items`). An item is a hash with `identifier`, `name`
  (`SiteName`, else the identifier), `locale`, `lang`, `url`, `display_url` (without the scheme and trailing slash),
  `current` and `admin`.

It is the data of the globe button in the admin4 and admin4l headers, configured in `site.ini`
`[AdminSiteAccessMenu]` of the administration siteaccess: `Show`, `List` (`related` uses `RelatedSiteAccessList[]`,
`available` uses `AvailableSiteAccessList[]`), `HiddenSiteAccessList[]`, `AdminSiteAccessList[]` and
`ListAdministrations`. With a user, a siteaccess other than the front site is listed only when that user may log in
there (`eZUser::canLoginToSiteAccess()`); the admin templates pass `$current_user`.

Rendered on the `admin` siteaccess without a user:

```tpl
{def $menu = siteaccess_menu()}
enabled={$menu.enabled|choose( 'no', 'yes' )} key length={siteaccess_menu_key()|count_chars}
front: {$menu.front.identifier} {$menu.front.display_url}

{foreach $menu.groups as $group}
{$group.identifier}: {foreach $group.items as $item}{$item.identifier} ({$item.locale}){delimiter}, {/delimiter}{/foreach}

{/foreach}
```

Output on alpha:

```text
enabled=yes key length=32
front: site admin.alpha.se7enx.com/site
site: bold (eng-US), bold_ger (ger-DE), editor (eng-US), starter (eng-US), starter_ger (ger-DE)
```

### siteaccess_menu_key

Generated page: [siteaccess_menu_key](../../examples/template-operators/siteaccess_menu_key.md).

- **Input:** none. **Parameters:** none.
- **Returns:** an MD5 string for cache-block keys (an empty string when the menu is switched off). It changes with
  the listed siteaccesses, the administration list, the front siteaccess, the current siteaccess, and the scheme, host
  and port of the request, so a cached header shows the right menu. Line 1 of the example above shows its length. The
  key reads no setting of the listed siteaccesses themselves: a changed `SiteName` or `Locale` of one of them shows
  after the template caches of the administration are cleared.

### language_switcher

Generated page: [language_switcher](../../examples/template-operators/language_switcher.md).

| Parameter | Type | Required | Default |
|---|---|---|---|
| `destination` | string | no | empty |

- **Input:** none.
- **Returns:** what `setupTranslationSAList()` of the class in `[RegionalSettings] LanguageSwitcherClass` returns, or
  nothing when the setting is missing. The kernel's `ezpLanguageSwitcher` returns one entry per
  `[RegionalSettings] TranslationSA[<siteaccess>]=<name>`: a hash with `url` (`/switchlanguage/to/<siteaccess>/` plus
  the destination), `text` (the name) and `locale` (`ContentObjectLocale` of that siteaccess). An empty array when
  `TranslationSA` is not set.

When the destination has no `?`, the query string of the current request is appended to it. The `url` is a path for
`ezurl`, which adds the current siteaccess prefix; the `switchlanguage/to` view (`kernel/private/modules/switchlanguage/to.php`) asks the same
class for the address of the destination in the other siteaccess and redirects there. On alpha only some siteaccesses
set `TranslationSA`; rendered on `starter`:

```tpl
TranslationSA has {ezini( 'RegionalSettings', 'TranslationSA' )|count} entries

{foreach language_switcher( 'privacy-policy' ) as $sa => $lang}
{$sa}: {$lang.text} {$lang.locale} {$lang.url}

{/foreach}
```

Output on alpha:

```text
TranslationSA has 2 entries
starter: English eng-US /switchlanguage/to/starter/privacy-policy
starter_ger: Deutsch ger-DE /switchlanguage/to/starter_ger/privacy-policy
```

## Compiled templates

`siteaccess_url`, `siteaccess_menu` and `siteaccess_menu_key` have template hints with transformations: the compiler
writes a direct call (`ezpSiteAccessURL::root()` or `front()`, `ezpSiteAccessMenu::current()`,
`ezpSiteAccessMenu::key()`) into the compiled template, so they cost no operator lookup at run time and are evaluated
on every run, never at compile time. `language_switcher` has no hints and runs through `modify()`.

## Related pages

- [URL, design, INI and HTTP operators](url-operators.md)
- [Content languages](../../guides/content-languages.md)
- [All template operators (generated)](../../examples/template-operators/README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
