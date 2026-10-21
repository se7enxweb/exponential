# sevenx_valkey_cache: Cache-Backend für Redis und Valkey

Diese Seite ist für Administratoren, die schnellere Seiten möchten oder mehrere Webserver betreiben.
`sevenx_valkey_cache` speichert die Caches von Exponential in **Redis oder Valkey** statt in Dateien. Es ersetzt den
Plattenspeicher für:

- Template-Blöcke `{valkey-block ...}` (ein Cache-Block, der in Redis oder Valkey liegt);
- den **Inhaltsansichts-Cache** (Vollansicht von `content/view`);
- den **kompilierten INI-Cache** (Arrays von `eZINI`). Das entfernt die Zugriffe auf `var/cache/ini/*.php` pro Anfrage und
  macht INI-Caches clustersicher.

Einträge haben einen Ablauf über TTL, Generierungssperren (kein Cache-Ansturm) und umgekehrte Indizes nach Knoten und
Teilbaum, sodass das Veröffentlichen von Inhalten genau die Einträge leert, die es betrifft. Es wurde zuerst am 20. Juli
2026 ausgeliefert (1.0.0); 1.0.1 dokumentierte 6.0.15 als benötigte Plattform.

## Anforderungen

- **Exponential 6.0.15 oder neuer**, dessen Kernel die Hooks der Extension aufruft (siehe „Was der Kernel tut“).
- Die PHP-Erweiterung `redis` (oder igbinary).
- Ein erreichbarer Server mit Redis oder Valkey.

## Einrichten

1. Installieren und aktivieren Sie die Extension:

```bash
composer require se7enxweb/sevenx_valkey_cache
```

```ini
# settings/override/site.ini.append.php
[ExtensionSettings]
ActiveExtensions[]=sevenx_valkey_cache
```

2. Konfigurieren Sie die Verbindung in `settings/override/valkeycache.ini.append.php`:

```ini
[ValkeyCacheSettings]
Host=127.0.0.1
Port=6379
Database=0
KeyPrefix=sevenx_valkey_cache:
InstallationHash=auto
DefaultTTL=3600
LockTTL=30
Persistent=disabled
LocalCache=enabled
IniCache=enabled
```

3. Erzeugen Sie die Autoloads neu und leeren Sie die Caches:

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all --allow-root-user
```

4. Prüfen Sie, dass es funktioniert: Rufen Sie eine Seite zweimal auf und listen Sie dann die Schlüssel auf:

```bash
redis-cli --scan --pattern 'sevenx_valkey_cache:*'
```

   Schlüssel werden aufgeführt. Mit eingeschaltetem Debug von eZINI sagt ein Hinweis
   `Loading Redis cache '...' for file '...'`, wenn INI-Daten vom Server kommen.

## Einstellungen

Alle Schlüssel stehen in `valkeycache.ini`, Block `ValkeyCacheSettings`; setzen Sie sie in `settings/override`.

| Schlüssel | Standard | Bedeutung |
|---|---|---|
| `Host`, `Port`, `Database` | `127.0.0.1`, `6379`, `0` | Server |
| `KeyPrefix` | `sevenx_valkey_cache:` | Präfix jedes Schlüssels. Halten Sie es pro Installation eindeutig, wenn mehrere Websites einen Server teilen |
| `InstallationHash` | `auto` | Ein Hash aus Datenbankname, Website-Name, Dateisystemwurzel und Hostname; verwenden Sie eine feste Zeichenkette, um einen Cache zwischen Servern zu teilen |
| `DefaultTTL` | `3600` | Sekunden, wenn kein Ablauf angegeben ist |
| `LockTTL` | `30` | Sekunden, die eine Generierungssperre lebt, wenn der erzeugende Prozess abstürzt |
| `Persistent` | `disabled` | `pconnect`; mit Vorsicht verwenden, wenn mehrere Datenbanken einen php-fpm-Pool teilen |
| `LocalCache` | `enabled` | Eine Kopie geholter Einträge pro Anfrage, die Hin- und Rückwege spart. Nur zur Fehlersuche bei der Konsistenz des Caches abschalten |
| `IniCache` | `enabled` | Kompilierte INI-Arrays in Redis/Valkey. Braucht Exponential 6.0.15 |

## In Templates verwenden

```
{valkey-block expiry=3600 subtree_expiry=$node.node_id keys=array( 'my-block', $node.node_id )}
    ... expensive template logic ...
{/valkey-block}
```

| Parameter | Bedeutung |
|---|---|
| `expiry` | TTL in Sekunden (Standard 7200) |
| `subtree_expiry` | Knoten-ID als Teilbaum-Abhängigkeit: Jede Änderung darunter leert den Block |
| `node_expiry` | Knoten-ID als Knoten-Abhängigkeit |
| `keys` | Weitere Bestandteile des Schlüssels (Array oder Skalar) |
| `lock` | Generierungssperren gegen Ansturm (Standard `true`) |
| `ignore_content_expiry` | Die globalen Zeitstempel `template-block-cache` und `global-template-block-cache` ignorieren |

Aus Code: `sevenxValkeyCacheBlock::instance()` bietet `put( $key, $content, $ttl )`, `get( $key )`, `purge( $key )` und
`flush()`. Die Klasse `sevenxValkeyCacheHandler` ist als `ContentSettings.StaticCacheHandler` registriert und leert bei
Inhaltsänderungen die nach den betroffenen Knoten-IDs und ihren Vorfahren indizierten Ansichts- und Blockeinträge.

## Alles aus der Shell leeren

`bin/php/ezvalkeycacheclear.php` (hinzugefügt am 20. Juli 2026) leert alle Caches über dieselben PHP-Cache-Handler, die
die Administrationsoberfläche verwendet, leert die Schlüssel in Redis/Valkey, die die Extension geschrieben hat, und
erzeugt die Extension-Autoloads neu, wobei es für jedes Extension-Verzeichnis, das nicht in `ActiveExtensions[]` steht,
`--exclude` übergibt, sodass abgeschaltete, aber vorhandene Extensions aus `var/autoload/ezp_extension.php` und aus
Prüfungen mit `class_exists()` verschwinden. Es ist der Rückfall, wenn **Alle Caches leeren** der
Administrationsoberfläche nach dem Entfernen einer Extension oder dem Abschalten eines Cache-Backends nicht aufräumen
kann.

## Schnellerer INI-Cache (1.0.2, 30. Juli 2026)

Der kompilierte INI-Cache ist **ein Redis-Hash**, mit dem MD5 der Datei als Schlüssel (`HGET`, `HSET`, `HDEL`), statt
eines Schlüssels pro Datei. Das bisherige Design durchsuchte bei jeder Anfrage den ganzen Schlüsselraum (`SCAN` und
`MGET`), was auf einem stark belasteten Server teuer ist; der auf die Anfrage begrenzte Cache im Speicher bleibt und wird
bei Bedarf gefüllt.

## Was der Kernel tut (Hooks)

Der Kernel ruft die Extension nur auf, wenn ihre Klassen existieren (`class_exists()` wird zuerst geprüft), sodass eine
Website ohne die Extension nichts bezahlt. Hooks, hinzugefügt am 20. Juli 2026 (Commits `fa17892025`, `6ef123636c`,
`908994bb03`):

| Wo | Datei | Verhalten mit eingeschalteter Extension |
|---|---|---|
| Inhaltsansichts-Cache | `kernel/private/classes/views/content/view.php` (Einstieg `kernel/content/view.php`) | Der Eintrag der Seite wird mit `sevenxValkeyCacheBlock::get()` gelesen; bei einem Fehltreffer wird die Seite erzeugt und mit `put()` für 3600 Sekunden gespeichert, mit der Knoten-ID als Schlüssel. Die 3600 Sekunden stehen in dieser Datei, nicht in einer INI-Datei. |
| Seiten, die nicht zwischengespeichert werden dürfen | dieselbe Klasse | Ein Ergebnis, das `no_cache` sagt, wird jedes Mal erzeugt und nie gespeichert (Korrektur vom 26. Juli 2026). |
| Kompilierter INI-Cache | `lib/ezutils/classes/ezini.php` | `sevenxValkeyINICache::instance()->isEnabled()` wird zuerst gefragt; kompilierte INI-Daten werden vom Server geladen oder auf ihm gespeichert statt in `var/cache/ini/`. Das Leeren des INI-Caches löscht die Einträge. |

Bei einem Fehltreffer oder wenn abgeschaltet, greift der Kernel genau wie bisher auf den Cluster-Datei-Handler und die
Dateien zurück.

## Grenzen

* Einträge liegen im Speicher: Bemessen Sie den Server nach der Anzahl der Seiten und Siteaccesses, und setzen Sie eine
  Verdrängungsrichtlinie.
* Ein Ausfall des Servers wird zu Cache-Fehltreffern; die Extension entscheidet, wie Fehler gemeldet werden.

## Verwandte Seiten

- [Leeren von Caches durch Umbenennen](../cache-leeren-durch-umbenennen.md)
- [HTTP-Caching](../../../../eng-us/bc/6.0/http-caching.md), [SQL-Abfrage-Cache](../../../../eng-us/bc/6.0/sql-query-cache.md)
- [Changelog 6.0.15](../../../../changelogs/6.0/6.0.15.md), [Juli 2026](../../../../eng-us/history/2026/2026-07.md)
- [Chronik](../../../../eng-us/history/extensions/sevenx_valkey_cache.md) und [Versionshinweise](../../../../changelogs/extensions/sevenx_valkey_cache.md)
- [Änderungsverzeichnis](../../../../eng-us/history/ledger/sevenx_valkey_cache.md)
- [Verhaltensänderungen der Extensions](../../../../eng-us/bc/6.0/extensions-behaviour-changes.md)
- [Monat: 2026-07 (alle Extensions)](../../../../eng-us/history/extensions/months/2026-07.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
