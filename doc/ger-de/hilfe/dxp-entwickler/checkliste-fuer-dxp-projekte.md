---
title: Checkliste für DXP-Projekte
audience: dxp-developer
category: dxp-development
order: 49
summary: Eine Checkliste für ein DXP-Projekt von der ersten Entscheidung bis zum Start und zur Wartung - Produktwahl, Installation, Sicherheit, Inhaltsmodell, Theme, APIs, Leistung, Betrieb und Übergabe - jeder Punkt mit der Seite verknüpft, die ihn erklärt.
keywords: Checkliste, Start, Projekt, Live-Gang, Sicherheit, Inhaltsmodell, Theme, APIs, Leistung, Betrieb, Übergabe, DXP, checklist, launch, project, go live, security, content model, performance, operations, handover
type: help
---
# Checkliste für DXP-Projekte

Diese Seite richtet sich an Entwickler und Projektleiter, die ein DXP-Projekt führen. Kopieren Sie sie in den Tracker
Ihres Projekts und haken Sie die Punkte ab.

## Inhalt

1. [Vor dem Projekt](#vor-dem-projekt)
2. [Installation und Sicherheit](#installation-und-sicherheit)
3. [Inhalte und Theme](#inhalte-und-theme)
4. [APIs](#apis)
5. [Leistung und Betrieb](#leistung-und-betrieb)
6. [Start](#start)
7. [Nach dem Start](#nach-dem-start)

## Vor dem Projekt

- [ ] Die DXP ist das richtige Produkt, nicht Nexus, Legacy oder Exponential 6
      ([Wählen](dxp-legacy-nexus-oder-exponential-6-waehlen.md)).
- [ ] Jede versprochene Funktion steht in der Paketliste ([Aussagen, die Sie vermeiden sollten](aussagen-die-sie-vermeiden-sollten.md)).
- [ ] Die Server laufen mit PHP 8.3 oder neuer; Node.js 20 ist dort verfügbar, wo Sie bauen
      ([Versionen](versionen-von-php-symfony-und-node.md)).

## Installation und Sicherheit

- [ ] Projekt aus dem Skeleton angelegt, unter Git, `.env.local` und JWT-Schlüssel ignoriert
      ([Installation](installation-mit-dem-skeleton.md)).
- [ ] Das Administratorpasswort des Installers geändert; Redakteure haben eigene Konten.
- [ ] `APP_ENV=prod`, `APP_DEBUG=0`, in der Produktion nur HTTPS ([Webserver einrichten](webserver-einrichten.md)).
- [ ] Berechtigungen mit ACLs gesetzt; nichts steht auf 777 ([Dateiberechtigungen](dateiberechtigungen.md)).
- [ ] Kein Upstream-Paket neben seinem Fork installiert ([Die Forks](die-se7enxweb-forks-und-replace.md#ihr-projekt-prüfen)).
- [ ] Testbenutzer und E-Mails verwenden `example.invalid`; E-Mails in der Entwicklung gehen an `null://null`.

## Inhalte und Theme

- [ ] Inhaltstypen mit Redakteuren und Entwicklern der Clients geprüft
      ([Inhaltsmodell](inhaltstypen-und-platzierungen.md#ratschläge-zur-modellierung)).
- [ ] Ein Design mit Ihrem Theme vor `standard` ([Das Standarddesign und Ihr Theme](das-standarddesign-und-ihr-theme.md)).
- [ ] Bildvariationen für jede Größe, die das Design verwendet ([Bildvariationen](bildvariationen.md)).
- [ ] Templates barrierefrei, ohne Inline-Skripte oder -Styles.

## APIs

- [ ] REST-Präfix geprüft und in der Konfiguration der Clients gehalten ([Die REST-API](die-rest-api.md#wo-sie-liegt)).
- [ ] JWT-Firewalls nur bei Bedarf eingeschaltet; ein Benutzer und eine Rolle je Client ([JWT](jwt-authentifizierung.md)).
- [ ] GraphQL-Schema erzeugt und committet ([GraphQL](graphql-und-graphiql.md)).
- [ ] CORS auf Ihre Origins beschränkt.

## Leistung und Betrieb

- [ ] HTTP-Cache an, Purging getestet ([HTTP-Cache](http-cache-und-varnish.md)).
- [ ] Cache-Pool gewählt (Dateisystem oder Redis) ([Anwendungs-Cache](anwendungs-cache-und-redis.md)).
- [ ] Suchmaschine gewählt und indexiert ([Suche](suche-legacy-und-solr.md)).
- [ ] Cron-Zeile für den Webserver-Benutzer eingetragen ([Cron](cron.md)).
- [ ] Nachrichten-Worker überwacht, falls Sie einen asynchronen Transport verwenden ([Messenger](messenger-und-benachrichtigungen.md)).
- [ ] Sicherungen von Datenbank, `public/var/`, `.env.local` und den JWT-Schlüsseln, und eine Wiederherstellung getestet.

## Start

- [ ] Die Bereitstellung läuft in der dokumentierten Reihenfolge ([Bereitstellen](dxp-bereitstellen.md)).
- [ ] Kurztests der Website, von `/adminui/` und der APIs bestehen.
- [ ] Die Logs werden in den ersten Tagen beobachtet.

## Nach dem Start

- [ ] Aktualisierungen nach Plan, in einem Branch, mit committeter `composer.lock` ([Aktualisieren](dxp-aktualisieren.md)).
- [ ] Release Notes der Forks vor jeder Aktualisierung gelesen.
- [ ] Alte Versionen mit `exponential:content:cleanup-versions` aufgeräumt, zuerst getestet.
- [ ] Gefundene Fehler zurückgemeldet ([Beitragen](zu-den-forks-beitragen.md)).

## Verwandte Seiten

- [DXP-Entwickler: hier beginnen](dxp-entwickler-hier-beginnen.md)
- [Bereitstellen](dxp-bereitstellen.md)
- [Fehlersuche](fehlersuche-in-der-dxp.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
