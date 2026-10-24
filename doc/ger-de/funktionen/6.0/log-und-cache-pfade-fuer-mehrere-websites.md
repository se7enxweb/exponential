# Logs, INI-Cache und Ablaufzeitstempel pro Website (Hosting mehrerer Websites)

Lesen Sie diese Seite, wenn Sie mehrere Websites auf einer Exponential-Installation betreiben, jede mit einem eigenen
VarDir, und die Logs jeder Website in einem eigenen Verzeichnis haben, den Cache einer Website auf ein anderes
Dateisystem (Arbeitsspeicher) legen oder den INI-Cache jeder Website einzeln leeren möchten. Sie beschreibt die
Einstellungen, ihr Verhalten unter Velocity und auf der Kommandozeile und was zu tun ist, wenn eine Installation von
einer eigenen Lösung auf diese Einstellungen umgestellt wird.

## Kurz gefasst

| | |
|---|---|
| Logs pro Website | `site.ini [FileSettings] UseGlobalLogDir=disabled`: Die Debug-Logs (`error.log`, `warning.log`, ...) und die Standard-Logs von `eZLog` landen im Log-Verzeichnis der Website statt in `var/log`. |
| Logs auf einem anderen Dateisystem | `LogDir` darf wie `CacheDir` ein absoluter Pfad sein; `LogVarDir=var_log` legt die Logs aller Websites mit einer Zeile in `var_log/<site>/log` ab. |
| Cache im Arbeitsspeicher | `CacheVarDir=var_cache` legt die Caches aller Websites in `var_cache/<site>/cache` ab, ein gemeinsames Speicher-Dateisystem für alle; oder Sie hängen eines unter `var/<site>/cache` ein. `ExpiryDir` hält `expiry.php` dort, wo der Speicher liegt, sodass ein Neustart die Ablaufzeitstempel nicht verliert. |
| INI-Cache pro Website | `INICacheDir=site`: Die INI-Dateien der Website werden in ihrem Cache-Verzeichnis zwischengespeichert, und das Leeren lässt die anderen Websites unberührt. |
| Standard | Jede Einstellung behält das bisherige Verhalten. |
| Velocity, Skripte | Jeder Wert wird beim Wechsel des Siteaccess neu aus site.ini abgeleitet; nichts wird von einer Anfrage oder einer Website zur nächsten übernommen. |
| Wer handeln muss | Niemand, es sei denn, `[FileSettings] AllowedDeletionDirs` verließ sich auf einen Eintrag, der nicht existiert, oder auf ein Präfix eines Verzeichnisnamens (siehe [was wo geleert wird](#was-wo-geleert-wird)). |

## Einstellungen

Alle in `site.ini [FileSettings]`, für jeden Siteaccess der Website (oder in einer Überschreibung, die für alle gilt,
siehe [Website-spezifische Einstellungen in Extensions](ini-ueberschreibungen-fuer-mehrere-websites.md)):

| Schlüssel | Standard | Bedeutung |
|---|---|---|
| `LogDir` | `log` | Das Log-Verzeichnis innerhalb von VarDir; ein absoluter Pfad wird unverändert verwendet. `storage.log` und das Log der Debug-Leiste landen in jedem Fall dort. |
| `UseGlobalLogDir` | `enabled` | `disabled` schreibt die Debug-Logs und die Logs, die `eZLog::write()` in sein Standardverzeichnis schreibt, in das Log-Verzeichnis der Website. |
| `LogVarDir` | leer | Ein Verzeichnisbaum für die Logs aller Websites: Das erste Verzeichnis von VarDir wird ersetzt, `var/example` → `var_log/example`, sodass das Log-Verzeichnis `var_log/example/log` ist. Ein relativer oder absoluter Pfad. |
| `CacheVarDir` | leer | Dasselbe für die Caches: `var_cache/example/cache`. Ein Verzeichnis innerhalb der Installation, damit seine öffentlichen Caches ausgeliefert werden können; ein absoluter Pfad wird verwendet, seine öffentlichen Caches werden aber nicht ausgeliefert, wie bei einem absoluten `CacheDir`. |
| `ExpiryDir` | leer | Wo `expiry.php` liegt: leer im Cache-Verzeichnis, sonst ein Verzeichnis innerhalb von VarDir oder ein absoluter Pfad. |
| `INICacheDir` | `global` | `site` speichert die INI-Dateien der Website in `<cache directory>/ini/` zwischen; `global` in `var/cache/ini/`. |

## Viele Websites mit Logs und Caches in eigenen Verzeichnisbäumen

Die Einrichtung einer Installation mit Dutzenden Websites, jede in einer Website-Extension mit `VarDir=var/<site>`:
je eine Zeile in `settings/override/site.ini.append.php` für alle.

```ini
[FileSettings]
UseGlobalLogDir=disabled
LogVarDir=var_log
CacheVarDir=var_cache
ExpiryDir=expiry
INICacheDir=site
```

```
# /etc/fstab: the caches of all sites in memory
tmpfs  /srv/www/exponential/var_cache  tmpfs  size=8g,mode=0775,uid=www-data,gid=www-data  0 0
```

- Die Logs von `var/example` liegen in `var_log/example/log`, sein Cache in `var_cache/example/cache` (mit dem
  INI-Cache in `ini/`), `expiry.php` in `var/example/expiry/`. Der Speicher bleibt in `var/example/storage`.
- Velocity liefert die öffentlichen Caches unterhalb von `var_cache/` aus (gepackte Skripte und Stylesheets, Text als
  Bild) und sonst nichts dort: `expVelocity::staticPaths()` fügt sie den Pfaden aller drei Engines hinzu, wenn
  `CacheVarDir` gesetzt ist. Für Apache aktivieren Sie die auskommentierte `var_cache`-Regel in `.htaccess_root`, für
  nginx die auskommentierte `location` in [Website ausliefern](../../installation/08-die-website-ausliefern.md).
- Mit einem Cluster-Datei-Handler siehe [Cluster](#cluster-dfs) weiter unten.
- Setzen Sie die Einstellungen dort, wo jeder Siteaccess jeder Website sie liest (die globale Überschreibung). Velocity
  schreibt seine Liste der ausgelieferten Pfade beim Start, ohne Siteaccess: Es liest `CacheVarDir` aus den globalen
  Einstellungen und aus den vollständigen Einstellungen jedes Siteaccess in `[SiteAccessSettings]
  AvailableSiteAccessList`, sodass auch ein nur für einige Websites gesetztes `CacheVarDir` ausgeliefert wird. Starten
  Sie Velocity nach einer Änderung neu.

## Eine Website mit ihrem Cache im Arbeitsspeicher

```ini
# settings/siteaccess/example/site.ini.append.php (and the other siteaccesses of the site)
[FileSettings]
VarDir=var/example
UseGlobalLogDir=disabled
LogDir=/srv/logs/example
ExpiryDir=expiry
INICacheDir=site
```

```
# /etc/fstab: the cache of the site in memory, emptied at every restart
tmpfs  /srv/www/exponential/var/example/cache  tmpfs  size=2g,mode=0775,uid=www-data,gid=www-data  0 0
```

- Das Cache-Verzeichnis bleibt `var/example/cache`, sodass die Dateien, die Webserver und Velocity daraus ausliefern
  (gepackte Skripte und Stylesheets in `cache/public`, `cache/texttoimage`), ihre Adressen behalten. Ein absolutes
  `CacheDir` würde sie aus den ausgelieferten Pfaden herausnehmen; deshalb bezeichnet site.ini es als nicht
  unterstützt.
- `expiry.php` enthält die Zeitpunkte, zu denen Caches und Bild-Aliase zuletzt geleert wurden. Die Bild-Aliase liegen
  im Speicher, der einen Neustart übersteht; würde `expiry.php` mit dem Cache geleert, gälten Bild-Aliase, die vor
  einem Leeren erzeugt wurden, wieder als aktuell. Mit `ExpiryDir=expiry` liegt die Datei in `var/example/expiry/`.
- Die Logs werden ab dem Moment, in dem der Siteaccess bekannt ist, nach `/srv/logs/example/` geschrieben. Meldungen,
  die vorher geschrieben werden (während die Anfrage einem Siteaccess zugeordnet wird), landen weiterhin in `var/log`.

## Was wo geleert wird

Die Cache-Befehle und **Setup > Caches** funktionieren wie bisher; die Einstellungen entscheiden nur, welches
Verzeichnis ein Cache-Eintrag bezeichnet.

| Cache | Was entfernt wird |
|---|---|
| INI-Cache (`ini`) | `<cache directory of the site>/ini/`: mit `INICacheDir=site` nur der INI-Cache dieser Website |
| Globaler INI-Cache (`global_ini`) | `var/cache/ini/`: die INI-Dateien, die gelesen werden, bevor ein Siteaccess bekannt ist, und die der Websites mit `INICacheDir=global` |
| Per Zeitstempel geleerte Caches (Ansichts-Cache, Template-Blöcke, Benutzerinformationen, ...) | Ein Zeitstempel in der `expiry.php` der Website (in `ExpiryDir`, falls gesetzt); beim Bereinigen werden die abgelaufenen Dateien aus dem Cache-Verzeichnis entfernt |
| Alles leeren, alles bereinigen | Alles im Cache-Verzeichnis der Website und `var/cache/ini/`; `expiry.php` in `ExpiryDir` bleibt erhalten und erhält die neuen Zeitstempel |

Geprüft auf einer Installation mit `bin/php/ezcache.php -s <siteaccess>` für `global_ini`, `ini`, den Tag `content`
und alle Caches, jeweils geleert und bereinigt: Der INI-Cache der Website wurde nur durch `ini` und das vollständige
Leeren entfernt, der globale nur durch `global_ini` und das vollständige Leeren, `expiry.php` in `ExpiryDir` blieb
bei allen erhalten und erhielt die Zeitstempel, und die `expiry.php` im Cache-Verzeichnis wurde nicht angetastet.

Ein laufendes Velocity hält ebenfalls Einstellungen im Speicher: Starten Sie es nach dem Leeren eines INI-Cache neu,
wie es **Setup > Caches** angibt.

Verzeichnisse außerhalb der Installation: `eZDir::recursiveDelete()`, über das jedes Leeren eines Cache läuft,
löscht nur innerhalb des Wurzelverzeichnisses und innerhalb von `[FileSettings] AllowedDeletionDirs`. Es löscht auch
innerhalb der Verzeichnisse, in die die aktuelle Website schreibt, wie auch immer sie gesetzt sind (ein absolutes
`CacheDir`, `LogDir` oder `ExpiryDir`, ein `CacheVarDir` oder `LogVarDir` außerhalb des Wurzelverzeichnisses, ein
`CacheVarDir`, das ein Link auf ein Speicher-Dateisystem ist, ein von einem Wrapper gesetztes
`$GLOBALS['eZINI_CONFIG_CACHE_DIR']`): deren Inhalt und das Cache-Verzeichnis als Ganzes, wie innerhalb des
Wurzelverzeichnisses; niemals das Log- oder Ablaufverzeichnis selbst, nichts daneben, niemals die Wurzel eines
Dateisystems und niemals die Installation oder ein Verzeichnis, das sie enthält (ein `LogDir` von `/var/www` erlaubt
nichts). Ein Eintrag in `AllowedDeletionDirs`, der nicht existiert, erlaubt nichts (bisher erlaubte er jeden Pfad),
und `/srv/cache` erlaubt nicht mehr `/srv/cache-other`.

## Velocity, Skripte und Wechsel des Siteaccess

`eZSiteAccess::change()` setzt alle drei für den Siteaccess, zu dem gewechselt wird: das Log-Verzeichnis
(`eZSiteAccess::updateLogDirectory()`), das INI-Cache-Verzeichnis (`eZSiteAccess::updateINICacheDirectory()`) und die
Ablaufdatei (`eZExpiryHandler::resetForCurrentCacheDirectory()`). Dies geschieht für jeden Front-Controller,
einschließlich `soap.php`, und für jedes Skript, sodass `bin/php/ezcache.php -s example` und die
Cronjobs einer Website in die Verzeichnisse dieser Website schreiben.

Mit `INICacheDir=site` wird auch die `site.ini` des Siteaccess selbst im INI-Cache der Website zwischengespeichert:
`change()` liest sie zuerst aus dem gemeinsamen `var/cache/ini/` (dort erfährt es `INICacheDir`) und ein weiteres
Mal aus dem Verzeichnis der Website, sodass das Leeren des INI-Cache der Website sie auffrischt. Dieses zweite Lesen
kostet für eine solche Website eine zwischengespeicherte Datei pro Anfrage. Eine Website, zu der danach im selben
Prozess gewechselt wird, hinterlässt nie eine Kopie ihrer `site.ini` im INI-Cache der vorherigen Website.

- **Velocity** bedient die Websites einer Installation nacheinander, Anfrage für Anfrage, im selben Worker. Zwischen
  den Anfragen stellt es die globalen Variablen und statischen Eigenschaften seines Warm-ups wieder her und entfernt
  alle anderen globalen Variablen, mit Ausnahme der Typ-Registrierungen seiner Exponential-Voreinstellung. Die drei
  Pfade liegen in globalen Variablen (`eZDebugLogDir`, `eZINI_CONFIG_CACHE_DIR`, die Instanzen von `eZDebug` und
  `eZExpiryHandler`) und in keiner statischen Eigenschaft, sodass die nächste Anfrage mit den Standardwerten beginnt
  und die Pfade ihrer eigenen Website übernimmt, sobald ihr Siteaccess bekannt ist.
- **Das Velocity-Warm-up** rendert eine Website im Elternprozess, bevor die Worker abgespalten werden. Es setzt die
  Log- und INI-Cache-Verzeichnisse, die diese Website gesetzt hat, auf die einer Anfrage ohne Siteaccess zurück
  (`eZSiteAccess::resetSitePaths()`), bevor es die globalen Variablen des Renderns entfernt, sodass keine Anfrage mit
  den Verzeichnissen der beim Warm-up gerenderten Website beginnt.
- **Zwei Websites in einem Prozess** (ein Skript, das den Siteaccess wechselt, oder das Leeren des Inhalts-Cache, das
  jeden Siteaccess besucht): Der zweite Wechsel übernimmt die Pfade der zweiten Website; eine Website ohne die
  Einstellungen kehrt zu `var/log`, `var/cache/ini/` und ihrem Cache-Verzeichnis zurück. Zweimal zur selben Website zu
  wechseln ändert nichts.
- **Ein von der Installation selbst gesetztes Verzeichnis**: Ein Wrapper für mehrere Websites kann
  `$GLOBALS['eZINI_CONFIG_CACHE_DIR']` vor dem Start setzen, für einen INI-Cache, den alle Projekte teilen. Eine
  Website mit `INICacheDir=site` verwendet ihren eigenen; nach ihr erhält eine Website ohne die Einstellung das
  Verzeichnis des Wrappers zurück.

Geprüft mit Velocity 0.0.4.45 (Engine qbix, vier dauerhafte Worker) auf einer Installation mit zwei Siteaccesses,
einer mit `UseGlobalLogDir=disabled`, `INICacheDir=site` und gesetztem `ExpiryDir`, einer ohne: Von 40 abwechselnden
Anfragen, wobei jeder Worker beide bediente, lagen die 20 Fehler des einen alle in dessen eigenem Log-Verzeichnis und
die 20 des anderen alle in `var/log`. Dreißig weitere Anfragen an die Website ohne die Einstellungen ließen den
INI-Cache der anderen unberührt und schrieben ihren eigenen nach `var/cache/ini`. Mit `LogVarDir=var_log`,
`CacheVarDir=var_cache`, `INICacheDir=site` und `ExpiryDir=expiry` in der globalen Überschreibung lieferte Velocity
die gepackten Stylesheets und Skripte aus `var_cache/<site>/cache/public/` aus und verweigerte die anderen Dateien
dort, die Fehler landeten in `var_log/<site>/log`, und das Leeren und Bereinigen des INI-Cache, des Tags content und
aller Caches wirkte auf `var_cache`, während `expiry.php` in `var/<site>/expiry/` blieb. Dasselbe mit nginx und
PHP-FPM.

## Was die Logs liest

- **Setup-Log** (`var/log/setup.log`, die Installationsläufe): Die Fehler und Warnungen jedes Schritts werden in der
  `error.log` und `warning.log` gezählt, die eZDebug zu Beginn des Schritts schreibt, mit `UseGlobalLogDir=disabled`
  im Log-Verzeichnis der Website, sonst in `var/log`. `setup.log` selbst bleibt in `var/log`.
- **Setup > Systeminformationen** nennt das Log-Verzeichnis der aktuellen Website in der Karte **Speicher**, warnt,
  wenn es oder `var/log` nicht beschreibbar ist, und findet die Cronjob-Logs (`cronjob*.log`) in `var/log` und im
  Log-Verzeichnis der Website. Die Seite beschreibt den Siteaccess, in dem sie läuft; die Log-Verzeichnisse anderer
  Websites werden nicht aufgeführt.
- **Audit-Einträge** (`audit.ini [AuditSettings] LogDir`, standardmäßig `log/audit`) bleiben innerhalb von VarDir:
  Sie gehören zum Speicher der Website, werden jahrelang aufbewahrt und gegen ihre Hash-Kette geprüft, deshalb
  verschieben `LogVarDir`, `LogDir` und `UseGlobalLogDir` sie nicht (ein `LogVarDir` auf einem Speicher-Dateisystem
  würde sie verlieren). Ein absolutes `[AuditSettings] LogDir` verschiebt sie. Die alten Textdateien mit den Namen aus
  4.x (`[AuditCompatSettings] LegacyFiles`) werden in dasselbe Verzeichnis geschrieben, und ein absolutes `VarDir` wird
  unverändert verwendet.
- **Der Cronjob für den Aufrufzähler** (`updateviewcount`) hält `updateview.log` im Log-Verzeichnis der Website.

## Cluster (DFS)

- Mit `eZDFSFileHandler` werden die Dateien des Cache in der Cluster-Datenbank und auf dem DFS-Mount unter denselben
  Namen wie auf der Festplatte geführt, unter `CacheVarDir` also als `var_cache/<site>/cache/...`. Die getrennte
  Cache-Tabelle (`MetaDataTableNameCache`) übernimmt jeden Namen, der `CacheDir` und nicht `StorageDir` enthält, was
  auf einen Namen unterhalb von `var_cache` zutrifft. Das Leeren der getrennten Cache-Tabelle (`deleteCacheFiles()`)
  erfasst die Namen unterhalb des Cache-Verzeichnisses der Website, `eZSys::cacheDirectory()`.
- `index_cluster.php` liefert den angefragten Namen aus, wenn die Cluster-Datenbank ihn kennt, aber der Webserver
  leitet nur die Pfade an sie weiter, die seine Rewrite-Regeln nennen. Ergänzen Sie die öffentlichen Caches unterhalb
  von `CacheVarDir` neben denen von `var/`:

  ```
  RewriteRule ^/var_cache/([^/]+/)?cache/(texttoimage|public)/.* /index_cluster.php [L]
  ```

  Velocity liefert die öffentlichen Caches von der lokalen Festplatte aus, wie unterhalb von `var/`.
- `expiry.php` ist ebenfalls eine Cluster-Datei: Ihre Hauptkopie liegt in der Cluster-Datenbank und auf dem Mount,
  sodass ein Speicher-Dateisystem auf dem Webserver sie nicht verliert, und `ExpiryDir` ist dafür nicht nötig. Mit
  `ExpiryDir=expiry` lautet ihr Name `var/<site>/expiry/expiry.php`; er wird in der Haupttabelle (`ezdfsfile`)
  geführt, nicht in der Cache-Tabelle, und beim Leeren der Cache-Tabelle nicht entfernt.

## Grenzen

- **Kompilierte Templates** landen in `<cache directory>/template/compiled/`, einmal pro Prozess abgeleitet
  (`eZTemplateCompiler::compilationDirectory()`). Ein Skript oder eine Vorschau, die nach dem Kompilieren eines
  Templates den Siteaccess wechselt, schreibt kompilierte Templates weiter in das Cache-Verzeichnis der ersten Website.
  Das war mit einem `VarDir` pro Website schon so; `CacheVarDir` ändert daran nichts: Die kompilierte Datei ist nach
  dem Template benannt, das sie kompiliert, sie liegt also nur im Cache einer anderen Website und wird nie anstelle
  einer Datei dieser Website verwendet. Velocity entfernt das Verzeichnis nach jeder Anfrage mit den anderen globalen
  Variablen.
- **INI-Dateien, die gelesen werden, bevor der Siteaccess bekannt ist** (außer `site.ini`), enthalten keine
  Einstellung eines Siteaccess und bleiben im gemeinsamen `var/cache/ini/`. Front-Controller und Skripte lesen nach
  dem Wechsel jede INI-Datei erneut (`eZINI::resetAllInstances( false )`), und diese Kopie wird im INI-Cache der
  Website zwischengespeichert.
- **Windows-Pfade**: Ein absoluter Pfad ist einer, der mit `/` beginnt, wie bisher bei `CacheDir`. `C:\...` in
  `CacheDir`, `LogDir`, `LogVarDir`, `CacheVarDir` oder `ExpiryDir` wird als Verzeichnis innerhalb von VarDir
  aufgefasst; verwenden Sie unter Windows Pfade innerhalb der Installation.

## Eine bestehende Einrichtung mehrerer Websites auf diese Einstellungen umstellen

Installationen, die Logs und Caches bereits mit eigenen Änderungen am Kernel getrennt hielten, erreichen dasselbe mit
den Einstellungen:

- Logs in einem eigenen Verzeichnisbaum (`var_log/<site>/log`): `UseGlobalLogDir=disabled` und `LogVarDir=var_log`.
- Caches in einem eigenen Verzeichnisbaum (`var_cache/<site>/cache`): `CacheVarDir=var_cache`. Regeln, die der
  Webserver bereits für `/var_cache/` hat, funktionieren weiter; Velocity liefert die öffentlichen Caches selbst aus.
- Eine `expiry.php`, die unter einem anderen Namen außerhalb des Cache liegt: Kopieren Sie sie vor der ersten Anfrage
  nach `var/<site>/expiry/expiry.php` und setzen Sie `ExpiryDir=expiry`. Ohne sie gälten Bild-Aliase, die vor dem
  letzten Leeren erzeugt wurden, als aktuell.
- Ein INI-Cache pro Website: `INICacheDir=site`.
- Code, der jeden `var/<site>/log`-Pfad der Installation umschrieb, ist nicht nötig und wird nicht mitgeliefert:
  Extensions, die über `eZLog` oder `eZSys::logDirectory()` loggen, folgen den Einstellungen; eine Extension, die den
  Pfad selbst aus VarDir und LogDir zusammensetzt, muss auf `eZSys::logDirectory()` umgestellt werden.

## Für Autoren von Extensions

- `eZSys::logDirectory()` liefert das Log-Verzeichnis der Website, so wie `eZSys::cacheDirectory()` das für den Cache
  tut; beide folgen `LogVarDir` und `CacheVarDir` (`eZSys::relocatedVarDirectory()`). Setzen Sie keinen Log- oder
  Cache-Pfad selbst aus `VarDir` zusammen. Für das Cache-Verzeichnis eines anderen Siteaccess übergeben Sie dessen
  Einstellungen an `eZSys::cacheDirectoryOf( eZINI::instance( 'site.ini.append', <its settings directory> ) )`, wie
  es das klassische Menü und die Werkzeugleiste tun. Ein `VarDir`, das mit `..` beginnt, wird nicht verlagert.
- `eZDebug::instance()->logDirectory()` liefert, wohin die Debug-Logs gehen; `eZLog::write()` ohne Verzeichnis
  schreibt dorthin, ebenso `eZLog::write()` mit dem Verzeichnis `var/log` (dem alten Standard, den Aufrufer auch
  ausdrücklich übergeben). Jedes andere an `eZLog::write()` übergebene Verzeichnis und eine ohne `eZLog` nach
  `var/log` geschriebene Datei bleiben, wo sie sind.
- `eZExpiryHandler::filePath()` liefert die `expiry.php` der Website.

## Tests

- `eZSiteLogAndExpiryPathsTest` (ohne Datenbank): das Log-Verzeichnis innerhalb von VarDir und absolut; `LogVarDir`
  und `CacheVarDir`, relativ und absolut, mit VarDir `var` und mit einem absoluten VarDir; die Debug-Logs und `eZLog`
  dort und zurück nach `var/log`; `storage.log` in einem absoluten `LogDir`; `UseGlobalLogDir`; `ExpiryDir` leer,
  relativ, absolut und mit verschobenem Cache; Zeitstempel, die ein geleertes Cache-Verzeichnis überstehen; die
  gemeinsame Ablauf-Instanz nach einem Wechsel; die Standardwerte, die genau die bisherigen Verzeichnisse ergeben; ein
  `VarDir`, das mit `..` oder `./` beginnt; leeres `CacheDir` und `LogDir`; das Cache-Verzeichnis eines anderen
  Siteaccess; `eZDir::recursiveDelete()` innerhalb eines absoluten Cache-, Log- und Ablaufverzeichnisses außerhalb des
  Wurzelverzeichnisses und nirgends daneben, ein Eintrag in `AllowedDeletionDirs`, der nicht existiert oder nur ein
  Präfix ist (benötigt ein temporäres Systemverzeichnis außerhalb der Installation).
- `eZDirDeletionGuardTest` (ohne Datenbank): `eZDir::recursiveDelete()` und die darüber laufenden Cache-Leerungen in
  jeder Anordnung: das standardmäßige `var/<site>/cache`, ein `CacheVarDir` innerhalb des Wurzelverzeichnisses und
  eines, das ein Link nach außerhalb ist, ein absolutes `CacheDir` und `CacheVarDir`; innerhalb des Cache und das
  Cache-Verzeichnis als Ganzes, niemals sein Elternverzeichnis, der Cache einer anderen Website oder was daneben
  liegt; die Codepage-, Template-Kompilierungs- und Überschreibungs-Caches und ein Paket-Importverzeichnis, geleert mit
  und ohne `RenameBeforeDelete`; ein `LogDir` oder `CacheDir`, das die Installation ist oder enthält, erlaubt nichts;
  ein nicht existierendes Cache-Verzeichnis erlaubt nichts.
- `eZSiteAccessSitePathsTest` (schreibt eigene Siteaccesses, ohne Datenbank): `eZSiteAccess::change()` zu einer
  Website mit den Einstellungen und danach zu einer ohne, im selben Prozess; `LogVarDir`, `CacheVarDir` und der
  INI-Cache im verschobenen Cache; ein von der Installation gesetztes Verzeichnis, beibehalten und wiederhergestellt;
  eine neue Anfrage ohne die globalen Variablen, die Velocity entfernt; `site.ini` im INI-Cache der eigenen Website
  zwischengespeichert und keine im Cache der vorherigen Website zurückgelassen; zweimal dieselbe Website; das
  Log-Verzeichnis ohne die Funktionsdatei von `eZUpdateDebugLogDirectory()`; eine INI-Datei, die vor dem Wechsel
  gelesen und danach erneut in den Cache der Website gelesen wird.
- `eZSiteLogReadersTest` (ohne Datenbank): Das Setup-Log zählt die Fehler im Log-Verzeichnis der Website; der
  Systembericht nennt dieses Verzeichnis und findet das Cronjob-Log dort; Audit-Einträge bleiben innerhalb von VarDir,
  was immer `LogVarDir` und `LogDir` sagen, auch mit einem absoluten VarDir; `eZSiteAccess::resetSitePaths()`.
- `expVelocityEnginesTest`: Die öffentlichen Caches unterhalb von `CacheVarDir` werden ausgeliefert und sonst nichts
  dort; ein absoluter Pfad, `..` oder andere Zeichen nicht; `./var_cache/` wird als `var_cache` ausgeliefert; ein nur
  für einen Siteaccess gesetztes `CacheVarDir` wird ebenfalls ausgeliefert; das Warm-up setzt die Pfade zurück, bevor
  es die globalen Variablen bereinigt.

## Verwandte Seiten

- [Website-spezifische Einstellungen in Extensions (Hosting mehrerer Websites)](ini-ueberschreibungen-fuer-mehrere-websites.md)
- [Caches vor dem Löschen beiseite umbenennen](cache-leeren-durch-umbenennen.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
