---
title: Viele Ministerien auf einer Plattform
audience: government, project-manager, admin, website-creator
category: public-sector
order: 70
summary: Wie sich mehrere Ministerien oder Ressorts eine Exponential-Installation teilen können - ein Siteaccess und eine Sektion je Ministerium, ein gemeinsames Design, gemeinsame und getrennte Inhalte - und wann getrennte Installationen die bessere Wahl sind.
keywords: Multisite, gemeinsame Plattform, Regierungsportal, Ministerien, Siteaccess, Sektionen, gemeinsames Design, websiteübergreifende Links, zentrales Webteam, getrennte Installationen, multisite, shared platform, government portal, ministries, sections, shared design, cross-site links, central web team, separate installations
type: help
---
# Viele Ministerien auf einer Plattform

Diese Seite richtet sich an Projektverantwortliche, Administratorinnen und Administratoren und Website-Ersteller eines
zentralen Webteams der Regierung, das Websites für mehrere Ministerien oder Ressorts betreibt. Sie erklärt, wie eine
Exponential-Installation mehrere Websites bedient, wie jedes Ministerium auf seine eigenen Inhalte beschränkt bleibt,
was sich teilen lässt und wann getrennte Installationen besser sind. Die Mechanismen sind unter
[Multisite einrichten](../../../ersteller/website-ersteller/multisite-einrichten.md) und auf den Funktionsseiten zu
Multisite dokumentiert.

## Inhalt

1. [Eine Installation, mehrere Websites](#eine-installation-mehrere-websites)
2. [Jedes Ministerium auf seine eigenen Inhalte beschränken](#jedes-ministerium-auf-seine-eigenen-inhalte-beschränken)
3. [Was sich teilen lässt](#was-sich-teilen-lässt)
4. [Wann Sie getrennte Installationen verwenden](#wann-sie-getrennte-installationen-verwenden)
5. [Die Plattform betreiben](#die-plattform-betreiben)

## Eine Installation, mehrere Websites

In Exponential ist eine Website ein **Siteaccess**: ein Name mit eigenen Einstellungen (Host, Design, Sprachen,
Startknoten), der einen Teil des gemeinsamen Inhaltsbaums bedient. Mehrere Siteaccesses laufen auf einer Installation
und einer Datenbank.

| Teil | Einer je Ministerium | Gemeinsam |
|---|---|---|
| Siteaccess (Hostname, Einstellungen in `settings/siteaccess/<name>/`) | ja | |
| Startknoten der Website (ein oberster Ordner im Baum) | ja | |
| Design | kann sein | meist eine gemeinsame Design-Erweiterung der Regierung |
| Inhaltsklassen | | ja |
| Benutzer, Rollen, Sektionen | Gruppen und Sektionen je Ministerium | ein gemeinsamer Benutzerbestand |
| Caches, Protokolle | getrennte Pfade verfügbar ([Protokoll- und Cache-Pfade bei Multisite](../../../../funktionen/6.0/log-und-cache-pfade-fuer-mehrere-websites.md)) | |

Einstellungen können je Website mit INI-Overrides abweichen
([INI-Overrides bei Multisite](../../../../funktionen/6.0/ini-ueberschreibungen-fuer-mehrere-websites.md)). Links von einer Website auf eine
Seite einer anderen werden mit dem richtigen Host geschrieben
([Websiteübergreifende Links](../../../../funktionen/6.0/websiteuebergreifende-links-im-multisite.md)). Die Zuordnung nach Host und
Sprache entscheidet, welcher Siteaccess eine Anfrage beantwortet
([Zuordnung von Siteaccesses nach Host und Sprache](../../../../funktionen/6.0/siteaccess-zuordnung-nach-host-und-sprache.md)).

## Jedes Ministerium auf seine eigenen Inhalte beschränken

| Maßnahme | Wie |
|---|---|
| Eine Sektion je Ministerium | den obersten Ordner des Ministeriums seiner Sektion zuweisen ([Sektionen](../../../../anleitungen/sektionen.md)) |
| Rollen mit einer Einschränkung nach Sektion oder Teilbaum zugewiesen | die gemeinsame Rolle Editor, je Gruppe eines Ministeriums mit dessen Sektion zugewiesen |
| Der Baum der Administration beginnt beim Ordner des Ministeriums | die Einstellung für den obersten Knoten des Inhaltsbaums ([Oberster Knoten des Inhaltsbaums der Administration](../../../../funktionen/6.0/oberster-knoten-des-inhaltsbaums-im-admin.md)) |
| Freigebende je Ministerium | ein Ereignis *Freigeben* je Ministerium, auf seine Sektion eingeschränkt |
| Audit je Ministerium | das Audit-Protokoll zeichnet Benutzer und Objekt auf; filtern Sie danach ([Audit-Protokoll](../../../../funktionen/6.0/pruefprotokoll.md)) |

Testen Sie die Trennung als Benutzer jedes Ministeriums: Sie dürfen die Inhalte eines anderen Ministeriums nicht
bearbeiten, verschieben oder entfernen können, und Links der Administration, die sie nicht nutzen können, sind
ausgeblendet ([Links der Administration folgen den Berechtigungen](../../../../funktionen/6.0/admin-links-folgen-den-berechtigungen.md)).

## Was sich teilen lässt

| Gemeinsamer Teil | Nutzen | Sorgfalt |
|---|---|---|
| Die Design-Erweiterung | ein barrierefreies Design, einmal getestet | jedes Ministerium möchte vielleicht eine Variante: ein Design je Siteaccess auf gemeinsamer Grundlage |
| Inhaltsklassen | ein Modell, eine Schulung | eine Änderung an einer Klasse betrifft jedes Ministerium |
| Gemeinsame Seiten (Datenschutzerklärung, Erklärung zur Barrierefreiheit, Informationen zu Cookies) | einmal geschrieben, mit zusätzlichen Orten in mehrere Websites gestellt | der Text muss zu jedem Ministerium passen |
| Tags | eine Taxonomie über die gesamte Regierung | eine verantwortliche Stelle für das Vokabular |
| Suche | ein Index, eine Suche je Website oder über alle Websites | entscheiden, was Besucher erwarten |

## Wann Sie getrennte Installationen verwenden

| Wählen Sie getrennte Installationen, wenn | Weil |
|---|---|
| Ministerien getrennte Verantwortliche im Sinne des Datenschutzes mit getrennter Sicherheitsakkreditierung sind | eine Datenbank ihre personenbezogenen Daten vermischen würde |
| Release-Zyklen sich unterscheiden (ein Ministerium kann ein Upgrade nicht annehmen, wenn ein anderes es braucht) | eine Installation eine Version bedeutet |
| Ein Ministerium Erweiterungen braucht, die andere nicht ausführen dürfen | Erweiterungen je Installation aktiv sind, teilweise je Siteaccess |
| Die Plattform weiter ausliefern muss, wenn eine Website angegriffen oder überlastet wird | getrennte Installationen getrennt ausfallen |

Getrennte Installationen können Design und Klassen weiterhin teilen, als Erweiterung und als Pakete
([Pakete](../../../../anleitungen/pakete.md)).

## Die Plattform betreiben

- **Ein Wartungsfenster für alle.** Der Wartungsmodus nimmt die gesamte Installation vom Netz
  ([Wartungsmodus](../../../../anleitungen/wartungsmodus.md)); kündigen Sie ihn jedem Ministerium an.
- **Upgrades** betreffen jede Website; testen Sie zuerst die Templates jeder Website auf der Testinstallation
  ([Aktualisieren](../../../../anleitungen/aktualisieren.md)).
- **Caches vorwärmen** je Website nach einer Bereitstellung ([Caches vorwärmen](../../../../anleitungen/caches-vorwaermen.md)).
- **Service-Level**: Halten Sie fest, welches Team für die Plattform und welches für die Inhalte jedes Ministeriums
  einsteht.

## Verwandte Seiten

- [Nationale Regierung](README.md)
- [Multisite einrichten](../../../ersteller/website-ersteller/multisite-einrichten.md)
- [Gemeinsamer Kopf- und Fußbereich](../../../ersteller/website-ersteller/gemeinsamer-kopf-und-fussbereich.md)
- [Kurs Multisite und Pakete](../../../trainer/multisite-und-pakete/README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
