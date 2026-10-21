---
title: Die ersten 90 Tage für Parlamente
audience: government, project-manager, admin
category: public-sector
order: 60
summary: Ein Plan für 90 Tage für ein Parlament oder eine gesetzgebende Versammlung, die mit Exponential beginnen - die Quellsysteme der Aufzeichnung erfassen, Modell und Importe bauen, Mitglieder und laufende Geschäfte migrieren, außerhalb der Sitzungswochen starten - mit Prüfungen.
keywords: Projektplan, 90 Tage, Parlament, Gesetzgeber, Quellsysteme, Import, Mitglieder, Gesetzentwürfe, Abstimmungen, Start, sitzungsfreie Zeit, Prüfungen, project plan, 90 days, parliament, legislature, source systems, import, members, bills, votes, launch, recess, checks
type: help
---
# Die ersten 90 Tage für Parlamente

Diese Seite richtet sich an Projektverantwortliche und Administratorinnen und Administratoren, die die Website eines
Parlaments mit Exponential starten. Planen Sie den Start für eine sitzungsfreie Zeit, nie für eine Sitzungswoche. Der
Plan setzt voraus, dass eine Installation existiert ([Erste Schritte](../../../../anleitungen/erste-schritte.md)).

## Inhalt

1. [Tage 1 bis 30: Quellen und Regeln](#tage-1-bis-30-quellen-und-regeln)
2. [Tage 31 bis 60: Modell und Importe](#tage-31-bis-60-modell-und-importe)
3. [Tage 61 bis 90: Migration und Start](#tage-61-bis-90-migration-und-start)
4. [Risiken](#risiken)

## Tage 1 bis 30: Quellen und Regeln

| Schritt | Ergebnis |
|---|---|
| Jede Art von Aufzeichnung ihrem Quellsystem und ihrer verantwortlichen Stelle zuordnen (Gesetzentwürfe, Abstimmungen, Protokolle, Video) | eine Quellenkarte |
| Für jede entscheiden: in das CMS kopiert oder zur Quelle verlinkt | eine Entscheidung je Art der Aufzeichnung |
| Veröffentlichungszeiten vereinbaren (zum Beispiel Abstimmungen innerhalb einer festgelegten Zeit nach der Sitzung) | Service-Level |
| Das Adressschema (Wahlperiode, Nummer) für Einträge der Aufzeichnung vereinbaren | eine URL-Regel ([URL-Aliase und sprechende URLs](../../../ersteller/website-ersteller/url-aliasse-und-sprechende-urls.md)) |
| Die Weiterverwendung von Daten vereinbaren: Formate und Lizenz | eine Datenrichtlinie |

## Tage 31 bis 60: Modell und Importe

| Schritt | Prüfung |
|---|---|
| Die Klassen Mitglied, Sitzung, Gesetzentwurf und Abstimmungsergebnis anlegen ([Inhaltsmodell](inhaltsmodell.md)) | Testobjekte mit Beziehungen erscheinen auf den Seiten der jeweils anderen |
| Sektionen und Rollen anlegen ([Struktur](struktur-sektionen-und-rollen.md)) | die Parlamentsverwaltung veröffentlicht eine Abstimmung sofort; die Kommunikation kann sie nicht bearbeiten |
| Importe aus den Quellsystemen bauen, wo Inhalte kopiert werden (REST-API oder Skripte) | ein Importlauf erzeugt auf der Testinstallation die erwarteten Objekte ([REST API v1 und v2](../../../../anleitungen/rest-api-v1-und-v2.md)) |
| Die Grenze des Versionsverlaufs für die Klassen der Aufzeichnung anheben, falls erforderlich | `VersionHistoryClass[<class id>]` in einem Override |
| RSS-Exporte für Tagesordnungen, Gesetzentwürfe und Abstimmungen | die Feeds öffnen sich in einem Reader ([RSS-Feeds](../../../../anleitungen/rss-feeds.md)) |
| Die Seiten eines Tages mit vielen Abstimmungen mit eingeschalteten Caches einem Lasttest unterziehen | ein gemessenes Ergebnis |

## Tage 61 bis 90: Migration und Start

| Schritt | Lesen |
|---|---|
| Mitglieder migrieren, aktuelle und ehemalige, mit Daten | [Inhaltsmodell](inhaltsmodell.md) |
| Die Geschäfte der laufenden Wahlperiode migrieren; ältere Wahlperioden mit dem alten Archiv verlinken | [Umleitungen und verschobene Inhalte](../../../ersteller/website-ersteller/weiterleitungen-und-verschobene-inhalte.md) |
| Die Parlamentsverwaltung in der Veröffentlichung jeder Art von Aufzeichnung schulen | [Tagesordnungen, Gesetzentwürfe und Abstimmungsergebnisse](tagesordnungen-gesetzentwuerfe-und-abstimmungsergebnisse.md) |
| Test der Barrierefreiheit der Seiten der Aufzeichnung und der Videoseite | [Die Barrierefreiheit testen](../../../ersteller/website-ersteller/barrierefreiheit-testen.md) |
| Start in einer sitzungsfreien Zeit | [Start-Checkliste](../../../ersteller/website-ersteller/checkliste-fuer-den-start.md) |

Prüfungen beim Start:

- [ ] Jedes aktuelle Mitglied hat eine Seite; ehemalige Mitglieder der laufenden Wahlperiode ebenfalls.
- [ ] Ein Testergebnis einer Abstimmung wird innerhalb der vereinbarten Zeit veröffentlicht, mit Zahlen und der
  amtlichen Aufzeichnung.
- [ ] Alte Adressen der Aufzeichnung leiten um oder verweisen auf das Archiv.

## Risiken

| Risiko | Antwort |
|---|---|
| Website und Quellsystem laufen auseinander | eine dokumentierte Richtung des Datenflusses; Berichtigungen zuerst in der Quelle |
| Importe schlagen unbemerkt fehl | ein Importbericht, nach jedem Lauf von der Parlamentsverwaltung geprüft |
| Eine Spitze legt die Website lahm | Caches, statische Seiten für die meistbesuchten Seiten, eine geprobte Wartungsseite ([Wartungsmodus](../../../../anleitungen/wartungsmodus.md)) |

## Verwandte Seiten

- [Parlamente](README.md)
- [Tagesordnungen, Gesetzentwürfe und Abstimmungsergebnisse](tagesordnungen-gesetzentwuerfe-und-abstimmungsergebnisse.md)
- [Ihre Website planen](../../../ersteller/website-ersteller/ihre-website-planen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
