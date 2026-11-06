---
title: /content/urlimport
audience: developer, admin
category: examples
summary: "Die Ansicht content/urlimport: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, content, urlimport, content/urlimport, module, view, URL
type: generated
generated: exp:help:examples
---
# /content/urlimport

Eine Ansicht des Moduls `content`, definiert in `kernel/content/module.php` und ausgeführt von `kernel/content/urlimport.php`.

## Adresse

```text
/content/urlimport/(plan)/<Plan>/(state)/<State>/(offset)/<Offset>/(report)/<Report>
```

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(plan)` setzt `Plan`, `(state)` setzt `State`, `(offset)` setzt `Offset`, `(report)` setzt `Report`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`content/urltranslator_import`](../../richtlinien/content.md#urltranslator_import)

Eine Richtlinie auf `content/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| UI-Kontext | `administration` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/content/urlimport'|ezurl}>content/urlimport</a>
<a href={concat( '/content/urlimport/(plan)/', 1 )|ezurl}>content/urlimport</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/content/urlimport' );
```

## Siehe auch

- [Alle Ansichten von content](README.md)
- [Die Richtlinien von content](../../richtlinien/content.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/module.php`, `kernel/content/urlimport.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
