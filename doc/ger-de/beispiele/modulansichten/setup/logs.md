---
title: /setup/logs
audience: developer, admin
category: examples
summary: "Die Ansicht setup/logs: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, setup, logs, setup/logs, module, view, URL
type: generated
generated: exp:help:examples
---
# /setup/logs

Eine Ansicht des Moduls `setup`, definiert in `kernel/setup/module.php` und ausgeführt von `kernel/setup/logs.php`.

## Adresse

```text
/setup/logs/<Mode>/<Key>/(rotation)/<rotation>/(level)/<level>/(search)/<search>/(regex)/<regex>/(case)/<case>/(from)/<from>/(to)/<to>/(siteaccess)/<siteaccess>/(uri)/<uri>/(ip)/<ip>/(reference)/<reference>/(scope)/<scope>/(rotations)/<rotations>/(cursor)/<cursor>/(offset)/<offset>/(follow)/<follow>/(wrap)/<wrap>/(unmask)/<unmask>/(done)/<done>/(ok)/<ok>/(archive)/<archive>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `Mode`, `Key`.

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(rotation)` setzt `rotation`, `(level)` setzt `level`, `(search)` setzt `search`, `(regex)` setzt `regex`, `(case)` setzt `case`, `(from)` setzt `from`, `(to)` setzt `to`, `(siteaccess)` setzt `siteaccess`, `(uri)` setzt `uri`, `(ip)` setzt `ip`, `(reference)` setzt `reference`, `(scope)` setzt `scope`, `(rotations)` setzt `rotations`, `(cursor)` setzt `cursor`, `(offset)` setzt `offset`, `(follow)` setzt `follow`, `(wrap)` setzt `wrap`, `(unmask)` setzt `unmask`, `(done)` setzt `done`, `(ok)` setzt `ok`, `(archive)` setzt `archive`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`setup/logs`](../../richtlinien/setup.md#logs)

Eine Richtlinie auf `setup/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| UI-Kontext | `administration` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/setup/logs/mode/key'|ezurl}>setup/logs</a>
<a href={concat( '/setup/logs/mode/key/(rotation)/', 1 )|ezurl}>setup/logs</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/setup/logs/mode/key' );
```

## Siehe auch

- [Alle Ansichten von setup](README.md)
- [Die Richtlinien von setup](../../richtlinien/setup.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/setup/module.php`, `kernel/setup/logs.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
