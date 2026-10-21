---
title: Bekannte Schwachstellen mit composer audit auflisten
audience: government, admin, developer, hosting
category: public-sector
order: 400
summary: Schritt für Schritt - die für Ihre Exponential-Installation festgeschriebenen Composer-Abhängigkeiten mit composer audit gegen bekannte Sicherheitshinweise prüfen und mit composer outdated neuere Releases sehen, weil die CI des Projekts keine Abhängigkeitsprüfung ausführt, und das Ergebnis ablegen.
keywords: composer audit, composer outdated, Schwachstellen, Abhängigkeiten, Sicherheitshinweise, composer.lock, Patchen, Lieferkette, OWASP A06, Schwachstellenmanagement, öffentlicher Sektor, vulnerabilities, dependencies, advisories, patching, supply chain, vulnerability management, public sector
type: help
---
# Bekannte Schwachstellen mit composer audit auflisten

Für Administratoren, Entwickler und Hosting-Teams. Die Workflows des Projekts führen keine Prüfung der Abhängigkeiten
auf Schwachstellen aus ([Sicherheitsstandards: bekannte Lücken](../../../../../eng-us/standards/security.md#known-gaps)); die
Anleitung zur Härtung empfiehlt stattdessen `composer audit`
([Sicherheitshärtung: veraltete Abhängigkeiten](../../../sicherheitshaertung.md#veraltete-abhängigkeiten-und-die-lieferkette)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Auf Befunde reagieren](#auf-befunde-reagieren)
4. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Führen Sie es auf einer **Kopie** der Installation mit derselben `composer.lock` wie in der Produktion aus (ein
  Staging-Server oder Ihr Checkout des Repositorys), nicht in der Produktion.
- Diese Befehle lesen nur; führen Sie im Rahmen dieser Aufgabe kein `composer update` in der Produktion aus.

## Schritte

Aus dem Wurzelverzeichnis der Installation der Kopie:

```bash
composer audit                 # known vulnerabilities in the locked dependencies
composer outdated --direct     # newer releases of the packages you require
```

Speichern Sie beide Ausgaben mit dem Datum, zum Beispiel `var/tmp/composer-audit-<YYYY-MM-DD>.txt` auf der Kopie, und
legen Sie sie ab.

## Auf Befunde reagieren

| Befund | Tun |
|---|---|
| ein Sicherheitshinweis für Exponential oder eine seiner Erweiterungen | auf das korrigierte Release in der Kopie aktualisieren, testen, ausrollen („Patch-Rhythmus“) |
| ein Sicherheitshinweis für eine Bibliothek | prüfen, ob ein neueres Release von Exponential eine korrigierte Version festschreibt; sonst die Bibliothek in der Kopie aktualisieren und testen |
| keine Korrektur veröffentlicht | das Risiko bewerten; abmildern (die Funktion abschalten, eine Regel des Request Shield); das Projekt vertraulich fragen, wenn es Exponential betrifft |
| nur neuere Releases, kein Sicherheitshinweis | im normalen Update-Zyklus einplanen |

Nach jedem Update: Prüfung der Dateikonsistenz, Smoke-Test, Verifikation des Audits.

## Verwandte Seiten

- [Patch-Rhythmus](../../../../../eng-us/help/government/security/patching-cadence.md)
- [Die Prüfung der Dateikonsistenz ausführen](die-pruefung-der-dateikonsistenz-ausfuehren.md)
- [Aktualisieren](../../../../anleitungen/aktualisieren.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
