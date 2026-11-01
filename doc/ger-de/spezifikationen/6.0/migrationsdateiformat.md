# Spezifikation: das Migrationsdateiformat

Diese Seite ist die verbindliche Beschreibung einer Migrationsdatei von Exponential 6.0.15: der Dateiname, der Kopf, die
PHP-Klasse, die Prüfsumme und die Formate der Aufzeichnungsdateien, die ein Lauf schreibt (die Statusdatei und das
Laufprotokoll). Sie deckt ab, was der Kernel von 6.0.15 liest und schreibt; die Schritte von YAML-, SQL- und
INI-Migrationen kommen hinzu, sobald es sie gibt. Die Anleitung ist [Migrationen](../../anleitungen/migrationen.md).

Version dieser Spezifikation: 1 (exp-migration/1, exp-migration-status/1).

## Der Dateiname

```text
YYYYMMDDHHMMSS_<name>.<type>
YYYYMMDDHHMMSS_<name>.<engine>.sql
```

| Teil | Regel |
|---|---|
| `YYYYMMDDHHMMSS` | ein gültiges Datum mit Uhrzeit (als Kalenderdatum geprüft) |
| `<name>` | Buchstaben, Ziffern, `_` und `-` |
| `<type>` | `php`, `yml`, `sql` oder `ini` |
| `<engine>` | nur vor `.sql`: `mysql`, `postgresql`, `sqlite`, `oracle` oder `mongodb` |

- Die **ID** ist der Dateiname ohne `.<type>` (und ohne `.<engine>`): `20261031120000_add_news_class`.
- Dateien einer ID, die sich nur in der Engine unterscheiden (`..._index.mysql.sql`, `..._index.postgresql.sql` und
  ein portables `..._index.sql`), sind **eine** Migration mit einer Datei je Engine; die eigene Datei der Engine gewinnt,
  sonst die portable.
- Eine ID hat einen Typ: ein `.php` und ein `.yml` mit derselben ID sind ein Fehler.
- Eine Datei, deren Name nicht mit 14 Ziffern und `_` beginnt, ist keine Migration und wird übersprungen (eine README,
  eine `.gitkeep`); eine, die so beginnt, aber eine Regel oben verletzt, ist ein Fehler.
- Nur die Dateien direkt in einem Migrationsordner werden gelesen; Unterordner nicht.
- In 6.0.15 läuft nur `php`; die anderen Typen werden gefunden, geordnet und geprüft und vom Runner abgelehnt.

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
