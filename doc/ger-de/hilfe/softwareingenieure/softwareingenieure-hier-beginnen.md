---
title: "Softwareingenieure: hier beginnen"
audience: software-engineer, developer
category: software-engineering
order: 1
summary: Wo Softwareingenieure beginnen, die Exponential als System bewerten, entwerfen, betreiben, prüfen oder dazu beitragen. Das System in einem Absatz, Ihre erste Stunde, fünf technische Regeln und eine Übersicht über jede Seite für Softwareingenieure, von Architektur, Daten und Caching bis zu Anwendungsservern, Tests, CI, Release Engineering, Sicherheit und Beiträgen an Upstream.
keywords: Softwareingenieur, Softwaretechnik, Architektur, Systementwurf, Bewertung, Code-Review, Betrieb, CI, Tests, Release Engineering, hier beginnen, Überblick, Index, Übersicht, software engineer, engineering, architecture, system design, evaluation, code review, operations, testing, release engineering, start here, overview
featured: true
type: help
---
# Softwareingenieure: hier beginnen

Dieser Bereich der Hilfe richtet sich an Softwareingenieure, gleich in welcher Sprache Sie sonst schreiben: an Menschen,
die Exponential für ein Projekt bewerten, ein System darum herum entwerfen, es in der Produktion betreiben, Änderungen
daran prüfen oder Code zurückgeben. Er erklärt das System als System: seine Teile und ihre Verträge, wie eine Anfrage
hindurchläuft, wo Daten liegen, welche Caches wo sitzen, wie es ausgeliefert, getestet, veröffentlicht und abgesichert
wird und welche Dokumente für jeden dieser Punkte maßgeblich sind.

Diese Seiten wiederholen nicht die ausführlichen Anleitungen. Sie geben Ihnen die technische Sicht und verweisen auf die
Seite mit den Details, meist in `doc/eng-us/standards/`, `doc/eng-us/bc/6.0/`, `doc/eng-us/features/6.0/` oder den
Anleitungen. Wo die Quellen eine Frage nicht beantworten, sagen die Seiten das, statt zu raten.

## Inhalt

1. [Exponential in einem Absatz](#exponential-in-einem-absatz)
2. [Ihre erste Stunde](#ihre-erste-stunde)
3. [Fünf technische Regeln](#fünf-technische-regeln)
4. [Übersicht der Seiten für Softwareingenieure](#übersicht-der-seiten-für-softwareingenieure)
5. [Seiten anderer Zielgruppen, die Sie nutzen werden](#seiten-anderer-zielgruppen-die-sie-nutzen-werden)
6. [Diese Seiten wiederfinden](#diese-seiten-wiederfinden)

## Exponential in einem Absatz

**Exponential** ist ein freies, quelloffenes PHP-Content-Management-System und Anwendungs-Framework, lizenziert unter der
GNU GPL, Version 2 oder später. Sein README nennt es "a professional PHP application framework with advanced CMS (content
management system) functionality", dessen bemerkenswerteste Eigenschaft "its fully customizable and extendable content
model" ist. Der Kernel (`kernel/`, `lib/`) läuft auf PHP 8.0 bis 8.5, speichert Inhalte in SQLite 3 (Standard),
PostgreSQL, MongoDB, MariaDB, Oracle oder MySQL, gibt über seine eigene Template-Sprache aus und wird über Erweiterungen
in `extension/` erweitert. Seit 6.0.15 liefert es einen eigenen Anwendungsserver mit, Exponential Velocity, neben Apache
oder nginx mit PHP-FPM und FrankenPHP. Die Codebasis wurde ab 1999 entwickelt, zuerst unter dem Namen eZ Publish, einem
Produkt, das sein Hersteller nach fast 18 Jahren eingestellt hat; heute führt 7x die Entwicklung, und die Exponential
Foundation betreibt und finanziert das Projekt ([Wer ist 7x?](../wer-ist-7x.md),
[Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md)).

## Ihre erste Stunde

1. **Die Gestalt erfassen** (10 Minuten): [Exponential für Softwareingenieure](exponential-fuer-softwareingenieure.md)
   und [Architekturüberblick](architekturueberblick.md).
2. **Einer Anfrage folgen** (10 Minuten): [Der Lebenszyklus einer Anfrage](lebenszyklus-einer-anfrage.md).
3. **Wissen, wo Zustand liegt** (10 Minuten): [Datenbanken: eine wählen und betreiben](datenbanken-und-die-wahl-einer-datenbank.md)
   und [Caching-Ebenen für Ingenieure](caching-ebenen-fuer-ingenieure.md).
4. **Wissen, wie es läuft** (10 Minuten): [Anwendungsserver](anwendungsserver.md) und
   [Nebenläufigkeit, Statics und langlebige Prozesse](nebenlaeufigkeit-statics-und-langlebige-prozesse.md).
5. **Wissen, wie Änderungen kontrolliert werden** (10 Minuten): [Teststrategie](teststrategie.md),
   [CI-Gates](ci-gates.md) und [Release Engineering](release-engineering.md).
6. **Den Sicherheitsvertrag kennen** (10 Minuten): [Sicherheitstechnik im Überblick](sicherheitstechnik-im-ueberblick.md).

## Fünf technische Regeln

1. **Ändern Sie nie `kernel/`, `lib/` oder eine andere Erweiterung an Ort und Stelle.** Eine Aktualisierung ersetzt
   sie. Verwenden Sie die Erweiterungspunkte; ein Override einer Kernel-Klasse ist das letzte Mittel
   ([Events, Erweiterungspunkte und Plugins](events-erweiterungspunkte-und-plugins.md)).
2. **Gehen Sie von einem langlebigen Prozess aus.** Unter Velocity bedient ein Worker viele Anfragen: keine Daten je
   Anfrage in Statics ohne Zurücksetzen, idempotente Registrierungen
   ([Nebenläufigkeit, Statics und langlebige Prozesse](nebenlaeufigkeit-statics-und-langlebige-prozesse.md)).
3. **Schreiben Sie für jede Datenbank.** Gehen Sie über `eZDB::instance()`, maskieren und casten Sie, und liefern Sie
   Schemaänderungen für jeden Treiber mit ([Portables SQL schreiben](portables-sql-schreiben.md)).
4. **Ein veröffentlichter Tag ändert sich nie.** Ein Fehler in einem Release wird durch die nächste Version behoben,
   nie durch Verschieben eines Tags ([Versionsnummern und unveränderliche Tags](versionsnummern-und-unveraenderliche-tags.md)).
5. **Jeder Bruch hat Upgrade-Hinweise.** Eine Änderung, die von einer Installation eine Handlung verlangt, erhält eine
   Seite in `doc/eng-us/bc/6.0/` ([Richtlinie zur Rückwärtskompatibilität](richtlinie-zur-rueckwaertskompatibilitaet.md)).

## Übersicht der Seiten für Softwareingenieure

### Orientierung

| Seite | Wobei sie Ihnen hilft |
|---|---|
| [Exponential für Softwareingenieure](exponential-fuer-softwareingenieure.md) | das System, seinen Stack und seine Einschränkungen auf einer Seite verstehen |
| [Lesepfad für Softwareingenieure](lesepfad-fuer-softwareingenieure.md) | die Seiten in sinnvoller Reihenfolge lesen, nach Rolle |
| [Exponential bewerten](exponential-bewerten.md) | eine strukturierte technische Bewertung durchführen |
| [Glossar für Softwareingenieure](glossar-fuer-softwareingenieure.md), [FAQ für Softwareingenieure](faq-fuer-softwareingenieure.md) | Begriffe und schnelle Antworten |

### Architektur

| Seite | Wobei sie Ihnen hilft |
|---|---|
| [Architekturüberblick](architekturueberblick.md) | die Schichten und ihre Verantwortlichkeiten sehen |
| [Der Lebenszyklus einer Anfrage](lebenszyklus-einer-anfrage.md) | einer Anfrage von `index.php` bis zur Antwort folgen |
| [Kernel, lib und Erweiterungen: die Schichtung](kernel-lib-und-erweiterungen.md) | wissen, welcher Code von welchem abhängen darf |
| [Siteaccesses und Konfiguration](siteaccesses-und-konfiguration.md) | die INI-Kaskade und die Siteaccess-Zuordnung verstehen |
| [Module, Views und Zugriffskontrolle](module-views-und-zugriffskontrolle.md) | Routing und das Berechtigungsmodell verstehen |
| [Das Inhaltsmodell für Ingenieure](inhaltsmodell-fuer-ingenieure.md) | Klassen, Objekte, Versionen, Knoten und Datentypen verstehen |
| [Persistenz und die Datenbankschicht](persistenz-und-die-datenbankschicht.md) | `eZDB` und `eZPersistentObject` verstehen |
| [Die Template-Engine und die Design-Kette](template-engine-und-design-kette.md) | kompilierte Templates, Overrides und Designs verstehen |
| [Events, Erweiterungspunkte und Plugins](events-erweiterungspunkte-und-plugins.md) | erweitern, ohne zu forken |
| [Konsole, Cronjobs und Hintergrundarbeit](konsole-cron-und-hintergrundarbeit.md) | Arbeit außerhalb einer Webanfrage ausführen |

### Daten

| Seite | Wobei sie Ihnen hilft |
|---|---|
| [Datenbanken: eine wählen und betreiben](datenbanken-und-die-wahl-einer-datenbank.md) | unter den sechs unterstützten Datenbanken wählen |
| [Schemaänderungen und Migrationen](schemaaenderungen-und-migrationen.md) | eine Schemaänderung ausliefern, die jede Installation anwenden kann |
| [Portables SQL schreiben](portables-sql-schreiben.md) | Abfragen schreiben, die auf jedem Treiber funktionieren |

### Laufzeit, Leistung und Skalierung

| Seite | Wobei sie Ihnen hilft |
|---|---|
| [Caching-Ebenen für Ingenieure](caching-ebenen-fuer-ingenieure.md) | jeden Cache und seine Schlüssel kennen |
| [Cache-Invalidierung und Leeren](cache-invalidierung-und-leeren.md) | nach einer Änderung korrekt invalidieren |
| [Anwendungsserver](anwendungsserver.md) | zwischen Apache oder nginx mit PHP-FPM, Velocity und FrankenPHP wählen |
| [Persistente Worker von Velocity](persistente-worker-von-velocity.md) | den Worker-Pool und sein Rücksetzmodell verstehen |
| [Nebenläufigkeit, Statics und langlebige Prozesse](nebenlaeufigkeit-statics-und-langlebige-prozesse.md) | Code schreiben, der in einem persistenten Worker sicher ist |
| [Skalierung und Clustering](skalierung-und-clustering.md) | mehrere Webknoten und gemeinsamen Speicher planen |
| [Leistungsmessung und Benchmarks](leistungsmessung-und-benchmarks.md) | vor und nach einer Änderung messen |

### Betrieb

| Seite | Wobei sie Ihnen hilft |
|---|---|
| [Beobachtbarkeit: Logs, Debug-Leiste und Audit-Trail](beobachtbarkeit-logs-debug-und-audit.md) | sehen, was das System tut |
| [Eine Änderung bereitstellen](eine-aenderung-bereitstellen.md) | eine PHP-, Einstellungs- oder Template-Änderung in der richtigen Reihenfolge ausliefern |
| [Umgang mit Störungen](umgang-mit-stoerungen.md) | auf einen Ausfall oder eine Regression reagieren |
| [Installationen mit mehreren Websites](installationen-mit-mehreren-websites.md) | mehrere Websites aus einer Installation betreiben |
| [Internationalisierung und Rechts-nach-links](i18n-und-rechts-nach-links.md) | Sprachen, Gebietsschemata und Schreibrichtung behandeln |

### Qualität

| Seite | Wobei sie Ihnen hilft |
|---|---|
| [Teststrategie](teststrategie.md) | wissen, welche Arten von Tests es gibt und wann jede läuft |
| [Tests ohne Datenbank und Testsuiten](tests-ohne-datenbank-und-testsuiten.md) | Tests ohne Datenbank schreiben und ausführen |
| [Statische Analyse und Codestil](statische-analyse-und-codestil.md) | mit PHPStan, der Baseline und phpcs arbeiten |
| [CI-Gates](ci-gates.md) | eine rote Prüfung lesen und reproduzieren |
| [Checkliste für Code-Reviews](checkliste-fuer-code-reviews.md), [Checkliste für Design-Reviews](checkliste-fuer-design-reviews.md) | eine Änderung oder einen Entwurf prüfen |
| [Mit technischen Schulden umgehen](technische-schulden.md) | Schulden finden, festhalten und abbauen |

### Release Engineering und Kompatibilität

| Seite | Wobei sie Ihnen hilft |
|---|---|
| [Release Engineering](release-engineering.md) | ein Release des Kernels oder einer Erweiterung erstellen |
| [Versionsnummern und unveränderliche Tags](versionsnummern-und-unveraenderliche-tags.md) | die nächste Nummer wählen und nie einen Tag verschieben |
| [Changelogs und Upgrade-Hinweise](changelogs-und-bc-hinweise.md) | die Aufzeichnung einer Änderung schreiben |
| [Abhängigkeitsverwaltung mit Composer](abhaengigkeitsverwaltung-mit-composer.md) | Pakete festlegen, neu festlegen und auflösen |
| [Richtlinie zur Rückwärtskompatibilität](richtlinie-zur-rueckwaertskompatibilitaet.md) | wissen, was als Bruch zählt und was ihn begleiten muss |

### Sicherheit

| Seite | Wobei sie Ihnen hilft |
|---|---|
| [Sicherheitstechnik im Überblick](sicherheitstechnik-im-ueberblick.md) | das Sicherheitsmodell, die Standards und den Meldeprozess kennen |
| [HTTP-Sicherheit: Header, CSP, Anfrageregeln und Proxys](http-sicherheit-csp-und-proxys.md) | die HTTP-Schicht sicher konfigurieren |
| [Authentifizierung, Sessions und Zwei-Faktor-Anmeldung](authentifizierung-sessions-und-2fa.md) | Anmeldung und Sessions verstehen |
| [Checkliste für Sicherheitsprüfungen](checkliste-fuer-sicherheitspruefungen.md) | eine Änderung auf Sicherheit prüfen |

### Schnittstellen und Zusammenarbeit

| Seite | Wobei sie Ihnen hilft |
|---|---|
| [API-Design: REST, ezjscore und Headless](api-design-rest-ezjscore-headless.md) | eine API wählen und entwerfen |
| [Git-Arbeitsablauf](git-arbeitsablauf.md) | mit Branches, Commits und Pull-Requests arbeiten |
| [Upstream beitragen](upstream-beitragen.md) | eine Änderung an den Kernel oder eine Erweiterung schicken |
| [Dokumentation als Code](dokumentation-als-code.md) | Hilfeseiten wie Code schreiben und prüfen |

## Seiten anderer Zielgruppen, die Sie nutzen werden

| Seite | Warum Softwareingenieure sie lesen |
|---|---|
| [Erweiterungsentwicklung](../erweiterungsentwicklung/README.md) | der vollständige Kurs vom Benutzer zum Erweiterungsentwickler, mit einer API-Referenz |
| [Anwendungsentwickler: hier beginnen](../ersteller/anwendungsentwickler/anwendungsentwickler-hier-beginnen.md) | Anwendungen auf Exponential bauen, mit Code |
| [PHP-Entwickler: hier beginnen](../php-entwickler/php-entwickler-hier-beginnen.md) | die PHP-Seite im Detail |
| [Web-Framework-Entwickler: hier beginnen](../web-framework-entwickler/web-framework-entwickler-hier-beginnen.md) | Exponential im Vergleich mit anderen Web-Frameworks |
| [Hosting-Unternehmen: hier beginnen](../hosting/hosting-anbieter-hier-beginnen.md) | Server, Panels, Betrieb und Runbooks |
| [Datenbankunterstützung](../datenbankunterstuetzung.md) | die Unterstützungsaussage für jede Datenbank |
| [Mit Git entwickeln](../mit-git-entwickeln.md), [Mit Git aktualisieren](../mit-git-aktualisieren.md) | Arbeit mit Klonen des Kernels und der Erweiterungen |
| [Sicherheitshärtung](../sicherheitshaertung.md) | die an Angreifern orientierte Anleitung zur Härtung |
| [Was Exponential ist](../fuehrung/was-exponential-ist.md) | die Produktfamilie und was jedes Produkt enthält |

## Diese Seiten wiederfinden

- Filtern Sie in der Hilfe nach der Zielgruppe **Softwareingenieure**, oder öffnen Sie **Alle Seiten** und den Ordner
  `softwareingenieure`.
- Jede Seite hat **Auf GitHub bearbeiten**. Ist eine Aussage hier falsch oder veraltet, korrigieren Sie sie dort; die
  Seiten werden wie Code geprüft ([Dokumentation als Code](dokumentation-als-code.md)).

## Verwandte Seiten

- [Exponential für Softwareingenieure](exponential-fuer-softwareingenieure.md)
- [Architekturüberblick](architekturueberblick.md)
- [Lesepfad für Softwareingenieure](lesepfad-fuer-softwareingenieure.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
