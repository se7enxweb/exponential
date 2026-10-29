# Upgrade mit der Konsole: exp:upgrade

Diese Anleitung erklärt `./console exp:upgrade`: den einen Befehl, der die Datenbank und die Einstellungen einer
Installation von Exponential 6.0 auf den Stand des Codes bringt, den sie ausführt. Er zeigt zuerst, was er ändern
würde, macht eine Sicherung, führt die Schritte der Reihe nach aus, zeichnet auf, was er getan hat, und prüft, ob die
Site danach noch antwortet. Er ersetzt die Liste aus SQL-Dateien, Update-Skripten und Setup-Befehlen im Abschnitt
„Upgrading“ eines Releases; diese Liste bleibt im Changelog als Referenz, und jeder Punkt nennt den Schritt, der ihn
erledigt.

Sie richtet sich an Administratoren, die eine Installation aktualisieren, und an alle, die Upgrades aus einer
Deployment-Pipeline ausführen. Alles Folgende wurde am 29. Oktober 2026 auf SQLite-, MariaDB- und
PostgreSQL-Datenbanken ausgeführt.

[Anleitungen](README.md) · Siehe auch: [Upgrade](aktualisieren.md), [Die Upgrade-Prüfung](upgrade-pruefung.md),
[Upgrade-Schritte für Erweiterungen](upgrade-schritte-fuer-erweiterungen.md), [Bereitstellung](bereitstellung.md),
[Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)

## Kurz

```bash
./console exp:upgrade:status            # was erledigt ist, was offen ist; nichts wird geändert
./console exp:upgrade                   # Probelauf: was jeder offene Schritt ändern würde
./console exp:upgrade --apply           # Sicherung, dann jeder offene Schritt, dann Deploy und Prüfung
```

Führen Sie die Befehle im Wurzelverzeichnis der Installation als der Benutzer aus, unter dem der Webserver läuft
(als root mit `--allow-root-user`). Ohne `--apply` wird nie etwas geschrieben.

## Die Stufen

Ein Upgrade besteht aus fünf Stufen, die immer in dieser Reihenfolge laufen. Jede ist ein eigener Befehl, und
`exp:upgrade` führt alle in einem Aufruf aus.

| # | Stufe | Befehl | Was sie tut |
|---|---|---|---|
| 1 | code | `exp:upgrade:status --stages=code` | die Git-Checkouts der Installation (in 6.0.15 noch ohne Schritte: den Code aktualisieren, wie es [Upgrade](aktualisieren.md), Teil C, beschreibt) |
| 2 | libraries | `exp:upgrade:status --stages=libraries` | Composer (in 6.0.15 noch ohne Schritte: `composer install` wie in Teil C) |
| 3 | database | `exp:upgrade:database` | die Tabellen, Spalten, Indizes und Zeilen, die das Release braucht, auf jeder Datenbank-Engine |
| 4 | scripts | `exp:upgrade:scripts` | die PHP-Upgrade-Schritte: die Update-Skripte und die Setup-Befehle, über ihre Klassen |
| 5 | finish | `exp:upgrade:finish` | `exp:velocity deploy --kernel` (Autoloads, Caches, PHP-FPM, Velocity), danach ein Abruf der Startseite |

Eine spätere Stufe schreibt nicht, solange eine frühere offene Schritte hat (`--force-order` führt sie trotzdem
aus): Richtlinien werden nicht in eine Datenbank geschrieben, der die Tabellen dafür fehlen. Die Abschluss-Stufe hat
nur dann Arbeit, wenn der Lauf etwas geändert hat oder wenn Sie `exp:upgrade:finish` allein ausführen.

## Jeder Schritt und sein Zustand

Jede Stufe besteht aus Schritten. `./console exp:upgrade:steps` listet sie in der Reihenfolge auf, in der sie laufen;
jeder Schritt hat eine ID, die sich nie ändert, etwa `6.0.15-user-audit-table`.

Ein Schritt sieht immer in der Installation selbst nach, nie nur in einer Aufzeichnung: Eine Tabelle, die er anlegt,
wird im Katalog der Datenbank gesucht, eine Richtlinie in der Rolle. Was Sie von Hand mit der SQL-Update-Datei oder
einem alten Skript erledigt haben, zählt.

| Zustand | Bedeutet |
|---|---|
| `done` | vorhanden, und `exp:upgrade` hat es ausgeführt (die Aufzeichnung sagt wann und wie lange) |
| `not-needed` | vorhanden ohne `exp:upgrade` (eine neue Installation oder von Hand erledigt), oder nicht für diese Installation (eine andere Engine, eine nicht installierte Funktion) |
| `pending` | fehlt: der Schritt hat Arbeit |
| `failed` | fehlt, und der letzte Versuch ist gescheitert; die Meldung steht in der Aufzeichnung |
| `unknown` | lässt sich nicht feststellen; der Grund wird angezeigt, ein Lauf überspringt den Schritt und macht weiter |

```text
$ ./console exp:upgrade:status
Exponential upgrade: status
Code 6.0.15stable, database sqlite, version 6.0.15stable

Stage database     pending (1 pending)
  pending     6.0.15-upgrade-record-table          missing: table expupgrade_step
  not-needed  6.0.15-user-password-hash-width      not for sqlite
  not-needed  6.0.15-trash-columns                 in place: ezcontentobject_trash.trashed, ...
  ...
Stage scripts      not-needed
  not-needed  6.0.15-editor-policies               in place
  ...
Summary: 1 pending, 0 done, 28 not needed, 0 failed, 0 unknown
PASS
```

So sieht eine Installation aus, die von Hand aktualisiert wurde: alles ist `not-needed` außer der
Aufzeichnungstabelle, die mit `exp:upgrade` selbst neu ist.

## Der Probelauf

`./console exp:upgrade` (oder der Befehl einer Stufe) ohne `--apply` listet für jeden offenen Schritt auf, was er
ändern würde: das SQL, das er auf dieser Engine ausführen würde, die Richtlinien, die er hinzufügen würde, die
Zeilen, die er anlegen würde. Er schreibt nichts und nimmt keine Sperre. `--dry-run` wird als dasselbe angenommen;
`--apply` zusammen mit `--dry-run` ist ein Bedienfehler (Exit-Code 2).

```text
Stage database     pending (1 pending)
  pending     6.0.15-upgrade-record-table          missing: table expupgrade_step
              would create table expupgrade_step: CREATE TABLE expupgrade_step ( ... PRIMARY KEY ( step_id ) );
```

## Ausführen

```bash
./console exp:upgrade --apply
```

1. Die Sperre `var/<site>/upgrade.lock` wird genommen: Ein zweiter Lauf zur selben Zeit endet mit Exit-Code 1.
2. Vor dem ersten Schreiben wird die Sicherung gemacht (siehe unten); scheitert sie, wird nichts geändert.
3. Die offenen Schritte laufen der Reihe nach. Nach jedem sieht der Schritt erneut in der Installation nach; nur wenn
   seine Wirkung jetzt da ist, wird er als `done` aufgezeichnet.
4. Der erste Schritt, der scheitert, beendet den Lauf (Exit-Code 1). Die Schritte danach werden als nicht ausgeführt
   gelistet. Beheben Sie die Ursache und starten Sie den Befehl erneut: Was erledigt ist, wird nicht zweimal
   erledigt, und ein halb ausgeführter Schritt wird vervollständigt.
5. Die Abschluss-Stufe stellt die Änderungen bereit und ruft die Startseite von `[SiteSettings] SiteURL` (oder jede
   `--url`) ab.

Was läuft, wählen Sie mit `--stages=database,scripts`, `--only=<Schritt-ID>` und `--skip=<Schritt-ID>` (als
Komma-Liste oder wiederholt). `--no-deploy` schließt nur mit den Autoloads der Erweiterungen und den INI-, Template-
und Inhalts-Caches ab, wenn Sie später selbst bereitstellen.

## Die Sicherung

`./console exp:upgrade:backup` macht dieselbe Sicherung für sich allein. Sie landet in `var/backup/upgrade/<Zeit>/`
(`--backup-dir=` für einen anderen Ort):

| Engine | Sicherung |
|---|---|
| SQLite | `database.sqlite`, eine konsistente Kopie, mit `VACUUM INTO` gemacht, während die Site läuft |
| MySQL, MariaDB | `database.sql` aus `mysqldump --single-transaction` |
| PostgreSQL | `database.sql` aus `pg_dump` |
| MongoDB | `mongodb/` aus `mongodump` |
| Oracle | keine: machen Sie eine mit `expdp` und starten Sie mit `--no-backup` |

`settings/override` wird daneben kopiert, und `manifest.json` sagt, was vorhanden ist. Das Passwort der Verbindung
erreicht das Dump-Werkzeug in einer Datei, die nur ihr Eigentümer lesen kann (MySQL, MongoDB), oder in seiner
Umgebung (PostgreSQL), nie auf einer Befehlszeile. Die Dateien kann nur ihr Eigentümer lesen. Die Sicherung ist der
Weg zurück: Kein Schritt lässt sich für sich allein rückgängig machen.

## Engines

Die Datenbank-Schritte werden aus dem Kernel-Schema (`share/db_schema.dba`) geschrieben, denselben Definitionen, aus
denen eine neue Installation angelegt wird, von der Schema-Klasse der jeweiligen Engine: MySQL und MariaDB,
PostgreSQL, SQLite und Oracle über die Erweiterung ezoracle. MongoDB erhält die Collections; Spalten und Indizes
gibt es dort nicht. Nach der Datenbank-Stufe entspricht das Schema dem einer neuen Installation (geprüft auf SQLite,
MariaDB und PostgreSQL, mit einem gewollten Extra auf MySQL: dem Volltextindex des Audit-Index).

Zwei Schritte gelten nicht für jede Engine: die Breite von `ezuser.password_hash` (SQLite speichert keine Länge) und
die Tabelle des Benutzer-Auditprotokolls (nicht auf MongoDB, wo die Audit-Dateien jedes Ereignis behalten).

Der eindeutige Index auf `ezuser.login` wird nur angelegt, wenn kein Login zu zwei Konten gehört. Solange es
Duplikate gibt, ist der Schritt `unknown`, und der Lauf macht weiter; lösen Sie sie mit
`./console exp:user-login-check` auf und starten Sie erneut.

## Prüfen und die Aufzeichnung

`exp:upgrade` zeichnet jeden ausgeführten Schritt in der Tabelle `expupgrade_step` auf: den Schritt, seine Stufe und
Version, erledigt oder gescheitert, wann, wie lange, den Systembenutzer, der ihn ausgeführt hat, die Engine, die
Prüfsumme des Schritt-Codes und die letzte Meldung. Die Tabelle legt der erste Schritt an,
`6.0.15-upgrade-record-table`.

`./console exp:upgrade:verify` sieht für jeden Schritt erneut nach und meldet Abweichungen: einen Schritt, den die
Aufzeichnung als erledigt führt, dessen Wirkung aber jetzt fehlt (eine Tabelle gelöscht, eine Richtlinie von Hand
entfernt). Findet er welche, endet er mit Exit-Code 1. Führen Sie ihn nach einer Wiederherstellung oder aus der
Überwachung aus.

Jeder ausgeführte Schritt wird außerdem in `var/log/upgrade.log` geschrieben.

## In einer Pipeline

```bash
./console exp:upgrade:status --fail-on-pending        # Exit-Code 1, wenn etwas offen ist
./console exp:upgrade --apply --format=json > upgrade.json
```

`--format=json` schreibt die Maschinenausgabe `exp-upgrade/1`: die Installation (Engine, Datenbankversion,
Code-Version), die Sicherung, jede Stufe mit jedem Schritt (`id`, `stage`, `version`, `state`, `detail`, `action`,
`plan`, `messages`, `duration_ms`, `record`, `drift`), eine Zusammenfassung und den Exit-Code.

| Exit-Code | Bedeutet |
|---|---|
| 0 | nichts offen, oder alles ausgeführt |
| 1 | ein Schritt ist gescheitert, die Sperre oder die Sicherung wurde verweigert, eine Stufe kam außer der Reihe, oder mit `--fail-on-pending` sind Schritte offen; `exp:upgrade:verify`: Abweichungen |
| 2 | Bedienfehler (ein unbekannter Optionswert, eine unbekannte Stufe oder ein unbekannter Schritt; `--apply` mit `--dry-run`) |
| 3 | die Datenbank ist nicht erreichbar |
| 4 | interner Fehler |

## Die Schritte von 6.0.15

| Schritt | Stufe | Ersetzt |
|---|---|---|
| `6.0.15-upgrade-record-table` | database | (neu) die Aufzeichnungstabelle `expupgrade_step` |
| `6.0.15-user-password-hash-width` | database | `ezuser.password_hash` in der SQL-Update-Datei auf 255 verbreitert |
| `6.0.15-trash-columns` | database | `ezcontentobject_trash.trashed`, `trashed_by`, `trashed_via` und ihr Index |
| `6.0.15-pdf-export-footer` | database | `ezpdf_export.show_footer`, `footer_text` |
| `6.0.15-rss-opml-podcast` | database | `ezrss_export.opml_head`, `podcast_head`, Tabelle `ezrss_export_opml_item` |
| `6.0.15-order-statuses` | database | die Bestellstatus 4 bis 17 |
| `6.0.15-audit-index-tables` | database | `update/common/scripts/6.0/createaudittables.php` (Tabellen) |
| `6.0.15-bookmark-folders` | database | Lesezeichenordner: Spalten, Index, Tabelle `expbookmark_folder` |
| `6.0.15-bookmark-view-table` | database | `createbookmarkviewtable.php` |
| `6.0.15-mail-preference-tables` | database | die fünf Tabellen `expmail_*` |
| `6.0.15-api-key-table` | database | `createapikeytable.php` |
| `6.0.15-transfer-run-table` | database | `createtransferruntable.php` |
| `6.0.15-data-table-tables` | database | `exp_data_connection`, `exp_secret`, `exp_data_sync*` |
| `6.0.15-user-session-table` | database | `createusersessiontable.php` |
| `6.0.15-oauth-pkce-columns` | database | `ezprest_authcode.code_challenge`, `code_challenge_method` |
| `6.0.15-passkey-table` | database | `createpasskeytable.php` |
| `6.0.15-help-content-tables` | database | `exphelp_help_id`, `exphelp_feedback` |
| `6.0.15-order-siteaccess-column` | database | `ezorder.siteaccess` |
| `6.0.15-user-audit-table` | database | `createuseraudittable.php` |
| `6.0.15-user-login-unique-index` | database | `./console exp:user-login-check --fix-index` |
| `6.0.15-site-version` | database | `ezsite_data` `ezpublish-version` auf 6.0.15 gesetzt |
| `6.0.15-missing-user-settings` | scripts | `createmissingusersettings.php` |
| `6.0.15-trash-records-to-columns` | scripts | `movetrashrecords.php` (die Datei bleibt erhalten) |
| `6.0.15-anonymous-public-policies` | scripts | `bin/php/addanonymouspublicpolicies.php` |
| `6.0.15-editor-policies` | scripts | `bin/php/addeditorpolicies.php --apply` |
| `6.0.15-member-management` | scripts | `bin/php/addmembermanagement.php --apply` |
| `6.0.15-user-approval` | scripts | `bin/php/adduserapproval.php` |
| `6.0.15-help-read-policy` | scripts | `bin/php/addexphelpreadpolicy.php` |
| `6.0.15-oauth-authorize-policy` | scripts | `bin/php/addoauthauthorizepolicy.php` |
| `6.0.15-order-siteaccess-backfill` | scripts | `bin/php/expbackfillordersiteaccess.php --apply` |
| `6.0.15-finish-deploy` | finish | `./console exp:velocity deploy --kernel` |
| `6.0.15-finish-smoke` | finish | ein Abruf der Startseite |

Die Setup-Befehle laufen mit ihren Standardoptionen. Brauchen Sie eine andere Option (etwa
`addeditorpolicies --keep-content-wildcard` oder `addanonymouspublicpolicies --skip=user/register`, damit die
Registrierung geschlossen bleibt), überspringen Sie den Schritt (`--skip=6.0.15-editor-policies`) und führen Sie den
Befehl selbst aus; der Schritt meldet dann, was noch fehlt.

Keine Schritte, weil sie nicht die Datenbank oder die Einstellungen betreffen: die Crontab
(`bin/php/installcrontab.php`), die Befehle für Hilfe als Inhalt (`exp:help:content install`, `build-manuals`,
`ebooks`) und eine Prüfung der Umgebungsvariablen für den Demo-Modus. Das Changelog listet sie auf.

## Erweiterungen

Eine Erweiterung fügt eigene Schritte mit `ExtensionStepList[]` in ihrer `settings/upgrade.ini.append.php` hinzu;
sie laufen mit denen des Kernels, nach Stufe und Version. [Upgrade-Schritte für
Erweiterungen](upgrade-schritte-fuer-erweiterungen.md) zeigt, wie.

## Das Versprechen

Die Befehlsnamen (`exp:upgrade` und `exp:upgrade:*`), ihre Optionen, die Exit-Codes, die Zustände der Schritte und
die Ausgabe `exp-upgrade/1` bleiben dauerhaft: Optionen und Schlüssel der Ausgabe können hinzukommen, nichts wird
umbenannt oder entfernt. Ein Test im Kernel vergleicht sie mit einem Schnappschuss.
