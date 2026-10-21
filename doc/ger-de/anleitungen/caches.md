# Caches: was jeder enthält und wie Sie ihn leeren

Diese Anleitung erklärt die Administrationsseite **Setup > Caches** (`/setup/cache`): die Caches einer Installation in
Gruppen, was jeder enthält, wie groß er ist, wann er zuletzt geleert wurde, was sein Leeren unter Apache mit PHP-FPM
und unter Exponential Velocity erreicht, und die Befehle, die dasselbe in einer Shell tun.

Sie richtet sich an Administratoren. Alles Folgende wurde am Code geprüft (`kernel/classes/expcachecatalogue.php`,
`kernel/classes/expcachemanager.php`, `kernel/private/classes/views/setup/cache.php`) und am 6. Oktober 2026 auf dem
Demonstrationsserver ausprobiert, wobei nur der Template-Block-Cache und die Bildvarianten
geleert wurden.

[Anleitungen](README.md) · Verwandte Anleitungen: [Systeminformationen](systeminformationen.md),
[Betrieb einer Site](betrieb-einer-website.md), [Caches vorwärmen](caches-vorwaermen.md), [Bereitstellung](bereitstellung.md)

## Kurz gesagt

- **Setup > Caches** erfordert die Richtlinie `setup/managecache`. Jeder Knopf sendet das Formular mit seinem
  Formular-Token; eine Anfrage ohne Token wird abgewiesen.
- Die Seite listet jeden Cache in sechs Gruppen auf: **Inhalte und Ansichten**, **Templates**, **INI und Einstellungen**, **Bilder**,
  **Seiten, Abfragen und der Server**, **Sonstige** (Caches von Extensions, die in keine andere Gruppe passen).
- Zu jedem Cache: sein Name und was er enthält, seine ID und seine Tags, sein Verzeichnis (relativ zur Installation), seine
  Größe und Anzahl der Dateien (**Größen messen**, `/setup/cache/(sizes)/1`, höchstens drei Sekunden), wann und von wem er zuletzt
  geleert wurde, sofern die Installation das erfasst, und Hinweisschilder: **Velocity nach dem Leeren neu starten**, **auch im Antwort-Cache
  von Velocity**, **in den Einstellungen deaktiviert**.
- Leeren Sie **ausgewählte** Caches (ankreuzen, **Ausgewähltes leeren**, Liste bestätigen), eine **Gruppe** (**Diese Gruppe leeren …**),
  nach **Tag** (**Inhalts-/Template-/INI-Caches leeren …**) oder **alle** (**Alle Caches leeren …**). Jedes Leeren nennt
  vorher an Ort und Stelle, was entfernt wird, und endet mit einer Meldung: was geleert wurde, wie viele Millisekunden es dauerte, was als
  Nächstes zu tun ist, und den Befehl.
- **Suchen** schränkt die Liste nach Name, ID, Tag oder Verzeichnis ein; die Knöpfe unter **Gruppen** zeigen eine einzelne Gruppe.
- Die Cache-Dateien liegen in `var/`, das Apache mit PHP-FPM und Velocity gemeinsam nutzen: Ein hier geleerter Cache erreicht beide.
  Zwei Dinge nicht: Velocity hält Einstellungen im Speicher (starten Sie ihn nach dem Leeren der INI-Caches neu) und beantwortet Seiten
  einige Sekunden lang aus seinem eigenen Antwort-Cache (leeren Sie ihn in der letzten Gruppe oder mit `./console exp:velocity cache clear`).

## 1. Die Übersicht

Die Zahlen oben zählen die Caches, die aktivierten Caches und die Gruppen, zeigen die Gesamtgröße und die Zahl der Dateien, sobald
die Größen gemessen sind, und den letzten Zeitpunkt, zu dem Inhaltsansichten, Template-Blöcke oder Benutzerinformationen verworfen wurden.

## 2. Die Gruppen

| Gruppe | Caches (IDs) |
|---|---|
| Inhalte und Ansichten | `content`, `classid`, `sortkey`, `urlalias`, `rss_cache`, `user_info_cache`, `content_tree_menu`, `state_limitations`, `content_language`, `rest` |
| Templates | `template`, `template-block`, `template-override`, `texttoimage`, `design_base`, `ezjscore-packer` |
| INI und Einstellungen | `global_ini`, `ini`, `codepage`, `chartrans`, `active_extensions`, `translation`, `sslzones`, `rest-routes` |
| Bilder | `imagealias` |
| Seiten, Abfragen und der Server | `exphttpcache`, `querycache` und die eigenen Caches des Servers (siehe unten) |
| Sonstige | Caches von Extensions, die hier eingeordnet werden, sofern ihr Tag nicht content, template, ini, i18n oder image ist |

**Seiten, Abfragen und der Server** enthält außerdem folgende Zeilen mit eigenen Knöpfen:

- **Ganze Seiten (HTTP-Cache)**: HTTP-Cache leeren, Tote Einträge entfernen, Zähler zurücksetzen;
- **Ergebnisse von Datenbankabfragen (SQL-Query-Cache)**: Query-Cache leeren, Die Zähler zurücksetzen;
- **SQL-Profil jeder Anfrage**: an oder aus;
- **Antwort-Cache von Velocity**: seine Dateien und seine Größe, wann er zuletzt geleert wurde, und **Antwort-Cache von Velocity leeren**;
- **OPcache** und **APCu** des Serverprozesses, der die Seite beantwortet hat: OPcache zurücksetzen, APCu leeren.

Die Knöpfe für HTTP-Cache, Query-Cache und SQL-Profil sind dieselben wie unter Setup > Systeminformationen: Beide Seiten
senden dieselben Felder an `expCacheManager::sharedActionFromPost()`.

## 3. Zuletzt geleert

Die Spalte gibt an, wann jeder Cache zuletzt geleert wurde und, laut Audit-Protokoll, von wem: „gerade eben“,
„vor 12 Minuten“, „vor 3 Stunden“, sonst das Datum, darunter „von admin“ oder „aus einer Shell, os:root“.

- **Das Audit-Protokoll** (`system.cache.clear`) erfasst jedes Leeren: über diese Seite, Setup > Systeminformationen,
  `./console exp:cache` und `bin/php/ezcache.php` (ein Eintrag pro Lauf, also auch das Leeren durch
  `exp:velocity deploy`). Eine Shell wird mit ihrem Betriebssystembenutzer benannt (`os:root`, `os:www-data`). Ein Lauf, der
  nichts leert (`--list-ids`, `--list-tags`, eine unbekannte ID), schreibt keinen Eintrag; kann der Eintrag nicht geschrieben werden, werden die
  Caches trotzdem geleert und eine Warnung geht an stderr.
- Die Seite liest es einmal pro Ansicht: eine Abfrage des Audit-Index (die neuesten 200 Leerungen) und das Ende der neuesten
  Systemdatei jedes Audit-Verzeichnisses (jeweils höchstens 3000 Zeilen), für Leerungen, die der Indexer noch nicht erreicht hat.
  Die Einträge verteilen sich auf `var/log/audit` (Befehle ohne Siteaccess) und `var/<site>/log/audit`; beide werden
  gelesen. Wer geleert hat, sehen Benutzer, die den Systemkanal des Audits lesen dürfen (`audit/read`).
- Der Kernel erfasst außerdem einen Ablaufzeitpunkt für Inhaltsansichten, Template-Blöcke, Benutzerinformationen, Übersetzungen,
  Bildvarianten und das Inhaltsbaum-Menü. Der neuere der beiden Zeitpunkte gilt; ein Audit-Eintrag bis zu fünf Sekunden vor
  dem Ablauf ist dasselbe Leeren und nennt, wer es ausgelöst hat. Die Seite, die das Ergebnis eines Leerens zeigt, nennt Sie bereits,
  bevor Ihr eigener Eintrag geschrieben ist.
- Ein Cache, dessen Leeren auf keine der beiden Arten erfasst wurde, zeigt „nicht erfasst“.

## 4. Velocity

- **INI und Einstellungen** (`global_ini`, `ini`, `active_extensions`, `sslzones`): Ein laufendes Velocity hat die
  Einstellungen in den Speicher gelesen. Führen Sie nach dem Leeren `./console exp:velocity restart --allow-root-user` aus.
- **Inhalte, Templates und der HTTP-Cache**: Der Antwort-Cache von Velocity kann eine vor dem Leeren erzeugte Seite noch
  für seine Lebensdauer ausliefern (einige Sekunden auf dem Demonstrationsserver). Leeren Sie ihn mit dem Knopf oder mit `./console exp:velocity cache clear`.
- **OPcache und APCu** gehören zu dem Serverprozess, der geantwortet hat; der andere Server behält seine eigenen.

## 5. In einer Shell

```
php bin/php/ezcache.php --clear-id=template-block,content --allow-root-user   one cache or several, by id
php bin/php/ezcache.php --clear-tag=template --allow-root-user                 by tag
php bin/php/ezcache.php --clear-all --allow-root-user                          everything
./console exp:cache clear --id=content --dry-run --allow-root-user             what would be cleared, with sizes
./console exp:velocity cache clear                                            Velocity's response cache
./console exp:velocity restart --allow-root-user                               Velocity, after settings changed
```

Jede Gruppe und jede Ergebnismeldung zeigt den Befehl für genau die Caches, die sie leert.

## 6. Häufige Fragen

**Eine Seite zeigt nach dem Leeren noch die alte Fassung.** Leeren Sie den Antwort-Cache von Velocity oder warten Sie einige Sekunden. Handelt
es sich um eine Änderung der Einstellungen, starten Sie Velocity neu.

**Das Protokoll meldet „Expiring all view cache“.** Eine Änderung hat so viele Seiten betroffen, dass das einzelne Leeren länger dauern würde
als das Leeren aller: Wenn die View-Caches von `site.ini [ContentSettings] CacheThreshold` Knoten oder mehr
(Standard 250) geleert werden sollen, wird stattdessen jeder View-Cache der Site verworfen, und die nächsten Besuche jeder Seite
rendern sie neu. Die Debug-Ausgabe enthält dann eine Warnung mit der Anzahl der Knoten, auch in `var/log/warning.log`,
solange die Debug-Ausgabe eingeschaltet ist, oder immer mit `[DebugSettings] AlwaysLog[]=warning`:

```
Expiring all view cache: the view cache of 312 nodes was to be cleared, which reaches site.ini [ContentSettings] CacheThreshold=250
```

Eine gelegentliche Warnung nach dem Verschieben eines großen Teilbaums oder dem Veröffentlichen eines Objekts, auf das sich viele Seiten beziehen, ist zu erwarten. Wenn
sie bei gewöhnlichen Bearbeitungen erscheint, prüfen Sie die Beziehungen des Objekts und `viewcache.ini` (welche Knoten mit ihm
geleert werden), oder erhöhen Sie `CacheThreshold` in einem Override. Ein Upgrade ändert nur die Stufe der Meldung (sie war
eine Debug-Meldung); Dank an Mugo Web für den LoveStack-Commit f9691221a3, der sie zu einer Warnung gemacht hat.

**Ein Cache ist ausgegraut.** Er ist in den Einstellungen deaktiviert (zum Beispiel der Cache der SSL-Zonen), es gibt also nichts zu
leeren.

**Größen messen zeigt „≥“.** Das Zeitbudget von drei Sekunden war aufgebraucht, bevor jede Datei gezählt war.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
