---
title: Einen Datensatz mit einer stabilen URL veröffentlichen
audience: government, editor
category: public-sector
order: 220
summary: Aufgabe für Datenveröffentlicher - eine Datensatzseite mit ihren Dateien im Katalog anlegen, ihr einen kurzen dauerhaften Alias zum Zitieren geben und die Adressen als Besucher prüfen.
keywords: Datensatz, stabile URL, dauerhafte Adresse, URL-Alias, zitieren als, offene Daten, Datensatz veröffentlichen, Distribution, dataset, stable URL, permanent address, URL alias, cite as, open data, publish dataset, distribution
type: help
---
# Einen Datensatz mit einer stabilen URL veröffentlichen

Diese Aufgabe richtet sich an Redakteure in einem Datenteam. Hintergrund:
[Datensatzdateien veröffentlichen](../../offene-daten/datensatzdateien-veroeffentlichen.md) und
[Stabile URLs für Datensätze](../../offene-daten/stabile-urls-fuer-datensaetze.md).

## Bevor Sie beginnen

- Eine Datensatzklasse und ein Katalogordner existieren; Sie dürfen dort Datensätze anlegen.
- Die Datei hat die Datenschutzprüfung bestanden; die Lizenz ist entschieden.

## Schritte

1. Öffnen Sie den Katalogordner; unter **Unterelemente** **Neu erstellen**, **Dataset**. Füllen Sie Titel,
   Beschreibung, Herausgeber, Kontaktpostfach, Lizenz und Daten aus. **Zur Veröffentlichung schicken**.
2. Im neuen Datensatz **Neu erstellen**, **Distribution** (oder **Datei**); laden Sie die Datei mit einem stabilen
   Namen in Kleinbuchstaben hoch (`street-lights.csv`). Veröffentlichen Sie.
3. Fügen Sie im Reiter **URL-Aliase** des Datensatzes einen kurzen Alias wie `data/street-lights` hinzu.
4. Geben Sie ihn auf der Seite als „Zitieren als: https://www.example.gov/data/street-lights“ aus (in der Beschreibung
   oder in Ihrem Template).

## Prüfen

```bash
curl -sI https://www.example.gov/data/street-lights | head -1
```

antwortet mit `200` oder einem `301` auf die Seite. Abgemeldet öffnen sich die Seite und der Download der Datei.

## Probleme

| Problem | Lösung |
|---|---|
| Download für Besucher verweigert | die Sektion des Katalogs ist für Anonymous nicht lesbar |
| Der Alias öffnet sich nicht | ein Cache oder ein Tippfehler; prüfen Sie den Reiter **URL-Aliase** |

## Verwandte Seiten

- [Weiterleitungen und verschobene Inhalte](../../../ersteller/website-ersteller/weiterleitungen-und-verschobene-inhalte.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
