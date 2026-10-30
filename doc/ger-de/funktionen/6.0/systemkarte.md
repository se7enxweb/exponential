---
title: "Die Systemkarte: exp:expfetch, exp:info und exp:about"
audience: admin, developer, hosting, partner, supporter, trainer, advocate, press
category: administration
summary: Drei Konsolenbefehle, die das Exponential-„e“ neben die Fakten einer Installation, ihrer Engine und ihrer Maschine zeichnen, mit Hinweisen zum Zustand. exp:expfetch ist die Karte, exp:info ergänzt eine Bestandsaufnahme, exp:about ergänzt das Projekt. Text, reiner Text oder JSON; Themes, Farbtiefen, eine Live-Ansicht mit --watch und eine Extension-API für Provider, Themes und Logos. Gibt nie ein Geheimnis aus.
keywords: exp:expfetch, exp:info, exp:about, expfetch, Systemkarte, system card, syscard, syscard.ini, expSysCardProvider, expSysCardBaseProvider, expSysCardRegistry, expSysCardRenderer, Systeminformationen, system information, Zustandshinweise, health hints, neofetch, fastfetch, --json, --plain, --watch, --animate, NO_COLOR, Truecolor, 256 Farben, Themes, Logo, Konsole, Support-Anfrage, Monitoring
type: feature
---
# Die Systemkarte: exp:expfetch, exp:info und exp:about

Diese Seite richtet sich an Administratoren, Hosting-Mitarbeiter und Entwickler, die auf einen Blick sehen möchten, was
eine Exponential-Installation ist und wie es ihr geht, und an alle, die das in eine Support-Anfrage einfügen oder in
einer Demo zeigen müssen. Sie behandelt die drei Befehle, jede Option, die Ausgabeformate, die Hinweise, die
Einstellungen und wie eine Extension eigene Fakten hinzufügt. Hinzugefügt am 20.10.2026 in 6.0.15.

## Inhalt

1. [Kurz gefasst](#kurz-gefasst)
2. [exp:expfetch: die Karte](#expexpfetch-die-karte)
3. [exp:info: die Karte und eine Bestandsaufnahme](#expinfo-die-karte-und-eine-bestandsaufnahme)
4. [exp:about: die Karte und das Projekt](#expabout-die-karte-und-das-projekt)
5. [Optionen](#optionen)
6. [Ausgabeformate](#ausgabeformate)
7. [Layout, Farben und Themes](#layout-farben-und-themes)
8. [Zustandshinweise](#zustandshinweise)
9. [Provider, Zeitbudgets und Isolation](#provider-zeitbudgets-und-isolation)
10. [Niemals Geheimnisse](#niemals-geheimnisse)
11. [Einstellungen: syscard.ini](#einstellungen-syscardini)
12. [Fakten, ein Theme oder ein Logo aus einer Extension hinzufügen](#fakten-ein-theme-oder-ein-logo-aus-einer-extension-hinzufügen)
13. [Wie die Aliasse funktionieren](#wie-die-aliasse-funktionieren)
14. [Shell-Vervollständigung](#shell-vervollständigung)
15. [Warum die Interna „Systemkarte“ heißen](#warum-die-interna-systemkarte-heißen)
16. [Verwandte Seiten](#verwandte-seiten)

## Kurz gefasst

| Befehl | Zeigt | Verwenden Sie ihn, wenn |
|---|---|---|
| `./console exp:expfetch` | Exponential, die Maschine, PHP, die Engine (Velocity, Apache, PHP-FPM), die Datenbank und die Website, mit Hinweisen | Sie sich an einem Server anmelden, nach einer Installation oder einem Upgrade, in einer Demo |
| `./console exp:info` | dasselbe, dazu jede Extension mit ihrer Version, die Siteaccesses, wichtige Einstellungen, die Cronjob-Teile, die Warteschlange, die Cache-Größen und die Abdeckung der Hilfe pro Sprache | Sie eine Bestandsaufnahme brauchen: eine Support-Anfrage, ein Audit, eine Übergabe |
| `./console exp:about` | dasselbe wie exp:expfetch, dazu, was Exponential ist, die Foundation und 7x, die Lizenz, Links, wie man beiträgt, das Partnerprogramm und die Credits | jemand fragt „Was ist das?“ |

Alle drei sind rein lesend. Sie geben nur dann farbig im Terminal aus, wenn die Ausgabe ein Terminal ist, nie bei
gesetztem `NO_COLOR`, und geben nie ein Passwort, einen Schlüssel, ein Token oder eine vollständige Datenbankadresse
aus. Fügen Sie als root `--allow-root-user` hinzu, wie bei jedem Konsolenbefehl.

## exp:expfetch: die Karte

**Was sie zeigt.** Das Exponential-„e“ als orangefarbene Blockgrafik und daneben die Fakten, in Abschnitte gruppiert:

- **Exponential**: die Version, ihr Status und ihre Linie; bei einem Git-Checkout der Branch und der Commit.
- **System**: das Betriebssystem, der Kernel, der Hostname, die Laufzeit, die Last, die CPU (Modell und Kerne), der
  Arbeitsspeicher, der Datenträger der Installation, die Shell und das Terminal.
- **PHP**: die Version und die SAPI, OPcache (mit seinem Speicher), der JIT, die für Exponential wichtigen Extensions
  und `memory_limit`.
- **Engine**: Exponential Velocity (Version, läuft oder gestoppt, laufende und konfigurierte Worker, Ports und TLS,
  die Anzahl der Variablen in seiner envvars-Datei, Demo-Modus aus den Site-envvars der Installation) und die anderen Webserver auf der Maschine (Apache,
  nginx, Caddy, FrankenPHP, PHP-FPM-Master und Pool-Worker).
- **Datenbank**: ihr Typ, die Version des Servers und der Host (nur Host und Port).
- **Website**: die Siteaccesses und der Standard-Siteaccess, die aktiven Extensions, der Demo-Modus, die
  Debug-Ausgabe, der Mail-Transport (nur sein Typ), die Caches, die Hilfe (Seiten, Sprachen, ob ihr Index aktuell
  ist), der letzte Cronjob-Lauf und Composer.
- **Hinweise**: eine Zeile für jeden Fakt, der Aufmerksamkeit braucht ([Zustandshinweise](#zustandshinweise)).
- **Farbbalken**: die 16 Farben des Terminals und ein Balken mit dem Farbverlauf des Themes.

**Wann Sie sie verwenden.** Als Erstes nach der Anmeldung an einem Server, nach einer Installation oder einem Upgrade,
vor und nach einem Deployment und in einer Demo.

```bash
./console exp:expfetch
./console exp:expfetch --plain
./console exp:expfetch --section=php,engine
```

Beispielausgabe (`--plain`, Werte einer Beispielmaschine):

```text
Exponential 6.0.15 @ web.example.org
====================================

[Exponential]
Version: Exponential 6.0.15 (stable, 6.0 line)
Git: main @ 9b945ab6bd

[System]
OS: AlmaLinux 9.8 (Olive Jaguar)
Kernel: Linux 5.14.0-687.29.1.el9_8.x86_64 x86_64
Host: web.example.org
Uptime: 12d 4h
Load: 1.52 1.31 1.20
CPU: AMD EPYC (12 cores)
Memory: 25.9 GiB / 46.8 GiB (55%)
Disk: 1.4 TiB / 2.0 TiB (72%)
Shell: bash
Terminal: xterm-256color (120x40)

[PHP]
PHP: 8.5.11 (cli)
OPcache: on, also for the CLI, 256 MiB
JIT: off
Extensions: intl mbstring curl gd xsl sodium apcu mysqli pdo_mysql sqlite3 pdo_sqlite imagick pcntl posix zip
memory_limit: 512M

[Engine]
Velocity: v0.0.4.54, running, up 3h 12m
Workers: 52 live / 48 configured
Ports: 8088 (HTTP), 8080 (HTTPS, TLS)
Envvars: 6 variables
Demo mode: off (Velocity)
Apache: running (13 processes)
PHP-FPM: 1 master (8.5), 16 pool workers

[Database]
Database: MySQL/MariaDB 10.11.14-MariaDB
Host: localhost

[Site]
Siteaccesses: 4, default site
Extensions: 38 active
Demo mode: off
Debug: off
Mail: sendmail
Caches: view, templates on; static off
Help: 7,083 pages, eng-us ger-de
Cronjobs: last run 4 min ago, 14 parts
Composer: 105 packages, all consistent
```

Die laufenden Worker zählen jeden Prozess unterhalb des Velocity-Servers (Worker und ihre Helfer), daher können es
ein paar mehr sein als die konfigurierte Anzahl.

## exp:info: die Karte und eine Bestandsaufnahme

**Was sie zeigt.** Alles, was exp:expfetch zeigt, bei einigen Fakten etwas detaillierter (das Support-Datum von PHP,
die Anzahl der geladenen PHP-Extensions, jeden lokalen Datenträger, die Namen der Velocity-envvars, die Instanzen und
das Worker-Modell, die Namen der FPM-Pools, den Zeichensatz der Datenbank und das Lese-Replikat), dazu diese
Abschnitte:

| Abschnitt | Was er auflistet | Gelesen aus |
|---|---|---|
| Extensions | jede aktive Extension und ihre Version | `extension.xml` `<metadata>`, `ezinfo.php`, dann `composer.json` |
| Siteaccesses | den URL-Modus (MatchOrder) und pro Siteaccess sein Design, seine Sprache und den Host der SiteURL | `site.ini` jedes Siteaccess |
| Einstellungen | die Einstellungen einer Positivliste | `syscard.ini [Inventory] Settings[]` |
| Cronjobs | jeden Cronjob-Teil und seine Skripte | `cronjob.ini` |
| Warteschlange | den expqueue-Dienst, laufende Worker, die letzte Aktion | `expQueueService::statusSummary()` (wenn expqueue aktiv ist) |
| Caches | die Größe des Cache-Verzeichnisses und seiner zehn größten Caches | ein Durchlauf durch `var/<site>/cache`, beim Zeitbudget abgebrochen |
| Hilfe | die Seiten jeder Dokumentationssprache im Vergleich zum Original | der Hilfe-Index |

**Wann Sie sie verwenden.** Für eine Support-Anfrage, ein Audit, die Übergabe einer Website oder um zwei Server zu
vergleichen (die `--json`-Ausgabe lässt sich gut mit diff vergleichen).

```bash
./console exp:info
./console exp:info --json --no-logo > info.json
./console exp:info --section=extensions,settings --plain
```

Beispiel der Abschnitte, die sie hinzufügt (`--plain`):

```text
[Caches]
Cache size: 1.2 GiB in 48,210 files
content: 812 MiB
template-block: 201 MiB
exphelp: 96 MiB
ini: 1.4 MiB

[Help]
Help eng-us: 7,083 / 7,083 pages (100%)
Help ger-de: 785 / 7,083 pages (11%)

[Cronjobs]
default: unpublish, rssimport, indexcontent, hide, internal_drafts_cleanup
frequent: notification, workflow, contentjobs, audit, mailbounces
infrequent: basket_cleanup, linkcheck, mailpreferences

[Extensions]
ezjscore: 1.4.0
ezoe: 6.0.15
explayouts: 1.2.3

[Siteaccesses]
URL mode: uri, host, port
site: design site, eng-US, www.example.org
admin: design admin4, eng-US, www.example.org/admin

[Settings]
SiteSettings/SiteName: Example site
ContentSettings/ViewCaching: enabled
MailSettings/Transport: sendmail
ServerSettings/Workers: 48

[Queue]
Service: exp-queue.service, installed
Workers: 2 alive
```

## exp:about: die Karte und das Projekt

**Was sie zeigt.** Alles, was exp:expfetch zeigt, danach das Projekt: was Exponential ist, die Exponential Foundation,
7x, Exponential Velocity als Teil von 6.0.15, die Lizenz (Code GPL 2.0 oder neuer, Dokumentation GFDL 1.3 oder neuer),
Links (Website, Dokumentation, GitHub, Packagist, Issues, Foren), wie man beiträgt (CONTRIBUTING.md und die CLA), den
Aufruf „Become an Exponential Partner“ und die Credits. Der Text ist `share/syscard/about.txt`, aus der Dokumentation
geschrieben (jeder Block nennt seine Quelle); keine Aussage wird für die Karte erfunden.

**Wann Sie sie verwenden.** In einer Demo, einem Vortrag oder auf einem Screenshot, oder wenn jemand auf einem Server
ein Terminal öffnet und fragt, was dort läuft.

```bash
./console exp:about
./console exp:about --section=about,links --plain
```

Beispiel der Projektabschnitte (`--plain`):

```text
[About]
What Exponential is:
Exponential is a professional PHP application framework with advanced CMS
(content management system) functionality. As a CMS its most notable feature is
its fully customizable and extendable content model. ...

[Project]
Code: GNU General Public License v2.0 (or any later version)
Documentation: GNU Free Documentation License 1.3 (or any later version)
Copyright: Exponential Copyright © 1998-2026 Exponential Foundation and 7x; originally Copyright (C) 1999 - 2014 eZ Systems AS

[Links]
Website: https://exponential.earth
Documentation: https://exponential.doc.exponential.earth/Exponential
GitHub: https://github.com/se7enxweb/exponential
Packagist: https://packagist.org/packages/se7enxweb/exponential
Issues: https://issues.exponential.earth
Forums: https://share.exponential.earth/forums

[Contribute]
...
Become an Exponential Partner:
Join the network of official partners: companies that build, host or sell around
Exponential and support the project in return. Apply at
https://exponential.earth/partner/apply
```

## Optionen

Für alle drei Befehle gelten dieselben Optionen.

| Option | Was sie tut | Beispiel |
|---|---|---|
| `--json` | ein JSON-Dokument: Abschnitte, Hinweise, wie jeder Provider lief; kein Logo, keine Farben | `./console exp:info --json --no-logo` |
| `--plain` | reiner Text: kein Logo, keine Farben, ASCII-Markierungen, ein `Label: value` pro Zeile; `[warn]`/`[error]` nach einem Wert, der Aufmerksamkeit braucht | `./console exp:expfetch --plain` |
| `--no-logo` | die Fakten ohne das Logo | `./console exp:expfetch --no-logo` |
| `--section=<list>` | nur diese Abschnitte, durch Kommas getrennt: `system`, `php`, `exponential`, `engine`, `database`, `site`; exp:info zusätzlich `extensions`, `siteaccesses`, `settings`, `cron`, `queue`, `caches`, `help`; exp:about zusätzlich `about`, `project`, `links`, `contribute`, `credits` | `./console exp:expfetch --section=php,engine` |
| `--no-color` | keine Farben (wie `NO_COLOR=1`) | `./console exp:expfetch --no-color` |
| `--color=<depth>` | `auto` (Standard), `truecolor`, `256`, `16` oder `none`; `NO_COLOR` hat weiterhin Vorrang | `./console exp:expfetch --color=256` |
| `--theme=<name>` | `rainbow` (Standard), `orange`, `mono`, `high-contrast`, `light` oder eines, das eine Extension hinzufügt | `./console exp:expfetch --theme=light` |
| `--width=<columns>` | die Breite (Standard: `COLUMNS`, sonst `stty size`, sonst `[Display] DefaultWidth`) | `./console exp:expfetch --width=100` |
| `--ascii` | ASCII-Grafik und -Markierungen statt Unicode-Blockgrafik | `./console exp:expfetch --ascii` |
| `--no-hints` | den Block Hinweise weglassen | `./console exp:expfetch --no-hints` |
| `--no-bars` | die Farbbalken weglassen | `./console exp:expfetch --no-bars` |
| `--watch[=<seconds>]` | alle N Sekunden (Standard 2) an Ort und Stelle aktualisieren, wie `top`; Strg-C beendet | `./console exp:expfetch --watch=5` |
| `--count=<n>` | mit `--watch`: nach n Aktualisierungen beenden | `./console exp:expfetch --watch=1 --count=10` |
| `--animate` | ein Einblenden des Logos und seines Farbverlaufs, unter einer Sekunde; nur auf einem Farbterminal | `./console exp:expfetch --animate` |
| `--mode=<card>` | `card`, `info` oder `about`: welche Karte, wenn der Befehl als `php bin/php/expfetch.php` ausgeführt wird | `php bin/php/expfetch.php --mode=info` |
| `--list-providers` | die Provider: ID, Klasse, Karten, Priorität, Budget, ein oder aus | `./console exp:expfetch --list-providers` |
| `--list-themes` | die Themes aus `syscard.ini` | `./console exp:expfetch --list-themes` |
| `--completion=<shell>` | ein Vervollständigungsskript für `bash` oder `zsh` | `./console exp:expfetch --completion=bash >> ~/.bashrc` |

Die Standardoptionen jedes Konsolenbefehls funktionieren ebenfalls (`--siteaccess`, `--quiet`, `--allow-root-user`
...). Der Exit-Code ist 0 oder 2 bei einer falschen Option. Generierte Referenz:
[exp:expfetch](../../beispiele/befehle/expfetch.md).

### --watch

`--watch` leert den Bildschirm und zeichnet die Karte alle N Sekunden neu. Nur die Provider, die sich von Sekunde zu
Sekunde ändern, laufen erneut (die Maschine: Last, Arbeitsspeicher, Datenträger; Velocity: seine Worker); die anderen
behalten ihre ersten Fakten, sodass eine Aktualisierung fast nichts kostet. Die Fußzeile zeigt das Intervall und die
Uhrzeit. Strg-C (oder SIGTERM) beendet es und zeigt den Cursor wieder an. Ist die Ausgabe kein Terminal, wird jede
Aktualisierung stattdessen unter der vorherigen ausgegeben.

## Ausgabeformate

**Text** (der Standard) zeichnet Logo und Fakten nebeneinander. **Plain** (`--plain`) ist zum Einfügen in ein Issue,
eine Mail oder ein Log gedacht. **JSON** (`--json`) ist für Programme, Monitoring und Diffs gedacht:

```json
{
    "card": "info",
    "generated": "2026-10-20T08:00:00+00:00",
    "title": "Exponential 6.0.15",
    "host": "web.example.org",
    "sections": {
        "php": [
            { "key": "php.version", "label": "PHP", "value": "8.5.11 (cli)", "severity": "ok", "kind": "row" },
            { "key": "php.opcache", "label": "OPcache", "value": "off", "severity": "warn", "kind": "row",
              "hint": "OPcache is off (opcache.enable=0): every request compiles the PHP code again. Turn it on in php.ini." }
        ]
    },
    "hints": [
        { "key": "php.opcache", "severity": "warn", "hint": "OPcache is off (opcache.enable=0): ..." }
    ],
    "providers": [
        { "id": "php", "class": "expSysCardPhpProvider", "status": "ok", "seconds": 0.004, "priority": 80, "budget": 0.5 }
    ]
}
```

- `card` ist `card`, `info` oder `about`; `generated` ist ISO 8601; `host` ist der Hostname.
- `sections` ordnet einer Abschnitts-ID ihre Fakten in Reihenfolge zu. Ein Fakt hat einen stabilen `key`
  (`<provider>.<name>`), ein `label`, einen `value` (immer eine Zeichenkette), eine `severity` (`ok`, `info`, `warn`,
  `error`), eine `kind` (`row` oder `text` für einen Absatz von exp:about) und, wenn er Aufmerksamkeit braucht, einen
  `hint`.
- `hints` listet die Hinweise auf, die schwersten zuerst.
- `providers` sagt, wie jeder Provider lief: `status` ist `ok`, `slow` (er brauchte länger als sein Budget, ohne
  Isolation), `timeout`, `error` oder `skipped` (das Gesamtbudget war aufgebraucht).

Eine Monitoring-Prüfung kann einen Wert lesen, zum Beispiel
`jq -r '.sections.php[] | select(.key=="php.opcache") | .severity'`, oder die Fehler zählen:
`jq '[.hints[] | select(.severity=="error")] | length'`.

## Layout, Farben und Themes

### Layout nach Breite

Die Karte folgt der Breite des Terminals: das Logo und ein Abstand von drei Spalten neben mindestens 36 Spalten mit
Fakten.

| Breite | Layout |
|---|---|
| 82 Spalten oder mehr | das große „e“ mit dem Schriftzug EXPONENTIAL darunter, dann die Fakten |
| 60 bis 81 | das große „e“ |
| 50 bis 59 | das kleine „e“ |
| 41 bis 49 | das Mini-„e“ |
| 40 oder weniger | gestapelt: das Mini-„e“ oben, die Fakten darunter |

Werte, die nicht passen, werden mit Auslassungspunkten gekürzt; die Absätze von exp:about werden umbrochen. Mit einem
UTF-8-Locale (`LC_ALL`, `LC_CTYPE`, `LANG`) ist das Logo Blockgrafik und die Markierungen sind Punkte und Zeichen;
andernfalls oder mit `--ascii` werden die ASCII-Zeichnungen und `+ - ! x` verwendet.

Bei 120 Spalten, ohne Farben (die ersten Zeilen):

```text
      ▄▄██████▄▄                              Exponential 6.0.15 @ web.example.org
   ▄███▀▀    ▀▀███▄                           ────────────────────────────────────
  ███            ███
 ███▄▄▄▄▄▄▄▄▄▄▄▄▄▄███                         Exponential
 ███▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀                         · Version      Exponential 6.0.15 (stable, 6.0 line)
 ███                                          · Git          main @ 9b945ab6bd
  ███▄           ▄▄▄
   ▀███▄▄    ▄▄███▀                           System
      ▀▀██████▀▀                              · OS           AlmaLinux 9.8 (Olive Jaguar)
                                              · Kernel       Linux 5.14.0-687.29.1.el9_8.x86_64 x86_64
█▀▀ ▀▄▀ █▀█ █▀█ █▄ █ █▀▀ █▄ █ ▀█▀ █ ▄▀█ █     · Host         web.example.org
██▄ █ █ █▀▀ █▄█ █ ▀█ ██▄ █ ▀█  █  █ █▀█ █▄▄   · Uptime       12d 4h
                                              · Load         1.52 1.31 1.20

                                              PHP
                                              ● PHP          8.5.11 (cli)
                                              ▲ OPcache      off
```

Die Markierungen: `●` ok (grün), `·` info (gedimmt), `▲` warn (gelb), `✖` error (rot, fett).

### Farbtiefe

| Tiefe | Wann | Wie eine Farbe gesendet wird |
|---|---|---|
| truecolor | `COLORTERM=truecolor` oder `24bit` | `ESC[38;2;R;G;Bm`, die exakte Farbe |
| 256 | `TERM` enthält `256` | `ESC[38;5;Nm`, die nächstliegende Farbe des 6x6x6-Würfels oder der Graustufenreihe |
| 16 | jedes andere `TERM` | `ESC[30m`-`ESC[37m` und `ESC[90m`-`ESC[97m`, die nächstliegende Grundfarbe (oder `<Role>16` des Themes) |
| none | `NO_COLOR` gesetzt (<https://no-color.org>), `TERM=dumb`, Ausgabe ist kein Terminal, `--no-color`, `--plain`, `--json` | überhaupt keine Escape-Codes |

`--color=` erzwingt eine Tiefe (für einen Screenshot oder ein Terminal, das nicht mitteilt, was es kann), nur dass
`NO_COLOR` immer Vorrang hat. Weitere Codes: `ESC[1m` fett (Beschriftungen, Überschriften, das Logo), `ESC[2m` gedimmt
(wenn ein Theme keine Farbe Dim hat), `ESC[0m` Zurücksetzen nach jedem farbigen Stück. Die Palettenzeilen sind
Hintergrundfarben `ESC[40m`-`ESC[47m` und `ESC[100m`-`ESC[107m`, sodass sie die eigene Palette des Terminals zeigen.
`--watch` leert mit `ESC[H ESC[2J`, blendet den Cursor mit `ESC[?25l` aus und mit `ESC[?25h` wieder ein; `--animate`
zeichnet das Logo Zeile für Zeile, springt mit `ESC[nA` zurück nach oben und leert jede Zeile mit `ESC[K`.

### Themes

| Theme | Für |
|---|---|
| `rainbow` (Standard) | das orangefarbene „e“ mit einem Regenbogenverlauf auf dem Schriftzug und dem Balken |
| `orange` | nur das Exponential-Orange |
| `mono` | keine Farben, nur fett |
| `high-contrast` | kräftige Farben, kein gedimmter Text |
| `light` | ein heller Terminal-Hintergrund: dunklere Farben |

Ein Theme ist ein Block `[Theme-<name>]` in `syscard.ini` mit einer `#RRGGBB`-Farbe pro Rolle (`Logo`, `LogoShade`,
`Accent`, `Label`, `Value`, `Dim`, `Ok`, `Info`, `Warn`, `Error`), `Gradient[]`-Stopps, `GradientDirection=columns|lines`
und `Bold`. Eine leere Farbe ist die eigene des Terminals. `<Role>16=` nennt die Grundfarbe für ein 16-Farben-Terminal
(`yellow`, `bright-yellow` ...), sodass das orangefarbene „e“ dort gelb wird und nicht rot. Das „e“ wird von `Logo`
oben zu `LogoShade` unten schattiert.

## Zustandshinweise

Ein Fakt, der Aufmerksamkeit braucht, wird markiert und fügt dem Block Hinweise eine Zeile hinzu (und `hints` im
JSON):

| Provider | Hinweis, wenn |
|---|---|
| host | die Last mehr als doppelt so hoch ist wie die Anzahl der Kerne; der Arbeitsspeicher zu 90 % belegt ist (Fehler bei 95 %); ein Datenträger zu 90 % voll ist (Fehler bei 97 %) |
| php | der PHP-Branch keinen Sicherheitssupport mehr hat (Fehler) oder ihn innerhalb von `[Php] WarnDays` verliert (Warnung); OPcache nicht geladen oder ausgeschaltet ist; eine empfohlene Extension fehlt (intl, mbstring, curl, gd oder imagick, xsl, sodium, apcu); `memory_limit` unter 256M liegt |
| velocity | Velocity im Demo-Modus läuft (`EXP_DEMO_MODE=1` in seinen Site-envvars, `sites-available/<site>.envvars`); `EXP_DEMO_MODE=1` steht in den gemeinsamen `envvars`, wo die Worker es ablehnen (`refused (shared envvars)`) |
| database | die Datenbank nicht antwortet (Fehler) |
| site | der Demo-Modus eingeschaltet ist; die Debug-Ausgabe für jeden Besucher eingeschaltet ist |
| caches | der View-Cache ausgeschaltet ist |
| help | der Hilfe-Index älter als die Seiten ist oder fehlt |
| cron | kein Cronjob-Lauf gefunden wurde oder seit zwei Tagen keiner |
| composer | `vendor/` fehlt (Fehler); die Pakete zwischen composer.json, composer.lock und vendor/ nicht übereinstimmen (die Bestandsaufnahme von ezupdate) |
| queue | der expqueue-Dienst installiert ist, aber kein Worker läuft |

Die PHP-Support-Daten stehen in `syscard.ini [Php] SecuritySupportEnds[]` (von php.net). Die Composer-Zählung nutzt die
Bestandsaufnahme der Extension ezupdate, die nur lokale Dateien liest; sie wird `[Updates] CacheTime` Sekunden lang
aufbewahrt, und nur dann, wenn der Befehl als Eigentümer des Cache-Verzeichnisses läuft (eine dort von root
geschriebene Datei würde den Benutzer des Webservers daran hindern, die Caches zu leeren).

Die Fakten sind die des Kommandozeilen-PHP und der Maschine. Das PHP des Webservers (PHP-FPM, Velocity) kann eine
eigene php.ini haben: [Setup > Systeminformationen](../../anleitungen/systeminformationen.md) zeigt diese, und
`./console exp:system:info` enthält die vollständigen Zustandsprüfungen.

## Provider, Zeitbudgets und Isolation

Die Fakten stammen von Providern, einer Klasse pro Gruppe von Fakten, aufgeführt in
`syscard.ini [Providers] ProviderClasses[]` in der Reihenfolge, in der ihre Fakten angezeigt werden:

| ID | Klasse | Karte | Abschnitt(e) |
|---|---|---|---|
| exponential | `expSysCardExponentialProvider` | alle | exponential |
| host | `expSysCardHostProvider` | alle | system |
| php | `expSysCardPhpProvider` | alle | php |
| velocity | `expSysCardVelocityProvider` | alle | engine |
| webserver | `expSysCardWebServerProvider` | alle | engine |
| database | `expSysCardDatabaseProvider` | alle | database |
| site | `expSysCardSiteProvider` | alle | site |
| caches | `expSysCardCacheProvider` | alle | site (exp:info: caches) |
| help | `expSysCardHelpProvider` | alle | site (exp:info: help) |
| cron | `expSysCardCronProvider` | alle | site (exp:info: cron) |
| composer | `expSysCardComposerProvider` | alle | site |
| extensions | `expSysCardExtensionsProvider` | info | extensions |
| siteaccess | `expSysCardSiteAccessProvider` | info | siteaccesses |
| settings | `expSysCardSettingsProvider` | info | settings |
| queue | `expSysCardQueueProvider` | info | queue |
| about | `expSysCardAboutProvider` | about | about, project, links, contribute, credits |

Jeder Provider hat eine **Priorität** (je höher, desto früher läuft er) und ein **Budget** (Sekunden). Alle zusammen
erhalten `[Runtime] TotalBudget` Sekunden; ist das aufgebraucht, zeigen die verbleibenden Provider `n/a (time)`, weshalb
die Priorität wichtig ist. Mit `[Runtime] Isolation=enabled` (und den Extensions pcntl und posix) läuft jeder Provider
in einem Kindprozess, der beendet wird, wenn sein Budget aufgebraucht ist: Eine Datenbank, die nicht antwortet, oder
ein hängender Datenträger zeigt `n/a (timeout)` und blockiert die Karte nie. Das Kind endet mit SIGKILL, sodass es nie
eine Verbindung schließt oder eine Datei entfernt, die dem Befehl gehört. Ohne Isolation halten die Provider ihr Budget
selbst ein (ein Programm, das sie ausführen, und ein Verzeichnis, das sie durchlaufen, stoppen bei der Frist), und
einer, der es überschritten hat, wird im JSON als `slow` gemeldet.

`./console exp:expfetch --list-providers` zeigt die Liste, die Karten, die Prioritäten, die Budgets und ob jeder
eingeschaltet ist. Nichts startet eine Shell: Programme werden ohne Shell gestartet, und nichts wird über das Netzwerk
abgefragt.

## Niemals Geheimnisse

Die Karte ist dafür gedacht, an öffentlichen Stellen eingefügt zu werden, daher ist sie von der Konstruktion her
sicher:

- Provider zeigen nur, was sie brauchen: Typ, Version und Host der Datenbank (nie den Benutzer, das Passwort, den
  Datenbanknamen oder einen vollständigen DSN); den Typ des Mail-Transports (nie seinen Server oder seine Anmeldung);
  die Namen der Velocity-envvars (nie ihre Werte); den Host einer SiteURL (nie darin enthaltene Zugangsdaten).
- `exp:info` zeigt nur die Einstellungen aus `[Inventory] Settings[]` und nie eine, deren Name wie ein Geheimnis
  aussieht (password, key, token, secret, salt, user, login, auth, session, dsn ...), selbst wenn die Liste sie nennt.
- Jeder Fakt durchläuft `expSysCardRegistry::safe()`, bevor er angezeigt wird, gleich was ein Provider (auch der einer
  Extension) zurückgibt: Ein Fakt, dessen Name auf `expSysCardRegistry::SECRET_NAME` passt (password, secret, token,
  api key, private key, credential, salt, cookie, session id, dsn ...), wird verworfen, und `mask()` ersetzt das
  Passwort in `scheme://user:password@host`, den Wert von `password=...`, `token: ...`, `api_key=...` sowie
  `Bearer`/`Basic`-Zugangsdaten durch `***` und wendet dann die Maskierung des Systemberichts an (lange,
  schlüsselähnliche Zeichenketten).
- Der Installationspfad wird nicht ausgegeben (der Datenträger der Installation ist mit „Disk“ beschriftet).

## Einstellungen: syscard.ini

| Block | Einstellungen |
|---|---|
| `[Providers]` | `ProviderClasses[]` (Reihenfolge), `Enabled[<id>]=disabled`, `Priority[<id>]=<n>`, `Budget[<id>]=<seconds>` |
| `[Runtime]` | `TotalBudget=6`, `Isolation=enabled` |
| `[Display]` | `DefaultWidth=80`, `Wordmark`, `ColorBars`, `Hints` (enabled/disabled), `Charset=auto|unicode|ascii` |
| `[Sections]` | `Titles[<id>]=<title>` |
| `[Logo]` | `Logo=e`, `Directories[]` (im zuletzt aufgeführten wird zuerst gesucht) |
| `[Theme]` | `Default=rainbow`, `Themes[]` |
| `[Theme-<name>]` | die Farben eines Themes |
| `[Php]` | `SecuritySupportEnds[<branch>]=<date>`, `WarnDays=180` |
| `[Database]` | `AskVersion=enabled`: einmal verbinden und den Server nach seiner Version fragen |
| `[Updates]` | `AskUpdateInventory=enabled`, `CacheTime=3600` |
| `[Inventory]` | `Settings[]=<file>/<block>/<name>`: was exp:info zeigt |
| `[About]` | `TextFile=share/syscard/about.txt` |

Jede Einstellung: [syscard.ini](../../beispiele/ini-einstellungen/syscard/README.md).

## Fakten, ein Theme oder ein Logo aus einer Extension hinzufügen

Eine Extension ergänzt die Karte mit ihrer eigenen `settings/syscard.ini.append.php`; im Kernel ändert sich nichts.

### Ein Provider

Ein Provider implementiert `expSysCardProvider` (`id()`, `modes()`, `priority()`, `budget()`, `watched()`,
`collect( expSysCardContext $context )`); die meisten erweitern `expSysCardBaseProvider`, das Standardwerte und Helfer
mitbringt (`fact()`, `text()`, `size()`, `bytes()`, `duration()`, `ago()`). Lesen Sie alles über den Kontext
(`$context->ini()`, `->file()`, `->env()`, `->glob()`, `->command()`, `->get( $key, $closure )`), damit ein Test dem
Provider mit `expSysCardContext::fake()` erfundene Werte geben kann. Ein vollständiges Beispiel:

```php
<?php
// extension/myshop/classes/myshopsyscardprovider.php

/**
 * The shop on the system card: the open orders, and a hint when the payment gateway is in test mode.
 */
class myShopSysCardProvider extends expSysCardBaseProvider
{
    protected $id = 'myshop';          // Enabled[myshop], Priority[myshop], Budget[myshop]
    protected $section = 'shop';       // a section of its own (or 'site')
    protected $modes = array( expSysCardContext::MODE_CARD );   // MODE_INFO: only on exp:info
    protected $priority = 30;
    protected $budget = 1.0;

    public function collect( expSysCardContext $context )
    {
        $facts = array();
        $open = $context->get( 'myshop.open_orders', function () {
            $rows = eZDB::instance()->arrayQuery( 'SELECT COUNT(*) AS n FROM myshop_order WHERE status = 1' );
            return isset( $rows[0]['n'] ) ? (int)$rows[0]['n'] : null;
        } );
        $facts[] = $this->fact( 'orders', 'Open orders', $open === null ? 'n/a' : $open );

        $test = $context->iniEnabled( 'myshop.ini', 'Gateway', 'TestMode', 'disabled' );
        $facts[] = $this->fact( 'gateway', 'Payments', $test ? 'test mode' : 'live',
                                $test ? expSysCardFact::WARN : expSysCardFact::OK,
                                $test ? 'The payment gateway is in test mode (myshop.ini [Gateway] TestMode): no payment is taken.' : '' );
        return $facts;
    }
}
```

```ini
# extension/myshop/settings/syscard.ini.append.php
[Providers]
ProviderClasses[]=myShopSysCardProvider

[Sections]
Titles[shop]=Shop
```

Erzeugen Sie die Autoloads neu (`php bin/php/ezpgenerateautoloads.php -e`) und prüfen Sie dann mit
`./console exp:expfetch --list-providers` und `./console exp:expfetch --section=shop`. Ein Test mit einem
vorgetäuschten Kontext:

```php
$context = expSysCardContext::fake( array(
    'myshop.open_orders' => 7,
    'ini' => array( 'myshop.ini' => array( 'Gateway' => array( 'TestMode' => 'enabled' ) ) ),
) );
$facts = ( new myShopSysCardProvider() )->collect( $context );
```

Geben Sie nie ein Geheimnis zurück: Die Registry verwirft und maskiert, was sie erkennt, aber ein Provider sollte gar
nicht erst nach einem fragen.

### Ein Theme

```ini
# extension/myshop/settings/syscard.ini.append.php
[Theme]
Themes[]=myshop

[Theme-myshop]
Logo=#2563EB
LogoShade=#1E3A8A
Logo16=bright-blue
Accent=#2563EB
Label=#60A5FA
Dim=#8A8A8A
Gradient[]
Gradient[]=#60A5FA
Gradient[]=#2563EB
GradientDirection=lines
```

`./console exp:expfetch --theme=myshop` oder `[Theme] Default=myshop`, um es zum Standard zu machen.

### Ein Logo

Legen Sie die Zeichnungen in `extension/<ext>/share/syscard/logos/` ab: `<name>-big.txt`, `<name>-small.txt`,
`<name>-mini.txt` (Unicode) und dieselben mit `.ascii.txt`, dazu `wordmark.txt` und `wordmark.ascii.txt`, wenn Sie den
Schriftzug ersetzen.

```ini
[Logo]
Logo=myshop
Directories[]=extension/myshop/share/syscard/logos
```

Im zuletzt aufgeführten Verzeichnis wird zuerst gesucht, und eine dort nicht gefundene Zeichnung wird aus dem nächsten
genommen, sodass ein Logo mit demselben Namen („e“) das des Kernels ersetzt.

## Wie die Aliasse funktionieren

`exp:info` und `exp:about` sind Aliasse von `exp:expfetch`, deklariert mit `@alias` in `bin/php/expfetch.php`. Die
Konsole (`bin/php/console`) übergibt dem Befehl den Namen, mit dem sie aufgerufen wurde, in der Umgebungsvariablen
`EXP_CONSOLE_COMMAND`, und der Befehl wählt daraus die Karte. Direkt mit PHP ausgeführt, hat der Befehl keinen Alias,
den er lesen könnte: Verwenden Sie `--mode`.

```bash
php bin/php/expfetch.php --mode=info --allow-root-user
```

## Shell-Vervollständigung

```bash
./console exp:expfetch --completion=bash >> ~/.bashrc     # or: eval "$(./console exp:expfetch --completion=bash)"
./console exp:expfetch --completion=zsh  >> ~/.zshrc
```

Sie vervollständigt die drei Befehle nach `./console`, ihre Optionen und die Werte von `--theme=`, `--color=`,
`--mode=`, `--section=` und `--completion=`.

## Warum die Interna „Systemkarte“ heißen

Nur der Befehlsname sagt „fetch“, nach den Werkzeugen, denen er nachempfunden ist. Alles im Inneren ist die
**Systemkarte** (`expSysCard*`, `kernel/classes/syscard/`, `syscard.ini`, `share/syscard/`), weil „fetch“ in Exponential
schon etwas bedeutet: die Template-Funktionen `fetch()`, die Fetch-Definitionen von Modulen, Funktionssammlungen und die
`fetch*()`-Methoden persistenter Objekte. Ein Test hält den neuen Code außerhalb des Befehlsnamens frei von diesem Wort.

## Verwandte Seiten

- [exp:expfetch (generierte Referenz)](../../beispiele/befehle/expfetch.md)
- [syscard.ini (generierte Referenz)](../../beispiele/ini-einstellungen/syscard/README.md)
- [Zustandsprüfungen und Verfügbarkeit](../../hilfe/hosting/betrieb/zustandspruefungen-und-verfuegbarkeit.md)
- [Systeminformationen](../../anleitungen/systeminformationen.md)
- [Das Hilfesystem](hilfesystem.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- [Freie Dokumentation](../../hilfe/freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
