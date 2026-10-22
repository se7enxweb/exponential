---
title: Versionen von PHP, Symfony und Node.js
audience: dxp-developer
category: dxp-development
order: 7
summary: Die Versionen, die ein DXP-Projekt braucht - PHP 8.3 oder neuer mit seinen Erweiterungen und ini-Einstellungen, Symfony 7.4, Composer 2, Node.js 20 und Yarn 1.22, die Datenbanken und die optionalen Dienste - mit den Befehlen, mit denen Sie einen Rechner vor der Installation prüfen.
keywords: Anforderungen, PHP 8.3, PHP 8.5, Symfony 7.4, Node.js 20, Yarn 1.22, Composer 2, Erweiterungen, memory_limit, MySQL, MariaDB, PostgreSQL, SQLite, Redis, Solr, Varnish, requirements, extensions
type: help
---
# Versionen von PHP, Symfony und Node.js

Diese Seite richtet sich an Entwickler und Systemadministratoren, die einen Rechner für ein DXP-Projekt vorbereiten.
Alle Zahlen stammen aus der Installationsanleitung des Skeletons (Abschnitt 1, "Requirements") und den beiden
`composer.json`-Dateien.

## Inhalt

1. [Die Tabelle](#die-tabelle)
2. [PHP-Erweiterungen und -Einstellungen](#php-erweiterungen-und--einstellungen)
3. [Node.js und Yarn](#nodejs-und-yarn)
4. [Einen Rechner in zwei Minuten prüfen](#einen-rechner-in-zwei-minuten-prüfen)
5. [Andere Linien der Familie](#andere-linien-der-familie)

## Die Tabelle

| Komponente | Mindestens | Empfohlen |
|---|---|---|
| PHP | 8.3 | 8.3 oder 8.5 |
| Symfony | 7.4 (LTS) | 7.4 |
| Composer | 2.x | neueste 2.x |
| Node.js | 20 LTS | 20 LTS über nvm |
| Yarn | 1.x | 1.22 über corepack |
| MySQL | 8.0 (utf8mb4) | 8.0 oder neuer |
| MariaDB | 10.3 | 10.6 oder neuer |
| PostgreSQL | 14 | 16 oder neuer |
| SQLite | 3.35 | 3.39 oder neuer, Entwicklung und Tests |
| Redis (optional) | 6.0 | 7.x |
| Solr (optional) | 8.x | 8.11.x |
| Varnish (optional) | 6.0 | 7.1 oder neuer |
| Apache | 2.4 | 2.4, Event-MPM mit PHP-FPM |
| Nginx | 1.18 | 1.24 oder neuer |

## PHP-Erweiterungen und -Einstellungen

Die `composer.json` des Skeletons verlangt `ctype`, `iconv`, `gd`, `curl`, `json`, `xsl`, `xml`, `intl` und
`mbstring`. Die Anleitung ergänzt `pdo_mysql`, `pdo_pgsql` oder `pdo_sqlite` für Ihre Datenbank, `imagick` als
Alternative zu `gd` und `opcache`.

| Einstellung | Wert aus der Anleitung |
|---|---|
| `memory_limit` | 256M oder mehr, 512M empfohlen |
| `date.timezone` | muss gesetzt sein |
| `max_execution_time` | 120 oder mehr, 300 empfohlen für Arbeit auf der Kommandozeile |

## Node.js und Yarn

Node.js und Yarn bauen die Assets der Website und die Administrationsoberfläche. Die Anleitung ist streng: Verwenden Sie
nur **Node.js 20 LTS** ("Do not use Node.js 18 or 22 — only 20 LTS is tested and supported for this release") und
Yarn 1.22, aktiviert mit `corepack enable`. Der Nexus-v5-Starter verwendet Node.js 22; folgen Sie der Anleitung des
Projekts, das Sie installieren ([DXP-Skeleton, Grenzen](../../funktionen/6.0/plattform-dxp-skeleton.md#grenzen)).

```bash
source ~/.nvm/nvm.sh
nvm install 20
nvm use 20
corepack enable
```

Produktionsserver brauchen kein Node.js, wenn Sie die Assets anderswo bauen und das Ergebnis bereitstellen
([Assets bauen](assets-mit-yarn-und-encore-bauen.md)).

## Einen Rechner in zwei Minuten prüfen

```bash
php -v                                   # 8.3 or newer
php -m | grep -iE "ctype|iconv|gd|imagick|curl|json|xsl|xml|intl|mbstring|pdo|opcache|sqlite"
php -i | grep -E "memory_limit|date.timezone|max_execution_time"
composer --version                       # Composer version 2.x
node -v                                  # v20.x
yarn -v                                  # 1.22.x
```

Bei einem vorhandenen Projekt kann Composer Ihnen sagen, was eine Version blockiert:

```bash
composer check-platform-reqs
composer why-not php 8.2
```

## Andere Linien der Familie

| Linie | PHP | Symfony | Node.js |
|---|---|---|---|
| DXP v5 (dieser Bereich) | 8.3 oder neuer | 7.4 | 20 |
| [Skeleton 4.6.x](das-skeleton-v4x.md) | 7.4 bis 8.5 | 5.4 | 20 |
| Nexus 1.3 und der v5-Starter | 8.4 oder neuer | 7.4 | 22 |

Quelle: [Forks der Plattformpakete, Schritt 3](../../bc/6.0/plattform-paket-forks-und-umbenannte-befehle.md#schritt-3-versionen-von-php-und-node-prüfen).

## Verwandte Seiten

- [Installation mit dem Skeleton](installation-mit-dem-skeleton.md)
- [Datenbanken](datenbanken-mysql-postgresql-sqlite.md)
- [Webserver einrichten](webserver-einrichten.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
