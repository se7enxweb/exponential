---
title: Andere Anwendungen und Datenbanken
audience: developer, admin, project-manager
category: install-upgrade
order: 52
summary: Die allgemeine Methode, um Inhalte aus jeder anderen Quelle in Exponential 6 zu übernehmen – Tabellenkalkulationen, eigene PHP- oder andere Webanwendungen, Intranets, beliebige SQL-Datenbanken, Office-Dokumente, Feeds – mit den jeweils passenden Werkzeugen, einem Plan Schritt für Schritt und dem Rückweg.
keywords: Tabellenkalkulation, CSV, Excel, eigene Anwendung, SQL-Datenbank, Intranet, Office-Dokumente, Feeds, RSS, Import, Migration, xrowextract, ezodf, spreadsheet, custom application, SQL database, office documents, import, migration
type: install
---
# Andere Anwendungen und Datenbanken

Für jede Quelle ohne eigene Anleitung ist die Methode dieselbe: Holen Sie die Inhalte in strukturierter Form heraus, ordnen Sie
sie zu, importieren Sie sie mit Remote-IDs und erhalten Sie die Adressen. Diese Seite beschreibt die Methode und das Werkzeug für jede Art von Quelle.

[Übersicht Migration](README.md) · [Checkliste](checkliste.md) · [Zuordnung](inhaltsmodell-zuordnen.md) · [Werkzeuge](exponential-tools.md)

## 1. Das Werkzeug für jede Art von Quelle

| Quelle | Herausholen als | Übernehmen mit |
|---|---|---|
| Eine Tabellenkalkulation (Excel, LibreOffice, Google Sheets) | CSV, UTF-8, ein Tabellenblatt pro Inhaltstyp | dem Importer von xrowextract mit `--map` (zuerst ein Probelauf); `exp:ezcsvimport` für einfache Klassen (durch `;` getrennt, keine Kopfzeile, Spalten in der Reihenfolge der Attribute) |
| Jede SQL-Datenbank (eine eigene PHP-Anwendung, ein Intranet, ein altes CMS) | eine Abfrage pro Inhaltstyp in CSV oder JSON | dem Importer von xrowextract oder dem [Skript-Grundgerüst](exponential-tools.md#4-ein-eigenes-importskript-das-grundgerüst) |
| Eine Anwendung, die nur eine API hat | ein Skript, das die API seitenweise in JSON ausliest | dem Skript-Grundgerüst |
| Office-Dokumente (Writer, Word per Konvertierung) | die Dokumente | `ezodf` (Writer-Import; Word und PDF brauchen den Konvertierungsdienst) |
| Ein Ordner mit Bildern und Dateien | ein ZIP | dem ZIP-Import der Upload-Seite (behält auf Wunsch die Ordnerstruktur bei) |
| Ein Feed (RSS) | der Feed | dem Import unter **Setup > RSS**, ausgeführt vom Cronjob `rssimport` (nur die neuesten Einträge) |
| Ein anderes Exponential | Pakete, xrowextract-Archive, `exp:layouts` | denselben Werkzeugen auf dem Ziel |
| Eine Site ohne Zugriff auf ihr Backend | ein Crawl | der [Methode für statisches HTML](statisches-html.md) |

## 2. Die Methode

1. **Bestandsaufnahme** ([Checkliste, 1](checkliste.md#1-bestandsaufnahme-wissen-was-sie-haben)): Typen, Anzahlen, Dateien, Benutzer,
   Adressen.
2. **Zuordnungstabelle** pro Typ ([Zuordnung](inhaltsmodell-zuordnen.md)), einschließlich dessen, was absichtlich wegfällt.
3. **Export** in einen eingefrorenen Dateibestand (CSV/JSON plus Dateien), mit einer stabilen Quell-ID pro Element.
4. **Umwandeln** in einem Skript außerhalb von Exponential: HTML bereinigen, Datumsangaben in Unix-Zeitstempel normalisieren, Kategorien
   übergeordneten Orten zuordnen, Dateipfade auflösen.
5. **Import** in der Reihenfolge der Abhängigkeiten (Medien, Container, Inhalte, Beziehungen, Übersetzungen, Benutzer), mit Remote-IDs,
   wiederholbar.
6. **Adressen**: Weiterleitungstabelle aus den alten Routen; Aliase, Platzhalter, Webserver-Regeln.
7. **Prüfen** Sie Anzahlen und Stichproben; **proben** Sie; **stellen Sie um**; halten Sie das alte System bis zur Abnahme schreibgeschützt.

## 3. Der Rückweg

Jeder Export aus [Migration weg von Exponential](exponential-verlassen.md) liegt in einem einfachen Format vor (CSV, JSON, XML, SQL),
sodass jede Anwendung ihn mit ihrem eigenen Importer laden kann.

Diese Seite ist Teil der Exponential-Dokumentation und freie Dokumentation unter der GNU Free Documentation License
1.3 oder einer späteren Version ([die Lizenz dieser Dokumentation](../../../en/us/README-LICENSE.md)).
