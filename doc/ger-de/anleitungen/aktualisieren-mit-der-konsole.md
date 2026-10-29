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
| 1 | code | `exp:upgrade:code` | der Git-Checkout der Installation (und jeder `--repos`-Checkout) auf `--to` oder seinen Upstream gebracht, nur Fast-Forward, über git_manager |
| 2 | libraries | `exp:upgrade:libraries` | `vendor/` enthält, was `composer.lock` nennt (`composer install`, oder vorher `--update` benannter Pakete), mit Sicherheitsprüfung |
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
2. Vor dem ersten Schreiben wird ein Sicherungspunkt angelegt (siehe unten), der Punkt zum Zurückrollen; scheitert
   er, wird nichts geändert.
3. Die offenen Schritte laufen der Reihe nach. Nach jedem sieht der Schritt erneut in der Installation nach; nur wenn
   seine Wirkung jetzt da ist, wird er als `done` aufgezeichnet.
4. Der erste Schritt, der scheitert, beendet den Lauf (Exit-Code 1). Die Schritte danach werden als nicht ausgeführt
   gelistet. Beheben Sie die Ursache und starten Sie den Befehl erneut: Was erledigt ist, wird nicht zweimal
   erledigt, und ein halb ausgeführter Schritt wird vervollständigt.
5. Die Abschluss-Stufe stellt die Änderungen bereit und ruft die Startseite von `[SiteSettings] SiteURL` (oder jede
   `--url`) ab.
6. Die Installation wird geprüft (siehe unten). Scheitert der Lauf oder die Prüfung, endet der Befehl mit Exit-Code 1
   und listet die Sicherungspunkte, die neuesten zuerst, jeden mit dem Befehl, der darauf zurückrollt; mit
   `--rollback-on-fail` rollt er sofort auf den Sicherungspunkt aus Schritt 2 zurück. Besteht sie, legt
   `--post-caption` einen Sicherungspunkt des geprüften Stands an, den „letzten guten“ für das nächste Upgrade.

Was läuft, wählen Sie mit `--stages=database,scripts`, `--only=<Schritt-ID>` und `--skip=<Schritt-ID>` (als
Komma-Liste oder wiederholt). `--no-deploy` schließt nur mit den Autoloads der Erweiterungen und den INI-, Template-
und Inhalts-Caches ab, wenn Sie später selbst bereitstellen.

Hat die Code-Stufe den Code bewegt, laufen die Stufen danach in einem neuen PHP-Prozess mit denselben Optionen: Der
Prozess, der begonnen hat, hat die alten Klassen und die alte Liste der Schritte geladen, der neue liest den neuen
Code. Der Bericht beider ist ein Bericht, und er sagt es.

## Die Code-Stufe

```bash
./console exp:upgrade:code --to=v6.0.16                 # Probelauf: holt (fetch), listet die kommenden Commits
./console exp:upgrade --apply --to=v6.0.16              # das ganze Upgrade auf dieses Release
./console exp:upgrade:code --apply --repos=extension/myext
```

Der Git-Checkout der Installation wird auf `--to` (einen Tag, einen Branch oder einen Commit) gebracht oder, ohne die
Option, auf den Upstream seines Branches, über git_manager (`extension/git_manager`), nur per Fast-Forward. Die
Submodule, die die Installation aufzeichnet, folgen. `--repos` nennt weitere Checkouts in der Installation, jeder
geht auf seinen eigenen Upstream oder `--to`. Ein Checkout wird verweigert, wenn:

- er lokale Änderungen in versionierten Dateien hat;
- er Commits hat, die dem Ziel fehlen (kein Fast-Forward);
- der Prozess nicht sein Eigentümer ist (führen Sie das Upgrade als der Benutzer aus, dem die Dateien gehören,
  `sudo -u <Eigentümer>`).

`exp:upgrade:status` liest die Remote-Tracking-Refs, wie der letzte Fetch sie hinterlassen hat. Probelauf und Lauf
holen zuerst, nur die Refs, nie das Arbeitsverzeichnis, und nur als Eigentümer. Der Sicherungspunkt vor dem ersten
Schreiben enthält die Commits, auf denen jeder Checkout stand (`code.json`). Ohne git_manager, oder wenn die
Installation kein Git-Checkout ist, gilt die Stufe nicht: Aktualisieren
Sie den Code, wie es [Upgrade](aktualisieren.md), Teil C, beschreibt.

## Die Bibliotheken-Stufe

```bash
./console exp:upgrade:libraries                         # Probelauf: die abweichenden Pakete, die Prüfung
./console exp:upgrade:libraries --apply
./console exp:upgrade:libraries --apply --update=guzzlehttp/guzzle
```

`6.0.15-libraries-install` vergleicht `composer.lock` mit dem, was laut `vendor/composer/installed.json` installiert
ist, und führt `composer install` aus, mit den Dev-Paketen nur, wenn `vendor/` sie hatte. `6.0.15-libraries-update`
führt vorher `composer update <Pakete> --with-dependencies` aus, nur für die mit `--update` genannten Pakete;
committen Sie danach die neue `composer.lock`. `composer audit` läuft vor und nach der Installation und meldet
Sicherheitshinweise und aufgegebene Pakete; es hält den Lauf nie an (`--no-audit` überspringt es). `--composer=` nennt
das Composer-Programm oder `composer.phar`.

Die Installation wird verweigert, und nichts wird installiert, wenn:

- Composer lokale Änderungen in `vendor/` meldet (`composer status`);
- ein Paket, das sie ändern würde, ein eigener Git-Checkout ist (eine Erweiterung, die per Git und nicht per
  Composer aktualisiert wird: bewegen Sie sie stattdessen mit der Code-Stufe und `--repos=`);
- der Prozess nicht der Eigentümer der Installation ist.

`--allow-vendor-changes` installiert über die ersten beiden hinweg.

## Sicherungspunkte

Ein Sicherungspunkt (Caption) ist ein sicherer Zeitpunkt: die vollständige Sicherung von git_manager
(`extension/git_manager`, der Datenbankabzug und der Speicher unter `var/`) im Sicherungsordner von git_manager, mit
dem, was `exp:upgrade` in seinem Ordner `upgrade/` hinzufügt:

| Im Sicherungspunkt | Was es ist |
|---|---|
| `sql_<Sicherungspunkt>.tar.gz`, `dump.json` | die Datenbank: MySQL und MariaDB `mysqldump`, PostgreSQL `pg_dump --clean`, SQLite eine konsistente Kopie (`VACUUM INTO`), MongoDB `mongodump`; Oracle hat keinen (Export mit `expdp`) |
| `var_<Sicherungspunkt>.tar.gz` | der Speicher unter `var/` (ohne die `VarExcludeDirs` von git_manager) |
| `upgrade/settings/override` | die Einstellungs-Overrides |
| `upgrade/libraries/` | `composer.json`, `composer.lock`, `vendor/composer/installed.json` |
| `upgrade/code.json` | der Commit des Checkouts der Installation und jedes `--repos`-Checkouts |
| `upgrade/caption.json` | die Bezeichnung, die Art (`pre`, `post`, `safety`, `manual`), die Versionen (Datenbank, Code, Erweiterungen), die Engine, die Tabellen |
| `upgrade/checksums.json` | die SHA-256-Prüfsumme jeder Datei des Sicherungspunkts |

`exp:upgrade --apply` legt einen Sicherungspunkt `pre` mit der Bezeichnung `upgrade <von>→<nach> <Zeit> pre` an,
bevor es irgendetwas schreibt, und zeichnet ihn im Bericht und in `expupgrade_step` auf. Ohne git_manager, oder mit
einem git_manager, der die Engine nicht sichern kann (ein älterer sichert nur MySQL), legt `exp:upgrade` den
Sicherungspunkt selbst in `var/backup/upgrade/<Zeit>/` an (die Datenbank, kein Speicher) und sagt warum.
`--no-caption` (dasselbe wie `--no-backup`) schreibt ohne einen; auf einer Installation, deren Einstellungen sie als
Produktion ausweisen (`site.ini [AdminEnvironment] Level=production`, oder `Level=auto` mit `EXP_ENV` gleich `prod`,
`production` oder `live`), wird das mit Exit-Code 2 verweigert, außer zusätzlich ist `--force-no-caption` angegeben.

```bash
./console exp:upgrade:captions                                  # jeder Sicherungspunkt, neueste zuerst, mit dem Befehl zum Zurückrollen
./console exp:upgrade:captions --verify=2026-10-29_10-15-00     # Prüfsummen, und der Abzug in eine Wegwerf-Datenbank eingespielt
./console exp:upgrade:captions --prune                          # was die Aufbewahrungs-Einstellungen freigeben
./console exp:upgrade:captions --prune --apply
```

Die Liste zeigt auch die eigenen Sicherungspunkte von git_manager. `--verify` prüft jede Prüfsumme und spielt den
Datenbankabzug in eine Wegwerf-Datenbank ein (SQLite: eine Kopie und `PRAGMA integrity_check`; MySQL, MariaDB und
PostgreSQL: eine Datenbank `<Name>_capverify_<pid>`, mit dem Konto der Installation angelegt, geladen, gezählt und
gelöscht; ein MySQL-Abzug mit `--databases` wird ohne sein `CREATE DATABASE` und `USE` geladen, damit er nie die
laufende Datenbank erreicht). `--prune` behält die neuesten `CaptionKeep` (10), jeden Sicherungspunkt, der jünger als
`CaptionKeepDays` (90) ist, und die Sicherungspunkte `pre` und `post` der letzten `CaptionKeepUpgrades` (3) Upgrades,
alles in `upgrade.ini [UpgradeSettings]`; es entfernt nur Sicherungspunkte von `exp:upgrade`, die eigenen von
git_manager folgen dessen `MaxBackups`.

Das Passwort der Verbindung erreicht ein Dump-Werkzeug in einer Datei, die nur ihr Eigentümer lesen kann (MySQL,
MongoDB), oder in seiner Umgebung (PostgreSQL), nie auf einer Befehlszeile, und jede Datei eines Sicherungspunkts kann
nur ihr Eigentümer lesen. `./console exp:upgrade:backup` macht weiterhin die Datenbanksicherung von `exp:upgrade` für
sich allein, ohne Sicherungspunkt.

## Die Prüfung nach dem Upgrade

```bash
./console exp:upgrade:verify --after
./console exp:upgrade:verify --after --url=https://www.example.com/,https://www.example.com:8080/ --no-tests
```

Am Ende von `exp:upgrade --apply` wird die Installation geprüft, in einem neuen Prozess, damit der neue Code geprüft
wird (`--no-verify` überspringt das):

| Prüfung | Besteht, wenn |
|---|---|
| steps | kein Schritt abweicht (als erledigt aufgezeichnet, Wirkung fehlt) und keiner gescheitert ist |
| urls | jede Rauchprobe-Adresse mit 200 bis 399 antwortet: `upgrade.ini [UpgradeSettings] SmokeURL[]`, sonst `velocity.ini [DeploySettings] WarmUpURL[]` (die Adressen von Apache und Velocity), sonst die Startseite und die Anmeldeseite von `SiteURL`; `--url` hat Vorrang |
| tests | die PHPUnit-Testsuite `upgrade-smoke` besteht (schnell, ohne Datenbank); übersprungen, wo PHPUnit nicht installiert ist, oder mit `--no-tests` |
| errorlog | keine neue Zeile eines fatalen Fehlers in `var/log/error.log` und dem Fehlerprotokoll der Site seit dem Start des Laufs |
| autoload | jede registrierte Schritt-Klasse lädt, und die Autoload-Arrays sind lesbar |

Eine Prüfung, die nicht laufen kann, sagt warum und lässt die Prüfung nicht scheitern.

## Zurückrollen

```bash
./console exp:upgrade:rollback                                          # die Sicherungspunkte
./console exp:upgrade:rollback 2026-10-29_10-15-00                      # was ein Zurückrollen darauf wiederherstellt
./console exp:upgrade:rollback 2026-10-29_10-15-00 --apply
./console exp:upgrade:rollback 2026-10-29_10-15-00 --apply --parts=database,settings
./console exp:upgrade:finish --apply                                    # live machen
```

Ein Zurückrollen ist nie automatisch (außer mit `--rollback-on-fail`) und läuft nie ohne `--apply`. Mit `--apply`
legt es zuerst einen Sicherungspunkt `safety` des aktuellen Stands an, damit sich das Zurückrollen selbst rückgängig
machen lässt, indem man auf diesen zurückrollt (`--no-caption` überspringt ihn, auf Produktion ohne
`--force-no-caption` verweigert). Dann stellt es die Teile wieder her, die der Sicherungspunkt enthält, in dieser
Reihenfolge, und prüft danach erneut (`--no-verify` überspringt das):

| Teil | Stellt wieder her |
|---|---|
| database | SQLite: die Kopie über die Backup-API von SQLite zurückgeschrieben; MySQL und MariaDB: der Abzug über `mysql`; PostgreSQL: der Abzug über `psql`. Tabellen, die nach dem Sicherungspunkt entstanden, werden gelöscht. MongoDB und Oracle: von Hand (`mongorestore --drop`, `impdp`) |
| storage | `var/` aus dem Archiv von git_manager über das der Installation entpackt (später hinzugekommene Dateien bleiben) |
| settings | `settings/override` über das der Installation zurückkopiert (später hinzugekommene Dateien bleiben) |
| libraries | `composer.json` und `composer.lock` zurückkopiert, dann `composer install` (verweigert bei Paketen, die eigene Git-Checkouts sind, wie in der Bibliotheken-Stufe) |
| code | jeder Checkout aus `code.json` zurück auf seinen Commit (`git reset --keep`, verweigert bei lokalen Änderungen in den Dateien, die es zurückbewegen würde) |

Ein verschlüsselter Sicherungspunkt (die GPG-Option von git_manager) muss zuerst mit git_manager entschlüsselt werden.

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

Jeder ausgeführte Schritt wird außerdem in `var/log/upgrade.log` geschrieben, und in das Auditprotokoll:
`system.upgrade.step` für jeden ausgeführten oder gescheiterten Schritt, `system.upgrade.backup` für die Sicherung,
`system.upgrade.run` am Ende eines Laufs mit `--apply`, `system.upgrade.rollback` für ein Zurückrollen,
`system.upgrade.caption` für einen angelegten, geprüften oder aufgeräumten Sicherungspunkt, `system.upgrade.verify`
für die Prüfung nach dem Upgrade ([das Audit](../bc/6.0/audit.md)).

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
| 1 | ein Schritt ist gescheitert, die Prüfung nach dem Upgrade ist gescheitert, die Sperre oder der Sicherungspunkt wurde verweigert, eine Stufe kam außer der Reihe, oder mit `--fail-on-pending` sind Schritte offen; `exp:upgrade:verify`: Abweichungen oder eine gescheiterte Prüfung; `exp:upgrade:captions --verify`: eine geänderte Datei oder ein Abzug, der sich nicht einspielen lässt |
| 2 | Bedienfehler (ein unbekannter Optionswert, eine unbekannte Stufe, ein unbekannter Schritt, ein unbekannter Sicherungspunkt oder ein unbekannter Teil; `--apply` mit `--dry-run`; `--no-caption` oder `--no-backup` auf Produktion ohne `--force-no-caption`) |
| 3 | die Datenbank ist nicht erreichbar |
| 4 | interner Fehler |

## Die Schritte von 6.0.15

| Schritt | Stufe | Ersetzt |
|---|---|---|
| `6.0.15-code-update` | code | `git pull` von Hand, über git_manager, nur Fast-Forward |
| `6.0.15-libraries-update` | libraries | `composer update <Paket>` von Hand (nur mit `--update`) |
| `6.0.15-libraries-install` | libraries | `composer install` von Hand, mit `composer audit` |
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
