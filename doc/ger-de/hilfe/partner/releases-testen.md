---
title: Releases testen, bevor Ihre Kunden es tun
audience: partner
category: partnership
order: 670
summary: Eine Routine für Release-Tests bei Partnern - Releases und main verfolgen, eine Staging-Kopie pro Kunde, eine Test-Checkliste, Entwicklungsversionen früh testen, Ergebnisse als Issues zurückmelden und warum frühes Testen Kunden und Projekt schützt.
keywords: Testen, Release, Staging, Upgrade, Regression, Branch main, Änderungsprotokoll, Checkliste, Testplan, Rückmeldung, testing, release, staging, upgrade, regression, main branch, changelog, checklist, test plan, feedback
type: help
---
# Releases testen, bevor Ihre Kunden es tun

Diese Seite ist für Partner, die Kunden-Websites pflegen. Ein Release auf den Umgebungen Ihrer Kunden zu testen, bevor
Sie es ausrollen, ist der beste Weg, Überraschungen zu vermeiden, und es vor der Veröffentlichung zu testen, hilft allen.

## Wissen, was kommt

- Releases sind Tags von <https://github.com/se7enxweb/exponential>; beobachten Sie seine Releases auf GitHub.
- Die Änderungen stehen im Änderungsprotokoll jeder Version (`doc/changelogs/6.0/`) und in der Release-Übersicht
  (`doc/eng-us/release-overview`).
- Die Entwicklung findet auf `main` statt; „Sicherheitskorrekturen werden auf dem Branch `main` gemacht ... und mit dem
  nächsten Release 6.0.x veröffentlicht“ (SECURITY.md).
- „Ein veröffentlichtes Tag wird nie geändert“ (SECURITY.md): Eine Korrektur kommt als nächstes Release.

## Eine Staging-Kopie pro Kunde

- Dieselbe PHP-Version, Datenbank, Engine und dieselben Erweiterungen wie im Produktivbetrieb.
- Inhalte: wenn möglich eine Kopie ohne personenbezogene Daten oder anonymisiert ([Kundendaten schützen](kundendaten-schuetzen.md)).
- Aus Git installiert, damit Sie schnell zwischen Versionen wechseln können ([Mit Git entwickeln](../mit-git-entwickeln.md)).

## Test-Checkliste

- [ ] Upgrade-Schritte des Release ausgeführt (`doc/eng-us/install/11-upgrading.md`)
- [ ] Startseite, eine Inhaltsseite, Suche und Formulare funktionieren
- [ ] Anmeldung an der Administration; einen Entwurf bearbeiten und veröffentlichen; ein Bild hochladen
- [ ] Jede Sprache und jeder Siteaccess
- [ ] Eigene Erweiterungen und Templates: keine Fehler in den Logs
- [ ] Cronjobs laufen
- [ ] Caches geleert; Leistung vergleichbar
- [ ] Die kritischen Abläufe des Kunden (Bestellabschluss, Mitgliedschaft, ...)

## Früh testen: Entwicklungsversionen

Wer `main` vor einem Release testet, findet Probleme, solange sie billig zu beheben sind. Verwenden Sie eine
Staging-Kopie, nie den Produktivbetrieb. Melden Sie, was Sie finden, als Issues ([Fehler gut melden](fehler-gut-melden.md)).

## Rückmelden

- Melden Sie Regressionen mit genauen Versionen.
- Sagen Sie dem Projekt, wenn ein Upgrade für eine Art von Website gut gelaufen ist; solche Berichte helfen anderen
  bei der Planung.

## Verwandte Seiten

- [Kunden-Websites aktuell halten](kunden-websites-aktuell-halten.md)
- [Release- und Upgrade-Planung](../fuehrung/release-und-upgrade-planung.md)
- [Fehler gut melden](fehler-gut-melden.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
