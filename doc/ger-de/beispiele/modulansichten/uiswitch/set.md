---
title: /uiswitch/set
audience: developer, admin
category: examples
summary: "Die Ansicht uiswitch/set: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, uiswitch, set, uiswitch/set, module, view, URL
type: generated
generated: exp:help:examples
---
# /uiswitch/set

Eine Ansicht des Moduls `uiswitch`, definiert in `kernel/uiswitch/module.php` und ausgeführt von `kernel/uiswitch/set.php`.

## Adresse

```text
/uiswitch/set
```

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`uiswitch/language`](../../richtlinien/uiswitch.md#language) oder [`uiswitch/design`](../../richtlinien/uiswitch.md#design)

Eine Richtlinie auf `uiswitch/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezmynavigationpart` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/uiswitch/set'|ezurl}>uiswitch/set</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/uiswitch/set' );
```

## Siehe auch

- [Alle Ansichten von uiswitch](README.md)
- [Die Richtlinien von uiswitch](../../richtlinien/uiswitch.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/uiswitch/module.php`, `kernel/uiswitch/set.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
