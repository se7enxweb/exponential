---
title: /exphelp/topic
audience: developer, admin
category: examples
summary: "Die Ansicht exphelp/topic: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, exphelp, topic, exphelp/topic, module, view, URL
type: generated
generated: exp:help:examples
---
# /exphelp/topic

Eine Ansicht des Moduls `exphelp`, definiert in `kernel/exphelp/module.php` und ausgeführt von `kernel/exphelp/topic.php`.

## Adresse

```text
/exphelp/topic/(step)/<Step>/(helpful)/<Helpful>
```

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(step)` setzt `Step`, `(helpful)` setzt `Helpful`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`exphelp/read`](../../richtlinien/exphelp.md#read)

Eine Richtlinie auf `exphelp/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/exphelp/topic'|ezurl}>exphelp/topic</a>
<a href={concat( '/exphelp/topic/(step)/', 1 )|ezurl}>exphelp/topic</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/exphelp/topic' );
```

## Siehe auch

- [Alle Ansichten von exphelp](README.md)
- [Die Richtlinien von exphelp](../../richtlinien/exphelp.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/exphelp/module.php`, `kernel/exphelp/topic.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
