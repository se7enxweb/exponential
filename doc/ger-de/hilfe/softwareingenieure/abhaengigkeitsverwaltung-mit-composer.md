---
title: Abhängigkeitsverwaltung mit Composer
audience: software-engineer, developer
category: software-engineering
order: 630
summary: Wie Exponential seine Abhängigkeiten mit Composer verwaltet - das Hauptpaket und sein Typ, Erweiterungen als se7enxweb-Pakete mit Tilde-Bedingungen, keine committete Lock-Datei, der Branch-Alias für main, die Entwicklungswerkzeuge außerhalb von vendor/, Packagist und sein Webhook, neues Festlegen in der richtigen Reihenfolge, Git-Klone statt Archiven und der PSR-Stand der Codebasis.
keywords: Composer, Abhängigkeiten, composer.json, Packagist, Tilde-Bedingung, branch-alias, composer.lock, composer.dev.json, devtools, neu festlegen, Legacy-Installer, Erweiterungen als Pakete, PSR, Softwareingenieur, dependencies, tilde constraint, repin, legacy installer, extensions as packages
type: help
---
# Abhängigkeitsverwaltung mit Composer

Diese Seite richtet sich an Ingenieure, die Abhängigkeiten einer Exponential-Installation hinzufügen, aktualisieren oder
prüfen, oder die ein Paket pflegen, das das Hauptprojekt verlangt. Der Standard ist
[Standards für PHP, PHP-FIG und Composer](../../standards/php-und-composer.md).

## Inhalt

1. [Das Hauptpaket](#das-hauptpaket)
2. [Erweiterungen sind Pakete](#erweiterungen-sind-pakete)
3. [Bedingungen und die Lock-Datei](#bedingungen-und-die-lock-datei)
4. [In der richtigen Reihenfolge neu festlegen](#in-der-richtigen-reihenfolge-neu-festlegen)
5. [Entwicklungswerkzeuge](#entwicklungswerkzeuge)
6. [Klone statt Archiven](#klone-statt-archiven)
7. [PSR-Stand](#psr-stand)
8. [Checkliste für eine neue Abhängigkeit](#checkliste-für-eine-neue-abhängigkeit)

## Das Hauptpaket

| Feld | Wert |
|---|---|
| `name` | `se7enxweb/exponential` |
| `type` | `ezpublish-legacy` (der Typ, den das Legacy-Installer-Plugin versteht) |
| `license` | `GPL-2.0-or-later` |
| `php` | `^8.0` bis `^8.8` |
| `extra.branch-alias` | `"dev-main": "6.0.x-dev"` |
| `config.allow-plugins` | nur die Legacy-Installer-Plugins |

Exponential zu installieren bedeutet `composer install` im Hauptverzeichnis des Repositorys; das README sagt, dass die
Software ohne es nicht läuft.

## Erweiterungen sind Pakete

Das `require` des Hauptprojekts listet die Erweiterungen, die jede Installation erhält, als Pakete `se7enxweb/*`, jede mit
einer Tilde-Bedingung, zum Beispiel `se7enxweb/exponential-velocity` in `~0.0.4.55` und
`se7enxweb/sevenx_authentication_2fa` in `~1.0.14`. Das Legacy-Installer-Plugin legt sie in `extension/` ab. `suggest`
listet optionale, etwa `se7enxweb/sevenx_valkey_cache` und `se7enxweb/ezfind`, mit den PHP-Erweiterungen, die jede
Datenbank braucht.

## Bedingungen und die Lock-Datei

- Eine Tilde-Bedingung lässt nur die letzte Stelle frei: `~0.0.4.54` nimmt `0.0.4.55` und später an, nie `0.0.5.0`.
- `composer.lock` wird nicht committet; jede Installation löst aus den Bedingungen auf. Der PHPUnit-Job der CI löst bei
  jedem Lauf frisch auf, ein Paket-Release kann also ein Ergebnis ohne Änderung im Repository verändern
  ([CI-Gates](ci-gates.md)).
- Pakete, die `se7enxweb/exponential` mit einer Versionsbedingung verlangen, lösen über den Branch-Alias gegen `main` auf,
  und die CI setzt für Pull-Requests `COMPOSER_ROOT_VERSION=dev-main`.

## In der richtigen Reihenfolge neu festlegen

1. Veröffentlichen Sie die Erweiterung ([Release Engineering](release-engineering.md)).
2. Sehen Sie die neue Version auf Packagist. Ein Repository ohne den Packagist-Webhook bleibt bei einer alten Version, bis
   es von Hand aktualisiert wird.
3. Committen Sie die neue Bedingung des Hauptprojekts.
4. Führen Sie die Gates auf diesem Commit aus und pushen Sie ihn.

Ein Festlegen auf eine Version, die Packagist noch nicht listet, bricht den Installationsschritt jedes CI-Zweigs auf
einmal; das Push-Gate lehnt es deshalb ab.

## Entwicklungswerkzeuge

PHP_CodeSniffer, PHPCompatibility und PHPStan werden aus einer eigenen `composer.dev.json` mit `make devtools` nach
`.devtools/` aufgelöst, für die Plattform PHP 8.0.30, nie nach `vendor/`. `require-dev` des Hauptprojekts enthält nur
PHPUnit und einen Code-Generator ([Statische Analyse und Codestil](statische-analyse-und-codestil.md)).

## Klone statt Archiven

Composer kann den Kernel und die Erweiterungen als Git-Klone statt als Archive installieren, sodass Sie sehen, was sich
geändert hat, eine Regression mit Bisect finden, eine Korrektur vor ihrer Veröffentlichung nutzen und Änderungen
zurückschicken können. Composer weigert sich, einen Klon mit nicht committeten oder nicht gepushten Änderungen zu
aktualisieren, aber ein Ordner, den Sie von Hand geklont haben, wo Composer ein Archiv installiert hatte, wird bei der
nächsten Aktualisierung ohne Rückfrage ersetzt; lassen Sie Composer den Klon mit `composer reinstall <package>` anlegen.
Aktualisieren Sie auf einer Live-Website ein Paket nach dem anderen ([Mit Git entwickeln](../mit-git-entwickeln.md),
[Mit Git aktualisieren](../mit-git-aktualisieren.md)).

## PSR-Stand

Das Projekt sagt es klar: kein Autoloading nach PSR-4 (stattdessen erzeugte Klassenzuordnungen), keine Schnittstellen nach
PSR-3, PSR-6, PSR-7, PSR-11, PSR-14, PSR-15 bis PSR-18 im Kernel und ein eigener Codestil statt PSR-12. Planen Sie
Adapter ein, wenn Ihr System diese Schnittstellen erwartet.

## Checkliste für eine neue Abhängigkeit

- [ ] Wird sie gebraucht, oder erledigt das der Kernel oder eine mitgelieferte Erweiterung bereits?
- [ ] Ihre Lizenz ist mit der GNU GPL, Version 2 oder später, vereinbar.
- [ ] Sie unterstützt PHP 8.0 (die älteste unterstützte Version) und, wo möglich, PHP 7.4 (ohne Support nutzbar), oder sie ist optional und abgesichert, oder sie wird nur für neueres PHP über `se7enxweb/exponential-php-floor` verlangt.
- [ ] Sie wird gepflegt und auf Packagist veröffentlicht.
- [ ] Die Bedingung nennt eine veröffentlichte Version.
- [ ] Ihre Sicherheitshinweise werden verfolgt (`composer audit` empfiehlt die Anleitung zur Härtung).

## Verwandte Seiten

- [Composer und Packagist, für Anwendungsentwickler](../ersteller/anwendungsentwickler/composer-und-packagist.md)
- [composer.json für Erweiterungen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/composer-json-fuer-erweiterungen.md)
- [Composer und Git auf Servern](../hosting/grundlagen/composer-und-git-auf-servern.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
