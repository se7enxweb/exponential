---
title: /package/compare
audience: developer, admin
category: examples
summary: "Die Ansicht package/compare: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, package, compare, package/compare, module, view, URL
type: generated
generated: exp:help:examples
---
# /package/compare

Eine Ansicht des Moduls `package`, definiert in `kernel/package/module.php` und ausgeführt von `kernel/package/compare.php`.

## Adresse

```text
/package/compare/<PackageName>/(filter)/<Filter>/(class)/<Class>/(search)/<Search>/(sort)/<Sort>/(dir)/<Dir>/(limit)/<Limit>/(offset)/<Offset>/(item)/<Item>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `PackageName`.

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(filter)` setzt `Filter`, `(class)` setzt `Class`, `(search)` setzt `Search`, `(sort)` setzt `Sort`, `(dir)` setzt `Dir`, `(limit)` setzt `Limit`, `(offset)` setzt `Offset`, `(item)` setzt `Item`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`package/read`](../../richtlinien/package.md#read)

Eine Richtlinie auf `package/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| Einzelne POST-Aktionen | CompareRefreshButton: Refresh, ImportItemButton: ImportItem, ImportSelectedButton: ImportSelected, ImportFilterButton: ImportFilter, ImportViewedButton: ImportViewed, ConfirmImportButton: ConfirmImport, CancelImportButton: CancelImport |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/package/compare/packagename'|ezurl}>package/compare</a>
<a href={concat( '/package/compare/packagename/(filter)/', 1 )|ezurl}>package/compare</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/package/compare/packagename' );
```

## Siehe auch

- [Alle Ansichten von package](README.md)
- [Die Richtlinien von package](../../richtlinien/package.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/package/module.php`, `kernel/package/compare.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
