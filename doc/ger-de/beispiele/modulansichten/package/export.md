---
title: /package/export
audience: developer, admin
category: examples
summary: "Die Ansicht package/export: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, package, export, package/export, module, view, URL
type: generated
generated: exp:help:examples
---
# /package/export

Eine Ansicht des Moduls `package`, definiert in `kernel/package/module.php` und ausgeführt von `kernel/package/export.php`.

## Adresse

```text
/package/export/<PackageName>/<RepositoryID>/(format)/<Format>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `PackageName`, `RepositoryID`.

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(format)` setzt `Format`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`package/export`](../../richtlinien/package.md#export)

Eine Richtlinie auf `package/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| UI-Kontext | `edit` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/package/export/packagename/1'|ezurl}>package/export</a>
<a href={concat( '/package/export/packagename/1/(format)/', 1 )|ezurl}>package/export</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/package/export/packagename/1' );
```

## Siehe auch

- [Alle Ansichten von package](README.md)
- [Die Richtlinien von package](../../richtlinien/package.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/package/module.php`, `kernel/package/export.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
