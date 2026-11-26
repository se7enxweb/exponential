---
title: Exponential unter Windows
audience: admin, developer
category: install-upgrade
order: 12
summary: Exponential 6.0.x läuft unter Windows - auf Windows Server und auf Windows 10 und 11 - mit dem offiziellen PHP für Windows hinter IIS, Apache oder Exponential Velocity, auf SQLite, Microsoft SQL Server, PostgreSQL, MySQL, MariaDB, Oracle oder MongoDB. Diese Seite beschreibt das zu installierende PHP, die Webserver, Velocitys portablen Worker-Pool für Windows, die Datenbanken, was die CI des Projekts unter Windows ausführt und was sich von Linux unterscheidet.
keywords: Windows, Windows Server, Windows 11, Windows 10, IIS, FastCGI, URL Rewrite, web.config, Apache unter Windows, PHP für Windows, php.exe, php-cgi, Velocity unter Windows, vc, vcc, qbixserver, portabler Pool, Pool-Modus, proc_open, SQL Server, LocalDB, sqlsrv, pdo_sqlsrv, Azure SQL, WSL2, PowerShell, Smoke-Test, Apache on Windows, PHP for Windows, Velocity on Windows, portable pool, smoke test
featured: true
context: setup/info
type: help
---
# Exponential unter Windows

**Exponential läuft unter Windows.** Es besteht aus reinem PHP und einer Datenbank, und so führt das offizielle PHP für
Windows es genauso aus wie unter Linux und macOS: hinter IIS, hinter Apache oder auf Exponential Velocity, dem
Anwendungsserver, der mit Exponential ausgeliefert wird und für Windows einen eigenen Worker-Pool hat. Die Datenbank
kann SQLite sein (ganz ohne Server), Microsoft SQL Server und Azure SQL, PostgreSQL, MySQL, MariaDB, Oracle oder
MongoDB.

Diese Seite richtet sich an Administratoren, die Exponential auf Windows Server betreiben, und an Entwickler, die
Websites auf einem Windows-Desktop bauen. Die anderen Systeme stehen unter
[Betriebssystemunterstützung](betriebssystemunterstuetzung.md).

## Inhalt

1. [Was Sie brauchen](#was-sie-brauchen)
2. [Installieren](#installieren)
3. [Webserver unter Windows](#webserver-unter-windows)
4. [Exponential Velocity unter Windows](#exponential-velocity-unter-windows)
5. [Datenbanken unter Windows](#datenbanken-unter-windows)
6. [Was die CI des Projekts unter Windows ausführt](#was-die-ci-des-projekts-unter-windows-ausführt)
7. [Was sich von Linux unterscheidet](#was-sich-von-linux-unterscheidet)
8. [Verwandte Seiten](#verwandte-seiten)

## Was Sie brauchen

| Bedarf | Unter Windows |
|---|---|
| Windows | eine Windows-Version, die Microsoft noch unterstützt: Windows Server 2022 oder 2025, Windows 10 oder 11 |
| PHP | PHP 8.0 bis 8.5 von [windows.php.net](https://windows.php.net/download/), x64 (für Velocity auch x86). Der nicht threadsichere Build (NTS) für IIS und FastCGI. PHP 7.4 ist wie überall ohne Support nutzbar ([warum](warum-exponential-php-7-4-unterstuetzt.md)) |
| PHP-Erweiterungen | die offiziellen Builds bringen sie alle als DLLs mit; schalten Sie sie in der `php.ini` ein: `intl`, `mbstring`, `curl`, `gd`, `fileinfo`, `openssl`, `sockets` und die Erweiterung der Datenbank (`sqlite3`, `sqlsrv` oder `pdo_sqlsrv`, `pgsql`, `mysqli`, `oci8`, `mongodb`) |
| Composer | das [Composer-Setup für Windows](https://getcomposer.org/download/) |
| Webserver | IIS, Apache für Windows oder Exponential Velocity (kommt mit Exponential) |
| Datenbank | keine für SQLite; sonst der Server Ihrer Wahl, auf demselben Rechner oder anderswo |

## Installieren

Composer installiert Exponential unter Windows genau wie auf jedem anderen System, aus PowerShell oder der
Eingabeaufforderung:

```bat
composer create-project se7enxweb/exponential C:\sites\exponential
cd C:\sites\exponential
```

Öffnen Sie dann die Website im Browser und folgen Sie dem Setup-Assistenten, oder verwenden Sie den Kickstarter; beide
beschreibt die [Installationsanleitung](../INSTALL.md). Der Konsolen-Installer funktioniert ebenso, zum Beispiel mit
SQLite:

```bat
php bin\php\console exp:install --apply --random-password --url=http://localhost:8080
```

Websites auf Microsoft SQL Server werden mit `--db=sqlserver` installiert
([SQL-Server-Treiber](../funktionen/6.0/sqlserver-datenbanktreiber.md)).

Geben Sie dem Konto, unter dem der Webserver läuft (der Identität des IIS-Anwendungspools, dem Dienstkonto von Apache
oder dem Konto, das Velocity startet), während der Installation Schreibrechte auf `var\`, `settings\override\` und
`settings\siteaccess\` und danach auf `var\`.

## Webserver unter Windows

| Server | Wie PHP läuft | Konfiguration |
|---|---|---|
| **IIS** (Windows Server, Windows 10 und 11) | FastCGI (`php-cgi.exe`, NTS-Build), das Modul URL Rewrite für die Umschreiberegeln | [IIS-Beispiel](../beispiele/webserver/iis.md) mit einer fertigen [`web.config`](../../eng-us/examples/web-servers/web.config); Hinweise zum Hosting: [IIS](hosting/server/microsoft-iis.md) |
| **Apache für Windows** (zum Beispiel die Builds von Apache Lounge) | FastCGI oder das Apache-Modul von PHP | derselbe virtuelle Host wie unter Linux: [Virtueller Host für Apache](../beispiele/webserver/apache-virtueller-host.md) |
| **Exponential Velocity** | ein eigener Pool von PHP-Workern | [unten](#exponential-velocity-unter-windows) |
| der eingebaute Server von PHP | ein Prozess | für die Entwicklung, aus dem Installationsordner: `php -S localhost:8080 -t . bin\php\velocity-router.php` (der Router wendet die Umschreiberegeln an) |

Die Umschreiberegeln sind auf jedem Server dieselben: Das IIS-Beispiel überträgt die Regeln von `.htaccess_root` Regel
für Regel in Regeln von URL Rewrite, sodass dieselben URLs `index.php` erreichen, die Bild- und Design-Dateien direkt
ausgeliefert und die privaten Ordner der Installation abgewiesen werden.

## Exponential Velocity unter Windows

Exponential Velocity (`vc`) ist der Anwendungsserver, der mit Exponential ausgeliefert wird (die `composer.json` im
Wurzelverzeichnis verlangt `se7enxweb/exponential-velocity`). Er lädt die Anwendung einmal und hält sie zwischen den
Anfragen im Speicher; daher kommt seine Geschwindigkeit.

**Unterstützung für Windows.** Ab Velocity 0.0.4.63 hat Velocity einen **portablen Worker-Pool**: Wo es kein `fork()`
gibt, wie unter Windows, ist jeder PHP-Worker ein eigener Prozess, gestartet mit `proc_open()`, der die Anwendung einmal
startet und dann Anfrage um Anfrage beantwortet. Die Worker verbinden sich über Loopback-TCP mit dem Server und weisen
sich mit einem Token aus; ein Worker, der stirbt, wird ersetzt; STRG+C in der Konsole beendet den Server und jeden
Worker geordnet. Der Pool-Modus wird mit `--pool-mode` gewählt (`auto`, `fork`, `portable`); `auto` bedeutet unter
Windows `portable` und unter Linux, den BSDs und macOS `fork`. Einstellungen und Interna: die
[Windows-Seite](https://github.com/se7enxweb/exponential-velocity/blob/main/docs/windows.md) der Engine.

**Starten und beenden.** Das Serverskript von Velocity liegt im Ordner `vendor\` von Exponential:

```bat
cd C:\sites\exponential
C:\php\php.exe vendor\se7enxweb\exponential-velocity\sbin\qbixserver.php --root=. --preset=exponential --port=8080 --pid=C:\sites\run\vc.pid
rem in einer anderen Konsole:
C:\php\php.exe vendor\se7enxweb\exponential-velocity\sbin\qbixserver.php --stop --pid=C:\sites\run\vc.pid
```

| Aktion | Unter Windows |
|---|---|
| Beenden | `--stop --pid=DATEI` oder STRG+C in der Konsole des Servers |
| Geänderten Code laden | `--reload --pid=DATEI`: jeder Worker wird nacheinander ersetzt |
| Worker | `--workers=N`; Standard ist einer pro Kern, 2 bis 8 |
| Pool | `--pool-mode=portable` (unter Windows der Standard) |
| Smoke-Test | `powershell -ExecutionPolicy Bypass -File vendor\se7enxweb\exponential-velocity\tests\windows\smoke.ps1 -Php C:\php\php.exe` |

**Binaries.** Jede Velocity-Version veröffentlicht außerdem ausführbare Windows-Dateien aus einer einzigen Datei
(`qbixserver-windows-x64*.exe`, eine pro PHP-Version und Variante) auf der
[Release-Seite](https://github.com/se7enxweb/exponential-velocity/releases). Sie bringen ihr eigenes PHP mit, ohne
`intl` gebaut, das Exponential braucht; betreiben Sie Velocity für eine Exponential-Website daher wie oben gezeigt mit
dem offiziellen PHP für Windows.

## Datenbanken unter Windows

| Datenbank | Unter Windows |
|---|---|
| SQLite 3 | im offiziellen PHP enthalten (`sqlite3`); die Datenbank ist eine Datei unter `var\`. Kein Server |
| Microsoft SQL Server 2017 bis 2025, Azure SQL | der eigene Treiber des Kernels `expSQLServerDB` über Microsofts Erweiterung `sqlsrv` oder `pdo_sqlsrv` (oder `pdo_odbc`); SQL Server Express und LocalDB eignen sich für die Entwicklung ([Treiber](../funktionen/6.0/sqlserver-datenbanktreiber.md)) |
| PostgreSQL 13 bis 19 | `pgsql` (im offiziellen PHP); der Windows-Installer von PostgreSQL für den Server |
| MySQL, MariaDB | `mysqli` (im offiziellen PHP) |
| Oracle 19c und neuer | `oci8` mit Oracle Instant Client für Windows |
| MongoDB 6.0 und neuer | die DLL der Erweiterung `mongodb` von PECL, PHP 8.2 oder neuer |

Jede Engine, ihre Versionen und ihre Installer: [Unterstützte Datenbanken](datenbankunterstuetzung.md).

## Was die CI des Projekts unter Windows ausführt

- **Exponential** (`.github/workflows/phpunit.yml`, Job „SQL Server LocalDB smoke“): Auf GitHubs Runner
  `windows-latest` mit PHP 8.4, `sqlsrv` und SQL Server LocalDB laufen bei jedem Push die Testsuiten des
  SQL-Server-Treibers gegen eine laufende LocalDB-Instanz.
- **Exponential Velocity** (der Workflow `windows` der Engine, bei jedem Push): Windows Server 2022 und 2025 mit PHP
  8.1, 8.3 und 8.5 (x64) und Windows Server 2022 mit PHP 8.3 (x86, 32 Bit) führen den Unit-Test des portablen Pools
  (81 Prüfungen) und den Windows-Smoke-Test aus (15 Prüfungen: Anfragen, die persistente Worker beantworten,
  Wiederverwendung der Worker, ein beendeter Worker wird ersetzt, ein fataler PHP-Fehler wird mit 500 beantwortet, ein
  geordnetes Beenden mit danach freiem Port).
- Die gesamte PHPUnit-Suite des Kernels läuft unter Linux auf PHP 8.1 bis 8.5; der Code, den sie testet, ist dasselbe
  PHP, das unter Windows läuft.

## Was sich von Linux unterscheidet

- **Kein `pcntl` und kein `posix` unter Windows.** Asynchrones Veröffentlichen (`bin/php/ezasynchronouspublisher.php`)
  forkt und ist nicht verfügbar; veröffentlichen Sie synchron, wie es der Standard ist. Velocity verwendet seinen
  portablen Pool, in dem jeder Worker seine eigene Kopie der Anwendung hält (kein Copy-on-Write); halten Sie die Zahl
  der Worker daher maßvoll.
- **Velocity-Funktionen, die `pcntl` brauchen** (sein Watchdog, das Neustarten beim Hot-Reload, gemeinsame Instanzen,
  geplante Aufgaben und WebSocket-Räume), sind unter Windows aus oder laufen im Serverprozess. Starten Sie Velocity aus
  einer Konsole oder der Aufgabenplanung.
- **Pfade.** Die Befehle in dieser Hilfe sind mit `/` geschrieben; verwenden Sie unter Windows `\` in der Shell, und
  `php` ist dort, wo Sie PHP entpackt haben (zum Beispiel `C:\php\php.exe`).
- **WSL2** führt die Linux-Einrichtung unverändert aus und ist eine gute Wahl für die Entwicklung auf einem
  Windows-Desktop; halten Sie die Website im Linux-Dateisystem statt unter `/mnt/c`.
- **Statisches Veröffentlichen unter Windows** hat eigene Hinweise:
  [Statisches Veröffentlichen unter Windows und in Clustern](../anleitungen/statisches-veroeffentlichen-unter-windows-und-in-clustern.md).

## Verwandte Seiten

- [Betriebssystemunterstützung](betriebssystemunterstuetzung.md)
- [Unterstützte Datenbanken](datenbankunterstuetzung.md)
- [Weg von einer alten Datenbank](weg-von-einer-alten-datenbank.md)
- [Velocity ist Teil von Exponential 6.0.15](velocity-ist-teil-von-exponential-6-0-15.md)
- [IIS-Hosting](hosting/server/microsoft-iis.md)
- [Microsoft-SQL-Server-Treiber](../funktionen/6.0/sqlserver-datenbanktreiber.md)
