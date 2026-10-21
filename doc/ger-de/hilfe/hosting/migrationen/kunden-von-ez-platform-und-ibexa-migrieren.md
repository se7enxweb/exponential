---
title: Kunden von eZ Platform und Ibexa migrieren
audience: hosting
category: hosting
order: 40
summary: Für Hoster, deren Kunden eZ Platform oder Ibexa DXP/OSS betreiben - die zwei Ziele bei Exponential (Exponential Platform Nexus und der Legacy-Kernel von Exponential 6.0), was jedes für den Hosting-Stack bedeutet, wie Sie den Aufwand bewerten und wo Kapitel 16 des Installationsbuchs die Schritte enthält.
keywords: Migration, eZ Platform, Ibexa, DXP, OSS, Symfony, Exponential Platform Nexus, Exponential Platform Legacy, Exponential 6.0, Legacy-Kernel, RichText, Page Builder, Netgen Layouts, Weg A, Weg B, Hosting-Stack, migration, legacy kernel, path A, path B, hosting stack
type: help
---
# Kunden von eZ Platform und Ibexa migrieren

Diese Seite ist für Hosting-Anbieter, deren Kunden eine Symfony-basierte Website auf eZ Platform 1.x bis 3.x oder Ibexa DXP
oder OSS 3.3 bis 5.0 betreiben und auf ein Exponential-Produkt auf der Plattform des Hosters umziehen möchten. Sie
beantwortet: welche Ziele es gibt, was jedes von Ihren Servern braucht und wie Sie die Aufgabe bewerten, bevor Sie sie
anbieten. Die Schritte selbst stehen in [Kapitel 16](../../../installation/16-migration-von-ez-platform-und-ibexa.md).

## Inhalt

1. [Zwei Ziele](#zwei-ziele)
2. [Was jedes Ziel vom Hoster braucht](#was-jedes-ziel-vom-hoster-braucht)
3. [Mit dem Kunden wählen](#mit-dem-kunden-wählen)
4. [Bewertung](#bewertung)
5. [Phasen](#phasen)
6. [Beides während eines Übergangs hosten](#beides-während-eines-übergangs-hosten)

## Zwei Ziele

Aus [16.1](../../../installation/16-migration-von-ez-platform-und-ibexa.md#161-die-beiden-ziele):

| | Weg A: Exponential Platform Nexus | Weg B: Exponential 6.0 (allein oder als Exponential Platform Legacy) |
|---|---|---|
| Was es ist | der gleichwertige Weg: derselbe Symfony-Stack, dieselbe Generation des Datenbankschemas und dieselben PHP-Namespaces, mit den Paketen gegen die Forks von se7enxweb getauscht | der Legacy-Kernel dieses Repositorys; Inhalte ziehen in die Welt von INI und TPL um |
| Aufwand | das geringste Risiko: dieselbe Generation, ein Austausch von Paketen | der höchste Aufwand: eine Migration von Daten und Templates |
| Eigene Symfony-Bundles und Twig | bleiben erhalten | werden als Legacy-Erweiterungen und TPL-Templates neu geschrieben |

Faustregeln aus [16.2](../../../installation/16-migration-von-ez-platform-und-ibexa.md#162-einen-weg-wählen): zuerst dieselbe
Generation; Weg B ist eine Neufassung der Darstellung und des eigenen Codes, nicht der Inhalte; und mischen Sie die Wege
nicht in einer Umschaltung.

## Was jedes Ziel vom Hoster braucht

| | Weg A (Symfony-Plattform) | Weg B (Exponential 6.0) |
|---|---|---|
| PHP | hängt von der gewählten Nexus-Linie ab; Kapitel 16 nennt das PHP jeder Linie | 8.0 bis 8.5 (einschließlich Velocity) |
| Webserver | ein Symfony-Front-Controller unter Apache oder nginx mit PHP-FPM | Velocity, FrankenPHP oder Apache/nginx mit PHP-FPM ([Einen Webserver wählen](../server/einen-webserver-waehlen.md)) |
| Datenbank | wie die Ausgangsplattform | die sechs von Exponential, in Reihenfolge: SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL |
| Suche | wie die Ausgangsplattform | siehe [Such-Backends](../grundlagen/such-backends.md) |
| Shell und Composer | erforderlich | empfohlen ([Composer und Git auf Servern](../grundlagen/composer-und-git-auf-servern.md)) |

> **Hinweis**
> Die Hosting-Seiten dieses Bereichs beschreiben Exponential 6.0. Für eine Website auf Weg A lesen Sie die Anforderungen
> der in Kapitel 16 gewählten Linie von Exponential Platform und ihr Repository; nehmen Sie nicht an, dass die
> Anforderungen von 6.0 gelten.

## Mit dem Kunden wählen

Fragen, die den Weg entscheiden, aus der Tabelle von Kapitel 16:

1. Hat die Website eigene Symfony-Bundles, Twig-Templates, REST- oder GraphQL-Clients, die der Kunde behalten möchte?
   Dann Weg A.
2. Nutzt sie RichText-Inhalte oder Landingpages aus Page Builder? Weg A behält RichText; Page Builder hat auf keinem der
   Wege eine Entsprechung und wird mit Netgen Layouts (A) oder Exponential Layouts (B) neu gebaut.
3. Kennt das Team des Kunden INI, TPL und Legacy-Erweiterungen? Dann ist Weg B vertrautes Terrain.
4. Hat das geringste Risiko für eine Live-Website Vorrang? Dann Weg A, dieselbe Generation.

## Bewertung

Was Sie vor einem Angebot erfragen ([16.4](../../../installation/16-migration-von-ez-platform-und-ibexa.md#164-bevor-sie-beginnen-bestandsaufnahme-einfrieren-und-sicherungen)):

| Element | Warum |
|---|---|
| Ausgangsprodukt und genaue Version | entscheidet die Generation und die Nexus-Linie |
| `composer.json` und `composer.lock` | die Pakete, die getauscht oder ersetzt werden |
| Eigene Bundles, Templates und ihr Umfang | der größte Treiber des Aufwands |
| Genutzte Feldtypen, besonders solche ohne Gegenstück auf dem Ziel | Arbeit für die Datenumwandlung |
| Größe der Inhaltstabellen | entscheidet die Datenbank und die Zeit für die Neuindizierung |
| Genutzte Suchmaschine | muss auf Ihrer Seite bereitgestellt werden |

Kapitel 16 nennt die drei Zahlen, die die Dauer bestimmen: die Anzahl der eigenen Bundles und Templates, die Anzahl der
Felder von Typen ohne Gegenstück auf dem Ziel und die Größe der Inhaltstabellen
([16.10](../../../installation/16-migration-von-ez-platform-und-ibexa.md#1610-ein-plan-und-seine-phasen)). Messen Sie die letzte mit
einem vollständigen Probelauf auf einer Kopie.

## Phasen

| Phase | Anteil des Hosters |
|---|---|
| 1. Bestandsaufnahme und Entscheidung | die Elemente oben erfragen; den Weg mit dem Kunden vereinbaren |
| 2. Ausgangssystem auf das letzte Release seiner Linie | meist beim aktuellen Hoster des Kunden |
| 3. Code | ein Staging-Server auf Ihrer Plattform mit dem Stack des Ziels |
| 4. Daten | die Kopie der Datenbank auf Ihrem Staging-Server |
| 5. Probelauf mit Zeitmessung | Ihr Staging-Server; schreiben Sie jede Dauer auf |
| 6. Einfrieren, Umschaltung, Prüfung | Wartungsfenster; Prüfungen aus [16.8](../../../installation/16-migration-von-ez-platform-und-ibexa.md#168-die-migration-prüfen) |
| 7. Entscheidungsfenster, dann Aufräumen | den Rückfall ([16.9](../../../installation/16-migration-von-ez-platform-und-ibexa.md#169-rollback)) für die vereinbarte Zeit bereithalten |

## Beides während eines Übergangs hosten

Manche Linien von Exponential Platform und Exponential Platform Legacy betreiben den Legacy-Kernel und einen Symfony-Stack
auf einer Datenbank ([16.2](../../../installation/16-migration-von-ez-platform-und-ibexa.md#162-einen-weg-wählen), letzte Zeile).
Für einen Hoster bedeutet das eine Website mit zwei Front-Controllern und einer Datenbank; dimensionieren und überwachen
Sie sie als eine Website mit den Anforderungen beider.

## Verwandte Seiten

- [Kunden zu Ihrem Hosting migrieren](kunden-zu-ihrem-hosting-migrieren.md)
- [Von älteren Exponential-Versionen](kunden-von-aelteren-versionen-migrieren.md)
- [Kapitel 17: Referenz zur Migration](../../../installation/17-migrationsreferenz.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
