---
title: /setup/logsfeed
audience: developer, admin
category: examples
summary: "Die Ansicht setup/logsfeed: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, setup, logsfeed, setup/logsfeed, module, view, URL
type: generated
generated: exp:help:examples
---
# /setup/logsfeed

Eine Ansicht des Moduls `setup`, definiert in `kernel/setup/module.php` und ausgeführt von `kernel/setup/logsfeed.php`.

## Adresse

```text
/setup/logsfeed/(action)/<action>/(file)/<file>/(offset)/<offset>/(ino)/<ino>/(unmask)/<unmask>/(q)/<q>/(regex)/<regex>/(case)/<case>/(levels)/<levels>/(from)/<from>/(to)/<to>/(siteaccess)/<siteaccess>/(uri)/<uri>/(ip)/<ip>/(reference)/<reference>/(scope)/<scope>/(rotations)/<rotations>/(cursor)/<cursor>
```

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(action)` setzt `action`, `(file)` setzt `file`, `(offset)` setzt `offset`, `(ino)` setzt `ino`, `(unmask)` setzt `unmask`, `(q)` setzt `q`, `(regex)` setzt `regex`, `(case)` setzt `case`, `(levels)` setzt `levels`, `(from)` setzt `from`, `(to)` setzt `to`, `(siteaccess)` setzt `siteaccess`, `(uri)` setzt `uri`, `(ip)` setzt `ip`, `(reference)` setzt `reference`, `(scope)` setzt `scope`, `(rotations)` setzt `rotations`, `(cursor)` setzt `cursor`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`setup/logs`](../../richtlinien/setup.md#logs)

Eine Richtlinie auf `setup/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| UI-Kontext | `ajax` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/setup/logsfeed'|ezurl}>setup/logsfeed</a>
<a href={concat( '/setup/logsfeed/(action)/', 1 )|ezurl}>setup/logsfeed</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/setup/logsfeed' );
```

## Siehe auch

- [Alle Ansichten von setup](README.md)
- [Die Richtlinien von setup](../../richtlinien/setup.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/setup/module.php`, `kernel/setup/logsfeed.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
