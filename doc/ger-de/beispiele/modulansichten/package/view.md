---
title: /package/view
audience: developer, admin
category: examples
summary: "Die Ansicht package/view: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Ansicht, Modul, package, view, package/view, module, URL
type: generated
generated: exp:help:examples
---
# /package/view

Eine Ansicht des Moduls `package`, definiert in `kernel/package/module.php` und ausgeführt von `kernel/package/view.php`.

## Adresse

```text
/package/view/<ViewMode>/<PackageName>/<RepositoryID>/(type)/<Type>/(search)/<Search>/(limit)/<Limit>/(offset)/<Offset>/(file)/<File>/(as)/<As>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `ViewMode`, `PackageName`, `RepositoryID`.

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(type)` setzt `Type`, `(search)` setzt `Search`, `(limit)` setzt `Limit`, `(offset)` setzt `Offset`, `(file)` setzt `File`, `(as)` setzt `As`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`package/read`](../../richtlinien/package.md#read)

Eine Richtlinie auf `package/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| Einzelne POST-Aktionen | InstallButton: Install, UninstallButton: Uninstall, ExportButton: Export, ValidateButton: Validate |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/package/view/viewmode/packagename/1'|ezurl}>package/view</a>
<a href={concat( '/package/view/viewmode/packagename/1/(type)/', 1 )|ezurl}>package/view</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/package/view/viewmode/packagename/1' );
```

## Siehe auch

- [Alle Ansichten von package](README.md)
- [Die Richtlinien von package](../../richtlinien/package.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/package/module.php`, `kernel/package/view.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
