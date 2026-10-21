# 14. Migration von der 4.x-Linie

Dieses Kapitel bringt eine Website, die noch mit eZ Publish 3.10, 4.0 bis 4.7 oder einem frühen 5.x-Legacy-Kernel unter
PHP 5 läuft, auf Exponential 6.0 unter PHP 8. [Kapitel 11](11-aktualisieren.md) listet die Update-Kette auf; dieses
Kapitel beschreibt die praktische Seite, wenn Sie sie von so weit zurück durchlaufen. Es behandelt, was gleich bleibt
und was nicht, wie Sie eine Bestandsaufnahme der alten Website machen, einen Plan für die Staging-Umgebung, das
Datenbank-Ablaufprotokoll in der Reihenfolge, die mit dem heutigen Code funktioniert (welche Update-Datei, welches
Reparaturskript, welche Vorabprüfungsabfrage, je Datenbank-Engine), die Dateien, die Einstellungen, die Designs und
Templates, die Portierung Ihrer eigenen Extensions von PHP 5 auf PHP 8 mit Code vorher und nachher, Passwörter, Suche,
die anschließende Prüfung der Inhalte, die Probleme, auf die man tatsächlich stößt, einen Rollback-Plan und eine
Checkliste. Jeder Befehl, jeder Pfad und jede Einstellung darin wurde am Code dieses Repositorys geprüft; wo die
eigenen Upgrade-Seiten des Herstellers für 4.0 bis 4.7 etwas anderes sagen, wird auf den Unterschied hingewiesen.

[Inhalt](README.md) · Zurück: [13. Sicherheitshärtung für den Produktivbetrieb](13-sicherheitshaertung.md) · Weiter: [15. Migration vom 5.x-Legacy-Stack](15-migration-von-5x-legacy.md)

## 14.1 Was sich ändert und was nicht

Exponential 6.0 ist derselbe Legacy-Kernel, weitergeführt. Das bestimmt die Form der gesamten Migration.

**Was gleich bleibt, sodass Sie es nicht konvertieren müssen:**

| Bereich | In Exponential 6.0 |
|---|---|
| Familie des Datenbankschemas | Dieselben Tabellen (`ezcontentobject`, `ezcontentobject_attribute`, `ezcontentobject_tree`, `ezurlalias_ml` und die übrigen). Die alten Update-Dateien bringen eine alte Datenbank auf das aktuelle Schema; nichts wird exportiert und erneut importiert. |
| Inhalts-Datentypen | Alle Kernel-Datentypen von 3.x/4.x befinden sich weiterhin in `kernel/classes/datatypes/` und in `content.ini [DataTypeSettings] AvailableDataTypes[]`: `ezxmltext`, `ezimage`, `ezbinaryfile`, `ezmedia`, `ezobjectrelationlist`, `ezmatrix`, `ezenum`, `ezoption`, `ezmultioption`, `ezmultioption2`, `ezprice`, `ezmultiprice`, `ezisbn`, `ezkeyword`, `ezuser` und die anderen. |
| Rich Text | `ezxmltext` bleibt `ezxmltext` und wird mit dem Online-Editor (ezoe) bearbeitet. Es gibt **keine** Konvertierung von XmlText nach RichText. |
| Seiten | Der Seiten-Datentyp `ezflow` wird als Paket ausgeliefert (`composer.json` verlangt `se7enxweb/ezflow`). Es gibt **keine** Konvertierung von Page nach LandingPage. |
| Templates | Die Template-Sprache, ihre Funktionen (einschließlich der alten Funktionen `section`, `let`, `set` und `default`) und ihre Operatoren behalten ihre Namen; INI-Blöcke und -Schlüssel behalten ihre ([Das Produkt heißt Exponential](../funktionen/6.0/umbenennung-in-exponential.md)). |
| Extension-API | `eZPersistentObject`, `eZDataType`, `eZWorkflowEventType`, Module mit `module.php`, Fetch-Funktionen in `function_definition.php`, in `autoloads/eztemplateautoload.php` registrierte Template-Operatoren, Einstellungen in `settings/*.ini.append.php`. |
| Klassennamen | Kernel-Klassen mit dem Präfix `eZ` behalten ihre Namen. Die Versionsklasse ist `ExponentialSDK`; `eZPublishSDK` existiert weiterhin als Unterklasse davon (`lib/ezpublishsdk.php`). |

**Was sich ändert:** PHP 5 zu PHP 8 (Ihr eigener PHP-Code ist der größte Arbeitsanteil), eZ Components ersetzt durch
die per Composer installierten Zeta Components, die Cluster-Handler, die Dateien in der Datenbank speicherten, YUI
(entfernt), jQuery (4.0.0), der Online-Editor (standardmäßig TinyMCE 3, optional TinyMCE 8), mehrere Rewrite-Regeln und
eine strengere Sicherheitsgrundlage. All das wird unten behandelt.

### Im Vergleich mit der Migration des Herstellers auf seine neuere Plattform

Der Hersteller hat eine Migration von eZ Publish auf seinen Symfony-basierten Nachfolger dokumentiert
([Migrating from eZ Publish](https://doc.ibexa.co/en/5.0/update_and_migration/migrate_to_ibexa_dxp/migrating_from_ez_publish/)).
Dieser Weg führt zu einem anderen Produkt, und der größte Teil seiner Arbeit trifft hier nicht zu:

| Aussage der Herstellerseite | Für Exponential 6.0 |
|---|---|
| XmlText-Felder werden durch ein RichText-Feld ersetzt | Nicht zutreffend: `ezxmltext` bleibt, wie es ist. |
| Das Page-Feld (ezflow) wird durch LandingPage ersetzt | Nicht zutreffend: ezflow bleibt erhalten, als Composer-Paket. |
| Das Datenbankschema hat sich inkompatibel zu Legacy geändert | Nicht zutreffend: Das Schema ist das Legacy-Schema, fortgeführt durch die Dateien in `update/database/`. |
| Eigene Feldtypen müssen für den neuen Stack neu geschrieben oder durch einen Platzhalter ersetzt werden | Nicht nötig: Eigene `eZDataType`-Klassen funktionieren nach den PHP-8-Korrekturen aus [14.9](#149-eigene-extensions-von-php-5-auf-php-8-portieren) weiter. |
| Das Web-Frontend und die Administrationsmodule müssen auf Symfony neu geschrieben werden | Nicht nötig: Templates, Designs und Module werden übernommen ([14.7](#147-einstellungen-und-siteaccesses), [14.8](#148-designs-templates-und-javascript)). |
| Interne Entwürfe vor der Migration entfernen (`InternalDraftsCleanUpLimit`, `InternalDraftsDuration`, der Cronjob zum Aufräumen interner Entwürfe) | Lohnt sich für eine kleinere Datenbank, ist aber nicht erforderlich: siehe [14.4](#144-die-alte-installation-vorbereiten). |
| Code-Kompatibilität nur für Code, der für den Symfony-Stack von 5.x geschrieben wurde; die Legacy-Codebasis wird nicht übernommen | Das Gegenteil: Legacy-Code (Datentypen, Module, Templates, Extensions) wird nach den PHP-8-Arbeiten aus 14.9 übernommen |
| Die neue Plattform neben Legacy zu betreiben, ist „praktisch unmöglich“ | Hier nicht die Frage: Exponential ist der Legacy-Kernel. Eine Brücke zu einer Symfony-Plattform gibt es, wenn Sie beides möchten ([16.6.8](16-migration-von-ez-platform-und-ibexa.md#1668-einen-symfony-stack-neben-dem-legacy-kernel-behalten)). |
| Wenn Sie noch nicht migrieren können, bleiben Sie bei eZ Publish Enterprise 5.4 (unterstützt bis Ende 2021) | Diese Unterstützung ist beendet; Exponential 6.0 ist die gepflegte Fortführung desselben Kernels, unter PHP 8. |

## 14.2 Die Migration auf einer Staging-Kopie planen

Migrieren Sie niemals die produktive Installation an Ort und Stelle. Bauen Sie die neue Website neben der alten auf,
proben Sie auf einer Kopie der Produktivdatenbank, bis der Durchlauf langweilig ist, und wiederholen Sie ihn dann
einmal mit einer frischen Kopie, die gezogen wird, während sich die alte Website im Wartungsmodus befindet.

1. **Den alten Code einfrieren.** Notieren Sie die genaue alte Version (14.3), die Liste der aktiven Extensions und
   jede Datei, die Sie im Kernel geändert haben. Kernel-Änderungen werden nicht übernommen; sie müssen zu Extensions
   oder Overrides werden.
2. **Staging-Server.** PHP 8.0 bis 8.5 (auch für Exponential Velocity), die
   Datenbank-Engine, die Sie im Produktivbetrieb verwenden werden, und die Voraussetzungen aus
   [Kapitel 2](02-voraussetzungen.md).
3. **Neuer Code.** Beziehen Sie Exponential wie in [Kapitel 3](03-den-code-beschaffen.md). Führen Sie **keinen**
   Installer gegen die alte Datenbank aus: Der Setup-Assistent, der Kickstarter und `exp:install` legen eine neue
   Datenbank an ([11.1](11-aktualisieren.md#111-bevor-sie-beginnen)).
4. **Proben.** Spielen Sie den Produktiv-Dump auf Staging ein, führen Sie das Datenbank-Ablaufprotokoll aus
   ([14.5](#145-die-datenbank-schritt-für-schritt)), übertragen Sie die Dateien (14.6), die Einstellungen (14.7), die Designs
   (14.8) und die Extensions (14.9) und prüfen Sie dann (14.12). Schreiben Sie jeden Befehl auf und wie lange er gedauert
   hat.
5. **Korrigieren und wiederholen**, bis die Probe von oben bis unten ohne manuelle Reparatur durchläuft.
6. **Umstellen.** Alte Website in den Wartungsmodus, frischer Dump, dasselbe Ablaufprotokoll, Prüfung, Umstellung von
   DNS oder Proxy. Lassen Sie die alte Website und ihre Datenbank für den Rollback unberührt (14.14).

Drei Stränge können von verschiedenen Personen parallel bearbeitet werden, weil sie erst am Ende zusammenkommen: die
**Datenbank** (14.5), die **Dateien und Einstellungen** (14.6, 14.7) und der **Code** (14.8, 14.9).

## 14.3 Eine Bestandsaufnahme der alten Website machen

Führen Sie diese Abfragen auf einer Kopie der alten Datenbank aus; sie lesen nur.

**Welche Version.** Die Zeilen `ezpublish-version` und `ezpublish-release` von `ezsite_data` zeigen, wo die Kette
beginnt (die Abfrage steht in [11.2](11-aktualisieren.md#112-welche-version-betreiben-sie)). Die Update-Dateien 5.1 bis 5.4
schreiben `5.1.0alpha1` bis `5.4.0alpha1` in diese Zeile; so wurden sie ausgeliefert, und es ist kein Zeichen für
einen fehlgeschlagenen Schritt.

**Welche Datentypen verwendet werden.** Alles, was kein Kernel-Datentyp ist, stammt aus einer Extension, die Sie
portieren oder installieren müssen:

```sql
SELECT data_type_string, COUNT(*) FROM ezcontentclass_attribute
 WHERE version = 0 GROUP BY data_type_string ORDER BY 2 DESC;
```

**Welche Passwort-Hash-Typen gespeichert sind** (siehe [14.10](#1410-benutzer-passwörter-und-anmeldung)):

```sql
SELECT password_hash_type, COUNT(*) FROM ezuser GROUP BY password_hash_type;
```

**Zeichensatz und Tabellentyp (MySQL).** Der neue Code verbindet sich mit `[DatabaseSettings] Charset=utf-8`, was der
MySQL-Treiber auf MySQLs `utf8` abbildet (`lib/ezdb/classes/ezmysqlcharset.php`). Eine Datenbank, die noch `latin1`
oder gemischt ist, muss konvertiert werden (14.5.2).

```sql
SHOW CREATE DATABASE your_database;
SELECT table_name, engine, table_collation FROM information_schema.tables
 WHERE table_schema = 'your_database' ORDER BY engine, table_collation;
```

oder, wenn der neue Code bereits auf die Datenbank zeigt, `php bin/php/ezconvertmysqltabletype.php --list`.

**Cluster-Modus.** Suchen Sie in der alten `settings/override/file.ini.append.php` nach
`[ClusteringSettings] FileHandler`. `eZFSFileHandler` (einfache Dateien) und `eZDFSFileHandler` (Dateien auf NFS,
Metadaten in einer Datenbank) gibt es in Exponential 6.0. `eZDBFileHandler` (Dateien in der Datenbank gespeichert,
Tabelle `ezdbfile`) und `eZFS2FileHandler` gibt es **nicht**: Sie wurden im Januar 2013 entfernt (EZP-20288, „eZDB &
eZFS2 Cluster removed in favor of eZDFS“). Siehe 14.4.

**Suchmaschine.** `[SearchSettings] SearchEngine` in den alten `site.ini`-Overrides: `eZSearchEngine` ist die
eingebaute; alles andere ist eine Extension wie eZ Find (14.11).

**Größe.** Diese Zahlen bestimmen, wie lange jeder Schritt dauert (14.15):

```sql
SELECT COUNT(*) FROM ezcontentobject;
SELECT COUNT(*) FROM ezcontentobject_attribute;
SELECT COUNT(*) FROM ezcontentobject_tree;
SELECT COUNT(*) FROM ezurlalias_ml;
SELECT COUNT(*) FROM ezimagefile;
```

sowie die Größe von `var/` (oder des DFS-Mounts) auf der Festplatte.

## 14.4 Die alte Installation vorbereiten

Erledigen Sie diese Punkte auf der alten Website, solange sie noch mit ihrem alten Code läuft, bevor Sie den Dump
ziehen, den Sie migrieren.

- **Die Konsistenz mit der alten Administration prüfen.** Die Seite
  [Backup and consistency checks](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Backup-and-consistency-checks.html)
  des Herstellers beschreibt **Setup > Upgrade-Prüfung** mit **Überprüfe Dateikonsistenz** und **Überprüfe
  Datenbankkonsistenz**. Dieselben beiden Schaltflächen gibt es in Exponential 6.0
  (`design/admin/templates/setup/systemupgrade.tpl`); führen Sie sie also nach der Migration erneut aus und vergleichen
  Sie.
- **Den Datenbank-Cluster verlassen.** Wenn die alte Website `eZDBFileHandler` verwendet, holen Sie die Dateien mit den
  eigenen Werkzeugen der alten Installation aus der Datenbank, solange sie noch läuft, und stellen Sie sie auf
  `eZFSFileHandler` (oder auf DFS) um. Exponential 6.0 hat keinen Handler, der `ezdbfile` lesen kann, und kann diesen
  Schritt daher nicht für Sie erledigen.
- **Optionale Aufräumarbeiten, die den Dump verkleinern:** interne Entwürfe entfernen (`content.ini [ContentSettings]
  InternalDraftsCleanUpLimit` und `InternalDraftsDuration` niedrig setzen und den Cronjob `internal_drafts_cleanup.php`
  ausführen), alte archivierte Versionen, abgelaufene Sitzungen. Dieselben Werkzeuge gibt es danach in Exponential:
  `php runcronjobs.php --script=internal_drafts_cleanup.php`, `php bin/php/cleanupversions.php`,
  `php update/common/scripts/cleanup.php session`.
- **Sichern** Sie die Datenbank und das gesamte alte Verzeichnis, einschließlich `var/`. Die Befehle stehen in
  [11.1](11-aktualisieren.md#111-bevor-sie-beginnen). Spielen Sie die Sicherung einmal auf einer Testmaschine zurück.
- **Festhalten, was funktioniert**: eine Liste von URLs je Siteaccess mit ihrem HTTP-Status, einige Suchen mit der
  Anzahl ihrer Treffer, die Zahl der Benutzer, die sich im letzten Monat angemeldet haben. Diese Liste ist Ihr
  Prüfskript in 14.12.

## 14.5 Die Datenbank, Schritt für Schritt

### 14.5.1 Welche Ausgangspunkte funktionieren

| Sie betreiben | Start |
|---|---|
| 3.0 bis 3.9 | Nicht mit Exponential 6.0. Die Update-Skripte für diese Versionen (`updatemultioption.php`, `correctxmltext.php`, `updateclasstranslations.php` sowie das `updatexmltext.php` von 3.x und seine Verwandten, die in den [System-Upgrade-Skripten](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/The-system-upgrade-scripts.html) des Herstellers aufgeführt sind) sind nicht in diesem Repository enthalten. Bringen Sie die Website zuerst mit der alten Release 3.10 auf 3.10, wie es der Hersteller verlangte („direct upgrading from version 3.9 (and earlier) to 4.0 is impossible“). |
| 3.10.x | Die Kette ab `4.0/dbupdate-3.10.0-to-4.0.0.sql`. |
| 4.0 bis 4.7 | Die Kette ab der Datei nach Ihrer Version. |
| 5.0 bis 5.4 | Die Kette ab der Datei nach Ihrer Version; danach [11.5](11-aktualisieren.md#115-von-54-oder-590-auf-600). |

### 14.5.2 Vor der ersten Datei: Zeichensatz und Tabellentyp

Konvertieren Sie unter **MySQL** zuerst MyISAM-Tabellen nach InnoDB und eine Datenbank, die nicht UTF-8 ist, nach
UTF-8, wie es der Hersteller in seinem Schritt von 3.10 auf 4.0 tat. Beide Skripte sind in Exponential 6.0 enthalten
und lesen die Verbindung aus den Einstellungen der Website:

```bash
php bin/php/ezconvertmysqltabletype.php --list
php bin/php/ezconvertmysqltabletype.php --newtype=InnoDB
php bin/php/ezconvertdbcharset.php --help
php bin/php/ezconvertdbcharset.php
```

- `ezconvertmysqltabletype.php` akzeptiert `--host`, `--user`, `--password`, `--database`, `--list`, `--newtype` und
  `--usecopy`.
- `ezconvertdbcharset.php` läuft unter MySQL, PostgreSQL und Oracle (andere Treiber lehnt es ab). Seine Optionen:
  `--collation` (Standard `utf8_general_ci`), `--iconv-character-set` (für einen Zeichensatz, den iconv und die
  Datenbank unterschiedlich benennen; das eigene Beispiel des Skripts ist `windows-1252` und `iso-8859-1`),
  `--log-filename` sowie `--extra-xml-attributes`, `--extra-xml-data` und `--extra-serialized-data` für Ihre eigenen
  Tabellen oder Datentypen, die XML oder serialisiertes PHP speichern. Serialisiertes PHP speichert Bytelängen; ein
  eigener Datentyp, der serialisierte Daten ablegt, muss daher dort angegeben werden, sonst lässt er sich nach der
  Konvertierung nicht mehr deserialisieren.
- Belassen Sie die konvertierten Tabellen bei MySQLs `utf8` (3 Byte), denn das verwendet die Verbindung. MySQL 8 legt
  neue Tabellen mit `utf8mb4` an, sofern der Standard der Datenbank nichts anderes vorgibt, und die Update-Dateien legen
  Tabellen an, ohne einen Zeichensatz zu nennen; setzen Sie den Standard der Datenbank auf denselben Zeichensatz wie die
  konvertierten Tabellen, bevor Sie sie ausführen, damit alte und neue Tabellen verknüpft werden können:
  `ALTER DATABASE your_database CHARACTER SET utf8 COLLATE utf8_general_ci;`

Unter **PostgreSQL** wird die Kodierung der Datenbank bei ihrer Anlage festgelegt; eine Datenbank, die nicht UTF-8 ist,
wird ebenfalls mit `ezconvertdbcharset.php` konvertiert oder per Dump in eine neue UTF-8-Datenbank übertragen.

### 14.5.3 Vorabprüfungsabfragen

Drei Anweisungen der alten Kette scheitern an Daten, die ältere Releases zuließen. Führen Sie diese Abfragen vor der
Kette aus; jede muss null Zeilen zurückgeben.

```sql
-- 4.1 adds UNIQUE INDEX ezcontentobject_remote_id (from 4.0.1)
SELECT remote_id, COUNT(*) FROM ezcontentobject
 WHERE remote_id IS NOT NULL GROUP BY remote_id HAVING COUNT(*) > 1;

-- 4.1 adds UNIQUE INDEX ezgeneral_digest_user_settings_address before it removes stale rows
SELECT address, COUNT(*) FROM ezgeneral_digest_user_settings
 GROUP BY address HAVING COUNT(*) > 1;

-- 6.0.15 and later refuse to sign in an account without a settings row
SELECT u.contentobject_id, u.login FROM ezuser u
 LEFT JOIN ezuser_setting s ON s.user_id = u.contentobject_id
 WHERE s.user_id IS NULL;
```

Was zu tun ist, wenn sie Zeilen zurückgeben:

- **Doppelte Remote-IDs von Objekten.** Führen Sie die 4.1-Datei ohne die Zeile
  `ALTER TABLE ezcontentobject ADD UNIQUE INDEX ezcontentobject_remote_id(remote_id);` aus, schließen Sie die Kette ab,
  führen Sie `php update/common/scripts/4.1/fixobjectremoteid.php --mode=a` aus (`--mode=d` fragt bei jedem Duplikat
  nach) und legen Sie dann den Index mit genau dieser Anweisung an. Zur selben Familie gehören `fixclassremoteid.php`
  und `fixnoderemoteid.php`.
- **Doppelte Digest-Adressen.** Behalten Sie vor der 4.1-Datei jeweils die älteste Zeile jeder Adresse:

  ```sql
  -- MySQL
  DELETE d1 FROM ezgeneral_digest_user_settings d1
    JOIN ezgeneral_digest_user_settings d2 ON d1.address = d2.address AND d1.id > d2.id;
  -- PostgreSQL
  DELETE FROM ezgeneral_digest_user_settings a
   USING ezgeneral_digest_user_settings b WHERE a.address = b.address AND a.id > b.id;
  ```

  Die 5.0-Datei ersetzt die Adresse später durch eine Spalte `user_id` mit einem eindeutigen Index.
- **Benutzer ohne `ezuser_setting`.** Siehe [14.10](#1410-benutzer-passwörter-und-anmeldung).

### 14.5.4 Zwei Phasen: zuerst das Schema, danach die Datenreparatur

Die PHP-Reparaturskripte in `update/common/scripts/` laufen auf dem Kernel von Exponential 6.0, und die
Persistent-Object-Definitionen dieses Kernels beschreiben das **aktuelle** Schema. So liest etwa `eZNodeAssignment` die
Spalten `priority` und `is_hidden`, die erst die 5.2-Datei hinzufügt; `4.3/updatenodeassignment.php` kann daher mit
dem heutigen Code nicht gegen eine 4.3-Datenbank laufen. Deshalb gilt:

1. **Phase A** wendet jede SQL-Datei von Ihrer Version bis 6.0.15 der Reihe nach an, ohne etwas dazwischen, außer dem
   einen Skript, das das alte Schema sehen muss (`updateimagesystem.php`, siehe unten).
2. **Phase B** führt die Reparaturskripte jeder durchlaufenen Version in Versionsreihenfolge gegen das fertige Schema
   aus.

Das unterscheidet sich von den versionsbezogenen Seiten des Herstellers, die die Skripte jeder Version direkt nach
ihrer SQL-Datei mit dem passenden alten Code ausführten. Mit der heutigen einheitlichen Codebasis ist die
zweiphasige Reihenfolge diejenige, die funktioniert.

`updateimagesystem.php` konvertiert `ezimage`-Attribute von Websites, die vor 3.3 angelegt wurden, und benötigt die
Tabelle `ezimage`, die die 4.1-Datei entfernt. Prüfen Sie, ob Sie es brauchen:

```sql
SELECT COUNT(*) FROM ezimage;
```

Gibt die Abfrage 0 zurück, überspringen Sie es. Andernfalls führen Sie `php update/common/scripts/4.1/updateimagesystem.php`
vor der 4.1-Datei aus, zuerst auf der Probekopie: Es lässt den 6.0-Kernel gegen ein 4.0-Schema laufen.

### 14.5.5 Phase A: die SQL-Dateien

Wenden Sie jede Datei einmal und vollständig in dieser Reihenfolge an, beginnend nach Ihrer Version. Bis 5.4 sind die
Dateinamen unter `update/database/mysql/` und `update/database/postgresql/` gleich.

| # | Von | Datei | Was sie ändert |
|---|---|---|---|
| 1 | 3.10 | `4.0/dbupdate-3.10.0-to-4.0.0.sql` | entfernt verwaiste Zeilen von `ezuser_setting` und Klassengruppen; Tabelle `ezurlwildcard` (ab 3.10.1) |
| 2 | 4.0 | `4.1/dbupdate-4.0.0-to-4.1.0.sql` | Objektzustände (Tabellen `ezcobj_state*`), `ezurlalias_ml.alias_redirects` (ab 3.10.1), eindeutige Remote-ID für Objekte (ab 4.0.1), `ezurlalias_ml_incr` (ab 4.0.2), `ezsession.user_hash`, `ezpending_actions.created`; **entfernt `ezimage` und `ezimagevariation`** |
| 3 | 4.1 | `4.2/dbupdate-4.1.0-to-4.2.0.sql` | Textspalten von `ezworkflow_event` (ab 4.1.1), Standardwert für `ezsession.user_hash` (ab 4.1.2), Indizes für Schlüsselwörter und Informationssammlungen (ab 4.1.4), Indizes für Richtlinien und Workflows |
| 4 | 4.2 | `4.3/dbupdate-4.2.0-to-4.3.0.sql` | RSS-Enclosure, Spalten für Klassenbeschreibung und Kategorie, Tabelle `ezscheduled_script` |
| 5 | 4.3 | `4.4/dbupdate-4.3.0-to-4.4.0.sql` | entfernt `ezcontentobject.is_published`; `ezsection.identifier`; `ezpolicy.original_id`; `ezuser`-Attribute nicht mehr übersetzbar |
| 6 | 4.4 | `4.5/dbupdate-4.4.0-to-4.5.0.sql` | Tabelle `ezpublishingqueueprocesses` für asynchrones Veröffentlichen; Tabellen `ezprest_*` der REST-API |
| 7 | 4.5 | `4.6/dbupdate-4.5.0-to-4.6.0.sql` | `ezorder_nr_incr`; Klassen-IDs des Multiplexer-Workflows nach `data_text5` verschoben |
| 8 | 4.6 | `4.7/dbupdate-4.6.0-to-4.7.0.sql` | `ezpending_actions.id`; veraltete Kontoschlüssel entfernt; Float-Spalten zu `double`; Trigger umbenannt |
| 9 | 4.7 | `5.0/dbupdate-4.7.0-to-5.0.0.sql` | `ezcobj_state_group_language.real_language_id`; Digest-Einstellungen über `user_id` zugeordnet; Index auf `ezuser.login` |
| 10 | 5.0 | `5.1/dbupdate-5.0.0-to-5.1.0.sql` | Indizes neu geordnet; **löscht `ezcontentobject_link`-Zeilen mit `op_code <> 0` oder `relation_type = 0`** und entfernt `op_code` |
| 11 | 5.1 | `5.2/dbupdate-5.1.0-to-5.2.0.sql` | Sprachmasken auf `BIGINT`; doppelte `ezurl_object_link`-Zeilen entfernt (MySQL); `ezcontentobject.language_mask` korrigiert; `eznode_assignment.priority`, `is_hidden` |
| 12 | 5.2 | `5.3/dbupdate-5.2.0-to-5.3.0.sql` | Index auf `contentclassattribute_id` |
| 13 | 5.3 | `5.4/dbupdate-5.3.0-to-5.4.0.sql` | entfernt `ezsearch_return_count`; leere Datumswerte als `NULL` gespeichert; verwaiste `ezuser_setting`-Zeilen entfernt |
| 14 | 5.4 | `6.0/dbupdate-5.4.0-6.0.0.sql` (MySQL), `6.0/dbupdate-5.4-to-6.0.sql` (PostgreSQL) | vermerkt 6.0.0; erweitert `ezuser.password_hash` von 50 auf 255 für die bcrypt-Hashes von 6.0, fügt `ezcontentobject_trash.trashed` hinzu und benennt unter PostgreSQL die Sequenzen in `<table>_<column>_seq` um ([11.3](11-aktualisieren.md#die-verzeichnisse-612-72-und-73)) |
| 15 | 6.0.0 | `6.0/dbupdate-6.0.0-6.0.15.sql` | die Tabellen und Spalten der 6.0-Linie ([11.6](11-aktualisieren.md#116-von-einer-beliebigen-60x-auf-den-heutigen-stand), Schritt 2) |

Die 5.4-Datei sagt zu ihrer Datumsaktualisierung: „Skip this if updating from 5.3.3 or higher as this should ideally
not be applied twice.“ Wenn Sie von 4.x kommen, liegen Sie unter 5.3.3; wenden Sie sie also an.

**MySQL.** Jede alte Datei von 4.0 bis 5.4 sowie `6.0/dbupdate-5.4.0-6.0.0.sql` beginnt mit
`SET default_storage_engine=InnoDB;`, was jedes MySQL ab 5.5.3 und MariaDB akzeptieren; wenden Sie die Dateien also
vollständig an. Kopien von vor Oktober 2026 begannen mit `SET storage_engine=...;`, was MySQL ab 5.7.5 und MariaDB ab
12.0 ablehnen; verwenden Sie die aktuellen Dateien:

```bash
mysql -u USER -p DATABASE < update/database/mysql/4.1/dbupdate-4.0.0-to-4.1.0.sql
```

Der `mysql`-Client hält in diesem Modus bei der ersten fehlschlagenden Anweisung an. Wenn das passiert, notieren Sie
die Anweisung, spielen Sie die Sicherung zurück, beheben Sie die Ursache (meist ein Fall aus der Vorabprüfung) und
beginnen Sie erneut mit der Sicherung.

**PostgreSQL.** Halten Sie ebenfalls beim ersten Fehler an:

```bash
psql -v ON_ERROR_STOP=1 -U USER -d DATABASE -f update/database/postgresql/4.1/dbupdate-4.0.0-to-4.1.0.sql
```

Die Erweiterung `pgcrypto` (Funktion `digest`) muss für 6.0 in der Datenbank vorhanden sein ([11.3](11-aktualisieren.md#113-die-update-dateien)).

**SQLite und MongoDB.** Für sie gibt es keine alte Kette: Die Unterstützung für SQLite beginnt mit 6.0.1, die für
MongoDB mit 6.0.14. Migrieren Sie unter MySQL oder PostgreSQL; eine fertige Website danach auf eine andere Engine zu
übertragen, ist ein eigenes Projekt ([Kapitel 9](09-datenbanken.md)).

**Oracle.** Die Extension `ezoracle` enthält die Kernel-Kette für Oracle bis 5.3 in
`extension/ezoracle/update/database/` ([11.3](11-aktualisieren.md#113-die-update-dateien)). Für 5.4 oder 6.0 gibt es keine
Oracle-Datei: Schreiben Sie die Änderungen von 5.4 aus der MySQL-Datei in Oracle-Syntax und führen Sie dann von Hand
aus, was die 6.0-Dateien auf den anderen Engines tun, mindestens `ALTER TABLE ezuser MODIFY ( password_hash VARCHAR2(255) );`
und, falls der Tabelle die Spalte fehlt, `ALTER TABLE ezcontentobject_trash ADD ( trashed INTEGER DEFAULT 0 NOT NULL );`
([11.5](11-aktualisieren.md#115-von-54-oder-590-auf-600)), danach die Spalten und Tabellen der 6.0.15-Datei in Oracle-Syntax
(die Audit-Tabellen kommen aus `createaudittables.php`, das unter Oracle funktioniert). Proben Sie diesen Weg doppelt so
oft wie die anderen; er ist der am wenigsten begangene.

**Den Verzeichnisbaum prüfen, bevor Sie beginnen.** `php bin/php/console exp:checkdbfiles --no-verify-branches`
vergleicht die Update-Dateien mit dem Upgrade-Pfad, jedes Verzeichnis von 4.0 bis 7.3 für jede Engine, und gibt `!`
für eine fehlende Datei und `?` für eine unerwartete aus. Keine Ausgabe bedeutet, dass der Baum vollständig ist. Ohne
`--no-verify-branches` versucht es außerdem, das stillgelegte SVN-Repository des alten Herstellers zu erreichen, und
meldet eine `C`-Zeile.

**Markierungen für Patch-Releases.** Einige Dateien enthalten Blöcke, die mit `-- START: from 4.0.1` ...
`-- END: from 4.0.1` markiert sind (ebenso `from 3.10.1`, `from 4.0.2`, `from 4.1.0`, `from 4.1.1`, `from 4.1.2`,
`from 4.1.4`). Sie enthalten die Anweisungen, die eine spätere Patch-Release der älteren Linie bereits ausgeliefert
hat. Wenn Ihre Datenbank das eigene Update dieser Patch-Release durchlaufen hat (zum Beispiel haben Sie 4.0.2
betrieben und dessen Update angewendet), wurden diese Anweisungen bereits ausgeführt und scheitern mit „duplicate
column“ oder „duplicate key“. Erstellen Sie eine Kopie der Datei ohne die Blöcke, die Ihre Website bereits hat, und
wenden Sie die Kopie an. Das ist der Hinweis „select the appropriate SQL for 4.1.1, 4.1.2, 4.1.3 or 4.1.4“ auf der Seite
des Herstellers zum
[direkten Upgrade von 4.1 auf 4.7](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Direct-upgrading/Direct-upgrading-to-4.7-from-4.1-4.2-4.3-4.4-and-4.5/Direct-upgrading-from-4.1-to-4.7.html).
Die 4.0-Seite des Herstellers nennt außerdem eine Datei `dbupdate-4.0.0-to-4.0.1.sql`; in diesem Repository sind ihre
Anweisungen der Block `from 4.0.1` der 4.1-Datei.

**`unstable/`-Verzeichnisse** enthalten die Dateien von Alphas, Betas und Release Candidates. Eine Website, die eine
finale Release betrieben hat, wendet sie nie an. Eine Website, die eine Vorabversion betrieben hat, muss statt der
stabilen Datei die übrigen `unstable/`-Dateien dieser Linie bis zur finalen Release anwenden.

**Cluster-Dateien (nur MySQL).** Die DFS-Metadatendatenbank ist eine eigene Datenbank. Wenden Sie an derselben Stelle
der Kette auf sie an:

| Schritt | Datei | Für DFS |
|---|---|---|
| 4.3 | `4.3/dbupdate-cluster-4.2.0-to-4.3.0.sql` | nein: Sie aktualisiert `ezdbfile`, den entfernten Datenbank-Cluster |
| 4.7 | `4.7/dbupdate-cluster-4.6.0-to-4.7.0.sql` | nur die Anweisung für `ezdfsfile`; die für `ezdbfile` gilt dem entfernten Handler |
| 5.2 | `5.2/dbupdate-cluster-5.1.0-to-5.2.0.sql` | ja: legt `ezdfsfile_cache` an |
| 5.4 | `5.4/dbupdate-cluster-5.3.0-to-5.4.0.sql` | ja: benennt den Bereich `images` in `ezdfsfile` in `image` um |

### 14.5.6 Phase B: die Reparaturskripte

Führen Sie nach Phase A aus dem Wurzelverzeichnis der Installation die Skripte jeder durchlaufenen Version in dieser
Reihenfolge aus. Starten Sie jedes zuerst mit `--help`. Skripte, die einen Probelauf anbieten (`--dry-run`, oder `-n`,
wo es „nichts ändern“ bedeutet), erhalten zuerst einen solchen.

| Version | Befehl | Wann |
|---|---|---|
| 4.0 | `php update/common/scripts/4.0/updatebinaryfile.php` | ergänzt die fehlende Dateiendung bei Dateien von `ezbinaryfile` |
| 4.0 | `php update/common/scripts/4.0/updatevatcountries.php` | nur, wenn die Website den Schritt 3.9.3 übersprungen hat („Fixes bug with aplying VAT rules“) |
| 4.0 | `php update/common/scripts/4.0/updatetipafriendpolicy.php -s <admin siteaccess> -l <admin login> -p <password>` | optional; fügt eine Tip-a-friend-Rolle für alle Gruppen außer Anonymous hinzu |
| 4.1 | `php update/common/scripts/4.1/addlockstategroup.php` | **einmal**; legt die Zustandsgruppe `ez_lock` an |
| 4.1 | `php update/common/scripts/4.1/fixclassremoteid.php`, `fixnoderemoteid.php`, `fixobjectremoteid.php` (`--mode=a` oder `--mode=d`) | doppelte Remote-IDs |
| 4.1 | `php update/common/scripts/4.1/fixezurlobjectlinks.php --fix` | Links in XML-Blöcken, die nicht für alle Versionen oder Übersetzungen verknüpft sind |
| 4.1 | `php update/common/scripts/4.1/initurlaliasmlid.php` | initialisiert `ezurlalias_ml_incr` |
| 4.1 | `php update/common/scripts/4.1/correctxmlalign.php` | `custom:align` zu `align`, fehlendes `align` bei Einbettungen (`--skip-embed-align`, `--skip-custom-align`) |
| 4.2 | `php update/common/scripts/4.2/fixorphanimages.php` | Fehler 15155 des Bild-Alias-Handlers (`-n` überspringt die Sicherheitswartezeit von 10 Sekunden) |
| 4.3 | `php update/common/scripts/4.3/updatenodeassignment.php` | ungenutzte Knotenzuweisungen (Fehler 15478) |
| 4.4 / 4.5 | `php update/common/scripts/4.5/updatesectionidentifier.php` | Sektionen ohne Bezeichner; die Kopien von 4.4 und 4.5 sind hier identisch, führen Sie eine davon aus |
| 4.6 | `php update/common/scripts/4.6/removetrashedimages.php` | optional; Bilddateien von Inhalten im Papierkorb |
| 4.6 | `php update/common/scripts/4.6/updateordernumber.php` | Websites mit Webshop: gleicht `ezorder_nr_incr` an vorhandene Bestellnummern an |
| 5.0 | `php update/common/scripts/5.0/deduplicatecontentstategrouplanguage.php` | **erforderlich**: entfernt Duplikate und verlegt den Primärschlüssel auf `real_language_id`, was die 5.0-Datei ihm überlässt |
| 5.0 | `php update/common/scripts/5.0/restorexmlrelations.php` | nur Websites mit mehr als einer Sprache |
| 5.0 | `php update/common/scripts/5.0/disablesuspicioususers.php`, danach mit `--disable` | Konten, deren Login `<` oder `>` enthält |
| 5.1 | `php update/common/scripts/5.1/fiximagesoutsidevardir.php --dry-run` | Bildverweise außerhalb von `VarDir` |
| 5.2 | `php update/common/scripts/5.2/cleanupdfscache.php` | nur DFS: Cache-Zeilen in `ezdfsfile` |
| 5.3 | `php update/common/scripts/5.3/recreateimagesreferences.php --dry-run` | fehlende `ezimagefile`-Zeilen |
| 5.3 | `php update/common/scripts/5.3/updatenodeassignmentparentremoteids.php` | `eznode_assignment.parent_remote_id` |
| 5.4 | `cleanuntranslatablerelations.php --dry-run`, `cleanupfieldvaluerelations.php`, `fixremovedezurlobjectlinks.php` (`--fix`), `fixtrashedimagereferences.php --dry-run` | veraltete Relationen, Links und Bildverweise |
| 6.0 | `php update/common/scripts/6.0/createaudittables.php --dry-run`, danach ohne | die Audit-Indextabellen |
| beliebig | `php update/common/scripts/updatenbxmlcontents.php --dry-run` | Kodierung geschützter Leerzeichen in XML-Text |

Die Direkt-Upgrade-Seite des Herstellers sagt, man solle das Sektionsbezeichner-Skript von 4.4 überspringen („(bug in
update script)“) und das von 4.5 verwenden; in diesem Repository sind beide Dateien gleich.

## 14.6 Dateien: Speicher, Bilder, Binärdateien und der Cluster

- **Kopieren Sie `var/`** (oder mindestens `var/<VarDir>/storage/`) von der alten Website. Belassen Sie
  `[FileSettings] VarDir` in `settings/override/site.ini.append.php` genau so, wie es war: Die Pfade jedes
  gespeicherten Bildes und jeder Datei in `ezimagefile` und im `ezimage`-XML beginnen damit. Eine Website, deren
  `VarDir` sich im Laufe ihres Bestehens geändert hat, hat Bilder außerhalb davon; `5.1/fiximagesoutsidevardir.php`
  korrigiert die Verweise.
- **Kopieren Sie keine Caches.** Lassen Sie `var/cache/`, `var/<VarDir>/cache/` und `var/autoload/` weg; der neue Code
  baut sie neu auf. Die Seiten des Herstellers beenden jeden Schritt mit `php bin/php/ezcache.php --clear-all --purge`
  und einer manuellen Kontrolle von `var/cache/` und `var/<siteaccess>/cache/`; derselbe Befehl funktioniert hier
  (`--purge` entfernt die Dateien physisch).
- **Bild-Aliase** werden bei Bedarf aus dem Originalbild und den Aliasen von `image.ini [AliasSettings]
  AliasList[]` erzeugt (standardmäßig `reference`, `small`, `tiny`, `medium`, `large`, `rss`). Übernehmen Sie Ihre
  eigenen Alias-Blöcke aus der alten `image.ini.append.php` und leeren Sie den Alias-Cache
  (`php bin/php/ezcache.php --clear-id=imagealias`); jeder Alias wird beim ersten Abruf neu erzeugt.
  `image.ini [ImageConverterSettings] ImageConverters[]` listet `GD` und `ImageMagick`;
  `[ImageMagick] Executable=convert`, `ExecutablePath` leer.
- **Dateiberechtigungen.** Die Seiten des Herstellers verwendeten `chmod -R a+rwx design extension settings var`. Tun
  Sie das nicht: Der Benutzer, unter dem PHP läuft, muss in `var/` schreiben können (und in `settings/override/` nur,
  wenn die Administration Einstellungen bearbeitet); siehe [Kapitel 8](08-die-website-ausliefern.md) und
  [Kapitel 13](13-sicherheitshaertung.md).

### Der Cluster

| Alter Handler | In Exponential 6.0 |
|---|---|
| `eZFSFileHandler` | derselbe, der Standard (`file.ini [ClusteringSettings] FileHandler=eZFSFileHandler`) |
| `eZDFSFileHandler` mit dem Backend `mysql` | `eZDFSFileHandler` mit `eZDFSFileHandlerMySQLiBackend` (`file.ini [eZDFSClusteringSettings] DBBackend`, der Standard); das einfache `mysql`-Backend wurde entfernt |
| `eZDFSFileHandler` unter PostgreSQL | das PostgreSQL-DFS-Backend wurde 2012 in die Extension `ezpostgresqlcluster` verlagert |
| `eZDBFileHandler` (`ezdbfile`), `eZFS2FileHandler` | entfernt (EZP-20288): Lassen Sie sie auf der alten Website, siehe 14.4 |

`index_cluster.php` liefert Cluster-Dateien aus. Es liest `config.php` und `config.cluster.php` und erwartet die
Konstanten `CLUSTER_STORAGE_BACKEND` (zum Beispiel `dfsmysqli`), `CLUSTER_STORAGE_HOST`, `CLUSTER_STORAGE_PORT`,
`CLUSTER_STORAGE_USER`, `CLUSTER_STORAGE_PASS` und `CLUSTER_STORAGE_DB` (`config.php-RECOMMENDED` zeigt sie). Die
Seiten 4.5 und 4.6 des Herstellers setzen `define( 'STORAGE_BACKEND', 'dfsmysqli' )` innerhalb von
`index_cluster.php`; dieser Name wird nicht mehr gelesen, und die alten Dateien `index_cluster_*` und `index_image_*`
sind aus dem Document Root zu entfernen (Hinweise zu 4.7). Die Werkzeuge für eine DFS-Website:
`php bin/php/clusterize.php` (`-u` verschiebt Dateien zurück ins einfache Dateisystem), `php bin/php/dfscleanup.php`
(Konsistenz zwischen `ezdfsfile` und dem Mount), `php bin/php/clusterpurge.php`.

## 14.7 Einstellungen und Siteaccesses

### Was übernommen wird

Kopieren Sie aus der alten Installation in die neue **nur**:

- `settings/override/` (globale Overrides),
- `settings/siteaccess/<name>/` für jeden Siteaccess,
- `config.php` oder `config.cluster.php`, falls die alte Website sie hatte (vergleichen Sie mit
  `config.php-RECOMMENDED`),

und gehen Sie sie anschließend mit der folgenden Liste durch. Kopieren Sie niemals die alten `settings/*.ini`-Dateien
über die neuen: Die neuen Standardwerte sind Teil des Codes. Die Seiten des Herstellers sagten dasselbe in ihrem
Schritt 1 (`settings/siteaccess`, `settings/override` und die eigenen `design/<site>`-Verzeichnisse der Website in die
entpackte neue Release kopieren).

### Zu ändernde Einstellungen

| Alte Einstellung | Was zu tun ist |
|---|---|
| `[DatabaseSettings] DatabaseImplementation=ezmysql` | Funktioniert: `site.ini [DatabaseSettings] ImplementationAlias[ezmysql]=eZMySQLiDB`. Schreiben Sie der Klarheit halber `ezmysqli`. Die Seiten 4.5 und 4.6 des Herstellers verlangten dieselbe Änderung. |
| `[DatabaseSettings] Charset`, `i18n.ini [CharacterSettings] Charset` | `utf-8` (die neuen Standardwerte); entfernen Sie andere Werte, sobald die Datenbank konvertiert ist. |
| `[ExtensionSettings] ActiveExtensions[]` | Entfernen Sie `ezdhtml` (seit 4.1 durch `ezoe` ersetzt) und jede Extension, die Sie nicht installieren. Behalten Sie die Reihenfolge bei: Sie entscheidet, welche Templates und Einstellungen welcher Extension gewinnen ([Ladereihenfolge der Extensions](../funktionen/6.0/ladereihenfolge-der-extensions.md)). `ezjscore` wird von der Administration und von ezwebin benötigt. |
| `[UserSettings] HashType`, `SiteName`, `UpdateHash` | Siehe [14.10](#1410-benutzer-passwörter-und-anmeldung). |
| `[DesignSettings]` des Administrations-Siteaccess | Das alte Design `admin2` wurde 2012 zum ausgelieferten Design `admin`; die heutigen Administrationsdesigns sind `admin`, `admin3`, `admin4` und `admin4l`, dazu die YUI-freie klassische graue Oberfläche als Siteaccess `classic` ([YUI entfernt](../bc/6.0/yui-entfernung.md)). Übernehmen Sie die `[DesignSettings]` eines frisch installierten Administrations-Siteaccess statt der 4.x-Liste. |
| `override.ini.append.php` des Administrations-Siteaccess | Entfernen Sie die Regeln für Benutzer-Tabs, die die 4.6-Seite des Herstellers nennt (`[window_controls]`, `[windows]` mit Treffer auf `ezusernavigationpart`); ihre Templates gibt es nicht mehr. |
| Blöcke in `override.ini` | Seit dem 12. Juli 2026 werden Blöcke nach `Priority` sortiert, die niedrigste zuerst, und ein Block ohne Priorität kommt zuletzt. Geben Sie Blöcken, die gewinnen müssen, eine `Priority` ([11.6](11-aktualisieren.md#116-von-einer-beliebigen-60x-auf-den-heutigen-stand), Schritt 3). |
| YUI-Schlüssel in `ezjscore.ini` (`ExternalScripts[yui*]`, `LocalScripts[yui*]`, `[YUI3]`, `PreferredLibrary=yui3`) | Löschen; sie haben heute keine Bedeutung mehr. |
| `site.ini [SiteAccessSettings] DetectMobileDevice`, `MobileSiteAccessURL`, `MobileSiteAccessList[]` (4.7-Seite des Herstellers) | Nur, wenn Sie den mobilen Siteaccess verwendet haben; die Schlüssel sind unverändert. |
| `template.ini [PHP] PHPOperatorList[]` | Jede dort zugeordnete PHP-Funktion muss in PHP 8 existieren (14.9). |
| Dateien mit dem Namen `*.ini.append` oder `*.ini.php` | Werden weiterhin gelesen, sind aber seit 4.4 veraltet. Benennen Sie sie in `*.ini.append.php` um, mit der ersten Zeile `<?php /* #?ini charset="utf-8"?`, damit ein falsch konfigurierter Webserver sie nicht als Text ausliefern kann. |
| Extension-Verzeichnisse | Neu und optional: `[ExtensionSettings] AdditionalExtensionDirectories[]` erlaubt, dass Ihre eigenen Extensions in einem zweiten Wurzelverzeichnis liegen ([Zusätzliche Extension-Verzeichnisse](../bc/6.0/zusaetzliche-erweiterungsverzeichnisse.md)). |
| `MinPasswordLength`, `GeneratePasswordLength` | Neue Standardwerte 10 und 16 ([Passwortlänge](../bc/5.90/passwortlaenge.md)). |

Bauen Sie danach den INI-Cache neu auf und prüfen Sie jeden Siteaccess:

```bash
php bin/php/ezcache.php --clear-tag=ini
php bin/php/ezpgenerateautoloads.php -e
```

### Rewrite-Regeln

Die 4.x-Seiten des Herstellers fügten von Release zu Release Rewrite-Regeln hinzu und entfernten welche (`index_ajax.php`
für ezjscore-Aufrufe, `var/([^/]+/)?cache/(texttoimage|public)`, die Design-Verzeichnisse der Extensions).
`index_ajax.php` gibt es nicht mehr. Übernehmen Sie die alten Regeln nicht: Verwenden Sie die Regeln aus
[Kapitel 8](08-die-website-ausliefern.md) für Ihren Webserver, die genau auflisten, was ausgeliefert werden darf, und ergänzen
Sie nur Regeln, die Ihre eigenen Extensions benötigen.

### Siteaccesses

Die Siteaccess-Zuordnung (`[SiteAccessSettings] MatchOrder`, `HostMatchMapItems[]`, `URIMatch...`) ist unverändert;
kopieren Sie sie. Prüfen Sie jeden Siteaccess nach dem Umzug:

```bash
php bin/php/eztemplatecheck.php -s<siteaccess>
```

und öffnen Sie seine Startseite. Der Editor-Siteaccess und die neueren Administrationsdesigns sind Ergänzungen, die Sie
danach vornehmen können ([10. Nach der Installation](10-nach-der-installation.md)).

## 14.8 Designs, Templates und JavaScript

**Ihre Designs.** Kopieren Sie Ihre eigenen `design/<name>/`-Verzeichnisse oder, besser, verlagern Sie sie in eine
Design-Extension (`extension/<yours>/design/<name>/` mit `design.ini.append.php` `[ExtensionSettings] DesignExtensions[]`),
damit Updates von Exponential sie nie berühren. Überschreibende Templates liegen in `design/<name>/override/templates/`,
wie es die 4.0-Seite des Herstellers verlangte.

**Bearbeiten Sie keine paketierten Extensions.** ezwebin, ezflow, ezoe, ezjscore und die anderen Extensions aus
`composer.json` werden bei jedem Update ersetzt. Änderungen, die Sie auf der alten Website darin vorgenommen haben,
müssen als Overrides in Ihre eigene Design-Extension wandern.

**Prüfen Sie die Syntax** jedes Templates eines Siteaccess oder eines Verzeichnisses:

```bash
php bin/php/eztemplatecheck.php -s<siteaccess>
php bin/php/eztemplatecheck.php extension/<yours>/design/
```

**Was sich in Templates anders verhält:**

| Bereich | Änderung | Was zu tun ist |
|---|---|---|
| `wash` und die HTML-Escaping-Operatoren | Maskieren einfache Anführungszeichen unter jeder PHP-Version (`ENT_QUOTES \| ENT_SUBSTITUTE \| ENT_HTML401`) | Nichts, außer ein Test vergleicht die exakte Ausgabe; leeren Sie den Template-Cache einmal ([Unterstützung für PHP 8.0](../bc/6.0/php-8.0-unterstuetzung.md)). |
| `template.ini [PHP] PHPOperatorList[]` | Die zugeordnete PHP-Funktion wird direkt aufgerufen | Entfernen Sie Zuordnungen zu Funktionen, die PHP 8 entfernt hat (14.9). |
| YUI | `ezjsc::yui2`, `ezjsc::yui3`, `ezjsc::yui3io` laden nichts; `YUI(...)`, `YAHOO.*` sind undefiniert | Verlagern Sie den Code nach jQuery oder Exponential UI (`exp::*`); die Tabelle der Ersetzungen steht in [YUI entfernt](../bc/6.0/yui-entfernung.md). |
| jQuery | `ezjsc::jquery` ist jQuery 4.0.0 mit jQuery Migrate 4.0.2 (stiller Build), `ezjsc::jqueryUI` ist jQuery UI 1.14.2 (`extension/ezjscore/settings/ezjscore.ini`) | Ersetzen Sie `.bind()`, `.unbind()`, Event-Kurzformen, `jQuery.trim()`, `.size()` ([jQuery 4](../funktionen/6.0/jquery4-und-entfernung-von-yui.md)). |
| Online-Editor | ezoe verwendet standardmäßig TinyMCE 3.5.12, TinyMCE 8 ist optional zuschaltbar; das gespeicherte XML ist dasselbe | Eigene ezoe-Plugins, die für TinyMCE 3 geschrieben wurden, funktionieren mit der Standard-Engine weiter ([TinyMCE 3 und 8](../bc/6.0/ezoe-tinymce8.md)). |
| ezdhtml | Seit 4.1 entfernt | Verwenden Sie ezoe; das XML von `ezxmltext` braucht keine Konvertierung. |
| Benutzer-Tabs der Administration | `window_controls_user.tpl` und `windows_user.tpl` gibt es seit 4.6 nicht mehr | Entfernen Sie die Override-Regeln (14.7). |

**eZ Webin und eZ Flow.** In 4.x wurden sie mit `bin/php/ezwebinupgrade.php` und `bin/php/ezflowupgrade.php` über den
Paketserver des Herstellers aktualisiert. Hier kommt der Extension-Code über Composer (`se7enxweb/ezwebin`,
`se7enxweb/ezflow`); die beiden Skripte gibt es weiterhin für die installierten Website-Pakete
([11.6](11-aktualisieren.md#116-von-einer-beliebigen-60x-auf-den-heutigen-stand), Schritt 4). Die Direkt-Upgrade-Seite des Herstellers fügt für
eZ-Flow-Websites, die von 4.1 oder 4.2 kommen, einen Index hinzu:
`ALTER TABLE ezm_pool ADD INDEX ezm_pool__block_id__ts_hidden (block_id, ts_hidden);` Das aktuelle ezflow-Schema
(`extension/ezflow/sql/mysql/mysql.sql`) enthält diesen Index; legen Sie ihn also an, wenn er Ihrer Datenbank fehlt.

## 14.9 Eigene Extensions von PHP 5 auf PHP 8 portieren

Das ist bei den meisten Migrationen der größte Teil. Arbeiten Sie ihn auf Staging mit eingeschalteter Debug-Ausgabe ab.

### Die Checkliste

1. **Erfassen** Sie Ihre Extensions: alles unter `extension/`, was nicht in `composer.json` steht.
2. **Prüfen Sie die Syntax** jeder PHP-Datei mit dem PHP, das Sie betreiben werden:
   `find extension/<yours> -name '*.php' -print0 | xargs -0 -n1 php -l`.
   Ein Parse-Fehler an dieser Stelle legt die gesamte Website lahm, nicht nur die Extension.
3. **Erzeugen Sie das Autoload-Array neu** (`php bin/php/ezpgenerateautoloads.php -e`) und **laden Sie jede Klasse**:
   `php bin/php/console exp:checkclasses` meldet jede Klasse, die PHP nicht laden will (Signaturkonflikte, fehlende
   Elternklassen, entfernte Funktionen auf Klassenebene). `--kernel` bezieht den Kernel ein.
4. **Prüfen Sie Zeichen rund um die PHP-Tags**: `php bin/php/ezcheckphptag.php extension/<yours>`.
5. **Beheben Sie die unten aufgeführten PHP-8-Brüche**, danach die Deprecations, die die Debug-Ausgabe anzeigt.
6. **Templates und Einstellungen** wie in 14.7 und 14.8.
7. **Geben Sie der Extension eine Version**: `ezinfo.php` und `extension.xml`, angezeigt unter **Setup > Extensions**.

### Autoloading und Konstanten

4.0 ersetzte die Include-Listen durch eine Klasse pro Datei und generierte Autoload-Arrays. In Exponential 6.0 ist das
Kernel-Array `autoload/ezp_kernel.php`; die Arrays für Extensions, Kernel-Overrides und Tests werden von
`ezpgenerateautoloads.php` in `var/autoload/ezp_extension.php`, `ezp_override.php` und `ezp_tests.php` erzeugt (`-e`
Extensions, `-k` Kernel, `-o` Kernel-Overrides, `-s` Tests, `--exclude=<dir>`, `-n` Probelauf). Ein
Kernel-Override-Array wird nur verwendet, wenn `config.php` `EZP_AUTOLOAD_ALLOW_KERNEL_OVERRIDE` als true definiert.
Die Registrierung erfolgt über `spl_autoload_register()`; eine `function __autoload()` in Ihrem Code ist seit PHP 8.0
ein fataler Fehler.

Die globalen Konstanten von 3.x wurden in 4.0 durch Klassenkonstanten ersetzt (die
[Hinweise zu 4.0](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Upgrading-to-4.0/Important-notes.html) des
Herstellers verwenden `EZ_CONTENT_OBJECT_STATUS_DRAFT` als Beispiel), und die alten Namen sind nicht definiert:

```php
// before (3.x)
if ( $object->attribute( 'status' ) == EZ_CONTENT_OBJECT_STATUS_DRAFT )
// after
if ( $object->attribute( 'status' ) == eZContentObject::STATUS_DRAFT )
```

### Konstruktoren, die nach der Klasse benannt sind

Seit PHP 8.0 ist eine Methode mit dem Namen der Klasse **kein** Konstruktor. Bei einem Datentyp ist das fatal: `new`
fällt auf `eZDataType::__construct( $dataTypeString, $name, ... )` durch, dessen erste zwei Parameter Pflicht sind.

```php
// before (PHP 5)
class myRatingType extends eZDataType
{
    function myRatingType()
    {
        $this->eZDataType( 'myrating', 'Rating' );
    }
}

// after
class myRatingType extends eZDataType
{
    const DATA_TYPE_STRING = 'myrating';

    public function __construct()
    {
        parent::__construct( self::DATA_TYPE_STRING, 'Rating' );
    }
}
eZDataType::register( myRatingType::DATA_TYPE_STRING, 'myRatingType' );
```

Der Kernel behält Kompatibilitätsmethoden mit den alten Namen bei (`eZDataType::eZDataType()`,
`eZPersistentObject::eZPersistentObject()`, `eZWorkflowType::eZWorkflowType()`,
`eZWorkflowEventType::eZWorkflowEventType()`), sodass `$this->eZDataType( ... )` innerhalb Ihres eigenen
`__construct()` weiterhin funktioniert; was nicht mehr ausgeführt wird, ist der Konstruktor alten Stils Ihrer eigenen
Klasse. Siehe auch [5.90 und PHP 7](../bc/5.90/php7.md).

### Signaturen, die zur Elternklasse passen müssen

PHP 8 prüft beim Laden der Klasse jede überschreibende Methode gegen ihre Elternmethode. Referenzparameter, eine
abweichende Anzahl von Parametern oder ein abweichender Standardwert machen die Klasse unbrauchbar. Die
`eZDataType`-Methoden des Kernels nehmen Objekte als Wert entgegen:

```php
// before: fatal on PHP 8 ("Declaration ... must be compatible with ...")
function onPublish( &$contentObjectAttribute, &$contentObject, &$publishedNodes ) { ... }

// after: as in kernel/classes/ezdatatype.php
function onPublish( $contentObjectAttribute, $contentObject, $publishedNodes ) { ... }
```

Die eigenen Extensions von Exponential hatten genau das (`onPublish()` von syndication, `makeObjectsArray()` von
ezstarrating), siehe [Unterstützung für PHP 8.0](../bc/6.0/php-8.0-unterstuetzung.md).

### Aus PHP entfernte Funktionen und Syntax

| PHP-5-Code | Entfernt | Ersatz |
|---|---|---|
| `while ( list( $k, $v ) = each( $array ) )` | 8.0 | `foreach ( $array as $k => $v )` |
| `$f = create_function( '$a', 'return $a * 2;' );` | 8.0 | `$f = function ( $a ) { return $a * 2; };` |
| `$first = $string{0};` | 8.0 | `$first = $string[0];` |
| `split( ',', $s )`, `ereg()`, `eregi()` | 7.0 | `explode( ',', $s )`, `preg_match()` |
| `mysql_query()` und die `mysql_*`-Familie | 7.0 | `eZDB::instance()->arrayQuery()` / `query()` |
| `$obj =& new Foo();` | 7.0 | `$obj = new Foo();` |
| `myFunction( &$value );` (Referenz zur Aufrufzeit) | 5.4 | `function myFunction( &$value )` deklarieren, `myFunction( $value )` aufrufen |
| `implode( $pieces, ',' )` | 8.0 | `implode( ',', $pieces )` |
| `get_magic_quotes_gpc()`, `money_format()` | 8.0 | entfernen; `NumberFormatter` |
| `$a ? $b : $c ? $d : $e` | 8.0 | Klammern setzen |
| `(real) $x` | 8.0 | `(float) $x` |
| `crypt( $password )` ohne Salt | 8.0 (`ArgumentCountError`) | `password_hash()` |

Exponential hat dasselbe in seinen eigenen Extensions behoben: birthday verwendete `each()`, enhancedezbinaryfile
verwendete `split()`, das OAuth-Modul und swark riefen `implode()` mit vertauschten Argumenten auf.

### Zu beseitigende Deprecations (nicht fatal, aber störend und als Nächstes an der Reihe)

```php
// Implicitly nullable parameter (deprecated in 8.4)
function load( eZContentObject $object = null )      // before
function load( ?eZContentObject $object = null )     // after

// Dynamic properties (deprecated in 8.2): declare them
class myHelper
{
    public $cache = array();                          // declared, no warning
}

// null passed to string functions (deprecated in 8.1)
$length = strlen( $value );                           // before
$length = strlen( $value ?? '' );                     // after

// count() on something that may not be countable (warning since 7.2, TypeError since 8.0)
$n = count( $list );                                  // before
$n = is_countable( $list ) ? count( $list ) : 0;      // after

// "${var}" interpolation (deprecated in 8.2)
$s = "Node ${nodeID}";                                // before
$s = "Node {$nodeID}";                                // after

// Methods implementing Iterator, ArrayAccess, Countable (deprecated return types in 8.1)
#[\ReturnTypeWillChange]
public function current() { ... }
```

`utf8_encode()` und `utf8_decode()` sind seit 8.2 veraltet (`mb_convert_encoding()`); eine nicht statische Methode
statisch aufzurufen, ist seit 8.0 ein `Error`; Vergleiche zwischen Zahlen und nicht numerischen Strings haben sich in
8.0 geändert (`0 == 'a'` ist false). `eZPersistentObject` trägt bereits `#[AllowDynamicProperties]` und speichert nicht
deklarierte Felder selbst, sodass Unterklassen keine Warnung erzeugen; Ihre anderen Klassen müssen ihre Eigenschaften
deklarieren. Die Abfolge der PHP-8-Änderungen des Kernels selbst, Release für Release, steht in
[Unterstützung für PHP 8](../bc/6.0/php8.md).

### php.ini

| PHP-5-Einstellung | Status |
|---|---|
| `register_globals`, `magic_quotes_gpc`, `safe_mode`, `allow_call_time_pass_reference` | in PHP 5.4 entfernt; Code, der sich darauf verließ, muss `eZHTTPTool::instance()->postVariable()` und Verwandte lesen |
| `mbstring.func_overload` | in 8.0 entfernt |
| `date.timezone` | weiterhin setzen (die 4.0-Seite des Herstellers verlangte es ebenfalls); das Setup prüft es |
| `memory_limit`, `max_execution_time`, `file_uploads`, `upload_tmp_dir`, `open_basedir`, `allow_url_fopen`, `variables_order` | werden vom Setup-Assistenten geprüft (`kernel/setup/ezsetuptests.php`); Werte in [Kapitel 2](02-voraussetzungen.md) |

### eZ Components

4.x benötigte eZ Components, die neben dem Code installiert waren, in einer Mindestversion je Release (2008.2 für 4.1,
2009.1 für 4.2, 2009.2.1 für 4.3 und 4.4, später `ezcomponents-ezp45` bis `ezcomponents-ezp47`). Exponential verwendet
deren Fortführung, die Zeta Components, installiert per Composer (`zetacomponents/*` in `composer.json`) und geladen
über `ezcBase::autoload`. Die Klassennamen (`ezcMail`, `ezcConsoleInput` ...) sind dieselben; entfernen Sie jeden
Include-Pfad und jedes `ezc`-Verzeichnis, das Sie für 4.x eingerichtet haben.

## 14.10 Benutzer, Passwörter und Anmeldung

**Alte Hashes funktionieren weiter und werden bei der Anmeldung aktualisiert.** `ezuser.password_hash_type` benennt
das Verfahren jedes gespeicherten Hashes (`kernel/classes/datatypes/ezuser/ezuser.php`):

| Typ | Name | Hinweise |
|---|---|---|
| 0 | `empty` | kein eigenes Passwort (LDAP-, Textdatei- und Single-Sign-on-Benutzer); nicht in 4.x, 2019 hinzugefügt |
| 1 | `md5_password` | md5 des Passworts |
| 2 | `md5_user` | md5 von Login und Passwort |
| 3 | `md5_site` | md5 von Login, Passwort und `[UserSettings] SiteName` |
| 4 | `mysql` | MySQLs `PASSWORD()` |
| 5 | `plaintext` | |
| 6 | `bcrypt` | |
| 7 | `php_default` | `password_hash()`; der neue Standard (`[UserSettings] HashType=php_default`) |

Mit `[UserSettings] UpdateHash=true` (dem Standard) speichert eine erfolgreiche Anmeldung mit einem alten Hash einen
neuen vom Typ `HashType`. Was Sie übernehmen und prüfen sollten:

- **`md5_site`-Hashes** enthalten `[UserSettings] SiteName`. Das ist ein anderer Schlüssel als `[SiteSettings]
  SiteName`, und sein Standardwert ist `ez.no`. Wenn die alte Website ihn geändert hat, setzen Sie denselben Wert in
  `settings/override/site.ini.append.php`, sonst können sich diese Benutzer nicht anmelden.
- **`mysql`-Hashes** werden mit `PASSWORD()` geprüft, das MySQL 8.0 entfernt hat. Unter MySQL 8 können sich diese
  Konten nicht anmelden; setzen Sie ihre Passwörter zurück (siehe unten) oder lassen Sie sie „Passwort vergessen“
  verwenden.
- **Konten ohne `ezuser_setting`-Zeile** können sich seit August 2026 nicht anmelden: Eine fehlende Zeile gilt jetzt
  als deaktiviert. Die alte Kette löscht zwar verwaiste Zeilen (Dateien 4.0 und 5.4), legt aber niemals fehlende an.
  Finden Sie sie mit der Vorabprüfungsabfrage aus 14.5.3 oder mit den Abfragen der
  [Sicherheitsspezifikation vom August 2026](../spezifikationen/6.0/sicherheitshaertung-2026-08.md#f-06-konten-finden-die-sich-nicht-anmelden-können).
- **Die Hash-Spalte muss breit genug sein, bevor sich jemand anmeldet.** Eine 4.x-Datenbank hat
  `ezuser.password_hash varchar(50)`; die erste Anmeldung schreibt den Hash in einen bcrypt-Hash mit 60 Zeichen um.
  Die Datei von 5.4 auf 6.0.0 erweitert die Spalte auf 255 (und die Datei von 6.0.0 auf 6.0.15 noch einmal, für ältere
  Kopien); lassen Sie die Website also im Wartungsmodus, bis Phase A abgeschlossen ist. Wenn sich Benutzer gegen eine
  schmale Spalte angemeldet haben, hat die Spalte den Hash abgelehnt (strikter SQL-Modus) oder abgeschnitten, und
  diese Benutzer können sich nicht erneut anmelden: Erweitern Sie die Spalte und setzen Sie dann ihre Passwörter
  zurück. Unter Oracle erweitern Sie sie von Hand (14.5.5).
- **Passwörter mit weniger als 10 Zeichen** funktionieren weiter; die neue Mindestlänge gilt, wenn ein Passwort
  gesetzt wird.
- **Ein Passwort zurücksetzen** auf der Kommandozeile:
  `php bin/php/resetuserpassword.php -a admin -ap '<admin password>' -u <login> -p '<new password>'`.

## 14.11 Suche

Die eingebaute Engine (`site.ini [SearchSettings] SearchEngine=eZSearchEngine`) ist der Standard. Ihre Tabellen sind
Teil der Update-Kette (die 5.4-Datei entfernt `ezsearch_return_count`), doch bauen Sie den Index nach der Migration
neu auf, statt dem alten zu vertrauen:

```bash
php bin/php/updatesearchindex.php --clean
```

eZ Find (Solr) wird in `composer.json` als `se7enxweb/ezfind` vorgeschlagen; installieren Sie es als Paket und
indizieren Sie mit seinen eigenen Werkzeugen neu. `settings/solr.ini` und `php bin/php/console exp:solr` steuern einen
Solr-Server für diese Installation; er ist aus, bis `[SolrSettings] Enabled=true` gesetzt ist. Relationen werden seit
2018 eine Ebene tief indiziert ([Indizierung von Relationen](../bc/5.90/indizierung-von-beziehungen.md)): Suchergebnisse, die
sich auf Relationen von Relationen stützten, ändern sich.

## 14.12 Die Inhalte nach der Migration prüfen

Führen Sie die Prüfungen aus [11.7](11-aktualisieren.md#117-das-upgrade-überprüfen) aus und zusätzlich:

1. **Schema gegen die Referenz.** `share/db_schema.dba` ist das Schema der aktuellen Release. Vergleichen Sie die
   migrierte Datenbank damit; der Exit-Code sagt, ob sie sich unterscheiden:

   ```bash
   php bin/php/ezsqldiff.php --type=mysql --user=USER --password=PASS share/db_schema.dba DATABASE
   ```

   (`--type=postgresql` unter PostgreSQL). Die Ausgabe ist das SQL, das das zweite Argument (Ihre Datenbank) in das
   erste (die Referenz) überführen würde: Eine Zeile mit `CREATE TABLE` oder `ADD` nennt etwas, das ein ausgelassener
   Schritt hätte anlegen sollen, eine Zeile mit `DROP TABLE` eine Tabelle, die nur Ihre Datenbank hat. Tabellen Ihrer
   eigenen Extensions erscheinen als solche Drops; alles andere ist ein ausgelassener Schritt. Lesen Sie die Ausgabe,
   führen Sie sie nicht aus: Reparieren Sie, indem Sie die Sicherung zurückspielen und den ausgelassenen Schritt
   ausführen.
2. **Zahlen gegen die alte Datenbank.** Führen Sie die Größenabfragen aus 14.3 auf der alten und der migrierten
   Datenbank aus. Objekte, Attribute und Knoten müssen genau übereinstimmen; `ezurlalias_ml` wächst, wenn Aliase neu
   aufgebaut werden.
3. **URL-Aliase.** `php bin/php/updateniceurls.php` (mit `--update-nodes`), danach `php bin/php/verify_aliases.php`
   (`--fix` repariert, was sich gefahrlos reparieren lässt, `--verbose` erklärt).
4. **Bilder und Dateien.** Öffnen Sie eine Seite jeder Klasse mit Bildern; der Alias wird beim ersten Abruf erzeugt.
   Die Bildskripte von 5.1, 5.3 und 5.4 aus Phase B melden defekte Verweise.
5. **Die Upgrade-Prüfung der Administration.** **Setup > Upgrade-Prüfung**, beide Schaltflächen.
6. **Jeder Siteaccess**: `php bin/php/eztemplatecheck.php -s<siteaccess>`, danach die URL-Liste, die Sie in 14.4
   festgehalten haben, mit Vergleich der HTTP-Statuscodes.
7. **Bearbeiten.** Legen Sie in jeder Sprache ein Objekt jeder Klasse mit `ezxmltext`, einem Bild und einer Relation
   an, bearbeiten und veröffentlichen Sie es. Prüfen Sie, dass das Objekt in der Suche erscheint.
8. **Benutzer.** Melden Sie sich als Redakteur an, dessen Hash noch von einem alten Typ ist (14.10), und prüfen Sie,
   dass sich `password_hash_type` danach auf 7 geändert hat.
9. **Cronjobs** laufen ohne Fehler: `php runcronjobs.php` und `php runcronjobs.php frequent` (oder die in
   `cronjob.ini` gesetzten Teile), siehe [Kapitel 10](10-nach-der-installation.md).
10. **Logs.** `var/log/error.log` und `var/log/warning.log` (und `var/<VarDir>/log/`) bleiben ruhig, während Sie sich
    durch die Website klicken.

## 14.13 Häufige Probleme

Der Hersteller listet die Probleme auf, auf die man bei seiner eigenen Migration gestoßen ist
([Common migration issues](https://doc.ibexa.co/en/5.0/update_and_migration/migrate_to_ibexa_dxp/common_issues/)).
Zuerst ihre Entsprechungen in Exponential, danach die für den Legacy-Weg spezifischen:

| Problem | Ursache | Lösung in Exponential 6.0 |
|---|---|---|
| Ein Migrations-Bundle aktivieren | Die Werkzeuge des Herstellers kamen als Symfony-Bundle | Nichts zu aktivieren: Die Werkzeuge sind die Dateien in `update/` und die Skripte in `bin/php/`. |
| URL-Aliase müssen neu erzeugt werden | Die Alias-Tabellen haben sich zwischen 4.x und 5.x geändert | `php bin/php/updateniceurls.php --update-nodes`, danach `php bin/php/verify_aliases.php --fix`. |
| Bilder mit ungewöhnlichen Zeichen oder außerhalb des var-Verzeichnisses | Dateinamen und Änderungen von `VarDir` aus alten Releases | `5.1/fiximagesoutsidevardir.php`, `5.3/recreateimagesreferences.php`, `5.4/fixtrashedimagereferences.php` (jeweils mit `--dry-run`), `4.2/fixorphanimages.php`. Es gibt keinen Befehl zum Normalisieren von Pfaden; belassen Sie `VarDir`, wie es war. |
| „Unknown relation type 0“ | Zeilen mit `relation_type = 0` in `ezcontentobject_link` | Die 5.1-Datei löscht sie; `5.4/cleanuntranslatablerelations.php` und `cleanupfieldvaluerelations.php` entfernen andere veraltete Relationen. |
| Always-available-Kennzeichen an allen Feldern | Ein Verhalten der neueren öffentlichen API des Herstellers | Nicht zutreffend: Exponential schreibt und liest `language_id` so, wie es 4.x tat. |
| Unterelemente werden nicht aufgelistet (leerer `sort_key_string`) | Von der neueren öffentlichen API des Herstellers geschrieben | Für von 4.x geschriebene Daten nicht zutreffend. Sortierschlüssel werden gesetzt, wenn ein Attribut gespeichert wird; erneutes Veröffentlichen eines Objekts schreibt sie neu. |
| `SET storage_engine` scheitert | Eine Update-Datei von vor Oktober 2026; die Schreibweise wurde in MySQL 5.7.5 und MariaDB 12.0 entfernt | Verwenden Sie die aktuelle Datei, die `SET default_storage_engine` enthält (14.5.5). |
| Inhalte in den Papierkorb zu verschieben, scheitert; unter PostgreSQL lässt sich kein neues Objekt, kein neuer Knoten und keine neue Version speichern | `ezcontentobject_trash.trashed` fehlt, oder die alten Sequenznamen `<table>_s`: eine 6.0-Datei von vor Oktober 2026 | Wenden Sie die aktuelle `6.0/dbupdate-5.4.0-6.0.0.sql` (MySQL) oder `dbupdate-5.4-to-6.0.sql` (PostgreSQL) erneut an; sie ändert nur, was fehlt; setzen Sie danach die Versionszeile wieder auf `6.0.15stable` ([11.3](11-aktualisieren.md#die-verzeichnisse-612-72-und-73)). |
| „Duplicate entry“ beim Anlegen von `ezcontentobject_remote_id` oder des Index für Digest-Adressen | Duplikate, die ältere Releases zuließen | Die Vorabprüfung aus 14.5.3. |
| „Duplicate column“ oder „Duplicate key name“ in den Dateien 4.0 bis 4.2 | Die Anweisungen einer Patch-Release wurden bereits angewendet | Entfernen Sie diesen `START: from`-Block aus einer Kopie (14.5.5). |
| `Unknown column 'priority'` oder Ähnliches aus einem Reparaturskript | Skript vom heutigen Kernel gegen ein Zwischenschema ausgeführt | Zuerst Phase A, dann Phase B (14.5.4). |
| `Fatal error: Uncaught ArgumentCountError ... eZDataType::__construct()` | Konstruktor alten Stils in einem Datentyp | 14.9, „Konstruktoren, die nach der Klasse benannt sind“. |
| „Declaration of ... must be compatible with ...“ | Referenz- oder zusätzliche Parameter in einer überschreibenden Methode | 14.9, „Signaturen, die zur Elternklasse passen müssen“. |
| `Call to undefined function each()` / `create_function()` / `split()` / `mysql_query()` | Aus PHP entfernt | 14.9, die Tabelle der entfernten Funktionen. |
| `Class '...' not found` | Autoload-Array nicht neu erzeugt, oder die Klasse liegt in einer Datei, die der Generator ausschließt | `php bin/php/ezpgenerateautoloads.php -e`, Caches leeren. |
| Seite zeigt `?` statt Zeichen mit Akzent | Die Verbindung ist UTF-8, die Daten haben noch einen anderen Zeichensatz | `ezconvertdbcharset.php` (14.5.2). |
| „Illegal mix of collations“ unter MySQL 8 | Alte Tabellen in `utf8`, von den Update-Dateien angelegte Tabellen in `utf8mb4` | Setzen Sie den Standard der Datenbank vor der Kette (14.5.2); konvertieren Sie die neuen Tabellen in denselben Zeichensatz. |
| Benutzer können sich nicht anmelden | Fehlendes `ezuser_setting`, `md5_site` mit einem anderen `SiteName`, `mysql`-Hashes unter MySQL 8 | 14.10. |
| Administration hat keine Menüs, `YUI is not defined` in der Konsole | Eigene Templates oder Einstellungen laden noch YUI | [YUI entfernt](../bc/6.0/yui-entfernung.md). |
| Dateien einer Cluster-Website fehlen | Website mit `eZDBFileHandler` oder `STORAGE_BACKEND`-Konstanten | 14.4 und 14.6. |
| Ein Template-Override gewinnt nicht mehr | `override.ini`-Blöcke nach `Priority` sortiert, oder Reihenfolge der Extensions | 14.7. |
| Die Übersetzungen der Zustandsgruppen aus 5.0 erscheinen doppelt | `deduplicatecontentstategrouplanguage.php` nicht ausgeführt | Führen Sie es aus (Phase B, 5.0). |
| Veraltete Seiten nach der Umstellung | Alte Caches, oder ein Cache, der vor dem Neuladen von altem Code gefüllt wurde | `php bin/php/ezcache.php --clear-all --purge` nach dem Neuladen von PHP ([11.7](11-aktualisieren.md#117-das-upgrade-überprüfen)). |

Weitere Symptome und Lösungen für laufende Websites: [Kapitel 12](12-fehlerbehebung.md).

## 14.14 Rollback-Plan

Eine Migration wird zurückgenommen, indem man **nicht** umstellt, niemals, indem man die migrierte Datenbank
rückgängig macht.

- **Bis zur Umstellung:** Die alte Website liefert weiter aus (während des letzten Durchlaufs im Wartungsmodus). Der
  Rollback besteht darin, die alte Website aus dem Wartungsmodus zu nehmen (`php bin/php/maintenance.php off` unter
  Exponential; auf der alten Website mit dem, was sie verwendet hat).
- **Nach der Umstellung:** Behalten Sie den alten Server, seinen Code und eine unberührte Kopie seiner Datenbank
  mindestens so lange, bis Sie sicher sind. Richten Sie DNS oder den Proxy wieder darauf. Inhalte, die nach der
  Umstellung auf der neuen Website angelegt wurden, sind nicht in der alten Datenbank; frieren Sie deshalb das
  Bearbeiten in den ersten Tagen ein (oder protokollieren Sie es zumindest), oder planen Sie ein, diese Änderungen
  erneut einzugeben.
- Führen Sie **niemals** eine Update-Datei ein zweites Mal aus, um einen halb fertigen Durchlauf zu „reparieren“;
  spielen Sie den vor dem Durchlauf gezogenen Dump zurück und beginnen Sie das Ablaufprotokoll von vorn.
- **Bewahren Sie die Dumps auf:** den alten Produktiv-Dump, den migrierten Dump direkt nach 14.12 und das Archiv von
  `var/`. Mit diesen dreien können Sie jede der beiden Seiten neu aufbauen.

## 14.15 Wie lange es dauert

Die Dauer hängt viel stärker von der Größe einiger weniger Tabellen ab als von der Version, von der Sie kommen. Messen
Sie sie bei der Probe und planen Sie das Zeitfenster für den Produktivbetrieb anhand der Messung, mit einem Puffer.
Was womit wächst:

| Schritt | Wächst mit |
|---|---|
| Dump und Wiederherstellung | Größe der Datenbank auf der Festplatte |
| Konvertierung von Zeichensatz und Tabellentyp | Gesamtzahl der Zeilen; jede Tabelle wird neu geschrieben |
| Dateien 4.1, 5.1 und 5.2 | `ezcontentobject`, `ezcontentobject_attribute`, `ezcontentobject_link`, `ezurlalias_ml` (Index-Neuaufbauten und `BIGINT`-Änderungen schreiben diese Tabellen neu) |
| Skripte aus Phase B | Objekte und Attribute mit XML-Text, Bilder, Relationen; diejenigen mit `--iteration-limit` und `--iteration-sleep` lassen sich drosseln |
| `updateniceurls.php` | Knoten mal Sprachen |
| `updatesearchindex.php --clean` | Objekte; meist der längste einzelne Schritt |
| Kopieren von `var/` | Anzahl und Größe der gespeicherten Dateien |
| Portieren von Extensions | Zeilen Ihres eigenen PHP-Codes und Ihrer Templates, nicht die Größe der Datenbank |

Die Datenbankschritte können auf der Probekopie laufen, während die alte Website noch ausliefert; nur der letzte
Durchlauf braucht das Wartungsfenster. Die Neuindizierung kann nach der Umstellung abgeschlossen werden, wenn die Suche
eine Zeit lang unvollständig sein darf.

## 14.16 Die Upgrade-Seiten des Herstellers, Punkt für Punkt

Der Hersteller hat jeden Schritt der 4.x-Linie gesondert dokumentiert. Jeder Punkt dieser Seiten wird oben behandelt;
diese Tabelle sagt, wo, und wie sich Exponential unterscheidet.

| Punkt des Herstellers | Wo | Exponential 6.0 |
|---|---|---|
| Datenbank und Verzeichnis sichern; die Website offline nehmen ([Sicherung und Prüfungen](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Backup-and-consistency-checks.html)) | 14.4, [11.1](11-aktualisieren.md#111-bevor-sie-beginnen) | `bin/php/maintenance.php` nimmt die Website mit HTTP 503 offline |
| Datei- und Datenbankkonsistenzprüfung unter Setup > Upgrade-Prüfung | 14.4, 14.12 | dieselben Schaltflächen; die Dateiprüfung vergleicht mit `share/filelist.md5` |
| Fünf Schritte: Dateien, Datenbank, System-Skripte, Konfiguration, Caches ([Vorgehensweise](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/How-to-proceed.html)) | 14.5 bis 14.8 | Datenbankskripte in zwei Phasen (14.5.4) |
| Direktes Upgrade nur zwischen aufeinanderfolgenden stabilen Releases; sonst schrittweise | 14.5.5 | eine Kette, in einer Sitzung angewendet, Datei für Datei |
| 4.0: PHP 5, `date.timezone`, Autoload-Arrays, Klassenkonstanten statt globaler Konstanten ([wichtige Hinweise](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Upgrading-to-4.0/Important-notes.html)) | 14.9 | PHP 8.0 bis 8.5; Arrays in `autoload/` und `var/autoload/` |
| 4.0: Zeichensatzkonvertierung mit `ezconvertdbcharset.php`, InnoDB mit `ezconvertmysqltabletype.php` ([3.10 auf 4.0](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Upgrading-to-4.0/from-3.10.x-to-4.0.y.html)) | 14.5.2 | dieselben Skripte; MySQL `utf8`, Standardwerte von MySQL 8 beachten |
| 4.0: überschreibende Templates in `override/templates/` | 14.8 | unverändert; `Priority` ordnet jetzt die Override-Blöcke |
| 4.1: `updateimagesystem.php` **vor** dem SQL; ezdhtml durch ezoe ersetzt; `$Result['path']` muss ein Array sein ([4.0 auf 4.1](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Upgrading-to-4.1/4.0.x-to-4.1.y.html)) | 14.5.4, 14.7, 14.9 | nur, wenn `ezimage` Zeilen hat; ezoe mit TinyMCE 3 oder 8 |
| 4.1: `addlockstategroup.php` (einmal), Korrekturen der Remote-IDs, `fixezurlobjectlinks.php`, `initurlaliasmlid.php`, `correctxmlalign.php` | 14.5.6 | nach dem Schema (Phase B) |
| 4.1, 4.2: eZ Flow und eZ Webin mit `ezflowupgrade.php` / `ezwebinupgrade.php` aktualisiert | 14.8 | Code über Composer; Skripte für die Website-Pakete |
| 4.2: PHP 5.2 (nicht 5.2.9) oder 5.3; `correctxmlalign.php`, `fixorphanimages.php` ([4.1 auf 4.2](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Upgrading-to-4.2/Upgrading-from-4.1.x-to-4.2.y.html)) | 14.5.6 | |
| 4.3: `updatenodeassignment.php`; optionales admin2-Design mit ezjscore und dem Werkzeug `admin_preferences` ([4.2 auf 4.3](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Upgrading-to-4.3/Upgrading-from-4.2.x-to-4.3.x.html)) | 14.5.6, 14.7 | admin2 ist das heutige `admin`; neuere Administrationsdesigns |
| 4.4: `updatesectionidentifier.php` ([4.3 auf 4.4](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Upgrading-to-4.4/Upgrading-from-4.3.x-to-4.4.x.html)) | 14.5.6 | Kopien von 4.4 und 4.5 identisch |
| 4.5: `DatabaseImplementation=ezmysqli`, mysqli-Cluster-Backends, `STORAGE_BACKEND` in `index_cluster.php`, neue Rewrite-Regel ([4.4 auf 4.5](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Upgrading-to-4.5/Upgrading-from-4.4-to-4.5.html)) | 14.6, 14.7 | `ezmysql` als Alias für mysqli; `CLUSTER_*`-Konstanten; Regeln aus Kapitel 8 |
| 4.6: `removetrashedimages.php`, `updateordernumber.php`, Overrides der Benutzer-Tabs entfernen, die Regel für `index_ajax.php` entfernen ([4.5 auf 4.6](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Upgrading-to-4.6/Upgrading-from-4.5-to-4.6.html)) | 14.5.6, 14.7 | `index_ajax.php` gibt es nicht mehr |
| 4.7: Einstellungen von `index_cluster.php` in `config.php` / `config.cluster.php`; Cluster-Spalte `datatype`; Erkennung mobiler Geräte; eZ-Flow-Override `add_to_block_frontpage` ([4.6 auf 4.7](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Upgrading-to-4.7/Upgrading-from-4.6-to-4.7.html)) | 14.5.5, 14.6, 14.7 | nur DFS; die Hälfte für `ezdbfile` gilt dem entfernten Handler |
| Direkt von 4.1 auf 4.7: SQL-Dateien der Reihe nach, SQL je Patch-Release, Index für `ezm_pool`, das Sektionsskript von 4.4 überspringen, ezjscore und admin2 aktivieren ([direktes Upgrade](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Direct-upgrading/Direct-upgrading-to-4.7-from-4.1-4.2-4.3-4.4-and-4.5/Direct-upgrading-from-4.1-to-4.7.html)) | 14.5.5, 14.8 | `START: from`-Markierungen; der Index für `ezm_pool` ist im aktuellen ezflow-Schema enthalten |
| Mindestversion der eZ Components je Release | 14.9 | Zeta Components über Composer |
| `chmod -R a+rwx` auf design, extension, settings, var | 14.6 | nicht tun; siehe Kapitel 8 und 13 |
| `ezcache.php --clear-all --purge` und eine manuelle Kontrolle der Cache-Verzeichnisse | 14.6 | derselbe Befehl |

## 14.17 Checkliste für die Migration

Vorher:

- [ ] Alte Version, Extensions, Datentypen, Hash-Typen, Zeichensatz, Tabellentypen, Cluster-Modus und Suchmaschine festgehalten (14.3)
- [ ] Konsistenzprüfungen der alten Website ausgeführt; Datenbank-Cluster verlassen; optionale Aufräumarbeiten erledigt (14.4)
- [ ] Vollständige Sicherung der alten Datenbank und des alten Verzeichnisses, einmal auf einer Testmaschine zurückgespielt
- [ ] Staging-Server mit PHP 8 und dem Code von Exponential 6.0; kein Installer gegen die alte Datenbank ausgeführt (14.2)
- [ ] URL-Liste, Trefferzahlen der Suche und Anmeldezahlen zum Vergleich festgehalten

Datenbank (auf Staging, dann im Ernstfall):

- [ ] Tabellen in InnoDB, Datenbank in UTF-8, Standardzeichensatz der Datenbank gesetzt (14.5.2)
- [ ] Vorabprüfungsabfragen liefern keine Zeilen, oder ihre Korrektur ist eingeplant (14.5.3)
- [ ] `ezimage` geprüft; `updateimagesystem.php` bei Bedarf vor der 4.1-Datei ausgeführt (14.5.4)
- [ ] Phase A: jede SQL-Datei von Ihrer Version bis 6.0.15, der Reihe nach, Blöcke der Patch-Releases berücksichtigt, Cluster-Dateien auf der DFS-Datenbank (14.5.5)
- [ ] Phase B: Reparaturskripte jeder durchlaufenen Version, zuerst Probeläufe (14.5.6)
- [ ] `createaudittables.php` ausgeführt; die Versionszeile lautet `6.0.15stable`

Dateien und Einstellungen:

- [ ] Speicher in `var/` kopiert, `VarDir` unverändert, Caches nicht kopiert (14.6)
- [ ] Cluster: DFS mit dem mysqli-Backend, `CLUSTER_*`-Konstanten (14.6)
- [ ] `settings/override/` und `settings/siteaccess/` übernommen und anhand von 14.7 geprüft
- [ ] Rewrite-Regeln aus Kapitel 8 übernommen

Code:

- [ ] Designs in eine Design-Extension verlagert; nichts innerhalb paketierter Extensions bearbeitet (14.8)
- [ ] Templates mit `eztemplatecheck.php` geprüft; YUI- und alte jQuery-Aufrufe ersetzt
- [ ] Syntax der eigenen Extensions geprüft, Autoloads neu erzeugt, `exp:checkclasses` ohne Befund, PHP-8-Brüche und Deprecations behoben (14.9)

Danach:

- [ ] Benutzer: `ezuser_setting`-Zeilen, `[UserSettings] SiteName`, `mysql`-Hashes behandelt (14.10)
- [ ] Suchindex neu aufgebaut (14.11)
- [ ] Schema mit `share/db_schema.dba` verglichen; Zahlen verglichen; Aliase geprüft; Upgrade-Prüfung ausgeführt (14.12)
- [ ] Bearbeiten, Veröffentlichen, Anmeldung, Cronjobs und Logs geprüft
- [ ] Caches nach dem Neuladen von PHP geleert; alte Website und Dumps für den Rollback aufbewahrt (14.14)

## Referenzen

In diesem Repository:

- [11. Upgrade](11-aktualisieren.md): die Update-Kette, die dieses Kapitel durchläuft, die Schritte für 6.0 und die
  Prüfbefehle; [12. Fehlerbehebung](12-fehlerbehebung.md); [13. Sicherheitshärtung](13-sicherheitshaertung.md);
  [2. Voraussetzungen](02-voraussetzungen.md); [8. Die Site ausliefern](08-die-website-ausliefern.md); [9. Datenbanken](09-datenbanken.md).
- [Anleitung zum Upgrade](../anleitungen/aktualisieren.md).
- Release Notes der 4.x-Linie: [4.1](../../eng-us/bc/4.1/changes-4.1.0.txt), [4.2](../../eng-us/bc/4.2/changes-4.2.0.txt),
  [4.3](../../eng-us/bc/4.3/changes-4.3.0.txt), [4.4](../../eng-us/bc/4.4/changes-4.4.0.txt), [4.5](../../eng-us/bc/4.5/changes-4.5.0.txt),
  [4.6](../../eng-us/bc/4.6/changes-4.6.0.txt), [4.7](../../eng-us/bc/4.7/changes-4.7.0.txt).
- Changelogs der alten Linie: [3.9 auf 3.10](../../changelogs/3.10/CHANGELOG-3.9.0-to-3.10.0),
  [3.10 auf 4.0](../../changelogs/4.0/CHANGELOG-3.10.0-to-4.0.0), [4.0 auf 4.1](../../changelogs/4.1/CHANGELOG-4.0.0-to-4.1.0),
  [4.1 auf 4.2](../../changelogs/4.2/CHANGELOG-4.1.0-to-4.2.0), [4.2 auf 4.3](../../changelogs/4.3/CHANGELOG-4.2.0-to-4.3.0),
  [4.3 auf 4.4](../../changelogs/4.4/CHANGELOG-4.3.0-to-4.4.0), [4.5 auf 4.6](../../changelogs/4.6/CHANGELOG-4.5.0-to-4.6.0.txt),
  [4.6 auf 4.7](../../changelogs/4.7/CHANGELOG-4.6.0-to-4.7.0.txt).
- 5.90-Linie: [Überblick](../bc/5.90/README.md), [PHP 7](../bc/5.90/php7.md),
  [Passwortlänge](../bc/5.90/passwortlaenge.md), [Indizierung von Relationen](../bc/5.90/indizierung-von-beziehungen.md).
- PHP 8: [Unterstützung für PHP 8](../bc/6.0/php8.md), [Unterstützung für PHP 8.0](../bc/6.0/php-8.0-unterstuetzung.md).
- JavaScript und Editor: [YUI entfernt](../bc/6.0/yui-entfernung.md),
  [jQuery 4 und die Entfernung von YUI](../funktionen/6.0/jquery4-und-entfernung-von-yui.md),
  [TinyMCE 3 und TinyMCE 8 in ezoe](../bc/6.0/ezoe-tinymce8.md).
- Extensions: [Ladereihenfolge der Extensions](../funktionen/6.0/ladereihenfolge-der-extensions.md),
  [zusätzliche Extension-Verzeichnisse](../bc/6.0/zusaetzliche-erweiterungsverzeichnisse.md),
  [Verhaltensänderungen der Legacy-Extensions](../bc/6.0/verhaltensaenderungen-von-erweiterungen.md).
- [Das Produkt heißt Exponential](../funktionen/6.0/umbenennung-in-exponential.md),
  [Wartungsmodus](../funktionen/6.0/wartungsmodus.md),
  [Dateikonsistenzprüfung](../funktionen/6.0/dateikonsistenzpruefung.md), [Audit](../bc/6.0/audit.md),
  [Legacy Bridge](../funktionen/6.0/legacy-bridge.md),
  [Sicherheitshärtung August 2026](../spezifikationen/6.0/sicherheitshaertung-2026-08.md).
- Code: `update/database/`, `update/common/scripts/`, `bin/php/ezconvertdbcharset.php`,
  `bin/php/ezconvertmysqltabletype.php`, `bin/php/ezsqldiff.php`, `bin/php/updateniceurls.php`,
  `bin/php/verify_aliases.php`, `bin/php/ezpgenerateautoloads.php`, `bin/php/eztemplatecheck.php`,
  `bin/php/resetuserpassword.php`, `kernel/classes/datatypes/ezuser/ezuser.php`, `kernel/classes/ezdatatype.php`,
  `kernel/classes/ezpersistentobject.php`, `lib/ezdb/classes/ezmysqlcharset.php`, `index_cluster.php`,
  `config.php-RECOMMENDED`, `share/db_schema.dba`.

Die Upgrade-Dokumentation des Herstellers für die 4.x-Linie (archivierte Kopie der früheren Seiten von doc.ez.no):

- [How to proceed](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/How-to-proceed.html),
  [Backup and consistency checks](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Backup-and-consistency-checks.html),
  [Direct upgrading](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Direct-upgrading.html),
  [The system upgrade scripts](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/The-system-upgrade-scripts.html).
- [4.0: wichtige Hinweise](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Upgrading-to-4.0/Important-notes.html),
  [3.10 auf 4.0](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Upgrading-to-4.0/from-3.10.x-to-4.0.y.html),
  [4.0 auf 4.1](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Upgrading-to-4.1/4.0.x-to-4.1.y.html),
  [4.1 auf 4.2](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Upgrading-to-4.2/Upgrading-from-4.1.x-to-4.2.y.html),
  [4.2 auf 4.3](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Upgrading-to-4.3/Upgrading-from-4.2.x-to-4.3.x.html),
  [4.3 auf 4.4](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Upgrading-to-4.4/Upgrading-from-4.3.x-to-4.4.x.html),
  [4.4 auf 4.5](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Upgrading-to-4.5/Upgrading-from-4.4-to-4.5.html),
  [4.5 auf 4.6](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Upgrading-to-4.6/Upgrading-from-4.5-to-4.6.html),
  [4.6 auf 4.7](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Upgrading-to-4.7/Upgrading-from-4.6-to-4.7.html),
  [direkt von 4.1 auf 4.7](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Direct-upgrading/Direct-upgrading-to-4.7-from-4.1-4.2-4.3-4.4-and-4.5/Direct-upgrading-from-4.1-to-4.7.html).

Die Migration des Herstellers auf seine neuere Plattform:

- [Migrating from eZ Publish](https://doc.ibexa.co/en/5.0/update_and_migration/migrate_to_ibexa_dxp/migrating_from_ez_publish/)
- [Common migration issues](https://doc.ibexa.co/en/5.0/update_and_migration/migrate_to_ibexa_dxp/common_issues/)

Migrationsleitfäden für PHP:
[7.0](https://www.php.net/manual/en/migration70.php), [7.1](https://www.php.net/manual/en/migration71.php),
[7.2](https://www.php.net/manual/en/migration72.php), [7.3](https://www.php.net/manual/en/migration73.php),
[7.4](https://www.php.net/manual/en/migration74.php), [8.0](https://www.php.net/manual/en/migration80.php),
[8.1](https://www.php.net/manual/en/migration81.php), [8.2](https://www.php.net/manual/en/migration82.php),
[8.3](https://www.php.net/manual/en/migration83.php), [8.4](https://www.php.net/manual/en/migration84.php),
[8.5](https://www.php.net/manual/en/migration85.php).

Code und Pakete:

- Exponential auf GitHub: [github.com/se7enxweb/exponential](https://github.com/se7enxweb/exponential)
  ([Releases](https://github.com/se7enxweb/exponential/releases)).
- Der ursprüngliche Legacy-Kernel, als historische Quelle der Update-Dateien:
  [github.com/ezsystems/ezpublish-legacy](https://github.com/ezsystems/ezpublish-legacy).
- Packagist: [se7enxweb/exponential](https://packagist.org/packages/se7enxweb/exponential).
- MySQL: [Zeichensätze](https://dev.mysql.com/doc/refman/8.4/en/charset.html); PostgreSQL:
  [psql](https://www.postgresql.org/docs/current/app-psql.html), [pgcrypto](https://www.postgresql.org/docs/current/pgcrypto.html).

[Inhalt](README.md) · Zurück: [13. Sicherheitshärtung für den Produktivbetrieb](13-sicherheitshaertung.md) · Weiter: [15. Migration vom 5.x-Legacy-Stack](15-migration-von-5x-legacy.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
