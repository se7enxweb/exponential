---
title: /infocollector/export
audience: developer, admin
category: examples
summary: "Die Ansicht infocollector/export: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, infocollector, export, infocollector/export, module, view, URL
type: generated
generated: exp:help:examples
---
# /infocollector/export

Eine Ansicht des Moduls `infocollector`, definiert in `kernel/infocollector/module.php` und ausgeführt von `kernel/infocollector/export.php`.

## Adresse

```text
/infocollector/export/<ObjectID>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `ObjectID`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`infocollector/read`](../../richtlinien/infocollector.md#read)

Eine Richtlinie auf `infocollector/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/infocollector/export/1'|ezurl}>infocollector/export</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/infocollector/export/1' );
```

## Siehe auch

- [Alle Ansichten von infocollector](README.md)
- [Die Richtlinien von infocollector](../../richtlinien/infocollector.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/infocollector/module.php`, `kernel/infocollector/export.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
