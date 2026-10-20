---
title: /workflow/processlist
audience: developer, admin
category: examples
summary: "Die Ansicht workflow/processlist: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, workflow, processlist, workflow/processlist, module, view, URL
type: generated
generated: exp:help:examples
---
# /workflow/processlist

Eine Ansicht des Moduls `workflow`, definiert in `kernel/workflow/module.php` und ausgeführt von `kernel/workflow/processlist.php`.

## Adresse

```text
/workflow/processlist/(offset)/<Offset>/(status)/<Status>
```

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(offset)` setzt `Offset`, `(status)` setzt `Status`.

## Wer sie öffnen darf

Die Ansicht nennt keine Richtlinienfunktion, daher braucht der Benutzer eine Richtlinie für das ganze Modul: `workflow/*` (jede Funktion des Moduls) in einer der Rollen, die ihm zugewiesen sind.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/workflow/processlist'|ezurl}>workflow/processlist</a>
<a href={concat( '/workflow/processlist/(offset)/', 1 )|ezurl}>workflow/processlist</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/workflow/processlist' );
```

## Siehe auch

- [Alle Ansichten von workflow](README.md)
- [Die Richtlinien von workflow](../../richtlinien/workflow.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/workflow/module.php`, `kernel/workflow/processlist.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
