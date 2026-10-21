---
title: /contentfinder/search
audience: developer, admin
category: examples
summary: "Die Ansicht contentfinder/search: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, contentfinder, search, contentfinder/search, module, view, URL
type: generated
generated: exp:help:examples
---
# /contentfinder/search

Eine Ansicht des Moduls `contentfinder`, definiert in `kernel/contentfinder/module.php` und ausgeführt von `kernel/contentfinder/search.php`.

## Adresse

```text
/contentfinder/search/(text)/<Text>/(type)/<Type>
```

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(text)` setzt `Text`, `(type)` setzt `Type`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`contentfinder/search`](../../richtlinien/contentfinder.md#search)

Eine Richtlinie auf `contentfinder/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/contentfinder/search'|ezurl}>contentfinder/search</a>
<a href={concat( '/contentfinder/search/(text)/', 1 )|ezurl}>contentfinder/search</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/contentfinder/search' );
```

## Siehe auch

- [Alle Ansichten von contentfinder](README.md)
- [Die Richtlinien von contentfinder](../../richtlinien/contentfinder.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/contentfinder/module.php`, `kernel/contentfinder/search.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
