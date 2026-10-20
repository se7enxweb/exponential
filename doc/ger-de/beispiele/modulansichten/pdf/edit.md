---
title: /pdf/edit
audience: developer, admin
category: examples
summary: "Die Ansicht pdf/edit: ihre Adresse, Parameter und die Richtlinienfunktionen, die sie braucht."
keywords: Modul, Ansicht, pdf, edit, pdf/edit, module, view, URL
type: generated
generated: exp:help:examples
---
# /pdf/edit

Eine Ansicht des Moduls `pdf`, definiert in `kernel/pdf/module.php` und ausgeführt von `kernel/pdf/edit.php`.

## Adresse

```text
/pdf/edit/<PDFExportID>/<PDFGenerate>/(language)/<Language>
```

Geordnete Parameter, in dieser Reihenfolge nach dem Namen der Ansicht: `PDFExportID`, `PDFGenerate`.

Ungeordnete Parameter, in beliebiger Reihenfolge als `/(name)/value`: `(language)` setzt `Language`.

## Wer sie öffnen darf

Der Benutzer braucht diese Richtlinienfunktion:

- [`pdf/edit`](../../richtlinien/pdf.md#edit)

Eine Richtlinie auf `pdf/*` (jede Funktion) erlaubt sie ebenfalls; Einschränkungen der Richtlinie (Sektion, Teilbaum, Klasse ...) gelten weiterhin, wo die Ansicht sie prüft.

## Details

| Schlüssel | Wert |
|---|---|
| Navigationsbereich | `ezsetupnavigationpart` |
| UI-Kontext | `edit` |
| Einzelne POST-Aktionen | ExportPDFBrowse: BrowseSource, ExportPDFButton: Export, DiscardButton: Discard, CreateExport: CreateExport |
| Parameter der POST-Aktionen | Export (Title: Title, DisplayFrontpage: DisplayFrontpage, IntroText: IntroText, SubText: SubText, ShowFooter: ShowFooter, FooterText: FooterText, SourceNode: SourceNode, ExportType: ExportType, ClassList: ClassList, SiteAccess: SiteAccess, DestinationType: DestinationType, DestinationFile: DestinationFile), BrowseSource (Title: Title, DisplayFrontpage: DisplayFrontpage, IntroText: IntroText, SubText: SubText, ShowFooter: ShowFooter, FooterText: FooterText, ExportType: ExportType, ClassList: ClassList, SiteAccess: SiteAccess, DestinationType: DestinationType, DestinationFile: DestinationFile) |

## Beispiel

Ein Link in einem Template (`ezurl` ergänzt den Siteaccess und setzt die Adresse in Anführungszeichen); die Parameterwerte sind Platzhalter:

```tpl
<a href={'/pdf/edit/1/pdfgenerate'|ezurl}>pdf/edit</a>
<a href={concat( '/pdf/edit/1/pdfgenerate/(language)/', 1 )|ezurl}>pdf/edit</a>
```

Aus PHP die Weiterleitung, die eine Ansicht nach getaner Arbeit gibt:

```php
return $Module->redirectTo( '/pdf/edit/1/pdfgenerate' );
```

## Siehe auch

- [Alle Ansichten von pdf](README.md)
- [Die Richtlinien von pdf](../../richtlinien/pdf.md)
- [Ein eigenes Modul mit Ansicht](../../../../eng-us/examples/cookbook/custom-module-view.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/pdf/module.php`, `kernel/pdf/edit.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
