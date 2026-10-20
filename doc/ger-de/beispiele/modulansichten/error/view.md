---
title: /error/view
audience: developer, admin
category: examples
summary: "Die Ansicht error/view: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Ansicht, Modul, error, view, error/view, module, URL
type: generated
generated: exp:help:examples
---
# /error/view

Eine Ansicht des Moduls `error`, definiert in `kernel/error/module.php` und ausgeführt von `kernel/error/view.php`.

## Adresse

```text
/error/view/<Type>/<Number>/<ExtraParameters>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `Type`, `Number`, `ExtraParameters`.

## Wer sie öffnen darf

Die Ansicht nennt keine Richtlinienfunktion, daher braucht der Benutzer eine Richtlinie für das ganze Modul: `error/*` (jede Funktion des Moduls) in einer der Rollen, die ihm zugewiesen sind.

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/error/view/type/1/extraparameters'|ezurl}>error/view</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/error/view/type/1/extraparameters' );
```

## Siehe auch

- [Alle Ansichten von error](README.md)
- [Die Richtlinien von error](../../richtlinien/error.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/error/module.php`, `kernel/error/view.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
