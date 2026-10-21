---
title: OPcache
audience: hosting, admin
category: hosting
order: 30
summary: OPcache für gehostete Exponential-Websites - die Einstellungen unter PHP-FPM, Velocity und FrankenPHP, warum die von Velocity abweichen, die eine Einstellung, die Sie nie verwenden, was JIT gemessen hat, und wie Sie den Cache prüfen und profilieren.
keywords: OPcache, Opcode-Cache, opcache.enable, opcache.enable_cli, validate_timestamps, revalidate_freq, file_update_protection, memory_consumption, max_accelerated_files, interned_strings_buffer, JIT, OPcache-Profil, Velocity, PHP-FPM, FrankenPHP, opcode cache, opcache profile
type: help
---
# OPcache

Diese Seite ist für Systemadministratoren bei Hosting-Anbietern, die PHP für Exponential-Websites konfigurieren. Sie beantwortet: welche
OPcache-Einstellungen Sie unter jedem Server nutzen, warum die Einstellungen von Velocity von denen von PHP-FPM abweichen, welche
Einstellung Exponential kaputtmacht, was das Projekt gemessen hat und wie Sie den Cache auf einer Live-Website prüfen. Die Quelle ist
[Kapitel 10, Abschnitt 10.12](../../../installation/10-nach-der-installation.md#opcache) und
[Velocity und der Opcode-Cache](../../../funktionen/6.0/velocity-opcode-cache-und-profil.md).

## Inhalt

1. [Warum es zählt](#warum-es-zählt)
2. [Unter PHP-FPM](#unter-php-fpm)
3. [Unter Velocity](#unter-velocity)
4. [Unter FrankenPHP](#unter-frankenphp)
5. [JIT](#jit)
6. [Prüfen und profilieren](#prüfen-und-profilieren)
7. [Checkliste](#checkliste)

## Warum es zählt

Die Kommentare in `settings/velocity.ini` halten eine Messung auf einer Inhaltsseite mit vier Workern fest: ohne Opcode-Cache 526,6 ms
(1,9 Anfragen pro Sekunde), mit OPcache 390,8 ms (2,6 Anfragen pro Sekunde), dort als +25,8 % festgehalten. Der Cache allein ist also
der Gewinn. Zeitstempelprüfungen kosten nichts Messbares, und das Tracing-JIT war langsamer als gar kein JIT.

## Unter PHP-FPM

Schalten Sie OPcache in der `php.ini` des Pools ein (Kapitel 10):

```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
opcache.interned_strings_buffer=32
; keep timestamp checks on: Exponential rewrites PHP cache files (INI caches,
; compiled templates, expiry.php) under the same name
opcache.validate_timestamps=1
opcache.revalidate_freq=2
```

> **Warnung**
> Setzen Sie auf einer Exponential-Website nie `opcache.validate_timestamps=0`: Eine nach dem Leeren neu geschriebene Cache-Datei
> würde bis zum Neustart von PHP in ihrer alten Form ausgeliefert.

Mit eingeschalteten Zeitstempelprüfungen kann `exp:velocity deploy` (mit `[DeploySettings] PhpFpmReload=auto`) `revalidate_freq`
Sekunden auf geänderte Dateien warten, statt den FPM-Master neu zu laden, was die anderen Websites dieses Masters schont
([Abstimmung von PHP-FPM](abstimmung-von-php-fpm.md#neu-laden-ohne-andere-websites-aufzuhalten)).

## Unter Velocity

Velocity übergibt dem Serverprozess seine eigenen PHP-Optionen (`settings/velocity.ini [PHPSettings] IniOptions[]`), weil beim PHP
der Kommandozeile OPcache systemweit aus sein kann:

| Option | Mitgeliefert | Warum |
|---|---|---|
| `opcache.enable_cli` | `1` | der Server ist ein Kommandozeilenprozess |
| `opcache.memory_consumption` | `256` | MB |
| `opcache.max_accelerated_files` | `20000` | |
| `opcache.interned_strings_buffer` | `32` | |
| `opcache.revalidate_freq` | `0` | jedes Include prüfen |
| `opcache.file_update_protection` | `0` | siehe unten |
| `apc.shm_size` | `256M` | APCu für den Antwort-Cache und den Abfrage-Cache |

OPcache vergleicht die Zeit einer Datei mit der **Startzeit der Anfrage**, und ein Velocity-Worker ist eine lange Anfrage, die begann,
als der Worker startete:

- mit `revalidate_freq` auf seinem Standard 2 prüfte ein Worker eine Datei nie wieder, sodass eine neu geschriebene Cache-Datei so
  ausgeliefert wurde, wie sie war, als der Worker sie zuerst kompiliert hatte;
- mit `file_update_protection` auf seinem Standard galt jede Datei, die nach dem Start des Workers geschrieben wurde (INI-Caches,
  Override-Caches, kompilierte Templates, alle nach einem Ausrollen oder Leeren neu erzeugt), für das ganze Leben des Workers als „zu
  neu“ und wurde bei jedem Include erneut kompiliert. Gemessen auf dem Demonstrationsserver: Der Cache bediente unter Velocity 2,7 %
  der Includes gegenüber 97 % unter PHP-FPM, und eine gerenderte Seite kostete 644 ms CPU gegenüber 166 ms.

Der Server selbst liest ein Include vollständig und verwirft die zwischengespeicherte Kopie einer Datei, die in den letzten zwei
Sekunden geändert wurde, sodass nichts halb Geschriebenes ausgeliefert wird. Keine Einstellung muss sich ändern; ein Neustart wendet
die mitgelieferten Werte an. Kapitel 10 fasst es zusammen: Jede PHP-Einstellung, die mit „der Zeit der Anfrage“ vergleicht, verhält sich
in einem Velocity-Worker wie eingefroren; vermuten Sie das zuerst, wenn etwas unter PHP-FPM funktioniert und unter Velocity nicht.

## Unter FrankenPHP

Das eingebettete PHP liest keine `php.ini` der Maschine. Es bekommt `opcache.enable=1` aus `[FrankenPHPSettings] IniOptions[]` und die
Abstimmung oben aus `[PHPSettings] IniOptions[]`. `revalidate_freq=0` ist dort harmlos, weil jede Anfrage ihre eigene Zeit der Anfrage
hat ([FrankenPHP](../server/frankenphp.md)).

## JIT

Gemessen in `settings/velocity.ini`: Tracing-JIT 416,7 ms gegenüber 390,8 ms für OPcache allein auf derselben Seite; das Tracing-JIT ist
auf dieser Last also langsamer als kein JIT und fehlt absichtlich. Die Seite zu FrankenPHP berichtet dasselbe. Wenn ein Kunde nach JIT
fragt, messen Sie seine Website mit und ohne, bevor Sie einen Standard eines Tarifs ändern
([Benchmark einer gehosteten Website](benchmark-einer-gehosteten-website.md)).

## Prüfen und profilieren

```bash
grep -n "opcache\." settings/velocity.ini                     # Velocity's shipped options
php bin/php/cache.php opcache status --allow-root-user        # the cache of the process that answers
```

Setup > Caches hat **OPcache zurücksetzen** und **APCu leeren** für den Serverprozess, der die Seite beantwortet hat; der andere Server
behält seine eigenen ([Caches](../../../anleitungen/caches.md#4-velocity)).

Um zu sehen, wie Anfragen den Cache nutzen, schalten Sie das Opcode-Cache-Profil ein. Es ist aus, solange keine Markierungsdatei
existiert; solange sie existiert, hängt jede Anfrage eine JSON-Zeile an `var/tmp/opcache_profile.log` an:

```bash
touch var/tmp/opcache_profile.on
curl -s -o /dev/null https://example.com/
tail -n 3 var/tmp/opcache_profile.log
```

Lesen Sie `included_not_cached` bei der zweiten und den späteren Anfragen eines Prozesses: Nahe 0 ist gesund, viele bedeutet, dass
Dateien bei jedem Include erneut kompiliert werden. `opcache_ini` zeigt die Einstellungen, mit denen der Prozess läuft. Schalten Sie es
wieder aus, indem Sie die Datei `.on` aus `var/tmp/` verschieben; das Log wächst mit jeder Anfrage, solange es an ist.

## Checkliste

- [ ] OPcache in jedem PHP-FPM-Pool eingeschaltet; `validate_timestamps=1`.
- [ ] Die mitgelieferten `[PHPSettings] IniOptions[]` von Velocity unverändert gelassen.
- [ ] JIT nur nach einer Messung geändert.
- [ ] Das Profil nach der Nutzung ausgeschaltet.

## Verwandte Seiten

- [Abstimmung von PHP-FPM](abstimmung-von-php-fpm.md)
- [Velocity-Worker und Pools](../server/velocity-worker-und-pools.md)
- [Cache-Schichten](cache-schichten.md)
- [Velocity und der Opcode-Cache](../../../funktionen/6.0/velocity-opcode-cache-und-profil.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
