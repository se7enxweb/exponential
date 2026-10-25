---
title: /datatable/connections
audience: developer, admin
category: examples
summary: "Die Ansicht datatable/connections: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, datatable, connections, datatable/connections, module, view, URL
type: generated
generated: exp:help:examples
---
# /datatable/connections

Eine Ansicht des Moduls `datatable`, definiert in `kernel/datatable/module.php` und ausgeführt von `kernel/datatable/connections.php`.

## Adresse

```text
/datatable/connections
```

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`datatable/connections`](../../richtlinien/datatable.md#connections)

Eine Richtlinie auf `datatable/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/datatable/connections'|ezurl}>datatable/connections</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/datatable/connections' );
```

## Siehe auch

- [Alle Ansichten von datatable](README.md)
- [Die Richtlinien von datatable](../../richtlinien/datatable.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/datatable/module.php`, `kernel/datatable/connections.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
