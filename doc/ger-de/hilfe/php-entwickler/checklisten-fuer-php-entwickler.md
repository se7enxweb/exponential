---
title: Checklisten für PHP-Entwickler
audience: php-developer, developer
category: php-development
order: 480
summary: Kopierbare Checklisten für PHP-Arbeit an Exponential - bevor Sie schreiben, für jede PHP-Datei, für eine neue Klasse, eine View, einen Befehl oder Cronjob-Teil, einen Datentyp oder ein Workflow-Ereignis, Sicherheit, Velocity-Kompatibilität, Tests, einen Commit und Pull Request und ein Deployment.
keywords: Checkliste, PHP-Checkliste, Code-Review, Checkliste für Pull Requests, Sicherheits-Checkliste, Velocity-Checkliste, Deployment-Checkliste, neue Klasse, Modul-View, Datentyp, checklist, PHP checklist, code review, pull request checklist, security checklist, Velocity checklist, deploy checklist, new class, module view, datatype, PHP 8.0, phpcs, phpstan, tests, manifest
type: help
---
# Checklisten für PHP-Entwickler

Diese Seite richtet sich an PHP-Entwickler, die ihre Arbeit in einem Durchgang prüfen möchten. Jede Liste verweist auf
die Seite, die sie erklärt. Kopieren Sie eine Liste in die Beschreibung Ihres Pull Requests oder in Ihre Review-Notizen.

## Inhalt

1. [Bevor Sie schreiben](#bevor-sie-schreiben)
2. [Jede PHP-Datei](#jede-php-datei)
3. [Eine neue Klasse](#eine-neue-klasse)
4. [Eine Modul-View](#eine-modul-view)
5. [Ein Befehl oder Cronjob-Teil](#ein-befehl-oder-cronjob-teil)
6. [Ein Datentyp oder Workflow-Ereignistyp](#ein-datentyp-oder-workflow-ereignistyp)
7. [Sicherheit](#sicherheit)
8. [Velocity und lange Läufe](#velocity-und-lange-läufe)
9. [Tests](#tests)
10. [Commit und Pull Request](#commit-und-pull-request)
11. [Deployment](#deployment)

## Bevor Sie schreiben

- [ ] Gibt es eine Einstellung, eine mitgelieferte Erweiterung oder einen Erweiterungspunkt, der es bereits tut? ([Erweiterungspunkte aus PHP](erweiterungspunkte-aus-php.md))
- [ ] Kernel-Änderung oder Erweiterung? ([PHP-Änderungen beitragen](php-aenderungen-beitragen.md#kernel-oder-erweiterung))
- [ ] Das Präfix der neuen Namen ist gewählt ([Klassenbenennung und das Präfix exp](klassenbenennung-und-das-praefix-exp.md))

## Jede PHP-Datei

- [ ] Beginnt mit `<?php`, Unix-Zeilenenden, Leerzeichen, keine Leerzeichen am Zeilenende
- [ ] `php -l` besteht unter dem neuesten PHP und unter PHP 8.0 (`make lint80`)
- [ ] Keine ungeschützte Syntax oder Funktion, die neuer als PHP 8.0 ist ([Code schreiben, der unter PHP 8.0 läuft](code-der-unter-php-8-0-laeuft.md))
- [ ] `array( ... )`, Leerzeichen innerhalb von Klammern, geschweifte Klammern von Funktionen in eigener Zeile ([Der Coding-Standard](der-coding-standard.md))
- [ ] Keine skalaren, Rückgabe- oder Eigenschafts-Typdeklarationen; Docblocks mit `@param` und `@return`
- [ ] `make phpcs` fügt keine Verstöße hinzu; `make phpstan` fügt keine Befunde hinzu

## Eine neue Klasse

- [ ] Eine Klasse pro Datei, Dateiname ist der Klassenname in Kleinbuchstaben
- [ ] Eigenschaften deklariert (keine dynamischen Eigenschaften)
- [ ] `php bin/php/ezpgenerateautoloads.php -e` ausgeführt ([Autoloads und Klassen-Maps](autoloads-und-klassen-maps.md))
- [ ] Keine statische Klasseneigenschaft außerhalb des Klassenrumpfs zugewiesen
- [ ] Eine Einstellung, die die Klasse nennt, geht erst live, nachdem die Klasse bereitgestellt ist

## Eine Modul-View

- [ ] `module.php` nennt die `functions` der View (Richtlinie) ([Schreiben Sie Ihre erste Modul-View](ihre-erste-modul-view-schreiben.md))
- [ ] Als Klasse geschrieben, die `Exponential\Runnable\ModuleView` erweitert, die View-Datei eine Zeile
- [ ] `$Params` gecastet und validiert
- [ ] Datenänderungen nur per POST, über Modulaktionen
- [ ] HTML in einem Template, jeder Wert durch `|wash`
- [ ] POST, Weiterleitung, GET nach einem erfolgreichen Speichern; Weiterleitungsziele aus der Anfrage geprüft

## Ein Befehl oder Cronjob-Teil

- [ ] Eine Klasse, die `Exponential\Runnable\Command` oder `CronjobPart` erweitert ([Einen Konsolenbefehl schreiben](einen-konsolenbefehl-schreiben.md), [Einen Cronjob-Teil schreiben](einen-cronjob-teil-schreiben.md))
- [ ] Tag `@description` in der Einstiegsdatei
- [ ] Exit-Code `0` bei Erfolg, ein anderer bei einem Fehlschlag; still mit `-q`
- [ ] `--dry-run` für alles, was Daten ändert
- [ ] Begrenzte Arbeit pro Lauf, kurze Transaktionen, In-Memory-Caches zwischen Stapeln geleert
- [ ] Die Arbeit in einer Dienstklasse, die View, Befehl und Cronjob-Teil gemeinsam nutzen

## Ein Datentyp oder Workflow-Ereignistyp

- [ ] Methodensignaturen exakt wie in `eZDataType` / `eZWorkflowEventType` ([Einen Datentyp schreiben](einen-datentyp-schreiben.md))
- [ ] Registriert mit `eZDataType::register()` oder `eZWorkflowEventType::registerEventType()` und in der INI-Datei
- [ ] Hin- und Rückweg von `toString()`/`fromString()` getestet
- [ ] `execute()` schnell, gibt einen Status zurück, wirft nie ([Einen Workflow-Ereignistyp schreiben](einen-workflow-ereignistyp-schreiben.md))

## Sicherheit

- [ ] Eingaben über `eZHTTPTool`, `$Params` oder Modulaktionen gelesen; Zahlen gecastet, Auswahlwerte in Allow-Listen ([Eingaben validieren und casten](eingaben-validieren-und-casten.md))
- [ ] SQL: Strings durch `escapeString()`, Zahlen gecastet, oder Bedingungen persistenter Objekte ([SQL mit eZDB, sicher](sql-mit-ezdb.md))
- [ ] Ausgaben für ihren Kontext maskiert; `htmlspecialchars()` mit ausdrücklichen Flags ([Ausgaben aus PHP maskieren](ausgaben-aus-php-maskieren.md))
- [ ] Berechtigungen in Views, Fetch-Funktionen, Serverfunktionen, REST-Aktionen und Befehlen geprüft ([Sicherheit in PHP-Code](sicherheit-in-php-code.md))
- [ ] POSTs tragen das Formular-Token ([CSRF-Schutz und ezformtoken](csrf-und-ezformtoken.md))
- [ ] Keine Geheimnisse in Code, URLs oder Logs
- [ ] Keine Inline-Skripte und kein `style=""` in erzeugtem Markup

## Velocity und lange Läufe

- [ ] Keine statischen Variablen in Funktionen, keine `true`-Flags für einmaliges Registrieren ([Fallen durch statischen Zustand](fallen-durch-statischen-zustand.md))
- [ ] Caches nach Siteaccess, Sprache und Benutzer geschlüsselt, wo sie davon abhängen
- [ ] `eZExecution::cleanExit()` nicht innerhalb von `catch ( Exception )`
- [ ] Pfade aus `EXP_ROOT_DIR` oder `eZSys`, nicht aus `__DIR__` einer Kernel-Datei
- [ ] Zweimal hintereinander auf demselben Worker getestet, als zwei Benutzer

## Tests

- [ ] Ein Test ohne Datenbank für die Logik ([PHPUnit ohne Datenbank](phpunit-ohne-datenbank.md))
- [ ] Datenbanktests mit `@group database` markiert ([Datenbank- und Live-Tests](datenbank-und-live-tests.md))
- [ ] In einem Test gesetzte INI-Werte werden in `tearDown()` wiederhergestellt
- [ ] Funktioniert mit PHPUnit 10.5 bis 13
- [ ] Eintrag in `tests/test-map.json`, wenn der Test die Quelldatei nicht wörtlich nennt

## Commit und Pull Request

- [ ] Betreff beginnt mit `Added: `, `Updated: `, `Fixed: `, `Removed: ` oder `Renamed: `; der Text sagt, warum
- [ ] Das Produkt wird in der Nachricht Exponential geschrieben
- [ ] `share/filelist.md5` aktualisiert (`make manifest-fix`) und committet
- [ ] `make check` besteht
- [ ] CLA hinterlegt; Pull Request klein und fokussiert; gesquasht und auf `main` rebased ([PHP-Änderungen beitragen](php-aenderungen-beitragen.md))

## Deployment

- [ ] Autoloads neu generiert, INI-Cache geleert (`--clear-tag=ini`)
- [ ] PHP-FPM neu geladen, Velocity-Worker neu gestartet (`exp:velocity deploy`)
- [ ] Caches mit gerenderter Ausgabe nach dem Neuladen geleert
- [ ] Caches vorgewärmt ([OPcache und Cache-Vorladen](opcache-und-cache-vorladen.md))
- [ ] Startseite und die geänderten Seiten geprüft; Logs gelesen

## Verwandte Seiten

- [Die Checkliste für das Upgrade](../erweiterungsentwicklung/teil-4-aktualisieren/die-upgrade-checkliste.md)
- [Die Checkliste für das Release](../erweiterungsentwicklung/teil-7-veroeffentlichen/die-release-checkliste.md)
- [FAQ für PHP-Entwickler](faq-fuer-php-entwickler.md)
- [Freie Dokumentation](../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
