# Exponential Platform Nexus: eine fertige Website in Minuten

Diese Seite richtet sich an Entwickler und Agenturen, die eine vollständige Demo-Website auf Exponential Platform
wollen, dem auf Symfony basierenden Geschwister von Exponential 6, und sie dann zu ihrer eigenen Website ausbauen
möchten. Sie ist nicht Teil des Legacy-Kernels.

**Repositories:** `se7enxweb/exponential-platform-nexus` (ein Branch pro Plattformgeneration, `1.0.0.x` bis `1.3.0.x`,
dazu die Tags `v2.5.0.x`), `se7enxweb/exponential-platform-nexus-starter` (Platform v5), `se7enxweb/cjw-exponential-platform-nexus`
(CJW-Variante).

## Was es ist

Nexus ist ein fertiges Website-Projekt. Sie führen einmal Composer aus, führen einen Installationsbefehl aus, und Sie haben eine Website mit Demo-Inhalten, dem Seiten-Baukasten Netgen Layouts, einem Twig-Frontend, einer Administrationsoberfläche, einer REST-API und einer GraphQL-API. Sie ändern das Inhaltsmodell und das Design anschließend, statt zuerst Bundles zusammenzustellen.

Es gibt eine Linie pro Plattformgeneration, und Sie wählen nach der Plattform, die Sie betreiben wollen. Gelesen aus
`composer.json` und `composer.lock` jedes Branches und Tags:

| Linie | Repository, Branch und Tags | Läuft auf | Legacy-Kernel | Verwenden, wenn |
|---|---|---|---|---|
| Nexus 1.0.0.x | `exponential-platform-nexus` Branch `1.0.0.x` (auch `master`), Tags `v1.0.0.0.1` bis `1.0.0.10` | eZ Platform 2.5 (`se7enxweb/ezpublish-kernel ~7.5.40`), Symfony 3.4 (`se7enxweb/symfony`), PHP 7.1.3 bis 8.5 | ja: `se7enxweb/exponential ^6.0.12`, `se7enxweb/legacy-bridge ^2.1` | Sie eine Legacy-Website und ihren Legacy-Admin auf dem 2.5-Stack behalten. |
| Nexus 1.1.0.x | Branch `1.1.0.x`, Tags `v1.1.0.0` bis `v1.1.0.7` | eZ Platform 3.3 (`se7enxweb/oss ~3.3.0`), Symfony 5.4, PHP `^8.0` | ja: `se7enxweb/legacy-bridge ^3.0`, `se7enxweb/site-legacy-bundle ^2.0` | Generation 3.3. |
| Nexus 1.2.0.x | Branch `1.2.0.x`, Tag `v1.2.0.0` | Ibexa OSS 4.6 (`se7enxweb/oss ~4.6.0`), Symfony 5.4, PHP 8.2 oder neuer | ja: `se7enxweb/site-legacy-bundle v2.0.0` zieht `se7enxweb/ibexa-legacy-bridge` 4.x und `se7enxweb/exponential` (dev-main im Lock) nach | Generation 4.6. |
| Nexus 1.3.0.x | Branch `1.3.0.x`, Tags `1.3.0.0.0`, `1.3.0.1` bis `1.3.0.5` | Platform v5 (`se7enxweb/exponential-platform-dxp-core v5.0.7` im Lock), Symfony 7.4, PHP 8.4 oder neuer | nein | Sie ein neues Projekt auf v5 beginnen. |
| Nexus v5 starter | `exponential-platform-nexus-starter` | wie 1.3.0.x: Symfony 7.4 LTS, PHP 8.4+ (8.5 empfohlen), Node.js 22; SQLite (Standard in der Entwicklung), MySQL 8.0+, MariaDB 10.3+, PostgreSQL 14+ | nein | Dieselbe v5-Website als separates Repository. |
| Nexus 2.5 | Tags `v2.5.0.0` bis `v2.5.0.6` | die Distribution Exponential Platform Legacy 2.5, verpackt als Nexus | ja | Sie der langfristig unterstützten Linie 2.5 folgen. |
| CJW Nexus | `cjw-exponential-platform-nexus` Tag `1.0.0.6` | wie Nexus 1.0.0.x | ja | Sie die CJW-Startinhalte wollen. |

Zwei Dinge über Versionen, die Sie vor der Installation wissen sollten:

- **Fünfteilige Tags sind nicht auf Packagist.** `v1.0.0.0.1` bis `v1.0.0.0.3` und `1.3.0.0.0` existieren nur als
  Git-Tags; Composer findet `1.0.0.4` bis `1.0.0.10`, `v1.1.0.0` bis `v1.1.0.7`, `v1.2.0.0`, `1.3.0.1` bis `1.3.0.5` und
  `v2.5.0.0` bis `v2.5.0.6`.
- **Nennen Sie immer eine Version.** Das Repository ist ein Fork der Netgen Media Site und trägt auch deren Tags;
  Packagist führt sie unter dem Namen Nexus. `composer create-project se7enxweb/exponential-platform-nexus` ohne Version
  wählt die höchste davon, `3.1.6`, und das ist das Upstream-Paket `netgen/media-site`, kein Nexus-Release. Der
  Standard-Branch, `master`, ist die Linie 2.5.

## Was Sie im v5 starter erhalten

Aus der README des Repositorys:

- das Open-Source-Content-Repository von Ibexa DXP v5 (Inhaltsklassen, Versionen, Übersetzungen, Platzierungen);
- Netgen Layouts 2.0.x als Seiten-Baukasten, mit Netgen Content Browser und der Ibexa Site API;
- Netgen Information Collection für Kontaktformulare, Netgen Tags für die Taxonomie;
- die Administrationsoberfläche von Platform v5 unter `/adminui/`, den Admin von Netgen Layouts unter `/nglayouts/admin`;
- REST API v2 mit JWT-Anmeldung, einen GraphQL-Endpunkt unter `/graphql`;
- einen Build mit Webpack Encore 5 für die Assets der Website, Unterstützung mehrerer Siteaccesses, optional Solr, Varnish und Redis;
- SQLite ohne Konfiguration, sodass ein Laptop oder ein Demo-Server keinen Datenbankserver braucht.

Die drei se7enxweb-Forks, von denen der Starter abhängt, existieren, weil Upstream-Pakete unter PHP 8.4 und Twig 3.24 nicht mehr funktionierten. Sie werden erklärt in [Forks der Framework-Schicht für PHP 8.4 / 8.5](plattform-php85-framework-forks.md), [dem Fork von layouts-core](plattform-layouts-core-fork.md) und [dem Fork der Admin-Oberfläche](plattform-admin-ui-fork.md).

## Den v5 starter installieren

Voraussetzungen: PHP 8.4 oder neuer mit `gd`, `curl`, `json`, `xsl`, `xml`, `intl`, `mbstring`, `ctype`, `iconv` und einem von `pdo_sqlite`, `pdo_mysql`, `pdo_pgsql`; Composer 2; Node.js 22 mit Yarn 1.22 (die `package.json` des Projekts baut nicht unter Node 20 oder älter); ein Webserver (Apache 2.4, Nginx 1.18 oder die Symfony CLI für die Entwicklung).

```bash
git clone --branch 1.3.0.x git@github.com:se7enxweb/exponential-platform-nexus.git   # without --branch: master, the 2.5 line
cd exponential-platform-nexus
COMPOSER_ALLOW_SUPERUSER=1 composer install
cp .env .env.local                      # set APP_SECRET; keep the SQLite DATABASE_URL or change it
php bin/console exponential:install exponential-media --no-interaction
chmod 660 var/data_dev.db
chown $USER:www-data var/data_dev.db    # use your PHP-FPM user instead of www-data
source ~/.nvm/nvm.sh && nvm use 22
corepack enable
yarn install
yarn build:prod
php bin/console assets:install --symlink --relative public
yarn ibexa:build
php bin/console lexik:jwt:generate-keypair
php bin/console ibexa:graphql:generate-schema
php bin/console cache:clear
symfony server:start
```

Hinweise zu den Befehlen:

- Es gibt zwei Installationstypen. `exponential-media` installiert die Nexus-Demo (Layouts, Inhalte, Tags und die Konfiguration von Netgen Layouts); er wird von `ExponentialMediaInstaller` im Paket `se7enxweb/exponential-platform-dxp-core` bereitgestellt und ist eine datenbankübergreifende Alternative zum nur für MySQL verfügbaren Typ `netgen-media`: Er lädt `media_schema.sql` und `media_data.sql` für MySQL / MariaDB, PostgreSQL oder SQLite, je nachdem, was Ihre `DATABASE_URL` verwendet. `exponential-oss` installiert nur die saubere Plattform ohne Demo-Inhalte (der Starter registriert dafür seinen eigenen `App\Installer\ExponentialOssInstaller`). `php bin/console help exponential:install` zeigt die Typen Ihres Checkouts.
- Die README des Starter-Repositorys klont ebenfalls `exponential-platform-nexus`; welches Sie auch klonen, prüfen Sie,
  dass `composer.json` `se7enxweb/exponential-platform-dxp` und PHP `>=8.4` verlangt, bevor Sie `composer install` ausführen.
- Die Branches 1.1.0.x und 1.2.0.x installieren auf dieselbe Weise (`exponential:install exponential-media`) auf ihrer
  eigenen Generation. Ihre Projekte bringen außerdem einen eigenen Befehl `exponential:reindex` mit (`src/.../ExponentialReindexCommand.php`),
  der an `ibexa:reindex` delegiert.
- `exponential:install` ist der Exponential-Name des Upstream-Befehls `ibexa:install`. Auf den Forks 3.x und 4.6
  funktioniert der alte Name weiterhin als veralteter Alias; der v5-Kernel, den der Starter installiert (`se7enxweb/exponential-platform-dxp-core`
  v5.0.7, `#[AsCommand(name: 'exponential:install')]`), registriert nur den neuen Namen. Siehe [Namen der Konsolenbefehle der Plattform](../../spezifikationen/6.0/platform-konsolenbefehle.md).
- Das Administratorkonto, das der Installer anlegt, ist in der README des Repositorys dokumentiert. Ändern Sie sein Passwort sofort nach der ersten Anmeldung; lassen Sie den Standard nie auf einem erreichbaren Server stehen.

Nach der Installation antworten diese Adressen:

| URL | Was |
|---|---|
| `https://127.0.0.1:8000/` | die öffentliche Website (Siteaccess `site`) |
| `https://127.0.0.1:8000/adminui/` | Administrationsoberfläche von Platform v5 |
| `https://127.0.0.1:8000/api/ibexa/v2/` | REST API v2, per JWT authentifiziert (`ibexa.rest.path_prefix`; `/api/ezp/v2` war das Präfix vor 4.0) |
| `https://127.0.0.1:8000/graphql` | GraphQL-Endpunkt |
| `https://127.0.0.1:8000/nglayouts/admin` | Admin von Netgen Layouts |

Die vollständige Schritt-für-Schritt-Anleitung (Webserver-Konfiguration, Berechtigungen, Checkliste für die Produktion, Cron, Solr, Varnish, Fehlerbehebung, Datenbankkonvertierung) ist `doc/sevenx/INSTALL.md` im Starter-Repository.

## Nexus 1.0.0.x installieren (die Linie 2.5)

Die Linie 1.0.0.x wird mit Composer und den SQL-Dumps installiert, die im Projekt mitgeliefert werden:

```bash
cd /var/www
composer create-project se7enxweb/exponential-platform-nexus:1.0.0.10 --ignore-platform-reqs
```

`--ignore-platform-reqs` ist nötig, weil die Paketdefinitionen des alten Stacks noch ältere PHP-Grenzen deklarieren; die README von 1.0.0.x sagt, dass diese Anforderung wegfällt, sobald die Pakete aktualisiert sind.

Dann:

1. Legen Sie eine Datenbank an: `CREATE DATABASE <db_name> CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_general_ci;`
2. Importieren Sie `src/AppBundle/Resources/database/sql/starter_project_database_sql_dump.sql` (alles) oder das Schema `.../sql/schema/schema.sql` sowie die Inhalte `.../sql/data/content.sql`.
3. Tragen Sie die Datenbankeinstellungen in `app/config/parameters.yml` ein.
4. Legen Sie die symbolischen Links an, die die Installationsanleitung aufführt, damit der Legacy-Speicher außerhalb des Verzeichnisses `ezpublish_legacy` liegt, das Composer bei einer Aktualisierung ersetzt:

```bash
cd ezpublish_legacy/extension/
ln -s ../../../src/AppBundle/ezpublish_legacy/extension/app .
ln -s ../../../vendor/se7enxweb/admin-ui-bundle/bundle/ezpublish_legacy/ngadminui .
cd ../var/site/ && mv storage storage-empty && ln -s ../../../src/AppBundle/ezpublish_legacy/var/site/storage .
cd ../../../web/bundles/ && ln -s ../../src/AppBundle/Resources/public app
cd ../../ && php bin/console cache:clear --env=dev
```

5. Überprüfen Sie die Zuordnung von Hosts zu Siteaccesses in `app/config/ezplatform_siteaccess.yml`. Verwenden Sie mindestens zwei Hostnamen: einen für Besucher, einen für Redakteure.

APCu wird dringend empfohlen und ist in der Konfiguration von 1.0.0.x standardmäßig aktiviert (es beschleunigt das Rendern nicht gecachter Seiten erheblich); es kann über die Service-Konfiguration ersetzt werden.

### Der Installationstyp `exponential-cjw` (1.0.0.4 bis 1.0.0.6)

Release `1.0.0.6` von Nexus (21.04.2026) fügt einen Installationstyp `exponential-cjw` (Klasse `ExponentialCjwInstaller`) hinzu, der die gesamten CJW-Startinhalte in SQLite installiert: eine Schemadatei mit 164 Tabellen, die die zusammengesetzten Primärschlüssel beibehält, und eine Datendatei mit 41.568 Insert-Anweisungen. Dasselbe Release ergänzt den fehlenden Parameter `ngsite.default.locations.tree_root.id` und verbindet den SQLite-Parameter `database_path` mit der Doctrine-Verbindung.

```bash
php bin/console ezplatform:install exponential-cjw
```

## Beispiele, die funktionieren

Prüfen Sie, ob die Installation in Ordnung ist:

```bash
php bin/console list exponential          # the exponential:* commands are registered
php bin/console debug:router | head       # routes load
curl -sI https://127.0.0.1:8000/ | head -1
```

Später von SQLite zu MySQL wechseln: Setzen Sie `DATABASE_URL` in `.env.local` auf einen MySQL-DSN, legen Sie die Datenbank an, führen Sie die Installation auf der leeren Datenbank erneut aus, oder verwenden Sie die Rezepte zur Datenbankkonvertierung in der Starter-Anleitung (beliebig nach SQLite, SQLite nach MySQL / PostgreSQL, MySQL nach PostgreSQL, PostgreSQL nach MySQL).

## Grenzen

- Der v5 starter braucht PHP 8.4. Für PHP 8.1 bis 8.3 verwenden Sie die Linien 3.x / 4.6 von Exponential Platform oder den Legacy-Kernel.
- Die Linie Nexus 1.0.0.x baut auf dem Stack von Symfony 3.4 auf; sie braucht die in [Forks für PHP 8.4 / 8.5](plattform-php85-framework-forks.md) aufgeführten Framework-Forks, um unter PHP 8.2 bis 8.5 zu laufen.
- Die öffentlichen Installationsanleitungen in den Repositorys enthalten ein Standard-Administratorkonto für die Demo-Daten. Betrachten Sie es als öffentlich bekannt und ändern Sie es.

## Verwandte Seiten

- [DXP-Projektgerüst](plattform-dxp-skeleton.md) (das schlichte Gerüst ohne das Nexus-Demo-Design), [Legacy Bridge](legacy-bridge.md) (der Legacy-Admin und der Symfony-Stack in einer Installation)
- Funktionen der Plattform: [Administrationsoberfläche der Plattform](plattform-admin-ui-fork.md), [Layouts auf der Plattform](plattform-layouts-core-fork.md), [Framework-Forks für PHP 8.5](plattform-php85-framework-forks.md), [Site-Bundles](plattform-site-bundles.md), [SQLite für Exponential Platform](plattform-sqlite-installation.md), [Datenbankmanager AdminNeo](adminneo-datenbankverwaltung.md)
- Spezifikationen: [Paketübersicht der Plattform](../../spezifikationen/6.0/platform-paketzuordnung.md), [Namen der Konsolenbefehle der Plattform](../../spezifikationen/6.0/platform-konsolenbefehle.md), [SQLite-Installer der Plattform](../../spezifikationen/6.0/platform-sqlite-installer.md), [Legacy-Bridge-Bundle](../../spezifikationen/6.0/legacy-bridge-bundle.md)
- Hinweise zur Aktualisierung: [Paket-Forks und umbenannte Befehle](../../bc/6.0/plattform-paket-forks-und-umbenannte-befehle.md)
- Changelog: [Changelog der Plattform](../../../changelogs/extensions/exponential-platform.md)
- Geschichte: [exponential-platform-nexus-starter](../../geschichte/oekosystem/exponential-platform-nexus-starter.md), [exponential-platform-nexus](../../geschichte/oekosystem/exponential-platform-nexus.md), [cjw-exponential-platform-nexus](../../geschichte/oekosystem/cjw-exponential-platform-nexus.md), [Überblick über das Ökosystem](../../geschichte/oekosystem.md), [Monate des Ökosystems](../../geschichte/oekosystem/monate/2026-04.md), [Änderungsverzeichnis](../../geschichte/aenderungsverzeichnis/README.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
