---
title: Glossar für Journalisten
audience: press, advocate
category: press-media
order: 170
summary: Verständliche Erklärungen der Begriffe, die in Beiträgen über Exponential vorkommen - die Produkte und Organisationen, die Geschichte, die Technik (Inhaltsmodell, Siteaccess, Erweiterungen, Webserver, Datenbanken), Open Source und Lizenzen, Sicherheit und Releases - jeweils mit einem Link auf die Seite, die sie ausführlich erklärt.
keywords: Glossar, Begriffe, Definitionen, Wörterbuch, Fachjargon, einfache Sprache, CMS, Inhaltsmodell, Siteaccess, Erweiterung, Fork, GPL, GFDL, CSP, Velocity, Legacy, DXP, glossary, terms, definitions, dictionary, jargon, plain language, content model, extension
featured: true
type: help
---
# Glossar für Journalisten

Diese Seite ist für Journalisten und ihre Leser. Jeder Begriff wird in ein oder zwei einfachen Sätzen erklärt, mit einem
Link auf die Seite, die tiefer geht. Die eigenen Glossare des Projekts sind [das Glossar](../../../eng-us/glossary.md) für
technische Begriffe und das [Glossar der Foundation](../wer-ist-die-exponential-foundation.md#glossar) für die Familie
und die Geschichte.

## Inhalt

1. [Produkte und Organisationen](#produkte-und-organisationen)
2. [Geschichte](#geschichte)
3. [Inhalte und Bearbeitung](#inhalte-und-bearbeitung)
4. [Eine Website betreiben](#eine-website-betreiben)
5. [Open Source und Lizenzen](#open-source-und-lizenzen)
6. [Sicherheit](#sicherheit)
7. [Releases und Entwicklung](#releases-und-entwicklung)

## Produkte und Organisationen

| Begriff | Bedeutung |
|---|---|
| **Exponential** | Ein freies, quelloffenes Content-Management-System und Anwendungsframework in PHP. [Exponential in Kürze](../../../eng-us/help/press/exponential-in-brief.md) |
| **Exponential 6, 6.0.x** | Die aktuelle Hauptlinie, seit dem 1. Januar 2024 |
| **7x** | Das Unternehmen, das Exponential führt; früher Brookins Consulting. [7x für Reporter](7x-fuer-reporter.md) |
| **se7enxweb** | Der Name von 7x auf GitHub und Packagist |
| **die Exponential Foundation** | Die gemeinnützige Organisation, die das Projekt betreibt und finanziert, gegründet am 19. März 2026. [Die Exponential Foundation für Reporter](die-foundation-fuer-reporter.md) |
| **Exponential Velocity (vc)** | Ein in PHP geschriebener Webserver, der die Anwendung zwischen den Anfragen geladen hält. [Thema: Exponential Velocity](thema-exponential-velocity.md) |
| **Exponential Intelligence (expai)** | Ein Schreibassistent in der Administration, der nur Entwürfe schreibt. [Thema: Exponential Intelligence](thema-exponential-intelligence.md) |
| **Exponential Layouts (explayouts)** | Ein visueller Seitenbaukasten: Layouts, Zonen, Blöcke und Regeln. [Thema: Exponential Layouts, der Seitenbaukasten](thema-exponential-layouts.md) |
| **Exponential Platform** | Die auf Symfony basierende Linie: 3.2.9, Legacy, Nexus und DXP. [Die Produktfamilie für Reporter](die-produktfamilie-fuer-reporter.md) |
| **Legacy** | Exponential 6, das neben einer Symfony-Plattform läuft; nicht „alt“ |
| **DXP** | Digital Experience Platform; hier die reine Symfony-Ausgabe Exponential Platform DXP |
| **AdminUI** | Eine alternative Administration, eine Portierung der Netgen AdminUI |
| **admin4** | Das neueste Design der Administration, mit hellem und dunklem Theme |

## Geschichte

| Begriff | Bedeutung |
|---|---|
| **eZ Publish** | Der Name der Software, solange eZ Systems sie entwickelte; ein Produkt, das sein Hersteller nach fast 18 Jahren eingestellt hat. Nur Geschichte. [Über die Jahre von eZ Publish schreiben](ueber-die-ez-publish-jahre-schreiben.md) |
| **eZ Systems, Ibexa** | Der ursprüngliche Entwickler und sein Nachfolgeunternehmen; nicht mit Exponential verbunden. [Nicht verbunden mit eZ Systems oder Ibexa](nicht-verbunden-mit-ez-systems-oder-ibexa.md) |
| **eZ Platform, Ibexa DXP OSS** | Die auf Symfony basierenden Nachfolger von eZ Publish |
| **Fork** | Eine Kopie einer Codebasis, die getrennt weitergeführt wird; das Repository von Exponential ist ein GitHub-Fork |
| **Pflege (Stewardship)** | Das Wort des Projekts für die Obhut von 7x über die Codebasis seit Dezember 2023 |
| **LoveStack** | Die Distribution des Legacy-Kernels von Mugo Web, deren Funktionen 6.0.15 mit Nennung neu gebaut hat |

## Inhalte und Bearbeitung

| Begriff | Bedeutung |
|---|---|
| **Inhaltsmodell** | Die Arten von Inhalten, die eine Website festlegt, und ihre Felder |
| **Inhaltsklasse, Objekt** | Eine Art von Inhalt (Klasse) und ein einzelnes Element davon (Objekt) |
| **Version, Entwurf** | Jede Bearbeitung ist eine neue Version; ein Entwurf ist eine, die noch nicht veröffentlicht ist. [Thema: Inhalte bearbeiten](thema-inhalte-bearbeiten.md) |
| **Sektion, Status** | Möglichkeiten, Inhalte für Berechtigungen und Workflows zu gruppieren |
| **Siteaccess** | Ein Zugang zu einer Installation: eine Website, eine Sprache oder die Administration |
| **Multisite** | Mehrere Websites aus einer Installation |
| **Erweiterung** | Ein Paket, das Funktionen hinzufügt. [Thema: das Ökosystem der Erweiterungen](thema-das-oekosystem-der-erweiterungen.md) |

## Eine Website betreiben

| Begriff | Bedeutung |
|---|---|
| **Webserver** | Das Programm, das Anfragen des Browsers beantwortet: Apache, Nginx, Velocity |
| **PHP-FPM** | Der klassische Weg, PHP hinter einem Webserver zu betreiben |
| **FrankenPHP** | Caddy mit eingebautem PHP, eine alternative Engine |
| **SQLite** | Eine Datenbank in einer Datei, die keinen Server braucht; die Voreinstellung von Exponential. [Thema: SQLite als Voreinstellung](thema-sqlite-als-standard.md) |
| **Composer, Packagist** | Der Paketmanager von PHP und sein öffentliches Verzeichnis |
| **Git** | Das Versionsverwaltungssystem, das das Projekt nutzt; „Git first“-Installationen enthalten die vollständige Historie |

## Open Source und Lizenzen

| Begriff | Bedeutung |
|---|---|
| **Open Source, freie Software** | Software, die jeder nutzen, untersuchen, ändern und weitergeben darf |
| **GPL** | GNU General Public License; die Lizenz der Software, Version 2 oder später |
| **GFDL** | GNU Free Documentation License; die Lizenz der Dokumentation, 1.3 oder später |
| **MIT** | Eine kurze, freizügige Lizenz; die Lizenz von Velocity |
| Mehr | [Lizenzen für Reporter erklärt](lizenzen-fuer-reporter-erklaert.md) |

## Sicherheit

| Begriff | Bedeutung |
|---|---|
| **CSP** | Content Security Policy: sagt dem Browser, welche Skripte eine Seite ausführen darf. [Thema: die Content Security Policy](thema-content-security-policy.md) |
| **Audit-Trail** | Ein manipulationssicheres Protokoll dessen, was in der Administration geschehen ist |
| **Zweistufige Anmeldung (2FA)** | Ein zweiter Faktor bei der Anmeldung, etwa ein Code aus einer App |
| **Vertrauliche Meldung** | Eine Sicherheitslücke vertraulich melden, damit sie behoben ist, bevor Einzelheiten öffentlich werden. [Über Sicherheitskorrekturen berichten](ueber-sicherheitskorrekturen-berichten.md) |

## Releases und Entwicklung

| Begriff | Bedeutung |
|---|---|
| **Tag** | Ein benannter, fester Punkt in der Historie des Codes; ein Release |
| **Changelog** | Die Liste der Änderungen eines Release. [Ein Changelog lesen](ein-changelog-lesen.md) |
| **Main-Branch** | Wo die Entwicklung stattfindet; jederzeit öffentlich |
| **Pull Request** | Eine vorgeschlagene Änderung; das Projekt übernimmt jeden Pull Request |
| **CLA** | Contributor Licensing Agreement: was jemand, der zum Kernel beiträgt, vor der ersten Übernahme einmal unterschreibt; mit der Unterschrift und der Einreichung überträgt er das Urheberrecht an 7x und gibt die Kontrolle ab: 7x darf den Beitrag auf jede Weise nutzen, ändern, neu lizenzieren und verbreiten, ohne zu fragen oder zu bezahlen, und er kann ihn nicht zurückziehen. Die eigenen Erweiterungen der Anwender sind nicht betroffen. [Übertragung des Urheberrechts (CLA)](../../../../CONTRIBUTING.md#copyright-assignment-cla) |
| **In Entwicklung** | Noch nicht veröffentlicht, wie 6.0.15 am 18. Oktober 2026 |

## Verwandte Seiten

- [FAQ für Reporter](faq-fuer-reporter.md)
- [Das Glossar](../../../eng-us/glossary.md)
- [Glossar für Fürsprecher](../fuersprecher/glossar-fuer-fuersprecher.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
