---
title: FrankenPHP für PHP-Entwickler
audience: php-developer, developer
category: php-development
order: 450
summary: Was ein PHP-Entwickler wissen muss, wenn Exponential über exp:velocity von FrankenPHP ausgeliefert wird - der klassische Modus mit einem sauberen Zustand pro Anfrage, warum der Worker-Modus nicht angeboten wird, das eingebettete PHP und seine ini-Einstellungen, der MySQL-Socket, Logs und wie es sich zum Testen von Code mit der Engine qbix und PHP-FPM vergleicht.
keywords: FrankenPHP, klassischer Modus, Worker-Modus, eingebettetes PHP, MySQL-Socket, Engines, Testen, Caddy, classic mode, worker mode, exp:velocity, engine frankenphp, FrankenPHPSettings, IniOptions, PHPIniFile, embedded PHP, MySQL socket, Caddyfile, expVelocityFrankenPHP, engines, testing
type: help
---
# FrankenPHP für PHP-Entwickler

Diese Seite richtet sich an PHP-Entwickler, deren Code möglicherweise von FrankenPHP ausgeliefert wird, dem Webserver
Caddy mit in eine Binärdatei kompiliertem PHP, den `exp:velocity` steuern kann (`--engine=frankenphp`). FrankenPHP ist
die produktionsreife Alternative zur empfohlenen Engine `qbix`. Für PHP-Code verhält es sich wie PHP-FPM. Der
vollständige Betriebsleitfaden ist [FrankenPHP als Web-Engine von Exponential](../../bc/6.0/frankenphp.md).

## Inhalt

1. [Klassischer Modus: ein sauberer Zustand pro Anfrage](#klassischer-modus-ein-sauberer-zustand-pro-anfrage)
2. [Die drei Engines im Vergleich](#die-drei-engines-im-vergleich)
3. [Das eingebettete PHP](#das-eingebettete-php)
4. [Für einen Test starten](#für-einen-test-starten)
5. [Was Sie in Ihrem Code prüfen sollten](#was-sie-in-ihrem-code-prüfen-sollten)

## Klassischer Modus: ein sauberer Zustand pro Anfrage

FrankenPHP führt PHP im **klassischen Modus** aus: ein Pool von PHP-Threads in einem Prozess, wobei jede Anfrage von
einem sauberen Zustand ausgeht, genau wie unter PHP-FPM. Der Worker-Modus, in dem ein PHP-Skript über Anfragen hinweg
lebendig bleibt, wird **bewusst nicht angeboten**, weil der Kernel Anfragezustand in globalen Variablen hält und eine
Anfrage in die nächste durchsickern lassen würde. Für Ihren Code bedeutet das: Die Fallen durch statischen Zustand bei
persistenten Workern gelten unter FrankenPHP nicht ([Fallen durch statischen Zustand](fallen-durch-statischen-zustand.md)), und Sie
zahlen die Bootstrap-Kosten bei jeder Anfrage, wie unter PHP-FPM.

## Die drei Engines im Vergleich

| | `qbix` (Velocity) | `frankenphp` | `php` (eingebauter Server) |
|---|---|---|---|
| Rolle | empfohlen, jede Stufe | produktionsreife Alternative | nur Entwicklung |
| PHP-Zustand zwischen Anfragen | in persistenten Workern erhalten, von Kernel und Engine zurückgesetzt | keiner (klassischer Modus) | keiner |
| Antwort-Cache im Server | ja (`[CacheSettings]`) | noch nicht; `[CacheSettings]` wird ignoriert, und `status` sagt das | nein |
| HTTPS und HTTP/2 | ja | ja, standardmäßig selbstsigniert | einfaches HTTP |
| PHP-Binärdatei | das Kommandozeilen-PHP der Maschine | in die Binärdatei eingebettet | das Kommandozeilen-PHP der Maschine |

Code auf `frankenphp` oder `php` neben `qbix` zu testen trennt Fehler in Ihrem Code von Zustand, der zwischen Anfragen
erhalten bleibt ([Velocity-Engines](../../bc/6.0/velocity-engines.md)).

## Das eingebettete PHP

FrankenPHP bringt sein eigenes PHP und seine eigenen Erweiterungen mit; es liest nicht die `php.ini` der Maschine.
Einstellungen kommen aus `settings/velocity.ini`:

| Einstellung | Wirkung |
|---|---|
| `[PHPSettings] IniOptions[]` | gemeinsam mit der Engine qbix, zuerst angewendet |
| `[FrankenPHPSettings] IniOptions[]` | danach angewendet; mitgeliefert `display_errors=Off`, `log_errors=On`, `memory_limit=512M`, `opcache.enable=1`, `post_max_size=64M`, `upload_max_filesize=32M` |
| `[FrankenPHPSettings] PHPIniFile` | eine eigene `php.ini` für das eingebettete PHP |

Zwei Folgen für Entwickler:

- **MySQL braucht einen ausdrücklichen Socket.** Ohne `php.ini` der Maschine gibt es keinen Standard-MySQL-Socket;
  setzen Sie ihn in `IniOptions[]`, sonst schlagen Datenbankverbindungen fehl (§6.1 des Leitfadens).
- **Die Erweiterungen sind die der Binärdatei.** Eine PHP-Erweiterung, die Ihr Code braucht, muss im Build von
  FrankenPHP enthalten sein, nicht nur im PHP der Maschine; lesen Sie den Leitfaden, bevor Sie sich auf eine verlassen.

## Für einen Test starten

```bash
php bin/php/console exp:velocity install --engine=frankenphp --check
php bin/php/console exp:velocity start --engine=frankenphp
php bin/php/console exp:velocity status --all
php bin/php/console exp:velocity stop --engine=frankenphp
```

`install` lädt ein festgelegtes Release herunter und prüft seine SHA-256-Prüfsumme, bevor es überhaupt ausgeführt wird.
Die Engine lauscht auf eigenen Ports (standardmäßig `[FrankenPHPSettings] Port=8089`, `HTTPSPort=8444`), sodass sie neben
der Engine qbix läuft. Das Caddyfile wird bei jedem Start aus `velocity.ini` erzeugt
(`var/vc/frankenphp/run/Caddyfile`); Logs liegen unter `var/vc/frankenphp/log`. Die Engine-Klasse ist
`expVelocityFrankenPHP` (`kernel/classes/expvelocityfrankenphp.php`).

## Was Sie in Ihrem Code prüfen sollten

- **Kein Verlass auf Worker-Zustand.** Code, der nur funktioniert, weil eine statische Eigenschaft überlebt hat (ein in
  einer früheren Anfrage vorgewärmter Cache), ist hier langsamer oder falsch; Code, der hier korrekt und unter `qbix`
  falsch ist, hält Zustand, den er nicht halten sollte.
- **Pfade und das Wurzelverzeichnis.** Wie überall: `EXP_ROOT_DIR` oder `eZSys::rootDir()`, keine aus einer
  Kernel-Datei abgeleiteten Pfade ([Das Engine-Archiv (phar) für PHP-Entwickler](das-engine-phar-fuer-php-entwickler.md)).
- **Fehler.** `display_errors` ist in dieser Engine standardmäßig aus; lesen Sie PHP-Fehler in den Logs und in der
  Debug-Leiste.

## Verwandte Seiten

- [FrankenPHP als Web-Engine von Exponential](../../bc/6.0/frankenphp.md)
- [Persistente Velocity-Worker für PHP-Entwickler](persistente-velocity-worker-fuer-php-entwickler.md)
- [Befehle für Webserver und Solr](../../funktionen/6.0/befehle-fuer-webserver-und-solr.md)
- [Freie Dokumentation](../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
