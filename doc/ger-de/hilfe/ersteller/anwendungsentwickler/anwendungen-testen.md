---
title: Anwendungen testen
audience: app-creator, developer
category: app-creation
order: 490
summary: Eine Teststrategie für Anwendungen auf Exponential - Unit-Tests ohne Datenbank, Tests gegen eine Wegwerf-Datenbank, Prüfungen von Templates und Links, Browserprüfungen mit einem Testbenutzer, Barrierefreiheit und was vor jedem Push laufen sollte.
keywords: Testen, Unit-Tests, Integrationstests, Datenbanktests, Template-Prüfung, Browsertests, Barrierefreiheit, Testbenutzer, Qualitätsschranken, Anwendungsentwickler, testing, PHPUnit, unit tests, integration tests, database tests, template check, browser tests, accessibility, CSP, test user, quality gates, app creator
type: help
---
# Anwendungen testen

Diese Seite richtet sich an Anwendungsentwickler, die Code ohne Angst ändern möchten. Die eigenen Tests und
Qualitätsschranken des Kernels sind das Vorbild: Die meisten Tests laufen ohne Datenbank, einige gegen eine
Wegwerf-Datenbank, und Prüfungen von Templates, Links und im Browser vervollständigen das Bild
([Standard für Tests und CI](../../../../eng-us/standards/testing-and-ci.md)).

## Inhalt

1. [Die Ebenen](#die-ebenen)
2. [Unit-Tests ohne Datenbank](#unit-tests-ohne-datenbank)
3. [Tests mit Datenbank](#tests-mit-datenbank)
4. [Prüfungen von Templates, Einstellungen und Links](#prüfungen-von-templates-einstellungen-und-links)
5. [Browserprüfungen](#browserprüfungen)
6. [Vor jedem Push](#vor-jedem-push)

## Die Ebenen

| Ebene | Werkzeug | Findet |
|---|---|---|
| Syntax | `php -l`, `eztemplatecheck.php` | Parse-Fehler in PHP und Templates |
| Unit | PHPUnit, ohne Datenbank | Logik von Diensten, Validatoren, Handlern |
| Integration | PHPUnit mit `tests/runtests.php --dsn ...` | SQL, persistente Objekte, Inhaltsoperationen |
| Gerenderte Seiten | HTTP-Anfragen, Browser-Automatisierung | Templates, Rechte, Weiterleitungen |
| Barrierefreiheit und CSP | ein automatischer Barrierefreiheitsprüfer, das CSP-Berichtslog | Barrieren, Inline-Code |

## Unit-Tests ohne Datenbank

Schreiben Sie die Logik als einfache Klassen (Dienste), die Ansicht, Befehl und Cronjob aufrufen; testen Sie diese mit
PHPUnit ([PHPUnit-Tests ohne Datenbank](phpunit-tests-ohne-datenbank.md)). Der Kernel führt seine gesamte Standard-Suite ohne
Datenbank aus: `vendor/bin/phpunit` schließt die Gruppe `database` aus.

## Tests mit Datenbank

`tests/runtests.php --dsn <dsn> --db-per-test` führt die Suiten gegen eine Wegwerf-Datenbank aus, wie es der
Datenbank-Job der CI tut. Kennzeichnen Sie solche Tests mit `@group database`. Richten Sie Tests niemals auf eine
Datenbank, die echte Inhalte enthält.

## Prüfungen von Templates, Einstellungen und Links

```bash
php bin/php/eztemplatecheck.php -s<siteaccess> extension/expdemo/design/   # every template of your extension
./console exp:ini where expdemo.ini/ListSettings/DefaultLimit <siteaccess>  # settings resolve as expected
./console exp:help check                                                    # links in your extension's docs, when indexed
```

## Browserprüfungen

- Verwenden Sie einen eigenen Testbenutzer, der für den Lauf angelegt und danach entfernt wird; niemals ein gemeinsam
  genutztes Administratorkonto, dessen Einstellungen jemandem gehören.
- Prüfen Sie jeden Seitentyp Ihrer Erweiterung abgemeldet, als eingeschränkter Benutzer und als Redakteur.
- Prüfen Sie die Browserkonsole auf Fehler und das CSP-Berichtslog (`var/log/csp.log`) auf blockierten Code
  ([Content Security Policy für Anwendungen](content-security-policy.md)).
- Lassen Sie auf jeder Seite einen Barrierefreiheitsprüfer laufen ([Barrierefreiheit testen](../../../../eng-us/help/creators/website-creators/testing-accessibility.md)).

## Vor jedem Push

- [ ] `php -l` für geänderte PHP-Dateien; Template-Prüfung für geänderte Templates.
- [ ] Die Unit-Tests des geänderten Codes bestehen.
- [ ] Keine Inline-Skripte oder -Styles hinzugefügt.
- [ ] Autoloads neu generiert, falls sich Klassen geändert haben; Caches geleert; die Funktion von Hand ausprobiert.
- [ ] CI für das Repository eingerichtet ([CI für Erweiterungen](ci-fuer-erweiterungen.md)).

## Verwandte Seiten

- [PHPUnit-Tests ohne Datenbank](phpunit-tests-ohne-datenbank.md)
- [Qualitätsprüfungen](../../../../eng-us/features/6.0/quality-checks.md)
- [Qualitätsschranken und Abnahme](../../fuehrung/qualitaetsschranken-und-abnahme.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
