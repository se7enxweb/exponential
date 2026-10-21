---
title: OpenDocument-Import und -Export
audience: government, admin, editor, website-creator
category: public-sector
order: 80
summary: Wie die Erweiterung ezodf OpenDocument-Writer-Dateien als Inhalte in Exponential importiert und Inhalte zurück nach Writer exportiert - was sie braucht (LibreOffice oder OpenOffice als Konverter), ihre Einstellungen, die Views und ihre dokumentierten Grenzen - damit in einer Office-Suite geschriebene Dokumente zu barrierefreien Webseiten werden.
keywords: OpenDocument, ODT, LibreOffice, OpenOffice, Import, Export, Writer, Konverter, offene Standards, ezodf, import, export, converter, odf.ini, RegisteredClassArray, ezwt, open standards
type: help
---
# OpenDocument-Import und -Export

Diese Seite richtet sich an Administratoren, Redakteure und Site-Ersteller in Behörden, die mit Office-Suiten
schreiben. Viele Dokumente werden in LibreOffice oder OpenOffice Writer entworfen; sie als Webseiten statt nur als PDF
zu veröffentlichen, macht sie leichter lesbar und auffindbar. Die Erweiterung `ezodf` importiert ein Writer-Dokument
als Inhaltsobjekt und exportiert Objekte zurück nach Writer. Quelle: [ezodf](../../../../../eng-us/features/6.0/extensions/ezodf.md).
Die Standardinstallation aktiviert die Erweiterung
([Erweiterungen, die es aktiviert](../../../seitenpakete/sevenx-multisite/erweiterungen-die-sevenx-multisite-aktiviert.md)).

## Inhalt

1. [Was sie tut](#was-sie-tut)
2. [Was sie braucht](#was-sie-braucht)
3. [Einstellungen](#einstellungen)
4. [Verwendung](#verwendung)
5. [Grenzen](#grenzen)

## Was sie tut

| Richtung | Ergebnis |
|---|---|
| Import | ein Redakteur lädt eine `.odt`-Datei hoch; ein Inhaltsobjekt wird angelegt: der Titel und ein Text mit Überschriften, Absätzen, Listen, Tabellen und Bildern, auf Klassenattribute abgebildet |
| Export | ein Objekt wird mithilfe einer Vorlage in ein Writer-Dokument exportiert |
| Bibliothek | eine allgemeine Bibliothek, um aus eigenen Modulen Writer-Dokumente zu erzeugen |

OpenDocument ist ein offenes, standardisiertes Office-Format; seine Verwendung passt zu den Richtlinien für offene
Standards, die viele Behörden haben.

## Was sie braucht

- OpenOffice oder LibreOffice 2.4 oder neuer, als Konverterdienst laufend, den die Erweiterung erreicht
  (`OOConverterAddress`, `OOConverterPort`, voreingestellt `127.0.0.1:9090`);
- ein `zip`-Programm (`ZipPath`) und einen temporären Ordner (`TmpDir`).

Eine Office-Suite als Dienst auf dem Webserver zu betreiben, ist eine Entscheidung des Betriebs; fragen Sie Ihr
Hosting-Team.

## Einstellungen

`odf.ini` (überschreiben in `settings/override/odf.ini.append.php`):

| Block | Schlüssel | Voreinstellung | Bedeutung |
|---|---|---|---|
| `ODFImport` | `DefaultImportClass` | `article` | die Klasse, die ein Import anlegt |
| `ODFImport` | `RegisteredClassArray[]` | article, folder, image, documentation_page, blog_post | Klassen, die ein Import anlegen darf |
| `ODFImport` | `PlaceImagesInMedia`, `ImportedImagesMediaNodeName` | `false`, `Imported images` | wohin Bilder kommen |
| `<class>` | `DefaultImportTitleAttribute`, `DefaultImportBodyAttribute`, `Attribute[<odf field>]` | je Klasse | Zuordnung zu Attributen |
| `ODFExport` | `UseTemplate`, `TemplateName` | `true`, eine Writer-Vorlage | Exportvorlage |

Um in eine eigene Klasse zu importieren (eine Klasse für Leitfäden oder Publikationen), fügen Sie sie
`RegisteredClassArray[]` hinzu und geben Sie ihr einen Zuordnungsblock.

## Verwendung

| View | Zweck |
|---|---|
| `ezodf/import`, `ezodf/upload_import` | ein Writer-Dokument importieren |
| `ezodf/export`, `ezodf/upload_export` | ein Objekt nach Writer exportieren |

Die Website-Toolbar von ezwt zeigt Schaltflächen für Import und Export bei den Klassen in
`websitetoolbar.ini [WebsiteToolbarSettings] ODFDisplayClasses[]`. Öffnen Sie nach einem Import das neue Objekt, prüfen
Sie Überschriften, Tabellen und Bilder, ergänzen Sie Alternativtexte und veröffentlichen Sie.

## Grenzen

Aus der Dokumentation der Erweiterung:

- die Zugriffsprüfung für Bilder in importierten Dokumenten ist nicht vollständig;
- Bilder werden beim Export ignoriert, wenn der Benutzer sie nicht lesen darf;
- das Speichern importierter Bilder im Medienordner funktioniert nicht richtig.

Formatierungen außer Überschriften, Listen, Tabellen und Bildern werden nicht übernommen. Prüfen Sie jeden Import vor
dem Veröffentlichen.

## Verwandte Seiten

- [Dokumente und Publikationen](README.md)
- [Barrierefreie Dokumente](barrierefreie-dokumente.md)
- [ezwt](../../../../../eng-us/features/6.0/extensions/ezwt.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
