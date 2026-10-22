---
title: Installation mit dem Skeleton
audience: dxp-developer
category: dxp-development
order: 8
summary: Schritt für Schritt von einem leeren Ordner zu einem laufenden DXP-Projekt - create-project oder git clone, Umgebung, Datenbank, Installationsbefehl, Berechtigungen, Assets, JWT-Schlüssel, GraphQL-Schema, Neuindexierung und der erste Start - mit einem Sicherungspunkt nach jeder Etappe.
keywords: Installation, installieren, create-project, Skeleton, git clone, exponential:install, exponential-oss, JWT, GraphQL-Schema, Neuindexierung, reindex, symfony server, erster Start, Administratorpasswort, install, skeleton, GraphQL schema, first start, admin password
type: help
---
# Installation mit dem Skeleton

Diese Seite richtet sich an Entwickler, die zum ersten Mal ein DXP-Projekt installieren. Sie folgt der
Installationsanleitung des Skeletons (Abschnitte 3 bis 11) in kürzerer Form und verweist auf die Seiten, die jeden
Schritt erklären. Rechnen Sie auf einem vorbereiteten Rechner mit etwa 30 Minuten
([Versionen, die Sie brauchen](versionen-von-php-symfony-und-node.md)).

## Inhalt

1. [Zwei Wege zum Start](#zwei-wege-zum-start)
2. [Die Schritte](#die-schritte)
3. [Nach der Installation](#nach-der-installation)
4. [Sicherungspunkte](#sicherungspunkte)
5. [Wenn ein Schritt fehlschlägt](#wenn-ein-schritt-fehlschlägt)

## Zwei Wege zum Start

| Weg | Befehl | Verwenden Sie ihn, wenn |
|---|---|---|
| Ein Projekt anlegen (empfohlen) | `composer create-project se7enxweb/exponential-platform-dxp-skeleton my-project` | Sie Ihr eigenes Projekt beginnen |
| Das Skeleton klonen | `git clone git@github.com:se7enxweb/exponential-platform-dxp-skeleton.git`, dann `composer install --keep-vcs` | Sie am Skeleton selbst arbeiten |

`create-project` lädt die Pakete herunter, führt die Rezepte von Flex und die Skripte nach der Installation aus
(`assets:install`, `cache:clear`).

## Die Schritte

1. **Legen Sie das Projekt an.**

   ```bash
   composer create-project se7enxweb/exponential-platform-dxp-skeleton my-project
   cd my-project
   ```

2. **Konfigurieren Sie die Umgebung** in `.env.local`, nie in `.env`, und committen Sie sie nie
   ([Umgebungsvariablen](umgebungsvariablen.md)).

   ```bash
   cp .env .env.local
   $EDITOR .env.local
   ```

3. **Legen Sie die Datenbank an** (MySQL, MariaDB oder PostgreSQL) oder setzen Sie den SQLite-DSN
   ([Datenbanken](datenbanken-mysql-postgresql-sqlite.md), [SQLite für die Entwicklung](sqlite-fuer-die-entwicklung.md)).

4. **Installieren Sie das Schema und die Ausgangsdaten.**

   ```bash
   php bin/console exponential:install exponential-oss
   ```

   Das Argument ist der Installationstyp; `exponential-oss` ist der Standard. `--skip-indexing` überspringt die
   automatische Neuindexierung ([Konsolenbefehle](konsolenbefehle-und-umbenennungen.md)).

5. **Setzen Sie die Berechtigungen** für den Benutzer des Webservers auf `var/` und `public/var/`
   ([Dateiberechtigungen](dateiberechtigungen.md)).

6. **Bauen Sie die Assets der Website.**

   ```bash
   source ~/.nvm/nvm.sh && nvm use 20
   yarn install
   yarn dev
   ```

7. **Bauen Sie die Assets der Administration.**

   ```bash
   php bin/console assets:install --symlink --relative public
   yarn ibexa:build
   ```

8. **Erzeugen Sie das JWT-Schlüsselpaar** für die REST-API ([JWT-Authentifizierung](jwt-authentifizierung.md)).

   ```bash
   php bin/console lexik:jwt:generate-keypair
   ```

9. **Erzeugen Sie das GraphQL-Schema** ([GraphQL](graphql-und-graphiql.md)).

   ```bash
   php bin/console ibexa:graphql:generate-schema
   ```

10. **Leeren Sie den Cache und bauen Sie den Suchindex auf.**

    ```bash
    php bin/console cache:clear
    php bin/console exponential:reindex
    ```

11. **Starten Sie einen Entwicklungsserver.**

    ```bash
    symfony server:start
    ```

## Nach der Installation

| URL | Was es ist |
|---|---|
| `https://127.0.0.1:8000/` | die öffentliche Website (Twig) |
| `https://127.0.0.1:8000/adminui/` | die Administrationsoberfläche |
| `https://127.0.0.1:8000/api/ibexa/v2/` | REST-API v2 (die Anleitung zeigt `/api/ezp/v2/`; prüfen Sie das Präfix wie unter [Die REST-API](die-rest-api.md#wo-sie-liegt)) |
| `https://127.0.0.1:8000/graphql` | GraphQL |

Die Ausgangsdaten legen ein Administratorkonto an, dessen Anmeldedaten in der öffentlichen Installationsanleitung
stehen. **Ändern Sie sein Passwort sofort in der Administrationsoberfläche**, bevor die Website von irgendwo anders
erreichbar ist.

## Sicherungspunkte

Die Anleitung empfiehlt einen Git-Commit nach jeder Etappe, damit Sie zu einem funktionierenden Zustand zurückkehren
können:

```bash
git init && git add -A && git commit -m "Added: project created from the DXP skeleton"
git add composer.lock && git commit -m "Updated: locked vendor dependencies"
```

Prüfen Sie vor `git add -A`, dass `.gitignore` `.env.local`, `config/jwt/*.pem`, `var/` und `vendor/` ausschließt
([Mit Git entwickeln](../mit-git-entwickeln.md)).

## Wenn ein Schritt fehlschlägt

| Symptom | Seite |
|---|---|
| Composer lehnt eine PHP-Version ab | [Versionen](versionen-von-php-symfony-und-node.md#einen-rechner-in-zwei-minuten-prüfen) |
| `yarn ibexa:build` meldet "Module not found" | Führen Sie zuerst `assets:install` aus ([Assets bauen](assets-mit-yarn-und-encore-bauen.md)) |
| `attempt to write a readonly database` | [SQLite für die Entwicklung](sqlite-fuer-die-entwicklung.md) |
| Alles andere | [Fehlersuche](fehlersuche-in-der-dxp.md) |

## Verwandte Seiten

- [DXP-Skeleton: create-project und eine dokumentierte Installation](../../funktionen/6.0/plattform-dxp-skeleton.md)
- [Umgebungsvariablen](umgebungsvariablen.md)
- [Befehle für den Alltag](befehle-fuer-den-alltag.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
