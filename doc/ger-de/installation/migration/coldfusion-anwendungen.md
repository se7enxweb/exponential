---
title: ColdFusion-Anwendungen und Exponential
audience: developer, admin, project-manager
category: install-upgrade
order: 51
summary: Die Inhalte einer Adobe-ColdFusion- (oder Lucee-)Anwendung zu Exponential 6 übertragen – ColdFusion ist eine Laufzeitumgebung, kein CMS, daher werden die Inhalte aus der Datenbank und den Dateien der Anwendung gelesen – mit Zuordnung, .cfm-Adressen und Weiterleitungen sowie dem Rückweg.
keywords: ColdFusion, Adobe ColdFusion, Lucee, CFML, cfm, Datenbank, Datenquelle, SQL Server, Migration, database, datasource, migration
type: install
---
# ColdFusion-Anwendungen und Exponential

Adobe ColdFusion ist eine Laufzeitumgebung für Anwendungen, kein Content-Management-System: Jede ColdFusion-Site speichert
ihre Inhalte auf ihre eigene Weise, meist in einer relationalen Datenbank, die über eine Datenquelle angesprochen wird, manchmal in Dateien oder in
handgeschriebenen `.cfm`-Seiten. Die Migration liest daher diese Datenbank und diese Dateien direkt. Zu den eigenen Funktionen
von ColdFusion wurde für diese Seite nichts geprüft; die folgenden Schritte hängen nicht von ihnen ab.

[Übersicht Migration](README.md) · [Checkliste](checkliste.md) · [Andere Anwendungen und Datenbanken](andere-anwendungen.md)

## 1. Die Inhalte finden

1. **Die Datenquelle**: Der ColdFusion Administrator nennt die Datenbank, auf die jede Datenquelle zeigt (oft SQL Server,
   MySQL oder Oracle). Lassen Sie sich eine Kopie dieser Datenbank und das Schema geben.
2. **Die Abfragen**: Durchsuchen Sie den Code nach den Abfragen, die die öffentlichen Seiten aufbauen; sie zeigen Ihnen, welche Tabellen und
   Spalten Inhalte sind und wie die Seiten zusammengesetzt werden.
3. **Dateien**: hochgeladene Bilder und Dokumente sowie `.cfm`-Seiten, deren Text in der Seite selbst steht (diese werden
   wie [statisches HTML](statisches-html.md) aus der gerenderten Ausgabe gelesen).

## 2. Übertragen

Folgen Sie [Andere Anwendungen und Datenbanken](andere-anwendungen.md): eine SQL-Abfrage pro Inhaltstyp in JSON- oder CSV-Zeilen
mit Remote-IDs (`cf-<table>-<id>`), HTML umgewandelt, zuerst die Dateien importiert, dann die Inhalte, dann die
Beziehungen. Die Importer von Exponential und das [Skript-Grundgerüst](exponential-tools.md#4-ein-eigenes-importskript-das-grundgerüst)
übernehmen das Schreiben.

## 3. Adressen

ColdFusion-Adressen enthalten meist die Seite und ihre Parameter: `/news.cfm?id=123`, `/index.cfm?page=about`. Query-Strings
erreichen die URL-Aliase von Exponential nie; leiten Sie sie daher im Webserver anhand einer Tabelle weiter (`id` zur neuen Adresse),
die der Import erzeugt ([Checkliste, 4](checkliste.md#4-urls-und-weiterleitungen)). Suchmaschinenfreundliche Pfade werden, falls die
Anwendung welche hatte, zu weiterleitenden eigenen Aliasen.

## 4. Benutzer

Wie eine eigene Anwendung Passwörter speichert, haben ihre Entwickler entschieden. Standardwerte von PHPs `password_hash()` kann
Exponential prüfen; alles andere (einfache Hashes, eigenes Salting) bedeutet, dass Passwörter zurückgesetzt werden müssen
([Zuordnung, 6](inhaltsmodell-zuordnen.md#6-benutzer-gruppen-rollen-und-passwörter)).

## 5. Der Rückweg

Von Exponential zu einer ColdFusion-Anwendung: Exportieren Sie mit xrowextract nach CSV oder JSON
([Migration weg von Exponential](exponential-verlassen.md)) und laden Sie die Daten in die Tabellen der Anwendung.

## 6. Checkliste

- [ ] Kopie der Datenbank und Schema; die Abfragen hinter den öffentlichen Seiten gelesen.
- [ ] Dateien und handgeschriebene `.cfm`-Inhalte aufgelistet.
- [ ] Zeilen pro Inhaltstyp mit Remote-IDs exportiert; in der Reihenfolge der Abhängigkeiten importiert.
- [ ] `.cfm?`-Adressen im Webserver weitergeleitet; getestet.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->

Diese Seite ist Teil der Exponential-Dokumentation und freie Dokumentation unter der GNU Free Documentation License
1.3 oder einer späteren Version ([die Lizenz dieser Dokumentation](../../../eng-us/README-LICENSE.md)).
