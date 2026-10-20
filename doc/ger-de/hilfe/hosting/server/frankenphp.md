---
title: FrankenPHP
audience: hosting, admin
category: hosting
order: 110
summary: FrankenPHP für Hoster - was die produktionsreife alternative Engine ist, wie exp:velocity die Binärdatei installiert und prüft, ihr eingebettetes PHP, das keine php.ini liest, Threads, HTTPS, Logs, ihre Grenzen gegenüber Velocity und die Fallen, denen ein Hoster zuerst begegnet.
keywords: FrankenPHP, Caddy, exp:velocity install, exp:frankenphp, Binärdatei, SHA-256, Sha256-Pin, klassischer Modus, Worker-Modus, num_threads, IniOptions, PHPIniFile, MySQL-Socket, Caddyfile, SiteInclude, Admin-Socket, GOMEMLIMIT, binary, classic mode, worker mode, MySQL socket, admin socket
type: help
---
# FrankenPHP

Diese Seite ist für Systemadministratoren bei Hosting-Unternehmen, die FrankenPHP für Kunden-Websites in Betracht ziehen. Sie
beantwortet: was FrankenPHP in Exponential ist, wie es installiert und geprüft wird, was sein eingebettetes PHP braucht, wie es sich mit
der eigenen Engine von Velocity vergleicht und was zuerst schiefgeht. Die Quellen sind
[Kapitel 8, Abschnitt 8.4](../../../installation/08-die-website-ausliefern.md#84-frankenphp) und
[FrankenPHP als Web-Engine von Exponential](../../../../eng-us/bc/6.0/frankenphp.md).

## Inhalt

1. [Was es ist](#was-es-ist)
2. [FrankenPHP oder die Engine qbix von Velocity](#frankenphp-oder-die-engine-qbix-von-velocity)
3. [Installieren und starten](#installieren-und-starten)
4. [Das eingebettete PHP](#das-eingebettete-php)
5. [Threads](#threads)
6. [HTTPS](#https)
7. [Konfiguration, Steuerung und Logs](#konfiguration-steuerung-und-logs)
8. [Fehlerbehebung](#fehlerbehebung)
9. [Checkliste](#checkliste)

## Was es ist

FrankenPHP ist der Webserver Caddy mit eingebautem PHP, als eine Binärdatei (150 bis 180 MB pro Plattform). Velocity lädt sie herunter,
prüft sie und steuert sie mit denselben Befehlen wie seine anderen Engines. Sie läuft im **klassischen Modus**: ein Pool von PHP-Threads,
jede Anfrage beginnt mit einem sauberen Zustand wie unter PHP-FPM. Der Worker-Modus von FrankenPHP wird bewusst nicht angeboten, weil der
Kernel den Zustand einer Anfrage in globalen Variablen hält und eine Anfrage in die nächste durchsickern würde.

`exp:frankenphp <verb>` ist dasselbe wie `exp:velocity <verb> --engine=frankenphp`
([Befehle zur Serversteuerung](../../../../eng-us/features/6.0/web-server-and-solr-commands.md)).

## FrankenPHP oder die Engine qbix von Velocity

| | `qbix` (Velocity) | `frankenphp` |
|---|---|---|
| Rolle | für jede Phase empfohlen | produktionsreife Alternative |
| PHP | das der Maschine, 8.1 oder neuer | das eigene der Binärdatei (PHP 8.5 in den offiziellen Releases 1.12) |
| Anfragen | persistente Worker | klassischer Modus, sauberer Zustand pro Anfrage |
| Antwort-Cache | ja | noch nicht (`[CacheSettings]` wird ignoriert) |
| TLS und HTTP/2 | ja | ja |
| `graceful` | erneutes exec, Socket bleibt erhalten | Neuladen der Konfiguration, keine abgebrochene Verbindung |
| Konfiguration | der Baum `/etc/vc` | ein erzeugtes Caddyfile |
| Zu pflegendes PHP der Maschine und PHP-FPM | das PHP der Maschine | keines für das Ausliefern (die Konsole nutzt weiterhin das PHP der Maschine) |

Wählen Sie FrankenPHP, wenn Sie eine geprüfte Binärdatei mit TLS und HTTP/2 und eine Trennung pro Anfrage wie bei PHP-FPM wollen. Wählen
Sie `qbix`, wenn Sie persistente Worker und den Antwort-Cache wollen ([Velocity-Engines](../../../../eng-us/bc/6.0/velocity-engines.md)).

## Installieren und starten

```bash
php bin/php/console exp:velocity config set ServerSettings Engine frankenphp --allow-root-user
php bin/php/console exp:velocity install --allow-root-user          # optional: start installs it when missing
php bin/php/console exp:velocity install --check --allow-root-user  # is the pinned binary in place?
php bin/php/console exp:velocity start --allow-root-user
php bin/php/console exp:velocity status --all --allow-root-user
```

- `install` holt das Release-Asset für diese Maschine zur `[FrankenPHPSettings] Version` (ausgeliefert `1.12.7`) und weist es ab, wenn
  sein SHA-256 nicht mit der daneben festgelegten Zeile `Sha256[<asset>]` übereinstimmt. Die Binärdatei wird in `var/vc/frankenphp/bin/`
  gespeichert.
- `Variant` wählt den Linux-Build: `gnu` (Standard, kann `.so`-Erweiterungen laden), `musl` (vollständig statisch) oder `mimalloc`.
- `install --from=<file>` installiert eine kopierte Datei auf einer Maschine ohne Zugriff auf GitHub, auf dieselbe Weise geprüft.
- Wurde ein Release upstream neu erstellt und passt der Pin nicht mehr, scheitert die Installation mit einem Unterschied im SHA-256. Die
  Quellseite beschreibt, wie Sie den veröffentlichten Digest prüfen und den Pin in einer Überschreibung setzen
  ([FrankenPHP, Abschnitt 2.4](../../../../eng-us/bc/6.0/frankenphp.md#24-if-the-pin-is-stale--the-re-cut-release-procedure-the-gotcha-we-hit)).
  Schalten Sie die Prüfung nie aus, um eine Installation durchzubringen.

Ausgelieferte Ports: `8089` für HTTP und `8444` für HTTPS (`[FrankenPHPSettings] Port`, `HTTPSPort`), sodass es für Tests neben der
Engine `qbix` laufen kann.

## Das eingebettete PHP

**Die Binärdatei liest keine `php.ini` der Maschine.** Alles, was die `php.ini` der Maschine liefern würde, muss als Zeilen
`IniOptions[]` angegeben werden, in dieser Reihenfolge (das Spätere gewinnt): `[PHPSettings] IniOptions[]` (mit `qbix` geteilt), dann
`[FrankenPHPSettings] IniOptions[]`. Oder `[FrankenPHPSettings] PHPIniFile` nennt eine eigene `php.ini`.

Ausgelieferte `[FrankenPHPSettings] IniOptions[]`: `display_errors=Off`, `log_errors=On`, `memory_limit=512M`, `opcache.enable=1`,
`post_max_size=64M`, `upload_max_filesize=32M`. Die Abstimmung von OPcache kommt aus `[PHPSettings]`.

> **Warnung**
> Die Falle, der Hoster zuerst begegnen: Mit `[DatabaseSettings] Server=localhost` verbinden sich die Treiber für MariaDB und MySQL über
> einen Socket, dessen Pfad normalerweise aus der `php.ini` kommt. Das eingebettete PHP hat keine, die Verbindung scheitert, und die
> Website zeigt `An unexpected error has occurred.` Setzen Sie den Socket ausdrücklich, oder verbinden Sie sich über TCP mit
> `Server=127.0.0.1` ([FrankenPHP, Abschnitt 6.1](../../../../eng-us/bc/6.0/frankenphp.md#61-the-mysql-socket-fix-the-key-gotcha)).

```ini
# settings/override/velocity.ini.append.php
[PHPSettings]
IniOptions[]=mysqli.default_socket=/var/lib/mysql/mysql.sock
IniOptions[]=pdo_mysql.default_socket=/var/lib/mysql/mysql.sock
```

Den echten Pfad finden Sie mit `php -i | grep -i "default_socket"`. Dasselbe gilt für jeden anderen Wert, den eine `php.ini` liefert: eine
Zeitzone, `sendmail_path`, `open_basedir`, `session.save_path`, die Konfiguration einer Erweiterung. Wenn etwas unter Apache funktioniert,
aber nicht unter FrankenPHP, vermuten Sie einen Wert der `php.ini`, den das eingebettete PHP nie gesehen hat.

Die Binärdatei bringt eine breite Auswahl an Erweiterungen mit, aber nicht unbedingt jede, die eine Website braucht. Auf der Variante
`gnu` kann eine fehlende Erweiterung als `IniOptions[]=extension=/path/ext.so` geladen werden, wenn sie zur PHP-Version der Binärdatei
passt; sonst ist ein eigener Build nötig.

## Threads

`[FrankenPHPSettings] Workers` ist die Anzahl der PHP-Threads; leer bedeutet den Standard von FrankenPHP, das Doppelte der CPU-Kerne.
`SpareWorkers` fügt unter Last Threads hinzu. Diese fallen **nicht** auf `[ServerSettings]` zurück. Gemessen auf einer Testinstallation mit
12 Kernen waren mehr Threads als das unter hoher Last langsamer: Bei 64 gleichzeitigen Anfragen schafften 12 bis 48 Threads etwa 10 % mehr
Arbeit als 96 oder 590, mit bis zu 24 % weniger CPU pro Seite
([FrankenPHP, Threads](../../../../eng-us/bc/6.0/frankenphp.md#threads-classic-mode)). Setzen Sie `Workers` erst ausdrücklich, nachdem Sie Ihre
eigenen Seiten gemessen haben.

FrankenPHP ist ein Go-Programm; `GOMEMLIMIT` und `GOMAXPROCS` sind Umgebungsvariablen des Prozesses, der `exp:velocity start` ausführt,
keine Konfiguration von Caddy.

## HTTPS

Standardmäßig an (`HTTPS=enabled`) auf `HTTPSPort` neben HTTP auf `Port`. Es nutzt `[HTTPSSettings] Certificate` und `Key`, wenn beide
gesetzt sind; sind beide leer, erstellt es ein selbstsigniertes Zertifikat in `var/vc/frankenphp/tls/` (ein Jahr gültig, einen Monat vor
Ablauf erneuert). Eine öffentliche Website nennt ihr Zertifikat; führen Sie nach dem Ersetzen der Dateien `exp:velocity graceful` aus.
ACME wird für diese Engine nicht verwaltet: `exp:velocity ssl` gehört zur Engine `qbix` und verweigert hier, und das erzeugte Caddyfile
setzt `auto_https off`.

## Konfiguration, Steuerung und Logs

| Was | Wo |
|---|---|
| Erzeugtes Caddyfile | `var/vc/frankenphp/run/Caddyfile`, bei jedem `start`, `graceful` und `restart` neu erzeugt und geprüft; nie bearbeiten |
| Ihre eigenen Caddy-Direktiven | eine Datei, die `[FrankenPHPSettings] SiteInclude` nennt |
| Admin-API | Socket nur für den Eigentümer, `var/vc/frankenphp/run/admin.sock`; `stop` und `graceful` nutzen ihn |
| Logs | `var/vc/frankenphp/log/access.log` und `error.log`, im JSON-Format von Caddy |
| Kompression | `[FrankenPHPSettings] Compression` (`zstd br gzip`) oder `disabled` |

```bash
php bin/php/console exp:velocity ctl caddyfile --engine=frankenphp --allow-root-user   # print what would be generated
php bin/php/console exp:velocity ctl validate  --engine=frankenphp --allow-root-user
php bin/php/console exp:velocity ctl version   --engine=frankenphp --allow-root-user
```

## Fehlerbehebung

| Symptom | Ursache | Abhilfe |
|---|---|---|
| `SHA-256 mismatch ... expected ... got ...` bei der Installation | der Pin ist veraltet, oder Asset oder Variante sind falsch | den veröffentlichten Digest prüfen, den Pin setzen, `install --force` |
| Datenbankverbindung scheitert, `An unexpected error has occurred.` | kein Socket für MariaDB oder MySQL im eingebetteten PHP | die Zeilen `IniOptions[]` für den Socket oder `Server=127.0.0.1` |
| Port von außen nicht erreichbar | Firewall geschlossen, oder `Host` an Loopback gebunden | den Port öffnen; `Host` setzen |
| Zertifikatswarnung im Browser | das selbstsignierte Zertifikat ist in Gebrauch | ein echtes Zertifikat in `[HTTPSSettings]` nennen |
| `graceful` meldet `reload rejected` | das neue Caddyfile (meist `SiteInclude`) ist ungültig | korrigieren; die laufende Konfiguration blieb erhalten |
| funktioniert unter Apache, hier 500 | ein Wert der `php.ini`, den das eingebettete PHP nie gesehen hat | als `IniOptions[]` hinzufügen oder `PHPIniFile` nutzen |

## Checkliste

- [ ] Binärdatei installiert und geprüft (`install --check`); der Pin passt.
- [ ] Datenbank-Socket oder TCP-Verbindung für das eingebettete PHP gesetzt; die Website antwortet.
- [ ] Jeder Wert der `php.ini`, von dem die Website abhängt, als `IniOptions[]` oder über `PHPIniFile` angegeben.
- [ ] Ein CA-Zertifikat in `[HTTPSSettings]` genannt, von Ihrem Werkzeug erneuert, gefolgt von `graceful`.
- [ ] `Workers` auf dem Standard belassen oder aus einer Messung gesetzt.
- [ ] Tarife, die auf den Antwort-Cache angewiesen sind, nutzen stattdessen `qbix`.

**Demomodus unter FrankenPHP.** Setzen Sie `EXP_DEMO_MODE=1` in der Umgebung des FrankenPHP-Prozesses (seiner Unit oder der Umgebung, mit der Velocity ihn startet), und zwar nur für eine öffentliche Demo. [Demomodus für Hoster](../sicherheit/demomodus-fuer-hoster.md).

## Verwandte Seiten

- [Velocity für Hoster](velocity-fuer-hoster.md)
- [Caddy](caddy.md)
- [Einen Webserver wählen](einen-webserver-waehlen.md)
- [FrankenPHP als Web-Engine von Exponential](../../../../eng-us/bc/6.0/frankenphp.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
