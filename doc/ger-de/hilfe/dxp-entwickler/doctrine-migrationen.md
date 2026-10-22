---
title: Doctrine-Migrationen
audience: dxp-developer
category: dxp-development
order: 36
summary: Datenbankänderungen in einem DXP-Projekt mit doctrine/doctrine-migrations-bundle - ausstehende Migrationen bei jeder Aktualisierung ausführen, SQL in der Vorschau ansehen, Migrationen für eigene Tabellen schreiben und warum der Installationsbefehl nicht für Aktualisierungen gedacht ist.
keywords: Doctrine, Migrationen, doctrine:migration:migrate, dry-run, status, diff, Schema prüfen, Datenbankänderungen, Bereitstellung, Aktualisierung, migrations, schema validate, database changes, deploy, update
type: help
---
# Doctrine-Migrationen

Diese Seite richtet sich an Entwickler, die die Datenbank eines DXP-Projekts ändern oder Änderungen bereitstellen, die das
tun. Die DXP verlangt `doctrine/doctrine-migrations-bundle`; die Anleitung des Skeletons führt Migrationen bei jeder
Aktualisierung und Bereitstellung aus.

## Inhalt

1. [Installieren gegenüber Migrieren](#installieren-gegenüber-migrieren)
2. [Die Befehle](#die-befehle)
3. [Ihre eigenen Tabellen](#ihre-eigenen-tabellen)
4. [In einer Bereitstellung](#in-einer-bereitstellung)
5. [Regeln](#regeln)

## Installieren gegenüber Migrieren

| Befehl | Wann |
|---|---|
| `exponential:install exponential-oss` | einmal, auf einer leeren Datenbank |
| `doctrine:migration:migrate --allow-no-migration` | danach bei jeder Aktualisierung und Bereitstellung |

Führen Sie den Installationsbefehl nie gegen eine Datenbank mit Inhalten aus, um sie zu "aktualisieren".

## Die Befehle

Aus der CLI-Referenz der Anleitung (Abschnitt 22.2):

```bash
php bin/console doctrine:migration:status                         # pending and applied
php bin/console doctrine:migration:migrate --dry-run              # preview the SQL
php bin/console doctrine:migration:migrate --allow-no-migration   # apply pending migrations
php bin/console doctrine:migration:diff                           # generate a migration from your entity changes
php bin/console doctrine:migration:execute --up <version>         # one migration up
php bin/console doctrine:migration:execute --down <version>       # roll one back
php bin/console doctrine:schema:validate                          # mapping versus database
```

## Ihre eigenen Tabellen

Speichert Ihr Projekt eigene Daten mit Doctrine-Entities in `src/Entity/`, ändern Sie die Entity, erzeugen Sie mit
`doctrine:migration:diff` eine Migration, lesen Sie das erzeugte SQL und committen Sie die Migration mit dem Code, der
sie braucht. Testen Sie sie auf jeder Datenbank-Engine, die Sie unterstützen: MySQL, PostgreSQL und SQLite unterscheiden
sich bei Typen und darin, was `ALTER TABLE` kann.

Speichern Sie Inhalte im Inhalts-Repository (Inhaltstypen und Felder), nicht in eigenen Tabellen, es sei denn, es sind
wirklich keine Inhalte: Dann erhalten sie Versionen, Übersetzungen, Berechtigungen und APIs des Repositorys umsonst.

## In einer Bereitstellung

```bash
php bin/console doctrine:migration:migrate --allow-no-migration --env=prod
```

Sichern Sie vorher die Datenbank, führen Sie Migrationen aus, bevor der neue Code Verkehr bedient, und leeren Sie danach
den Cache ([Bereitstellen](dxp-bereitstellen.md)).

## Regeln

- Eine Migration, die irgendwo gelaufen ist, wird nie geändert; schreiben Sie eine neue.
- Eine Migration muss auf einer leeren Datenbank und auf Produktionsdaten funktionieren.
- `doctrine:database:drop --force` gibt es; führen Sie es nie auf einem Server aus, den Sie nicht leeren wollten.

## Verwandte Seiten

- [Aktualisieren](dxp-aktualisieren.md)
- [Bereitstellen](dxp-bereitstellen.md)
- [Datenbanken](datenbanken-mysql-postgresql-sqlite.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
