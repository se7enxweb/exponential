---
title: "FAQ: Integrationen und APIs"
audience: government, project-manager, developer, admin
category: public-sector
order: 120
summary: Antworten auf die Integrationsfragen öffentlicher Stellen zu Exponential - die REST-API, persönliche API-Schlüssel, OAuth, RSS-Feeds, entfernte Dienste, Exporte, Single Sign-on, Karten und Suche -, mit dem, was vorhanden ist, und dem, was Eigenentwicklung wäre.
keywords: FAQ Integrationen, API, REST-API, API-Schlüssel, OAuth, RSS, entfernte Dienste, Export, CSV, JSON, Single Sign-on, SSO, SAML, LDAP, Karten, Solr, öffentlicher Sektor, integrations FAQ, REST API, API keys, remote services, export, single sign-on, maps, public sector
type: help
---
# FAQ: Integrationen und APIs

Diese Seite beantwortet die Integrationsfragen öffentlicher Stellen zu Exponential. Sie nennt, was vorhanden ist, mit
der Seite, die es dokumentiert, und sagt, wo etwas Eigenentwicklung wäre.

## Inhalt

1. [Gibt es eine API?](#gibt-es-eine-api)
2. [Wie authentifizieren sich Skripte?](#wie-authentifizieren-sich-skripte)
3. [Können andere Websites unsere Inhalte wiederverwenden?](#können-andere-websites-unsere-inhalte-wiederverwenden)
4. [Können wir die Administration aus anderen Systemen steuern?](#können-wir-die-administration-aus-anderen-systemen-steuern)
5. [Können wir Daten in offenen Formaten exportieren?](#können-wir-daten-in-offenen-formaten-exportieren)
6. [Gibt es Single Sign-on?](#gibt-es-single-sign-on)
7. [Gibt es Karten?](#gibt-es-karten)
8. [Können wir eine externe Suchmaschine nutzen?](#können-wir-eine-externe-suchmaschine-nutzen)

## Gibt es eine API?

Ja, eine REST-API für Inhalte in den Versionen 1 und 2, beide bereitgestellt von der Erweiterung `ezprestapi`
([REST-API v1 und v2](../../../anleitungen/rest-api-v1-und-v2.md)).

## Wie authentifizieren sich Skripte?

Mit OAuth oder mit persönlichen API-Schlüsseln: Ein angemeldeter Benutzer erstellt einen Schlüssel, ein Skript sendet
ihn, und die Anfrage läuft als dieser Benutzer, beschränkt auf das, wofür der Schlüssel erstellt wurde; Schlüssel lassen
sich erneuern, widerrufen und laufen ab ([Persönliche API-Schlüssel](../../../anleitungen/api-schluessel.md)).

## Können andere Websites unsere Inhalte wiederverwenden?

Ja, über RSS-Exporte jedes Teils des Baums ([RSS-Feeds](../../../anleitungen/rss-feeds.md)) und die REST-API. RSS-Importe
holen die Feeds anderer Websites herein.

## Können wir die Administration aus anderen Systemen steuern?

Ja: Entfernte Dienste lassen Skripte, Apps und Portale Exponential als HTTP-API aufrufen
([Entfernte Dienste und Apps](../../../anleitungen/entfernte-dienste-und-apps.md)).

## Können wir Daten in offenen Formaten exportieren?

Ja: xrowextract exportiert Inhalte pro Klasse als CSV, XML oder JSON mit einem Manifest; Formulareinsendungen als CSV;
Einwilligungsnachweise als CSV; ganze Datenbanken mit den Werkzeugen der Datenbank
([Von Exponential wegmigrieren](../../../installation/migration/exponential-verlassen.md#1-was-sie-exportieren-sollten)). PDF-Exporte von
Teilen des Baums gibt es ebenfalls ([PDF-Exporte](../../../anleitungen/pdf-exporte.md)).

## Gibt es Single Sign-on?

Die Dokumentation beschreibt die zweistufige Anmeldung und Social-Login-Handler in `sevenx_authentication_2fa`
([Zweistufige Anmeldung](../../../anleitungen/zwei-faktor-anmeldung.md),
[Datenschutzstandards](../../../standards/datenschutz.md#drittanbieterdienste-in-mitgelieferten-extensions)). Single Sign-on mit dem
Identitätsanbieter Ihrer Organisation (zum Beispiel SAML) ist nicht dokumentiert; behandeln Sie es als Eigenentwicklung
und fragen Sie die Bieter, wie sie es bauen und pflegen würden.

## Gibt es Karten?

Die Erweiterung `ezgmaplocation` bietet einen Datentyp für Orte: Die Redaktion sucht einen Punkt auf einer Google-Karte,
und Koordinaten und Adresse werden gespeichert und sind durchsuchbar, mit einem Filter, um Objekte nach Entfernung
abzurufen. Sie braucht einen Google-Maps-Schlüssel und nutzt damit einen Dienst eines Drittanbieters mit eigenen
Datenschutzbedingungen ([ezgmaplocation](../../../funktionen/6.0/erweiterungen/ezgmaplocation.md)). Exponential hat keine
GIS-Funktionen. Kartenfunktionen über eine Ortsmarkierung hinaus sind Eigenentwicklung oder ein getrenntes System.

## Können wir eine externe Suchmaschine nutzen?

Ja: Die Erweiterung ezfind verlagert die Suche der Website auf Apache Solr und ergänzt Facetten, Feinabstimmung der
Relevanz und den Inhalt von Dateien
([Suche mit ezfind und Solr](../../ersteller/website-ersteller/suche-mit-ezfind-und-solr.md)).

## Verwandte Seiten

- [FAQ für Verwaltungen](README.md)
- [Standards zu APIs und Protokollen](../../../standards/apis-und-protokolle.md)
- [FAQ: Suche](faq-suche.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
