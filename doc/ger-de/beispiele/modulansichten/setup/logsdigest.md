---
title: /setup/logsdigest
audience: developer, admin
category: examples
summary: "Die Ansicht setup/logsdigest: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, setup, logsdigest, setup/logsdigest, module, view, URL
type: generated
generated: exp:help:examples
---
# /setup/logsdigest

Eine Ansicht des Moduls `setup`, definiert in `kernel/setup/module.php` und ausgeführt von `kernel/setup/logsdigest.php`.

## Adresse

```text
/setup/logsdigest/<Key>/(hours)/<hours>/(known)/<known>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `Key`.

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(hours)` setzt `hours`, `(known)` setzt `known`.

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
<a href={'/setup/logsdigest/key'|ezurl}>setup/logsdigest</a>
<a href={concat( '/setup/logsdigest/key/(hours)/', 1 )|ezurl}>setup/logsdigest</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/setup/logsdigest/key' );
```

## Siehe auch

- [Alle Ansichten von setup](README.md)
- [Die Richtlinien von setup](../../richtlinien/setup.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/setup/module.php`, `kernel/setup/logsdigest.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
