---
title: Inhalte und Pakete exportieren
audience: government, admin
category: public-sector
order: 130
summary: Die Wege, Inhalte aus einer Exponential-Installation herauszuholen, von der vollständigen Datensicherung über Klassen- und Inhaltspakete bis zu Exporten pro Klasse, mit dem, was jeder Weg enthält, und welchen Sie für einen Ausstieg, eine Kopie für eine andere Stelle oder ein Archiv nutzen.
keywords: Export, Pakete, ezpkg, Setup Pakete, Paketassistent, Export von Inhaltsobjekten, Export von Inhaltsklassen, xrowextract, Datenbank-Dump, Datensicherung, Migration, Ausstieg, export, packages, package wizard, content object export, class export, database dump, backup, exit
type: help
---
# Inhalte und Pakete exportieren

Diese Seite richtet sich an Administratoren, die Inhalte aus einer Installation herausholen müssen: für einen Umzug zu
einem anderen Anbieter, eine Kopie für eine andere öffentliche Stelle, eine Abgabe an ein Archiv oder eine Migration
auf ein anderes System. Jede Methode enthält etwas anderes.

## Inhalt

1. [Welche Methode](#welche-methode)
2. [Eine vollständige Datensicherung](#eine-vollständige-datensicherung)
3. [Pakete](#pakete)
4. [Exporte pro Klasse](#exporte-pro-klasse)
5. [Die REST-API](#die-rest-api)
6. [Einen Export prüfen](#einen-export-prüfen)

## Welche Methode

| Ziel | Methode | Enthält |
|---|---|---|
| Die ganze Website zu einem anderen Host umziehen | Datenbank-Dump plus Speicher unter `var/` plus Code-Repository | alles |
| Die Datenbank wechseln | datenbankunabhängiger `.dba`-Dump und Einspielen | den Inhalt der Datenbank |
| Einen Teil der Website an eine andere Installation geben | Paket aus Inhaltsobjekten | Knoten oder Teilbäume mit ihren Inhalten, wahlweise mit Klassen und Templates |
| Das Inhaltsmodell anderswo wiederverwenden | Paket aus Inhaltsklassen | Klassendefinitionen, keine Inhalte |
| Daten für ein anderes System oder ein Archiv | `xrowextract` als CSV, JSON oder XML | Felder einer Klasse oder vieler Klassen |
| Laufender Zugriff durch ein anderes System | REST-API | Knoten und Felder als JSON |

## Eine vollständige Datensicherung

Der vollständige Zustand besteht aus dem Code (Ihrem Repository und `composer.lock`), der Datenbank und den
Speicherdateien unter `var/<site>/storage/`, dazu den Overrides der Einstellungen. Die Hilfe zum Hosting erklärt
jeden Teil und die Dump-Werkzeuge der Datenbanken
([Datensicherungen: was gesichert werden muss](../../hosting/betrieb/backups-was-gesichert-werden-muss.md),
[Datenbanksicherungen je Datenbank](../../hosting/betrieb/datenbank-backups-je-engine.md)). Eine Datensicherung ist
erst durch eine Wiederherstellung bewiesen
([Wiederherstellung und Wiederherstellungstests](../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md)).

## Pakete

**Setup > Pakete > Neues Paket erstellen** bietet Assistenten an ([Paketassistent](../../../../eng-us/features/6.0/package-wizard.md)):

| Assistent | Exportiert |
|---|---|
| Export von Inhaltsklassen | die Definitionen von Inhaltsklassen, keine Inhalte |
| Export von Inhaltsobjekten | Knoten oder Teilbäume mit ihren Inhalten, wahlweise mit ihren Klassen und Templates |
| Extension-Export | die Dateien von Erweiterungen (erfordert, dass der Export in `package.ini [ExtensionExportSettings]` eingeschaltet ist) |

Ein Paket ist ein Verzeichnis mit einer `package.xml`, das als eine `.ezpkg`-Datei exportiert und in einer anderen
Installation unter **Setup > Pakete** importiert wird ([Pakete](../../../anleitungen/pakete.md)). Der Export von
Inhaltsobjekten eignet sich für einen Teilbaum, etwa die Seiten einer Abteilung; für ganze Websites ist eine
vollständige Datensicherung zuverlässiger.

## Exporte pro Klasse

`xrowextract` exportiert eine Klasse oder jede Klasse unterhalb ausgewählter Knoten als CSV, JSON oder XML, mit Sprachen
als Zeilen, Filtern und Voreinstellungen, und kann Exporte zeitgesteuert an ein Ziel liefern; das `exp:ezcsvexport` des
Kernels schreibt eine CSV-Datei pro Klasse
([xrowextract](../../../../eng-us/features/6.0/extensions/xrowextract.md), [exp:ezcsvexport](../../../../eng-us/examples/commands/ezcsvexport.md)).
Prüfen Sie, wie Rich-Text-Felder im Export erscheinen (sehen Sie sich zuerst die Vorschau an), bevor Sie sich darauf
verlassen.

## Die REST-API

Für ein Zielsystem, das Inhalte selbst abruft, liefert die REST-API Knoten, Unterknoten und Felder als JSON, mit den
Rechten des Benutzers, unter dem sie läuft ([REST-API v1 und v2](../../../anleitungen/rest-api-v1-und-v2.md)).

## Einen Export prüfen

- Vergleichen Sie Anzahlen: Objekte pro Klasse in der Quelle und im Export.
- Öffnen Sie Dateien in einem Texteditor und einer Tabellenkalkulation; prüfen Sie UTF-8 und Sonderzeichen.
- Prüfen Sie, ob Dateien, auf die Inhalte verweisen (Bilder, Dokumente), enthalten oder aufgelistet sind.
- Importieren Sie Pakete in eine Testinstallation und sehen Sie sich das Ergebnis an.

## Verwandte Seiten

- [Anbieterabhängigkeit vermeiden](anbieterabhaengigkeit-vermeiden.md)
- [Ausstiegsplanung](ausstiegsplanung.md)
- [Pakete vergleichen und importieren](../../../../eng-us/features/6.0/package-compare-and-import.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
