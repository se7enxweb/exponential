---
title: /datatable/sync
audience: developer, admin
category: examples
summary: "Die Ansicht datatable/sync: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, datatable, sync, datatable/sync, module, view, URL
type: generated
generated: exp:help:examples
---
# /datatable/sync

Eine Ansicht des Moduls `datatable`, definiert in `kernel/datatable/module.php` und ausgeführt von `kernel/datatable/sync.php`.

## Adresse

```text
/datatable/sync/<SyncID>/(class)/<ClassName>/(content_class)/<ContentClass>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `SyncID`.

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(class)` setzt `ClassName`, `(content_class)` setzt `ContentClass`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`datatable/sync`](../../richtlinien/datatable.md#sync)

Eine Richtlinie auf `datatable/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/datatable/sync/1'|ezurl}>datatable/sync</a>
<a href={concat( '/datatable/sync/1/(class)/', 1 )|ezurl}>datatable/sync</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/datatable/sync/1' );
```

## Siehe auch

- [Alle Ansichten von datatable](README.md)
- [Die Richtlinien von datatable](../../richtlinien/datatable.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/datatable/module.php`, `kernel/datatable/sync.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
