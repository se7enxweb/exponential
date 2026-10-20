---
title: Pflege der Medienbibliothek
audience: customer, editor, admin
category: customer-success
order: 14
summary: Wie ein Website-Betreiber die Medienbibliothek einer Exponential-Website über die Jahre nutzbar hält - Ordnerregeln, wer wohin hochlädt, der vierteljährliche Blick auf ungenutzte Bilder und Dateien mit den Inhaltsprüfungen, fehlende Dateien und Speicher, und worum Sie den Administrator bitten sollten.
keywords: Medienbibliothek, Bilder, Dateien, Pflege, ungenutzte Dateien, nicht referenzierte Objekte, Speicher, fehlende Dateien, Ordnerstruktur, Aufräumen, vierteljährlich, media library, images, files, upkeep, unused files, unreferenced objects, storage, missing files, folder structure, clean-up, quarterly
type: help
---
# Pflege der Medienbibliothek

Diese Seite ist für den Betreiber und die Person im Team, die sich um Bilder und Dateien kümmert. Die Medienbibliothek
ist ein Teil des Inhaltsbaums für Inhalte, die von anderen Inhalten verwendet werden: Bilder, Dateien, Videos. Jeder
Upload ist ein eigenes Inhaltselement ([Bilder, Medien und Uploads](../../medien-und-uploads.md)). Ohne ein paar Regeln
füllt sie sich mit Duplikaten und Dateien, die niemand verwendet; mit ihnen finden Redakteure in Sekunden, was sie brauchen.

## Inhalt

1. [Einmal zu vereinbarende Regeln](#einmal-zu-vereinbarende-regeln)
2. [Wer was tut](#wer-was-tut)
3. [Das vierteljährliche Aufräumen](#das-vierteljährliche-aufräumen)
4. [Worum Sie Ihren Administrator bitten sollten](#worum-sie-ihren-administrator-bitten-sollten)

## Einmal zu vereinbarende Regeln

| Regel | Warum | Quelle |
|---|---|---|
| Eine Ordnerstruktur, höchstens drei Ebenen tief | der Upload-Dialog durchsucht höchstens drei Ebenen (`MaxDepth=3` in `settings/upload.ini`) | [Die Medienbibliothek organisieren](../../../../eng-us/help/creators/website-creators/organizing-the-media-library.md) |
| Ordner nach Thema, nach Jahr und Monat oder nach Website | wählen Sie die Ordnung, in der Ihre Redakteure denken | ebenda |
| Ein Ordner `Shared` für Logos und Icons | überall verwendet, von einer Person verantwortet | ebenda |
| Bilder nach dem benennen, was sie zeigen | der Name wird zum Titel des Elements und zum Vorschlag für den Alternativtext | ebenda |
| Jedes Bild mit einem Alternativtext | für Screenreader und wenn ein Bild nicht laden kann | [Bilder, Medien und Uploads](../../medien-und-uploads.md) |
| Einmal hochladen, per Beziehung wiederverwenden | eine zweite Kopie ist ein zweites Element, das aktuell gehalten werden muss | [Beziehungen und Wiederverwendung von Inhalten](../../../../eng-us/help/creators/website-creators/relations-and-reuse.md) |

Schreiben Sie die Regeln in Ihren Leitfaden für die Redaktion und in die [Richtlinien für Bilder und Dateien](richtlinien-fuer-bilder-und-dateien.md).

## Wer was tut

| Aufgabe | Wer | Wie oft |
|---|---|---|
| In den richtigen Ordner hochladen, mit Titel und Alternativtext | jeder Redakteur | bei jedem Upload |
| Die neuesten Uploads in der Medienbibliothek ansehen | Medienverantwortliche | wöchentlich |
| Das unten beschriebene Aufräumen durchführen | Medienverantwortliche mit dem Administrator | vierteljährlich |
| Entscheiden, was mit großen oder alten Dateien geschieht | Betreiber | wenn das Aufräumen sie findet |

Die Upload-Seite markiert eine Datei, die mit gleichem Namen und gleicher Größe bereits am Ort vorhanden ist, und lässt
Sie sie überspringen ([Bilder, Medien und Uploads](../../medien-und-uploads.md)). Ermutigen Sie die Redaktion, lieber zu
überspringen als „trotzdem hochzuladen“.

## Das vierteljährliche Aufräumen

Die Inhaltsprüfungen finden, was ungenutzt oder kaputt ist ([Die Inhaltsprüfungen](../../../../eng-us/features/6.0/content-checks.md)).
Sie stehen im Menü Setup und im Kontextmenü des Inhaltsbaums (**Fortgeschritten**), und auf einer neuen Installation
hat nur Administrator Zugriff darauf; erledigen Sie das also mit Ihrem Administrator oder bitten Sie um die Richtlinien.

1. **Nicht referenzierte Objekte unter Media.** Die veröffentlichten Bilder, Dateien und Medien unterhalb eines
   Knotens, die nichts verwendet: kein anderes Objekt, Layout, keine Einstellung, kein Template, keine Rolle, kein
   Shop- oder Newsletter-Eintrag. Sehen Sie die Liste durch und verschieben Sie, was wirklich ungenutzt ist, in den Papierkorb.
2. **Kaputte Beziehungen ab der Inhaltswurzel.** Sichtbare Inhalte, die auf etwas Gelöschtes, Verborgenes, im
   Papierkorb Liegendes oder nie Veröffentlichtes verweisen, mit einem Link zum Bearbeiten der Quelle. Beheben Sie jede Quelle.
3. **Fehlende Dateien.** Bilder und Dateien, deren Datei aus dem Speicher verschwunden ist
   ([Fehlende Dateien](../../../anleitungen/fehlende-dateien.md)). Laden Sie sie erneut hoch oder entfernen Sie das Element.
4. **Dateien im Speicher.** Dateien auf der Festplatte, die kein Inhalt verwendet. Sie können in eine Quarantäne
   verschoben und später von dort gelöscht werden; direkt aus dem Speicher wird nichts gelöscht.
5. **Den Papierkorb erst später leeren.** Lassen Sie Medien eine Weile im Papierkorb, falls etwas sie doch noch
   brauchte ([Archivieren und der Papierkorb](archivieren-und-der-papierkorb.md)).

> **Tipp**
> Öffnen Sie vor dem Entfernen eines Bildes seine umgekehrten Beziehungen: Die Administration warnt, bevor Sie ein
> Bild entfernen, das noch verwendet wird ([Prüfung umgekehrter Beziehungen](../../../../eng-us/features/6.0/reverse-relation-check.md)).

## Worum Sie Ihren Administrator bitten sollten

- Die Richtlinien `contentcheck/unreferenced` und `contentcheck/brokenrelations` für die Medienverantwortlichen, wenn
  diese die Prüfungen selbst ausführen sollen.
- Welche Dateitypen und -größen Uploads annehmen. Die Upload-Seite zeigt die größte Datei, die sie annimmt; die
  erlaubten Bildtypen sind in `settings/image.ini` festgelegt ([SVG und Bildformate](../../../../eng-us/help/creators/website-creators/svg-and-image-formats.md)).
- Dass `var/<site>/storage` in den Backups enthalten ist ([Backups und Wiederherstellungen](../../../../eng-us/help/creators/website-creators/backups-and-restores.md)).
- Dass der Bericht der Inhaltsprüfungen über die Kommandozeile laufen kann (`./console exp:content-check`), wenn Sie
  ihn monatlich haben möchten.

## Verwandte Seiten

- [Bilder, Medien und Uploads](../../medien-und-uploads.md)
- [Richtlinien für Bilder und Dateien](richtlinien-fuer-bilder-und-dateien.md)
- [Kaputte Links und Inhaltsprüfungen](../../../../eng-us/help/creators/website-creators/link-and-content-checks.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
