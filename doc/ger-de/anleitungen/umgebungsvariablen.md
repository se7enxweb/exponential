# Umgebungsvariablen

Diese Seite listet die Umgebungsvariablen auf, die Exponential 6 liest, was jede bewirkt und wer sie setzt. Sie
richtet sich an Administratoren, Hoster und Entwickler, die einen Server, einen Container oder eine
Deployment-Pipeline konfigurieren.

Exponential wird über seine INI-Dateien (`settings/`) konfiguriert und, für einige Dinge, die bekannt sein müssen,
bevor die Einstellungen gelesen werden, über Konstanten in `config.php` und `config.env.php` (`EXP_ENV`,
`EZP_INI_FILEMTIME_CHECK` und ähnliche). Umgebungsvariablen werden dort verwendet, wo die Einstellung zu **einem
Serverprozess** gehören muss und niemals mit einer Kopie der Einstellungen mitwandern darf: der Demo-Modus, das
Engine-Archiv, Passwörter, die einem Installer übergeben werden, und Werte, die ein Befehl an die von ihm gestarteten
Prozesse weitergibt.

PHP liest eine Umgebungsvariable mit `getenv()`. Unter PHP-FPM liefert das auch die FastCGI-Parameter der Anfrage
(`env[...]` im Pool, `SetEnv` in Apache mit `mod_proxy_fcgi`, `fastcgi_param` in nginx); unter Apache mit mod_php und
LiteSpeed mit `lsphp` kommt ein `SetEnv` auf dieselbe Weise an. Persistente Worker (Exponential Velocity, FrankenPHP)
erben beim Start die Umgebung des Serverprozesses.

## Inhalt

1. [Variablen, die Sie setzen](#variablen-die-sie-setzen)
2. [Variablen, die Exponential für seine eigenen Prozesse setzt](#variablen-die-exponential-für-seine-eigenen-prozesse-setzt)
3. [Konstanten, die keine Umgebungsvariablen sind](#konstanten-die-keine-umgebungsvariablen-sind)
4. [Wo eine Umgebungsvariable gesetzt wird](#wo-eine-umgebungsvariable-gesetzt-wird)
5. [Verwandte Seiten](#verwandte-seiten)

## Variablen, die Sie setzen

| Variable | Werte | Was sie bewirkt | Gelesen von |
|---|---|---|---|
| `EXP_DEMO_MODE` | `1` (genau) | **Demo-Modus** für eine öffentliche Demo: Administratorpasswörter können auf keinem Weg geändert oder zurückgesetzt werden, die Installer akzeptieren ein bekanntes oder kurzes Administratorpasswort (`publish`, `publishing`; nie ein leeres), die Administration zeigt ein Banner, und jeder Prozess vermerkt `demo/mode` in `security.log` und `system.demo.mode` im Audit. Jeder andere Wert oder gar keiner lässt ihn ausgeschaltet. Wird bei jedem Aufruf gelesen (`getenv()`, dann `$_SERVER`); nie aus einer INI-Datei oder einer Anfrage. Setzen Sie ihn nie für eine echte Site. [Demo-Modus](../bc/6.0/demomodus.md) | `expDemoMode` (`kernel/classes/expdemomode.php`) |
| `EXP_ENGINE_PHAR` | der Pfad eines Engine-Archivs | Führt die Kernel- und Bibliotheksklassen aus diesem Archiv statt aus den Dateien auf der Festplatte aus (nur wenn die Datei existiert). `exp:velocity` setzt sie für die von ihm gestarteten Server, wenn ein Archiv konfiguriert ist. [Velocity-Engines](../bc/6.0/velocity-engines.md) | `autoload.php`, `expVelocity` |
| `EXP_INSTALL_DB_PASSWORD` | ein Passwort | Das Datenbankpasswort für `exp:install`, wenn `--db-password` nicht angegeben ist, damit es weder in der Prozessliste noch in der Shell-Historie erscheint. [Kapitel 7](../installation/07-installation-ueber-die-konsole.md) | `bin/php/install.php` |
| `EXP_KICKSTART_LOG` | `0` | `0` schaltet `var/log/kickstart.log` von `exp:kickstarter run` ab. [Kapitel 6](../installation/06-kickstarter.md) | der Kickstarter |
| `EXP_BENCHMARK_AUTH` | `user:password` | HTTP-Basic-Authentifizierung für `exp:benchmark`, wenn `--auth` nicht angegeben ist | der Benchmark-Befehl |
| `EXP_MONGO_TRACE` | ein Dateipfad | Schreibt jede MongoDB-Anweisung vor ihrer Ausführung in diese Datei, um eine Anweisung zu finden, die nie zurückkehrt. Nicht gesetzt, ist sie aus und kostet nichts | der MongoDB-Treiber (`lib/ezdb/classes/expmongodb.php`) |
| `EXP_SETUP_LOG_DIR` | ein Verzeichnis | Wo `setup.log` und der Zustand des Web-Setups statt in `var/log` abgelegt werden (die Tests verwenden es, damit sie nie das Log einer echten Installation rotieren) | `expSetupLog` |
| `PHP_CLI_SERVER_WORKERS` | eine Zahl | PHPs eigene Variable: die Worker des eingebauten Webservers von PHP, den die `php`-Engine von Velocity verwendet | PHP; angezeigt unter **Setup > Systeminformationen** |

## Variablen, die Exponential für seine eigenen Prozesse setzt

Diese werden von einem Exponential-Befehl für einen Prozess gesetzt, den er startet. Setzen Sie sie nicht selbst.

| Variable | Gesetzt von | Bedeutung |
|---|---|---|
| `EXP_SETUP_RUN` | dem Setup, für die Befehle, die es ausführt | die Befehle arbeiten mit den Einstellungen, wie sie vor der Installation sind; das Audit schlägt den Benutzer nicht nach |
| `EXP_KICKSTART_LOG_CHILD` | `exp:kickstarter run` | der Lauf ist der protokollierte Kindprozess |
| `EXP_PRELOAD_DETACHED`, `EXP_PRELOAD_ERRORS` | dem Hintergrundjob des Preloaders | der Job läuft losgelöst; wohin seine Fehler gehen |
| `EXP_PHAR_REEXEC` | `exp:phar build` | der Build hat sich selbst mit abgeschaltetem `phar.readonly` erneut gestartet |
| `EXP_VELOCITY_ENGINE`, `EXP_VELOCITY_ROOT`, `EXP_VELOCITY_STATIC_PATHS`, `EXP_VELOCITY_NEVER_STATIC`, `EXP_VELOCITY_FOLLOW_SYMLINKS` | `exp:velocity` für die Engines `php` und `frankenphp` | der Name der Engine, das Document Root und welche Dateien unverändert ausgeliefert werden |

## Konstanten, die keine Umgebungsvariablen sind

`EXP_ENV` (der Name der Umgebung, `config.env.php`), `EZP_INI_FILEMTIME_CHECK`, `EZP_DIR_MODE_MAX`,
`EZP_FILE_MODE_MAX` und die anderen `EZP_*`- / `EXP_*`-Namen in `config.php-RECOMMENDED` und `config.env.php.example`
sind PHP-Konstanten, die in diesen Dateien definiert werden. Eine gleichnamige Umgebungsvariable zu setzen, bewirkt
nichts. Umgekehrt gilt für `EXP_DEMO_MODE`: Eine gleichnamige Konstante in `config.php` zu definieren, bewirkt nichts;
der Demo-Modus kommt nur aus der Umgebung.

## Wo eine Umgebungsvariable gesetzt wird

| Wo PHP läuft | Wie |
|---|---|
| Apache (mod_php oder PHP-FPM über `mod_proxy_fcgi`) | `SetEnv NAME value` im virtuellen Host oder in der `.htaccess` der Site (`mod_env`) |
| LiteSpeed / OpenLiteSpeed mit `lsphp` | `SetEnv` in `.htaccess` (LiteSpeed liest sie wie Apache) oder die Umgebung des virtuellen Hosts |
| PHP-FPM | `env[NAME] = value` im Pool der Site, dann den Master dieses Pools neu laden |
| nginx mit PHP-FPM | `fastcgi_param NAME value;` dort, wo Anfragen an PHP-FPM übergeben werden, oder der Pool |
| Exponential Velocity | die Umgebung des Prozesses, der `exp:velocity start` ausführt: `Environment=` in seiner systemd-Unit oder einem Drop-in, oder `export` in der Shell. Worker erben sie; ein Neustart aus einer anderen Shell startet mit der Umgebung dieser Shell. `/etc/vc/envvars` wird für die eigenen Einstellungen des Servers gelesen und nicht an die Worker exportiert |
| Cron | eine Zeile `NAME=value` am Anfang der Crontab |
| Konsole | `export NAME=value` oder `NAME=value command` für einen einzelnen Befehl |
| Container | `ENV` im Image, `-e`, `--env-file` oder `environment:` / `env_file:` für einen Dienst |

Beispiele für den Demo-Modus, eines pro Ort: [Beispiele zum Demo-Modus](../beispiele/demomodus/README.md).

## Verwandte Seiten

- [Demo-Modus](../bc/6.0/demomodus.md), [FAQ zum Demo-Modus](../hilfe/demomodus-faq.md)
- [Kapitel 2: Voraussetzungen](../installation/02-voraussetzungen.md), [Kapitel 13: Sicherheitshärtung](../installation/13-sicherheitshaertung.md)
- [Velocity-Engines](../bc/6.0/velocity-engines.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
