---
title: /contentfinder/action
audience: developer, admin
category: examples
summary: "Die Ansicht contentfinder/action: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, contentfinder, action, contentfinder/action, module, view, URL
type: generated
generated: exp:help:examples
---
# /contentfinder/action

Eine Ansicht des Moduls `contentfinder`, definiert in `kernel/contentfinder/module.php` und ausgeführt von `kernel/contentfinder/action.php`.

## Adresse

```text
/contentfinder/action
```

## Wer sie öffnen darf

Der Benutzer braucht alle diese (die Einträge werden mit „und“ verknüpft); ein Eintrag mit „oder“ braucht eine seiner Funktionen:

- [`contentfinder/search`](../../richtlinien/contentfinder.md#search)
- [`contentfinder/delete`](../../richtlinien/contentfinder.md#delete)

Eine Richtlinie auf `contentfinder/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/contentfinder/action'|ezurl}>contentfinder/action</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/contentfinder/action' );
```

## Siehe auch

- [Alle Ansichten von contentfinder](README.md)
- [Die Richtlinien von contentfinder](../../richtlinien/contentfinder.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/contentfinder/module.php`, `kernel/contentfinder/action.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
