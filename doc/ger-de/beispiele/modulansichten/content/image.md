---
title: /content/image
audience: developer, admin
category: examples
summary: "Die Ansicht content/image: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, content, image, content/image, module, view, URL
type: generated
generated: exp:help:examples
---
# /content/image

Eine Ansicht des Moduls `content`, definiert in `kernel/content/module.php` und ausgeführt von `kernel/content/image.php`.

## Adresse

```text
/content/image/<Part1>/<Part2>/<Part3>/<Part4>/<Part5>/<Part6>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `Part1`, `Part2`, `Part3`, `Part4`, `Part5`, `Part6`.

## Wer sie öffnen darf

Aufgeführt in `settings/site.ini` `[RoleSettings] PolicyOmitList[]`: Es wird keine Richtlinie geprüft, jeder darf sie öffnen.
Der Benutzer braucht diese Richtlinienfunktion:

- [`content/read`](../../richtlinien/content.md#read)

Eine Richtlinie auf `content/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezcontentnavigationpart` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/content/image/part1/part2/part3/part4/part5/part6'|ezurl}>content/image</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/content/image/part1/part2/part3/part4/part5/part6' );
```

## Siehe auch

- [Alle Ansichten von content](README.md)
- [Die Richtlinien von content](../../richtlinien/content.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/module.php`, `kernel/content/image.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
