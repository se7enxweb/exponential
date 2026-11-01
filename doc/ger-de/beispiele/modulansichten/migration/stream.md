---
title: /migration/stream
audience: developer, admin
category: examples
summary: "Die Ansicht migration/stream: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, migration, stream, migration/stream, module, view, URL
type: generated
generated: exp:help:examples
---
# /migration/stream

Eine Ansicht des Moduls `migration`, definiert in `kernel/migration/module.php` und ausgeführt von `kernel/migration/stream.php`.

## Adresse

```text
/migration/stream/<RunID>/(offset)/<Offset>/(log)/<LogOffset>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `RunID`.

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(offset)` setzt `Offset`, `(log)` setzt `LogOffset`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`migration/read`](../../richtlinien/migration.md#read)

Eine Richtlinie auf `migration/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/migration/stream/1'|ezurl}>migration/stream</a>
<a href={concat( '/migration/stream/1/(offset)/', 1 )|ezurl}>migration/stream</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/migration/stream/1' );
```

## Siehe auch

- [Alle Ansichten von migration](README.md)
- [Die Richtlinien von migration](../../richtlinien/migration.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/migration/module.php`, `kernel/migration/stream.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
