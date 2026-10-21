---
title: Intranets und Subsites von Fachbereichen
audience: government, project-manager, admin, website-creator
category: public-sector
order: 70
summary: Wie eine Fachhochschule oder Universität viele Subsites von Fachbereichen und ein Intranet für Beschäftigte und Lernende auf einer Exponential-Installation betreibt - Siteaccesses, Sektionen, ein gemeinsames Design mit Varianten, Anmeldung und die Entscheidungen, die das Ganze beherrschbar halten.
keywords: Intranet, Subsites von Fachbereichen, Multisite, Siteaccess, Universität, Websites von Fakultäten, eingeschränkte Sektion, gemeinsames Design, websiteübergreifende Links, Portal für Beschäftigte, intranet, department subsites, multisite, university, faculty sites, restricted section, RequireUserLogin, shared design, cross-site links, staff portal
type: help
---
# Intranets und Subsites von Fachbereichen

Diese Seite richtet sich an Projektverantwortliche, Administratorinnen und Administratoren und Website-Ersteller einer
Fachhochschule, einer Universität oder eines Schulträgers mit vielen Fachbereichen. Sie zeigt, wie eine
Exponential-Installation die zentrale Website, Subsites der Fachbereiche und ein Intranet bedient und was zu entscheiden
ist, damit Dutzende Websites beherrschbar bleiben. Die Mechanismen sind unter
[Multisite einrichten](../../../ersteller/website-ersteller/multisite-einrichten.md),
[Sektionen](../../../../anleitungen/sektionen.md) und auf den Funktionsseiten zu Multisite beschrieben.

## Inhalt

1. [Drei Formen](#drei-formen)
2. [Subsites von Fachbereichen](#subsites-von-fachbereichen)
3. [Das Intranet](#das-intranet)
4. [Gemeinsames Design mit Varianten](#gemeinsames-design-mit-varianten)
5. [Steuerung, die das Ganze beherrschbar hält](#steuerung-die-das-ganze-beherrschbar-hält)

## Drei Formen

| Form | Fachbereiche erhalten | Geeignet für |
|---|---|---|
| Seiten in der zentralen Website | einen Ordner und eine Sektion | die meisten Schulen und kleinen Hochschulen |
| Subsites auf einer Installation | ihren eigenen Hostnamen und Startknoten, gemeinsame Klassen und gemeinsames Design | Universitäten mit vielen Fakultäten |
| Getrennte Installationen | alles Eigene | Einheiten, die eigene Rechtsträger sind oder einen eigenen Release-Zyklus brauchen |

Der Rest dieser Seite behandelt die mittlere Form und das Intranet.

## Subsites von Fachbereichen

Jede Subsite ist ein Siteaccess ([Multisite einrichten](../../../ersteller/website-ersteller/multisite-einrichten.md)):

| Teil | Einstellung oder Ort |
|---|---|
| Hostname, zum Beispiel `<department>.example.edu` | Zuordnung nach Host ([Zuordnung von Siteaccesses nach Host und Sprache](../../../../funktionen/6.0/siteaccess-zuordnung-nach-host-und-sprache.md)) |
| Startknoten | der Ordner des Fachbereichs, gesetzt in der `site.ini` des Siteaccess |
| Design | das gemeinsame Design, mit der Variante des Fachbereichs |
| Abweichende Einstellungen | `settings/siteaccess/<name>/` ([INI-Overrides bei Multisite](../../../../funktionen/6.0/ini-ueberschreibungen-fuer-mehrere-websites.md)) |
| Links zu anderen Subsites | mit dem richtigen Host geschrieben ([Websiteübergreifende Links](../../../../funktionen/6.0/websiteuebergreifende-links-im-multisite.md)) |
| Protokolle und Caches je Website | [Protokoll- und Cache-Pfade bei Multisite](../../../../funktionen/6.0/log-und-cache-pfade-fuer-mehrere-websites.md) |

Die Redaktion der Fachbereiche arbeitet in der einen Administration; ihre Rolle ist mit der Sektion des Fachbereichs
zugewiesen, sodass sie nur ihre eigenen Inhalte sieht und ändert
([Struktur, Sektionen und Rollen](struktur-sektionen-und-rollen.md)). Gemeinsame Elemente (ein Studiengang, den zwei
Fachbereiche anbieten, eine gemeinsame Veranstaltung) sind ein Objekt mit Orten in beiden Subsites.

## Das Intranet

Zwei Wege zu einem Intranet:

| Weg | Wie | Wirkung |
|---|---|---|
| Eine eingeschränkte Sektion im öffentlichen Siteaccess | die Sektion `intranet`, für Anonymous nicht lesbar; Beschäftigte und Lernende melden sich auf der Website an | eine Website; öffentliche und Intranetseiten nach der Anmeldung nebeneinander |
| Ein eigener Siteaccess für das Intranet | eigener Host, `[SiteAccessSettings] RequireUserLogin=true` | alles verlangt zuerst eine Anmeldung; das Intranet hat sein eigenes Aussehen und Menü |

`RequireUserLogin` ist eine Einstellung des Siteaccess (das Standardpaket setzt sie für die öffentliche Website auf
`false`, [Siteaccesses und Sprachen](../../../seitenpakete/sevenx-multisite/siteaccesses-und-sprachen-von-sevenx-multisite.md)). Halten Sie
Intranetinhalte auch damit in einer Sektion, die Anonymous nicht lesen kann: Die Sektion ist das, was die Inhalte in
jedem Siteaccess schützt, einschließlich Suche und Feeds. Ein Siteaccess, der eine Anmeldung verlangt, wird außerdem bei
der Erzeugung des statischen Cache ausgelassen
([Generator für den statischen Cache](../../../../funktionen/6.0/generator-fuer-den-statischen-cache.md)).

Intranetinhalte, die im Netz der Einrichtung bleiben müssen, lassen sich zusätzlich auf Ebene des Webservers oder von
Velocity schützen (nach Adresse); das ist eine Entscheidung des Betriebs.

## Gemeinsames Design mit Varianten

Eine Design-Erweiterung für alle Subsites hält Tests der Barrierefreiheit und Upgrades an einer Stelle. Varianten je
Fachbereich (eine Farbe, ein Logo, ein Kopfbild) kommen aus Einstellungen oder aus einer kleinen Design-Erweiterung, die
in der Designliste des Siteaccess vor der gemeinsamen steht
([Designs und die Fallback-Kette](../../../ersteller/website-ersteller/designs-und-die-fallback-kette.md)). Widerstehen Sie
einem Design je Fachbereich: Jedes wird für sich getestet, gepflegt und aktualisiert.

## Steuerung, die das Ganze beherrschbar hält

| Regel | Warum |
|---|---|
| Eine Subsite braucht eine benannte verantwortliche Person und eine Redaktion, bevor sie angelegt wird | verwaiste Subsites veralten |
| Subsites verwenden die gemeinsamen Klassen; neue Klassen gehen über das Webteam | ein Modell, eine Schulung |
| Eine jährliche Überprüfung: Jeder Fachbereich bestätigt seine verantwortliche Person und gibt veraltete Seiten auf | Verfall der Inhalte |
| Konten folgen dem akademischen Jahr | Ausscheidende deaktiviert ([Benutzerkonten prüfen](../../../../funktionen/6.0/benutzerpflege.md)) |
| Ein Wartungsfenster, allen angekündigt | der Wartungsmodus betrifft jede Website ([Wartungsmodus](../../../../anleitungen/wartungsmodus.md)) |

## Verwandte Seiten

- [Schulen und Hochschulen](README.md)
- [Viele Ministerien auf einer Plattform](../national/viele-ministerien-auf-einer-plattform.md)
- [Sektionen, 13.2: ein Intranet mit Abteilungen](../../../../anleitungen/sektionen.md#132-ein-intranet-mit-abteilungen)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
