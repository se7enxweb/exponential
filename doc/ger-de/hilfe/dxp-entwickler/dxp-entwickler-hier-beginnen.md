---
title: "DXP-Entwickler: hier beginnen"
audience: dxp-developer
category: dxp-development
order: 1
summary: Wo Symfony-Entwickler beginnen, die mit Exponential Platform DXP bauen. Was die DXP ist und was nicht, welche Seiten welche Aufgabe beantworten, die erste Stunde mit einem neuen Projekt und die Regeln, die Aussagen und Projekte korrekt halten.
keywords: DXP, DXP-Entwickler, Exponential Platform DXP, Symfony, Skeleton, Metapaket, hier beginnen, Überblick, REST, GraphQL, Twig, Admin-Oberfläche, Ibexa OSS, eZ Platform, Platform v5, DXP developer, metapackage, start here, overview, admin UI
featured: true
type: help
---
# DXP-Entwickler: hier beginnen

Dieser Bereich der Hilfe richtet sich an **Entwickler, die mit Exponential Platform DXP bauen**: Symfony-Entwickler, die
ein neues Projekt beginnen, Teams, die von eZ Platform oder Ibexa OSS umziehen, und Entwickler von Exponential 6, die
wissen möchten, ob die Distribution DXP, Legacy oder Nexus zu ihrem nächsten Projekt passt. Er erklärt, was die DXP
mitliefert, wie ein Projekt installiert, konfiguriert, gebaut, bereitgestellt und aktualisiert wird und wo jeder Teil
ausführlich beschrieben ist.

Jede Aussage über die DXP auf diesen Seiten wurde am Metapaket `se7enxweb/exponential-platform-dxp` (seiner
`composer.json`), an der Installationsanleitung des Skeletons (`INSTALL.md` von
`se7enxweb/exponential-platform-dxp-skeleton`), an der Dokumentation in diesem Repository oder an den installierten
Paketen einer Installation von Platform v5 geprüft. Stammt eine Angabe aus einem Upstream-Paket, sagt die Seite das.

## Inhalt

1. [Was die DXP ist, in fünf Zeilen](#was-die-dxp-ist-in-fünf-zeilen)
2. [Was Sie hier finden](#was-sie-hier-finden)
3. [Ihre erste Stunde mit der DXP](#ihre-erste-stunde-mit-der-dxp)
4. [Die Regeln für diesen Bereich](#die-regeln-für-diesen-bereich)
5. [Seiten anderer Zielgruppen, die Sie nutzen werden](#seiten-anderer-zielgruppen-die-sie-nutzen-werden)
6. [Diese Seiten wiederfinden](#diese-seiten-wiederfinden)

## Was die DXP ist, in fünf Zeilen

- **Exponential Platform DXP** ist "eine freie, quelloffene Digital Experience Platform auf Basis des
  Symfony-Frameworks, die von 7x gepflegte Fortführung der Linie eZ Platform und Ibexa OSS durch die Gemeinschaft"
  ([Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md#exponential-platform-dxp)).
- Sie läuft auf **PHP 8.3 oder neuer** und **Symfony 7.4**, mit einem Kernel und einem Web-Einstiegspunkt.
- Sie liefert die Administrationsoberfläche unter `/adminui/`, die REST-API v2, GraphQL unter `/graphql`, Twig-Ausgabe
  mit der Design-Engine, Core- und Solr-Suche, HTTP-Cache, Cron und Messenger mit.
- Sie liefert **keinen** Layout-Editor, keinen Freigabe-Workflow, keinen Scheduler und nicht den Kernel von
  Exponential 6 mit ([Was die DXP mitliefert und was nicht](was-die-dxp-mitliefert-und-was-nicht.md)).
- Ein Projekt beginnen Sie mit `composer create-project se7enxweb/exponential-platform-dxp-skeleton`
  ([Installation mit dem Skeleton](installation-mit-dem-skeleton.md)).

## Was Sie hier finden

| Aufgabe | Seite |
|---|---|
| Entscheiden, ob die DXP das richtige Produkt ist | [DXP, Legacy, Nexus oder Exponential 6 wählen](dxp-legacy-nexus-oder-exponential-6-waehlen.md) |
| Genau wissen, was installiert ist | [Das Metapaket lesen](das-metapaket-lesen.md), [Die se7enxweb-Forks und `replace`](die-se7enxweb-forks-und-replace.md) |
| Ein Projekt installieren | [Versionen, die Sie brauchen](versionen-von-php-symfony-und-node.md), [Installation mit dem Skeleton](installation-mit-dem-skeleton.md), [Umgebungsvariablen](umgebungsvariablen.md), [Datenbanken](datenbanken-mysql-postgresql-sqlite.md), [Dateiberechtigungen](dateiberechtigungen.md), [Webserver einrichten](webserver-einrichten.md) |
| Sich im Projekt zurechtfinden | [Der Verzeichnisaufbau des Projekts](der-verzeichnisaufbau-des-projekts.md), [Siteaccesses](siteaccesses-site-und-admin.md), [Die Admin-Oberfläche](die-admin-oberflaeche.md) |
| Inhalte modellieren | [Inhalte, Inhaltstypen und Platzierungen](inhaltstypen-und-platzierungen.md), [Die mitgelieferten Feldtypen](die-mitgelieferten-feldtypen.md) |
| Die Website ausgeben | [Twig und die Design-Engine](twig-templates-und-die-design-engine.md), [Das Standarddesign und Ihr Theme](das-standarddesign-und-ihr-theme.md), [Twig-Funktionen für den Alltag](twig-funktionen-fuer-den-alltag.md), [Twig-Komponenten](twig-komponenten.md), [Bildvariationen](bildvariationen.md), [Assets bauen](assets-mit-yarn-und-encore-bauen.md) |
| Inhalte an andere Clients ausliefern | [Die REST-API](die-rest-api.md), [JWT-Authentifizierung](jwt-authentifizierung.md), [GraphQL und GraphiQL](graphql-und-graphiql.md), [Headless mit der DXP](headless-mit-der-dxp.md) |
| Schnell machen | [HTTP-Cache und Varnish](http-cache-und-varnish.md), [Anwendungs-Cache und Redis](anwendungs-cache-und-redis.md), [Suche](suche-legacy-und-solr.md), [Neu indexieren](neu-indexieren.md) |
| Betreiben | [Cron](cron.md), [Messenger und Benachrichtigungen](messenger-und-benachrichtigungen.md), [Konsolenbefehle](konsolenbefehle-und-umbenennungen.md), [Doctrine-Migrationen](doctrine-migrationen.md), [Befehle für den Alltag](befehle-fuer-den-alltag.md), [Bereitstellen](dxp-bereitstellen.md), [Aktualisieren](dxp-aktualisieren.md), [Fehlersuche](fehlersuche-in-der-dxp.md) |
| Ein vorhandenes Projekt umziehen | [Umstieg von Ibexa OSS oder eZ Platform](umstieg-von-ibexa-oss-oder-ez-platform.md), [Umstieg von Exponential 6](umstieg-von-exponential-6.md), [Das Skeleton 4.6.x](das-skeleton-v4x.md) |
| Seitenbau auf der DXP | [Layouts Core in der DXP](layouts-core-in-der-dxp.md) |
| Lizenzierung, Mitwirken, Ehrlichkeit | [Lizenzierung](lizenzierung.md), [Zu den Forks beitragen](zu-den-forks-beitragen.md), [Aussagen, die Sie vermeiden sollten](aussagen-die-sie-vermeiden-sollten.md) |
| Schnelle Antworten | [FAQ](faq-fuer-dxp-entwickler.md), [Checkliste für Projekte](checkliste-fuer-dxp-projekte.md), [Glossar](glossar-fuer-dxp-entwickler.md), [Lesepfade](lesepfade-fuer-dxp-entwickler.md) |

## Ihre erste Stunde mit der DXP

1. **Lesen Sie, was sie ist und was nicht** (10 Minuten):
   [Was Exponential Platform DXP ist](was-exponential-platform-dxp-ist.md) und
   [Was die DXP mitliefert und was nicht](was-die-dxp-mitliefert-und-was-nicht.md).
2. **Prüfen Sie Ihren Rechner** (5 Minuten): PHP 8.3 oder neuer, Composer 2, Node.js 20 und Yarn 1.22
   ([Versionen, die Sie brauchen](versionen-von-php-symfony-und-node.md)).
3. **Installieren Sie auf SQLite** (30 Minuten): kein Datenbankserver nötig
   ([Installation mit dem Skeleton](installation-mit-dem-skeleton.md),
   [SQLite für die Entwicklung](sqlite-fuer-die-entwicklung.md)).
4. **Sehen Sie sich um** (15 Minuten): Öffnen Sie `/adminui/`, legen Sie ein Inhaltselement an, lesen Sie es über die
   REST-API und `/graphql` zurück ([Die Admin-Oberfläche](die-admin-oberflaeche.md), [Die REST-API](die-rest-api.md)).

## Die Regeln für diesen Bereich

1. **Beschreiben Sie die DXP anhand ihrer Paketliste.** Das README des Skeletons enthält eine lange, allgemeine
   Funktionsliste; installiert ist nur, was das Metapaket verlangt. Prüfen Sie `composer.json`, bevor Sie eine
   Funktion versprechen ([Aussagen, die Sie vermeiden sollten](aussagen-die-sie-vermeiden-sollten.md)).
2. **Verwenden Sie in allem Neuen die Befehlsnamen `exponential:`**; die Namen `ibexa:` sind Aliase
   ([Konsolenbefehle](konsolenbefehle-und-umbenennungen.md)).
3. **Committen Sie nie `.env.local`, JWT-Schlüssel oder Passwörter.** Das Standard-Administratorkonto des Installers
   ist öffentlich dokumentiert: Ändern Sie sein Passwort direkt nach der Installation.
4. **Installieren Sie nie ein Upstream-Paket neben seinem Fork**
   ([Die se7enxweb-Forks und `replace`](die-se7enxweb-forks-und-replace.md)).
5. **Verwenden Sie die richtigen Namen.** Das Produkt heißt Exponential Platform DXP; 7x ist das Unternehmen, das es
   führt; die Exponential Foundation ist die gemeinnützige Organisation. Es steht nicht in Verbindung mit eZ Systems
   oder Ibexa AS ([Wer ist 7x?](../wer-ist-7x.md#kurz-gesagt)).

## Seiten anderer Zielgruppen, die Sie nutzen werden

| Seite | Warum ein DXP-Entwickler sie liest |
|---|---|
| [Entwickler: das Buch zur Erweiterungsentwicklung](../erweiterungsentwicklung/README.md) | Das Buch der Zielgruppe Entwickler für Exponential 6; lesen Sie es, wenn ein Projekt auch den Kernel von Exponential 6 betreibt |
| [Lesepfade für Entwickler](../erweiterungsentwicklung/lesepfade.md) | Wo die Zielgruppe Entwickler beginnt, nach Ziel |
| [Mit Git entwickeln](../mit-git-entwickeln.md) | Die Arbeitsweise mit Git, die das ganze Projekt verwendet |
| [Die Produktfamilie kennen](../fuersprecher/die-produktfamilie-kennen.md) | Jedes Produkt der Familie in einem Satz |
| [Headless mit der REST-API](../unternehmen/mobil-und-headless/headless-mit-der-rest-api.md) | Wie Organisationen ein Headless-Projekt planen |
| [Kunden von eZ Platform und Ibexa migrieren](../hosting/migrationen/kunden-von-ez-platform-und-ibexa-migrieren.md) | Die Hosting-Seite einer Migration |
| [Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md#welches-produkt-zu-ihnen-passt) | Welches Produkt zu Ihnen passt |

## Diese Seiten wiederfinden

- Verwenden Sie im Hilfe-Center den Zielgruppenfilter **DXP-Entwickler**, oder öffnen Sie **Alle Seiten** und den
  Ordner `dxp-entwickler`.
- Jede Seite hat **Auf GitHub bearbeiten**: Wenn sich ein Befehl geändert hat oder eine Angabe veraltet ist,
  korrigieren Sie sie für alle Entwickler nach Ihnen.

## Verwandte Seiten

- [Was Exponential Platform DXP ist](was-exponential-platform-dxp-ist.md)
- [DXP-Skeleton: create-project und eine dokumentierte Installation](../../funktionen/6.0/plattform-dxp-skeleton.md)
- [Übersicht der Plattformpakete](../../spezifikationen/6.0/platform-paketzuordnung.md)
- [Wer ist 7x?](../wer-ist-7x.md) und [Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
