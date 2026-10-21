---
title: /ezoe/upload
audience: developer, admin
category: examples
summary: "Die Ansicht ezoe/upload: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, ezoe, upload, ezoe/upload, module, view, URL
type: generated
generated: exp:help:examples
---
# /ezoe/upload

Eine Ansicht des Moduls `ezoe`, definiert in `extension/ezoe/modules/ezoe/module.php` und ausgeführt von `extension/ezoe/modules/ezoe/upload.php`.

## Adresse

```text
/ezoe/upload/<ObjectID>/<ObjectVersion>/<ContentType>/<ForcedUpload>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `ObjectID`, `ObjectVersion`, `ContentType`, `ForcedUpload`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`ezoe/editor`](../../richtlinien/ezoe.md#editor)

Eine Richtlinie auf `ezoe/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| UI-Kontext | `edit` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/ezoe/upload/1/1/contenttype/forcedupload'|ezurl}>ezoe/upload</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/ezoe/upload/1/1/contenttype/forcedupload' );
```

## Siehe auch

- [Alle Ansichten von ezoe](README.md)
- [Die Richtlinien von ezoe](../../richtlinien/ezoe.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `extension/ezoe/modules/ezoe/module.php`, `extension/ezoe/modules/ezoe/upload.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
