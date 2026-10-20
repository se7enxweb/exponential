---
title: Gesprächspunkte für Entwickler
audience: advocate
category: advocacy
order: 12
summary: Gesprächspunkte für Gespräche mit PHP-Entwicklern über das Inhaltsmodell, den Stack, die Installation mit Git zuerst, Erweiterungen und die RAD-Werkzeuge, die REST-API, Velocity, Tests und den Umgang mit Beiträgen, jeweils mit Quelle und einem Befehl zum Ausprobieren.
keywords: Gesprächspunkte, Entwickler, PHP, Composer, Git, Erweiterungen, RAD-Werkzeuge, REST-API, Templates, Inhaltsmodell, Velocity, CI, Tests, Pull Requests, beitragen, talking points, developers, extensions, RAD tools, REST API, templates, content model, tests, pull requests, contribute
type: help
---
# Gesprächspunkte für Entwickler

Diese Seite ist für Fürsprecher, die mit Entwicklern sprechen. Entwickler vertrauen Code, Befehlen und der
Commit-Historie mehr als Adjektiven. Jeder Punkt nennt den Satz, die Quelle und etwas zum Ausprobieren.

## Inhalt

1. [Was Entwickler wissen wollen](#was-entwickler-wissen-wollen)
2. [Die Punkte](#die-punkte)
3. [Befehle zum Anbieten](#befehle-zum-anbieten)
4. [Fragen, die Entwickler stellen](#fragen-die-entwickler-stellen)
5. [Sagen Sie nicht](#sagen-sie-nicht)

## Was Entwickler wissen wollen

- „Was ist der Stack, und ist er aktuell?“
- „Wie erweitere ich es, ohne den Kernel zu forken?“
- „Wie installiere ich es, und wie bleibe ich auf dem neuesten Stand?“
- „Wird mein Pull Request ignoriert?“

## Die Punkte

| Sagen Sie | Quelle | Probieren Sie |
|---|---|---|
| „Das Inhaltsmodell besteht aus Daten, nicht aus Code: Inhaltsklassen mit Attributen, in der Administration definiert.“ | [Das Inhaltsmodell und das Bearbeiten von Inhalten](../../anleitungen/inhaltsmodell-und-bearbeitung.md) | **Setup > Klassen** |
| „Schlichter Stack: PHP 8, Composer, eine Template-Sprache, INI-Einstellungen, die Datenbank Ihrer Wahl.“ | [Wer ist 7x?, Der Anwendungsstack](../wer-ist-7x.md#der-anwendungs-stack) | `composer show se7enxweb/exponential` |
| „PHP 8.0 bis 8.5 werden unterstützt, und die CI führt die Tests auf 8.1 bis 8.5 aus.“ | [PHP-Versionen](../wer-ist-die-exponential-foundation.md#php-versionen), [CI](../../../eng-us/bc/6.0/ci.md) | Die CI-Seite lesen |
| „Git zuerst: Der empfohlene Weg ist, den Kernel und jede Erweiterung als Git-Klone mit vollständiger Historie zu installieren.“ | [Mit Git entwickeln](../mit-git-entwickeln.md) | `git log` in `extension/<name>` |
| „Erweiterungen fügen Module, Klassen, Templates und Einstellungen hinzu; die RAD-Werkzeuge listen 68 Erweiterungspunkte und schreiben für die meisten davon eine funktionierende Erweiterung.“ | [Erweiterungen](../../anleitungen/extensions.md), [RAD-Werkzeuge](../../../eng-us/features/6.0/rad-tools.md) | **Setup > RAD** |
| „Eine REST-API, Versionen 1 und 2, für Apps und andere Systeme.“ | [REST-API v1 und v2](../../anleitungen/rest-api-v1-und-v2.md) | Eine Anfrage im Terminal |
| „Velocity hält die Anwendung zwischen Anfragen geladen; ein Befehl rollt eine PHP-Änderung aus.“ | [Velocity](../../../eng-us/features/6.0/velocity-persistent-worker-server.md) | `./console exp:velocity status` |
| „Jeder Pull Request wird übernommen, und seine Commits bleiben so erhalten, wie der Beitragende sie geschrieben hat.“ | [Wie das Projekt wächst](../wer-ist-die-exponential-foundation.md#wie-das-projekt-wächst) | Übernommene Pull Requests auf GitHub ansehen |
| „Commit-Nachrichten beginnen mit dem, was sie tun: Added, Updated, Fixed, Removed, Renamed; ein Hook prüft das.“ | [CONTRIBUTING.md](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md), [Qualitätsprüfungen](../../../eng-us/features/6.0/quality-checks.md) | `make hooks` in einem Klon |
| „Die Dokumentation liegt im Repository, in `doc/`, sodass eine Codeänderung und ihre Dokumentation in einen Pull Request gehören.“ | [Das Hilfesystem](../../../eng-us/features/6.0/help-system.md) | `ls doc/` |
| „Veröffentlichte Versionen ändern sich nie; eine Korrektur kommt als nächstes Release.“ | [Releases und Versionsnummern](../wer-ist-die-exponential-foundation.md#releases-und-versionsnummern) | `git tag -l 'v6.0.*' --sort=version:refname` |
| „Wenn Sie Symfony bevorzugen, hat die Familie Exponential Platform DXP sowie Legacy oder Nexus mit beiden Kerneln.“ | [Die Produktfamilie kennen](die-produktfamilie-kennen.md) | Die Repositorys unter se7enxweb |

## Befehle zum Anbieten

```bash
# Get the code with its full history
git clone https://github.com/se7enxweb/exponential.git
cd exponential
git log --oneline -10

# A complete site on SQLite in one command (in an installed copy)
./console exp:install --dry-run
./console exp:install

# The help from the command line
./console exp:help search "extension"
```

Die Installationsbefehle sind in [Exponential mit einem Befehl installieren](../../../eng-us/features/6.0/install-in-one-command.md)
beschrieben, die Hilfebefehle in [Das Hilfesystem, Auf der Kommandozeile](../../../eng-us/features/6.0/help-system.md#on-the-command-line).

## Fragen, die Entwickler stellen

| Frage | Antwort | Quelle |
|---|---|---|
| „Muss ich eine besondere Template-Sprache lernen?“ | Ja, Exponential hat seine eigene Template-Sprache mit Operatoren und Fetch-Funktionen | [Templates und Design](../../anleitungen/templates-und-design.md) |
| „Kann ich ein Template aus einer Erweiterung überschreiben?“ | Ja, über Design-Overrides | [Templates und Design](../../anleitungen/templates-und-design.md) |
| „Wo fange ich an, eine Erweiterung zu bauen?“ | Abschnitt 6 der Anleitung zu Erweiterungen, von Hand oder mit den RAD-Werkzeugen | [Erweiterungen, Ihre eigene Erweiterung bauen](../../anleitungen/extensions.md#6-eine-eigene-extension-bauen) |
| „Wie sende ich eine Korrektur?“ | Forken, Branch anlegen, committen, Pull Request | [Mit Git entwickeln, Eine Änderung beitragen](../mit-git-entwickeln.md#eine-änderung-per-pull-request-beitragen) |
| „Gibt es einen Programmierstil?“ | PSR-Standards und der Programmierstil des Projekts, verlinkt aus CONTRIBUTING.md | [CONTRIBUTING.md](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md) |
| „Kann ich aus alten Büchern lernen?“ | Ja, Schulungsbücher werden gespiegelt, und „Learning eZ Publish“ steht unter der GFDL | [Orte der Community](../wer-ist-die-exponential-foundation.md#orte-der-community) |

## Sagen Sie nicht

- „Es ist ein modernes Framework wie X.“ Beschreiben Sie, was es ist; lassen Sie sie urteilen ([Andere Projekte respektieren](andere-projekte-respektieren.md)).
- „Es gibt Tausende Entwickler.“ Keine Quelle zählt sie ([Aussagen, die Sie nicht machen dürfen](aussagen-die-sie-nicht-machen-duerfen.md)).
- „Velocity macht es N-mal schneller.“ Nennen Sie keine Geschwindigkeitszahlen, es sei denn, Sie haben selbst gemessen
  und sagen, wie ([Gesprächspunkte: Geschwindigkeit und Velocity](gespraechspunkte-geschwindigkeit-und-velocity.md)).

## Verwandte Seiten

- [Mit Git entwickeln](../mit-git-entwickeln.md)
- [Einen ersten Beitrag begleiten](einen-ersten-beitrag-begleiten.md)
- [Ein Tutorial schreiben, das funktioniert](ein-tutorial-schreiben.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
