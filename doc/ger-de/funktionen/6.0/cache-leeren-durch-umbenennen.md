# Leeren von Caches, das Verzeichnisse beiseiteschiebt

Diese Seite ist für Administratoren stark besuchter oder großer Websites, die Caches leeren, während die Website
ausliefert. Das Leeren eines großen Caches löschte ihn früher Datei für Datei innerhalb der Anfrage. Während das lief,
erzeugten andere Anfragen den Cache bereits wieder im selben Baum, und das Löschen konnte erfassen, was sie gerade
geschrieben hatten; die Anfrage, die ihn leerte, wartete auf alles. Seit dem 24. und 25. September 2026 **benennt** ein
Leeren das Cache-Verzeichnis in einem Schritt aus dem Weg und löscht es von dort. Das ist standardmäßig eingeschaltet; es
gibt nichts einzurichten.

## Was Sie bekommen

- Ein Leeren des Caches (Setup > Caches, `php bin/php/ezcache.php`, `exp:cache`, `eZCache::clearAll()` und Verwandte)
  kehrt schnell zurück, auch bei einem Cache mit Hunderttausenden von Dateien.
- Nichts, was erzeugt wird, während die alten Dateien gelöscht werden, wird vom Löschen erfasst.
- Ein Leeren in der Administrationsoberfläche lässt nichts zurück. Der Ansichts-Cache, Cache-Block, die
  Benutzerinformationen, der Übersetzungs- und der Packer-Cache wurden früher nicht mehr ausgeliefert (ein Zeitstempel
  in `expiry.php`), blieben aber auf der Platte, bis der Cronjob `cache_cleanup` lief.
- Der Cache für Text als Bild und der öffentliche Packer-Cache von `ezjscore` (JavaScript und Stylesheets) werden
  beiseite umbenannt statt an Ort und Stelle gelöscht.

## Den Aufräum-Cronjob ausführen

Das Leeren des Ansichts-Caches oder des Cache-Blocks verschiebt nur einen Zeitstempel, sodass Dateien blieben, bis
derselbe Schlüssel wieder erzeugt wurde, und Schlüssel, die nie wiederkommen, blieben für immer. Der Cronjob-Teil
`cache_cleanup` entfernt:

- Dateien, die älter sind als der globale Ablauf ihres Caches;
- Dateien, die älter sind als `MaxAge`;
- die Verzeichnisse, die dadurch leer werden;
- was `DelayedCacheBlockCleanup` in `template-block-expiry` abstellt.

Er läuft für das var-Verzeichnis des angegebenen Siteaccess, führen Sie ihn also einmal pro `VarDir` aus:

```bash
php runcronjobs.php -s site cache_cleanup
```

Nach einem globalen Leeren schiebt das Aufräumen `content/` und `template-block/` mit einer Umbenennung beiseite, statt
jede Datei anzusehen (getestet mit 200.000 Dateien; ein zweiter, überlappender Lauf begann mit 123.121 übrigen, und beide
endeten ohne Rest). Welches Leeren zuletzt behandelt wurde, steht in `cachecleanup-state.json`, nicht in einer PHP-Datei,
sodass der Opcode-Cache keine alte Kopie ausliefern kann. Der Cronjob liest `expiry.php` über seinen eigenen
`eZExpiryHandler`, weil `runcronjobs.php` die gemeinsame Instanz vor dem Wechsel zum Siteaccess erzeugt, sodass diese
die Zeitstempel des Standard-var-Verzeichnisses hält.

## Einstellungen

| Datei | Block | Schlüssel | Standard | Geltungsbereich | Bedeutung |
|---|---|---|---|---|---|
| `settings/site.ini` | `FileSettings` | `RenameBeforeDelete` | `enabled` | global | Ein Cache-Verzeichnis vor dem Löschen beiseite umbenennen. `disabled` löscht Datei für Datei. |
| `settings/site.ini` | `FileSettings` | `RenameExpiredCaches` | `enabled` | global | Auch Ansichts-Cache, Cache-Block, Benutzerinformationen und Übersetzungs-Cache beiseiteschieben, wenn sie in der Administrationsoberfläche oder mit `ezcache.php` geleert werden. Braucht `RenameBeforeDelete`. Das Veröffentlichen von Inhalten verschiebt weiterhin nur Zeitstempel. |
| `settings/cronjob.ini` | `CacheCleanupSettings` | `MaxAge` | `172800` | global | Sekunden, nach denen eine Datei unabhängig von ihrem Ablauf entfernt wird. `0` entfernt nur abgelaufene Dateien. |
| `settings/cronjob.ini` | `CacheCleanupSettings` | `IterationSleep` | `0` | global | Mikrosekunden Pause nach je 1000 Dateien |
| `settings/cronjob.ini` | `CacheCleanupSettings` | `RenameAfterClear` | `enabled` | global | Nach einem globalen Leeren das ganze Verzeichnis beiseiteschieben. `disabled` geht Datei für Datei vor. |
| `settings/cronjob.ini` | `CronjobPart-cache_cleanup` | `Scripts[]` | `cachecleanup.php`, `httpcache_cleanup.php` | global | Was der Teil ausführt |

## Wie es funktioniert

`eZCacheTrash` benennt ein Verzeichnis in `.cleanup-trash` im Cache-Verzeichnis um, benannt nach dem Cache und der Zeit,
zum Beispiel `template-compiled-20260924-151112-<id>`, `override-...`, `ini-...`, und löscht es von dort. Die ID hält
zwei Leerungen in derselben Sekunde auseinander.

- Zwischen `eZCacheTrash::begin()` und `end()` wird nichts gelöscht. Erst kommen alle Umbenennungen, dann das Durchgehen
  Datei für Datei der Caches, die nicht geleert wurden, dann das Löschen, sodass kein Cache hinter einem anderen wartet.
  `end()` läuft seit dem 24. September in einem Block `finally`. Vorher ließ eine Ausnahme in einem Cache-Element die
  Tiefe erhöht, und nichts, was dieser Prozess danach beiseiteschob, wurde je gelöscht.
- Ein Verzeichnis außerhalb des Cache-Verzeichnisses, etwa der globale INI-Cache in `var/cache`, kommt in den Papierkorb
  des Verzeichnisses, in dem es liegt. Wo die Umbenennung ein Dateisystem überqueren würde (ein Teil des Caches ist auf
  eine andere Platte verlinkt), wird ein Papierkorb neben dem Verzeichnis verwendet. Ein Verzeichnis, das selbst ein
  Link ist, wird in einen Papierkorb darin geleert. Das Löschen folgt nie einem Link, und das Cache-Verzeichnis selbst
  wird nie umbenannt.
- Ein Papierkorb-Verzeichnis bleibt an Ort und Stelle, und nur sein Inhalt geht, sodass ein überlappendes Leeren immer
  hineinbenennen kann.
- Ein lokales `SharedTranslationCacheDir` bleibt unberührt: Es gehört jeder Installation, die es teilt.

## Grenzen

Nur der Dateisystem-Handler (`eZFSFileHandler`) benennt um. Ein Cache im Cluster läuft über seinen eigenen Handler.

## Verwandte Seiten

- [Cache-Konsole](../../../eng-us/bc/6.0/cache-console.md) (`exp:cache`)
- [Generator für den statischen Cache](generator-fuer-den-statischen-cache.md), [Antwort-Cache von Velocity](velocity-antwort-cache.md)
- [Velocity: Exponential in einem Webserver mit dauerhaften Workern betreiben](velocity-server-mit-dauerhaften-workern.md)
- [Verhaltensänderungen, 16. bis 30. September 2026](../../../eng-us/bc/6.0/behaviour-changes-2026-09b.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- [Chronik, 16. bis 30. September 2026](../../../eng-us/history/2026/2026-09b.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
