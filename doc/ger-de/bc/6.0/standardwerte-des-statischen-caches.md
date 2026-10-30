# Standardwerte des statischen Caches

Lesen Sie diese Seite, wenn Sie den **statischen Cache** (`settings/staticcache.ini`, `bin/php/makestaticcache.php`)
verwenden oder verwenden wollen. Vor dieser Änderung erzeugten die ausgelieferten Einstellungen in einer
Standardinstallation nichts. Die neuen Standardwerte funktionieren sofort, was bedeutet, dass sich ein vorhandenes
Override jetzt anders verhalten kann.

> **Warnung**
> **`.htaccess` bremst jede Anfrage.** Sind `.htaccess`-Dateien erlaubt (`AllowOverride` alles außer `None`), geschweige
> denn genutzt, sucht Apache sie bei jeder Anfrage in jedem Verzeichnis des Pfads und liest und wertet sie aus, und
> die Ladezeiten der Seiten steigen drastisch ([gemessen](../../anleitungen/setup-dateien-von-der-konsole.md#gemessen-htaccess-gegen-regeln-im-virtuellen-host)). Die einzige wirkliche Abhilfe ist, die
> Funktion für den virtuellen Host abzuschalten: `AllowOverride None` im virtuellen HTTPS-Host, und die Regeln in
> der Konfiguration des virtuellen Hosts selbst als mod_rewrite-Regeln. Das ist in Geschwindigkeitstests im
> Produktivbetrieb weit schneller und die Zeit für die Optimierung einer Produktivkonfiguration unbedingt wert.
> Stellen Sie sofort um: Übernehmen Sie die Regeln des [Beispiels für einen virtuellen HTTPS-Host](../../beispiele/webserver/apache-virtueller-host.md) in
> Ihren virtuellen Host (mit dem statischen Cache auch [`staticcache_offload.conf`](../../../eng-us/examples/staticcache_offload.conf)), setzen Sie
> `AllowOverride None`, führen Sie `apachectl configtest` aus, laden Sie Apache neu und testen Sie die Website.
> Dieselben Regeln für andere Server: [nginx](../../beispiele/webserver/nginx.md), [LiteSpeed](../../beispiele/webserver/litespeed.md), [Caddy](../../beispiele/webserver/caddy.md), [IIS](../../beispiele/webserver/iis.md),
> [Velocity](../../beispiele/webserver/velocity.md) (eingebaut); wohin Regeln für virtuelle Hosts in einem Control Panel gehören:
> [Plesk](../../hilfe/hosting/control-panels/plesk.md), [cPanel](../../hilfe/hosting/control-panels/cpanel.md), [DirectAdmin](../../hilfe/hosting/control-panels/directadmin.md), [ISPConfig](../../hilfe/hosting/control-panels/ispconfig.md).
> **Unter LiteSpeed oder OpenLiteSpeed** kann zusätzlich der Seiten-Cache des Servers anonyme Besucher und Crawler ohne PHP bedienen: [Seiten-Caching mit LiteSpeed und OpenLiteSpeed](../../anleitungen/seiten-cache-mit-litespeed.md).

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Sechs Standardwerte in `[CacheSettings]` von `settings/staticcache.ini`; die Dateien gehen jetzt nach `var/<site>/static`. |
| Wer betroffen ist | Installationen, die `StaticStorageDir` überschreiben, statische Dateien vom Webserver ausliefern oder sich auf das alte `CachedURLArray[]` verlassen haben. |
| Wie Sie es prüfen | `grep -v '^#' settings/staticcache.ini \| grep -v '^$'` |
| Wie Sie es beheben | Folgen Sie den drei Schritten unter „So beheben Sie es“. |

## Was sich geändert hat

Bisher schickte `HostName=localhost` jeden Abruf an einen Host, der nicht Ihre Website ist, und der Fehler blieb
unbemerkt. Seiten wurden nach `./static` im Wurzelverzeichnis der Installation geschrieben, wo kein Cache-Werkzeug
nachsah.

Datei `settings/staticcache.ini`, Block `[CacheSettings]`, Geltungsbereich: Installation.

| Schlüssel | Vorher | Jetzt |
|---|---|---|
| `HostName` | `localhost` | leer |
| `SourceProtocol` | existierte nicht | `http` |
| `StaticStorageDir` | `static`, also `./static` im Wurzelverzeichnis | `static`, also `var/<site>/static` |
| `MaxCacheDepth` | `3` | `12` |
| `CachedURLArray[]` | `/`, `/news*`, `/weblog*` | `/`, `/*` |
| `CachedSiteAccesses[]` | leer, also nichts | leer, also jeder öffentliche Siteaccess |

## So prüfen Sie es

```bash
grep -v '^#' settings/staticcache.ini | grep -v '^$'
php bin/php/makestaticcache.php --help --allow-root-user
```

Der erste Befehl gibt die ausgelieferten Standardwerte aus der obigen Tabelle aus. Der zweite listet die Optionen des
Generators auf.

## So beheben Sie es

1. **Sie überschreiben `StaticStorageDir=static` und haben Dateien in `./static`.** Behalten Sie entweder dieses
   Verzeichnis, indem Sie `StaticStorageDir=/full/path/to/static` setzen (ein absoluter Pfad wird unverändert
   verwendet), oder erzeugen Sie den Cache neu und löschen Sie das alte Verzeichnis selbst.
2. **Sie liefern die Dateien vom Webserver aus.** Wechseln Sie zu `.htaccess_root_static` (oder den Regeln in
   `doc/eng-us/examples/staticcache_offload.conf`) und prüfen Sie `EZ_VAR_SITE`. `.htaccess_root` enthält wieder die
   einfachen Regeln und liefert keine statischen Dateien aus.
3. **Sie haben sich darauf verlassen, dass `CachedURLArray[]` nur `/news*` und `/weblog*` aufführt.** Setzen Sie diese
   Zeilen wieder in Ihr Override. Der neue Standard cacht die ganze Website, begrenzt durch `MaxCacheDepth`.

## Verwandte Seiten

- [Generator des statischen Caches](../../funktionen/6.0/generator-fuer-den-statischen-cache.md)
- [Preload Sites](../../funktionen/6.0/ansicht-websites-vorladen.md)
- [Cache über die Konsole](cache-konsole.md) und [HTTP-Caching](http-caching.md)
- [Statischer Cache in der Chronik vom September 2026](../../geschichte/2026/2026-09a.md#13-september-caches-die-sie-sehen-cronjobs-die-sie-ausführen-können)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
