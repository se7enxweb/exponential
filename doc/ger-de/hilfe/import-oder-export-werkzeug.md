---
title: Welches Import- oder Export-Werkzeug brauche ich?
audience: editor, admin
category: editing
order: 40
summary: Eine Tabelle von der Aufgabe zum Werkzeug, für jedes Import- und Export-Werkzeug von Exponential 6.0.15, mit Hin- und Rückwegen und wo jedes zu finden ist.
keywords: Import, Export, importieren, exportieren, welches Werkzeug, CSV, XLSX, Tabelle, Excel, Paket, ezpkg, RSS, Atom, Feed, Syndication, auf eine andere Website kopieren, Inhalte verschieben, Sicherung, Formular, Einsendungen, Abonnenten, Newsletter, Word, ODT, DOCX, PDF, Audit, erneut importieren, Hin- und Rückweg, import, export, which tool, spreadsheet, copy to another site, Imports, CIE
type: help
---
# Welches Import- oder Export-Werkzeug brauche ich?

Exponential hat mehrere Import- und Export-Werkzeuge, jedes für eine andere Aufgabe. Diese Seite führt Sie von dem,
was Sie tun möchten, zu dem Werkzeug, das es tut. Die Namen der Werkzeuge müssen Sie dafür nicht kennen.

Die Administration zeigt Ihnen nur die Werkzeuge, die in Ihrer Installation eingeschaltet sind und die Ihre Rolle
benutzen darf. Ein Werkzeug aus dieser Tabelle kann also in Ihren Menüs fehlen: Dann ist es hier nicht aktiv, oder
fragen Sie Ihren Administrator.

## Das Werkzeug nach der Aufgabe finden

| Ich möchte … | Werkzeug | Richtung | Arbeitet auf | Formate | Wo |
|---|---|---|---|---|---|
| Zeilen aus einer Tabelle, einer CSV-, XML- oder JSON-Datei als Inhaltsobjekte übernehmen | **Imports (7x SQLI Import)** | Import | einem Ordner, einer Klasse | CSV, TSV, XLSX, ODS, XML, JSON, RSS | Reiter **Imports**, oder **Import/Export > In diesen Ordner importieren …** an einem Ordner |
| Die Objekte einer Klasse, eines Ordners oder der ganzen Site als Tabellendatei herunterladen | **Export (xrowextract)** | Export | Site, Ordner, Klasse | CSV, XML, JSON, ZIP-Archiv | Reiter **Export**, oder **Import/Export > Export …** an einem Objekt |
| Viele Objekte in einer Tabelle ändern und die Änderungen zurückladen | **Export (xrowextract)** und sein Import | beide | Ordner, Klasse | CSV, XML, JSON | exportieren, die Datei bearbeiten, dann **Export > Import** (oder **Diese Datei erneut importieren** im Verlauf des Exports) |
| Einen Ordner mit seinen Klassen einmal in eine andere Exponential-Installation kopieren | **Packages** (Pakete) | beide | Site, Ordner, Objekt, Klasse | ezpkg | **Import/Export > Dieses Objekt mit Unterbaum als Paket …**, **Setup > Pakete** |
| Einen Ordner auf einer anderen Exponential-Site immer wieder abgleichen | **Syndication** | beide | Ordner, Objekt | (zwischen Sites) | Reiter **Syndication**, **Import/Export > Auf eine andere Site kopieren …** |
| Klassendefinitionen, eine Extension oder ein Design in eine andere Installation bringen | **Packages** (Pakete) | beide | Klasse, Site | ezpkg | **Setup > Pakete > Erstellen**, auf der Klassenseite **Klassen als Paket exportieren …** |
| Nachrichten einer anderen Website auf dieser zeigen | **RSS import** | Import | einem Ordner | RSS, Atom | **Setup > RSS** |
| Einen Ordner als Feed zum Abonnieren anbieten | **RSS export** | Export | einem Ordner | RSS, Atom, OPML | **Setup > RSS** |
| Herunterladen, was ein Formular gesammelt hat (Kontaktformular, Umfrage) | **Collected information** (Gesammelte Informationen) oder **Collected information export (CIE)** | Export | Formulareinsendungen | CSV (CIE: auch SYLK und andere Zeichensätze, zeitgesteuert) | **Setup > Gesammelte Informationen**, Reiter **CIE** |
| Abonnenten aus einer Datei in eine Newsletter-Liste aufnehmen, oder eine Liste herunterladen | **Newsletter subscribers** (Newsletter-Abonnenten) | beide | Abonnenten | CSV | Reiter **Newsletter**, Import und Export der Liste |
| Eine Word- oder OpenDocument-Datei zu einem Objekt machen, oder ein Objekt als Dokument herunterladen | **Word and OpenDocument** | beide | einem Objekt | DOCX, DOC, ODT | **Import/Export > Word- oder OpenDocument-Datei importieren …** an einem Ordner |
| Einen Ordner drucken oder als Dokument verschicken | **PDF export** | Export | einem Ordner | PDF | **Setup > PDF-Export** |
| Den Audit-Trail einem Prüfer übergeben | **Audit export** | Export | Audit-Einträge | CSV, JSON Lines, JSON | **Audit > Export** |

## Hin- und Rückwege: was liest was

Manche Exporte lassen sich wieder importieren. Diese Paare sind dafür gemacht:

| Sie exportieren mit | Sie importieren mit | Hinweise |
|---|---|---|
| Export (xrowextract) CSV, XML, JSON | Export (xrowextract) **Import** | Die Manifest-Datei des Exports trägt die Spalten, so ordnet der Import sie für Sie zu. **Diese Datei erneut importieren** im Verlauf des Exports öffnet den Import mit der Datei. |
| Export (xrowextract) CSV | Imports (7x SQLI Import) | Imports hat eine Vorlage für xrowextract-Dateien: Die Spalten werden den Feldern derselben Klasse zugeordnet. |
| Collected information export (CIE) CSV | Imports (7x SQLI Import) | Mit der CIE-Vorlage werden Formulareinsendungen zu Inhaltsobjekten (zum Beispiel, um Kundenstimmen zu veröffentlichen). |
| Packages (ezpkg) | Packages | Laden Sie die Datei auf der anderen Installation unter **Setup > Pakete** hoch und installieren Sie sie. |
| RSS export | RSS import, Imports | Der Feed einer Site ist der Import einer anderen. |
| Imports: Zuordnung und Ergebnisse | Imports | Die Zuordnung eines Imports lässt sich herunterladen und in eine andere Installation laden; seine Ergebnisse als CSV herunterladen. |

## Welches, wenn zwei zu passen scheinen

- **Einmal oder immer wieder?** Für eine einmalige Kopie in eine andere Installation nehmen Sie **Packages**. Um zwei
  Exponential-Sites abzugleichen, nehmen Sie **Syndication**. Um dieselbe Datei oder denselben Feed nach Zeitplan zu
  holen, nehmen Sie **Imports** (Zeitpläne) oder **Export** (Zeitpläne und Ziele).
- **Eigene Daten oder fremde?** Dateien, die Sie aus Exponential exportiert haben, gehen am besten mit dem Werkzeug
  zurück, das sie gemacht hat (der Import von Export, Packages). Eine Datei aus einem anderen System (ein Shop, ein
  CRM, eine Tabelle von jemandem) geht mit **Imports** hinein: Dort ordnen Sie ihre Spalten den Feldern Ihrer Klasse zu
  und sehen vorher eine Vorschau.
- **Inhalte oder Datensätze?** Inhaltsobjekte (Artikel, Produkte, Seiten) exportieren **Export** und **Packages**.
  Datensätze, die keine Inhaltsobjekte sind (Formulareinsendungen, Abonnenten, Audit-Einträge), haben ihren eigenen
  Export dort, wo sie gespeichert sind.

## Wo Sie die Werkzeuge in der Administration finden

- Auf der Hauptseite jedes Werkzeugs nennt ein Kasten **Suchen Sie …?** die anderen Werkzeuge und die, die seine
  Dateien lesen.
- An einem Objekt listet die Schaltfläche **Import/Export**, was Sie mit diesem Objekt tun können: es exportieren,
  in es importieren (in einem Ordner, in dem Sie Objekte anlegen dürfen) und es auf eine andere Site kopieren.
- Auf einer Klassenseite listet **Import und Export** die Werkzeuge, die mit den Objekten dieser Klasse arbeiten.
- Das Menü **Setup** und das linke Menü jedes Werkzeugs haben eine Liste **Import und Export** aller Werkzeuge, die
  Sie benutzen dürfen.
- Die Suche im Kopf schlägt passende Werkzeuge vor, wenn Sie nach einer Aufgabe suchen, etwa „CSV importieren" oder
  „auf eine andere Site kopieren".
- Das Hilfemenü **?** auf der Seite eines Werkzeugs verweist auf diese Seite zurück.

## Für Entwickler

Die Werkzeuge listet das Transfer-Register des Kernels: Jedes Werkzeug meldet sich in `kernelservices.ini` an, und
jede Liste und jeder Verweis oben liest es. Siehe [Das Transfer-Register](../funktionen/6.0/transfer-register.md).
