---
title: /workflow/down
audience: developer, admin
category: examples
summary: "Die Ansicht workflow/down: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, workflow, down, workflow/down, module, view, URL
type: generated
generated: exp:help:examples
---
# /workflow/down

Eine Ansicht des Moduls `workflow`, definiert in `kernel/workflow/module.php` und ausgeführt von `kernel/workflow/edit.php`.

## Adresse

```text
/workflow/down/<WorkflowID>/<EventID>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `WorkflowID`, `EventID`.

## Wer sie öffnen darf

Die Ansicht nennt keine Richtlinienfunktion, daher braucht der Benutzer eine Richtlinie für das ganze Modul: `workflow/*` (jede Funktion des Moduls) in einer der Rollen, die ihm zugewiesen sind.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/workflow/down/1/1'|ezurl}>workflow/down</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/workflow/down/1/1' );
```

## Siehe auch

- [Alle Ansichten von workflow](README.md)
- [Die Richtlinien von workflow](../../richtlinien/workflow.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/workflow/module.php`, `kernel/workflow/edit.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
