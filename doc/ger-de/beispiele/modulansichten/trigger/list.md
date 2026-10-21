---
title: /trigger/list
audience: developer, admin
category: examples
summary: "Die Ansicht trigger/list: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Auslöser, Modul, Ansicht, trigger, list, trigger/list, module, view, URL
type: generated
generated: exp:help:examples
---
# /trigger/list

Eine Ansicht des Moduls `trigger`, definiert in `kernel/trigger/module.php` und ausgeführt von `kernel/trigger/list.php`.

## Adresse

```text
/trigger/list
```

## Wer sie öffnen darf

Die Ansicht nennt keine Richtlinienfunktion, daher braucht der Benutzer eine Richtlinie für das ganze Modul: `trigger/*` (jede Funktion des Moduls) in einer der Rollen, die ihm zugewiesen sind.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| UI-Kontext | `administration` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/trigger/list'|ezurl}>trigger/list</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/trigger/list' );
```

## Siehe auch

- [Alle Ansichten von trigger](README.md)
- [Die Richtlinien von trigger](../../richtlinien/trigger.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/trigger/module.php`, `kernel/trigger/list.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
