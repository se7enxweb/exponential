---
title: /notification/status
audience: developer, admin
category: examples
summary: "Die Ansicht notification/status: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, notification, status, notification/status, module, view, URL
type: generated
generated: exp:help:examples
---
# /notification/status

Eine Ansicht des Moduls `notification`, definiert in `kernel/notification/module.php` und ausgeführt von `kernel/notification/status.php`.

## Adresse

```text
/notification/status
```

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`notification/administrate`](../../richtlinien/notification.md#administrate)

Eine Richtlinie auf `notification/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| UI-Kontext | `administration` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/notification/status'|ezurl}>notification/status</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/notification/status' );
```

## Siehe auch

- [Alle Ansichten von notification](README.md)
- [Die Richtlinien von notification](../../richtlinien/notification.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/notification/module.php`, `kernel/notification/status.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
