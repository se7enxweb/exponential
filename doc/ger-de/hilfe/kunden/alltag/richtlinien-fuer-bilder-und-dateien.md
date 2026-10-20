---
title: Richtlinien für Bilder und Dateien
audience: customer, editor
category: customer-success
order: 15
summary: Hausregeln für Bilder und Dateien auf einer Exponential-Website, die ein Betreiber an die Redaktion weitergeben kann - welches Format wofür, was die Website mit einem Upload macht, Größen, Namen, Alternativtexte, Dokumente zum Herunterladen und Rechte - auf Grundlage der ausgelieferten Bildeinstellungen.
keywords: Bildrichtlinien, Dateirichtlinien, Formate, JPEG, PNG, WebP, SVG, Alternativtext, Dateinamen, Bildgrößen, Downloads, PDF, Hausregeln, image guidelines, file guidelines, formats, alternative text, file names, image sizes, downloads, house rules
type: help
---
# Richtlinien für Bilder und Dateien

Diese Seite ist für den Betreiber, der die Hausregeln für Bilder und Dateien schreibt, und für die Redakteure, die sie
befolgen. Übernehmen Sie die passenden Teile in Ihren eigenen Leitfaden für die Redaktion. Die Angaben dazu, was die
Website annimmt, stammen aus den ausgelieferten Einstellungen; Ihr Administrator hat sie vielleicht geändert, fragen Sie
also einmal nach und notieren Sie die Antwort unten.

## Inhalt

1. [Was mit einem Upload geschieht](#was-mit-einem-upload-geschieht)
2. [Welches Format wofür](#welches-format-wofür)
3. [Größe und Qualität](#größe-und-qualität)
4. [Namen, Titel und Alternativtexte](#namen-titel-und-alternativtexte)
5. [Dokumente zum Herunterladen](#dokumente-zum-herunterladen)
6. [Rechte und Personen](#rechte-und-personen)
7. [Die Werte Ihrer Website](#die-werte-ihrer-website)

## Was mit einem Upload geschieht

- Ein Bild wird zu einem Element *Image*, die meisten anderen Dateien zu einem Element *File*; eine MP4-Datei zu einem
  Element *Video* ([Die Medienbibliothek organisieren](../../../../en/us/help/creators/website-creators/organizing-the-media-library.md)).
- Die Website bewahrt die hochgeladene Datei als `original` auf und erzeugt kleinere **Varianten** für die Seiten, etwa
  `reference` (passt in 600 x 600), `medium` und `large`. Varianten werden konvertiert: WebP und GIF nach PNG, alles
  andere nach JPEG ([Bilder und Bildvarianten](../../../../en/us/help/creators/website-creators/images-and-image-variations.md)).
- Ein SVG wird nie so gespeichert, wie es hochgeladen wurde: Es wird zuerst von Skripten und unsicheren Teilen
  bereinigt und nie skaliert ([SVG und Bildformate](../../../../en/us/help/creators/website-creators/svg-and-image-formats.md)).

Sie müssen also nicht jede Größe von Hand vorbereiten. Laden Sie ein gutes Bild hoch; den Rest erledigt die Website.

## Welches Format wofür

Aus [SVG und Bildformate](../../../../en/us/help/creators/website-creators/svg-and-image-formats.md):

| Bild | Format |
|---|---|
| Logo, Icons, einfache Illustrationen | SVG |
| Fotos | JPEG, oder WebP/AVIF-Uploads |
| Screenshots, Bilder mit Text oder Transparenz | PNG |
| Animationen | wo möglich ein kurzes Video statt GIF |

Die ausgelieferte Liste der Bildtypen, die Redakteure hochladen dürfen, umfasst JPEG, PNG, GIF, WebP, AVIF, TIFF, BMP
und SVG (`settings/image.ini [ImageUploadSettings] AllowedMIMETypes[]`).

## Größe und Qualität

- Laden Sie Bilder hoch, die groß genug für die größte Stelle sind, an der sie erscheinen, aber nicht direkt aus der
  Kamera. Vereinbaren Sie eine maximale Breite mit Ihrem Designer, der die größte Variante Ihres Designs kennt.
- Die Upload-Seite zeigt die größte Datei, die sie annimmt, zum Beispiel *Höchstens 64 MB pro Datei*
  ([Bilder, Medien und Uploads](../../medien-und-uploads.md)). Große Dateien verlangsamen die Redaktion und die Backups.
- Wird ein Upload als zu groß abgewiesen, verkleinern Sie ihn (weniger Pixel oder stärkere Kompression), statt eine
  höhere Grenze zu verlangen.

## Namen, Titel und Alternativtexte

| Feld | Regel | Beispiel |
|---|---|---|
| Dateiname | was es zeigt, Kleinbuchstaben, Bindestriche | `town-hall-front.jpg` |
| Titel | kurz und klar; Redakteure sehen ihn in Listen und Suchen, Besucher oft als Linktext | Rathaus, Vorderseite |
| Alternativtext | was das Bild zeigt, für jemanden, der es nicht sehen kann; nur bei reiner Dekoration leer | Der Rathauseingang mit der neuen Rampe |

Die Regeln dahinter: [Bilder, Medien und Uploads](../../medien-und-uploads.md#bewährte-vorgehensweise) und die
[Checkliste zur Barrierefreiheit für Redakteure](checkliste-barrierefreiheit-fuer-redakteure.md). Bilder aus einem ZIP-Upload
werden übersprungen, wo die Website einen Alternativtext verlangt, weil eine ZIP-Datei keinen Platz dafür hat.

## Dokumente zum Herunterladen

- Ziehen Sie eine Webseite einem PDF vor, wenn Menschen die Information auf dem Smartphone lesen.
- Wenn ein PDF nötig ist, geben Sie ihm einen klaren Titel („Jahresbericht 2026 (PDF)“) und sagen Sie im Linktext,
  was es ist.
- Ersetzen Sie ein veraltetes Dokument, indem Sie sein Element *File* bearbeiten und die neue Datei hochladen, damit
  jeder Link weiter funktioniert; laden Sie keine zweite Kopie hoch.
- Legen Sie Dokumente in den Ordner, der unter [Pflege der Medienbibliothek](pflege-der-medienbibliothek.md) vereinbart ist.

## Rechte und Personen

- Laden Sie nur Bilder hoch, die Sie auf der Website verwenden dürfen; vermerken Sie die Quelle oder den Fotografen im
  Element, wenn Ihre Regeln das verlangen.
- Fragen Sie vor dem Veröffentlichen von Fotos, auf denen Personen erkennbar sind, gemäß den Regeln Ihrer
  Organisation. Dies ist keine Rechtsberatung.
- Entfernen Sie Bilder, wenn eine Lizenz endet; die Inhaltsprüfungen zeigen, wo ein Bild noch verwendet wird
  ([Die Inhaltsprüfungen](../../../../en/us/features/6.0/content-checks.md)).

## Die Werte Ihrer Website

```text
Largest upload accepted: ________   Image types accepted: ______________________
Preferred photo width: ________ px   Where images go: ______________________
Who to ask: ______________ (role)
```

## Verwandte Seiten

- [Bilder, Medien und Uploads](../../medien-und-uploads.md)
- [Pflege der Medienbibliothek](pflege-der-medienbibliothek.md)
- [Dateien und Downloads](../../../../en/us/help/creators/website-creators/files-and-downloads.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
