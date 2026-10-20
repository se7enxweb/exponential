---
title: /pdf/list
audience: developer, admin
category: examples
summary: "Die Ansicht pdf/list: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, pdf, list, pdf/list, module, view, URL
type: generated
generated: exp:help:examples
---
# /pdf/list

Eine Ansicht des Moduls `pdf`, definiert in `kernel/pdf/module.php` und ausgeführt von `kernel/pdf/list.php`.

## Adresse

```text
/pdf/list/(language)/<Language>
```

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(language)` setzt `Language`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`pdf/edit`](../../richtlinien/pdf.md#edit)

Eine Richtlinie auf `pdf/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| Einzelne POST-Aktionen | NewPDFExport: NewExport, RemoveExportButton: RemoveExport |
| Parameter der POST-Aktionen | RemoveExport (DeleteIDArray: DeleteIDArray) |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/pdf/list'|ezurl}>pdf/list</a>
<a href={concat( '/pdf/list/(language)/', 1 )|ezurl}>pdf/list</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/pdf/list' );
```

## Siehe auch

- [Alle Ansichten von pdf](README.md)
- [Die Richtlinien von pdf](../../richtlinien/pdf.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/pdf/module.php`, `kernel/pdf/list.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
