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
/package/compare/<PackageName>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `PackageName`.

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
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/package/compare/packagename' );
```

## Siehe auch

- [Alle Ansichten von package](README.md)
- [Die Richtlinien von package](../../richtlinien/package.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/package/module.php`, `kernel/package/compare.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
