---
title: /infocollector/view
audience: developer, admin
category: examples
summary: "Die Ansicht infocollector/view: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Ansicht, Modul, infocollector, view, infocollector/view, module, URL
type: generated
generated: exp:help:examples
---
# /infocollector/view

Eine Ansicht des Moduls `infocollector`, definiert in `kernel/infocollector/module.php` und ausgeführt von `kernel/infocollector/view.php`.

## Adresse

```text
/infocollector/view/<CollectionID>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `CollectionID`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`infocollector/read`](../../richtlinien/infocollector.md#read)

Eine Richtlinie auf `infocollector/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| UI-Kontext | `view` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/infocollector/view/1'|ezurl}>infocollector/view</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/infocollector/view/1' );
```

## Siehe auch

- [Alle Ansichten von infocollector](README.md)
- [Die Richtlinien von infocollector](../../richtlinien/infocollector.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/infocollector/module.php`, `kernel/infocollector/view.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
