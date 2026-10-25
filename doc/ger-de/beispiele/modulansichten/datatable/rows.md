---
title: /datatable/rows
audience: developer, admin
category: examples
summary: "Die Ansicht datatable/rows: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, datatable, rows, datatable/rows, module, view, URL
type: generated
generated: exp:help:examples
---
# /datatable/rows

Eine Ansicht des Moduls `datatable`, definiert in `kernel/datatable/module.php` und ausgeführt von `kernel/datatable/rows.php`.

## Adresse

```text
/datatable/rows/<ClassName>/(offset)/<Offset>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `ClassName`.

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(offset)` setzt `Offset`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`datatable/read`](../../richtlinien/datatable.md#read)

Eine Richtlinie auf `datatable/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/datatable/rows/classname'|ezurl}>datatable/rows</a>
<a href={concat( '/datatable/rows/classname/(offset)/', 1 )|ezurl}>datatable/rows</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/datatable/rows/classname' );
```

## Siehe auch

- [Alle Ansichten von datatable](README.md)
- [Die Richtlinien von datatable](../../richtlinien/datatable.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/datatable/module.php`, `kernel/datatable/rows.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
