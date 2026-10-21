---
title: Bereitschaft für offene Daten
audience: government, admin, project-manager, developer
category: public-sector
order: 220
summary: Was eine öffentliche Stelle prüft, bevor sie offene Daten von einer Exponential-Website veröffentlicht - Datensätze als Inhalte, Dateien, Lizenzen, Metadaten, stabile Adressen, Feeds und die REST-API - mit dem, was Exponential mitbringt, und dem Hinweis, dass es keinen eingebauten Export nach einem Standard für Datenkataloge hat.
keywords: offene Daten, Open Data, Datensätze, Datenkatalog, DCAT, Lizenz, Metadaten, stabile URLs, RSS, REST-API, CSV, Richtlinie über offene Daten, öffentlicher Sektor, open data, datasets, data catalog, license, metadata, stable URLs, REST API, Open Data Directive, public sector
type: help
---
# Bereitschaft für offene Daten

Diese Checkliste richtet sich an die Leitung für offene Daten und die Website-Administration einer öffentlichen Stelle,
die Datensätze auf ihrer Exponential-Website veröffentlicht. Exponential ist ein Content-Management-System, kein
Datenportal: Datensätze werden als Inhalte mit angehängten Dateien veröffentlicht, und die Website bietet Feeds und
eine REST-API. Es gibt keinen eingebauten Export im Vokabular DCAT; wenn Sie einen brauchen, planen Sie ihn als eigene
Entwicklung oder verwenden Sie einen getrennten Katalog.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Siehe in der EU die
> [Richtlinie über offene Daten](https://eur-lex.europa.eu/eli/dir/2019/1024/oj); prüfen Sie die für Sie geltenden
> Regeln mit Ihrer Rechtsberatung.

## Inhalt

1. [Entscheiden](#entscheiden)
2. [Inhaltsmodell](#inhaltsmodell)
3. [Dateien und Formate](#dateien-und-formate)
4. [Auffindbarkeit](#auffindbarkeit)
5. [Zugriff für Maschinen](#zugriff-für-maschinen)
6. [Pflege](#pflege)

## Entscheiden

- [ ] Welche Datensätze Sie veröffentlichen, wem jeder gehört und wie oft er aktualisiert wird.
- [ ] Die Lizenz, vorzugsweise eine, die die [Open Definition](https://opendefinition.org/od/2.1/en/) erfüllt.
- [ ] Personenbezogene Daten vor der Veröffentlichung entfernt oder aggregiert.

## Inhaltsmodell

- [ ] Eine Inhaltsklasse für Datensätze, mit Feldern für Titel, Beschreibung, Herausgeber, Lizenz, Aktualisierungshäufigkeit, Abdeckung und die Datendateien ([Ihr Inhaltsmodell entwerfen](../../../../eng-us/help/creators/website-creators/designing-your-content-model.md), [Datentypen wählen](../../../../eng-us/help/creators/website-creators/choosing-datatypes.md)).
- [ ] Feldnamen, die einem Katalogvokabular wie [DCAT 3](https://www.w3.org/TR/vocab-dcat-3/) entsprechen, damit ein späterer Export einfach ist.
- [ ] Tags für Themen ([Verschlagworten mit eztags](../../../../eng-us/help/creators/website-creators/tagging-with-eztags.md)).

## Dateien und Formate

- [ ] Daten in offenen, maschinenlesbaren Formaten (CSV, JSON), nicht nur als PDF ([Dateien und Downloads](../../../../eng-us/help/creators/website-creators/files-and-downloads.md)).
- [ ] Downloads über `content/download` ausgeliefert, das die Leserechte prüft ([Sicherheitsstandards](../../../../eng-us/standards/security.md)).
- [ ] Dateigrößen innerhalb der Upload-Grenzen des Servers ([PHP-Einstellungen für Exponential](../../hosting/grundlagen/php-einstellungen-fuer-exponential.md)).
- [ ] Ein Datenwörterbuch für jeden Datensatz, das Spalten und Einheiten erklärt.

## Auffindbarkeit

- [ ] Eine Katalogseite, die jeden Datensatz auflistet ([Suche auf der Website](../../../../eng-us/help/creators/website-creators/site-search.md)).
- [ ] Stabile, lesbare Adressen, die sich nicht ändern, wenn eine Datei aktualisiert wird ([URL-Aliasse und sprechende URLs](../../../../eng-us/help/creators/website-creators/url-aliases-and-nice-urls.md)).
- [ ] Sitemaps enthalten die Datensatzseiten ([Sitemaps](../../../../eng-us/help/creators/website-creators/sitemaps.md)).
- [ ] Metadaten für Suchmaschinen und das Teilen ([Metadaten und Teilen in sozialen Medien](../../../../eng-us/help/creators/website-creators/metadata-and-social-sharing.md)).

## Zugriff für Maschinen

- [ ] Ein RSS-Feed neuer und geänderter Datensätze ([RSS-Feeds](../../../anleitungen/rss-feeds.md)).
- [ ] Die REST-API für Nachnutzer dokumentiert, mit Lesezugriff für anonyme Benutzer nur dort, wo es beabsichtigt ist ([REST-API v1 und v2](../../../anleitungen/rest-api-v1-und-v2.md)).
- [ ] Ratenbegrenzungen oder Caching vor der API mit dem Hoster entschieden ([Cache-Ebenen](../../hosting/leistung/cache-schichten.md)).

## Pflege

- [ ] Jeder Datensatz zeigt das Datum seiner letzten Aktualisierung.
- [ ] Alte Versionen aufbewahrt oder archiviert, wie Ihre Regeln zu Schriftgut es sagen ([Checkliste Schriftgut](checkliste-schriftgut.md)).
- [ ] Links regelmäßig geprüft ([Inhaltsprüfungen](../../../../eng-us/features/6.0/content-checks.md)).
- [ ] Rückmeldungen von Nachnutzern haben eine Kontaktstelle.

## Verwandte Seiten

- [Checklisten für Verwaltungen](README.md)
- [Bereitschaft für Informationsfreiheit](bereitschaft-fuer-informationsfreiheit.md)
- Der Bereich offene Daten dieser Hilfe für Verwaltungen (von einem anderen Autor geschrieben)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
