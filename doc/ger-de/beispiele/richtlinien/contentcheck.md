---
title: Richtlinien des Moduls contentcheck
audience: developer, admin
category: examples
summary: Die Richtlinienfunktionen des Moduls contentcheck (7) und ihre Einschränkungen.
keywords: Richtlinie, Richtlinien, Rolle, Einschränkung, Berechtigung, contentcheck, policy, policies, role, limitation, permission
type: generated
generated: exp:help:examples
---
# Richtlinien des Moduls contentcheck

Die Richtlinienfunktionen von `contentcheck` und ihre Einschränkungen, aus `$FunctionList` in `kernel/contentcheck/module.php`. In der Administration werden sie unter Rollen und Richtlinien gewählt; eine Richtlinie auf `contentcheck/*` erlaubt jede Funktion.

## brokenrelations

Geprüft von den Ansichten: [brokenrelations](../modulansichten/contentcheck/brokenrelations.md), [fixfiles](../modulansichten/contentcheck/fixfiles.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'contentcheck', 'brokenrelations' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'contentcheck', 'function', 'brokenrelations' ) )}
    ...
{/if}
```

## fixfiles

Geprüft von den Ansichten: [fixfiles](../modulansichten/contentcheck/fixfiles.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'contentcheck', 'fixfiles' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'contentcheck', 'function', 'fixfiles' ) )}
    ...
{/if}
```

## permissions

Keine Ansicht des Moduls nennt sie; Code prüft sie (`eZUser::hasAccessTo( 'contentcheck', 'permissions' )` oder der Fetch `user/has_access_to`).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'contentcheck', 'permissions' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'contentcheck', 'function', 'permissions' ) )}
    ...
{/if}
```

## quarantine

Keine Ansicht des Moduls nennt sie; Code prüft sie (`eZUser::hasAccessTo( 'contentcheck', 'quarantine' )` oder der Fetch `user/has_access_to`).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'contentcheck', 'quarantine' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'contentcheck', 'function', 'quarantine' ) )}
    ...
{/if}
```

## storage

Geprüft von den Ansichten: [storage](../modulansichten/contentcheck/storage.md), [storageaction](../modulansichten/contentcheck/storageaction.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'contentcheck', 'storage' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'contentcheck', 'function', 'storage' ) )}
    ...
{/if}
```

## trash

Geprüft von den Ansichten: [trash](../modulansichten/contentcheck/trash.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'contentcheck', 'trash' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'contentcheck', 'function', 'trash' ) )}
    ...
{/if}
```

## unreferenced

Geprüft von den Ansichten: [trash](../modulansichten/contentcheck/trash.md), [unreferenced](../modulansichten/contentcheck/unreferenced.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'contentcheck', 'unreferenced' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'contentcheck', 'function', 'unreferenced' ) )}
    ...
{/if}
```

## Siehe auch

- [Die Richtlinien aller Module](README.md)
- [Die Ansichten von contentcheck](../modulansichten/contentcheck/README.md)
- [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/contentcheck/module.php`, `kernel/classes/ezrole.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
