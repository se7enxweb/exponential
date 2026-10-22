---
title: "Server und Deployment: Apache und PHP-FPM, Velocity, FrankenPHP"
audience: web-framework-developer, developer, hosting
category: web-frameworks
order: 380
summary: Wie Exponential ausgeliefert und bereitgestellt wird, für Entwickler, die PHP-FPM hinter nginx, Laravel Octane, RoadRunner, den Worker-Modus von FrankenPHP, Gunicorn oder Puma kennen - die drei Arten der Auslieferung, die Regeln für persistente Worker, das Deployment mit einem Befehl über exp:velocity deploy, was per Git, Composer, Einstellungen und Upgrade-Skripten übertragen wird, und der Ablauf eines Releases.
keywords: Deployment, Bereitstellung, deploy, Apache, nginx, PHP-FPM, Velocity, qbix, FrankenPHP, persistente Worker, persistent workers, Octane, RoadRunner, exp:velocity deploy, engine.phar, OPcache, Wartungsmodus, maintenance mode, Release, Rollback, EXP_ENV, Web-Framework-Entwickler, web framework developer
type: help
---
# Server und Deployment: Apache und PHP-FPM, Velocity, FrankenPHP

Diese Seite richtet sich an Entwickler, die Framework-Anwendungen auf PHP-FPM, auf einem Anwendungsserver (Laravel
Octane, RoadRunner, Worker-Modus von FrankenPHP) oder auf Gunicorn, Puma oder einem Servlet-Container bereitstellen.
Exponential läuft unter drei Servern, die alle über `./console exp:velocity` und `settings/velocity.ini` gesteuert
werden, und bringt einen einzigen Befehl mit, der jeden Schritt ausführt, den eine Codeänderung erfordert ([Server: Velocity, PHP-FPM und FrankenPHP](../../standards/architektur/kernel-architektur.md#server-velocity-php-fpm-und-frankenphp)).

## Inhalt

1. [Drei Arten der Auslieferung](#drei-arten-der-auslieferung)
2. [Regeln für persistente Worker](#regeln-für-persistente-worker)
3. [Eine Änderung bereitstellen](#eine-änderung-bereitstellen)
4. [Was wie übertragen wird](#was-wie-übertragen-wird)
5. [Ein Release, Schritt für Schritt](#ein-release-schritt-für-schritt)
6. [Im Vergleich mit anderen Stacks](#im-vergleich-mit-anderen-stacks)

## Drei Arten der Auslieferung

| Server | Wert für Engine | Prozessmodell |
|---|---|---|
| Apache oder nginx mit PHP-FPM | (keine Engine) | ein Request pro PHP-Prozesslauf, nichts bleibt erhalten |
| Exponential Velocity | `qbix` (empfohlen) | ein Elternprozess lädt die Anwendung einmal und forkt persistente Worker, die viele Requests bedienen und nach jedem zurückgesetzt werden; ein Response-Cache beantwortet wiederholte öffentliche Requests |
| FrankenPHP | `frankenphp` | Caddy mit eingebautem PHP, eine einzige Binärdatei |

Seit 6.0.15 ist Velocity Teil von Exponential: `composer.json` verlangt `se7enxweb/exponential-velocity`, daher ist es
in jeder Installation vorhanden. `velocity.ini [ServerSettings] Engine` wird mit `php` ausgeliefert (dem eingebauten
Server von PHP, für die Entwicklung); jede echte Site setzt `qbix` ([Velocity-Engines](../../bc/6.0/velocity-engines.md),
[Die Site ausliefern](../../installation/08-die-website-ausliefern.md), [Deployment-Leitfaden](../../anleitungen/bereitstellung.md)).

## Regeln für persistente Worker

- Kein Zustand pro Request in statischen Eigenschaften oder globalen Variablen ohne Zurücksetzen.
- Kein `exit` zur Ablaufsteuerung außer `eZExecution::cleanExit()`, und dessen Exception niemals abfangen.
- Eingaben über `eZHTTPTool` lesen, das der Kernel pro Request vorbereitet, nicht direkt über `$_POST`.
- Registrierungen von Event-Listenern müssen idempotent sein.
- Eine geänderte Klasse oder Einstellung erreicht die Worker erst nach einem Neustart; eine neue Kernel-Klasse
  braucht `--kernel`.

Das sind dieselben Regeln, die Octane oder RoadRunner vorgeben; Code, der sie einhält, verhält sich unter PHP-FPM genauso
([Velocity: der Server mit persistenten Workern](../../funktionen/6.0/velocity-server-mit-dauerhaften-workern.md)).

## Eine Änderung bereitstellen

```bash
./console exp:velocity deploy --dry-run --allow-root-user   # what it would do
./console exp:velocity deploy --allow-root-user             # the usual case
./console exp:velocity deploy --kernel --allow-root-user    # a kernel class was added or renamed
```

Der Reihe nach: Autoloads der Erweiterungen (`--kernel`: auch des Kernels), INI- und Template-Caches, das
Engine-Archiv, wenn sich Kernel-Dateien geändert haben (jede Datei wird zuerst geparst, sodass eine fehlerhafte Datei
das Deployment stoppt, bevor ein Server angefasst wird), ein Reload von PHP-FPM bei Bedarf, der Neustart von Velocity,
dann Inhalts-, HTTP- und Template-Block-Caches und zuletzt der Response-Cache von Velocity, sodass eine von altem Code
gerenderte Seite nie erneut zwischengespeichert wird. Der Befehl gibt für jeden Schritt PASS, FAIL oder SKIP aus und
bricht beim ersten Fehler ab ([Deployment mit Velocity](../ersteller/anwendungsentwickler/mit-velocity-ausliefern.md)). `--packer`
leert zusätzlich gepackte Stylesheets und Skripte.

## Was wie übertragen wird

Aus [Eine Deployment-Pipeline](../ersteller/anwendungsentwickler/deployment-pipeline.md):

| Was | Übertragen durch |
|---|---|
| Kernel und Erweiterungen | Git und Composer (`composer.lock` im Projekt eingecheckt) |
| Ihre Erweiterung | ihren Git-Tag, per Version verlangt |
| Einstellungen | Git, ohne Geheimnisse; Geheimnisse werden pro Maschine gesetzt |
| Inhaltsklassen | Pakete oder Upgrade-Skripte |
| Tabellen | Ihre Installations- und Upgrade-Befehle ([Migrationen](migrationen-und-dba-schemadateien.md)) |
| Inhalte | bleiben in der Produktion; kopieren Sie die Produktion nach Staging, nie umgekehrt |

## Ein Release, Schritt für Schritt

1. Die Tests laufen in der CI durch.
2. Auf Staging bereitstellen, die Upgrade-Schritte ausführen, testen.
3. Die Produktion sichern.
4. Wartungsmodus, wenn das Release Daten ändert (`php bin/php/maintenance.php on --allow-admin`).
5. Den Code aktualisieren (Git oder `composer update <package>`), die Upgrade-Schritte ausführen.
6. `./console exp:velocity deploy` (`--kernel`, wenn sich Kernel-Klassen geändert haben).
7. Wartungsmodus aus; prüfen mit `exp:velocity status`, `exp:cache status`, den Seiten und dem Fehlerprotokoll.

Ein Rollback ist der vorherige Tag plus eine Wiederherstellung der Sicherung, wenn Upgrade-Schritte Daten verändert
haben.

## Im Vergleich mit anderen Stacks

| Stack | Typisches Deployment | Exponential |
|---|---|---|
| Symfony, Laravel auf PHP-FPM | `composer install`, `cache:clear`, Migrationen, FPM-Reload | `composer`, Ihre Upgrade-Befehle, `exp:velocity deploy` |
| Laravel Octane, RoadRunner | `octane:reload` | `exp:velocity deploy` (Neustart von Velocity inbegriffen) |
| Django, Rails | migrieren, Assets sammeln, Gunicorn oder Puma neu starten | derselbe Ablauf |

## Verwandte Seiten

- [Velocity ist Teil von Exponential 6.0.15](../velocity-ist-teil-von-exponential-6-0-15.md)
- [FrankenPHP als Web-Engine von Exponential](../../bc/6.0/frankenphp.md)
- [Anwendungen skalieren](../ersteller/anwendungsentwickler/anwendungen-skalieren.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
