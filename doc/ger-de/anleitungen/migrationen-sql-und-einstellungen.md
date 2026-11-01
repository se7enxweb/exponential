# Migrationen: SQL und Einstellungen

Diese Seite ist die Anleitung zu den Migrationen, die das Datenbankschema, Zeilen und Einstellungen ändern: die
portablen Schritte für Schema und Daten, die jede Engine versteht, SQL-Migrationsdateien mit einer Datei je Engine, der
Schritt `sql` sowie die INI-Migrationen und der Schritt `settings`, die `settings/override` und
`settings/siteaccess/<name>` ändern. Jeden Schlüssel der Schritttypen nennen ihre erzeugten Referenzseiten:
[`schema`](../beispiele/migrationsschritte/schema.md), [`data`](../beispiele/migrationsschritte/data.md),
[`sql`](../beispiele/migrationsschritte/sql.md) und [`settings`](../beispiele/migrationsschritte/settings.md).

[Anleitungen](README.md) · [Migrationen](migrationen.md) · Siehe auch: [Schritte von YAML-Migrationen](migrationen-yaml-schritte.md),
[Das Dateiformat](../spezifikationen/6.0/migrationsdateiformat.md), [Zurücknehmen und Sicherungspunkte](migrationen-rueckgaengig-und-captions.md)

## Was wofür

| Sie wollen | Dafür | Läuft auf |
|---|---|---|
| eine Tabelle, eine Spalte oder einen Index anlegen | den Schritttyp `schema` in einer YAML-Migration | jeder Engine, über die Schema-Handler des Kernels |
| Zeilen anlegen, ändern oder entfernen | den Schritttyp `data` | jeder Engine, die Werte setzt die Verbindung in Anführungszeichen |
| SQL ausführen, das Sie für eine Engine geschrieben haben | eine SQL-Migration (`.sql`, `.<engine>.sql`, `.mongodb.json`) oder den Schritttyp `sql` | den Engines, für die Sie es geschrieben haben |
| eine Einstellung ändern | eine INI-Migration (`.ini`) oder den Schritttyp `settings` | den Dateien, nicht der Datenbank |

Nehmen Sie lieber `schema` und `data`: Einmal geschrieben, laufen sie auf MySQL/MariaDB, PostgreSQL, SQLite, Oracle
und MongoDB gleich, und jeder sieht nach, bevor er etwas ändert. Eine Migration, die auf halbem Weg stehen blieb, kann
so einfach noch einmal laufen.

## Das portable Schema: der Schritttyp `schema`

```yaml
# description: Bewertungen von Nachrichtenartikeln
steps:
    - type: schema
      mode: create_table
      table: exp_news_rating
      columns:
          id: { type: auto_increment }
          contentobject_id: { type: int, not_null: true, default: 0 }
          rating: { type: int, not_null: true, default: 0 }
          comment: { type: varchar, length: 255, default: "" }
      indexes:
          exp_news_rating_object: { columns: [ contentobject_id ] }
    - type: schema
      mode: add_column
      table: exp_news_rating
      column: created
      definition: { type: int, not_null: true, default: 0 }
    - type: schema
      mode: add_index
      table: exp_news_rating
      index: exp_news_rating_created
      columns: [ created ]
```

Die Spalten werden so geschrieben, wie der Kernel seine eigenen Tabellen in `share/db_schema.dba` schreibt: `type` ist
eines von `auto_increment`, `int`, `bigint`, `float`, `double`, `decimal` (Länge `"10,2"`), `char`, `varchar` (beide mit
Länge) und `longtext` (`text` gilt als `longtext`); `not_null` und `default` wie dort. Eine Spalte `auto_increment` ist
der Primärschlüssel, wenn `primary_key` keine anderen nennt.

Der Schritt gibt die Tabelle dem Schema-Handler der Engine (`eZDbSchema`), demselben Code, der die Tabellen des Kernels
anlegt (`expKernelTableSchema`) und mit dem die Upgrade-Schritte von `exp:upgrade` Spalten hinzufügen: `CREATE TABLE`
mit `AUTO_INCREMENT` auf MySQL/MariaDB, eine Sequenz auf PostgreSQL und Oracle, `AUTOINCREMENT` auf SQLite und auf
MongoDB eine Collection mit ihren Indizes. Bevor er etwas ändert, fragt er den Katalog der Engine (`expUpgradeCatalog`):
Eine Tabelle, eine Spalte oder ein Index auf denselben Spalten, die schon da sind, gelten als erledigt, ebenso etwas zu
Entfernendes, das schon fehlt.

| Modus | Nimmt sich beim Zurücknehmen selbst zurück |
|---|---|
| `create_table` | ja: entfernt die Tabelle |
| `add_column` | ja: entfernt die Spalte |
| `add_index` | ja: entfernt den Index |
| `drop_table`, `drop_column`, `drop_index` | nein: geben Sie der Migration ein `down:` oder stellen Sie ihren Sicherungspunkt wieder her |

Auf MongoDB hat eine Collection keine Spalten: `add_column` gibt den Dokumenten ohne das Feld seinen Standardwert (wenn
die Spalte einen hat), `drop_column` entfernt das Feld aus jedem Dokument. Auf SQLite vor 3.35, das kein `DROP COLUMN`
kennt, wird die Spalte so entfernt, wie die Dokumentation von SQLite es beschreibt: eine neue Tabelle ohne sie, die
Zeilen kopiert, die alte Tabelle entfernt und die neue umbenannt, ihre übrigen Indizes neu angelegt.

## Zeilen: der Schritttyp `data`

```yaml
    - type: data
      mode: ensure                  # insert, ensure, update, delete
      table: ezsite_data
      key: [ name ]                 # die Spalten, die eine Zeile finden
      rows:
          - { name: news_list_limit, value: "20" }
    - type: data
      mode: insert
      table: exp_news_rating
      rows:
          - { contentobject_id: "${ref.article}", rating: 5, comment: "Gut" }
      id_column: id
      references: { rating: id }
    - type: data
      mode: update
      table: ezcontentclass
      where: { identifier: [ news, blog_post ] }   # gleich; eine Liste: einer davon; null: ist null
      values: { always_available: 1 }
```

Werte sind Zeichenketten, Zahlen, `true`/`false` (1 und 0) oder `null`. Auf den SQL-Engines setzt die Verbindung sie
in Anführungszeichen (`eZDB::escapeString()`), ein Wert aus einer Referenz ist hier also sicher; auf MongoDB werden sie
so gespeichert, wie sie sind, über den Treiber des Kernels (der Zähler eines ID-Felds wird wie bei jedem anderen
Einfügen benutzt). `update` und `delete` brauchen `where` oder `all: true` für jede Zeile. Der Schritt nimmt sich nicht
selbst zurück.

## SQL-Migrationsdateien

Eine SQL-Migration ist eine Datei oder eine Gruppe von Dateien mit derselben ID neben Ihren anderen Migrationen:

```text
migrations/20261107100000_news_index.sql             jede SQL-Engine
migrations/20261107100000_news_index.mysql.sql       nur MySQL/MariaDB (geht der portablen Datei vor)
migrations/20261107100000_news_index.pgsql.sql       PostgreSQL (.postgresql.sql geht auch)
migrations/20261107100000_news_index.sqlite.sql      SQLite
migrations/20261107100000_news_index.oracle.sql      Oracle
migrations/20261107100000_news_index.mongodb.json    MongoDB
```

```sql
-- description: Ein Index für die Nachrichtenliste
-- requires: 20261030090000_news_class
CREATE INDEX exp_news_list ON ezcontentobject ( published );
-- down
DROP INDEX exp_news_list;
```

Es läuft die eigene Datei der Engine, sonst die portable `.sql`-Datei. Eine portable Datei läuft nur auf den
SQL-Engines: Ohne `.mongodb.json` daneben (oder `engines:` im Kopf) ist die Migration nicht für MongoDB, und der Runner
lässt sie dort mit diesem Grund aus. Anweisungen enden mit `;` am Zeilenende; Zeilen, die mit `--` beginnen, sind
Kommentare. Der Teil nach einer Zeile `-- down` nimmt die Migration zurück (`exp:migration:rollback`); ohne ihn nimmt
nur der vor dem Lauf genommene Sicherungspunkt sie zurück. Hat die Datei einer Engine einen Down-Teil und die einer
anderen keinen, warnt `exp:migration:check`.

Eine `.mongodb.json`-Datei ist eine Liste von Befehlen oder `{ "up": [ ... ], "down": [ ... ] }`:

```json
{
    "up": [
        { "create_index": "ezcontentobject", "keys": { "published": 1 }, "name": "exp_news_list" }
    ],
    "down": [
        { "drop_index": "ezcontentobject", "name": "exp_news_list" }
    ]
}
```

Die Befehle sind `insert` (mit `documents`), `update` (`filter`, `set`, `unset`), `delete` (`filter`),
`create_collection`, `drop_collection`, `create_index` (`keys`, `name`, `unique`) und `drop_index` (`name`).

Eine SQL-Migration läuft als YAML-Migration mit einem Schritt `sql`, der ihre Datei nennt; Plan, Probelauf, Protokoll,
Aufzeichnung und Prüfsumme funktionieren also wie bei jeder anderen Migration. Die Prüfsumme ist die der Datei, die
auf der Engine läuft.

## Der Schritttyp `sql`

Dasselbe in einer YAML-Migration, neben anderen Schritten:

```yaml
    - type: sql
      sql: "UPDATE ezcontentclass SET always_available = 1 WHERE identifier = 'news';"
      mongodb:
          - { update: ezcontentclass, filter: { identifier: news }, set: { always_available: 1 } }
      missing: fail                 # oder skip, auf einer Engine, für die der Schritt nichts hat
    - type: sql
      file: sql/news_cleanup.sql    # eine Datei neben der Migration; sql/news_cleanup.mysql.sql gilt auf MySQL
```

`sql:` gilt für jede SQL-Engine; `mysql:`, `postgresql:`, `sqlite:` und `oracle:` gehen ihm auf ihrer Engine vor. Ein
Platzhalter (`${ref.name}`) im Text wird so eingesetzt, wie er ist, ohne Anführungszeichen: Werte gehören mit `data` in
Zeilen, nicht in den SQL-Text.

## Was nicht in einer Transaktion liegt

Jede Migration läuft in einer Transaktion, wo die Engine eine hat, und ein Fehler rollt sie zurück. Wie viel das
abdeckt, hängt von der Engine ab:

| Engine | Daten | Schemaänderungen (`CREATE`, `ALTER`, `DROP`, `RENAME`, `TRUNCATE`) |
|---|---|---|
| PostgreSQL, SQLite | zurückgerollt | mit dem Rest zurückgerollt |
| MySQL/MariaDB, Oracle | zurückgerollt | sofort festgeschrieben: Sie bleiben, wenn eine spätere Anweisung fehlschlägt |
| MongoDB | bleiben | bleiben (keine Transaktionen) |

Auf MySQL/MariaDB und Oracle sagt der Schritt das im Protokoll („committed the schema change at once“), und der Runner
schreibt zum Fehler „rolled back what could be: mysql keeps schema changes made before the failure“. Stellen Sie den
vor dem Lauf genommenen Sicherungspunkt wieder her, oder schreiben Sie die Migration so, dass sie noch einmal laufen
kann: `schema`-Schritte sehen nach, bevor sie etwas ändern.

Eine Anweisung, die nicht in einer Transaktion laufen kann (`CREATE INDEX CONCURRENTLY` auf PostgreSQL, `VACUUM` auf
SQLite), braucht eine Migration ohne: `-- transactional: no` im Kopf einer SQL-Datei (`# transactional: no` in einer
INI- oder YAML-Datei, oder `transactional: false` in einem YAML-Dokument).

## Einstellungen: INI-Migrationen und der Schritttyp `settings`

```ini
#?ini charset="utf-8"?
# description: Nachrichteneinstellungen und die Startseite der Administration
# environments: staging, production

[news.ini/NewsSettings]
ListLimit=20
ShownClasses[]
ShownClasses[]=article
ShownClasses[]=blog_post

[siteaccess:admin site.ini/SiteSettings]
DefaultPage=content/dashboard

[site.ini/ExtensionSettings]
ActiveExtensions[]=expnews
-DebugByIP
```

Jeder Block nennt seine Datei (`[news.ini/NewsSettings]` ist `settings/override/news.ini.append.php`) und für einen
Siteaccess den Siteaccess (`[siteaccess:admin site.ini/SiteSettings]`). `Var=value` setzt einen Wert, `Var[]` gefolgt
von `Var[]=...` macht das Array genau zu diesen Werten, `Var[]=value` allein fügt einen Wert hinzu, `Var[key]=value`
setzt einen Schlüssel eines Hashs, und eine Zeile, die mit `-` beginnt, entfernt (`-Var`, `-Var[key]`,
`-Var[]=value`). Dasselbe als Schritt:

```yaml
    - type: settings
      file: site.ini
      siteaccess: admin             # ohne: settings/override
      set:
          SiteSettings: { DefaultPage: content/dashboard }
          RegionalSettings: { SiteLanguageList: [ eng-GB, ger-DE ] }
      add:
          ExtensionSettings: { ActiveExtensions: [ expnews ] }
      remove:
          DebugSettings: [ DebugByIP ]
```

Einstellungen werden nur nach `settings/override` und `settings/siteaccess/<name>` geschrieben: nie in die
mitgelieferten `settings/*.ini`, nie in die Einstellungen einer Erweiterung. Es schreibt der INI-Editor des Kernels
(`expIniEditor`, derselbe wie bei `./console exp:ini`):

- nur die Zeilen, die sich ändern; Kommentare und die Reihenfolge der Datei bleiben;
- zuerst eine Kopie der Datei, wie sie war, unter `var/backup/ini/<Zeit>/`, nur für ihren Eigentümer lesbar;
- ein atomares Schreiben, das Eigentümer, Gruppe und Modus der Datei erhält;
- jede geänderte Variable im Audit-Protokoll (`system.setting.write`, ein Geheimnis als `[secret]`);
- gar nichts, wenn die Datei es schon so sagt, sodass ein zweiter Lauf nichts ändert.

Ein Probelauf listet die Änderungen und schreibt nichts; der Wert eines Geheimnisses ist dort maskiert. Der INI-Cache
wird nach dem Lauf geleert. Einstellungsschritte nehmen sich nicht selbst zurück: Das Zurücknehmen stellt den vor dem
Lauf genommenen Sicherungspunkt wieder her, der den Einstellungsordner enthält. Mit `ManagedByDeployment=enabled` in
`site.ini [ConfigurationSettings]` lehnt die Weboberfläche das Schreiben ab; Läufe auf der Kommandozeile schreiben
weiterhin.

## Weiterlesen

- [Alle Schritttypen](../beispiele/migrationsschritte/README.md), je eine erzeugte Seite
- [Das Dateiformat](../spezifikationen/6.0/migrationsdateiformat.md): die SQL-Migration und die INI-Migration
- [Schritte von YAML-Migrationen](migrationen-yaml-schritte.md)
- [Zurücknehmen und Sicherungspunkte](migrationen-rueckgaengig-und-captions.md)
