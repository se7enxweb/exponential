---
title: Der SQL-Abfrage-Cache
audience: dba
category: database-administration
order: 38
summary: Was der optionale SQL-Abfrage-Cache von Exponential für einen Datenbankadministrator tut - Modi und Speicher, was nie gecacht wird, wie Schreibzugriffe Ergebnisse ungültig machen, warum Schreibzugriffe außerhalb von Exponential ein Leeren verlangen, wie Sie ihn Schritt für Schritt einschalten, was normal ist und worauf Sie achten - auf jeder Engine außer MongoDB.
keywords: Abfrage-Cache, SQL-Abfrage-Cache, querycache.ini, eZDBQueryCache, Mode off request shared, Storage auto apcu file, APCu, ApcuReserve, MaxAge, MaxRows, ExcludeTables, clear-id=querycache, state.ser, Trefferquote, Invalidierung, Schreibzugriffe außerhalb von Exponential, Wiederherstellung, query cache, SQL query cache, hit rate, invalidation, writes outside Exponential, restore
type: help
---
# Der SQL-Abfrage-Cache

Diese Seite richtet sich an Datenbankadministratoren, die wissen möchten, ob der SQL-Abfrage-Cache ihrer Website hilft und was
er für Schreibzugriffe bedeutet, die sie selbst vornehmen. Der Cache behält die Zeilen, die ein `SELECT` zurückgegeben hat, und
beantwortet dieselbe Anweisung erneut, bis ein Schreibzugriff auf eine ihrer Tabellen das Ergebnis veralten lässt. Er ist
standardmäßig aus. Er funktioniert mit den Treibern für MySQL, MariaDB, PostgreSQL, SQLite und Oracle; der MongoDB-Treiber
verwendet ihn nicht. Quelle: [Der SQL-Abfrage-Cache](../../bc/6.0/sql-abfrage-cache.md) und `settings/querycache.ini`.

## Inhalt

1. [Die Einstellungen](#die-einstellungen)
2. [Was nie gecacht wird](#was-nie-gecacht-wird)
3. [Wie Schreibzugriffe ungültig machen](#wie-schreibzugriffe-ungültig-machen)
4. [Schreibzugriffe außerhalb von Exponential](#schreibzugriffe-außerhalb-von-exponential)
5. [Ihn einschalten](#ihn-einschalten)
6. [Was normal ist, worauf Sie achten](#was-normal-ist-worauf-sie-achten)
7. [Checkliste](#checkliste)

## Die Einstellungen

`settings/querycache.ini [QueryCacheSettings]`, überschrieben in `settings/override/querycache.ini.append.php`:

| Schlüssel | Standard | Bedeutung |
|---|---|---|
| `Mode` | `off` | `off`: nichts wird behalten. `request`: ein identisches `SELECT` wird in einer Anfrage nicht zweimal ausgeführt. `shared`: Ergebnisse werden auch über Anfragen hinweg behalten |
| `Storage` | `auto` | Wo `shared` Ergebnisse hält: `auto` (APCu, wo der Prozess es hat, sonst Dateien), `apcu` oder `file` (`var/<site>/cache/querycache/entries/`, geteilt von jedem Webserver, der `var/` einhängt) |
| `ApcuReserve` | `25` | Prozent von APCu, die frei bleiben müssen; darunter werden Ergebnisse nur für die Anfrage behalten |
| `MaxAge` | `300` | Längste Lebensdauer eines Ergebnisses in Sekunden, die Grenze für einen Schreibzugriff, den der Cache nicht sehen kann |
| `MaxRows` | `5000` | Ergebnisse mit mehr Zeilen werden nicht behalten |
| `ExcludeTables[]` | `ezsession`, `ezpending_actions`, `ezcollab_notification_rule` | Tabellen, deren `SELECT`s nie gecacht werden |

`[DebugSettings] Report=enabled` fügt die Zähler des Cache der Debug-Ausgabe hinzu.

## Was nie gecacht wird

- Lesezugriffe innerhalb einer Transaktion;
- Anweisungen, deren Tabellen sich nicht aus dem Text lesen lassen (Unterabfragen in `FROM`, alles, was kein einfaches
  `SELECT` ist);
- Anweisungen, deren Ergebnis keine Funktion ihrer Tabellen ist: `NOW()`, `RAND()`, `UUID()`, `LAST_INSERT_ID()`, Sperren
  (`FOR UPDATE`), Variablen, `SQL_NO_CACHE`;
- Lesezugriffe auf temporäre Tabellen;
- Ergebnisse mit mehr als `MaxRows` Zeilen und Lesezugriffe auf `ExcludeTables`.

Auf Oracle außerdem Anweisungen, die eine Sequenz, die Uhr, `SYS_GUID`, `SYS_CONTEXT`, `DBMS_RANDOM` oder den Katalog lesen.

## Wie Schreibzugriffe ungültig machen

`INSERT`, `REPLACE`, `UPDATE`, `DELETE`, `TRUNCATE`, `CREATE`/`ALTER`/`DROP TABLE`, Indexänderungen und `RENAME TABLE`
markieren die Tabellen, die sie nennen, als beschrieben. Ein Schreibzugriff, dessen Tabellen sich nicht lesen lassen, markiert
alles als veraltet. Innerhalb einer Transaktion werden die Tabellen beim äußersten `COMMIT` markiert; bei `ROLLBACK` wird nichts
markiert. Andere Prozesse (Skripte, der Admin-Host, Velocity) sehen die Änderung über eine gemeinsame Zustandsdatei in
`var/<site>/cache/querycache/`.

## Schreibzugriffe außerhalb von Exponential

Der Cache kann einen Schreibzugriff mit einem SQL-Client, ein `mysql < dump.sql`, eine Wiederherstellung oder ein anderes
Programm nicht sehen. Ergebnisse bleiben bis zu `MaxAge` Sekunden gültig. Leeren Sie ihn nach jedem solchen Schreibzugriff und
nach jeder Wiederherstellung:

```bash
php bin/php/ezcache.php --clear-id=querycache --allow-root-user
```

oder unter **Setup > Caches**. `--clear-all` und die Tags `content` und `ini` schließen ihn ein.

## Ihn einschalten

1. Prüfen Sie APCu: **Setup > Systeminformationen**, Abschnitt PHP, oder `php -m | grep apcu`.
2. Setzen Sie `Mode=request` in `settings/override/querycache.ini.append.php`; leeren Sie den INI-Cache, laden Sie PHP-FPM neu,
   starten Sie Velocity neu. Surfen Sie einen Tag lang.
3. Setzen Sie `Mode=shared` auf dieselbe Weise. Beobachten Sie die Trefferquote unter **Setup > Systeminformationen**, Feld
   **Datenbankabfragen**.

Um ihn auszuschalten, setzen Sie `Mode=off` und gehen genauso vor; nichts muss gelöscht werden. Die Schaltflächen unter
**Setup > Caches** und **Setup > Systeminformationen** (den Cache leeren, seine Zähler zurücksetzen) brauchen die Richtlinie
`setup/managecache`.

## Was normal ist, worauf Sie achten

- Eine Trefferquote von 30 bis 40 Prozent direkt nach einem Leeren oder einem Neustart, die steigt, während Seiten besucht
  werden.
- Der SQL-Profiler zählt nur Anweisungen, die die Datenbank erreicht haben: Eine warme Seite mit geteiltem Cache zeigt 0
  ([Langsame Abfragen finden](langsame-abfragen-finden.md#der-sql-profiler)).

| Symptom | Ursache |
|---|---|
| Eine Seite zeigt nach einer Änderung außerhalb von Exponential alte Daten | den Abfrage-Cache leeren, wie oben |
| Die Trefferquote bleibt niedrig | eine Tabelle, die die Seiten lesen, wird bei jeder Anfrage beschrieben; die Systeminformationen listen die zuletzt beschriebenen Tabellen; schließen Sie sie aus oder korrigieren Sie den Schreiber |
| Viele Dateien in `entries/` | der Teil `cache_cleanup` läuft nicht |
| APCu voll, andere Caches verdrängt | `ApcuReserve` über 0 halten, `MaxRows` oder `MaxAge` senken oder `apc.shm_size` erhöhen |
| `state.ser` nicht schreibbar | `var/<site>/cache/querycache/` muss für jeden Benutzer schreibbar sein, unter dem die Website läuft |

Setzen Sie im Zweifel `Mode=off` und vergleichen Sie.

## Checkliste

- [ ] Jede Betriebsanleitung, die außerhalb von Exponential in die Datenbank schreibt, endet mit dem Leeren des Abfrage-Cache.
- [ ] `cache_cleanup` ist eingeplant, wenn `Storage` `file` ist oder `auto` ohne APCu.
- [ ] Trefferquote und APCu-Speicher werden nach dem Umstellen auf `shared` beobachtet.

## Verwandte Seiten

- [Leistung und Skalierung](leistung-und-skalierung.md)
- [Caches](../../anleitungen/caches.md)
- [Caching-Ebenen](../hosting/leistung/cache-schichten.md) (für Hosting-Unternehmen)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
