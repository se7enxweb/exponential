---
title: /staticpublish/keys
audience: developer, admin
category: examples
summary: "Die Ansicht staticpublish/keys: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, staticpublish, keys, staticpublish/keys, module, view, URL
type: generated
generated: exp:help:examples
---
# /staticpublish/keys

Eine Ansicht des Moduls `staticpublish`, definiert in `kernel/staticpublish/module.php` und ausgeführt von `kernel/staticpublish/keys.php`.

## Adresse

```text
/staticpublish/keys/<TargetID>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `TargetID`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`staticpublish/setup`](../../richtlinien/staticpublish.md#setup)

Eine Richtlinie auf `staticpublish/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| Einzelne POST-Aktionen | GenerateButton: Generate |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/staticpublish/keys/1'|ezurl}>staticpublish/keys</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/staticpublish/keys/1' );
```

## Siehe auch

- [Alle Ansichten von staticpublish](README.md)
- [Die Richtlinien von staticpublish](../../richtlinien/staticpublish.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/staticpublish/module.php`, `kernel/staticpublish/keys.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
