---
title: /setup/aliasesfeed
audience: developer, admin
category: examples
summary: "Die Ansicht setup/aliasesfeed: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, setup, aliasesfeed, setup/aliasesfeed, module, view, URL
type: generated
generated: exp:help:examples
---
# /setup/aliasesfeed

Eine Ansicht des Moduls `setup`, definiert in `kernel/setup/module.php` und ausgeführt von `kernel/setup/aliasesfeed.php`.

## Adresse

```text
/setup/aliasesfeed/(job)/<Job>/(log)/<Log>/(event)/<Event>/(run)/<Run>/(alias)/<Alias>/(text)/<Text>/(lines)/<Lines>
```

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(job)` setzt `Job`, `(log)` setzt `Log`, `(event)` setzt `Event`, `(run)` setzt `Run`, `(alias)` setzt `Alias`, `(text)` setzt `Text`, `(lines)` setzt `Lines`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`setup/aliases`](../../richtlinien/setup.md#aliases)

Eine Richtlinie auf `setup/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| UI-Kontext | `ajax` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/setup/aliasesfeed'|ezurl}>setup/aliasesfeed</a>
<a href={concat( '/setup/aliasesfeed/(job)/', 1 )|ezurl}>setup/aliasesfeed</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/setup/aliasesfeed' );
```

## Siehe auch

- [Alle Ansichten von setup](README.md)
- [Die Richtlinien von setup](../../richtlinien/setup.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/setup/module.php`, `kernel/setup/aliasesfeed.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
