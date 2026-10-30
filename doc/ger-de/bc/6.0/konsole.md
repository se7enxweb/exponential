# Die Exponential-Konsole: `bin/php/console`

`bin/php/console` ist ein einziger Einstiegspunkt für jedes Kommandozeilenskript von Exponential 6.0.15, nach dem
Vorbild von `bin/console` aus Symfony. Lesen Sie diese Seite, wenn Sie Skripte von Hand oder aus Deploy- und
Cron-Jobs ausführen: Sie müssen sich keine Pfade unter `bin/php/`, `bin/shell/` oder innerhalb von Erweiterungen mehr
merken.

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
> [Plesk](../../hilfe/hosting/control-panels/plesk.md), [cPanel](../../hilfe/hosting/control-panels/cpanel.md), [DirectAdmin](../../hilfe/hosting/control-panels/directadmin.md), [ISPConfig](../../hilfe/hosting/control-panels/ispconfig.md).
> **Unter LiteSpeed oder OpenLiteSpeed** kann zusätzlich der Seiten-Cache des Servers anonyme Besucher und Crawler ohne PHP bedienen: [Seiten-Caching mit LiteSpeed und OpenLiteSpeed](../../anleitungen/seiten-cache-mit-litespeed.md).

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Neuer Dispatcher `bin/php/console`. Er findet jedes Skript zur Laufzeit (ohne Konfiguration) und führt es als `<namespace>:<name>` aus. Seit Juni 2026 führt er auch Cronjob-Teile aus (`cron:<part>`) und zeigt die Crontab (`crontab:list`, `crontab:edit`). |
| Wer betroffen ist | Niemand muss etwas ändern. Jedes Skript behält seinen Pfad; `php bin/php/ezcache.php ...` funktioniert wie bisher. |
| Wie Sie es prüfen | `php bin/php/console --version` |
| Wie Sie es beheben | Nichts zu beheben. Verwenden Sie `php bin/php/console list`, um einen Befehl zu finden. |

## In einer Minute starten

Führen Sie jeden Befehl aus dem Wurzelverzeichnis von Exponential aus, dem Verzeichnis, das `index.php`,
`autoload.php` und `bin/` enthält. Sie brauchen PHP 8.0 oder neuer (PHP 7.4 ohne Support nutzbar) in Ihrem `$PATH` (`php -v`); die Befehle
`exp:velocity` laufen ebenfalls unter 8.0, wie Velocity ab seinem Release 0.0.4.54. Einige Unterskripte brauchen
außerdem `curl` und `wget`; die Konsole selbst nicht.

```bash
ls index.php                      # confirms you are in the right directory
php bin/php/console               # list every command (same as "list")
php bin/php/console --version     # print the console version
```

Erwarteter Anfang der Auflistung:

```
════════════════════════════════════════════════════════════════════
  Exponential Console  1.0.0  (Exponential CMS 6.x)
  With great power comes great responsibility.
════════════════════════════════════════════════════════════════════

exp   — bin/php/*.php  (Exponential PHP scripts)
──────────────────────────────────────────────────
  exp:ezcache          Exponential Cache Handler
  exp:preload          Exponential CMS — site preloader & cache warmer
  exp:updatesearchindex  Exponential search index updater.
  ...
```

## Häufige Befehle

```bash
# Clear all caches
php bin/php/console exp:ezcache --clear-all

# Everything Setup > Cache does, with --dry-run and PASS/FAIL (see cache-console.md)
php bin/php/console exp:cache --help
php bin/php/console exp:cache clear --tag=ini --dry-run

# Regenerate the autoload arrays after adding an extension or a class
php bin/php/console exp:ezpgenerateautoloads

# Rebuild the search index
php bin/php/console exp:updatesearchindex

# Warm the caches of the default siteaccess, or of one siteaccess
php bin/php/console exp:preload --apply
php bin/php/console exp:preload --apply --siteaccess=sevenx_site_user

# Fix directory and file permissions after a deployment
php bin/php/console bin:modfix

# Run the session garbage collector
php bin/php/console exp:ezsessiongc

# Measure the site: page timings, two servers back to back, the kernel in-process (see benchmark.md)
php bin/php/console exp:benchmark
php bin/php/console exp:benchmark --compare=https://www.example.com,https://www.example.com:8080
php bin/php/console exp:benchmark kernel --save=var/benchmark/base.json

# Put .htaccess and config.php into place from the shipped templates (nothing is written without --apply)
php bin/php/console exp:setup:status
php bin/php/console exp:setup:htaccess --apply              # .htaccess_root -> .htaccess (--static: .htaccess_root_static)
php bin/php/console exp:setup:config --apply                # config.php-RECOMMENDED -> config.php

# Read and change the constants in config.php
php bin/php/console exp:config:list --only-set
php bin/php/console exp:config:get EZP_INI_FILEMTIME_CHECK
php bin/php/console exp:config:set EZP_INI_FILEMTIME_CHECK false --apply
php bin/php/console exp:config:unset EZP_INI_FILEMTIME_CHECK --apply
```

Die Befehle für Setup-Dateien und `config.php` sind in
[.htaccess und config.php von der Konsole aus einrichten](../../anleitungen/setup-dateien-von-der-konsole.md) beschrieben
und mit jeder Option in [exp:setupfiles](../../beispiele/befehle/setupfiles.md) und
[exp:configphp](../../beispiele/befehle/configphp.md) aufgeführt. `exp:setup:*` und `exp:config:*` sind Aliase von
`exp:setupfiles <htaccess|config|status>` und `exp:configphp <list|get|set|unset>`.

Der Befehl für Benchmarks ist in [Benchmark](../../funktionen/6.0/benchmark.md) beschrieben und wird in der
Anleitung [Benchmarking von Exponential](../../anleitungen/benchmarking.md) erklärt.

Bevor ein Skript läuft, gibt die Konsole einen kurzen Hinweis auf **stderr** aus, sodass weitergeleitete Ausgabe
sauber bleibt:

```
  ▶  running exp:ezcache  →  bin/php/ezcache.php
```

## Einen Befehl finden

```bash
php bin/php/console list              # every command
php bin/php/console list exp          # bin/php/*.php
php bin/php/console list shell        # bin/shell/*.sh
php bin/php/console list bin          # bin/*.sh and bin/*.php
php bin/php/console list cron         # cronjob parts
php bin/php/console list ext:hcaptcha # the commands of one extension
```

Wenn Sie sich bei einem Namen vertippen, schlägt die Konsole den ähnlichsten vor:

```
$ php bin/php/console exp:ezcach

  Unknown command: exp:ezcach
  Did you mean:    exp:ezcache?
```

## Hilfe zu einem Befehl

Alle drei Formen geben den Verwendungsblock des Skripts selbst aus:

```bash
php bin/php/console help exp:preload
php bin/php/console --help exp:preload
php bin/php/console exp:preload --help
```

## Befehlsnamen

| Namespace | Führt aus | Beispiel |
|---|---|---|
| `exp:<name>` | `bin/php/<name>.php` | `exp:ezcache` |
| `shell:<name>` | `bin/shell/<name>.sh` | `shell:phpcheck` |
| `bin:<name>` | `bin/<name>.sh` oder `bin/<name>.php` | `bin:modfix` |
| `ext:<ext>:<name>` | `extension/<ext>/bin/php/<name>.php` oder `extension/<ext>/bin/<name>.sh` | `ext:hcaptcha:install` |
| `ext:<ext>:sh:<name>` | `extension/<ext>/bin/shell/<name>.sh` | `ext:myext:sh:setup` |
| `cron:<part>` | den Cronjob-Teil `<part>` über `runcronjobs.php` | `cron:frequent` |
| `crontab:list`, `crontab:edit` | die System-Crontab des aktuellen Benutzers | |

Ohne Namespace versucht die Konsole `exp:`, dann `shell:`, dann `bin:`. Diese beiden sind gleich:

```bash
php bin/php/console ezcache --clear-all
php bin/php/console exp:ezcache --clear-all
```

## Optionen

Alles nach dem Befehlsnamen geht unverändert an das Skript:

```bash
php bin/php/console exp:preload --apply --siteaccess=sevenx_site_user
php bin/php/console exp:cleanupversions --dry-run
php bin/php/console exp:updatesearchindex --siteaccess=sevenx_site_user --verbose
```

Diese Optionen gehören zur Konsole und werden nicht weitergegeben:

| Option | Wirkung |
|---|---|
| `--version`, `-V` | die Version der Konsole ausgeben und beenden |
| `--help`, `-h` (allein) | die Liste der Befehle anzeigen |
| `--quiet`, `-q` | kein Hinweis zum Dispatch auf stderr |

## Cronjob-Teile und die Crontab (hinzugefügt im Juni 2026)

Commit `a2ef4efa50`.

Ein Cronjob-Teil ist ein Block `[CronjobPart-<name>]` mit einem oder mehreren Einträgen `Scripts[]=`, in
`settings/cronjob.ini` oder in der `settings/cronjob.ini.append.php` einer Erweiterung. Die Konsole liest die Teile
aus diesen INI-Dateien, nicht aus Dateinamen, und führt sie über `runcronjobs.php` aus:

```bash
php bin/php/console list cron
php bin/php/console cron:frequent --allow-root-user
php bin/php/console cron:infrequent -s site_admin    # siteaccess flags are passed on
```

Die Teile einer Standardinstallation sind `infrequent`, `frequent`, `contentjobs`, `audit`, `unlock`,
`cluster_maintenance`, `cleanuprss` und `cache_cleanup`. `list cron` beschreibt jeden Teil mit den Tags
`@description` der Skripte, die er ausführt; jedes Kernskript unter `cronjobs/` hat eines.

So sehen Sie, dass die Teile geplant sind:

```bash
php bin/php/console crontab:list      # print the system crontab of the current user
php bin/php/console crontab:edit      # open it in $EDITOR
```

Die Ansicht derselben Informationen im Browser ist die
[Cronjob-Konsole](../../funktionen/6.0/cronjobs-in-der-konsole.md).

## Befehle von Erweiterungen

Skripte an diesen Stellen werden automatisch gefunden:

```
extension/<extname>/bin/php/<name>.php      → ext:<extname>:<name>
extension/<extname>/bin/shell/<name>.sh     → ext:<extname>:sh:<name>
extension/<extname>/bin/<name>.sh           → ext:<extname>:<name>
```

```bash
php bin/php/console list ext:hcaptcha
php bin/php/console ext:hcaptcha:install --siteaccess=sevenx_site_user
```

## Das eigene Skript beschreiben

Wenn `list` `(no description)` anzeigt, fügen Sie oben in der Datei ein Tag `@description` hinzu. Beide Tags werden
automatisch gelesen; eine Registrierung ist nicht nötig.

PHP-Skript, im einleitenden Docblock:

```php
<?php
/**
 * File containing my-script.php
 *
 * @description One-line summary shown in console list
 * @long-description Longer explanation for console help output.
 */
```

Shell-Skript, in der Zeile nach dem Shebang:

```bash
#!/bin/bash
# @description One-line summary shown in console list
# @long-description Longer explanation for console help output.
```

Ein Cronjob-Skript verwendet dasselbe Tag, zum Beispiel
`@description Remove expired baskets of anonymous visitors`.

## Fehlerbehebung

| Meldung | Ursache und Lösung |
|---|---|
| `PHP Fatal error: ... autoload.php` | Sie sind nicht im Wurzelverzeichnis von Exponential. Wechseln Sie mit `cd /path/to/your/exponential-root` dorthin und führen Sie den Befehl erneut aus. |
| `Running scripts as root may be dangerous` | Das Skript läuft als `root`. Fügen Sie zur Bestätigung `--allow-root-user` hinzu, zum Beispiel `php bin/php/console exp:ezcache --clear-all --allow-root-user`. |
| `Warning: config.php does not compile: ...` | `config.php` enthält einen Syntaxfehler; auch die Website ist ausgefallen. Andere Befehle als `exp:setup:*` und `exp:config:*` brechen daran ab. Korrigieren Sie die genannte Zeile, oder ersetzen Sie die Datei mit `php bin/php/console exp:setup:config --force --apply` (die defekte Datei wird vorher gesichert). |
| `Unknown command: exp:myscript` | Die Datei liegt nicht dort, wo der Namespace sie erwartet. Prüfen Sie, dass `bin/php/myscript.php` existiert und lesbar ist, und führen Sie dann `php bin/php/console list` aus. |
| Keine Farben | Das Terminal unterstützt keine ANSI-Codes, oder die Ausgabe wird umgeleitet; Farben werden dann automatisch ausgeschaltet. |

## Wie es funktioniert

- Die Konsole ruft für sich selbst weder `eZScript::startup()` noch `eZScript::initialize()` auf. Jedes Skript
  verwaltet seinen eigenen Lebenszyklus, einschließlich der Datenbankverbindung.
- Skripte laufen mit derselben PHP-Binärdatei wie die Konsole.
- Der Exit-Code des Skripts wird unverändert zurückgegeben, sodass die Konsole in Shell-Pipelines und CI-Jobs sicher
  ist.

## Verwandte Seiten

- [Cache-Konsole (`exp:cache`)](cache-konsole.md)
- [.htaccess und config.php von der Konsole aus einrichten](../../anleitungen/setup-dateien-von-der-konsole.md) (`exp:setup:*`, `exp:config:*`)
- [Befehle, Cronjob-Teile und Modul-Views als Klassen](cli-cronjob-und-view-abstraktionen.md)
- [Cronjob-Konsole](../../funktionen/6.0/cronjobs-in-der-konsole.md)
- [Juni 2026, erste Hälfte (1. bis 15. Juni)](../../geschichte/2026/2026-06a.md)
- [Eine Website betreiben](../../anleitungen/betrieb-einer-website.md)
