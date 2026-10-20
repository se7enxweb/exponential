---
title: /workflow/workflowlist
audience: developer, admin
category: examples
summary: "Die Ansicht workflow/workflowlist: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, workflow, workflowlist, workflow/workflowlist, module, view, URL
type: generated
generated: exp:help:examples
---
# /workflow/workflowlist

Eine Ansicht des Moduls `workflow`, definiert in `kernel/workflow/module.php` und ausgeführt von `kernel/workflow/workflowlist.php`.

## Adresse

```text
/workflow/workflowlist/<GroupID>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `GroupID`.

## Wer sie öffnen darf

Die Ansicht nennt keine Richtlinienfunktion, daher braucht der Benutzer eine Richtlinie für das ganze Modul: `workflow/*` (jede Funktion des Moduls) in einer der Rollen, die ihm zugewiesen sind.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/workflow/workflowlist/1'|ezurl}>workflow/workflowlist</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/workflow/workflowlist/1' );
```

## Siehe auch

- [Alle Ansichten von workflow](README.md)
- [Die Richtlinien von workflow](../../richtlinien/workflow.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/workflow/module.php`, `kernel/workflow/workflowlist.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
