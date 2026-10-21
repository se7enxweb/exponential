# Aufräumen von RSS-Importen

Lesen Sie diese Seite, wenn Ihre Website RSS-Feeds importiert oder wenn Sie die Erweiterung **bccleanuprss** verwendet
haben. Ein RSS-Import legt für jeden Eintrag im Feed ein Objekt an und entfernt nie eines. Eine Website, die einen
lebhaften Feed importiert, wächst daher unbegrenzt: Der Zielordner erreicht Zehntausende Unterelemente, die
Administrationsoberfläche wird darauf langsam, der Suchindex füllt sich mit Einträgen, die niemand mehr liest, und die
nächtliche Sicherung trägt alles mit. Seit 6.0.15 kann der Kernel das für Sie stutzen: **Behalten Sie die neuesten *N*
Einträge jedes aktiven RSS-Imports; entfernen Sie den Rest.**

Der Code ist aus der Erweiterung **bccleanuprss** von Brookins Consulting portiert
(<https://github.com/se7enxweb/bccleanuprss>), GPL v2 oder neuer. Die Auswahlmethode des Originals bleibt erhalten;
siehe [Was beibehalten wurde und was sich geändert hat](#was-beibehalten-wurde-und-was-sich-geändert-hat).

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Neuer Befehl `bin/php/cleanuprss.php` (`./console exp:cleanuprss`), Cronjob-Teil `cleanuprss`, Klasse `expCleanupRSS`, Einstellungen `content.ini [RSSImportCleanupSettings]`. |
| Wer betroffen ist | Niemand, bis Sie es einschalten. Benutzer der Erweiterung bccleanuprss: Ihre Einstellungsdatei `bccleanuprss.ini` wird nicht gelesen. |
| Wie Sie es prüfen | `php bin/php/cleanuprss.php --dry-run --allow-root-user` gibt aus, was es entfernen würde, oder warum es nichts entfernt. |
| Wie Sie es einschalten | Setzen Sie `Enabled`, `ClassIdentifiers[]` und `KeepPerFeed`, machen Sie einen Probelauf und planen Sie dann den Cronjob-Teil ein. |

## In fünf Minuten einrichten

1. Fügen Sie zu `settings/override/content.ini.append.php` hinzu:

   ```ini
   [RSSImportCleanupSettings]
   Enabled=true
   KeepPerFeed=200
   ClassIdentifiers[]
   ClassIdentifiers[]=article
   MoveToTrash=true
   ```

2. Leeren Sie den INI-Cache: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`
3. Machen Sie einen Probelauf und lesen Sie die Liste: `php bin/php/cleanuprss.php --dry-run --allow-root-user`
4. Führen Sie es aus: `php bin/php/cleanuprss.php --allow-root-user`
5. Planen Sie es nächtlich ein: `php runcronjobs.php cleanuprss` in Ihrer crontab.

## Es tut nichts, bis Sie ihm sagen, was es entfernen soll

Das hier entfernt Inhalte. Drei voneinander unabhängige Bedingungen müssen erfüllt sein, bevor es etwas anfasst, und
keine davon ist in einer Installation erfüllt, die nicht dafür eingerichtet wurde:

1. `Enabled=true`
2. `ClassIdentifiers[]` nennt mindestens eine Klasse
3. `KeepPerFeed` ist mindestens 1

Ist eine davon nicht erfüllt, hält der Lauf an und sagt, welche:

```
$ php bin/php/cleanuprss.php
Nothing was removed: RSSImportCleanupSettings/Enabled is not true in content.ini.
```

Das ist kein Fehler — eine Installation, die nicht danach gefragt hat, ist der Normalfall. Sowohl das Skript als auch
der Cronjob enden sauber, daher kann der Teil eingeplant werden, bevor er konfiguriert ist.


## Einstellungen

`settings/content.ini`, neben den eigenen Einstellungen des Imports. Überschreiben Sie sie wie alles andere pro
Siteaccess.

```ini
[RSSImportCleanupSettings]
Enabled=false
KeepPerFeed=100
ClassIdentifiers[]
CleanupUser=admin
LogFile=rsscleanup.log
MoveToTrash=true
```

| Einstellung | Bedeutung |
|---|---|
| `Enabled` | Aus, bis die Website etwas anderes sagt. |
| `KeepPerFeed` | Wie viele Einträge pro Feed behalten werden, die neuesten zuerst. Mindestens 1. |
| `ClassIdentifiers[]` | Die Klassen, die entfernt werden dürfen. **Nichts außerhalb dieser Liste wird angefasst, wie alt es auch ist** — ein Ordner oder eine Seite, die zufällig im Ziel liegt, ist sicher. Leer bedeutet, dass nichts entfernt wird. |
| `CleanupUser` | Das Entfernen wird mit Berechtigungen geprüft, daher läuft der Lauf als dieser Benutzer. Ein fehlendes Konto hält den Lauf mit einer Meldung an. |
| `LogFile` | Wo der Lauf festgehalten wird, unter `var/<var dir>/log/`. Leer schreibt keine Datei. |
| `MoveToTrash` | Ob entfernte Einträge in den Papierkorb gehen, wo sie wiederhergestellt werden können, oder endgültig gelöscht werden. |

Eine funktionierende Konfiguration:

```ini
[RSSImportCleanupSettings]
Enabled=true
KeepPerFeed=200
ClassIdentifiers[]
ClassIdentifiers[]=article
MoveToTrash=true
```

### Zu `MoveToTrash`

`true` ist der Standard, weil eine automatische, geplante, endgültige Löschung von Inhalten ohne Weg zurück kein
vernünftiger Standard ist. Beachten Sie, dass dann stattdessen der Papierkorb wächst — kombinieren Sie es mit dem
Cronjob `trashpurge.php`, oder setzen Sie es auf `false`, sobald Sie der Klassenliste vertrauen.


## Ausführen

### Von Hand

```
php bin/php/cleanuprss.php --dry-run --allow-root-user
php bin/php/cleanuprss.php --allow-root-user
php bin/php/cleanuprss.php --keep=50 --allow-root-user
php bin/php/cleanuprss.php -s my_siteaccess --allow-root-user
./console exp:cleanuprss --dry-run --allow-root-user     # the same script through the console
```

(`--allow-root-user` nur, wenn Sie als root ausführen. Der Probelauf wurde in dieser Installation ausgeführt und gab
mit dem ausgelieferten `Enabled=false` die oben gezeigte Zeile „Nothing was removed“ aus.)

| Option | |
|---|---|
| `--dry-run` | Auflisten, was entfernt würde, und nichts entfernen. |
| `--keep=N` | So viele pro Feed behalten statt der konfigurierten Zahl. Hebt die beiden anderen Bedingungen nicht auf. |

Dazu die gewöhnlichen Skriptoptionen (`-s`, `-q`, `-d`, `--help`).

**Machen Sie auf einer Website, auf der Sie es noch nie ausgeführt haben, immer zuerst einen Probelauf.** Er gibt
jeden Eintrag, den er entfernen würde, mit Namen und Knoten-ID aus.

### Als Cronjob

```
php runcronjobs.php cleanuprss
./console cron:cleanuprss        # the same part through the console
```

Der Teil ist in `settings/cronjob.ini` deklariert:

```ini
[CronjobPart-cleanuprss]
Scripts[]=cleanuprss.php
```

Er ist ein benannter Teil und keines der Standard-`Scripts[]`, daher läuft er nur, wenn er angefordert wird. Nächtlich
ist meist richtig — gedrosselt werden muss der Import, nicht die Uhr.


## Was es tatsächlich tut

Für jeden **aktiven** Import (`eZRSSImport::fetchActiveList()`):

1. Nimm die `destination_node_id` des Imports. Überspringe den Feed, wenn er keine hat oder der Knoten nicht mehr
   existiert — das wird gemeldet, ist aber nicht fatal.
2. Frage die Unterelemente dieses Knotens ab, der konfigurierten Klassen, im Status veröffentlicht, Tiefe 1, **nach
   den neuesten zuerst sortiert und um `KeepPerFeed` versetzt**.
3. Was zurückkommt, ist per Definition der Überschuss; entferne ihn mit einem Aufruf.

Schritt 2 ist der ganze Trick, und er ist der des Originals. Da die Zahl der zu behaltenden Einträge der Offset der
Abfrage ist, gibt es keinen zweiten Durchgang, der entscheidet, was wegkommt, und kein Zeitfenster, in dem die neuesten
Einträge zurückgegeben werden könnten — die zu behaltenden Einträge überspringt die Datenbank.

`Depth => 1` bedeutet nur direkte Unterelemente. Die eigenen Unterelemente eines Eintrags gehen mit, wenn er entfernt
wird, aber ein Unterordner des Ziels wird nicht durchsucht.


## Aus einem Template oder aus PHP

```php
$cleanup = new expCleanupRSS( array( 'dry-run' => true ) );

if ( $cleanup->isEnabled() )
{
    $cleanup->cleanup();
    $counts = $cleanup->counts();   // feeds, examined, removed
    $perFeed = $cleanup->perFeed(); // name, node, surplus
}
else
{
    echo $cleanup->reason();
}
```

Optionen des Konstruktors: `dry-run`, `keep`, `quiet`.


## Was beibehalten wurde und was sich geändert hat

Die Auswahlmethode — nach den neuesten zuerst sortiert, um die Zahl der zu behaltenden Einträge versetzt — ist die des
Originals, und sie ist der Grund, warum es diese Portierung gibt und keine Neufassung.

Alles darum herum hat sich geändert, und die meisten Änderungen waren nicht kosmetisch:

| | Vorher | Jetzt |
|---|---|---|
| **Konstruktor** | `public function BCCleanupRSS()`, ein Konstruktor im Stil von PHP 4 | `__construct()` |
| **Standard-Klassenliste** | `RSSClasses[]=16`, `RSSClasses[]=1` — numerische IDs, die in einer Standardinstallation *article* und *folder* sind | leer, und Kennungen statt IDs |
| **Standardmäßig eingeschaltet** | ja | nein |
| **Entfernen** | `removeSubtrees( ..., false )` — endgültig, nicht konfigurierbar | `MoveToTrash`, standardmäßig in den Papierkorb |
| **Probelauf** | keiner | `--dry-run` |
| **Fehlender Benutzer zum Aufräumen** | fataler Fehler in der nächsten Zeile | hält den Lauf an und sagt das |
| **Aufrufe zum Entfernen** | ein `removeSubtrees()` pro Eintrag, jedes mit Leeren der Caches und Neuindizierung | ein Aufruf pro Feed |
| **Ausgabe im Terminal** | `global $cli`, nur gesetzt, wenn der Aufrufer eines erzeugt hat | `eZCLI::instance()` |
| **Einstellungen** | `bccleanuprss.ini` | `content.ini [RSSImportCleanupSettings]`, neben `[RSSImportSettings]` |

Der Konstruktor im Stil von PHP 4 verdient eine Erklärung: **PHP 8 hat sie entfernt**, daher führte
`new BCCleanupRSS()` diese Methode nicht mehr aus. Jede Eigenschaft, die sie setzte — `rssLimit`, `rssClasses`,
`logFile` — blieb undefiniert, und die Klasse konnte auf keiner unterstützten PHP-Version überhaupt funktionieren.

Auch die Standard-Klassenliste verdient eine Erklärung. `RSSClasses[]=1` ist in einer Standardinstallation *folder*.
Standardmäßig eingeschaltet, gegen ein Ziel, das Ordner enthält, entfernt das Ordner.

### Benennung

Namen mit dem Präfix `bc` sind weg, da dies keine Erweiterung eines Anbieters mehr ist: `BCCleanupRSS` →
`expCleanupRSS`, `bccleanuprss.ini` → ein Block in `content.ini`. Die Copyright-Zeilen von Brookins Consulting bleiben
in jeder portierten Datei erhalten — das Branding geht, die Namensnennung nicht.


## Dateien

| Datei | |
|---|---|
| `kernel/classes/expcleanuprss.php` | `expCleanupRSS` |
| `bin/php/cleanuprss.php` | das Kommandozeilenskript |
| `cronjobs/cleanuprss.php` | der Cronjob-Teil |
| `settings/content.ini` | `[RSSImportCleanupSettings]` |
| `settings/cronjob.ini` | `[CronjobPart-cleanuprss]` |
| `autoload/ezp_kernel.php` | der Eintrag in der Klassenzuordnung |


## Tests

Ein privates Funktionsskript baut seine eigene Testumgebung auf (einen Ordner, ein aktives `eZRSSImport`, das darauf
zeigt, zwölf im Abstand von einer Stunde veröffentlichte Artikel und einen Ordner, der überleben muss, weil seine
Klasse nicht auf der Liste steht) und prüft, dass jede der drei Bedingungen für sich ablehnt, dass ein Probelauf den
Überschuss findet und nichts entfernt, dass ein echter Lauf genau die neuesten fünf übrig lässt, dass die nicht
aufgeführte Klasse unberührt bleibt, dass ein zweiter Lauf nichts findet und dass das Entfernte aus dem Papierkorb
wiederhergestellt werden kann. Es wird nicht ausgeliefert. Um das Verhalten selbst zu prüfen, folgen Sie „Ausführen“,
zuerst mit `--dry-run`, auf einer Kopie der Installation.

## Verwandte Seiten

- [RSS: Feed-Liste, Podcasts und sicherere Exporte](../../funktionen/6.0/rss-podcast-und-feed-liste.md) und [OPML-Exporte](opml.md)
- [September 2026, erste Hälfte: 15. September](../../geschichte/2026/2026-09a.md#15-september-überall-seitenweise-anzeige)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- [Konsole für Cronjobs](../../funktionen/6.0/cronjobs-in-der-konsole.md)
- [Spezifikation der Syndizierung](../../spezifikationen/6.0/syndikation.md)
