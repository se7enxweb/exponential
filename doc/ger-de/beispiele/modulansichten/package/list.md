---
title: /package/list
audience: developer, admin
category: examples
summary: "Die Ansicht package/list: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, package, list, package/list, module, view, URL
type: generated
generated: exp:help:examples
---
# /package/list

Eine Ansicht des Moduls `package`, definiert in `kernel/package/module.php` und ausgeführt von `kernel/package/list.php`.

## Adresse

```text
/package/list/<RepositoryID>/(offset)/<Offset>/(search)/<Search>/(type)/<Type>/(state)/<State>/(sort)/<Sort>/(dir)/<Dir>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `RepositoryID`.

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(offset)` setzt `Offset`, `(search)` setzt `Search`, `(type)` setzt `Type`, `(state)` setzt `State`, `(sort)` setzt `Sort`, `(dir)` setzt `Dir`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`package/list`](../../richtlinien/package.md#list)

Eine Richtlinie auf `package/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| Einzelne POST-Aktionen | ChangeRepositoryButton: ChangeRepository, InstallPackageButton: InstallPackage, RemovePackageButton: RemovePackage, ConfirmRemovePackageButton: ConfirmRemovePackage, CancelRemovePackageButton: CancelRemovePackage, CreatePackageButton: CreatePackage |
| Parameter der POST-Aktionen | ChangeRepository (RepositoryID: RepositoryID), RemovePackage (PackageSelection: PackageSelection), ConfirmRemovePackage (PackageSelection: PackageSelection) |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/package/list/1'|ezurl}>package/list</a>
<a href={concat( '/package/list/1/(offset)/', 1 )|ezurl}>package/list</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/package/list/1' );
```

## Siehe auch

- [Alle Ansichten von package](README.md)
- [Die Richtlinien von package](../../richtlinien/package.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/package/module.php`, `kernel/package/list.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
