---
title: /setup/preloadstream
audience: developer, admin
category: examples
summary: "Die Ansicht setup/preloadstream: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, setup, preloadstream, setup/preloadstream, module, view, URL
type: generated
generated: exp:help:examples
---
# /setup/preloadstream

Eine Ansicht des Moduls `setup`, definiert in `kernel/setup/module.php` und ausgeführt von `kernel/setup/preloadstream.php`.

## Adresse

```text
/setup/preloadstream/(maxpages)/<MaxPages>/(maxdepth)/<MaxDepth>
```

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(maxpages)` setzt `MaxPages`, `(maxdepth)` setzt `MaxDepth`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`setup/preload`](../../richtlinien/setup.md#preload)

Eine Richtlinie auf `setup/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| UI-Kontext | `ajax` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/setup/preloadstream'|ezurl}>setup/preloadstream</a>
<a href={concat( '/setup/preloadstream/(maxpages)/', 1 )|ezurl}>setup/preloadstream</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/setup/preloadstream' );
```

## Siehe auch

- [Alle Ansichten von setup](README.md)
- [Die Richtlinien von setup](../../richtlinien/setup.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/setup/module.php`, `kernel/setup/preloadstream.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
