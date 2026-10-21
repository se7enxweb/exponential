---
title: Valkey und Redis
audience: hosting, admin
category: hosting
order: 90
summary: Redis oder Valkey für Exponential-Websites mit der Erweiterung sevenx_valkey_cache anbieten - was sie aus Dateien herausverlegt (Content-View-Cache, Template-Blöcke, kompilierter INI-Cache), Einrichtung, Einstellungen, einen Server sicher zwischen Kunden teilen und die Grenzen.
keywords: Valkey, Redis, sevenx_valkey_cache, valkeycache.ini, ValkeyCacheSettings, KeyPrefix, InstallationHash, DefaultTTL, LockTTL, Persistent, LocalCache, IniCache, valkey-block, ezvalkeycacheclear.php, Cluster, Cache-Backend, cluster, cache backend
type: help
---
# Valkey und Redis

Diese Seite ist für Hosting-Anbieter, die Redis oder Valkey betreiben und es Exponential-Websites anbieten möchten, für Geschwindigkeit oder
für Websites, die von mehreren Web-Knoten ausgeliefert werden. Sie beantwortet: was die Erweiterung `sevenx_valkey_cache` in Redis oder
Valkey verlegt, wie Sie sie einrichten, wie sich mehrere Kunden einen Server teilen können, ohne die Schlüssel der anderen zu sehen, und
wo die Grenzen liegen. Die Quelle ist [sevenx_valkey_cache: Cache-Backend für Redis und Valkey](../../../../eng-us/features/6.0/extensions/sevenx_valkey_cache.md).

## Inhalt

1. [Was sie speichert](#was-sie-speichert)
2. [Anforderungen](#anforderungen)
3. [Sie einrichten](#sie-einrichten)
4. [Einstellungen](#einstellungen)
5. [Mehrere Kunden auf einem Server](#mehrere-kunden-auf-einem-server)
6. [Leeren](#leeren)
7. [Was sie nicht abdeckt](#was-sie-nicht-abdeckt)
8. [Checkliste](#checkliste)

## Was sie speichert

Die Erweiterung ersetzt den Festplattenspeicher für:

- Template-Blöcke `{valkey-block ...}`;
- den **Content-View-Cache** (Vollansicht von `content/view`);
- den **kompilierten INI-Cache**, was die Zugriffe pro Anfrage auf `var/cache/ini/*.php` entfernt und INI-Caches für Cluster sicher macht.

Einträge laufen über eine TTL ab, haben Sperren für die Erzeugung gegen Cache-Stampedes und umgekehrte Indizes für Knoten und
Teilbäume, sodass das Veröffentlichen von Inhalten genau die Einträge leert, die es betrifft. Der Kernel ruft die Erweiterung nur auf,
wenn ihre Klassen existieren; eine Website ohne sie zahlt also nichts; bei einem Fehlschlag oder wenn ausgeschaltet greift der Kernel wie
bisher auf die Dateien zurück.

## Anforderungen

- Exponential 6.0.15 oder später (sein Kernel ruft die Hooks der Erweiterung auf).
- Die PHP-Erweiterung `redis`, in jedem PHP, das die Website ausliefert (der PHP-FPM-Pool oder das PHP von Velocity), und im PHP der
  Kommandozeile, das Cronjobs und das Leeren von Caches ausführt.
- Ein erreichbarer Server für Redis oder Valkey.

## Sie einrichten

1. Installieren und aktivieren Sie die Erweiterung:

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

3. Erzeugen Sie die Autoloads neu und leeren Sie die Caches (und starten Sie Velocity neu, wenn er die Website ausliefert):

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all --allow-root-user
   ```

4. Prüfen: Fragen Sie eine Seite zweimal an, und listen Sie dann die Schlüssel auf:

   ```bash
   redis-cli --scan --pattern 'sevenx_valkey_cache:*'
   ```

## Einstellungen

Alle in `valkeycache.ini`, Block `[ValkeyCacheSettings]`:

| Schlüssel | Standard | Bedeutung |
|---|---|---|
| `Host`, `Port`, `Database` | `127.0.0.1`, `6379`, `0` | der Server |
| `KeyPrefix` | `sevenx_valkey_cache:` | Präfix jedes Schlüssels; halten Sie es pro Installation eindeutig, wenn sich mehrere Websites einen Server teilen |
| `InstallationHash` | `auto` | ein Hash aus Datenbankname, Name der Website, Wurzel des Dateisystems und Hostname; eine feste Zeichenkette teilt einen Cache zwischen Servern |
| `DefaultTTL` | `3600` | Sekunden, wenn kein Ablauf angegeben ist |
| `LockTTL` | `30` | Sekunden, die eine Sperre für die Erzeugung lebt, wenn der erzeugende Prozess abstürzt |
| `Persistent` | `disabled` | `pconnect`; mit Vorsicht nutzen, wenn mehrere Datenbanken einen php-fpm-Pool teilen |
| `LocalCache` | `enabled` | eine Kopie abgerufener Einträge pro Anfrage |
| `IniCache` | `enabled` | kompilierte INI-Arrays in Redis oder Valkey |

Der Eintrag des Content-View-Caches wird für 3600 Sekunden gespeichert, ein Wert, der in `kernel/private/classes/views/content/view.php`
gesetzt ist, nicht in einer INI-Datei.

## Mehrere Kunden auf einem Server

Der eigene Rat der Erweiterung ist ein eindeutiges `KeyPrefix` pro Installation. Für einen Hoster:

| Maßnahme | Warum |
|---|---|
| ein eindeutiges `KeyPrefix` pro Installation eines Kunden, zum Beispiel `<customer>:` | Schlüssel einer Website kollidieren nie mit denen einer anderen |
| `InstallationHash=auto`, es sei denn, dieselbe Website läuft auf mehreren Web-Knoten | trennt Installationen, die sich versehentlich ein Präfix teilen |
| eine feste Zeichenkette für `InstallationHash` bei einer Website auf mehreren Web-Knoten | jeder Knoten liest dann dieselben Einträge |
| `Persistent=disabled`, wo Pools geteilt werden | die eigene Vorsicht der Einstellung |
| eine Datenbanknummer oder ein getrennter Server pro Kunde, wo Ihre Einrichtung von Redis oder Valkey Zugriffskontrolle erlaubt | ein Präfix trennt Schlüssel; es hindert den Code eines Kunden nicht daran, die eines anderen zu lesen |

> **Warnung**
> Ein geteilter Server für Redis oder Valkey ist geteilter Speicher: Jede Website darauf kann jeden Schlüssel lesen, sofern die eigene
> Zugriffskontrolle des Servers es nicht verhindert. Behandeln Sie ihn in Ihrem Konzept zur Abschottung wie einen geteilten
> Datenbankserver ([Trennung von Mandanten](../sicherheit/trennung-von-mandanten.md)), und lesen Sie in der Dokumentation von Redis oder
> Valkey zu ihrer Zugriffskontrolle nach.

Dimensionieren Sie den Server für die Anzahl der Seiten und Siteaccesses und setzen Sie eine Verdrängungsrichtlinie; ein Ausfall des
Servers wird zu Cache-Fehlschlägen; die Erweiterung entscheidet, wie Fehler gemeldet werden.

## Leeren

- Das Leeren des INI-Caches löscht die INI-Einträge; das Veröffentlichen leert die Einträge für Ansichten und Blöcke der betroffenen
  Knoten und ihrer Vorfahren.
- Die Seite der Erweiterung nennt `bin/php/ezvalkeycacheclear.php` (hinzugefügt am 20. Juli 2026), das alle Caches über dieselben
  Handler leert, die die Administration nutzt, die Schlüssel entfernt, die die Erweiterung geschrieben hat, und die Autoloads der
  Erweiterungen neu erzeugt. Es ist der Rückfall, wenn **Den kompletten Cache leeren** nach dem Entfernen einer Erweiterung oder dem
  Ausschalten eines Cache-Backends nicht aufräumen kann.

## Was sie nicht abdeckt

| Cache | Wo er bleibt |
|---|---|
| Sitzungen | der Session-Handler von PHP (standardmäßig Dateien); der rollenbewusste HTTP-Cache braucht Dateien |
| Der Antwort-Cache von Velocity | APCu und `var/cache/qbix-reverse` |
| Der SQL-Abfrage-Cache | APCu oder Dateien (`querycache.ini Storage`) |
| Kompilierte Templates, Bild-Aliase | Dateien in `var/` |

`settings/file.ini` erwähnt, dass eine Cache-Schicht wie Memcached oder Redis im Cluster-Backend hinzugefügt werden kann; das ist ein
getrenntes Thema des Clusterings ([Clustering und gemeinsamer Speicher](../betrieb/clustering-und-gemeinsamer-speicher.md)).

## Checkliste

- [ ] PHP-Erweiterung `redis` im Web-PHP und im PHP der Kommandozeile.
- [ ] Eindeutiges `KeyPrefix` pro Installation; `InstallationHash` entschieden.
- [ ] Server dimensioniert, Verdrängungsrichtlinie gesetzt, Zugriffskontrolle zwischen Kunden bedacht.
- [ ] Schlüssel erscheinen nach zwei Seitenaufrufen; das Verhalten bei angehaltenem Server für Redis oder Valkey auf Staging getestet.
- [ ] Der Support kennt `ezvalkeycacheclear.php`.

## Verwandte Seiten

- [Cache-Schichten](cache-schichten.md)
- [Mehrere Web-Knoten](../betrieb/mehrere-web-knoten.md)
- [Caches leeren und vorwärmen](caches-leeren-und-vorwaermen.md)
- [sevenx_valkey_cache](../../../../eng-us/features/6.0/extensions/sevenx_valkey_cache.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
