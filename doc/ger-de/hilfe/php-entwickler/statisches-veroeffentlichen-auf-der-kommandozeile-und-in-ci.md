---
title: "Statisches Veröffentlichen auf der Kommandozeile und in CI"
audience: php-developer, developer
category: php-development
order: 511
summary: Für Softwareingenieure und PHP-Entwickler - eine statische Kopie mit den Befehlen exp:static:* bauen, prüfen und veröffentlichen, ihre JSON-Ausgabe lesen und sie in GitHub Actions aus der Starter-Vorlage laufen lassen.
keywords: PHP-Entwickler, Softwareingenieur, Kommandozeile, Konsole, CI, Workflow, JSON, Exit-Codes, Starter-Vorlage, Statisches Veröffentlichen, php developer, command line, console, CI, GitHub Actions, exp:static:build, exp:static:publish, exp:static:diff, static site, GitHub Pages, gh-pages, free hosting, free website, Jekyll alternative, Netlify, CNAME, custom domain, deploy, static publishing, starter template
type: help
---
# Statisches Veröffentlichen auf der Kommandozeile und in CI

Alles, was die Administration unter Setup > Statisches Veröffentlichen tut, ist ein Konsolenbefehl, und jeder Befehl
gibt auf Wunsch JSON aus. Das macht statisches Veröffentlichen leicht zu skripten und in CI auszuführen.

## Was Sie am Ende haben

- Einen Build, eine Linkprüfung und eine Veröffentlichung, die Sie aus einer Shell oder einer Pipeline starten können.
- Einen CI-Workflow, der Exponential installiert, die statische Kopie baut und sie auf GitHub Pages ausliefert.

## Die Befehle

```bash
php bin/php/expstatictargets.php                       # Ziele und ihre Prüfungen
php bin/php/expstaticbuild.php demo --dry-run          # was sich ändern würde
php bin/php/expstaticbuild.php demo --report=var/tmp/report.json
php bin/php/expstaticdiff.php demo --check-links --mount=/ --mount=/my-site/
php bin/php/expstaticserve.php demo                    # Vorschau auf 127.0.0.1:8090
php bin/php/expstaticpublish.php demo --message="Frühjahrsupdate"
php bin/php/expstaticrollback.php demo 3 --apply
```

Dieselben Befehle laufen über die Konsole als `exp:static:build` und so weiter. `--format=json` gibt ein Dokument mit
dem Schema `exp-static/1` aus. Exit-Codes: 0 erledigt, 1 fehlgeschlagen, 2 Aufruf, 3 Datenbank nicht erreichbar,
4 interner Fehler. Referenz: die [Funktionsseite](../../funktionen/6.0/statisches-veroeffentlichen.md#befehle) und die
generierte Seite [exp:static:build](../../beispiele/befehle/expstaticbuild.md).

## Teil-Builds

`--nodes=12,34` oder `--paths=/a,/b` bauen nur diese Seiten neu und behalten den Rest; das nutzt auch das
Veröffentlichen beim Veröffentlichen von Inhalten.

## In CI

Die Vorlage [exponential-static-site-starter](https://github.com/se7enxweb/exponential-static-site-starter) hat einen
GitHub-Actions-Workflow, der Exponential mit Ihrem Code installiert, einen Datenbank-Dump oder ein Site-Paket lädt,
die Kopie baut und sie mit der eigenen Action von GitHub Pages ausliefert. Klicken Sie **Use this template** und folgen
Sie ihrer README.

In Ihrer eigenen Pipeline:

- setzen Sie `CrawlURL` auf die Adresse, auf der der CI-Webserver lauscht; Links zur Live-Site nutzen dann `LiveURL`;
- übergeben Sie den Token als Umgebungsvariable und verwenden Sie `Credential=token:env:<VARIABLE>`;
- lassen Sie den Job bei einem Exit-Code ungleich null von `exp:static:diff --check-links` scheitern.

## Gut zu wissen

- Builds crawlen über HTTP als anonymer Benutzer, der Webserver muss während des Builds also laufen.
- Der Zielordner ist ein eigenes Git-Repository; legen Sie ihn nicht in Ihr Code-Repository.
- Pushes werden nie erzwungen und Tags nie verschoben; eine Rücknahme ist ein neuer Commit.

Mehr: [Links und Basispfad](../statisches-veroeffentlichen/links-und-basispfad.md),
[Wenn eine Veröffentlichung fehlschlägt](../statisches-veroeffentlichen/wenn-eine-veroeffentlichung-fehlschlaegt.md).

## Jetzt Ihre eigene erstellen

Beginnen Sie mit [In 15 Minuten eine kostenlose Website](../statisches-veroeffentlichen/in-15-minuten-eine-kostenlose-website.md)
und bringen Sie es dann mit dem Starter in CI.
