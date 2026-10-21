---
title: /ezinfo/is_alive
audience: developer, admin
category: examples
summary: "Die Ansicht ezinfo/is_alive: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, ezinfo, is_alive, ezinfo/is_alive, module, view, URL
type: generated
generated: exp:help:examples
---
# /ezinfo/is_alive

Eine Ansicht des Moduls `ezinfo`, definiert in `kernel/ezinfo/module.php` und ausgeführt von `kernel/ezinfo/isalive.php`.

## Adresse

```text
/ezinfo/is_alive
```

## Wer sie öffnen darf

Aufgeführt in `settings/site.ini` `[RoleSettings] PolicyOmitList[]`: Es wird keine Richtlinie geprüft, jeder darf sie öffnen.
Der Benutzer braucht diese Richtlinienfunktion:

- [`ezinfo/read`](../../richtlinien/ezinfo.md#read)

Eine Richtlinie auf `ezinfo/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/ezinfo/is_alive'|ezurl}>ezinfo/is_alive</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/ezinfo/is_alive' );
```

## Siehe auch

- [Alle Ansichten von ezinfo](README.md)
- [Die Richtlinien von ezinfo](../../richtlinien/ezinfo.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/ezinfo/module.php`, `kernel/ezinfo/isalive.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
