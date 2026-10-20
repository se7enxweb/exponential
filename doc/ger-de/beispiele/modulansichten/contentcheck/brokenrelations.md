---
title: /contentcheck/brokenrelations
audience: developer, admin
category: examples
summary: "Die Ansicht contentcheck/brokenrelations: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, contentcheck, brokenrelations, contentcheck/brokenrelations, module, view, URL
type: generated
generated: exp:help:examples
---
# /contentcheck/brokenrelations

Eine Ansicht des Moduls `contentcheck`, definiert in `kernel/contentcheck/module.php` und ausgeführt von `kernel/contentcheck/brokenrelations.php`.

## Adresse

```text
/contentcheck/brokenrelations/<NodeID>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `NodeID`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`contentcheck/brokenrelations`](../../richtlinien/contentcheck.md#brokenrelations)

Eine Richtlinie auf `contentcheck/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/contentcheck/brokenrelations/1'|ezurl}>contentcheck/brokenrelations</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/contentcheck/brokenrelations/1' );
```

## Siehe auch

- [Alle Ansichten von contentcheck](README.md)
- [Die Richtlinien von contentcheck](../../richtlinien/contentcheck.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/contentcheck/module.php`, `kernel/contentcheck/brokenrelations.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
