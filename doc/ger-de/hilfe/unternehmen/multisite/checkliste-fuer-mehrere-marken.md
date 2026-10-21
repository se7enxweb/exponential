---
title: Checkliste für mehrere Marken
audience: businesses, project-manager, admin
category: business-solutions
order: 70
summary: Eine Checkliste, um einer Exponential-Installation, die bereits andere Websites bedient, eine Marke, eine Region oder eine Microsite hinzuzufügen - Entscheidungen, Aufbau, Inhalte, Links, Betrieb, Start und der erste Monat.
keywords: Checkliste, mehrere Marken, Multisite, neue Marke, Start, Siteaccess, Design, Links zwischen Websites, Logs, Caches, Unternehmen, checklist, multi-brand, multisite, new brand, launch, cross-site links, business
type: help
---
# Checkliste für mehrere Marken

Diese Seite richtet sich an Projektleiter, Markenverantwortliche und Administratoren, die einer Installation, auf der
bereits andere Websites laufen, eine Marke hinzufügen. Kopieren Sie sie in Ihr Projektwerkzeug und haken Sie sie ab.
Jeder Punkt verlinkt auf die Seite, die ihn erklärt. Die Checkliste setzt voraus, dass die Entscheidungen auf
[Mehrere Marken planen](mehrere-marken-planen.md) getroffen sind.

## Inhalt

1. [Entscheidungen](#entscheidungen)
2. [Aufbau](#aufbau)
3. [Inhalte und Teams](#inhalte-und-teams)
4. [Links und Sprachen](#links-und-sprachen)
5. [Betrieb](#betrieb)
6. [Starttag](#starttag)
7. [Der erste Monat](#der-erste-monat)

## Entscheidungen

- [ ] Die Marke hat einen namentlich benannten Verantwortlichen, der Inhalte und den Start freigibt.
- [ ] Eine Installation wurde bewusst gewählt; die Abwägungen (gemeinsame Upgrades, gemeinsame Wiederherstellungen,
      geteilte Last) stehen im Plan ([Eine Installation oder mehrere](mehrere-marken-planen.md#eine-installation-oder-mehrere)).
- [ ] Es ist entschieden, was die Marke mit den anderen teilt und was sie getrennt hält
      ([Entscheiden, was geteilt wird](inhalte-zwischen-marken-teilen.md#entscheiden-was-geteilt-wird)).
- [ ] Das Adressschema ist gewählt: eigener Host, Pfad unterhalb einer anderen Marke oder beides
      ([Wie Besucher eine Marke erreichen](eine-installation-viele-websites.md#wie-besucher-eine-marke-erreichen)).
- [ ] Die Sprachen der Marke sind entschieden ([Checkliste für Mehrsprachigkeit](../mehrsprachig/checkliste-mehrsprachigkeit.md)).

## Aufbau

- [ ] Unter der Inhaltswurzel gibt es einen Ordner für die Marke.
- [ ] Der oder die Siteaccesses der Marke existieren, mit Website-Name, Adresse, Startseite, Wurzelknoten und Design
      ([Schritt für Schritt: eine zweite Website](../../../../eng-us/help/creators/website-creators/multisite-setup.md#step-by-step-a-second-site)).
- [ ] Der Siteaccess ist registriert und wird erkannt (Host-Zuordnung oder Pfad).
- [ ] Das Design der Marke liegt in einer eigenen Erweiterung, ohne dass Theme-Dateien direkt bearbeitet wurden
      ([Ein Design pro Marke](ein-design-pro-marke.md)).
- [ ] Einstellungen pro Marke liegen nach Möglichkeit in der Website-Erweiterung der Marke
      ([Einstellungen, Logs und Caches pro Website](einstellungen-logs-und-caches-pro-website.md)).
- [ ] Die Administration kennt den neuen Siteaccess (`SiteList[]`), sodass Vorschauen funktionieren.

## Inhalte und Teams

- [ ] Startseite, Menüordner und rechtliche Seiten (Impressum, Datenschutzhinweis, Nutzungsbedingungen) sind
      veröffentlicht.
- [ ] Gemeinsame Inhalte sind platziert oder verknüpft, nicht kopiert.
- [ ] Gemeinsame Ordner, Benutzer und Medien sind als immer verfügbar markiert, wo die Marke andere Sprachen nutzt.
- [ ] Die Redakteure der Marke haben eine Rolle, die auf ihren Ordner beschränkt ist; zentrale Teams behalten ihre
      eigenen Rechte ([Teams in ihrer Marke halten](inhalte-zwischen-marken-teilen.md#teams-in-ihrer-marke-halten)).
- [ ] Ein Freigabe-Workflow ist eingerichtet, falls die Inhalte der Marke freigegeben werden müssen
      ([Workflows](../../../anleitungen/workflows.md)).

## Links und Sprachen

- [ ] `ezurlmultisite` führt die neue Marke mit der Knoten-ID **dieser** Installation auf
      ([Links zwischen Ihren Websites](links-zwischen-ihren-websites.md)).
- [ ] Links von anderen Marken in die neue und zurück öffnen die richtige Website (Tag-Seiten, Suche, verwandte
      Elemente).
- [ ] Wenn die Marke mehrere Sprachen hat: Der Sprachumschalter verlinkt jede Seite auf dieselbe Seite in der anderen
      Sprache ([Sprachumschalter und Adressen pro Sprache](../../../../eng-us/help/creators/website-creators/language-switcher-and-language-urls.md)).

## Betrieb

- [ ] Das DNS für die Hostnamen der Marke zeigt auf den Server.
- [ ] Die Zertifikate decken jeden Hostnamen ab
      ([Online gehen: Hosts, HTTPS und Siteaccesses](../../../../eng-us/help/creators/website-creators/going-live-hosts-https.md)).
- [ ] Logs und Caches der Marke landen dort, wo der Administrator sie erwartet; die Aufbewahrung ist abgestimmt.
- [ ] Das Monitoring prüft die Adresse der Marke, nicht nur die der Hauptmarke
      ([Zustandsprüfungen und Verfügbarkeit](../../hosting/betrieb/zustandspruefungen-und-verfuegbarkeit.md)).
- [ ] Das Backup deckt die Marke ab, und alle wissen, dass eine Wiederherstellung alle Marken wiederherstellt.
- [ ] Die Caches für die neue Marke sind vorgewärmt, bevor sie angekündigt wird
      ([Caches vorwärmen](../../../anleitungen/caches-vorwaermen.md)).

## Starttag

- [ ] Der INI-Cache ist nach der letzten Änderung der Einstellungen geleert; unter Velocity ist Velocity neu gestartet.
- [ ] Die Startseite der Marke antwortet unter jeder Adresse über HTTPS mit `200`.
- [ ] Eine Testbestellung, ein Formular oder eine Anmeldung funktioniert, falls die Marke so etwas hat.
- [ ] Die anderen Marken sehen genauso aus und verhalten sich genauso wie vorher.
- [ ] Sitemaps und Feeds der Marke zeigen auf ihre eigene Adresse
      ([Sitemaps](../../../../eng-us/help/creators/website-creators/sitemaps.md),
      [Feeds und Syndikation](../mobil-und-headless/feeds-und-syndication.md)).

## Der erste Monat

- [ ] Das Fehlerlog der Marke wird wöchentlich gelesen ([Logs ohne Shell-Zugang lesen](einstellungen-logs-und-caches-pro-website.md#logs-ohne-shell-zugang-lesen)).
- [ ] Fehlerhafte Links werden geprüft ([Fehlerhafte Links und Inhaltsprüfungen](../../../../eng-us/help/creators/website-creators/link-and-content-checks.md)).
- [ ] Der Markenverantwortliche bestätigt, dass die Redakteure ohne Hilfe arbeiten können.
- [ ] Die gewonnenen Erkenntnisse werden für die nächste Marke in diese Checkliste aufgenommen.

> **Tipp**
> Bewahren Sie eine ausgefüllte Kopie dieser Checkliste bei den Projektunterlagen jeder Marke auf. Wenn die
> Installation aktualisiert wird, sagt Ihnen dieselbe Liste, welche Marken und welche Funktionen Sie testen müssen.

## Verwandte Seiten

- [Mehrere Marken in einer Installation](README.md)
- [Mehrere Marken planen](mehrere-marken-planen.md)
- [Multisite- und mehrsprachige Projekte planen: Checklisten](../../fuehrung/multisite-und-mehrsprachigkeit-planen.md#checklisten)
- [Start-Checkliste für Websites](../../../../eng-us/help/creators/website-creators/launch-checklist.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
