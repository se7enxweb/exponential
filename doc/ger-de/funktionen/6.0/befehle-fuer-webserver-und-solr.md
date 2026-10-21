# Befehle zur Serversteuerung: exp:webserver, exp:frankenphp und exp:solr

Diese Seite ist für Administratoren, die die Server starten, stoppen und konfigurieren, die eine Installation von
Exponential neben PHP betreibt. Drei Konsolenbefehle, hinzugefügt am 25. und 26. September 2026, steuern sie: den
Webserver (jede der drei Velocity-Engines) und einen optionalen Suchserver Solr. Sie teilen sich einen Controller mit
[Velocity](velocity-server-mit-dauerhaften-workern.md), sodass sich jedes Verb gleich verhält, gleich, welchen Namen Sie
eingeben.

Führen Sie sie als `./console exp:<name> <verb>` aus. Als `root` fügen Sie `--allow-root-user` hinzu.
`./console exp:<name> --help --allow-root-user` gibt die vollständige Hilfe aus, aus der diese Seite stammt.

## exp:webserver: ein Befehl für die Engine, die Sie betreiben

`exp:webserver` ist das von der Engine unabhängige Frontend. Verwenden Sie es, wenn es Ihnen gleich ist, ob die Engine
der eingebaute Server von PHP (Entwicklung), FrankenPHP (Produktion) oder der mitgelieferte Qbix-Server ist.

| Verb | Was es tut |
|---|---|
| `status` (Standard) | Was jede Engine gerade tut. |
| `start` | Bei der ersten Verwendung konfigurieren, dann die Engine ausführen. |
| `stop`, `restart` | Um Anhalten bitten und warten; anhalten, dann starten. |
| `graceful` | Neu ausführen, ohne den lauschenden Socket aufzugeben. |
| `kill` | Anhalten, ohne zu fragen, für einen festhängenden Worker. |
| `configure` | Engine, Port, Document Root und HTTPS-Port ermitteln und in `settings/override/velocity.ini.append.php` speichern, ohne etwas zu starten; `--dry-run` zeigt nur den Plan. |
| `command` | Die Befehlszeile ausgeben, die es ausführen würde, und enden. |
| `config` | Die Einstellungen lesen und schreiben, mit denen es läuft. |
| `cache` | Cache leeren: jede zwischengespeicherte Seite bei ihrer nächsten Anfrage neu ausgeben. |
| `layout` | Der Konfigurationsbaum und jede Datei, die der Server verwendet. |
| `ctl` | Die eigene Steuerung der Engine mit dem Baum dieser Installation. |
| `install` | Das Programm der Engine bereitstellen. |

`--engine=<name>` und `--all` erreichen für start, stop, restart, graceful, kill und status die anderen Engines. Jede
Engine hat ihren eigenen Port, ihre eigene PID-Datei und eigene Protokolle, sodass sie nebeneinander laufen können.

**Einmal konfigurieren, dann direkt ausführen.** Der erste `start` ohne gespeicherte Engine ermittelt die geltende
Engine, den Port, das Document Root und den HTTPS-Port und schreibt sie in die Überschreibung der Installation; jeder
spätere Start läuft direkt daraus. Um das zu ändern, ohne die INI-Datei zu bearbeiten:

```bash
./console exp:webserver configure --engine=frankenphp --port=8089 --dry-run --allow-root-user   # plan only
./console exp:webserver configure --engine=frankenphp --port=8089 --allow-root-user             # persist, start nothing
./console exp:webserver start --reconfigure --engine=php --port=8087 --allow-root-user          # persist, then run
```

`configure` schreibt nur diese Schlüssel neu und lässt Zertifikate und PHP-INI-Optionen unberührt.

## exp:frankenphp: derselbe Befehl, festgelegt auf FrankenPHP

`./console exp:frankenphp <verb>` ist `exp:velocity <verb> --engine=frankenphp`. Sein vorgesehener Alias `exp:fp` ist im
Docblock des Befehls deklariert, wird von der Konsole auf diesem Stand aber nicht aufgelöst (geprüft am 2. Oktober 2026:
„Command exp:fp was not found“); geben Sie `exp:frankenphp` ein. Verben: `status`, `start`, `stop`, `restart`,
`graceful`, `kill`, `command`, `config`, `cache`, `ctl` (`caddyfile`, `validate`, `adapt`, `version`, `list-modules`)
und `install` (das festgelegte Release von FrankenPHP herunterladen und seinen SHA-256 prüfen; `--force`,
`--from=<file>`, `--check`, `--trust-github-digest`).

```bash
./console exp:frankenphp install --check --allow-root-user   # is the pinned binary in place?
./console exp:frankenphp status --allow-root-user
./console exp:frankenphp ctl validate --allow-root-user       # validate the generated Caddyfile (prints "Valid configuration")
```

Adresse und Ports kommen aus `[FrankenPHPSettings]` in `settings/velocity.ini` (`Port=8089` für einfaches HTTP,
`HTTPSPort=8444` für TLS, `HTTPS=enabled`). HTTPS ist standardmäßig mit einem selbstsignierten Zertifikat eingeschaltet,
sofern `[HTTPSSettings]` keines nennt (`Certificate` und `Key`, beide oder keines); `--https` und `--no-https` erzwingen
es für einen einzelnen Start. Lässt sich das Zertifikat nicht erstellen (keine Erweiterung `openssl`, ein nicht
beschreibbares Verzeichnis), startet der Standard einfaches HTTP und sagt, warum; `--https` oder ein genanntes
Zertifikat, das nicht existiert, lehnt den Start ab.

Zwei Korrekturen derselben Tage sind wichtig, wenn Sie FrankenPHP früher betrieben haben:

- **Die Anmeldung funktioniert.** FrankenPHP gibt jede Zeile `php_ini` der erzeugten Caddyfile als INI-Text an PHP
  weiter, in dem ein Semikolon einen Kommentar beginnt, sodass `session.save_path "0;0660;<dir>"` als `0` ankam, keine
  Sitzungsdatei gefunden wurde und jeder Besucher anonym war. Werte, die der INI-Parser von PHP abschneiden würde, werden
  jetzt in Anführungszeichen gesetzt.
- **Die eingebaute PHP-Engine startet neben HTTPS auf einer anderen Engine.** Sie spricht nur einfaches HTTP, zählte aber
  den gemeinsamen `HTTPSPort`, den eine andere Engine bedient (zum Beispiel Qbix auf 8080), als ihren eigenen und lehnte
  einen sonst freien Start ab.

Die Gründe und die erzeugte Caddyfile stehen in
[FrankenPHP und der eingebaute Server von PHP](../../bc/6.0/frankenphp.md).

## exp:solr: ein optionaler Suchserver

Solr ist optional. Exponential sucht mit seinem eingebauten Index (`php bin/php/updatesearchindex.php`), bis Solr
installiert und eingeschaltet ist; bis dahin sagt jedes Verb das, und `start` endet mit einem Wert ungleich null, sodass
nichts vorgibt, ein Suchserver laufe. (Ein Docblock-Alias `exp:search` existiert, wird von der Konsole auf diesem Stand
aber nicht aufgelöst; verwenden Sie `exp:solr`.)

| Verb | Was es tut |
|---|---|
| `status` | Ob Solr konfiguriert ist und, wenn ja, ob es antwortet. |
| `start`, `stop`, `restart` | Das eigene Starten und Anhalten von Solr für eine lokale Instanz ausführen (`BinaryPath`); mit einer entfernten `Url` melden sie, dass es anderswo verwaltet wird. |
| `ping` | Solr fragen, ob es lebt. |

`settings/solr.ini` (Überschreibung in `settings/override/solr.ini.append.php`), Block `[SolrSettings]`,
Geltungsbereich: die Installation:

| Schlüssel | Standard | Bedeutung |
|---|---|---|
| `Enabled` | `false` | Solr übernimmt nur bei `true`. |
| `Url` | leer | Ein laufendes Solr, mit dem gesprochen wird; ist sie gesetzt, gilt Solr als entfernt, und `start`/`stop` tun nichts. |
| `BinaryPath` | leer | Das Steuerprogramm von Solr, für eine lokale Instanz (verwendet, wenn `Url` leer ist). |
| `Host`, `Port` | `127.0.0.1`, `8983` | Wo Solr lauscht. |
| `DataDir` | leer | Das Datenverzeichnis von Solr. |
| `RunDir`, `LogDir` | `var/vc/solr/run`, `var/vc/solr/log` | PID- und Protokolldateien, im Layout von Velocity gehalten. |
| `StartTimeout` | `60` | Sekunden, die `start` wartet, bis Solr antwortet. |

```bash
./console exp:solr status --allow-root-user   # says how to enable it until [SolrSettings] Enabled=true
```

## Verwandte Seiten

- [Velocity: Exponential in einem Webserver mit dauerhaften Workern betreiben](velocity-server-mit-dauerhaften-workern.md)
- [FrankenPHP und der eingebaute Server von PHP](../../bc/6.0/frankenphp.md)
- [Velocity-Engines](../../bc/6.0/velocity-engines.md)
- [Verhaltensänderungen, 16. bis 30. September 2026](../../bc/6.0/verhaltensaenderungen-2026-09b.md)
- [Chronik, 16. bis 30. September 2026](../../geschichte/2026/2026-09b.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
