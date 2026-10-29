---
title: Richtlinien des Moduls user
audience: developer, admin
category: examples
summary: Die Richtlinienfunktionen des Moduls user (11) und ihre Einschränkungen.
keywords: Richtlinie, Richtlinien, Rolle, Einschränkung, Berechtigung, user, policy, policies, role, limitation, permission
type: generated
generated: exp:help:examples
---
# Richtlinien des Moduls user

Die Richtlinienfunktionen von `user` und ihre Einschränkungen, aus `$FunctionList` in `kernel/user/module.php`. In der Administration werden sie unter Rollen und Richtlinien gewählt; eine Richtlinie auf `user/*` erlaubt jede Funktion.

## activation

Geprüft von den Ansichten: [unactivated](../modulansichten/user/unactivated.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'user', 'activation' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'user', 'function', 'activation' ) )}
    ...
{/if}
```

## audit

Geprüft von den Ansichten: [audit](../modulansichten/user/audit.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'user', 'audit' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'user', 'function', 'audit' ) )}
    ...
{/if}
```

## deactivate

Geprüft von den Ansichten: [deactivate](../modulansichten/user/deactivate.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'user', 'deactivate' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'user', 'function', 'deactivate' ) )}
    ...
{/if}
```

## hygiene

Geprüft von den Ansichten: [hygiene](../modulansichten/user/hygiene.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'user', 'hygiene' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'user', 'function', 'hygiene' ) )}
    ...
{/if}
```

## login

Geprüft von den Ansichten: [activate](../modulansichten/user/activate.md), [edit](../modulansichten/user/edit.md), [login](../modulansichten/user/login.md), [logout](../modulansichten/user/logout.md), [megamenu](../modulansichten/user/megamenu.md), [passkeys](../modulansichten/user/passkeys.md), [preferences](../modulansichten/user/preferences.md), [registration](../modulansichten/user/registration.md), [security](../modulansichten/user/security.md), [sessions](../modulansichten/user/sessions.md).

| Einschränkung | Werte stammen aus |
|---|---|
| SiteAccess | `eZSiteAccess::siteAccessList()` |

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'user', 'login', array( 'SiteAccess' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'user', 'function', 'login' ) )}
    ...
{/if}
```

## password

Geprüft von den Ansichten: [forgotpassword](../modulansichten/user/forgotpassword.md), [password](../modulansichten/user/password.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'user', 'password' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'user', 'function', 'password' ) )}
    ...
{/if}
```

## preferences

Geprüft von den Ansichten: [setting](../modulansichten/user/setting.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'user', 'preferences' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'user', 'function', 'preferences' ) )}
    ...
{/if}
```

## register

Geprüft von den Ansichten: [register](../modulansichten/user/register.md), [success](../modulansichten/user/success.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'user', 'register' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'user', 'function', 'register' ) )}
    ...
{/if}
```

## selfedit

Keine Ansicht des Moduls nennt sie; Code prüft sie (`eZUser::hasAccessTo( 'user', 'selfedit' )` oder der Fetch `user/has_access_to`).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'user', 'selfedit' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'user', 'function', 'selfedit' ) )}
    ...
{/if}
```

## sessions

Keine Ansicht des Moduls nennt sie; Code prüft sie (`eZUser::hasAccessTo( 'user', 'sessions' )` oder der Fetch `user/has_access_to`).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'user', 'sessions' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'user', 'function', 'sessions' ) )}
    ...
{/if}
```

## sessions_manage

Geprüft von den Ansichten: [sessionreport](../modulansichten/user/sessionreport.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'user', 'sessions_manage' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'user', 'function', 'sessions_manage' ) )}
    ...
{/if}
```

## Siehe auch

- [Die Richtlinien aller Module](README.md)
- [Die Ansichten von user](../modulansichten/user/README.md)
- [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/user/module.php`, `kernel/classes/ezrole.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
