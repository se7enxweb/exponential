---
title: /staticpublish/log
audience: developer, admin
category: examples
summary: "Die Ansicht staticpublish/log: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, staticpublish, log, staticpublish/log, module, view, URL
type: generated
generated: exp:help:examples
---
# /staticpublish/log

Eine Ansicht des Moduls `staticpublish`, definiert in `kernel/staticpublish/module.php` und ausgeführt von `kernel/staticpublish/log.php`.

## Adresse

```text
/staticpublish/log/<RunID>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `RunID`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`staticpublish/read`](../../richtlinien/staticpublish.md#read)

Eine Richtlinie auf `staticpublish/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/staticpublish/log/1'|ezurl}>staticpublish/log</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/staticpublish/log/1' );
```

## Siehe auch

- [Alle Ansichten von staticpublish](README.md)
- [Die Richtlinien von staticpublish](../../richtlinien/staticpublish.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/staticpublish/module.php`, `kernel/staticpublish/log.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
