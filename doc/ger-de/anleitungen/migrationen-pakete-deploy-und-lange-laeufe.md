# Migrationen: Pakete, Deploys und lange Läufe

Diese Seite ist die Anleitung zu Migrationen, die laufen, ohne dass jemand sie von Hand startet, und zu Läufen, die
lange dauern: die Migrationen, die ein Paket mitbringt und die bei seiner Installation laufen; der Lauf des
Kickstarters; der Lauf eines Deploys (`exp:velocity deploy --migrate`); Läufe in Teilen, gedrosselt, im
Hintergrund, sowie PHP-Migrationen, die viele Einträge in Stapeln abarbeiten und dort weitermachen, wo sie aufgehört
haben; der Schritt, der Code- und Designdateien schreibt; und eine websiteweite Aufzeichnung für ein Zeitfenster.

[Anleitungen](README.md) · [Migrationen](migrationen.md) · Siehe auch: [PHP-Migrationen](migrationen-php.md),
[Zurücknehmen und Sicherungspunkte](migrationen-rueckgaengig-und-captions.md), [Das Migrations-Repository](migrationen-repositories.md)

## Migrationen in einem Paket

Ein Paket bringt Migrationen in einem Verzeichnis `migrations/` auf seiner obersten Ebene mit, neben seiner
`package.xml` oder `package.json`, benannt wie jede Migrationsdatei (`YYYYMMDDHHMMSS_name.<typ>`). Ein exportiertes
Paket nimmt das Verzeichnis mit: Der Export kopiert jedes Verzeichnis der obersten Ebene des Pakets.

```
mysite_news/
  package.xml
  ezcontentclass/...
  migrations/
    20261101120000_news_folder.yml
    20261101120100_news_index.sql
```

Die Installation des Pakets (Setup > Pakete, oder ein Paket, das der Einrichtungsassistent oder der Kickstarter
Element für Element installiert) führt sie aus, sobald seine Elemente installiert sind:

```
./console exp:migration:migrate --path=var/storage/packages/<repository>/mysite_news/migrations --apply \
    --include-uncommitted --no-push --format=json
```

in einem eigenen Prozess, als der Benutzer, der das Paket installiert (auf der Konsole `[RunSettings] RunAsUser`). Nur
die Migrationen des Pakets laufen; eine, die eine noch nicht angewendete Migration voraussetzt, hält den Lauf mit
einer Meldung an, die sie nennt. Der Lauf ist ein gewöhnlicher Lauf: Er nimmt die Sperre der Installation, seine
Sicherungspunkte und seinen Eintrag, und seine Caches werden geleert wie nach jedem Lauf.

Schlägt eine Migration fehl, gilt das Paket als **nicht installiert**: Der Sicherungspunkt von vor dem Lauf wird
wiederhergestellt, und eine erneute Installation des Pakets führt aus, was noch aussteht. Ein Paket, das später noch
einmal installiert wird, findet seine angewendeten Migrationen erledigt und führt nichts aus.

Die Migrationen jedes importierten Pakets findet auch jeder Lauf (die Quelle `package:<name>`), so dass
`./console exp:migration:migrate --apply` die anwendet, die eine Paketinstallation nicht ausgeführt hat.

| Einstellung (`migration.ini [InstallSettings]`) | Vorgabe | Was sie tut |
|---|---|---|
| `RunOnPackageInstall` | `enabled` | die Migrationen eines Pakets bei seiner Installation ausführen; `disabled`: sie dem nächsten Lauf überlassen |
| `RunOnKickstart` | `enabled` | der Kickstarter führt jede ausstehende Migration aus, nachdem die Websites angelegt sind |
| `RunOptions[]` | `include-uncommitted`, `no-push` | die Schalter dieser Läufe: `no-caption`, `no-restore`, `no-commit`, `no-push`, `no-tag`, `include-uncommitted` |
| `Timeout` | `3600` | Sekunden, die ein Lauf bei der Installation dauern darf |

`include-uncommitted` ist voreingestellt, weil die Installation Einstellungen, Design- und Erweiterungsdateien
schreibt, die ein Lauf sonst als nicht committete Änderungen ablehnen würde (sie bleiben im Sicherungspunkt erhalten).

## Der Kickstarter

`php bin/php/kickstarter.php run --apply` führt `./console exp:migration:migrate --apply` aus, sobald die Websites
angelegt und die Hilfehandbücher eingereiht sind, solange der Wartungsmodus noch an ist. Er gibt
`Migrations: 3 migrations applied (run <run id>)` aus, oder `Migrations: FAIL ...` mit dem Befehl, der weitermacht,
sobald die Ursache behoben ist:

```
./console exp:migration:status
./console exp:migration:resume --apply
```

Ein fehlgeschlagener Lauf beendet den Kickstarter mit Exit-Code 1; die Website ist installiert, und der
Sicherungspunkt von vor dem Lauf ist wiederhergestellt.

## In einem Deploy

`exp:velocity deploy --migrate` führt die ausstehenden Migrationen als Schritt des Deploys aus: nach den Autoloads und
den Caches von Einstellungen und Templates (damit die neuen Klassen und Einstellungen gefunden werden) und bevor das
Engine-Archiv, PHP-FPM und Velocity den neuen Code ausführen. Schlägt eine Migration fehl, hält der Deploy dort an,
und nichts nach dem Schritt wird getan.

```
./console exp:velocity deploy --migrate --dry-run --allow-root-user
./console exp:velocity deploy --migrate --allow-root-user
```

Der Schritt ist `./console exp:migration:migrate --apply`, mit seinen Sicherungspunkten, seinem Eintrag, Commit und
Push wie auf der Konsole. `velocity.ini [DeploySettings] Migrate=enabled` lässt jeden Deploy ihn ausführen; die
Vorgabe ist `disabled`, und `--no-migrate` überspringt ihn für einen Deploy. Die Caches, die die Schritte eines Laufs
berührt haben, leert der Lauf selbst; auf einem anderen Server leert `exp:velocity deploy --caches=<liste>` dieselbe
Liste (der Bericht des Laufs nennt sie).

## Lange Läufe

Ein Lauf vieler Migrationen, oder einer, die viele Objekte ändert, lässt sich aufteilen und drosseln. Jede Einstellung
unten steht in `migration.ini [RunSettings]`, und die gleichnamige Option gewinnt für einen Lauf
(`exp:migration:migrate`, `exp:migration:resume` und die Läufe, die Setup > Migrationen startet).

| Option | Einstellung | Was sie tut |
|---|---|---|
| `--max-migrations=<n>` | `MaxMigrations` | höchstens n Migrationen anwenden; der Rest wird **verschoben** |
| `--max-time=<sekunden>` | `MaxTime` | nach dieser Zeit keine weitere Migration beginnen (die laufende wird beendet); der Rest wird verschoben |
| `--throttle=<sekunden>` | `Throttle` | eine Pause zwischen zwei Migrationen und zwischen den Stapeln einer PHP-Migration (`0.5` ist eine halbe Sekunde, höchstens 60) |
| `--nice=<n>` | `Nice` | eine niedrigere Priorität für den Lauf, 1 bis 19 wie `nice(1)` (Konsole und Hintergrundläufe, nie eine Web-Anfrage) |

Eine verschobene Migration bleibt ausstehend: Der Lauf endet mit Exit-Code 0 und der Warnung `3 migrations postponed by
the limit of 20 migrations per run: the next run (./console exp:migration:migrate --apply) goes on with them`. Ein
nächtlicher Cronjob, der anwendet, was in ein Zeitfenster passt:

```
30 1 * * * cd /pfad/zur/website && ./console exp:migration:migrate --apply --max-time=3000 --throttle=1 --nice=10 --format=json >> var/log/migrations.log
```

Die erste Migration eines Laufs beginnt immer, wie lange die Zeit auch schon um ist, so dass jeder Lauf vorankommt.

### PHP-Migrationen in Stapeln

Eine PHP-Migration, die Tausende Objekte ändert, tut das mit `batches()` einen Stapel nach dem anderen, jeden Stapel
für sich committet. Nach jedem Stapel hält sie in ihrem **Checkpoint** fest, wie viele Einträge sie bearbeitet hat
(`var/<site>/migration/checkpoints/<migrations-id>.json`); ein Lauf, der fehlschlägt, angehalten wird oder dessen Zeit
um ist, macht beim nächsten Mal dort weiter, statt die ersten Stapel noch einmal zu bearbeiten. Der Runner entfernt
den Checkpoint, sobald die Migration angewendet ist.

```php
final class ReindexNewsTeasers extends \expMigrationPhp
{
    public function transactional()
    {
        return false; // batches() committet jeden Stapel für sich
    }

    public function up( $context )
    {
        $db = $context->db();
        $count = $this->batches( $context,
            function ( $offset, $limit ) use ( $db ) {
                return $db->arrayQuery( "SELECT id FROM ezcontentobject WHERE contentclass_id = 42 ORDER BY id",
                                        array( 'offset' => $offset, 'limit' => $limit ) );
            },
            function ( $row ) {
                // ein Objekt: ändern
            }, 200 );
        return array( "updated $count objects" );
    }
}
```

`$fetch` erhält den Offset (die in allen bisherigen Läufen bearbeiteten Einträge) und die Stapelgröße und gibt die
nächsten Einträge in einer festen Reihenfolge zurück; weniger als die Stapelgröße beendet die Arbeit. Ein Handler, der
die bearbeiteten Einträge entfernt, fragt stattdessen nach dem, was übrig ist, und übergeht den Offset. Zwischen den
Stapeln pausiert `batches()` so lange wie `--throttle`. Ein Probelauf bearbeitet nichts. Eine Migration, deren
`transactional()` true ist, kann es nicht verwenden: Die Stapel sind ihre Transaktionen.

Den Checkpoint selbst erreicht eine Migration als `$this->checkpoint( $context )` (`get()`, `set()`, `values()`,
`clear()`), für einen eigenen Cursor.

### Im Hintergrund

Ein Lauf, den Setup > Migrationen startet, läuft immer im Hintergrund, als eigener Konsolenprozess, und seine Seite
verfolgt ihn ([In der Administration](migrationen.md#in-der-administration)). Ist expqueue aktiv, kann der Lauf
stattdessen in dessen Warteschlange gehen:

```
; settings/override/migration.ini.append.php
[AdminSettings]
Background=queue

; settings/override/expqueue.ini.append.php
[JobType_migration.run]
Handler=expMigrationQueueHandler
Class=bulk
Timeout=86400
Description=A migration run started in Setup > Migrations
```

Der Worker der Warteschlange führt dann dieselbe Befehlszeile aus, und die Seite des Laufs verfolgt ihn genauso; bis
der Worker ihn übernimmt, gilt der Lauf als laufend, so dass ein zweiter abgelehnt wird. Ist expqueue nicht aktiv,
sind seine Tabellen nicht installiert, fehlt der Auftragstyp oder läuft kein Worker, startet der Lauf wie bisher als
Prozess. Ein fehlgeschlagener Lauf lässt den Auftrag der Warteschlange endgültig fehlschlagen: Ein Migrationslauf wird
nie von selbst wiederholt.

### In der Administration

Das Formular von Setup > Migrationen > Ausführen hat die vier Optionen für lange Läufe als Felder, in der Karte „Ein
langer Lauf in Teilen“: höchstens so viele Migrationen, keine weitere Migration nach (Sekunden), Pause zwischen
Migrationen (Sekunden) und niedrigere Priorität (1 bis 19). Ein leeres Feld behält die Einstellung aus
`[RunSettings]`, die als Platzhalter im Feld steht; ein Wert wird geprüft wie vom Befehl und an den Lauf im Hintergrund
weitergegeben (`--max-migrations`, `--max-time`, `--throttle`, `--nice`). Ein Lauf, der an einer Grenze anhält, endet
ohne Fehler, und die Migrationen, die er übrig lässt, bleiben ausstehend für den nächsten Lauf oder ein Fortsetzen.

## Code- und Designdateien

Migrationen ändern Daten, Einstellungen und die Dateien von `var/storage`; Code- und Designdateien gehen über git (der
Sicherungspunkt nimmt die Codebäume mit). Muss eine Migration eine solche Datei selbst schreiben, etwa ein Template,
das eine Erweiterung überschreibt, tut es der Schritttyp [`code_file`](../beispiele/migrationsschritte/code_file.md):

```yaml
- type: code_file
  mode: update                     # create, update, ensure (Vorgabe) oder delete
  path: design/mysite/templates/page_footer.tpl
  source: files/page_footer.tpl    # neben der Migration; oder content: "..."
  expect: 9a0b...                  # das SHA-256, das die Datei vorher haben muss (absent: sie darf nicht existieren)
  checksum: 3f1c...                # das SHA-256, das der neue Inhalt haben muss
```

- Nur unterhalb der Ordner von `migration.ini [FileStepSettings] AllowedRootList[]` (`design` und `extension` als
  Vorgabe), nie ein Ordner selbst, nie `..`, ein absoluter Pfad, eine URL, ein `.git`-Ordner, `settings/override`,
  `var` oder `vendor`, und nie über einen Link, der aus dem Ordner hinausführt. Eine leere Liste schaltet den Schritttyp
  ab.
- Die Datei, wie sie war, wird zuerst nach `var/backup/migration/files/<Migrations-ID>/<Pfad>` kopiert (nur für ihren
  Eigentümer lesbar), und die Änderung steht in `var/backup/migration/files/<Migrations-ID>.json`: ob die Datei
  existierte, ihr SHA-256 vorher und nachher, ihr Modus. Geschrieben wird atomar, mit dem Modus der ersetzten Datei.
- Schlägt ein späterer Schritt derselben Migration fehl, legt der Runner die Dateien zurück (die Transaktion nimmt sie
  nicht zurück); die Meldungen des Laufs nennen sie.
- Der Schritt nimmt sich selbst zurück: `exp:migration:rollback` der Migration legt die alte Datei zurück oder entfernt
  die Datei, die die Migration angelegt hat, aber nur, solange die Datei noch die ist, die die Migration geschrieben
  hat. Eine seither bearbeitete Datei bleibt unberührt, und das Zurücknehmen sagt es.
- Die Caches, die die Datei betrifft, werden nach dem Lauf geleert: Templates bei `.tpl`, der INI-Cache bei `.ini` und
  `.ini.append.php`, die gepackten Stylesheets und Skripte bei `.css` und `.js`. Eine neue PHP-Klasse braucht einen
  `autoload`-Schritt.

## Ein Aufzeichnungsfenster auf Zeit

Eine websiteweite Aufzeichnung ([Erzeugen und aufzeichnen](migrationen-erzeugen-und-aufzeichnen.md)) lässt sich für ein
Zeitfenster öffnen: Sie schließt sich selbst, wenn das Fenster endet, so bleibt die Aufzeichnung eines Nachmittags
Redaktionsarbeit nicht offen.

```
./console exp:migration:record:start --site-wide --for=120 --label="Nachmittag der Redaktion" --apply
./console exp:migration:record:start --site-wide --until="2026-11-20 18:00" --apply
```

Setup > Migrationen > Aufzeichnung hat beim websiteweiten Fenster das Feld „Schließt sich selbst nach (Minuten,
optional)“. Ein Fenster ist höchstens 31 Tage lang. Nach seinem Ende wird nichts mehr aufgezeichnet; die nächste
Änderung, der nächste Start, die Seite der Aufzeichnung oder `exp:migration:record:status` beendet es, und sein Ende
bleibt die Zeit, zu der es endete. `--for` und `--until` gelten auch für eine Sitzungsaufzeichnung.

## Nicht dabei

- **Parallele Worker.** Jeder Lauf hält die eine Sperre der Installation, Sicherungspunkt und Wiederherstellung decken
  den ganzen Lauf ab, und die Transaktion und die Schemaänderungen einer Migration (MySQL und Oracle schreiben DDL sofort
  fest) lassen sich nicht auf zwei Prozesse verteilen. Zwei Worker würden aufeinander warten oder den Weg zurück
  zerstören; Läufe bleiben deshalb einer nach dem anderen, lange Läufe werden mit den Grenzen oben aufgeteilt.
- **Pakete entfernen.** Das Entfernen eines Pakets nimmt seine Migrationen nicht zurück. Was sie angelegt haben, lebt
  meist länger als das Paket (Inhalte, Klassen, Rollen), und ein Zurücknehmen, das es entfernt, ist eine Entscheidung
  von Hand: `./console exp:migration:rollback <Migrations-ID> --apply` für jede, oder der Sicherungspunkt von vor der
  Installation des Pakets.

## Verwandte Seiten

- [Migrationen](migrationen.md), [PHP-Migrationen](migrationen-php.md), [Zurücknehmen und Sicherungspunkte](migrationen-rueckgaengig-und-captions.md)
- In der Administration: [Ein langer Lauf in Teilen](../hilfe/migrationen/ausfuehren-probelauf-und-rueckgaengig.md#ein-langer-lauf-in-teilen),
  [das Fenster für die ganze Website](../hilfe/migrationen/aufzeichnung.md#das-fenster-für-die-ganze-website), [Ausliefern, Schritt für Schritt](../hilfe/migrationen/ausliefern-schritt-fuer-schritt.md)
- [exp:migration:migrate](../beispiele/befehle/expmigrationmigrate.md), [exp:migration:resume](../beispiele/befehle/expmigrationresume.md),
  [exp:migration:record:start](../beispiele/befehle/expmigrationrecordstart.md), [Der Schritttyp code_file](../beispiele/migrationsschritte/code_file.md)
- [migration.ini](../beispiele/ini-einstellungen/migration/README.md)
