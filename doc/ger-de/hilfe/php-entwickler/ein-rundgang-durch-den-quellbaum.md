---
title: Ein Rundgang durch den Quellbaum
audience: php-developer, developer
category: php-development
order: 20
summary: Jedes Verzeichnis der obersten Ebene und jeder Einstiegspunkt eines Exponential-Checkouts aus der Sicht eines PHP-Entwicklers - wo der Kernel, die Bibliotheken, die Befehle, die Einstellungen, die Tests und die generierten Dateien liegen und welche davon Sie ändern dürfen.
keywords: Quellbaum, Verzeichnisse, Kernel, Erweiterung, Einstellungen, Einstiegspunkte, Tests, source tree, directories, kernel, lib, extension, settings, var, autoload, bin/php, cronjobs, tests, entry points, index.php, index_rest.php, config.php, config.env.php
type: help
---
# Ein Rundgang durch den Quellbaum

Diese Seite richtet sich an PHP-Entwickler, die zum ersten Mal einen Exponential-Checkout öffnen. Sie geht die
Verzeichnisse und Einstiegspunkte im Wurzelverzeichnis durch, sagt, was wo liegt und welche Orte generiert werden oder
zur Installation statt zum Code gehören.

> **Warnung**
> **`.htaccess` bremst jede Anfrage.** Sind `.htaccess`-Dateien erlaubt (`AllowOverride` alles außer `None`), geschweige
> denn genutzt, sucht Apache sie bei jeder Anfrage in jedem Verzeichnis des Pfads und liest und wertet sie aus, und
> die Ladezeiten der Seiten steigen drastisch ([gemessen](../../anleitungen/setup-dateien-von-der-konsole.md#gemessen-htaccess-gegen-regeln-im-virtuellen-host)). Die einzige wirkliche Abhilfe ist, die
> Funktion für den virtuellen Host abzuschalten: `AllowOverride None` im virtuellen HTTPS-Host, und die Regeln in
> der Konfiguration des virtuellen Hosts selbst als mod_rewrite-Regeln. Das ist in Geschwindigkeitstests im
> Produktivbetrieb weit schneller und die Zeit für die Optimierung einer Produktivkonfiguration unbedingt wert.
> Stellen Sie sofort um: Übernehmen Sie die Regeln des [Beispiels für einen virtuellen HTTPS-Host](../../beispiele/webserver/apache-virtueller-host.md) in
> Ihren virtuellen Host (mit dem statischen Cache auch [`staticcache_offload.conf`](../../../eng-us/examples/staticcache_offload.conf)), setzen Sie
> `AllowOverride None`, führen Sie `apachectl configtest` aus, laden Sie Apache neu und testen Sie die Website.
> Dieselben Regeln für andere Server: [nginx](../../beispiele/webserver/nginx.md), [LiteSpeed](../../beispiele/webserver/litespeed.md), [Caddy](../../beispiele/webserver/caddy.md), [IIS](../../beispiele/webserver/iis.md),
> [Velocity](../../beispiele/webserver/velocity.md) (eingebaut); wohin Regeln für virtuelle Hosts in einem Control Panel gehören:
> [Plesk](../hosting/control-panels/plesk.md), [cPanel](../hosting/control-panels/cpanel.md), [DirectAdmin](../hosting/control-panels/directadmin.md), [ISPConfig](../hosting/control-panels/ispconfig.md).

## Inhalt

1. [Die Einstiegspunkte](#die-einstiegspunkte)
2. [Code-Verzeichnisse](#code-verzeichnisse)
3. [In kernel/](#in-kernel)
4. [In lib/](#in-lib)
5. [Konfiguration und generierte Dateien](#konfiguration-und-generierte-dateien)
6. [Qualität und Tests](#qualität-und-tests)
7. [Was Sie ändern und was Sie in Ruhe lassen](#was-sie-ändern-und-was-sie-in-ruhe-lassen)

## Die Einstiegspunkte

| Datei | Führt aus |
|---|---|
| `index.php` | jede Webseite: Wartungsprüfung, `autoload.php`, dann `new ezpKernel( new ezpKernelWeb() )` und `run()` |
| `index_rest.php` | die REST-API |
| `index_treemenu.php` | das Inhaltsbaum-Menü der Administration |
| `index_cluster.php` | die Auslieferung geclusterter Binärdateien |
| `bin/php/*.php` | Kommandozeilenskripte; ein Dispatcher, `bin/php/console`, führt sie alle als `exp:<name>` aus |
| `runcronjobs.php` | Cronjob-Teile (`php runcronjobs.php frequent`) |
| `index_soap.php`, `index_webdav.php` | die SOAP- und WebDAV-Server (`soap.php` und `webdav.php` leiten nur dorthin weiter, [bis zu](../../bc/6.0/soap-einstiegsskript-index-soap.md) [einer späteren Hauptversion](../../bc/6.0/webdav-einstiegsskript-index-webdav.md)) |

Jeder PHP-Einstiegspunkt beginnt mit `autoload.php`. Es definiert `EXP_ROOT_DIR` (das Wurzelverzeichnis der Installation,
ein absoluter Pfad), lädt unter PHP älter als 8.1 `lib/phpcompat.php`, entscheidet über das Engine-Archiv und registriert
den Klassen-Map-Autoloader ([Wie eine Anfrage in PHP abläuft](wie-eine-anfrage-in-php-ablaeuft.md)).

Ein Beispiel dafür, wie ein Konsolenbefehl aus diesen Teilen gebaut ist: die Befehle, die `.htaccess` und `config.php`
an ihren Platz bringen ([.htaccess und config.php von der Konsole aus einrichten](../../anleitungen/setup-dateien-von-der-konsole.md)).

| Datei | Rolle |
|---|---|
| `bin/php/setupfiles.php`, `bin/php/configphp.php` | die Skripte hinter `exp:setupfiles` (`exp:setup:status`, `exp:setup:htaccess`, `exp:setup:config`) und `exp:configphp` (`exp:config:list`, `get`, `set`, `unset`); sie laufen auch ohne die Konsole |
| `kernel/private/classes/commands/setupfiles.php`, `configphp.php` | die Klassen der Konsolenbefehle |
| `kernel/classes/expsetupfilecopy.php` | das sichere Kopieren: Probelauf, Diff, Sicherung nach `var/backup/`, atomares Schreiben, Modus und Eigentümer |
| `kernel/classes/expconfigphpeditor.php` | liest `config.php` als PHP-Tokens und ändert ein `define()` nach dem anderen, geprüft mit `php -l` |
| `kernel/classes/expconfigphpcatalogue.php` | die Liste der beschriebenen Einstellungen, gelesen aus den Kommentaren von `config.php-RECOMMENDED` |
| `tests/tests/kernel/private/classes/commands/expSetupFilesAndConfigPhpCommandsTest.php` | ihre Tests |

## Code-Verzeichnisse

| Verzeichnis | Enthält | Namespaces und Präfixe |
|---|---|---|
| `kernel/` | das CMS: Module, Inhaltsmodell, Benutzer, Rollen, Workflows, Setup, den Anfrage-Kernel | Klassen `eZ*`, `ezp*`, `exp*`; `Exponential\...` für Runnables |
| `lib/` | allgemeine Bibliotheken, auf denen der Kernel aufbaut | `eZ*`, `ezp*`, `exp*` |
| `extension/` | mitgelieferte Erweiterungen und Ihre eigenen | das eigene Präfix jeder Erweiterung |
| `cronjobs/` | die Cronjob-Teil-Dateien des Kernels (`cronjobs/workflow.php` ...) | schlanke Dateien, die `Exponential\Cronjob\Kernel\...` aufrufen |
| `design/` | die Designs des Kernels: `standard`, `base`, `admin`, `admin3`, `admin4`, `admin4l`, `editor`, `plain` ... | Templates, Stylesheets, Bilder |
| `update/` | Datenbank-Update-Skripte und SQL je Engine (`update/database/sqlite/`, `postgresql/`, `mysql/`) | |
| `share/` | `share/filelist.md5` und andere mitgelieferte Daten | |
| `schemas/`, `templates/`, `support/` | XML-Schemata, mitgelieferte Templates für Pakete, Hilfsdateien | |

## In kernel/

Die meisten Verzeichnisse der obersten Ebene von `kernel/` sind **Module**: `kernel/content/`, `kernel/user/`,
`kernel/setup/`, `kernel/role/`, `kernel/workflow/`, `kernel/shop/` und weitere. Jedes enthält eine `module.php`, seine
Views und oft eine `function_definition.php` für Fetch-Funktionen. Zwei Verzeichnisse sind keine Module:

| Verzeichnis | Enthält |
|---|---|
| `kernel/classes/` | das Inhaltsmodell und die Dienste: `eZContentObject`, `eZContentObjectTreeNode`, `eZPersistentObject`, `eZDataType` und die Datentypen in `kernel/classes/datatypes/`, Workflow-Typen, das Hilfesystem (`kernel/classes/help/`), die Debug-Leiste (`kernel/classes/debugbar/`) |
| `kernel/private/classes/` | den Anfrage-Kernel (`ezpkernel.php`, `ezpkernelweb.php`), `ezpEvent` und die Runnable-Klassen: `commands/`, `cronjobs/`, `views/`, `services/`, `runnable/` |
| `kernel/common/` | gemeinsame Hilfsmittel, zum Beispiel `ezpI18n` und überall genutzte Template-Operatoren |
| `kernel/sql/` | das Datenbankschema des Kernels je Engine |

## In lib/

| Bibliothek | Hauptklassen |
|---|---|
| `lib/ezutils/` | `eZINI`, `eZDebug`, `eZDebugSetting`, `eZSys`, `eZHTTPTool`, `eZExecution`, `eZCLI`, `eZURI`, `eZExtension`, der Bericht der Debug-Leiste |
| `lib/ezdb/` | `eZDB` und die Treiber (`eZSQLite3DB`, `eZPostgreSQLDB`, `eZMySQLiDB`, `expMongoDB`), der SQL-Abfrage-Cache |
| `lib/eztemplate/` | die Template-Engine (`eZTemplate`), ihr Compiler und die eingebauten Operatoren |
| `lib/ezfile/` | Dateien, Verzeichnisse, `eZLog` |
| `lib/ezi18n/`, `lib/ezlocale/` | Übersetzung und Gebietsschema |
| `lib/ezdbschema/` | Schemabehandlung und `.dba`-Dateien |
| `lib/ezsession/` | Sitzungen |
| `lib/phpcompat.php` | Funktionen, die neuer als PHP 8.0 sind, nur definiert, wenn PHP sie nicht hat |
| `lib/version.php` | die Release-Nummer |

## Konfiguration und generierte Dateien

| Pfad | Was es ist | In git |
|---|---|---|
| `settings/*.ini` | die Standardeinstellungen des Kernels | ja |
| `settings/override/`, `settings/siteaccess/` | die eigenen Einstellungen der Installation; können Geheimnisse enthalten | nein (Installation) |
| `config.php` | Konstanten für diese Installation (aus `config.php-RECOMMENDED`; `exp:setup:config` kopiert sie, `exp:config:*` bearbeitet sie) | nein |
| `.htaccess` | die Regeln für Apache und LiteSpeed (aus `.htaccess_root` oder `.htaccess_root_static`; `exp:setup:htaccess` kopiert sie) | nein (Installation) |
| `var/backup/setup/`, `var/backup/config/` | die Kopien, die `exp:setup:*` und `exp:config:*` von jeder Datei aufbewahren, die sie ersetzen oder bearbeiten | nein |
| `config.env.php` | `EXP_ENV`, der Umgebungsname dieser Maschine (aus `config.env.php.example`) | nein |
| `autoload/ezp_kernel.php` | die Klassen-Map des Kernels | ja |
| `var/autoload/ezp_extension.php`, `ezp_override.php`, `ezp_tests.php` | generierte Klassen-Maps | nein |
| `var/cache/`, `var/log/`, `var/<site>/` | Caches, Logs, Speicher | nein |
| `vendor/` | Composer-Pakete | nein |
| `dist/engine.phar` | das optionale Engine-Archiv | nein (`dist/` wird ignoriert) |

## Qualität und Tests

| Pfad | Zweck |
|---|---|
| `phpunit.xml`, `tests/bootstrap.php`, `tests/tests/` | PHPUnit-Konfiguration, Bootstrap und die Tests |
| `tests/test-map.json` | welche Tests welche Quelldateien abdecken |
| `tests/runtests.php` | der ältere Runner für Datenbanktests (`--dsn ... --db-per-test`) |
| `tests/bin/bootsmoke.php` | der Boot-Smoke-Test, den die CI unter PHP 8.0 ausführt |
| `phpcs.xml.dist`, `bin/phpcs/` | der Code-Stil und die eigenen Sniffs des Projekts |
| `phpstan.neon.dist`, `phpstan-baseline.neon` | statische Analyse auf Level 1 mit ihrer Baseline |
| `Makefile`, `.githooks/`, `composer.dev.json` | die make-Ziele, die Git-Hooks und die Entwicklungswerkzeuge |
| `.github/workflows/` | CI: Quality, PHPUnit, Performance, CI health |

## Was Sie ändern und was Sie in Ruhe lassen

- **Ihre Arbeit gehört in fast jedem Fall in eine Erweiterung.** Der Kernel wird nur für Kernel-Korrekturen und
  Funktionen geändert, die Sie zurückgeben ([PHP-Änderungen beitragen](php-aenderungen-beitragen.md)).
- **Bearbeiten Sie niemals generierte Dateien** (`var/autoload/*`, Caches); generieren Sie sie neu.
- **Committen Sie niemals Installationsdateien**: `settings/override/`, `config.php`, `config.env.php`, `var/`. Tragen
  Sie in einem eigenen Projekt-Repository `/config.php` in dessen `.gitignore` ein: Die Datei kann das Passwort der
  Cluster-Datenbank enthalten.
- **Bearbeiten Sie niemals `vendor/`**; von Composer installierte Erweiterungen können in manchen Installationen
  lebendige Git-Checkouts sein, und Composer würde sie ersetzen.

## Verwandte Seiten

- [Exponential für PHP-Entwickler](exponential-fuer-php-entwickler.md)
- [Projektdateien](../projektdateien.md)
- [Verzeichnisaufbau einer Erweiterung](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/verzeichnisaufbau-einer-erweiterung.md)
- [Verzeichnisse unter var/](../erweiterungsentwicklung/teil-8-referenz/verzeichnisse-unter-var.md)
- [.htaccess und config.php von der Konsole aus einrichten](../../anleitungen/setup-dateien-von-der-konsole.md)
- [Freie Dokumentation](../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
