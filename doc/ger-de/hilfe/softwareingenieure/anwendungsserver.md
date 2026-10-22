---
title: Anwendungsserver
audience: software-engineer, admin
category: software-engineering
order: 320
summary: Die drei Wege, auf denen Exponential ausgeliefert wird - ein Webserver mit PHP-FPM, Exponential Velocity mit persistenten Workern und einem Antwort-Cache und FrankenPHP im klassischen Modus - verglichen nach Prozessmodell, Zustand zwischen Anfragen, Caching, TLS und HTTP/2, Bereitstellung und den technischen Folgen, mit den Befehlen, die sie steuern.
keywords: Anwendungsserver, Webserver, PHP-FPM, Apache, nginx, Velocity, qbix, FrankenPHP, Caddy, Prozessmodell, persistente Worker, Engine, velocity.ini, exp:velocity, Softwareingenieur, application server, web server, process model, persistent workers, engine
type: help
---
# Anwendungsserver

Diese Seite richtet sich an Ingenieure, die wählen, wie Exponential ausgeliefert wird, oder wissen müssen, welcher
Laufzeitumgebung ihr Code begegnet. Seit 6.0.15 liefert Exponential einen eigenen Anwendungsserver mit, Exponential
Velocity, und steuert drei Engines mit demselben Befehl und derselben Einstellungsdatei. Maßgeblich ist
[Velocity-Engines](../../bc/6.0/velocity-engines.md).

## Inhalt

1. [Die Möglichkeiten](#die-möglichkeiten)
2. [Verglichen](#verglichen)
3. [Was die Wahl für Code bedeutet](#was-die-wahl-für-code-bedeutet)
4. [Die Engines steuern](#die-engines-steuern)
5. [Die Wahl](#die-wahl)

## Die Möglichkeiten

- **Ein Webserver mit PHP-FPM** (Apache, nginx und die anderen Server, die das README nennt). Jede Anfrage startet PHP aus
  einem sauberen Zustand. Exponential läuft hinter jedem Webserver, der eine URL auf `index.php` umschreiben kann.
- **Exponential Velocity**, Engine `qbix`. Ein in PHP geschriebener Webserver: Der Elternprozess lädt die Anwendung einmal
  und forkt persistente Worker, die viele Anfragen bedienen und nach jeder zurückgesetzt werden; ein Antwort-Cache kann
  wiederholte öffentliche Anfragen beantworten, bevor ein Worker eingesetzt wird. Ab 6.0.15 verlangt ihn `composer.json`
  (`se7enxweb/exponential-velocity`), jede Installation hat ihn also. Das Projekt nennt ihn die empfohlene Engine für
  jede Phase ([Velocity ist Teil von Exponential 6.0.15](../velocity-ist-teil-von-exponential-6-0-15.md)).
- **FrankenPHP**, Engine `frankenphp`. Caddy mit eingebautem PHP, eine Binärdatei, von `exp:velocity install`
  heruntergeladen und gegen eine festgelegte SHA-256-Prüfsumme geprüft. PHP läuft im klassischen Modus (ein sauberer
  Zustand je Anfrage); der Worker-Modus wird absichtlich nicht angeboten ([FrankenPHP](../../bc/6.0/frankenphp.md)).
- **Der eingebaute Server von PHP**, Engine `php`: der mitgelieferte Standard in `velocity.ini`, weil er keine
  Installation braucht. Nur für die Entwicklung.

## Verglichen

| | PHP-FPM | Velocity (`qbix`) | FrankenPHP | `php -S` |
|---|---|---|---|---|
| Rolle im Projekt | traditionell, voll unterstützt | empfohlen, jede Phase | produktionsreife Alternative | nur Entwicklung |
| Zustand zwischen Anfragen | keiner | ein persistenter Worker, nach jeder Anfrage zurückgesetzt | keiner (klassischer Modus) | keiner |
| Antwort-Cache | nein | ja (`[CacheSettings]`) | noch nicht | nein |
| TLS und HTTP/2 | die Ihres Webservers | ja | ja | nein |
| Sanftes Neuladen | `systemctl reload` des FPM-Dienstes | erneutes exec, Socket bleibt | Neuladen der Konfiguration | Neustart |
| Eine Klassenänderung ist live nach | Neuladen von FPM (OPcache) | `exp:velocity restart` oder `deploy` | seinem Neuladen | Neustart |

## Was die Wahl für Code bedeutet

Derselbe Code läuft unter allen, und die Regel des Kernels ist, dass jede Änderung unter PHP-FPM, Velocity und
FrankenPHP weiter funktionieren **muss**. Was das persistente Modell hinzufügt:

- statische Eigenschaften, Globals und Registrierungen überdauern von einer Anfrage zur nächsten, sofern sie nicht
  zurückgesetzt werden ([Nebenläufigkeit, Statics und langlebige Prozesse](nebenlaeufigkeit-statics-und-langlebige-prozesse.md));
- `exit` und `die` beenden über eine Kompatibilitätsschicht die Anfrage, nicht den Worker; Kernel-Code verwendet
  `eZExecution::cleanExit()`;
- ein Worker, der vor einer Bereitstellung gestartet wurde, kennt eine neue Klasse erst nach seinem Neustart.

## Die Engines steuern

```bash
php bin/php/console exp:velocity status                          # every engine: role, state, URL
php bin/php/console exp:velocity start                           # the default engine
php bin/php/console exp:velocity graceful                        # reload workers one at a time
php bin/php/console exp:velocity deploy --dry-run --allow-root-user
```

`settings/velocity.ini [ServerSettings] Engine` wählt die Standard-Engine (`php` mitgeliefert, `qbix` empfohlen,
`frankenphp`). Die Verben sind für jede Engine dieselben: `start`, `stop`, `graceful`, `restart`, `kill`, `status`,
`deploy`, `command`, `config`, `install`.

## Die Wahl

| Situation | Wahl |
|---|---|
| Eine neue Website, jede Phase | Velocity, wie das Projekt empfiehlt |
| Ein Host, der nur PHP-FPM anbietet | PHP-FPM hinter seinem Webserver; alles funktioniert, ohne den Antwort-Cache |
| Eine geprüfte Binärdatei mit TLS und HTTP/2, ohne persistenten Zustand | FrankenPHP |
| Code, dem Sie in einem langlebigen Prozess noch nicht vertrauen | PHP-FPM oder FrankenPHP, bis er geprüft ist, oder der Modus `forkPerRequest` von Velocity |

Messen Sie auf Ihren eigenen Seiten, bevor Sie entscheiden ([Leistungsmessung und Benchmarks](leistungsmessung-und-benchmarks.md)).

## Verwandte Seiten

- [Persistente Worker von Velocity](persistente-worker-von-velocity.md)
- [Velocity: Exponential in einem Webserver mit persistenten Workern betreiben](../../funktionen/6.0/velocity-server-mit-dauerhaften-workern.md)
- [Bereitstellen mit Velocity](../ersteller/anwendungsentwickler/mit-velocity-ausliefern.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
