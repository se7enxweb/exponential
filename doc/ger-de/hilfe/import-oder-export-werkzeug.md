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
| Zeilen aus einer Tabelle, einer CSV-, XML- oder JSON-Datei als Inhaltsobjekte übernehmen, einmal oder nach Zeitplan, mit Probelauf vorher | **Imports (7x SQLI Import)** | Import | einem Ordner, einer Klasse | CSV, TSV, XLSX, ODS, XML, JSON, NDJSON, RSS, Atom und viele mehr (siehe seinen **Catalogue**) | Reiter **Imports**, oder **Import/Export > In diesen Ordner importieren …** an einem Ordner |
| Inhalte aus einem anderen System übernehmen: einer REST-API, einer Datenbank, einem WordPress-Export, einem Shop | **Imports (7x SQLI Import)** | Import | einem Ordner, einer Klasse | REST-APIs, SQL- und NoSQL-Datenbanken, WordPress WXR, Drupal, Joomla, Shopify, WooCommerce … | **Imports > Catalogue**, **New import** auf der Karte des Formats |
| Die eigene Tabelle eines Moduls (eine Datentabelle) aus einer Datei füllen statt Inhaltsobjekte anzulegen | **Imports (7x SQLI Import)** | Import | Datentabellen | jedes Format von Imports | **Imports > New import**, Schritt Verarbeitung: **Rows of a data table** |
| Ein anderes System (einen Shop, ein CRM) einen Import starten lassen, sobald seine Daten bereit sind | **Imports (7x SQLI Import)** | Import | einem Ordner, einer Klasse, Datentabellen | das Format des Imports, im Aufruf geschickt | im Schritt Zeitplan des Imports: **Started by another system (webhook or API)** |
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
| Die Tabelle eines Moduls auf einer anderen Datenbank anlegen oder aktualisieren, ihre Zeilen ansehen, eine Verbindung zu einer externen Datenbank einrichten | **Data tables** (Datentabellen) | beide | Datentabellen, Klassen | SQL, .dba | **Setup > Datentabellen** |
| Eine Datentabelle und die Inhalte einer Klasse abgleichen, einmal, nach Zeitplan oder live, in jede Richtung | **Data tables** (Synchronisationen) | beide | Datentabellen, eine Klasse und ein Ordner | (zwischen Tabelle und Inhalten) | **Setup > Datentabellen > Synchronisationen**, oder die Gruppe **Data tables** im Katalog von Imports |

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
| Imports: die Felder eines Imports | Datentabellen (Modultabellen-Assistent) | **More > New data table from its fields** an einem Import öffnet den Assistenten mit den Spalten des Imports; der Import kann die neue Tabelle danach füllen. |
| Datentabellen | Inhalte, und zurück | Eine Synchronisation kopiert eine Tabelle in Inhalte oder Inhalte in eine Tabelle, oder hält beide mit einer Konfliktregel abgeglichen. |

### Beispiel: Produkte in einer Tabelle ändern und zurückladen

1. Exportieren Sie im Reiter **Export** die Klasse *Folder* des Produktordners als CSV. Die Datei hat eine Spalte mit
   der Remote-ID jedes Objekts und eine Spalte pro Attribut.
2. Öffnen Sie die Datei in einer Tabellenkalkulation, korrigieren Sie die Namen von drei Produkten und speichern Sie
   sie als CSV (UTF-8).
3. Laden Sie sie entweder mit dem eigenen **Import** von Export, oder, um vorher einen Probelauf zu sehen, öffnen Sie
   **Imports > New import**, wählen das Format *Export (xrowextract) file* und laden die Datei hoch. Laden Sie die
   `<datei>.manifest.json` des Exports mit hoch, dann ist die Zuordnung exakt; ohne sie setzen Sie die Option *Class*
   auf *Folder*, und die Spalten, die nach ihren Attributen benannt sind, werden zugeordnet.
4. Im Schritt Vorschau zeigt der Probelauf die drei Produkte als **Updated**, mit altem und neuem Namen; die übrigen
   sind **Unchanged**. **Save and run** veröffentlicht nur die drei geänderten Objekte, als neue Versionen.

Weil die Spalte mit der Remote-ID dieselben Objekte wiederfindet, wird nichts doppelt angelegt. Ist das Ergebnis
nicht, was Sie wollten, holt **Undo this run** auf der Seite des Laufs die vorherigen Versionen zurück
([Importe und Läufe](sqli-import/importe-und-laeufe.md#einen-lauf-rückgängig-machen)).

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

- Auf der Hauptseite jedes Werkzeugs nennt ein Kasten **Suchen Sie …?** (Looking for …?) die anderen Werkzeuge und
  die, die seine Dateien lesen. Die Marke **Works with this tool** kennzeichnet die Werkzeuge, deren Dateien dieses
  liest oder die seine Dateien lesen:

  ![Der Kasten Looking for another import or export? am Ende der Übersicht von Imports: Export (xrowextract), RSS export, Collected information export (CIE) und Data tables mit der Marke Works with this tool, dann Packages, Syndication, Documents, RSS import, Collected information, Newsletter subscribers, Audit export und PDF export und ein Verweis auf Which import or export tool do I need?](../../eng-us/help/images/sqli-import/sqli-import-looking-for.webp)
- An einem Objekt listet die Schaltfläche **Import/Export**, was Sie mit diesem Objekt tun können: es exportieren,
  in es importieren (in einem Ordner, in dem Sie Objekte anlegen dürfen) und es auf eine andere Site kopieren.
- Auf einer Klassenseite listet **Import und Export** die Werkzeuge, die mit den Objekten dieser Klasse arbeiten.
- Das Menü **Setup** und das linke Menü jedes Werkzeugs haben eine Liste **Import und Export** aller Werkzeuge, die
  Sie benutzen dürfen.
- Die Suche im Kopf schlägt passende Werkzeuge vor, wenn Sie nach einer Aufgabe suchen, etwa „CSV importieren" oder
  „auf eine andere Site kopieren".
- Das Hilfemenü **?** auf der Seite eines Werkzeugs verweist auf diese Seite zurück.

## Wo Ihre Läufe aufgelistet sind

Jeder Lauf von Imports, Export, CIE und den Synchronisationen der Datentabellen wird in einer gemeinsamen
Lauf-Historie festgehalten: wer ihn ausgeführt hat, wann, wie lange er dauerte, wie viele Datensätze er bearbeitet
hat und wie er endete. Die Lauf-Liste und die Seite **Statistik** von Imports haben einen Filter **Werkzeug**: wählen
Sie Imports, Export, CIE, Data tables oder alle Import- und Export-Werkzeuge. Ein Lauf von Export oder CIE verweist auf seine eigene Seite (den Job mit seiner Datei). Die eigenen
Seiten jedes Werkzeugs behalten ihre Listen und haben einen Verweis **Alle Import- und Export-Läufe**.

Sie sehen Ihre eigenen Läufe, und die Läufe aller Benutzer eines Werkzeugs, wenn Ihre Rolle es erlaubt (bei Export
die Richtlinie „alle Jobs“). Lange Exporte laufen im Hintergrund in der Job-Warteschlange, oder in den Inhalts-Jobs,
wenn die Warteschlange nicht eingerichtet ist; Sie können die Seite verlassen und später zurückkommen.

## Mehr über Imports

Wie Sie einen Import anlegen, mit einem Probelauf prüfen, ausführen, rückgängig machen und planen:
[7x SQLI Import: hier beginnen](sqli-import/README.md). Sein eigenes Handbuch mit einem durchgerechneten Beispiel für
jedes Format steht in der Hilfe unter Erweiterungen (`/exphelp/viewer/ext/sqliimport/ger-de/index.md`); seine Seite
zu den anderen Werkzeugen ist `/exphelp/viewer/ext/sqliimport/ger-de/transfer.md`.

## Für Entwickler

Die Werkzeuge listet das Transfer-Register des Kernels: Jedes Werkzeug meldet sich in `kernelservices.ini` an, und
jede Liste und jeder Verweis oben liest es. Siehe [Das Transfer-Register](../funktionen/6.0/transfer-register.md). Wie
ein Werkzeug im Hintergrund läuft und seine Läufe festhält: [Transfer-Läufe](../funktionen/6.0/transfer-laeufe.md).
