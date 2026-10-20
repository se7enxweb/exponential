---
title: /notification/addtonotification
audience: developer, admin
category: examples
summary: "Die Ansicht notification/addtonotification: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, notification, addtonotification, notification/addtonotification, module, view, URL
type: generated
generated: exp:help:examples
---
# /notification/addtonotification

Eine Ansicht des Moduls `notification`, definiert in `kernel/notification/module.php` und ausgeführt von `kernel/notification/addtonotification.php`.

## Adresse

```text
/notification/addtonotification/<ContentNodeID>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `ContentNodeID`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`notification/use`](../../richtlinien/notification.md#use)

Eine Richtlinie auf `notification/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezcontentnavigationpart` |
| UI-Kontext | `administration` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/notification/addtonotification/1'|ezurl}>notification/addtonotification</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/notification/addtonotification/1' );
```

## Siehe auch

- [Alle Ansichten von notification](README.md)
- [Die Richtlinien von notification](../../richtlinien/notification.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/notification/module.php`, `kernel/notification/addtonotification.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
