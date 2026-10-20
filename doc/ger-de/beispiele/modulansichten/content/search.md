---
title: /content/search
audience: developer, admin
category: examples
summary: "Die Ansicht content/search: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, content, search, content/search, module, view, URL
type: generated
generated: exp:help:examples
---
# /content/search

Eine Ansicht des Moduls `content`, definiert in `kernel/content/module.php` und ausgeführt von `kernel/content/search.php`.

## Adresse

```text
/content/search/(offset)/<Offset>
```

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(offset)` setzt `Offset`.

## Wer sie öffnen darf

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
<a href={'/content/search'|ezurl}>content/search</a>
<a href={concat( '/content/search/(offset)/', 1 )|ezurl}>content/search</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/content/search' );
```

## Siehe auch

- [Alle Ansichten von content](README.md)
- [Die Richtlinien von content](../../richtlinien/content.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/module.php`, `kernel/content/search.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
