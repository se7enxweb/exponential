---
title: /workflow/groupedit
audience: developer, admin
category: examples
summary: "Die Ansicht workflow/groupedit: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, workflow, groupedit, workflow/groupedit, module, view, URL
type: generated
generated: exp:help:examples
---
# /workflow/groupedit

Eine Ansicht des Moduls `workflow`, definiert in `kernel/workflow/module.php` und ausgeführt von `kernel/workflow/groupedit.php`.

## Adresse

```text
/workflow/groupedit/<WorkflowGroupID>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `WorkflowGroupID`.

## Wer sie öffnen darf

Die Ansicht nennt keine Richtlinienfunktion, daher braucht der Benutzer eine Richtlinie für das ganze Modul: `workflow/*` (jede Funktion des Moduls) in einer der Rollen, die ihm zugewiesen sind.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| UI-Kontext | `edit` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/workflow/groupedit/1'|ezurl}>workflow/groupedit</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/workflow/groupedit/1' );
```

## Siehe auch

- [Alle Ansichten von workflow](README.md)
- [Die Richtlinien von workflow](../../richtlinien/workflow.md)
- [Ein eigenes Modul mit Ansicht](../../kochbuch/eigenes-modul-mit-ansicht.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/workflow/module.php`, `kernel/workflow/groupedit.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
