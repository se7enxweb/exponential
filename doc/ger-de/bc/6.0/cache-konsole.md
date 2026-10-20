# Cache-Steuerung von der Konsole aus: `exp:cache`

Lesen Sie diese Seite, wenn Sie Caches aus Skripten, Cron oder Deployments leeren oder wissen möchten, welcher Befehl
das tut, was eine Schaltfläche unter **Setup > Cache** tut. Seit Exponential 6.0.15 steht jede Aktion dieser Seite,
und die neueren Caches (statischer Cache, HTTP-Cache, SQL-Abfrage-Cache, Velocity), als ein Befehl zur Verfügung.
Vorhandene Befehle wie `bin/php/ezcache.php` funktionieren weiter.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Neuer Befehl `./console exp:cache` (`bin/php/cache.php`), der seinen Code über `expCacheManager` (`kernel/classes/expcachemanager.php`) mit Setup > Cache teilt. |
| Wer betroffen ist | Betreiber und Deploy-Skripte. Nichts ändert sich, bis Sie ihn verwenden. |
| Wie Sie es prüfen | `./console exp:cache all --dry-run` listet auf, was „alles leeren“ berühren würde, und ändert nichts. |
| Worauf Sie achten sollten | Leeren Sie nach einer Änderung der Einstellungen nach Tag (`--tag=ini`), nicht nach ID (`--id=ini`); siehe „INI-Caches: Verwenden Sie den Tag“. |

## Ausprobieren

```bash
./console exp:cache status --allow-root-user            # every group in one screen
./console exp:cache clear --tag=ini --dry-run           # what a settings change needs, without doing it
./console exp:cache ini                                 # do it
```

Die letzte Zeile jedes Laufs ist `PASS <message>` oder `FAIL <message>`.

## Was er ist

Alles, was die Seite **Setup > Cache** (`/setup/cache`) der Administration kann, und die neueren Caches der
Installation, als ein Befehl:

```
./console exp:cache <group> [action] [options]
```

Die Seite und der Befehl rufen dieselben Funktionen auf (`expCacheManager`), die ihrerseits die Funktionen aufrufen,
die der Rest des Kernels verwendet (`eZCache`, `eZDBQueryCache`, `ezpHttpCacheListener`, `expStaticCacheRunner`,
`expVelocity`). Nichts ist doppelt implementiert, daher leeren eine Schaltfläche und ihr Befehl dieselben Dateien.

Jede Gruppe hat ihre eigene Hilfe:

```bash
./console exp:cache --help              # the overview
./console exp:cache static --help       # one group
./console exp:cache help httpcache      # the same
```

## Optionen für jede Gruppe

| Option | Wirkung |
|---|---|
| `--dry-run` | auflisten, was geleert würde (Caches, Verzeichnisse, Dateien, Größen, Tags), und nichts ändern: Keine Datei wird geschrieben, entfernt oder abgerufen |
| `--json` | ein JSON-Objekt auf stdout: `ok`, `message`, `items`, `dry_run`, `data` |
| `--siteaccess=<name>` | der Siteaccess, dessen Einstellungen und Verzeichnis `var/` verwendet werden (Standardoption von eZScript) |
| `--allow-root-user` | nötig, wenn als root ausgeführt, wie bei jedem Exponential-Skript |

Die letzte Zeile ist `PASS <message>` oder `FAIL <message>` (`DRY RUN PASS ...` bei einem Probelauf). Exit-Codes:
**0** erledigt, **1** fehlgeschlagen, **2** Fehler im Aufruf.

Berechtigungen: Wie jedes Exponential-Skript braucht er Shell-Zugang zur Installation und verweigert ohne
`--allow-root-user` den Lauf als root. Führen Sie ihn als Benutzer des Webservers aus, damit das, was er neu anlegt,
für den Webserver beschreibbar bleibt.

## Befehlsreferenz

### Die Cache-Liste: IDs und Tags

| Befehl | Setup > Cache | Was er tut |
|---|---|---|
| `exp:cache list [--sizes]` | die Cache-Tabelle | jeder Cache (Kernel und Erweiterungen): ID, an/aus, Tags, wie er geleert wird, wo er liegt; `--sizes` zählt Dateien und Bytes |
| `exp:cache tags` | — | jeder Tag und die Caches, die er erreicht |
| `exp:cache clear --all` | Den kompletten Cache leeren | jeder Cache der Liste |
| `exp:cache clear --tag=content[,template]` | Cache des Inhalts leeren / Template-Cache leeren | jeder Cache mit dem Tag |
| `exp:cache clear --id=template-override,classid` | Ausgewähltes leeren | genau diese Caches |
| `exp:cache clear ... --purge [--expiry='-2 days']` | — | die Einträge wirklich entfernen, statt sie ablaufen zu lassen (wie `ezcache.php --purge`); `--iteration-sleep`, `--iteration-max` bestimmen das Tempo |
| `exp:cache content` | Cache des Inhalts leeren | Kurzform für `clear --tag=content` |
| `exp:cache template` | Template-Cache leeren | Kurzform für `clear --tag=template` (kompilierte Templates, Override-Cache, Template-Blöcke, Design-Base) |
| `exp:cache ini` | Ini-Cache leeren | Kurzform für `clear --tag=ini` |
| `exp:cache all` | Den kompletten Cache leeren | Kurzform für `clear --all` |
| `exp:cache imagealias [clear\|purge]` | Ausgewähltes leeren: Bild-Alias | `clear` lässt jeden Alias ablaufen (beim nächsten Ansehen neu erzeugt); `purge` löscht die Alias-Dateien und behält die Originale |

**INI-Caches: Verwenden Sie den Tag.** `--tag=ini` leert jeden Konfigurations-Cache: den globalen INI-Cache in
`var/cache/ini`, den INI-Cache pro Website, die Liste der aktiven Erweiterungen, den Abfrage-Cache und die SSL-Zonen.
Das tut die INI-Schaltfläche. `--id=ini` leert nur den INI-Cache pro Website und lässt `var/cache/ini`, aus dem die
zusammengeführten Einstellungen gelesen werden, stehen: Nach einer Änderung der Einstellungen können noch die alten
Werte ausgeliefert werden. `ezcache.php` verhält sich genauso (`--clear-tag=ini` gegenüber `--clear-id=ini`).

```bash
./console exp:cache clear --tag=ini --dry-run
  querycache           off content,ini                new query cache generation: every stored SQL result becomes stale
  global_ini           on  ini                        directory removed
                          var/cache/ini 96 files, 626.2 KB
  ini                  on  ini                        directory removed
                          var/site/cache/ini  (does not exist) 0 files, 0 B
  active_extensions    on  ini                        active extensions list cache expired
  sslzones             off ini                        handler eZSSLZone::clearCache()
DRY RUN PASS  would clear tag ini: 5 caches
```

In dieser Installation lag jede zusammengeführte Einstellung in `var/cache/ini`, und `--id=ini` hätte ein leeres
Verzeichnis geleert.

### Statischer Cache

| Befehl | Setup > Cache | Was er tut |
|---|---|---|
| `exp:cache static status` | die Tabelle des statischen Caches | wohin Seiten geschrieben werden, welche Websites erzeugt werden können, was jede enthält |
| `exp:cache static regenerate [--site=<sa>]` | Neu erstellen | ruft jede Seite ab, auf die die Website verlinkt, und speichert sie; die gespeicherten Seiten der Website werden vorher entfernt, außer mit `--keep`; `--max-pages`, `--max-depth` begrenzen das Crawlen |
| `exp:cache static regenerate --path=/about-us[,/news]` | — | nur diese Seiten (relativ zur Website) |
| `exp:cache static regenerate --node=62[,63]` | — | nur die Seiten dieser Knoten, auf jeder Website, die sie ausliefert (PathPrefix wird beachtet) |
| `exp:cache static clear [--site=<sa>]` | — | entfernt die gespeicherten Seiten; der Webserver fragt dann wieder das CMS |
| `exp:cache static clear --path=/about-us` oder `--node=62` | — | entfernt einzelne gespeicherte Seiten |

`--site` (nicht `--siteaccess`, das die eigenen Einstellungen des Skripts wählt) wählt die Website; Standard ist jede
cachebare. Das Crawlen ist dasselbe, das `bin/php/makestaticcache.php` und die Seite verwenden
(`expStaticCacheRunner`).

### Rollenabhängiger HTTP-Cache (`httpcache.ini`, `var/<site>/cache/exphttpcache`)

| Befehl | Setup > Cache / Systeminformationen | Was er tut |
|---|---|---|
| `exp:cache httpcache status` | Systeminformationen, HTTP-Cache | eingeschaltet, Einträge auf der Festplatte, Generation, gelöschte Tags, Trefferquote |
| `exp:cache httpcache clear` | HTTP-Cache leeren | jede Seite in jedem Berechtigungskontext (eine neue Generation) |
| `exp:cache httpcache purge --node=62[,63]` | — | die Seiten dieser Knoten (Tag `l62`) |
| `exp:cache httpcache purge --url=https://example.com/about-us` | — | die Seite, die eine URL zeigt: Die URL wird auf ihren Knoten aufgelöst (ein erstes Element, das einen Siteaccess nennt, und der PathPrefix dieses Siteaccess werden berücksichtigt) und dann in jedem Kontext gelöscht |
| `exp:cache httpcache purge --tag=c17,pl2` | — | Seiten mit diesen Tags (`l<node>`, `c<object>`, `pl<parent>`, `ct<class>`, `s<section>`, `p<path node>`, `dq`, `ez-all`) |
| `exp:cache httpcache gc` | Systeminformationen, Aufräumen | entfernt abgelaufene und gelöschte Einträge, verwaiste Bodies, alte Benutzerdatensätze |
| `exp:cache httpcache reset-stats` | Systeminformationen, Zähler zurücksetzen | startet die Zähler für Treffer und Fehlschläge neu |

Ist der HTTP-Cache in `httpcache.ini` ausgeschaltet, antworten die leerenden Aktionen mit FAIL „switched off“, genau
wie die Schaltfläche.

### SQL-Abfrage-Cache

| Befehl | Setup > Cache | Was er tut |
|---|---|---|
| `exp:cache querycache status` | die Zeile des Abfrage-Caches | an/aus und Modus |
| `exp:cache querycache clear` | Query-Cache leeren | eine neue Generation: Jedes gespeicherte Ergebnis ist auf jedem Server, der `var/` teilt, veraltet |

### Velocity: Antwort-Cache und vorkomprimierte statische Dateien

| Befehl | Entspricht | Was er tut |
|---|---|---|
| `exp:cache velocity status` | `exp:velocity cache stats` | Verzeichnis des Antwort-Caches, Dateien, Bytes, wann er zuletzt geleert wurde |
| `exp:cache velocity clear` | `exp:velocity cache clear` | berührt die Generationsmarkierung: Jede vor jetzt gespeicherte Seite wird erneut gerendert (bei eingeschaltetem HTTP-Cache auch dessen Generation); kein Neustart |
| `exp:cache precompress status` | — | `var/tmp/precompress`: Dateien, Bytes, `velocity.ini` PrecompressStatic/MaxFiles/MinSize |
| `exp:cache precompress clear` | — | entfernt die vorkomprimierten `.gz`-Dateien, die die Engine geschrieben hat (sonst nichts im Verzeichnis); jede wird bei ihrer nächsten Anfrage erneut komprimiert |

Beide Velocity-Befehle rufen `expVelocity::clearCache()` und `expCacheManager::velocityCacheStatus()` auf;
`exp:velocity cache stats` ist neu und verwendet dieselbe Funktion. Die Engine hat keinen eigenen Neuaufbau der
Vorkomprimierung (eine Datei wird bei ihrer ersten Anfrage komprimiert, und eine bearbeitete Datei erhält einen neuen
Eintrag, weil der Name ihre mtime trägt), daher antwortet `precompress rebuild` mit einem Aufruffehler, der das sagt.

### PHP-Caches dieses Prozesses

| Befehl | Setup > Cache | Was er tut |
|---|---|---|
| `exp:cache opcache status\|reset` | OPcache zurücksetzen | OPcache **dieses** Prozesses |
| `exp:cache apcu status\|clear` | APCu leeren | APCu **dieses** Prozesses |

OPcache und APCu leben im gemeinsamen Speicher des Prozesses, der den Code ausführt. Ein Kommandozeilenskript hat
seinen eigenen (nur mit `opcache.enable_cli` / `apc.enable_cli`), daher erreichen diese Befehle nie einen
php-fpm-Pool, einen Velocity-Server oder FrankenPHP: Setzen Sie deren Caches unter Setup > Cache zurück, das innerhalb
dieses Servers läuft, oder laden Sie den Server neu.

### Alles auf einmal

```bash
./console exp:cache status          # every group in one screen
./console exp:cache status --json   # for monitoring
```

## Beispiele

```bash
# After a settings change
./console exp:cache ini

# After a template change: templates, the Velocity response cache, the HTTP cache
./console exp:cache template
./console exp:cache velocity clear

# One page changed outside the editor: its static file and its HTTP cache entries
./console exp:cache static regenerate --node=62
./console exp:cache httpcache purge --node=62

# See what "clear everything" would touch before doing it
./console exp:cache all --dry-run
```

## Für Entwickler

`expCacheManager` gibt für jede Aktion ein Ergebnis-Array zurück (`ok`, `message`, `items`, `dry_run`, `data`);
`kernel/setup/cache.php` macht daraus die Rückmeldung der Seite, `bin/php/cache.php` PASS/FAIL-Zeilen oder JSON. Ein
neuer Cache gehört in `site.ini [Cache] CacheItems[]` (er erscheint dann in `list`, `clear --id` und auf der Seite);
eine neue Art von Aktion gehört in `expCacheManager` und wird von beiden aufgerufen. Tests:
`tests/tests/kernel/classes/expCacheManagerTest.php`.

## Verwandte Seiten

- [Leeren von Caches, das Verzeichnisse beiseiteschiebt](../../../eng-us/features/6.0/cache-clear-rename-aside.md)
- [HTTP-Cache](httpcache.md), [SQL-Abfrage-Cache](sql-abfrage-cache.md), [Standardwerte des statischen Caches](standardwerte-des-statischen-caches.md)
- [Antwort-Cache von Velocity](../../../eng-us/features/6.0/velocity-response-cache.md)
- [Verhaltensänderungen, 16. bis 30. September 2026](verhaltensaenderungen-2026-09b.md)
- [Chronik, 16. bis 30. September 2026](../../../eng-us/history/2026/2026-09b.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
