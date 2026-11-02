# Spezifikation: das Migrationsdateiformat

Diese Seite ist die verbindliche Beschreibung einer Migrationsdatei von Exponential 6.0.15: der Dateiname, der Kopf, die
PHP-Klasse, das YAML-Dokument mit seinen Schritten, Platzhaltern, Bedingungen und Schleifen, die ID-Zuordnung, die
Prüfsumme, die Formate der Aufzeichnungsdateien, die ein Lauf schreibt (die Statusdatei und das Laufprotokoll), und die
SQL- und INI-Migrationsdateien. Sie deckt ab, was der Kernel von 6.0.15 liest und schreibt. Die Anleitung ist [Migrationen](../../anleitungen/migrationen.md).

Version dieser Spezifikation: 1 (exp-migration/1, exp-migration-status/1, exp-migration-idmap/1).

## Der Dateiname

```text
YYYYMMDDHHMMSS_<name>.<type>
YYYYMMDDHHMMSS_<name>.<engine>.sql
YYYYMMDDHHMMSS_<name>.mongodb.json
```

| Teil | Regel |
|---|---|
| `YYYYMMDDHHMMSS` | ein gültiges Datum mit Uhrzeit (als Kalenderdatum geprüft) |
| `<name>` | Buchstaben, Ziffern, `_` und `-` |
| `<type>` | `php`, `yml`, `sql` oder `ini` |
| `<engine>` | nur vor `.sql`: `mysql`, `postgresql` (oder `pgsql`), `sqlite` oder `oracle`; die Datei für MongoDB ist `.mongodb.json` |

- Die **ID** ist der Dateiname ohne `.<type>` (und ohne `.<engine>`): `20261031120000_add_news_class`.
- Dateien einer ID, die sich nur in der Engine unterscheiden (`..._index.mysql.sql`, `..._index.postgresql.sql` und
  ein portables `..._index.sql`), sind **eine** Migration mit einer Datei je Engine; die eigene Datei der Engine gewinnt,
  sonst die portable.
- Eine ID hat einen Typ: ein `.php` und ein `.yml` mit derselben ID sind ein Fehler.
- Eine Datei, deren Name nicht mit 14 Ziffern und `_` beginnt, ist keine Migration und wird übersprungen (eine README,
  eine `.gitkeep`); eine, die so beginnt, aber eine Regel oben verletzt, ist ein Fehler.
- Nur die Dateien direkt in einem Migrationsordner werden gelesen; Unterordner nicht.
- Jeder Typ läuft: `sql`- und `ini`-Dateien laufen als YAML-Migration ihrer Schritte ([die SQL-Migration](#die-sql-migration),
  [die INI-Migration](#die-ini-migration)). Eine portable `.sql`-Datei gilt für die SQL-Engines: Ohne `.mongodb.json`
  (oder `engines` im Kopf) läuft die Migration nicht auf MongoDB.

## Der Kopf

Der Kopf ist der Kommentarblock am Anfang der Datei, eine Zeile `schlüssel: wert` je Angabe, in der Kommentarsyntax der
Datei. Für ihn werden höchstens die ersten 16 KiB einer Datei gelesen.

| Typ | Kommentarsyntax | Darf vor dem Kopf stehen |
|---|---|---|
| `php` | `//`, `#` oder die Zeilen eines Blocks `/** ... */` | `<?php` |
| `yml`, `ini` | `#` | eine Zeile `---` (YAML), eine Zeile `#?ini charset="utf-8"?` (INI) |
| `sql` | `--` | |

Der Kopf endet an der ersten Zeile, die kein Kommentar ist. Innerhalb davon ist eine Zeile, die kein kleingeschriebenes
`schlüssel:` mit einem Wert ist, Fließtext und wird übergangen.

| Schlüssel | Wert | Voreinstellung |
|---|---|---|
| `id` | die ID; wenn angegeben, muss sie der ID des Dateinamens gleichen | die ID des Dateinamens |
| `description` | eine Zeile Text | leer (eine Warnung in `exp:migration:check`) |
| `requires` | eine Liste: Migrations-IDs und `extension:<name>` für eine Erweiterung, die aktiv sein muss | keine |
| `environments` | eine Liste von Umgebungsnamen; `!<name>` lässt eine aus | jede Umgebung |
| `engines` | eine Liste aus `mysql`, `postgresql`, `sqlite`, `oracle`, `mongodb` | jede Engine |
| `maintenance` | `yes` oder `no` | `no` |
| `author` | eine Zeile Text | leer |
| `transactional` | `yes` oder `no`: `no` führt die Migration ohne Transaktion aus | `yes` |
| `events` | was ihre Inhaltsänderungen in das Ereignisjournal der GraphQL-API (Webhooks, Subscriptions) schreiben: `each` (jede Änderung, mit der Migration gekennzeichnet), `summary` (ein Ereignis am Ende), `none` ([das Ereignisjournal](../../funktionen/6.0/graphql.md#das-ereignisjournal)) | `each` |

- Listen sind durch Kommas getrennt; ein Listenschlüssel darf sich wiederholen, und die Werte addieren sich.
- Ein Schlüssel, der nicht in der Tabelle steht, ist ein Fehler (damit ein vertippter nicht still durchgeht); ein
  Schlüssel, der keine Liste ist, zweimal angegeben, ist ein Fehler.
- `environments`: nur mit Namen läuft die Migration in diesen; nur mit `!<name>` überall außer in diesen; die Umgebung
  einer Installation ist `site.ini [AdminEnvironment] Level`, mit `Level=auto` die Variable `EXP_ENV`, oder das
  `--environment` des Befehls.

Beispiel (PHP):

```php
<?php
// description: An index for the news list
// requires: 20261030090000_news_class, extension:expnews
// environments: staging, production
// engines: mysql, postgresql
// maintenance: no
// author: Site team
namespace MySite\Migrations;
```

## Reihenfolge

1. Nach ID: der Zeitstempel, dann der Name.
2. Eine Migration kommt nach jeder Migration, die sie in `requires` nennt, wohin deren Zeitstempel sie auch stellen.
3. Mit einer Umgebung bleiben nur die Migrationen, die in ihr laufen.

Fehler: eine zweimal definierte ID (die zuerst gefundene zählt; die Quellen werden in der Reihenfolge Site,
Erweiterungen, Pakete durchsucht), eine Anforderung, die keine Migration nennt, eine Anforderung, die in der Umgebung
nicht läuft, eine Anforderung `extension:<name>`, deren Erweiterung nicht aktiv ist, eine Migration, die sich selbst
verlangt, und ein Zyklus. Eine Migration in einem Zyklus, oder eine, die auf einen wartet, bleibt am Ende der
Reihenfolge, damit eine Liste sie noch zeigt. Solange ein Fehler da ist, läuft keine Migration.

## Die PHP-Migration

- Die Datei deklariert genau eine nicht abstrakte Klasse, die `\expMigrationPhp` erweitert. Keine andere Datei der
  Migrationen der Installation darf eine Klasse desselben Namens deklarieren.
- `up( expMigrationContext $context ): string[]` ist Pflicht; `plan()`, `down()`, `detect()` und `transactional()`
  sind frei ([PHP-Migrationen](../../anleitungen/migrationen-php.md#die-methoden)).
- `up()` meldet einen Fehler, indem es wirft; es gibt die Zeilen dessen zurück, was es getan hat.
- `detect()` gibt `done`, `pending`, `not-needed` oder `null` zurück; `null` heißt, die Aufzeichnung entscheidet.

## Die YAML-Migration

Eine `.yml`-Datei wird mit symfony/yaml gelesen (`^5.4 || ^6.4 || ^7.0`; composer.json verlangt es, und
`migration.ini [YamlSettings] LibraryPath` nennt eine Kopie außerhalb des Composer-`vendor/`). Ohne die Bibliothek
wird jede YAML-Migration mit dem Installationshinweis abgelehnt. Gelesen wird nur einfaches YAML: Ein doppelter
Schlüssel ist ein Fehler, `!php/...`-Tags werden abgelehnt, und ein Datum ohne Anführungszeichen ist kein Text
(schreiben Sie `'2026-11-01'`). Der Kopf bleibt in den `#`-Kommentarzeilen am Anfang; als YAML-Schlüssel geschriebene
Kopfschlüssel sind ein Fehler.

### Das Dokument

Das Dokument ist entweder die Liste der Schritte allein oder eine Zuordnung:

| Schlüssel | Wert | Standard |
|---|---|---|
| `steps` | die Liste der Schritte, in Reihenfolge ausgeführt (in der Zuordnungsform Pflicht) | |
| `down` | die Liste der Schritte, die die Migration zurücknehmen (`exp:migration:rollback <id>`) | keine |
| `transactional` | `false`: keine Transaktion um die Migration | `true` |

Jeder andere Schlüssel ist ein Fehler. Ein Dokument ohne Schritte ist gültig und tut nichts (eine Warnung in
`exp:migration:check`).

```yaml
# description: The languages of the news, checked before the import
steps:
    - type: reference
      set: { news_languages: [ eng-US, ger-DE ] }
    - foreach: "${ref.news_languages}"
      as: language
      steps:
          - type: assert
            that: { value: "${loop.language}", matches: "/^[a-z]{3}-[A-Z]{2}$/" }
down:
    - type: reference
      set: { news_languages: [] }
```

### Ein Schritt

Ein Schritt ist eine Zuordnung. Die Schlüssel, die die Engine liest:

| Schlüssel | Wert |
|---|---|
| `type` | der Schritttyp, wie `migration.ini [StepSettings] StepTypeList[<Name>]` ihn registriert (Pflicht) |
| `mode` | der Modus, einer von denen, die das Schema des Schritttyps nennt (`create`, `update`, `ensure`, `delete` ...); ohne Angabe der Standard des Typs; ein Typ ohne Modi nimmt kein `mode` |
| `if` | eine Bedingung (unten): Der Schritt wird übersprungen, wenn sie falsch ist |
| `environments` | eine Liste (oder ein Text mit Kommas) von Umgebungen; `!<Name>` lässt eine aus (wie im Kopf) |
| `engines` | eine Liste aus `mysql`, `postgresql`, `sqlite`, `oracle`, `mongodb`; `!<Name>` lässt eine aus |
| `references` | eine Zuordnung `<Referenzname>: <Wertname>`: Nach dem Schritt wird jede Referenz auf diesen Wert des Ergebnisses gesetzt |
| `comment` | ein Text, ignoriert |

Jeder andere Schlüssel gehört dem Schritttyp und wird gegen sein Schema geprüft: Unbekannte Schlüssel, fehlende
Pflichtschlüssel, der Typ jedes Werts (`string`, `int`, `number`, `bool`, `scalar`, `list`, `map`, `any`,
`identifier`, `condition`), die erlaubten Werte und Schlüssel eines anderen Modus sind Fehler. Ein Wert mit einem
Platzhalter wird geprüft, wenn der Schritt läuft, nachdem der Platzhalter ersetzt ist; ein Schritttyp ergänzt eigene
Prüfungen (`expMigrationStepType::validate()`). Die Standardwerte des Typs werden vor dem Lauf eingesetzt.

Ein Referenzname besteht aus Buchstaben, Ziffern und `_` und beginnt nicht mit einer Ziffer. `references:` darf nur
die Werte nennen, die das Schema des Schritttyps aufführt.

### Schleifen

Ein Schritt mit `foreach` oder `while` ist eine Schleife; er hat kein `type`.

| Schlüssel | Wert | Standard |
|---|---|---|
| `foreach` | eine Liste, eine Zuordnung oder ein Platzhalter, der für eine steht (`"${ref.languages}"`) | |
| `as` | nur `foreach`: der Name des aktuellen Elements | `item` |
| `key` | nur `foreach`: der Name des aktuellen Schlüssels (die Position in einer Liste) | `key` |
| `while` | eine Bedingung, vor jedem Durchlauf ausgewertet | |
| `max` | nur `while`: die meisten Durchläufe; einer mehr lässt die Migration scheitern | `[StepSettings] MaxLoopIterations` (1000) |
| `steps` | die Schritte der Schleife (Pflicht, nicht leer) | |
| `if`, `comment` | wie bei einem Schritt | |

In einer Schleife sind `${loop.<as>}`, `${loop.<key>}` und `${loop.index}` (der Durchlauf, ab 0) gesetzt; eine
verschachtelte Schleife sieht die Variablen der Schleifen um sie herum. Ein `${loop.<Name>}`, das keine Schleife um
ihn setzt, ist ein Fehler. Schleifen verschachteln höchstens 8 Ebenen tief.

### Platzhalter

`${<Bereich>.<Name>[.<Schlüssel>...]}` in einem Wert wird direkt vor dem Schritt ersetzt:

| Bereich | Steht für |
|---|---|
| `ref` | eine Referenz: gesetzt von `references:` eines früheren Schritts, von einem `reference`-Schritt oder von einer früheren Migration desselben Laufs (`$context->setReference()` in PHP) |
| `loop` | eine Variable einer Schleife um den Schritt |
| `idmap` | `${idmap.<Art>.<Name>}`: die ID-Zuordnung der Umgebung des Laufs (unten) |
| `run` | `${run.id}`, `${run.environment}`, `${run.engine}` |

- Ein Wert, der nur aus einem Platzhalter besteht, behält den Typ dessen, wofür er steht (eine Zahl, eine Liste); in
  einem längeren Text wird der Wert hineingeschrieben (`true`/`false` für einen Wahrheitswert, nichts für `null`), und
  eine Liste oder Zuordnung ist dort ein Fehler.
- `.<Schlüssel>` geht in eine Liste oder Zuordnung hinein (`${ref.node.id}`, `${ref.list.0}`).
- `$${` ist ein wörtliches `${`. Schlüssel von Zuordnungen werden nie ersetzt, nur Werte.
- Ein Platzhalter, der nichts benennt, lässt den Schritt scheitern. Im Probelauf bleibt er stehen, wie er geschrieben
  ist, und der Plan sagt, was erst beim Lauf der Migration entschieden wird.

### Bedingungen

`if:`, `while:` und das `that:` eines `assert`-Schritts nehmen eine Bedingung:

| Form | Wahr, wenn |
|---|---|
| `true`, `false` | wie geschrieben |
| `"${ref.flag}"` | der Wert nicht `null`, `false`, `0`, `""`, `"0"`, `"false"`, `"no"` oder eine leere Liste ist |
| `{ all: [ <Bedingung>, ... ] }` | jede wahr ist (eine leere Liste ist wahr) |
| `{ any: [ <Bedingung>, ... ] }` | eine wahr ist (eine leere Liste ist falsch) |
| `{ not: <Bedingung> }` | die Bedingung falsch ist |
| `{ ref: <Name>, set: true }` | die Referenz gesetzt ist (`set: false`: sie ist es nicht) |
| `{ environment: <Name oder Liste> }` | die Umgebung des Laufs eine von ihnen ist; `!<Name>` wie im Kopf |
| `{ engine: <Name oder Liste> }` | die Datenbank-Engine eine von ihnen ist |
| `{ value: <Wert>, <Operator>: <Operand> }` | der Vergleich gilt |

Operatoren: `equals`, `not_equals` (Zahlen und Zahlentexte werden als Zahlen verglichen), `in`, `not_in` (eine
Liste), `gt`, `gte`, `lt`, `lte` (Zahlen), `matches` (ein PCRE-Muster mit seinen Begrenzern), `empty` (`true` oder
`false`). Genau ein Operator je `value`.

### Bezeichner und die ID-Zuordnung

Ein Schlüssel vom Typ `identifier` nennt einen Knoten oder ein Objekt: eine Zahl (die ID), `remote:<Remote-ID>`,
`object-remote:<Remote-ID>` (ein Knoten: der Hauptknoten dieses Objekts) oder `path:<URL-Pfad>`
(`path:media/images`). IDs unterscheiden sich zwischen Installationen; eine Site hält die Namen ihrer IDs je Umgebung
in `<Repository>/idmap/<Umgebung>.json` (`default.json` ohne Umgebung):

```json
{
    "schema": "exp-migration-idmap/1",
    "ids": {
        "node": { "news": 1234, "media": "remote:media-root" }
    }
}
```

Arten und Namen bestehen aus Buchstaben, Ziffern, `_` und `-`; ein Wert ist eine Zahl oder ein Bezeichnertext. Eine
fehlende Datei ist eine leere Zuordnung; eine Datei, die keine ist, lässt jede YAML-Migration des Laufs scheitern.

### Schritttypen

Der Kernel registriert `reference` (`set:` eine Zuordnung von Referenzen), `assert` (`that:` eine Bedingung,
`message:` der Text des Fehlers), `content`, `location`, `content_version`, `content_class`, `content_class_group`,
`user`, `user_group`, `role`, `section`, `language`, `object_state_group`, `tag`, `url_alias`, `trash`, `file`, `image`,
`cache`, `search`, `autoload`, `layout`, `layout_block`, `layout_rule`, `http`, `mail` und
`command`; jeden Schlüssel jedes Typs nennt seine erzeugte Seite ([die Schritttypen](../../beispiele/migrationsschritte/README.md)).
Ein Bezeichner-Schlüssel nennt seine Art: `node`, `object`, `class`, `section`, `language`, `user`, `role`, `state`,
`class_group`, `state_group`, `tag` oder `url_alias`; alle außer `node`, `object` und `tag` nehmen auch einen Namen
(Bezeichner einer Klasse oder Sektion, eine Locale, ein Login, ein Rollenname, `<gruppe>/<zustand>`, der Name einer
Klassengruppe, der Bezeichner einer Zustandsgruppe, die Adresse eines URL-Alias ab der Wurzel). Ein Tag wird über seine
ID, `remote:<Remote-ID>` oder `path:<Schlagwortpfad>` genannt, und nur dort, wo die Erweiterung eztags installiert
ist. Die Schritte `file` und `image` lesen ihre `file:` unterhalb des Ordners der Migrationsdatei. Die Caches, die
jeder Schritt nennt, werden nach dem Lauf einmal geleert, und die Objekte, die die Schritte geändert haben, neu indiziert
([Caches und der Suchindex](../../anleitungen/migrationen-yaml-schritte.md#caches-der-suchindex-und-autoloads)).
Die Layout-Schritte arbeiten nur, wo Exponential Layouts installiert ist; `http`, `mail` und `command` senden und starten
in einem Probelauf nichts, und `command` führt nur die `exp:*`-Befehle der Positivliste aus
([HTTP-Anfragen, E-Mails und Konsolenbefehle](../../anleitungen/migrationen-yaml-schritte.md#http-anfragen-e-mails-und-konsolenbefehle)).
Eine Erweiterung registriert ihre eigenen Schritttypen in ihrer `migration.ini.append.php`:

```ini
[StepSettings]
StepTypeList[news_import]=expNewsImportStep
```

Die Klasse implementiert `expMigrationStepType` (`name()`, `schema()`, `validate()`, `caches()`, `plan()`, `apply()`;
`expMigrationStepTypeBase` hat die Standards). `apply()` gibt `lines` (was es getan hat) und `values` (was
`references:` nehmen kann) zurück. Eine Klasse, die es nicht gibt, die einen anderen Namen nennt oder ein fehlerhaftes
Schema hat, wird nicht registriert und ist ein Fehler jeder geprüften YAML-Migration.

### Ausführen und zurücknehmen

- `up()` führt die Schritte der Reihe nach aus; der erste, der scheitert, beendet die Migration mit seiner Stelle
  (`steps[1].steps[0] (assert): ...`), und die Transaktion der Migration wird zurückgerollt.
- `plan()` (der Probelauf) listet den Plan jedes Schritts; von einem `reference`-Schritt gesetzte Werte gelten für die
  Schritte danach, alles andere, was erst beim Lauf feststeht, wird so markiert.
- `down()` führt die `down:`-Schritte aus; ohne `down:` lässt sich eine Migration nur zurücknehmen, wenn der Typ jedes
  Schritts `expMigrationRevertibleStepType` implementiert und keiner eine Schleife ist: Dann läuft `revert()` jedes
  Schritts, der letzte zuerst, mit den Referenzen des Laufs, der zurücknimmt. `reference` und `assert` nehmen nichts
  zurück.

## Die SQL-Migration

Die Dateien einer ID (`.sql`, `.<engine>.sql`, `.mongodb.json`) sind eine Migration. Auf einer Engine läuft ihre eigene
Datei, sonst die portable `.sql` (nur auf den SQL-Engines). Eine SQL-Datei besteht aus Anweisungen, die mit `;` am
Zeilenende enden; Zeilen, die mit `--` beginnen, sind Kommentare; eine Zeile `-- down` (auch `-- down:`, gleich in
welcher Schreibung) beendet die Anweisungen, die die Migration ausführen, und beginnt die, die sie zurücknehmen. Eine
`.mongodb.json`-Datei ist eine JSON-Liste von Befehlen oder ein Objekt mit den Listen `up` und `down` (und
`description`); ein Befehl ist ein Objekt mit einem von `insert` (`documents`), `update` (`filter`, `set`, `unset`),
`delete` (`filter`), `create_collection`, `drop_collection`, `create_index` (`keys`, `name`, `unique`) und
`drop_index` (`name`), dessen Wert die Collection ist. Die Migration ist die YAML-Migration
`steps: [ { type: sql, file: <id>.sql } ]`, mit `down: [ { type: sql, file: <id>.sql, part: down } ]`, wenn eine
Datei einen Down-Teil hat, und `transactional` aus dem Kopf. Die Prüfsumme ist die der Datei der Engine.

## Die INI-Migration

Eine INI-Datei, deren Blöcke ihre Datei nennen: `[<file>.ini/<Block>]` für `settings/override/<file>.ini.append.php`,
`[siteaccess:<name> <file>.ini/<Block>]` für `settings/siteaccess/<name>/<file>.ini.append.php` (`override` oder
`global` vor dem Dateinamen ist dasselbe wie nichts). In einem Block: `Var=value` setzt, `Var[]` gefolgt von Zeilen
`Var[]=value` macht das Array zu diesen Werten, `Var[]=value` ohne `Var[]` davor fügt einen Wert hinzu,
`Var[key]=value` setzt einen Schlüssel, und `-Var`, `-Var[key]`, `-Var[]=value` entfernen. Zeilen, die mit `#` oder `;`
beginnen, sind Kommentare. Die Migration ist die YAML-Migration aus einem Schritt `settings` je Datei, in der
Reihenfolge, in der die Dateien zuerst vorkommen; sie hat kein `down:`.

## Die Prüfsumme

Die Prüfsumme einer Migration ist der SHA-256 der Bytes ihrer Datei mit auf `\n` vereinheitlichten Zeilenenden (ein
Checkout mit CRLF-Zeilenenden ergibt also dieselbe Prüfsumme), bei einer Migration mit einer Datei je Engine der Datei,
die auf der Engine läuft, als kleingeschriebene Hex-Zeichenkette mit 64 Zeichen. Sie wird in `exp_migration.checksum`
aufgezeichnet, wenn die Migration angewendet wird. Eine als `done` aufgezeichnete Migration mit abweichender Prüfsumme
wird abgelehnt, es sei denn, der Lauf bekommt `--force --reason=<text>`.

## Zustände und Aktionen

| Zustand | Bedeutet |
|---|---|
| `done` | als angewendet aufgezeichnet (oder `detect()` sagt es) |
| `pending` | noch nicht angewendet |
| `not-needed` | nicht für diese Umgebung oder Engine, oder `detect()` sagt, die Änderung ist da |
| `failed` | ihr letzter Lauf ist gescheitert |
| `unknown` | der Zustand ließ sich nicht feststellen (`detect()` hat geworfen) |

Aktionen in einem Bericht: `applied`, `would-apply`, `simulated`, `failed`, `not-run`, `blocked`, `refused`,
`accepted`, `would-accept`, `undone`, `would-undo`, `restored`, `none`.

## Die Aufzeichnungstabellen

`exp_migration` (Schlüssel: `source` + `migration_id`): `source`, `migration_id`, `stage`, `version`, `state`,
`started_at`, `finished_at`, `duration_ms`, `run_by`, `engine`, `checksum`, `message` (höchstens 1000 Zeichen),
`file_path`, `environment`, `run_id`, `caption_id`, `revision`. Quellen: `site`, `extension:<name>`,
`package:<name>`, und `kernel` für die Schritte von `exp:upgrade` (Stufe `code`, `libraries`, `database`, `scripts`,
`finish`) und ihre Sicherungspunkte (Stufe `caption`, `migration_id` `caption:<id>`).

`exp_migration_run` (Schlüssel: `run_id`): eine Zeile je Lauf mit seinem Zustand (`running`, `done`, `failed`,
`rolled-back`), Zeiten, Benutzer, Umgebung, `caption_pre`, `caption_post`, `commit_id`, `tag_name` und `push_state`
(`pushed`, `pending`, `disabled`, `no-remote`, `none`).

`run_by` ist `cli <Systembenutzer>` oder `web <Systembenutzer>`, nie ein Passwort. Eine Lauf-ID ist
`YYYYMMDD-HHMMSS-<6 hex>`; jede ID aus Buchstaben, Ziffern, `.`, `_` und `-` (höchstens 64, nicht mit einem Punkt
beginnend) wird angenommen.

## Die Statusdatei

`<repository>/status/<umgebung>.json`, Schema `exp-migration-status/1`
(`share/migration/schema/exp-migration-status-1.json`):

| Schlüssel | Wert |
|---|---|
| `schema` | `exp-migration-status/1` |
| `environment` | die Umgebung, `default`, wenn die Installation keine nennt |
| `engine` | die Datenbank-Engine |
| `updated_at` | die neueste Zeit der Aufzeichnung (UTC, ISO 8601), nie „jetzt“ |
| `summary` | Anzahlen je Zustand und `total` |
| `migrations[]` | `source`, `id`, `state`, `file`, `checksum`, `stage`, `version`, `run_id`, `started_at`, `finished_at`, `duration_ms`, `caption_id`, `revision` |
| `runs[]` | `run_id`, `state`, `started_at`, `finished_at`, `caption_pre`, `caption_post`, `commit`, `tag`, `push_state`, `log` |

Die Datei wird ganz und atomar geschrieben, sortiert, nur mit den Zeiten der Aufzeichnung, sodass dieselbe
Aufzeichnung dieselben Bytes ergibt. Sie enthält nie `run_by` oder eine Meldung.

## Das Laufprotokoll

`<repository>/runs/<run-id>.log`, eine Zeile je Ereignis:

```text
<UTC time, ISO 8601> <level> <text>
2026-11-01T02:30:20Z info applied 20261031120000_news_note_table in 3 ms: created exp_news_note | wrote the note 1
```

Stufen: `debug`, `info`, `notice`, `warning`, `error`. Bevor eine Zeile geschrieben wird, wird sie bereinigt von
geheimen Werten, Zugangsdaten in URLs, Paaren `password=...`, Bearer-Tokens und E-Mail-Adressen. Dieselben Zeilen gehen
mit der Lauf-ID davor nach `var/log/migration.log`.

## Die Maschinenausgabe

Jeder Befehl `exp:migration:*` mit `--format=json` gibt ein JSON-Objekt aus mit `schema` = `exp-migration/1`,
`command`, `mode` (`status`, `dry-run`, `simulate`, `apply`, `rollback`, `rollback-dry-run`, `check`, `generate`,
`caption`, ...), `errors`, `warnings` und `exit`, dazu, was der Modus meldet: `run_id`, `environment`, `engine`,
`transaction`, `user`, `migrations[]` (`id`, `source`, `state`, `action`, `detail`, `description`, `plan`,
`messages`, `duration_ms`, `record`, `checksum`, `changed`, ...), `summary`, `captions`, `restore`, `repository`,
`runs`, `orphans`, `pushes`, `lock`. In späteren Versionen können Schlüssel hinzukommen; innerhalb von
`exp-migration/1` wird keiner umbenannt oder entfernt.

## Aufgezeichnete Ereignisse (exp-migration-recording-event/1)

Eine Aufzeichnung von Änderungen ([die Anleitung](../../anleitungen/migrationen-erzeugen-und-aufzeichnen.md)) hält
jede Änderung als ein Ereignis fest, den Inhalt einer Zeile von `exp_migration_recording_event`: ein JSON-Objekt in
der Form des YAML-Schritts, für den es steht, so dass ein Generator den Schritt schreibt, ohne die Installation noch
einmal zu lesen.

| Schlüssel | Bedeutung |
|---|---|
| `v` | `1` |
| `audit` | der Name des Audit-Ereignisses aus der Taxonomie des Kernels (`content.object.publish` ...) |
| `subject` | worum es geht, in stabilen Namen: `object remote:<id>`, `location remote:<id>`, `class <bezeichner>`, `role <name>`, `section <bezeichner>`, `state_group <bezeichner>`, `user remote:<id>`, `url_alias <pfad>`, `setting <datei> [<block>] <variable>`, `workflow_trigger <schlüssel>` |
| `step` | der Schritt: `type`, `mode` und die Schlüssel dieses Schritts (unten), jede ID in stabiler Form: Knoten und Objekte als `remote:<remote id>` (siehe [Bezeichner und die ID-Zuordnung](#bezeichner-und-die-id-zuordnung)), Klassen, Sektionen und Objektzustände über ihren Bezeichner, Rollen über ihren Namen, Sprachen über ihr Locale; die Werte sind der Stand nach der Änderung |
| `omitted` | was absichtlich weggelassen wurde: das Konto und die persönlichen Felder von Benutzern, der Wert einer geheimen Einstellung |
| `notes` | was ein Generator wissen muss: ein Wert, wie der Kernel ihn speichert, die Sprachen, die eine Übersetzung hinzugefügt hat |

Das Ereignis nennt nie, wer die Änderung gemacht hat; die Zeile hält die Benutzer-ID (`actor_id`) nur für die
Installation. Die Zeilen einer Aufzeichnung sortieren nach `event_id` (die Millisekunde, eine Folgenummer des
Prozesses, fünf zufällige Hex-Ziffern) in der Reihenfolge der Änderungen.

Die Audit-Ereignisse, der Schritt, der aus jedem wird, und seine Schlüssel:

| Audit-Ereignis | `type` / `mode` | Schlüssel |
|---|---|---|
| `content.object.create` | `content` / `create` | `class`, `parent`, `remote_id`, `section`, `always_available`, `initial_language`, `states`, `fields`, `locations` |
| `content.object.publish`, `.translate`, `.translation.remove`, `.always_available`, `.initial_language`, `.state` | `content` / `update` | `match`, `section`, `always_available`, `initial_language`, `states`, `fields`, `locations`; `changed` (publish), `remove_translations` (Übersetzung entfernt) |
| `content.object.remove`, `content.object.purge` | `content` / `delete` | `match` |
| `content.object.restore` | `trash` / `restore` | `object`, `parent` |
| `content.node.add` | `location` / `create` | `object`, `parent`, `remote_id` |
| `content.node.move` | `location` / `move` | `match`, `parent` |
| `content.node.copy` | `content` / `copy` | `node`, `parent`, `remote_id`, `location_remote_id`, `subtree` |
| `content.node.hide`, `content.node.reveal` | `location` / `hide`, `reveal` | `match` |
| `content.node.sort`, `.priority`, `.main` | `location` / `update` | `match`; `sort_field`, `sort_order` / `child_priorities` / `main` |
| `content.node.swap` | `location` / `swap` | `match`, `with` |
| `content.node.remove` | `location` / `delete` | `match` |
| `content.node.remove.trash` | `trash` / `move` | `match` |
| `content.node.section` | `section` / `assign` | `match`, `subtree` |
| `content.class.create`, `content.class.copy` | `class` / `create` | `identifier`, `names`, `descriptions`, `contentobject_name`, `url_alias_name`, `is_container`, `always_available`, `sort_field`, `sort_order`, `groups`, `remote_id`, `attributes` |
| `content.class.change` | `class` / `update` | `match` und die Schlüssel von create außer `identifier` |
| `content.class.remove` | `class` / `delete` | `match` |
| `content.section.change` | `section` / `ensure` | `match`, `identifier`, `name`, `navigation_part` |
| `content.section.remove` | `section` / `delete` | `match` |
| `content.state.change` | `object_state_group` / `ensure` | `match`, `identifier`, `names`, `states`, `default_language` |
| `content.state.remove` | `object_state_group` / `delete` | `match` |
| `content.urlalias.change` | `url_alias` / `ensure` (hinzugefügt), `delete` (entfernt) | `path`, `node` / `match`, `which` |
| `access.role.create`, `access.role.copy` | `role` / `create` | `name`, `policies` |
| `access.role.change`, `access.policy.add`, `access.policy.remove` | `role` / `update` | `match`, `policies` |
| `access.role.remove` | `role` / `delete` | `match` |
| `access.role.assign`, `access.role.unassign` | `role` / `assign`, `unassign` | `match`, `user` oder `group`, `limitation` |
| `access.user.create` | `user` / `create` | `remote_id`, `class`, `groups`, `enabled`, `fields` (nur `[RecordingSettings] UserKeepAttributeList[]`) |
| `access.user.change`, `.move`, `.activate`, `.lock`, `.unlock` | `user` / `update` | `match`, `class`, `groups`, `enabled`, `fields` |
| `access.user.remove` | `user` / `delete` | `match` |
| `system.setting.write` | `ini` / `set`, `remove` | `file`, `scope`, `block`, `variable`, `value` oder `secret` |
| `system.workflow.trigger.change` | `workflow_trigger` / `ensure` | `triggers` |

Ein Inhaltsobjekt einer Benutzerklasse wird als Schritt `user` aufgezeichnet. `[RecordingSettings] EventList[]` kann
die Liste verkleinern, nie vergrößern. Innerhalb von `exp-migration-recording-event/1` können Ereignisse und Schlüssel
hinzukommen; keiner wird umbenannt oder entfernt.

## Verwandte Seiten

- [Migrationen: der Einstieg](../../hilfe/migrationen/README.md),
  [Alle Rezepte](../../hilfe/migrationen/rezepte/README.md), [Häufige Fragen](../../hilfe/migrationen/faq.md)
- [Die Anleitung](../../anleitungen/migrationen.md), [das Tutorial](../../anleitungen/migrationen-tutorial.md),
  [PHP-Migrationen](../../anleitungen/migrationen-php.md),
  [Zurücknehmen und Sicherungspunkte](../../anleitungen/migrationen-rueckgaengig-und-captions.md),
  [Das Migrations-Repository](../../anleitungen/migrationen-repositories.md)
- [In der Administration](../../hilfe/migrationen/in-der-administration.md),
  [Befehle](../../hilfe/migrationen/befehle.md), [Einstellungen](../../hilfe/migrationen/einstellungen.md),
  [Richtlinien, Ansichten und Ereignisse](../../hilfe/migrationen/richtlinien-ansichten-und-ereignisse.md)
- [Die Funktion](../../funktionen/6.0/migrationen.md)
- [Wenn eine Migration scheitert](../../hilfe/migrationen/wenn-eine-migration-scheitert.md),
  [Fehlermeldungen](../../anleitungen/migrationen-fehlerbehebung.md)
- [Migrationen oder Umzug einer Site](../../hilfe/migrationen/migrationen-oder-umzug-einer-site.md)
- [migration.ini](../../beispiele/ini-einstellungen/migration/README.md)
- [Audit-Ereignismodell](audit-ereignismodell.md)
