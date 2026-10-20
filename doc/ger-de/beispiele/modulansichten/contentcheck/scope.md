---
title: /contentcheck/scope
audience: developer, admin
category: examples
summary: "Die Ansicht contentcheck/scope: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, contentcheck, scope, contentcheck/scope, module, view, URL
type: generated
generated: exp:help:examples
---
# /contentcheck/scope

Eine Ansicht des Moduls `contentcheck`, definiert in `kernel/contentcheck/module.php` und ausgeführt von `kernel/contentcheck/scope.php`.

## Adresse

```text
/contentcheck/scope/<View>/<NodeID>/<StartNodeID>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `View`, `NodeID`, `StartNodeID`.

## Wer sie öffnen darf

Die Ansicht nennt keine Richtlinienfunktion, daher braucht der Benutzer eine Richtlinie für das ganze Modul: `contentcheck/*` (jede Funktion des Moduls) in einer der Rollen, die ihm zugewiesen sind.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/contentcheck/scope/view/1/1'|ezurl}>contentcheck/scope</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/contentcheck/scope/view/1/1' );
```

## Siehe auch

- [Alle Ansichten von contentcheck](README.md)
- [Die Richtlinien von contentcheck](../../richtlinien/contentcheck.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/contentcheck/module.php`, `kernel/contentcheck/scope.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
