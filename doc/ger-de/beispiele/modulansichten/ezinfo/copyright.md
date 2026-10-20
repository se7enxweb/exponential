---
title: /ezinfo/copyright
audience: developer, admin
category: examples
summary: "Die Ansicht ezinfo/copyright: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, ezinfo, copyright, ezinfo/copyright, module, view, URL
type: generated
generated: exp:help:examples
---
# /ezinfo/copyright

Eine Ansicht des Moduls `ezinfo`, definiert in `kernel/ezinfo/module.php` und ausgeführt von `kernel/ezinfo/copyright.php`.

## Adresse

```text
/ezinfo/copyright
```

## Wer sie öffnen darf

Aufgeführt in `settings/site.ini` `[RoleSettings] PolicyOmitList[]`: Es wird keine Richtlinie geprüft, jeder darf sie öffnen.
Der Benutzer braucht diese Richtlinienfunktion:

- [`ezinfo/read`](../../richtlinien/ezinfo.md#read)

Eine Richtlinie auf `ezinfo/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/ezinfo/copyright'|ezurl}>ezinfo/copyright</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/ezinfo/copyright' );
```

## Siehe auch

- [Alle Ansichten von ezinfo](README.md)
- [Die Richtlinien von ezinfo](../../richtlinien/ezinfo.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/ezinfo/module.php`, `kernel/ezinfo/copyright.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
