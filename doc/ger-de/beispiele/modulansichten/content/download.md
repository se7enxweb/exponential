---
title: /content/download
audience: developer, admin
category: examples
summary: "Die Ansicht content/download: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, content, download, content/download, module, view, URL
type: generated
generated: exp:help:examples
---
# /content/download

Eine Ansicht des Moduls `content`, definiert in `kernel/content/module.php` und ausgeführt von `kernel/content/download.php`.

## Adresse

```text
/content/download/<ContentObjectID>/<ContentObjectAttributeID>/(version)/<Version>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `ContentObjectID`, `ContentObjectAttributeID`.

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(version)` setzt `Version`.

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
<a href={'/content/download/1/1'|ezurl}>content/download</a>
<a href={concat( '/content/download/1/1/(version)/', 1 )|ezurl}>content/download</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/content/download/1/1' );
```

## Siehe auch

- [Alle Ansichten von content](README.md)
- [Die Richtlinien von content](../../richtlinien/content.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/module.php`, `kernel/content/download.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
