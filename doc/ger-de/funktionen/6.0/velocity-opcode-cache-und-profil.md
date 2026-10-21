# Velocity und der Opcode-Cache: warum eine Seite kostet, was sie kostet, und wie Sie es messen

Diese Seite ist für Administratoren, die Exponential Velocity betreiben und wissen wollen, wohin die CPU-Zeit einer
Seite geht. Zwei Änderungen gehören zusammen (hinzugefügt am 02.10.2026):

- Die Worker von Velocity behalten jetzt die Dateien, die der Kernel neu schreibt (INI-Caches, Überschreibungs-Caches,
  kompilierte Templates), im Opcode-Cache.
- Eine Diagnose, die Sie einschalten, schreibt pro Anfrage eine Zeile, die sagt, wie die Anfrage den Opcode-Cache
  genutzt hat, sodass sich zwei Engines mit Zahlen vergleichen lassen.

## Was nicht stimmte

Der Opcode-Cache von PHP behält kein Skript, das weniger als `opcache.file_update_protection` Sekunden vor dem
**Start der Anfrage** geändert wurde. Unter PHP-FPM beginnt jede Anfrage neu, sodass nur Dateien übersprungen werden, die
in den letzten zwei Sekunden geschrieben wurden. Ein Worker von Velocity lebt stundenlang, und sein „Start der Anfrage“
ist der Zeitpunkt, an dem der Worker gestartet wurde. Jede danach geschriebene Datei (die nach einem Deployment oder
dem Leeren eines Caches neu erzeugten Caches) galt für das ganze Leben des Workers als zu neu und wurde bei jedem
Include erneut kompiliert.

Gemessen auf dem Demonstrationsserver: Der Cache bediente unter Velocity 2,7 % der Includes gegenüber 97 % unter
PHP-FPM, und eine ausgegebene Seite kostete 644 ms CPU gegenüber 166 ms.

## Was sich geändert hat

`settings/velocity.ini` übergibt dem Serverprozess `opcache.file_update_protection=0` (neben
`opcache.revalidate_freq=0`). Eine Datei, die gerade noch geschrieben wird, behält der Cache nicht: Der Server selbst
liest ein Include vollständig und verwirft die Kopie des Caches von einer Datei, die in den letzten zwei Sekunden
geändert wurde, sodass nichts halb Geschriebenes ausgeliefert wird.

Keine Ihrer Einstellungen muss sich ändern. Ein Neustart von Velocity wendet es an (Worker behalten, was sie beim
Aufwärmen geladen haben).

| Datei | Block | Schlüssel | Mitgelieferter Wert | Geltungsbereich |
|---|---|---|---|---|
| `settings/velocity.ini` | Serveroptionen | `IniOptions[]=opcache.file_update_protection=0` | `0` | der Serverprozess von Velocity |
| `settings/velocity.ini` | Serveroptionen | `IniOptions[]=opcache.revalidate_freq=0` | `0` (bei jedem Include prüfen) | der Serverprozess von Velocity |
| `settings/velocity.ini` | Serveroptionen | `IniOptions[]=opcache.memory_consumption=256` | 256 (MB) | der Serverprozess von Velocity |

Prüfen:

```bash
grep -n "opcache\." settings/velocity.ini
```

## Mit dem Opcode-Cache-Profil messen

Das Profil ist **aus**, solange die Datei `var/tmp/opcache_profile.on` nicht existiert (der Kernel macht pro Anfrage ein
`file_exists()`, um das herauszufinden). Ist die Datei vorhanden, hängt jede Anfrage eine JSON-Zeile an
`var/tmp/opcache_profile.log` an.

1. Schalten Sie es ein und senden Sie jeder Engine einige Anfragen:

```bash
touch var/tmp/opcache_profile.on
curl -s -o /dev/null https://<site>/
tail -n 3 var/tmp/opcache_profile.log
```

2. Lesen Sie die Zeile der zweiten und späteren Anfragen eines Prozesses. Ein Worker, der seine Dateien im Cache
   behält, zeigt `included_not_cached` nahe 0; einer, der sie erneut kompiliert, zeigt viele. Vergleichen Sie sie mit
   einer PHP-FPM-Zeile derselben Adresse.
3. Schalten Sie es wieder aus, indem Sie die Datei `.on` aus `var/tmp/` verschieben. Das Protokoll wächst mit jeder
   Anfrage, solange es eingeschaltet ist.

| Feld | Bedeutung |
|---|---|
| `sapi`, `pid`, `uri`, `time` | Welcher Engine-Prozess welche Adresse beantwortet hat |
| `hits_this_request`, `misses_this_request` | Die Änderung der Treffer- und Fehlzähler von OPcache während dieser Anfrage |
| `cached_scripts`, `included_files`, `included_not_cached` | Wie viele Skripte OPcache hält, wie viele Dateien die Anfrage eingebunden hat, wie viele davon OPcache nicht hielt (jede wird bei jedem Include erneut kompiliert) |
| `served_most_this_request` | Die zehn Skripte, die OPcache während dieser Anfrage am häufigsten bedient hat |
| `not_cached_sample`, `not_cached_probe` | Die ersten nicht gehaltenen Dateien, mit echtem Pfad, Alter und dem, was OPcache über sie sagt |
| `opcache_ini` | Die OPcache-Einstellungen, mit denen der Prozess läuft (`file_update_protection`, `revalidate_freq`, `validate_timestamps`, `use_cwd`) |

## Grenzen

- Die Zahlen gehören zu dem Prozess, der geantwortet hat; vergleichen Sie mehrere Zeilen, nicht eine.
- Das Profil braucht die Erweiterung `opcache` (`function_exists('opcache_get_status')`).

## Verwandte Seiten

- [Velocity-Server mit dauerhaften Workern](velocity-server-mit-dauerhaften-workern.md)
- [Antwort-Cache von Velocity](../../../eng-us/features/6.0/velocity-response-cache.md)
- [Generator für den statischen Cache](generator-fuer-den-statischen-cache.md)
- [Hinweise zur Aktualisierung der Velocity-Engines](../../../eng-us/bc/6.0/velocity-engines.md)
- [Einstellungen und Programme der Velocity-Engine](../../spezifikationen/6.0/velocity-engine-einstellungen.md)
- [Worker-Pool und Kompatibilitätsschicht von Velocity](../../spezifikationen/6.0/velocity-worker-pool.md)
- [Changelog: Engine Exponential Velocity](../../../changelogs/extensions/exponential-velocity.md)
- [Checkliste zur Aktualisierung vom 1. und 2. Oktober 2026](../../../eng-us/bc/6.0/behaviour-changes-2026-10.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- [Chronik Oktober 2026](../../../eng-us/history/2026/2026-10.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
