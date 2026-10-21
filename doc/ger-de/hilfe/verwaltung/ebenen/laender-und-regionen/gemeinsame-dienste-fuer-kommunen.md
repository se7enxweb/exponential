---
title: Gemeinsame Dienste für die Kommunen einer Region
audience: government, project-manager, admin, website-creator
category: public-sector
order: 70
summary: Wie eine regionale Stelle Websites für die kleineren Kommunen ihres Gebiets auf einer Exponential-Installation hosten kann - ein Siteaccess und eine Sektion je Kommune, ein gemeinsames barrierefreies Design, gemeinsame Inhalte - und was vor dem Beginn vereinbart werden muss.
keywords: gemeinsame Dienste, Kommunen, regionales Hosting, Multisite, Siteaccess je Kommune, gemeinsames Design, gemeinsame Inhalte, Leistungsvereinbarung, Verantwortlicher, interkommunale Zusammenarbeit, shared services, municipalities, regional hosting, multisite, siteaccess per municipality, shared design, shared content, service agreement, data controller
type: help
---
# Gemeinsame Dienste für die Kommunen einer Region

Diese Seite richtet sich an Projektverantwortliche, Administratorinnen und Administratoren und Website-Ersteller einer
regionalen Stelle (oder eines Zusammenschlusses von Kommunen), die Websites für mehrere kleine Kommunen hostet. Kleinen
Kommunen fehlt oft ein Webteam; eine gemeinsame Plattform gibt jeder ihre eigene Website mit einem getesteten Design,
während die Region das System betreibt. Diese Seite erklärt, wie Exponential das unterstützt und was vereinbart werden
muss.

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Das Hosting der
> Website einer anderen Stelle berührt Regeln zu Datenschutz und Beschaffung; prüfen Sie sie mit Ihrer Rechtsberatung.

## Inhalt

1. [Das Modell](#das-modell)
2. [Eine Kommune einrichten](#eine-kommune-einrichten)
3. [Gemeinsame Inhalte](#gemeinsame-inhalte)
4. [Zu treffende Vereinbarungen](#zu-treffende-vereinbarungen)
5. [Grenzen](#grenzen)

## Das Modell

| Teil | Je Kommune | Gemeinsam |
|---|---|---|
| Website | ein Siteaccess mit seinem Hostnamen und Startknoten | |
| Inhalte | ein oberster Ordner im Baum, in einer eigenen Sektion | gemeinsame Seiten, mit zusätzlichen Orten platziert |
| Redaktion | eine Benutzergruppe und eine Rollenzuweisung, eingeschränkt auf ihre Sektion | die Definitionen der Rollen |
| Design | eine Farb- und Logovariante über Einstellungen oder eine kleine Design-Erweiterung | ein barrierefreies Design |
| Klassen | | ein Modell |
| Betrieb | | Sicherungen, Upgrades, Überwachung durch die Region |

Wie Siteaccesses, Designs und Einstellungen zusammenwirken, beschreiben
[Multisite einrichten](../../../ersteller/website-ersteller/multisite-einrichten.md) und
[INI-Overrides bei Multisite](../../../../funktionen/6.0/ini-ueberschreibungen-fuer-mehrere-websites.md). Jede Website kann eigene Protokoll-
und Cache-Pfade haben ([Protokoll- und Cache-Pfade bei Multisite](../../../../funktionen/6.0/log-und-cache-pfade-fuer-mehrere-websites.md)).

## Eine Kommune einrichten

1. **Inhalte.** Legen Sie unter der Inhaltswurzel einen Ordner `<municipality>` an; legen Sie aus Ihrer
   Vorlagenstruktur seine Standardseiten an (Dienstleistungen, Rat, Nachrichten, Kontakt). **Teilbaum kopieren** im
   Menü des Elements kopiert ein vorbereitetes Gerüst.
2. **Sektion.** **Setup > Sektionen**, **Neue Sektion**, Name und Bezeichner (`muni_<name>`), dann **Inhalt zuweisen**
   an den neuen Ordner ([Sektionen](../../../../anleitungen/sektionen.md)). Fügen Sie die Sektion der Richtlinie
   `content/read` der Rolle Anonymous hinzu.
3. **Gruppe und Rolle.** Legen Sie eine Benutzergruppe `<municipality> editors` an; auf der Seite der Rolle Editor
   **Mit Einschränkungen zuordnen**, Sektion = die neue Sektion, dann die Gruppe.
4. **Siteaccess.** Legen Sie `settings/siteaccess/<name>/` mit dem Host, dem Startknoten des Ordners, dem Design und
   den Sprachen an; fügen Sie den Siteaccess zu `[SiteAccessSettings] AvailableSiteAccessList[]` und seiner Zuordnung
   nach Host hinzu. Leeren Sie den INI-Cache: `php bin/php/ezcache.php --clear-tag=ini`.
5. **Prüfen.** Öffnen Sie den Host; melden Sie sich als Redaktion der Kommune an und bestätigen Sie, dass Sie nur Ihren
   Ordner bearbeiten können.

## Gemeinsame Inhalte

| Inhalt | Wie teilen |
|---|---|
| Regionale Dienstleistungen, auf die jede Kommune verlinkt | auf die eigenen Seiten der Region verlinken |
| Gemeinsame rechtliche Seiten (Datenschutzerklärung für die gemeinsame Plattform, Erklärung zur Barrierefreiheit für das gemeinsame Design) | ein Objekt, ein Ort im Baum jeder Kommune, angepasster Text, wo jede Kommune Verantwortliche ist |
| Gemeinsame Nachrichten der Region | ein RSS-Export der Nachrichten der Region, auf jeder Website angezeigt, oder ein gemeinsamer Ordner in jedem Baum |
| Gemeinsame Tags | ein Tag-Baum für Dienstleistungen und Orte ([Verschlagworten mit eztags](../../../ersteller/website-ersteller/verschlagworten-mit-eztags.md)) |

## Zu treffende Vereinbarungen

| Thema | Frage |
|---|---|
| Verantwortung für Inhalte | Jede Kommune steht für ihre eigenen Inhalte ein; wer prüft die Barrierefreiheit ihrer Dokumente? |
| Datenschutz | Wer ist Verantwortlicher und wer Auftragsverarbeiter für Formulare, Konten und Protokolle? Meist ist ein Vertrag zur Auftragsverarbeitung nötig. |
| Service-Level | Verfügbarkeit, Supportzeiten, Wartungsfenster (ein Fenster betrifft jede Website) |
| Ausstieg | Wie eine Kommune aussteigt: Export der Inhalte als Paket, Umleitungen, Entfernen der Konten ([Pakete](../../../../anleitungen/pakete.md)) |
| Kostenteilung | wie Hosting und Entwicklung geteilt werden (von den Mitgliedern entschieden, nicht von der Software) |

## Grenzen

- Eine Installation bedeutet eine Version und ein Wartungsfenster für alle Websites.
- Eine Erweiterung ist für die Installation aktiv (oder bei Access-Erweiterungen je Siteaccess); eine Funktion, die eine
  Kommune möchte, steht allen zur Verfügung oder braucht Sorgfalt in den Einstellungen.
- Suche: Entscheiden Sie, ob die Suche einer Kommune nur ihre eigene Website zeigt; der Fetch des Templates entscheidet.
- Getrennte juristische Personen mit strengen Anforderungen an die Trennung brauchen vielleicht getrennte
  Installationen; siehe
  [Viele Ministerien auf einer Plattform](../national/viele-ministerien-auf-einer-plattform.md#wann-sie-getrennte-installationen-verwenden)
  für dieselbe Entscheidung auf nationaler Ebene.

## Verwandte Seiten

- [Länder und Regionen](README.md)
- [Kommunen](../kommunen/README.md)
- [Gemeinsamer Kopf- und Fußbereich](../../../ersteller/website-ersteller/gemeinsamer-kopf-und-fussbereich.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
