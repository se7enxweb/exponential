---
title: Checkliste mobil
audience: businesses, project-manager, app-creator
category: business-solutions
order: 110
summary: Eine Checkliste für den Start einer mobilen Website, einer App auf der REST API, einer Partnerintegration oder eines Feeds mit Exponential - Strategie, responsive Website, API und Zugangsdaten, Inhalte und Vorschau, Feeds und Partner, Zuverlässigkeit und die Routine nach dem Start.
keywords: Checkliste, mobil, App-Start, responsiv, REST API, API-Schlüssel, OAuth, Feeds, Partner, Zuverlässigkeit, Navigationscache, Unternehmen, checklist, mobile, app launch, responsive, API keys, feeds, partners, reliability, navigation cache, business
type: help
---
# Checkliste mobil

Diese Seite ist für Projektleitungen, Produktverantwortliche und Administratoren, die etwas für Telefone oder für andere
Programme starten: eine responsive Website, eine App, eine Partnerintegration, einen Feed. Kopieren Sie die
zutreffenden Teile in Ihr Werkzeug zur Projektverfolgung. Jeder Punkt verlinkt die Seite, die ihn erklärt.

## Inhalt

1. [Strategie](#strategie)
2. [Responsive Website](#responsive-website)
3. [API und Zugangsdaten](#api-und-zugangsdaten)
4. [Inhalte, Vorschau und Sprachen](#inhalte-vorschau-und-sprachen)
5. [Feeds und Partner](#feeds-und-partner)
6. [Zuverlässigkeit](#zuverlässigkeit)
7. [Start](#start)
8. [Nach dem Start](#nach-dem-start)

## Strategie

- [ ] Die Kanäle sind danach gewählt, was Kunden tun, gemessen an Ihren eigenen Daten
      ([Mobile Strategie](mobile-strategie.md#bei-dem-beginnen-was-kunden-tun)).
- [ ] Zuerst die Website: Die Website funktioniert auf Telefonen, bevor eine App begonnen wird.
- [ ] Für eine App: hybrid (Website in Templates, App auf der API), wenn nichts dagegen spricht
      ([Die drei Optionen](mobile-strategie.md#die-drei-optionen)).
- [ ] Jede Zeile von „was eine App Ihnen an Arbeit hinzufügt“ hat eine verantwortliche Person und ein Budget
      ([Mobile Strategie](mobile-strategie.md#was-eine-app-ihnen-an-arbeit-hinzufügt)).

## Responsive Website

- [ ] Das Design funktioniert ab 320 px Breite, ohne horizontales Scrollen und mit Zoom auf 200 %
      ([Responsive Websites](responsive-websites.md#das-design-entscheidet)).
- [ ] Bilder verwenden Aliase, die für kleine Bildschirme bemessen sind
      ([Bilder, die auf kleine Bildschirme passen](responsive-websites.md#bilder-die-auf-kleine-bildschirme-passen)).
- [ ] Formulare und Bestellablauf bestehen den Telefontest
      ([Ein Testplan, den Sie selbst ausführen können](responsive-websites.md#ein-testplan-den-sie-selbst-ausführen-können)).
- [ ] Redakteure wissen, dass sie in der Administration vom Telefon aus arbeiten können
      ([Redakteure auf Telefonen](mobile-strategie.md#redakteure-auf-telefonen)).

## API und Zugangsdaten

- [ ] `ezprestapi` 1.2.6 oder neuer ist aktiv und `ezprestapiprovider` entfernt
      ([Fragen an Ihren Administrator](rest-api-v1-und-v2-fuer-unternehmen.md#fragen-an-ihren-administrator)).
- [ ] Die App liest in v1 oder v2; Schreibzugriffe, falls vorhanden, in v2; keine Aktualisierung über REST geplant
      (noch nicht umgesetzt).
- [ ] Die richtigen Zugangsdaten pro Programm: OAuth2 für Apps, bei denen sich Benutzer anmelden, ein persönlicher
      Schlüssel für das Skript eines Benutzers, ein Token für expservices für Remote-Dienste
      ([Apps und API-Schlüssel](apps-und-api-schluessel.md)).
- [ ] Ein eigener Integrationsbenutzer pro App und Partner, mit der kleinsten Rolle und dem kleinsten Bereich.
- [ ] Geheimnisse auf einem Server oder in einem Geheimnisspeicher, nie in der App oder einem Repository.
- [ ] HTTPS für jede API-Anfrage vorgeschrieben.
- [ ] Der Header `Authorization` erreicht PHP auf Ihrem Webserver.

## Inhalte, Vorschau und Sprachen

- [ ] Die Inhalte, die die App liest, sind benannt: Klassen, Ordner, Sprachen.
- [ ] Redakteure können Inhalte vor dem Veröffentlichen so in der Vorschau sehen, wie sie in der App erscheinen
      ([Dinge, die früh zu planen sind](headless-mit-der-rest-api.md#dinge-die-früh-zu-planen-sind)).
- [ ] Die App erfährt von Änderungen über einen Purge-Hook oder über begrenzte Abfragen.
- [ ] Bildaliase für die Größen der App existieren.
- [ ] Für jede Sprache der App sind die Inhalte übersetzt
      ([Checkliste Mehrsprachigkeit](../mehrsprachig/checkliste-mehrsprachigkeit.md)).

## Feeds und Partner

- [ ] Feeds haben Quellen, Adressen, Formate und Autodiscovery-Links
      ([Checkliste für Feeds](feeds-und-syndication.md#checkliste-für-feeds)).
- [ ] Podcast-Feeds haben Inhaber, Artwork und Kategorie sowie abspielbare Folgen.
- [ ] Endpunkte der Syndication sind geschützt; Zugangsdaten sind außerhalb der Versionsverwaltung.
- [ ] Partner haben schriftliche Vereinbarungen dazu, was sie zeigen dürfen, zu Aktualisierungsintervallen und zu einem
      Ausstiegsplan ([Vereinbarungen und Kontrollen](verkaufen-ueber-netzwerke-und-partner.md#vereinbarungen-und-kontrollen)).

## Zuverlässigkeit

- [ ] Das Monitoring prüft die Website, die Anmeldung der Administration und einen authentifizierten API-Lesezugriff
      ([Beobachten: Health-Checks](zuverlaessigkeit-des-app-backends.md#beobachten-health-checks)).
- [ ] Die App behandelt 503 mit `Retry-After`, 401, 403 und Ratenbegrenzungen
      ([Was die App tun muss](zuverlaessigkeit-des-app-backends.md#was-die-app-tun-muss)).
- [ ] Ratenbegrenzungen eingerichtet und durchsetzbar (APCu verfügbar).
- [ ] Ablaufdaten von Schlüsseln und Tokens im Betriebskalender.
- [ ] Wenn der Navigationscache im Browser verwendet wird: Der Support kennt die „zweite Ansicht“, und jemand kann ihn
      ausschalten ([Offline und wiederholte Besuche](offline-und-wiederholte-besuche.md)).

## Start

- [ ] Ein Testbenutzer installiert die App, meldet sich an, liest Inhalte und bestellt oder schickt, falls angeboten, ein
      Formular ab.
- [ ] Eine in der Administration veröffentlichte Änderung erscheint innerhalb der vereinbarten Zeit in der App.
- [ ] Das Widerrufen der Testzugangsdaten beendet den Zugriff bei der nächsten Anfrage.
- [ ] Die Website sieht genauso aus und verhält sich genauso wie vorher.
- [ ] Partner bestätigen, dass ihre Feeds oder Importe funktionieren.

## Nach dem Start

- [ ] Monatlich: die Übersicht der API-Schlüssel nach nie benutzten, bald ablaufenden oder abgelehnten Schlüsseln
      ([Steuerung, Begrenzungen und der Audit-Trail](apps-und-api-schluessel.md#steuerung-begrenzungen-und-der-audit-trail)).
- [ ] Monatlich: Feed-Exporte und -Importe sind gelaufen; der Status der Syndication hat keine fehlgeschlagenen
      Einträge.
- [ ] Nach jeder Aktualisierung: Die API-Prüfung, eine Prüfung der Feeds und der Telefontest werden wiederholt.
- [ ] Wenn ein Partner oder eine App außer Betrieb geht: Ihre Zugangsdaten werden am selben Tag widerrufen.

## Verwandte Seiten

- [Mobile Apps, Headless und Netzwerke](README.md)
- [Checkliste für mehrere Marken](../multisite/checkliste-fuer-mehrere-marken.md)
- [Vorlage Checkliste Go-live](../vorlagen/checkliste-go-live.md)
- [Die Checkliste für den Start](../../../../eng-us/help/creators/website-creators/launch-checklist.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
