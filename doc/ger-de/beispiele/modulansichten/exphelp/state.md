---
title: /exphelp/state
audience: developer, admin
category: examples
summary: "Die Ansicht exphelp/state: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, exphelp, state, exphelp/state, module, view, URL
type: generated
generated: exp:help:examples
---
# /exphelp/state

Eine Ansicht des Moduls `exphelp`, definiert in `kernel/exphelp/module.php` und ausgeführt von `kernel/exphelp/state.php`.

## Adresse

```text
/exphelp/state
```

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
<a href={'/exphelp/state'|ezurl}>exphelp/state</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/exphelp/state' );
```

## Siehe auch

- [Alle Ansichten von exphelp](README.md)
- [Die Richtlinien von exphelp](../../richtlinien/exphelp.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/exphelp/module.php`, `kernel/exphelp/state.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
