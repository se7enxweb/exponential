# The UI switch: provider API

The UI switch lets a signed-in user change a preference of the administration interface itself (the "chrome"): the
display language first, and whatever an extension adds. This page is for developers: the provider contract, the
registry and its request lifecycle, the cache variant key, the module, the fetch function, a complete example of a
provider in an extension, the rules for Exponential Velocity and the compatibility notes. What users see is in
[the feature page](../../features/6.0/ui-switch.md); the settings are in
[the settings guide](../../guides/ui-switch-settings.md).

## Overview

| File | Class | Role |
|---|---|---|
| `kernel/classes/uiswitch/expuiswitchproviderinterface.php` | `expUISwitchProviderInterface` | the provider contract |
| `kernel/classes/uiswitch/expuiswitchprovider.php` | `expUISwitchProvider` (abstract) | settings, allow-list, strict validation, cache variant key |
| `kernel/classes/uiswitch/expuiswitch.php` | `expUISwitch` | registry, request lifecycle, session storage, policy check, cache variant key, safe redirect, toolbar data |
| `kernel/classes/uiswitch/expuiswitchlanguageprovider.php` | `expUISwitchLanguageProvider` | the language provider (`language`) |
| `kernel/uiswitch/module.php`, `set.php`, `state.php` | module `uiswitch` | `uiswitch/set` (POST), `uiswitch/state` (GET, JSON); one policy function per provider |
| `kernel/private/classes/views/uiswitch/set.php`, `state.php` | `Exponential\View\Kernel\Uiswitch\Set`, `State` | the view code |
| `kernel/uiswitch/function_definition.php` | | `fetch( 'uiswitch', 'toolbar' )`, served by `expUISwitch::fetchToolbar()` |
| `design/admin4/templates/toolbar/full/admin_ui_switch.tpl`, `uiswitch/choices.tpl` | | the toolbar tool and the choice markup |
| `design/standard/templates/uiswitch/refused.tpl` | | the page of a refused change |

## The provider contract: expUISwitchProviderInterface

| Method | Contract |
|---|---|
| `identifier()` | `[a-z][a-z0-9_]{0,31}`. Also the policy function `uiswitch/<identifier>` and the settings group `uiswitch.ini [UISwitch_<identifier>]`. Must equal the identifier the registry built the provider with, or the provider is skipped. |
| `allowedValues()` | The values a user may choose on the current siteaccess, in display order: the allow-list. The default value is always one of them. |
| `label( $value )` | A label for an allowed value, for the toolbar (already translated where that makes sense). |
| `defaultValue()` | The siteaccess's own value, which the switch falls back to. Choosing it clears the switch. |
| `isValid( $value )` | True only for a member of `allowedValues()`, compared strictly: no trimming, no case folding, no normalization. Nothing from the request reaches a path, a file name or a settings name before this said yes. |
| `apply( $value )` | Applies a valid value other than the default for this request: overrides the in-memory settings and resets the statics and globals it touches, so what was built from the old settings is built again. Returns whether it applied. |
| `reset()` | Undoes what `apply()` left behind in an earlier request of the same process (statics, globals). Called at the start of the next request on a persistent worker. |
| `currentValue()` | The value in effect for this request. |
| `cacheVariantKey( $value )` | `''` for the default value (no cache file name changes), otherwise a short string of `[A-Za-z0-9_.-]` that separates caches built with the value. |

## The base class: expUISwitchProvider

Extend `expUISwitchProvider` rather than implementing the interface from scratch. It is constructed by the registry
as `new $class( $identifier, $settings )`, where `$settings` is the group `[UISwitch_<identifier>]`, and lives for one
request. A provider implements `defaultValue()` and `apply()`, and overrides `reset()` when `apply()` touches statics
(calling `parent::reset()`).

| Member | What it does |
|---|---|
| `VALUE_PATTERN` | `/^[A-Za-z0-9][A-Za-z0-9_.@-]{0,63}$/`: what a configured or stored value may look like at all. |
| `setting( $name, $default = null )` | One setting of the provider's group. |
| `configuredValues()` | `AllowedValues[]` as configured: the well-formed ones only (`VALUE_PATTERN`), each once. |
| `isAvailable( $value )` (protected) | Whether a well-formed configured value can be used on this installation (its files exist, ...). `true` by default; the language provider checks the locale and translation files. |
| `allowedValues()` | The default value first, then each configured value that `isAvailable()`; computed once per instance. |
| `isValid( $value )` | A non-empty string strictly in `allowedValues()`. |
| `label( $value )` | The value itself; override for a readable name. |
| `title()` | The name of the choice: `[UISwitch_<identifier>] Title`, translated in the context `kernel/uiswitch`, or the identifier with the first letter in upper case. |
| `labelLanguage( $value )` | The language a value's label is written in, as an HTML `lang` value, or `''` for the page's language. The language provider returns `de-DE` for `ger-DE`. |
| `remembersPreference()` | `RememberPreference=enabled` in the group. |
| `currentValue()` | `$this->current` when `apply()` set it, else `defaultValue()`. |
| `cacheVariantKey( $value )` | `''` for the default or an invalid value, else `<identifier>-<value>` with other characters replaced by `_` (for example `language-ger-DE`). |
| `reset()` | Sets `$this->current` back to null. |

## The registry: expUISwitch

### Providers

`providers()` builds the providers of this request from `uiswitch.ini`: for each well-formed identifier of
`[UISwitchSettings] ProviderList[]` with a group `[UISwitch_<identifier>]`, it takes `Class=` and skips, with a debug
warning, a class that does not exist (`class_exists`, so a Velocity worker started before the class was deployed
never fails on it) or does not implement `expUISwitchProviderInterface`. A group with `Enabled=false` (or `disabled`,
`0`) is skipped silently. With `[UISwitchSettings] Enabled=false` there are no providers. `provider( $identifier )`
returns one, or null.

### Request lifecycle

`ezpKernelWeb::requestInit()` calls the registry right after the session start and the database check, before the
login pre-check, the `request/input` event, `eZLocale::instance()`, the page language and any template:

```php
if ( class_exists( 'expUISwitch' ) )
{
    expUISwitch::beginRequest();
    if ( $this->siteBasics['session-required'] && empty( $this->siteBasics['validity-check-required'] ) )
        expUISwitch::applyRequest();
}
```

- `beginRequest()` calls `reset()` on every provider that applied a value earlier in this process, forgets the
  providers and the applied values, and sets `$GLOBALS['expUISwitchVariantKey'] = ''`.
- `applyRequest()` never starts a session and never throws. It applies nothing unless the framework is enabled, the
  current siteaccess is a switch siteaccess (`isSwitchSiteAccess()`), the session has started and the current user
  is signed in. It reads the stored values of this user and siteaccess and hands them to `applyValues()`. A stored
  value that `applyValues()` did not apply is dropped from the session.
- `applyValues( $stored, $user )` checks every value again, in every request: the user must hold
  `uiswitch/<identifier>` now (`canUse()`), the value must pass `isValid()` and must not be the default. A provider
  with `RememberPreference=enabled` and nothing in the session takes the value from the user's preferences, under the
  same checks. Each value that passes goes to `apply()`; at the end the cache variant key is stored in
  `$GLOBALS['expUISwitchVariantKey']`.
- `appliedValues()` returns what was applied in this request.

The setup wizard (validity check) and requests without a session do not apply anything.

### Session format

The session variable `expUISwitch` (`expUISwitch::SESSION_VARIABLE`) holds:

```php
array(
    'user'   => 14,                                  // the user the values belong to
    'values' => array(
        'admin'  => array( 'language' => 'ger-DE' ),  // siteaccess => provider => value
        'editor' => array( 'language' => 'eng-US' ),
    ),
)
```

Values of another user are ignored and dropped on the next write, so a session that changes user never carries a
choice over. Only well-formed identifiers and values (`IDENTIFIER_PATTERN`, `VALUE_PATTERN`) are read, and only
values a provider accepted are ever stored. When nothing is left, the variable is removed. Remembered values are the
preferences `exp_uiswitch_<identifier>` (`PREFERENCE_PREFIX`).

### Setting a value

`set( $identifier, $value )` returns `ok`, `unavailable` (framework off, not a switch siteaccess, no such provider),
`denied` (no policy) or `invalid` (not in the allow-list). On `ok` it stores the value for the current user and
siteaccess (the default value clears it), updates the preference when the provider remembers it, and records the
audit event `access.uiswitch.change` with the value and siteaccess before and after, the user as object and the
provider as target.

### Other helpers

| Method | What it does |
|---|---|
| `isEnabled()` | `[UISwitchSettings] Enabled` (default true). |
| `isSwitchSiteAccess( $siteaccess = null )` | In `SiteAccessList[]` when that is not empty, otherwise whether the siteaccess's design list has a design whose name starts with `admin`. |
| `canUse( $identifier, $user = null )` | Signed in and holding `uiswitch/<identifier>`. |
| `providerIdentifiers()`, `policyFunctions()` | The identifiers of `ProviderList[]`; the policy functions (always including `language`). |
| `isSameSiteRequest( $secFetchSite )` | False only for a `Sec-Fetch-Site` other than empty, `same-origin` or `same-site`. |
| `safeRedirect( $uri, $fallback )` | A local path (`/x/y`), or the fallback for `//host`, a scheme, a backslash, `.` or `..` segments, control characters and more than 2048 characters. |
| `toolbar()`, `fetchToolbar()` | The toolbar data (below). |
| `setSettings( $ini )` | Uses other settings than `uiswitch.ini` (tests). |

## The cache variant key

`expUISwitch::cacheVariantKey()` is `''` when nothing is applied, otherwise the applied providers' keys sorted by
identifier and joined with `.` (`language-ger-DE`, or `design-admin3.language-ger-DE` with two providers). It is kept
in `$GLOBALS['expUISwitchVariantKey']`, so generated cache code can read it without loading the class. It is used in
four places:

| Where | What it does |
|---|---|
| `{cache-block}` (`lib/eztemplate/classes/eztemplatecachefunction.php`) | Appended to the siteaccess part of the cache key, in compiled and in processed templates, read at run time: the cached markup of a user with a choice is stored apart. |
| The content view cache (`kernel/classes/eznodeviewfunctions.php`) | Added to the cache hash as the key `uiswitch`. |
| Compiled templates (`eZTemplateCompiler::compilationFilename()`) | Appended to the compiled file's key, so a provider that changes what a template resolves to (a design) gets its own compiled files. The language already varies the compiled names through the locale. |
| The role-aware HTTP page cache (`kernel/private/classes/httpcache/ezphttpcachelistener.php`) | A page rendered with a key is not stored; the reason given is `ui switch`. |

## The module uiswitch

| View | Method | Parameters | Answer |
|---|---|---|---|
| `uiswitch/set` | POST only | `Provider`, `Value`, `RedirectURI` | A redirect to `RedirectURI` when it is a local path, otherwise to `/content/dashboard`. Refusals render `design:uiswitch/refused.tpl` with the status 405 (`method`, with `Allow: POST`), 403 (`cross-site`, `denied`), 400 (`invalid`) or 404 (`unavailable`). The kernel refuses a POST without the form token (403) before the view runs. |
| `uiswitch/state` | GET | none | JSON `{ "siteaccess": ..., "providers": [ ...toolbar() ] }` with `Cache-Control: private, no-store`. |

Policy functions are built in `module.php` from `expUISwitch::policyFunctions()`, one per identifier of
`ProviderList[]` (and `language` always), with no limitations. An extension's provider therefore gets its function
`uiswitch/<identifier>` without a change in the kernel. The views require any one of them
(`language or <id2> ...`); `set` checks the single provider again. `Set::decide()` and `Set::status()` are pure and
tested on their own.

## fetch( 'uiswitch', 'toolbar' )

Templates never name the class. The fetch returns a list, empty when the framework is off, the siteaccess is not a
switch siteaccess or the user may use no provider. A provider with fewer than two allowed values is left out.

```
array(
  array(
    'identifier'    => 'language',
    'title'         => 'Interface language',
    'current'       => 'eng-GB',
    'current_label' => 'English (United Kingdom)',
    'default'       => 'eng-GB',
    'values'        => array(
      array( 'value' => 'eng-GB', 'label' => 'English (United Kingdom)', 'current' => true,  'default' => true,  'lang' => 'en-GB' ),
      array( 'value' => 'ger-DE', 'label' => 'Deutsch (Deutschland)',    'current' => false, 'default' => false, 'lang' => 'de-DE' ),
    ),
  ),
)
```

`design:uiswitch/choices.tpl` renders it (parameters `choices` and `prefix`, an id prefix unique on the page); the
toolbar tool, the admin4l block and the AdminUI side bar all include it.

## Example: a provider in an extension

An extension `myext` that lets users choose a density of the administration lists.

**1. The class**, `extension/myext/classes/myextuiswitchdensityprovider.php`:

```php
<?php

class myextUISwitchDensityProvider extends expUISwitchProvider
{
    /** the siteaccess's own value, read before any apply() */
    public function defaultValue()
    {
        return (string)eZINI::instance( 'myext.ini' )->variable( 'ListSettings', 'Density' );
    }

    public function label( $value )
    {
        return ezpI18n::tr( 'extension/myext', ucfirst( $value ) );
    }

    public function apply( $value )
    {
        if ( !$this->isValid( $value ) || $value === $this->defaultValue() )
            return false;
        eZINI::instance( 'myext.ini' )->setVariable( 'ListSettings', 'Density', $value );
        myextListRenderer::resetStatics();   // anything built from the old value
        $this->current = $value;
        return true;
    }

    public function reset()
    {
        myextListRenderer::resetStatics();
        parent::reset();
    }
}
```

**2. The settings**, `extension/myext/settings/uiswitch.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?

[UISwitchSettings]
ProviderList[]=density

[UISwitch_density]
Class=myextUISwitchDensityProvider
Enabled=true
Title=List density
AllowedValues[]
AllowedValues[]=compact
AllowedValues[]=comfortable
RememberPreference=disabled
*/ ?>
```

**3. Deploy the class before the settings name it.** Regenerate the autoloads and restart the workers first, then
activate the settings:

```bash
./console exp:velocity deploy --allow-root-user            # extension autoloads, caches, PHP-FPM, Velocity
./console exp:velocity deploy --kernel --allow-root-user   # when the class lives in kernel/ or lib/
```

A worker that does not know the class skips the provider with a warning rather than failing, but it shows no choice
until it is restarted. Then activate the extension's settings and deploy once more: the INI cache is cleared
(`--clear-tag=ini`) and the workers restart, so the module's policy functions include `density`.

**4. Grant the policy** `uiswitch/density` to the roles that may use it (*Roles and policies*, module `uiswitch`,
function `density`). The toolbar shows the choice from then on; no template change is needed.

Values in `AllowedValues[]` must match `VALUE_PATTERN`. When a value names a file or a design, check in
`isAvailable()` that it exists, and never build a path from a value before `isValid()` accepted it.

## Exponential Velocity

Velocity workers are persistent: statics and globals survive from one request to the next.

- Everything `apply()` changes in a static or a global must be undone in `reset()`. `beginRequest()` calls `reset()`
  on every provider that applied a value in this process before anything else happens in the next request.
- `apply()` changes settings only in memory, on the `eZINI` instances of this request; never write a settings file.
- After changing a setting, reset what was built from it. The language provider calls
  `eZTranslatorManager::resetTranslations()` (the translator, the default `eZLocale`, the translation cache globals)
  in both `apply()` and `reset()`.
- Guard every reference to a new class with `class_exists()` where a running worker may not have it yet, as the
  kernel does in `ezpKernelWeb::requestInit()` and `module.php`.

## Backward compatibility

- **No cache name changes without a choice.** When no switch is applied the variant key is `''`, and the content view
  cache, `{cache-block}` keys, compiled template names and the HTTP page cache behave exactly as before. Installations
  where nobody uses the switch keep their caches.
- **Content languages are untouched.** Only `[RegionalSettings] Locale` (and `TextTranslation=enabled`) change, in
  memory, for the user who chose.
- **New module and policy.** `settings/module.ini` lists `uiswitch`. Roles other than Administrator need the policy;
  new installations give `uiswitch/language` to Editor (`kernel/sql/*/cleandata.sql`, policy 332, and the multi-site
  installers).
- **The toolbar entry is opt-in on existing installations.** It appears only after `admin_ui_switch` is added to
  `toolbar.ini` (or the block to the admin layout); see [the settings guide](../../guides/ui-switch-settings.md#4-the-toolbar-on-an-existing-installation).
- **bcswitchadminlanguage is replaced.** The extension `bcswitchadminlanguage` (module `switchadminlanguage`, a kernel
  override of `ezpkernelweb.php`) did the same through a shared admin siteaccess. Do not install it alongside the UI
  switch: its kernel override would replace the hook in `ezpKernelWeb::requestInit()`. Remove it, and grant
  `uiswitch/language` where its policy was granted.

## Tests

`tests/tests/kernel/classes/uiswitch/expUISwitchTest.php` (DB-less, 18 tests): the shipped settings, the registry
(missing classes, non-providers, disabled and ill-formed entries), policy functions and the module's function list,
the strict allow-list, the language provider's locale and translation checks and its in-memory apply, the per-provider
policy check, switch siteaccesses, the session format, re-checks on apply, the reset at the next request, variant keys
and compiled template names, local redirects, and the refusals of `uiswitch/set`.
