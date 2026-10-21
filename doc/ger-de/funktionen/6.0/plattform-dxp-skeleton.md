# Das DXP-Skeleton von Exponential Platform: create-project und eine dokumentierte Installation

Diese Seite ist für Entwickler, die ein neues Projekt auf Exponential Platform (der Symfony-basierten Plattform) aus einem
sauberen Skeleton beginnen, ohne Demo-Design.

**Repositorys:** `se7enxweb/exponential-platform-dxp-skeleton` (Platform v5), `se7enxweb/exponential-platform-v4x-dxp-skeleton`
(Platform 4.6.x), `se7enxweb/exponential-platform-dxp` (das Metapaket, das beide verlangen), `se7enxweb/oss`, `se7enxweb/oss-skeleton`.

## Was es ist

Ein sauberes Symfony-Projekt für Exponential Platform ohne Demo-Design. `composer create-project` lädt die Pakete herunter, ein Symfony-Flex-Rezept konfiguriert sie, und die eigene Anleitung des Skeletons (`INSTALL.md`, 22 Abschnitte) führt Sie von einem leeren Ordner zu einer laufenden Administrationsoberfläche. Verwenden Sie den [Nexus-Starter](plattform-nexus-starter.md), wenn Sie stattdessen eine fertige Demo-Website möchten.

| Skeleton | Plattform | Symfony | PHP | Node.js |
|---|---|---|---|---|
| `exponential-platform-dxp-skeleton` | v5 | 7.4 LTS | 8.3+ (8.3 oder 8.5 empfohlen) | 20 LTS (die Anleitung sagt, nur 20 ist getestet) |
| `exponential-platform-v4x-dxp-skeleton` | 4.6.x | 5.4 (`extra.symfony.require` ist auf `5.4.*` festgelegt, damit Flex keine unpassende Version wählen kann) | 7.4 bis 8.5 | 20 LTS |

Das Metapaket `se7enxweb/exponential-platform-dxp` wurde am 12.03.2026 angelegt, und am 21.03.2026 wurden seine Kern- und Admin-Pakete auf die Forks von se7enxweb umgestellt (Kern mit Tag `v5.0.1.0`). Im April 2026 ersetzte es außerdem `ibexa/system-info`, `ibexa/admin-ui-assets` und `ibexa/fieldtype-richtext` durch die Pakete von se7enxweb und verlangte den Fork von layouts-core. Siehe die [Paketkarte](../../spezifikationen/6.0/platform-paketzuordnung.md).

## Warum es hilft

- Ein dokumentierter Weg. Jeder Befehl der Anleitung trägt den Namen Exponential (`exponential:install`, `exponential:reindex`); die alten Namen `ibexa:*` bleiben als Aliase, damit alte Skripte weiter funktionieren.
- Der Endpunkt der Flex-Rezepte von se7enxweb steht in der `composer.json` des Skeletons vor den Upstream-Rezepten (Commit „Add sevenx-recipes Flex endpoint before ibexa/recipes“, 12.03.2026), sodass Bundles mit den Rezepten konfiguriert werden, die die Forks kennen.
- Drei Datenbanken sind dokumentiert: MySQL / MariaDB, PostgreSQL und SQLite ganz ohne Server ([SQLite für die Plattform](plattform-sqlite-installation.md)).

## Ein Projekt anlegen

```bash
composer create-project se7enxweb/exponential-platform-dxp-skeleton my-project
cd my-project
```

Composer lädt die Pakete herunter, führt die Rezepte und die Skripte nach der Installation aus (`assets:install`, `cache:clear`).

Konfigurieren Sie die Umgebung (committen Sie `.env.local` nie):

```bash
cp .env .env.local
$EDITOR .env.local
```

Mindestvariablen für MySQL / MariaDB:

| Variable | Beispiel | Bedeutung |
|---|---|---|
| `APP_ENV` | `prod` oder `dev` | Symfony-Umgebung |
| `APP_SECRET` | 32 zufällige Hexadezimalzeichen | Symfony-Geheimnis |
| `DATABASE_DRIVER` | `pdo_mysql` (`pdo_pgsql` für PostgreSQL) | PDO-Treiber |
| `DATABASE_HOST`, `DATABASE_PORT` | `127.0.0.1`, `3306` | Server |
| `DATABASE_NAME`, `DATABASE_USER`, `DATABASE_PASSWORD` | Ihre Werte | Zugangsdaten |
| `DATABASE_CHARSET`, `DATABASE_COLLATION` | `utf8mb4`, `utf8mb4_unicode_520_ci` | Zeichensatz |
| `DATABASE_VERSION` | `mariadb-10.6.0` oder `8.0` | Serverversion, von der Doctrine ausgeht |
| `JWT_SECRET_KEY`, `JWT_PUBLIC_KEY`, `JWT_PASSPHRASE` | Schlüsselpfade unter `config/jwt/`, eine zufällige Passphrase | Anmeldung an der REST-API |

`DATABASE_URL` wird aus den Teilen abgeleitet, oder Sie setzen direkt einen vollständigen DSN.

Datenbank anlegen und installieren:

```bash
mysql -e "CREATE DATABASE exponential CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;"
php bin/console exponential:install exponential-oss
```

Bauen Sie dann die Ressourcen und die Schlüssel:

```bash
source ~/.nvm/nvm.sh && nvm use 20
yarn install
yarn dev                                              # site assets (yarn build for production)
php bin/console assets:install --symlink --relative public
yarn ibexa:build                                      # admin interface assets
php bin/console lexik:jwt:generate-keypair            # REST API keys
php bin/console ibexa:graphql:generate-schema         # GraphQL schema
php bin/console exponential:reindex                   # search index
php bin/console cache:clear
```

Dateiberechtigungen für den Benutzer des Webservers (ersetzen Sie `www-data`):

```bash
setfacl -R  -m u:www-data:rwX -m g:www-data:rwX var/ public/var/
setfacl -dR -m u:www-data:rwX -m g:www-data:rwX var/ public/var/
```

Siteaccesses: `site` liefert das Twig-Frontend unter `/` aus, `admin` die Administrationsoberfläche unter `/adminui/`. REST liegt unter `/api/ezp/v2/`, GraphQL unter `/graphql`.

Die Demodaten legen ein Administratorkonto an, das die Anleitung öffentlich dokumentiert. Ändern Sie sein Passwort sofort in der Administrationsoberfläche.

## Im Alltag

```bash
php bin/console cache:clear --env=prod                # after configuration or code changes
php bin/console cache:warmup --env=prod
php bin/console cache:pool:clear cache.tagaware.filesystem
php bin/console fos:httpcache:invalidate:path / --all # when Varnish or the HTTP cache is in use
php bin/console exponential:reindex --content-type=article
php bin/console doctrine:migration:migrate --dry-run  # preview pending migrations
php bin/console doctrine:migration:migrate --allow-no-migration
```

Cron (alle fünf Minuten):

```bash
*/5 * * * * /usr/bin/php /var/www/exponential/bin/console ibexa:cron:run --env=prod >> /var/log/exponential-cron.log 2>&1
```

Beachten Sie, dass `ibexa:cron:run` und `ibexa:graphql:generate-schema` in diesem Release noch ihre Upstream-Namen tragen; nur umgestellte Befehle haben einen Namen `exponential:` ([Liste](../../spezifikationen/6.0/platform-konsolenbefehle.md)).

## Konvertierung der Datenbank

Die Anleitung (Abschnitt 21) beschreibt den Wechsel zwischen Engines: jede Datenbank nach SQLite (`mysql2sqlite` für MySQL, `pgloader` für PostgreSQL), SQLite nach MySQL oder PostgreSQL, MySQL nach PostgreSQL und umgekehrt sowie einen Weg nur zum Export nach Oracle. Sie endet mit einer Checkliste nach der Konvertierung. Setzen Sie nach der Konvertierung nach SQLite `DATABASE_URL="sqlite:///%kernel.project_dir%/var/data_dev.db"` und `MESSENGER_TRANSPORT_DSN=sync://`.

## Grenzen

- Das Skeleton v5 lehnt PHP älter als 8.3 ab. Das Skeleton 4.6.x behält PHP 7.4 bis 8.5 und Symfony 5.4.
- Node.js: Die Anleitung des Skeletons v5 sagt, nur Node 20 LTS zu verwenden; der Nexus-Starter v5 verlangt Node 22. Folgen Sie der Anleitung des Projekts, das Sie installieren.
- Ein Klon des Skeletons mit `git clone` statt `create-project` braucht `composer install --keep-vcs` und dieselben Schritte in Reihenfolge.

## Verwandte Seiten

- [Exponential 6 selbst installieren](../../INSTALL.md)
- Plattformfunktionen: [Nexus-Starter](plattform-nexus-starter.md), [Legacy Bridge](legacy-bridge.md), [Administrationsoberfläche der Plattform](plattform-admin-ui-fork.md), [Layouts auf der Plattform](plattform-layouts-core-fork.md), [Framework-Forks für PHP 8.5](plattform-php85-framework-forks.md), [Site-Bundles](plattform-site-bundles.md), [SQLite für Exponential Platform](plattform-sqlite-installation.md), [Datenbankverwaltung AdminNeo](adminneo-datenbankverwaltung.md)
- Spezifikationen: [Namen der Konsolenbefehle der Plattform](../../spezifikationen/6.0/platform-konsolenbefehle.md), [Paketkarte der Plattform](../../spezifikationen/6.0/platform-paketzuordnung.md), [SQLite-Installer der Plattform](../../spezifikationen/6.0/platform-sqlite-installer.md), [Bundle der Legacy Bridge](../../spezifikationen/6.0/legacy-bridge-bundle.md)
- Hinweise zur Aktualisierung: [Paket-Forks und Umbenennungen von Befehlen](../../bc/6.0/plattform-paket-forks-und-umbenannte-befehle.md)
- Changelog: [Changelog der Plattform](../../../changelogs/extensions/exponential-platform.md)
- Chronik: [exponential-platform-dxp-skeleton](../../geschichte/oekosystem/exponential-platform-dxp-skeleton.md), [exponential-platform-v4x-dxp-skeleton](../../geschichte/oekosystem/exponential-platform-v4x-dxp-skeleton.md), [exponential-platform-dxp](../../geschichte/oekosystem/exponential-platform-dxp.md), [oss](../../geschichte/oekosystem/oss.md), [Übersicht über das Ökosystem](../../geschichte/oekosystem.md), [Monate des Ökosystems](../../geschichte/oekosystem/monate/2026-04.md), [Änderungsverzeichnis](../../geschichte/aenderungsverzeichnis/README.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
