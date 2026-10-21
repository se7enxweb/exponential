# 11. Upgrade

Dieses Kapitel bringt eine bestehende Installation auf die aktuelle Linie Exponential 6.0: von einer früheren 6.0.x,
von 5.4 oder 5.90 oder von den Linien 4.x und älteren 5.x. Es erklärt, wie Sie die Version ermitteln, die Sie
betreiben, die eine Regel, die ein Upgrade sicher hält (sichern und dann nie das Installationsprogramm über eine
bestehende Site laufen lassen), die Datenbank-Update-Dateien in `update/database/` für jede Engine und wie Sie sie
anwenden, die Skripte zur Datenreparatur in `update/common/scripts/`, die Konsolenbefehle, die das Ergebnis prüfen,
und die Release Notes, die Sie für jeden durchlaufenen Zeitraum lesen sollten. Es fasst die
[Anleitung zum Upgrade](../anleitungen/aktualisieren.md) zusammen und erweitert sie; diese bleibt das Verfahren Schritt für
Schritt.

[Inhalt](README.md) · Zurück: [10. Nach der Installation](10-nach-der-installation.md) · Weiter: [12. Fehlerbehebung](12-fehlerbehebung.md)

## 11.1 Bevor Sie beginnen

**Lassen Sie nie ein Installationsprogramm über eine bestehende Site laufen.** Der Setup-Assistent, der Kickstarter
und `exp:install` legen eine neue Datenbank an; mit `DatabaseAction=remove` (dem Standard von `exp:install`) leeren
sie die Datenbank, auf die sie gerichtet sind. Ein Upgrade ersetzt den Code und aktualisiert die bestehende Datenbank
an Ort und Stelle. `exp:install` verweigert die Ausführung, wo `settings/override/site.ini.append.php` bereits eine
Datenbank nennt („This directory already holds an installation“), und der Kickstarter verweigert seinen Schritt
`CreateSites` ohne `--force` („The CreateSites step will modify the database and site settings.“), unabhängig davon,
was das Verzeichnis enthält. Fügen Sie auf einer laufenden Site nicht `--force` hinzu, um eine der beiden Sperren zu
umgehen.

**Wer die Befehle ausführt.** Die Befehle in diesem Kapitel sind so geschrieben, wie der eigene Benutzer der Site sie
ausführt. Als `root` ausgeführt, verweigern die Skripte den Start, sofern nicht `--allow-root-user` angegeben ist, und
Dateien, die sie in `var/` anlegen, gehören danach `root`, sodass der Webserver sie nicht überschreiben kann
([Kapitel 12](12-fehlerbehebung.md#123-berechtigungen-und-eigentümerschaft)). Verwenden Sie bevorzugt
`sudo -u <site user> php ...`.

**Sichern Sie zuerst, jedes Mal.** Der Datenbankschritt ist derjenige, der sich nicht durch das Zurücklegen von
Dateien rückgängig machen lässt.

```bash
mysqldump -u USER -p DATABASE > backup-before-upgrade.sql          # MySQL or MariaDB
pg_dump -U USER DATABASE > backup-before-upgrade.sql               # PostgreSQL
sqlite3 var/storage/sqlite3/exponential.db ".backup backup-before-upgrade.db"   # SQLite, online backup
tar czf backup-var-and-settings.tgz var settings/override settings/siteaccess extension design
```

Verwenden Sie bei SQLite den Dateinamen Ihrer Installation (`[DatabaseSettings] Database` in
`settings/override/site.ini.append.php`); `.backup` ist die Online-Sicherung von SQLite und ist auch bei laufender Site
sicher, ein einfaches `cp` einer in Benutzung befindlichen Datenbankdatei ist es nicht. Stellen Sie die Sicherung
einmal auf einem Testrechner wieder her: Das ist der einzige Beweis, dass sie funktioniert.

**Nehmen Sie die Site für den Datenbankschritt vom Netz.**

```bash
php bin/php/maintenance.php on --message="Upgrade in progress" --until=30m
php bin/php/maintenance.php status
php bin/php/maintenance.php off
```

Solange der Wartungsmodus eingeschaltet ist, wird jede Seitenanfrage mit der Wartungsseite beantwortet (HTTP 503, nie
zwischengespeichert); Bilder, Stylesheets und Skripte werden weiterhin ausgeliefert. Mit `--allow-ip=` und
`--allow-admin` kommen Sie zum Prüfen hinein. Siehe [Wartungsmodus](../../eng-us/features/6.0/maintenance-mode.md).

## 11.2 Welche Version betreiben Sie?

Der Code und die Datenbank tragen jeweils eine Version, und nach einem korrekten Upgrade stimmen sie überein.

| Wo | Wie | Beispiel |
|---|---|---|
| Der Code | `php bin/php/console --version` (liest `lib/version.php`) | `console (Exponential) 6.0.15stable` |
| Die Datenbank | die Zeilen `ezpublish-version` und `ezpublish-release` der Tabelle `ezsite_data` | `6.0.14`, `1` |
| Die Administration | **Setup > Systeminformationen** | |

```bash
mysql -u USER -p DATABASE -e "SELECT name, value FROM ezsite_data WHERE name LIKE 'ezpublish-%';"
psql -U USER DATABASE -c "SELECT name, value FROM ezsite_data WHERE name LIKE 'ezpublish-%';"
sqlite3 var/storage/sqlite3/exponential.db "SELECT name, value FROM ezsite_data WHERE name LIKE 'ezpublish-%';"
```

Die Zeilennamen behalten ihre historische Schreibweise mit Absicht; sie sind Bezeichner, und jede Update-Datei schreibt
in sie.

Auf einer aktuellen Site gibt die Abfrage Folgendes aus (in der Darstellung von MySQL):

```text
+--------------------+--------------+
| name               | value        |
+--------------------+--------------+
| ezpublish-release  | 1            |
| ezpublish-version  | 6.0.15stable |
+--------------------+--------------+
```

Die Datei von 6.0.0 auf 6.0.15 schreibt `6.0.15stable`; die früheren Dateien schreiben eine Nummer wie `6.0.0` (die
Datei von 5.3 auf 5.4 schreibt `5.4.0alpha1`, wie sie es upstream tat).
Eine Versionszeile, die nicht zum Code passt, bedeutet, dass noch eine Update-Datei fehlt, und der Code verwendet
möglicherweise bereits eine Spalte, die die Datenbank noch nicht hat: Ermitteln Sie den Schritt anhand der Tabelle
unten und wenden Sie das Fehlende an, bevor die Site benutzt wird.

| Sie betreiben | Weg | Abschnitt |
|---|---|---|
| 3.10 bis 5.3 | die alte Kette bis 5.4, dann auf 6.0.0, dann auf den heutigen Stand | [11.4](#114-von-310-bis-53-die-alte-kette), [11.5](#115-von-54-oder-590-auf-600), [11.6](#116-von-einer-beliebigen-60x-auf-den-heutigen-stand) |
| 5.4.0, oder 5.90 (2017.08 und neuer) | auf 6.0.0, dann auf den heutigen Stand | [11.5](#115-von-54-oder-590-auf-600), [11.6](#116-von-einer-beliebigen-60x-auf-den-heutigen-stand) |
| 6.0.0 bis 6.0.14, oder ein früherer Checkout der Linie 6.0.15 | auf den heutigen Stand | [11.6](#116-von-einer-beliebigen-60x-auf-den-heutigen-stand) |

Dieses Kapitel ist die Update-Kette selbst. Für die praktische Seite des Umzugs einer ganzen Site gehen die
Migrationskapitel weiter: [14. von der Linie 4.x](14-migration-von-4x.md), [15. vom Legacy-Stack 5.x](15-migration-von-5x-legacy.md),
[16. von eZ Platform und Ibexa](16-migration-von-ez-platform-und-ibexa.md) und die Nachschlagetabellen in
[17. Migrationsreferenz](17-migrationsreferenz.md).

## 11.3 Die Update-Dateien

`update/database/<engine>/<version>/` enthält eine SQL-Datei je Schritt. Jede Datei bringt eine Datenbank **von** einer
Version **auf** die nächste, und die Kette muss der Reihe nach durchlaufen werden, jede Datei genau einmal.

### Nach Engine

| Engine | Verzeichnis | Verfügbare Schritte |
|---|---|---|
| MySQL oder MariaDB | `update/database/mysql/` | 3.10 → 4.0 → 4.1 → ... → 5.4, dann `6.0/dbupdate-5.4.0-6.0.0.sql`, dann `6.0/dbupdate-6.0.0-6.0.15.sql` |
| PostgreSQL | `update/database/postgresql/` | dieselbe Kette; der Schritt ab 5.4 ist `6.0/dbupdate-5.4-to-6.0.sql`, dann `6.0/dbupdate-6.0.0-6.0.15.sql` |
| SQLite | `update/database/sqlite/` | nur `6.0/dbupdate-6.0.0-6.0.15.sql`: Die SQLite-Unterstützung beginnt mit 6.0.1 |
| Oracle | `extension/ezoracle/update/database/` (in der Extension `ezoracle`): `ezpublish/` für die Kernel-Kette, `ezoracle/` für die eigenen Tabellen des Treibers | die Kernel-Kette bis 5.3 |
| MongoDB | keine | der MongoDB-Treiber kam mit 6.0.14; die Audit-Tabellen von 6.0.15 legt `createaudittables.php` an (unten); die Papierkorbfelder `trashed_by` und `trashed_via` brauchen kein Update ([Papierkorb](../../eng-us/bc/6.0/trash.md#where-trashed-by-is-kept)) |

### Die Kette von 4.0 bis 5.4 (Dateinamen für MySQL; PostgreSQL hat dieselben Namen)

| Von | Nach | Datei |
|---|---|---|
| 3.10 | 4.0 | `4.0/dbupdate-3.10.0-to-4.0.0.sql` |
| 4.0 | 4.1 | `4.1/dbupdate-4.0.0-to-4.1.0.sql` |
| 4.1 | 4.2 | `4.2/dbupdate-4.1.0-to-4.2.0.sql` |
| 4.2 | 4.3 | `4.3/dbupdate-4.2.0-to-4.3.0.sql` (+ `dbupdate-cluster-4.2.0-to-4.3.0.sql` bei MySQL) |
| 4.3 | 4.4 | `4.4/dbupdate-4.3.0-to-4.4.0.sql` |
| 4.4 | 4.5 | `4.5/dbupdate-4.4.0-to-4.5.0.sql` |
| 4.5 | 4.6 | `4.6/dbupdate-4.5.0-to-4.6.0.sql` |
| 4.6 | 4.7 | `4.7/dbupdate-4.6.0-to-4.7.0.sql` (+ `dbupdate-cluster-4.6.0-to-4.7.0.sql` bei MySQL) |
| 4.7 | 5.0 | `5.0/dbupdate-4.7.0-to-5.0.0.sql` |
| 5.0 | 5.1 | `5.1/dbupdate-5.0.0-to-5.1.0.sql` |
| 5.1 | 5.2 | `5.2/dbupdate-5.1.0-to-5.2.0.sql` (+ `dbupdate-cluster-5.1.0-to-5.2.0.sql` bei MySQL) |
| 5.2 | 5.3 | `5.3/dbupdate-5.2.0-to-5.3.0.sql` |
| 5.3 | 5.4 | `5.4/dbupdate-5.3.0-to-5.4.0.sql` (+ `dbupdate-cluster-5.3.0-to-5.4.0.sql` bei MySQL) |

### Regeln

- **Der Reihe nach, jede einmal.** Schlägt eine Anweisung fehl, weil eine Spalte oder Tabelle bereits existiert, wurde
  die Datei schon einmal angewendet: Halten Sie an und vergleichen Sie mit der Sicherung.
- **Cluster-Dateien.** Speichert die Installation Dateien im Datenbank-Cluster (DFS oder der Datenbank-Datei-Handler),
  wenden Sie zusätzlich die Datei `dbupdate-cluster-...` desselben Schritts an (MySQL: 4.3, 4.7, 5.2, 5.4).
- **Verzeichnisse `unstable/`** enthalten die Dateien von Vorabversionen (alpha, beta, rc). Wenden Sie sie nicht auf eine
  Site an, die eine finale Release betrieben hat.
- **`6.12/`, `7.2/` und `7.3/`** sind keine Schritte dieser Kette; wenden Sie sie nie als Dateien an. Ihre
  Schemaänderungen sind in den 6.0-Dateien enthalten: siehe [unten](#die-verzeichnisse-612-72-und-73).
- **Eine Datei, die mittendrin fehlschlägt.** Stellen Sie die Sicherung wieder her und wenden Sie die Datei erneut von
  Anfang an an; reparieren Sie nicht von Hand, um sie dann erneut auszuführen.
- **Prüfen Sie die Kette selbst.** `php bin/php/console exp:checkdbfiles --no-verify-branches` prüft die Update-Dateien
  gegen den Upgrade-Pfad, jedes Verzeichnis von 4.0 bis 7.3 für jede Engine, und meldet fehlende (`!`) oder
  überzählige (`?`) Dateien. Ohne `--no-verify-branches` versucht es außerdem, 4.3 bis 5.4 mit dem stillgelegten
  SVN-Repository des alten Herstellers zu vergleichen, was mit einer `C`-Zeile fehlschlägt.

Wenden Sie eine Datei mit dem Client der Datenbank an:

```bash
mysql -u USER -p DATABASE < update/database/mysql/4.1/dbupdate-4.0.0-to-4.1.0.sql
psql -U USER -d DATABASE -f update/database/postgresql/4.1/dbupdate-4.0.0-to-4.1.0.sql
sqlite3 var/storage/sqlite3/exponential.db < update/database/sqlite/6.0/dbupdate-6.0.0-6.0.15.sql
```

### Die Verzeichnisse 6.12, 7.2 und 7.3

Diese drei Verzeichnisse stammen aus dem Upstream-Legacy-Kernel der Jahre 2016 bis 2018, dem Code, auf dem Exponential
6.0 aufbaut. In diesen Jahren wurden seine Update-Dateien nach der Version des Symfony-Kernels nummeriert, neben dem
der Legacy-Kernel lief, nicht nach einer Legacy-Release, und jede Datei trägt diese Nummer in `ezsite_data` ein.
Exponential hat sie bis auf die Zeile `SET default_storage_engine` unverändert übernommen. Sie sind keine Schritte
zwischen 5.4 und 6.0.

| Datei | Engines | Herkunft | Wirkung |
|---|---|---|---|
| `6.12/dbupdate-5.4.0-to-6.12.0.sql` | MySQL, PostgreSQL | Februar 2016, Passwortsicherheit (EZP-24744) | trägt `6.12.0` ein; verbreitert `ezuser.password_hash` auf 255 |
| `7.2/dbupdate-6.13.0-to-7.2.0.sql` | PostgreSQL | Juni 2018, Sequenznamen (EZP-28706) | trägt `7.2.0` ein; benennt jede Sequenz von `<table>_s` in `<table>_<column>_seq` um und richtet jeden Spalten-Standardwert auf den neuen Namen aus |
| `7.3/dbupdate-7.2.0-to-7.3.0.sql` | MySQL, PostgreSQL | Oktober 2018, Papierkorbdatum (EZP-28881) | trägt `7.3.0` ein; fügt `ezcontentobject_trash.trashed` hinzu |

**Wenden Sie sie nicht als Dateien an** auf eine Datenbank auf dem Exponential-Pfad. Jede würde die Versionszeile durch
eine Nummer ersetzen, die keine Exponential-Version ist, und `7.2/` geht von 6.13.0 aus, einer Version, die keine
Exponential-Datenbank hat.

**Ihre Schemaänderungen sind in den 6.0-Dateien enthalten.** Der 6.0-Kernel wurde nach allen drei Änderungen gebaut
und verwendet ihr Schema; seit Oktober 2026 geben die 6.0-Dateien einer Datenbank, die von 5.4 (oder 5.90) kommt, alle
drei, sodass nichts von Hand ausgeführt werden muss:

- `ezuser.password_hash` auf 255 verbreitert ([11.5](#115-von-54-oder-590-auf-600), Schritt 2).
- `ezcontentobject_trash.trashed`, ohne das das Verschieben von Inhalten in den Papierkorb fehlschlägt: MySQL und
  PostgreSQL, dieselbe Definition wie in der Datei `7.3/`, nur hinzugefügt, wenn die Spalte fehlt.
- Nur PostgreSQL, die Sequenznamen: Der 6.0-Kernel liest die zuletzt eingefügte ID aus `<table>_<column>_seq`, deshalb
  schlägt auf einer Datenbank, die noch die Sequenzen `<table>_s` von 5.4 hat, jedes Einfügen fehl, das seine neue ID
  braucht. Dieselben 88 Sequenzen wie in der Datei `7.2/` werden umbenannt, jede nur dann, wenn der alte Name existiert
  und der neue frei ist, und jeder Spalten-Standardwert wird auf den neuen Namen ausgerichtet, wenn er ihn noch nicht
  nennt.

Alle drei sind in der Datei von 5.4 auf 6.0.0 enthalten und, für eine Site, die 6.0 mit einer älteren Kopie davon
erreicht hat, nochmals in der Datei von 6.0.0 auf 6.0.15. Auf einer Datenbank, die eine Änderung bereits hat, lassen
sie diese unverändert, sie sind also unschädlich auf einer Datenbank, die von Exponential 6.0 installiert wurde, oder
auf einer, die diese Upstream-Releases durchlaufen hat (ihre Versionszeile lautet `6.12.0`, `6.13.0`, `7.2.0` oder
`7.3.0`); eine solche Datenbank fährt mit [11.6](#116-von-einer-beliebigen-60x-auf-den-heutigen-stand) fort. SQLite braucht keine von ihnen:
Sein Schema hatte die Spalte schon immer, und es hat keine Sequenzen.

### Hinweise je Datenbank

| Engine | Hinweis |
|---|---|
| MySQL oder MariaDB | `6.0/dbupdate-5.4.0-6.0.0.sql` beginnt wie `6.12/`, `7.3/` und die Dateien der alten Kette mit `SET default_storage_engine=InnoDB;`, was MySQL (ab 5.5.3) und MariaDB akzeptieren: Wenden Sie die Dateien vollständig an. Kopien von vor Oktober 2026 begannen mit `SET storage_engine=InnoDB;`, einer Schreibweise, die MySQL in 5.7.5 und MariaDB in 12.0 entfernt hat; nehmen Sie die aktuellen Dateien. `6.0/dbupdate-6.0.0-6.0.15.sql` hat keine solche Zeile, weil jede ihrer Tabellen ihre Engine selbst nennt. |
| MySQL oder MariaDB | Tabellen müssen UTF-8 sein; `php bin/php/ezconvertdbcharset.php` konvertiert eine alte Datenbank. `php bin/php/ezconvertmysqltabletype.php --list` listet die Tabellentypen auf, und `--newtype=InnoDB` konvertiert sie. |
| PostgreSQL | Der Schritt ab 5.4 ist `6.0/dbupdate-5.4-to-6.0.sql`: die Versionszeilen, das breitere `password_hash`, die Spalte `trashed` und die Umbenennung der Sequenzen ([oben](#die-verzeichnisse-612-72-und-73)). Seine abgesicherten Schritte sind `DO`-Blöcke, die PostgreSQL 9.0 oder neuer brauchen. Die Funktion `digest` von `pgcrypto` muss in der Datenbank vorhanden sein. |
| SQLite | Jedes `ALTER TABLE` fügt eine Spalte hinzu, weil SQLite nur eine je Anweisung annimmt, und SQLite kennt kein `ADD COLUMN IF NOT EXISTS`: Bei einem zweiten Lauf schlägt jedes `ALTER TABLE` für eine bereits vorhandene Spalte fehl („duplicate column name“). Führen Sie die Datei einmal aus, nach der Online-Sicherung von SQLite. |
| Oracle | Die Extension `ezoracle` bringt eigene Update-Dateien bis 5.3 mit; wenden Sie sie mit Ihrem Oracle-Client an. Die Audit-Tabellen von 6.0.15 kommen von `createaudittables.php`. |
| MongoDB | Keine SQL-Dateien. `createaudittables.php` legt die Collections des Audit-Index über den Schema-Handler des Treibers an. Die Papierkorbdokumente erhalten `trashed_by` und `trashed_via` beim Schreiben, und ein älteres Dokument wird mit deren Standardwerten gelesen, sodass `movetrashrecords.php` wie bei den anderen Engines läuft. |

## 11.4 Von 3.10 bis 5.3: die alte Kette

1. Bringen Sie den neuen Code an seinen Platz (wie in [11.6](#116-von-einer-beliebigen-60x-auf-den-heutigen-stand), Schritt 1).
2. Wenden Sie die Update-Dateien der Kette aus [11.3](#113-die-update-dateien) an, von Ihrer Version bis 5.4, jede
   einmal.
3. Führen Sie die Skripte zur Datenreparatur jeder Version aus, die Sie durchlaufen (unten), aus dem
   Wurzelverzeichnis der Installation.
4. Lesen Sie die Release Notes jeder Version, die Sie durchlaufen:
   [4.1](../../eng-us/bc/4.1/changes-4.1.0.txt), [4.2](../../eng-us/bc/4.2/changes-4.2.0.txt), [4.3](../../eng-us/bc/4.3/changes-4.3.0.txt),
   [4.4](../../eng-us/bc/4.4/changes-4.4.0.txt), [4.5](../../eng-us/bc/4.5/changes-4.5.0.txt), [4.6](../../eng-us/bc/4.6/changes-4.6.0.txt),
   [4.7](../../eng-us/bc/4.7/changes-4.7.0.txt), [5.0](../../eng-us/bc/5.0/changes-5.0.txt), [5.1](../../eng-us/bc/5.1/changes-5.1.txt),
   [5.2](../../eng-us/bc/5.2/changes-5.2.txt), [5.3](../../eng-us/bc/5.3/changes-5.3.txt), [5.4](../../eng-us/bc/5.4/changes-5.4.txt).
5. Lesen Sie die Hinweise zur Linie 5.90, die den Wechsel zu PHP 7 und eine längere Passwortregel vorbereitet hat:
   [Übersicht 5.90](../../eng-us/bc/5.90/README.md), [PHP 7](../../eng-us/bc/5.90/php7.md), [Passwortlänge](../../eng-us/bc/5.90/password_length.md),
   [Indizierung von Verknüpfungen](../../eng-us/bc/5.90/relation_indexing.md).
6. Fahren Sie mit [11.5](#115-von-54-oder-590-auf-600) fort.

### Die Skripte zur Datenreparatur

`update/common/scripts/<version>/` enthält PHP-Skripte, die Daten für einen Schritt reparieren oder konvertieren.
Führen Sie die Skripte der Versionen aus, die Sie durchlaufen, aus dem Wurzelverzeichnis der Installation und nach der
SQL-Datei der jeweiligen Version, zum Beispiel:

```bash
php update/common/scripts/4.1/updateimagesystem.php --help
php update/common/scripts/4.1/updateimagesystem.php
```

Lesen Sie die ersten Zeilen eines Skripts oder seine `--help`-Ausgabe, bevor Sie es ausführen. Die Skripte und was sie
laut eigener Angabe tun:

| Version | Skript | Zweck |
|---|---|---|
| 4.0 | `updatebinaryfile.php`, `updatetipafriendpolicy.php`, `updatevatcountries.php` | Attribute für Binärdateien, die Richtlinie „Tip a friend“, Mehrwertsteuerländer |
| 4.1 | `addlockstategroup.php`, `correctxmlalign.php`, `fixclassremoteid.php`, `fixezurlobjectlinks.php`, `fixnoderemoteid.php`, `fixobjectremoteid.php`, `initurlaliasmlid.php`, `updateimagesystem.php` | Objektzustände, XML-Ausrichtung, Remote-IDs, URL-Verknüpfungen, mehrsprachige URL-Aliase, das Bildsystem |
| 4.2 | `fixorphanimages.php` | verwaiste Bilddateien |
| 4.3 | `updatenodeassignment.php` | Knotenzuweisungen |
| 4.4, 4.5 | `updatesectionidentifier.php` | Sektionsbezeichner |
| 4.6 | `removetrashedimages.php`, `updateordernumber.php` | Bilder von Inhalten im Papierkorb, Bestellnummern des Webshops |
| 5.0 | `deduplicatecontentstategrouplanguage.php`, `disablesuspicioususers.php`, `restorexmlrelations.php` | doppelte Übersetzungen von Zustandsgruppen, Deaktivieren von Konten, deren Login `<` oder `>` enthält, XML-Verknüpfungen |
| 5.1 | `fiximagesoutsidevardir.php` | Verweise auf Bilder außerhalb von `VarDir` |
| 5.2 | `cleanupdfscache.php` | Cache-Einträge in der DFS-Speichertabelle |
| 5.3 | `recreateimagesreferences.php`, `updatenodeassignmentparentremoteids.php` | fehlende Bildverweise, `parent_remote_id` von Knotenzuweisungen |
| 5.4 | `cleanuntranslatablerelations.php`, `cleanupfieldvaluerelations.php`, `fixremovedezurlobjectlinks.php`, `fixtrashedimagereferences.php` | veraltete Verknüpfungen, Links und Bildverweise |
| 6.0 | `createaudittables.php`, `movetrashrecords.php` | die Audit-Index-Tabellen von 6.0.15 (siehe [11.6](#116-von-einer-beliebigen-60x-auf-den-heutigen-stand)), wer was in den Papierkorb verschoben hat, aus `trashed.json` in die Papierkorbzeilen |
| beliebig | `cleanup.php`, `updatecontentobjectname.php`, `updatenbxmlcontents.php` | allgemeine Bereinigung, Objektnamen, Kodierung geschützter Leerzeichen in XML-Inhalten |

### Von einer 5.x-Site auf dem Symfony-Stack

Eine 5.x-Site, die den Legacy-Kernel innerhalb eines Symfony-Projekts betrieb, hatte ihn in `ezpublish_legacy/`; das
Installer-Plugin installiert ihn weiterhin dort, wenn das `composer.json` des Projekts `extra.symfony-app-dir` setzt.
Die Datenbank sowie die Legacy-Einstellungen, -Extensions und -Designs sind die der Legacy-Installation, und der oben
beschriebene Weg gilt für sie. Um Exponential 6 weiterhin innerhalb einer Symfony-Plattform zu betreiben, siehe
[Legacy Bridge](../../eng-us/features/6.0/legacy-bridge.md) und die
[Spezifikation des Legacy-Bridge-Bundles](../spezifikationen/6.0/legacy-bridge-bundle.md).

## 11.5 Von 5.4 (oder 5.90) auf 6.0.0

1. **Neuer Code.** Entweder ein frischer Checkout oder `composer create-project` der neuen Version, in den Sie Ihre
   Verzeichnisse `settings/override/`, `settings/siteaccess/`, Ihre eigenen `extension/`-Verzeichnisse und Ihre eigenen
   `design/`-Verzeichnisse kopieren; oder, wenn die Site ein Composer-Projekt ist, ändern Sie die Versionsbedingung von
   `se7enxweb/exponential` in Ihrem `composer.json` und führen `composer update se7enxweb/exponential` aus. Lassen Sie
   nie zu, dass ein Werkzeug Ihre eigenen Einstellungen oder Extensions ersetzt.
2. **Die Version eintragen und das Schema auf den Stand bringen, den der 6.0-Kernel verwendet.** Die Datei trägt 6.0.0
   ein und nimmt drei Schemaänderungen vor, die eine 5.4-Datenbank braucht ([11.3](#die-verzeichnisse-612-72-und-73)):
   Sie verbreitert `ezuser.password_hash` vom `varchar(50)` jedes 5.x-Schemas auf 255, die Breite des Kernel-Schemas;
   sie fügt `ezcontentobject_trash.trashed` hinzu; und unter PostgreSQL benennt sie die Sequenzen in
   `<table>_<column>_seq` um:

   ```bash
   mysql -u USER -p DATABASE < update/database/mysql/6.0/dbupdate-5.4.0-6.0.0.sql
   psql -U USER -d DATABASE -f update/database/postgresql/6.0/dbupdate-5.4-to-6.0.sql
   ```

   Wenden Sie die ganze Datei an: Nichts davon ist optional. Mit den Standardwerten `[UserSettings] HashType=php_default`
   und `UpdateHash=true` wird der Hash jedes Benutzers bei der ersten Anmeldung in einen 60 Zeichen langen
   bcrypt-Hash umgeschrieben, den eine 50 Zeichen breite Spalte ablehnt oder abschneidet, und der Benutzer kann sich
   nicht mehr anmelden; ohne `trashed` schlägt das Verschieben von Inhalten in den Papierkorb fehl; mit den alten
   Sequenznamen schlägt unter PostgreSQL jedes Einfügen fehl, das seine neue ID braucht. Jede Änderung lässt eine
   Datenbank, die sie bereits hat, unverändert, sodass die Datei erneut laufen kann. Kopien der Datei von vor Oktober
   2026 trugen nur die Version ein; die Datei von 6.0.0 auf 6.0.15 aus 11.6 nimmt dieselben Änderungen vor, für eine
   Site, die eine solche Kopie verwendet hat. Oracle (`ezoracle`) hat keine solche Datei: Führen Sie
   `ALTER TABLE ezuser MODIFY ( password_hash VARCHAR2(255) );` mit Ihrem Oracle-Client aus, und fügen Sie `trashed`
   hinzu (`ALTER TABLE ezcontentobject_trash ADD ( trashed INTEGER DEFAULT 0 NOT NULL );`), falls die Tabelle die
   Spalte nicht hat. Die MySQL-Datei läuft auf jedem MySQL ab 5.5.3 und auf MariaDB (siehe die Hinweise je Datenbank
   oben). Details: [Changelog 6.0.0](../../changelogs/6.0/6.0.0.md).
3. **PHP.** Die aktuelle Linie läuft unter PHP 8.0 bis 8.5 (`composer.json`: `^8.0`), Velocity ab seinem Release 0.0.4.54 eingeschlossen. Die
   Releases 6.0.8 bis 6.0.14 verlangten 8.1. Eine Site, die auf PHP 7.4 bleiben muss, bleibt auf 6.0.7. Prüfen Sie Ihre
   eigenen Extensions auf Probleme mit PHP 8: Klassen, die `eZPersistentObject` oder `eZDataType` erweitern, sind die
   üblichen Fälle. Siehe [PHP-8-Unterstützung](../../eng-us/bc/6.0/php8.md) und [PHP-8.0-Unterstützung](../../eng-us/bc/6.0/php-8.0-support.md).
4. Fahren Sie mit [11.6](#116-von-einer-beliebigen-60x-auf-den-heutigen-stand) fort.

6.0.8 bis 6.0.14 verlangten in `composer.json` 8.1; ab 6.0.15 wird 8.0 wieder akzeptiert, sodass ein Server, dessen
Betriebssystem PHP 8.0 mitliefert, die aktuelle Linie unter Apache, nginx oder Velocity betreiben kann (Velocity ab seinem Release 0.0.4.54)
([PHP-8.0-Unterstützung](../../eng-us/bc/6.0/php-8.0-support.md)). Composer setzt den Bereich durch: Unter einem älteren PHP bricht
`composer install` mit „Your requirements could not be resolved to an installable set of packages“ ab und nennt die
PHP-Version, bevor irgendetwas geändert wird.

## 11.6 Von einer beliebigen 6.0.x auf den heutigen Stand

### Schritt 1: den neuen Code an seinen Platz bringen

Wie in 11.5, Schritt 1: ein Checkout des Tags oder Branches (`git fetch --tags`, `git checkout v6.0.<n>` oder `main`)
gefolgt von `composer install`, oder `composer update se7enxweb/exponential` in einem Composer-Projekt. Erzeugen Sie dann
die Klassenverzeichnisse neu und leeren Sie die Caches, in dieser Reihenfolge:

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all
```

Warum zuerst die Autoloads: Eine neue Release kann Klassen hinzufügen oder umbenennen, und eine Klasse, die in den
Arrays fehlt, wird „nicht gefunden“, obwohl ihre Datei vorhanden ist. Warum danach die Caches: INI-, Template- und
Override-Caches enthalten, was der alte Code kompiliert hat.

Der Generator bricht bei einer Option, die er nicht kennt, mit Exit-Status 1 ab, sodass eine vertippte Option in einem
Upgrade-Skript auffällt. Wenn Sie das Kernel-Array neu erzeugen (`-k`), werden die Verzeichnisse aus
`.autoloadignore` ausgelassen; die mitgelieferte Datei schließt `vendor`, `var` und den Worktree-Ordner `.claude` aus.
Eine Installation, die weitere Arbeitskopien des Codes innerhalb ihres Wurzelverzeichnisses aufbewahrt (zum Beispiel
Git-Worktree-Ordner), muss sie dort eintragen oder `--exclude='<folder>'` übergeben; andernfalls werden
Kernel-Klassen in diese Kopien abgebildet.

Laden Sie dann das PHP neu, das die Site ausliefert: den PHP-FPM-Dienst der Site (`systemctl reload <service>`; unter
Plesk `plesk-php<XY>-fpm`, nicht `php-fpm`) und, unter Velocity, den Server:

```bash
php bin/php/console exp:velocity deploy --dry-run
php bin/php/console exp:velocity deploy
```

`exp:velocity deploy` führt jeden Schritt in der richtigen Reihenfolge aus: die Autoloads der Extensions, den
INI-Cache, die Template-, Override-, Übersetzungs- und Design-Caches, das Engine-Archiv bei Bedarf, das Neuladen von
PHP-FPM (`[DeploySettings] PhpFpmService`), den Neustart von Velocity, dann die Content-, HTTP- und
Template-Block-Caches und den Antwort-Cache von Velocity. Es gibt für jeden Schritt PASS, FAIL oder SKIP mit der
benötigten Zeit aus und hält beim ersten Fehler an. Fügen Sie `--kernel` hinzu, wenn das Upgrade Kernel-Klassen
hinzugefügt oder umbenannt hat (die Release Notes sagen es). Die Reihenfolge ist wichtig: Ein Cache dargestellter
Seiten, der geleert wird, bevor PHP den neuen Code ausführt, füllt sich wieder mit Seiten des alten Codes. Siehe
[Kapitel 8](08-die-website-ausliefern.md#8314-eine-änderung-bereitstellen-expvelocity-deploy) und
[Velocity-Engines](../../eng-us/bc/6.0/velocity-engines.md).

### Schritt 2: das Datenbank-Update anwenden

Die Datei für die Linie ist `update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql`, für MySQL, PostgreSQL und
SQLite. Sie trägt die Version ein (`6.0.15stable`); unter MySQL und PostgreSQL nimmt sie die drei Änderungen der Datei
von 5.4 auf 6.0.0 vor (das breitere `ezuser.password_hash`, `ezcontentobject_trash.trashed` und, unter PostgreSQL, die
Sequenznamen `<table>_<column>_seq`) für eine Site, die mit einer Kopie dieser Datei von vor Oktober 2026 von 5.x kam,
und lässt eine Datenbank, die sie bereits hat, unverändert (SQLite braucht keine davon:
[11.3](#die-verzeichnisse-612-72-und-73)); und sie fügt die Tabellen und Spalten der Linie hinzu: die Fußzeile des
PDF-Exports (`ezpdf_export.show_footer`, `footer_text`), OPML- und Podcast-Exporte (`ezrss_export.opml_head`,
`podcast_head`, Tabelle `ezrss_export_opml_item`), den Audit-Index (`expaudit_cursor`, `expaudit_event`,
`expaudit_file`), Lesezeichenordner (`expbookmark_folder`, `ezcontentbrowsebookmark.folder_id`, `priority`), die
E-Mail-Einstellungen (`expmail_category`, `expmail_consent_log`, `expmail_pending`, `expmail_preference`,
`expmail_suppression`) und wer ein Objekt in den Papierkorb verschoben hat (`ezcontentobject_trash.trashed_by`,
`trashed_via`; solange diese nicht existieren, schreibt der Kernel weiterhin `<VarDir>/trash/trashed.json`, danach
führen Sie `php update/common/scripts/6.0/movetrashrecords.php` aus, siehe
[die Papierkorbansicht](../../eng-us/bc/6.0/trash.md#where-trashed-by-is-kept)).

Wenden Sie sie **einmal, vollständig und der Reihe nach** an. Hat die Datenbank eine Spalte bereits, schlägt die
Anweisung fehl und nennt sie; überspringen Sie nur diese Anweisung.

Einer Site, die von 5.x kam und bereits eine ältere Kopie dieser Datei angewendet hat, können diese drei Änderungen
noch fehlen (`SHOW COLUMNS FROM ezuser LIKE 'password_hash'` und `SHOW COLUMNS FROM ezcontentobject_trash LIKE 'trashed'`;
unter PostgreSQL `\d ezuser`, `\d ezcontentobject_trash` und `\ds` in `psql`). Wenden Sie die Datei nicht erneut an:
Wenden Sie die aktuelle Datei von 5.4 auf 6.0.0 Ihrer Engine an, die die Änderungen vornimmt und erneut laufen kann,
und setzen Sie dann die Versionszeile, die diese Datei auf 6.0.0 setzt, zurück:
`UPDATE ezsite_data SET value='6.0.15stable' WHERE name='ezpublish-version';`.

```bash
mysql -u USER -p DATABASE < update/database/mysql/6.0/dbupdate-6.0.0-6.0.15.sql
```

Legen Sie dann die Audit-Index-Tabellen an und indizieren Sie die bisher geschriebenen Audit-Dateien (das
Audit-Protokoll ist ab 6.0.15 standardmäßig eingeschaltet):

```bash
php update/common/scripts/6.0/createaudittables.php --dry-run
php update/common/scripts/6.0/createaudittables.php
```

Das Skript funktioniert mit jeder Engine (MySQL/MariaDB, PostgreSQL, SQLite, Oracle und MongoDB, jeweils über ihren
Schema-Handler) und lässt vorhandene Tabellen unangetastet, sodass es erneut ausgeführt werden kann. `--dry-run` zeigt,
welche Tabellen fehlen; `--no-index` legt nur die Tabellen an. Bricht es mit „The audit index classes are not in the
autoload array“ ab, führen Sie zuerst `php bin/php/ezpgenerateautoloads.php -k` aus. Siehe [Audit](../../eng-us/bc/6.0/audit.md).

Prüfen Sie anschließend die Versionszeile (die Abfrage in [11.2](#112-welche-version-betreiben-sie)); erwartet wird
`6.0.15stable`.

### Schritt 3: die Checkliste jedes durchlaufenen Zeitraums lesen

Lesen Sie jede Release Note zwischen Ihrer Version und dem heutigen Stand; jede beginnt mit dem, was zu prüfen ist.

| Von | Lesen |
|---|---|
| 6.0.0 | [6.0.1](../../changelogs/6.0/6.0.1.md): SQLite, Ersetzen von Kernel-Modulen aus einer Extension |
| 6.0.1 | [6.0.2](../../changelogs/6.0/6.0.2.md): Links im Online-Editor |
| 6.0.2 | [6.0.3](../../changelogs/6.0/6.0.3.md): Datentyp-Extensions wurden Teil der Distribution |
| 6.0.3 | [6.0.4](../../changelogs/6.0/6.0.4.md): das responsive Administrationsdesign |
| 6.0.4 | [6.0.5](../../changelogs/6.0/6.0.5.md): eine fehlgeschlagene 6.0.4-Installation, Datentypen von Extensions |
| 6.0.5 | [6.0.6](../../changelogs/6.0/6.0.6.md): `admin3` auf Smartphones und Tablets |
| 6.0.6 | [6.0.7](../../changelogs/6.0/6.0.7.md): die letzte Release für PHP 7.4 |
| 6.0.7 | [6.0.8](../../changelogs/6.0/6.0.8.md): **braucht PHP 8.1**; und [PHP-8-Unterstützung](../../eng-us/bc/6.0/php8.md) |
| 6.0.8 | [6.0.9](../../changelogs/6.0/6.0.9.md): PostgreSQL, REST-Zugriff auf Inhalte |
| 6.0.9 | [6.0.10](../../changelogs/6.0/6.0.10.md): der Produktname in Templates, Übersetzungen und Overrides; [Das Produkt heißt Exponential](../../eng-us/features/6.0/rebranding-to-exponential.md) |
| 6.0.10 | [6.0.11](../../changelogs/6.0/6.0.11.md): PHP 8.5, Benachrichtigungs-E-Mails, Zählen von Seitenaufrufen |
| 6.0.11 | [6.0.12](../../changelogs/6.0/6.0.12.md): rollenbasierte Template-Operatoren, mehrere Sites in einer Installation |
| 6.0.12 | [6.0.13](../../changelogs/6.0/6.0.13.md): **Sicherheitskorrekturen**, Prüfungen der Sortierspalten; [Sicherheitshärtung 6.0.13](../spezifikationen/6.0/sicherheitshaertung-6.0.13.md), [Härtung](../../eng-us/bc/6.0/hardening.md) |
| 6.0.13 | [6.0.14](../../changelogs/6.0/6.0.14.md): MongoDB, die Prüfung der Upgrade-Dateien |
| 6.0.14 | [6.0.15](../../changelogs/6.0/6.0.15.md), dann der Reihe nach: [Juli und August 2026](../../eng-us/bc/6.0/behaviour-changes-2026-07-08.md), [16. bis 30. September 2026](../../eng-us/bc/6.0/behaviour-changes-2026-09b.md) mit [Sicherheitsvorgaben](../spezifikationen/6.0/sicherheitsvorgaben-2026-09.md), [1. und 2. Oktober 2026](../../eng-us/bc/6.0/behaviour-changes-2026-10.md), [E-Mail-Einstellungen und die Mail-Sperre](../../eng-us/bc/6.0/mail-preferences.md) |

Die Änderungen, auf die man beim Erreichen von 6.0.15 am häufigsten stößt:

- **Konten ohne Zeile in `ezuser_setting` können sich nicht anmelden** (16. August 2026): Ein Benutzer ohne
  Einstellungszeile galt früher als aktiviert und gilt jetzt als deaktiviert. In der Administration angelegte Konten
  haben die Zeile immer; per SQL, Paket oder Migration angelegte Konten möglicherweise nicht. Die Abfragen, die sie
  finden, stehen in der
  [Sicherheitsspezifikation vom August 2026](../spezifikationen/6.0/sicherheitshaertung-2026-08.md#f-06-konten-finden-die-sich-nicht-anmelden-können).
- **Overrides folgen `Priority`** (12. Juli 2026): Die Override-Blöcke von `override.ini` werden nach `Priority`
  sortiert, die niedrigste zuerst, und ein Block ohne diese Angabe kommt zuletzt. Überprüfen Sie jede `override.ini`, in
  der nur einige Blöcke ein `Priority` haben.
- **Sicherheits-Header, Sitzungscookies mit `HttpOnly`/`SameSite` und eine strengere Upload-Prüfung sind standardmäßig
  eingeschaltet** (September und Oktober 2026). `[Session] CookieSecure=auto` markiert das Sitzungscookie als `Secure`,
  sobald die Anfrage über HTTPS kam. Wenn ein anderer Host Ihre Administration in einen Frame einbettet, setzen Sie den
  Header bewusst.
- **Legacy-Extensions** wurden zwischen dem 22. September und dem 2. Oktober 2026 auf einen gemeinsamen Standard
  gebracht: [Verhaltensänderungen der Legacy-Extensions](../../eng-us/bc/6.0/extensions-behaviour-changes.md).
- **Die Standardwerte des Static Cache** haben sich geändert: [Standardwerte des Static Cache](../../eng-us/bc/6.0/static-cache-defaults.md).
- **Velocity**, falls Sie es betreiben: [Upgrade-Hinweise zur Velocity-Engine](../../eng-us/bc/6.0/velocity-engine-upgrade-notes.md)
  und [Verzeichnisstruktur auf der Festplatte](../../eng-us/bc/6.0/velocity-ondisk-layout.md).

Die Geschichte nach Monaten finden Sie in der [Chronik](../../eng-us/history/2026/); jede einzelne Änderung im
[Hauptbuch des Root-Repositorys](../../eng-us/history/ledger/exponential-root.md).

### Schritt 4: die eigenen Extensions prüfen

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/console exp:checkclasses
```

`exp:checkclasses` lädt jede deklarierte Klasse und meldet diejenigen, die PHP nicht laden will; so zeigt sich eine
Inkompatibilität mit PHP 8 in einer Extension. Extensions tragen ihre eigene Version in `ezinfo.php` und
`extension.xml`, und die Liste **Setup > Extensions** der Administration zeigt sie an
([Extension-Liste](../../eng-us/features/6.0/extension-list-and-downloads.md)). Die Reihenfolge, in der Extensions geladen werden,
hat sich 2026 geändert: [Ladereihenfolge der Extensions](../../eng-us/features/6.0/extension-loading-order.md).
Mit Composer installierte Extensions werden mit ihrem Paket aktualisiert: `composer update se7enxweb/<package>`,
nachdem Sie die Hinweise zum Paket in den [Release Notes der Extensions](../../changelogs/extensions/README.md)
gelesen haben.

Einige klassische Extensions haben eigene Upgrade-Skripte in `bin/php/`:

| Skript | Aktualisiert |
|---|---|
| `php bin/php/ezwebinupgrade.php --to-version=<version>` | eine bestehende `ezwebin`-Installation auf die aktuelle Paketversion |
| `php bin/php/ezflowupgrade.php --to-version=<version>` | die installierten `ezflow`-Pakete (`--package`, `--url`) |

Führen Sie sie zuerst mit `--help` aus.

## 11.7 Das Upgrade überprüfen

```bash
php bin/php/ezcache.php --clear-all                                # ends without an error
php bin/php/console exp:checkdbfiles --no-verify-branches          # prints nothing: every update file is in place
php bin/php/console exp:checkclasses                               # no class PHP refuses to load
php bin/php/console exp:checkmanifest --all                        # the installed files against share/filelist.md5; exit status 0 when all match
```

`exp:checkdbfiles` meldet nur Probleme, je eine Zeile: `!` für eine Datei, die der Upgrade-Pfad nennt, die aber auf der
Festplatte fehlt, `?` für eine Datei auf der Festplatte, die der Pfad nicht kennt. Keine Ausgabe (abgesehen von der
eigenen Zeile `running ...` der Konsole) bedeutet, dass die Update-Dateien vollständig sind. Ohne
`--no-verify-branches` versucht es außerdem, das stillgelegte SVN-Repository des alten Herstellers zu erreichen, und
gibt dafür eine `C`-Zeile aus; ignorieren Sie sie oder fügen Sie die Option hinzu.

Dann im Browser: Die Startseite lädt; Sie können sich an der Administration anmelden; **Setup > Systeminformationen**
zeigt die neue Version; ein Testelement lässt sich veröffentlichen und erscheint auf der Site. **Setup >
Upgrade-Prüfung** mit **Überprüfe Dateikonsistenz** vergleicht die installierten Dateien mit dem Dateimanifest
`share/filelist.md5` und listet die abweichenden auf ([Prüfung der Dateikonsistenz](../../eng-us/features/6.0/file-consistency-check.md)).
Dateien, die Sie absichtlich geändert haben, sind dort zu erwarten.

Weitere Werkzeuge, die nach einem Upgrade helfen:

| Befehl | Wann |
|---|---|
| `php bin/php/ezsqldiff.php --type=mysql --user=USER --password=PASS share/db_schema.dba <database>` | Vergleich Ihrer Datenbank mit dem Referenzschema, das **zuerst** angegeben wird. Die Ausgabe ist das SQL, das das zweite Argument (Ihre Datenbank) in das erste (die Referenz) überführen würde: Eine Zeile mit `CREATE TABLE` oder `ADD` nennt etwas, das ein Update-Schritt hätte anlegen sollen, eine Zeile mit `DROP TABLE` eine Tabelle, die nur Ihre Datenbank hat (die Tabellen Ihrer eigenen Extensions erscheinen so). Lesen Sie es, führen Sie es nicht aus. Exit-Code 0, wenn die Schemas übereinstimmen, 1, wenn sie sich unterscheiden (`--type` ist `mysql` oder `postgresql`). Die Kapitel 14 bis 16 verwenden es auf dieselbe Weise |
| `php bin/php/updateniceurls.php` | die URL-Aliase neu aufbauen (`--update-nodes`) |
| `php bin/php/updatesearchindex.php` | alle Inhalte in der Suchmaschine neu indizieren |
| `php bin/php/cleanupversions.php` | archivierte Versionen jenseits der Grenzen von `VersionManagement` entfernen |

Leeren Sie zuletzt den Content-Cache und, unter Velocity, dessen Antwort-Cache, und zwar **nach** dem Neuladen von
PHP-FPM oder dem Neustart von Velocity, damit keine vom alten Code dargestellte Seite erneut ausgeliefert wird:

```bash
php bin/php/ezcache.php --clear-tag=content
php bin/php/console exp:velocity cache clear
```

## 11.8 Wenn etwas schiefgeht

| Symptom | Was zu tun ist |
|---|---|
| Eine weiße Seite oder HTTP 500 nach dem Austausch des Codes | Lesen Sie `var/log/error.log`; führen Sie Schritt 1 aus 11.6 erneut aus; `vendor/` muss zum neuen `composer.json` passen. |
| „The site is missing the software libraries it needs“ (HTTP 503) | `vendor/` fehlt: `composer install`; siehe [Eine Installation reparieren](../../eng-us/bc/6.0/repair.md). |
| `Class ... not found` | `php bin/php/ezpgenerateautoloads.php -e`, dann die Caches leeren. |
| Eine SQL-Datei ist mittendrin fehlgeschlagen | Stellen Sie die Sicherung wieder her und wenden Sie die Datei erneut von Anfang an an. |
| `SET storage_engine=InnoDB;` schlägt fehl | Eine Update-Datei von vor Oktober 2026: In den aktuellen Dateien steht `SET default_storage_engine=InnoDB;`. Nehmen Sie die aktuelle Datei, oder überspringen Sie diese eine Zeile. |
| Jemand kann sich nicht anmelden | Die Änderung an `ezuser_setting` vom August 2026 (11.6, Schritt 3). |
| Eine Änderung wird unter Velocity nicht sichtbar | `exp:velocity restart` (oder `deploy`), dann `exp:velocity cache clear`. |

Mehr in [Kapitel 12](12-fehlerbehebung.md).

Eine veröffentlichte Release ändert sich nie; eine Korrektur kommt immer als nächste Version. Um zu sehen, was eine
Version enthält, öffnen Sie ihr Changelog: [6.0.0 bis 6.0.15](../../changelogs/6.0/6.0.15.md).

**Demo-Modus (6.0.15).** Auf einer aktualisierten Site ändert sich nichts, solange die Umgebungsvariable `EXP_DEMO_MODE=1` nicht gesetzt ist, die nur für öffentliche Demos gedacht ist. Prüfen Sie nach dem Upgrade eines Produktivservers, dass kein Prozess einer echten Site sie hat (`grep -r EXP_DEMO_MODE` über die PHP-FPM-Pools, virtuellen Hosts, `.htaccess`-Dateien, Service-Units und Crontabs). [Demo-Modus](../../eng-us/bc/6.0/demo-mode.md).

**Icon-Themes von Erweiterungen (6.0.15).** Die mitgelieferten Umschreiberegeln liefern jetzt `extension/<name>/icons/` (SVG-, PNG- und GIF-Bilder, deren Name genau eine Dateiendung hat) als Datei aus. Ein Upgrade schreibt Ihre eigene `.htaccess` oder Serverkonfiguration nie um: Wenn eine Erweiterung der Site Icons mitbringt (`icon.ini [ExtensionSettings] IconExtensions[]`), kopieren Sie die Regel hinein, vor die Regel des Front-Controllers. Apache `.htaccess`: `RewriteRule ^extension/[^/]+/icons/[A-Za-z0-9_/-]+\.(svg|png|gif)$ - [L]`; ein virtueller Host nimmt `^/extension/...`, nginx `location ~ ^/extension/[^/]+/icons/[A-Za-z0-9_/-]+\.(svg|png|gif)$ { try_files $uri =404; }`. Velocity und die erzeugte Konfiguration von FrankenPHP haben sie nach `exp:velocity deploy`. Alle Server: [Regeln für den Webserver](../bc/6.0/symbole.md#regeln-für-den-webserver).

## Referenzen

In diesem Repository:

- [Upgrade](../anleitungen/aktualisieren.md): die Schritt-für-Schritt-Anleitung, die dieses Kapitel erweitert.
- Release Notes: [Changelogs von 6.0](../../changelogs/6.0/6.0.15.md) ([6.0.0](../../changelogs/6.0/6.0.0.md) bis
  [6.0.14](../../changelogs/6.0/6.0.14.md)), die [Release Notes der Extensions](../../changelogs/extensions/README.md),
  [alle Changelogs](../../changelogs/README.md).
- Verhaltensänderungen: [Juli und August 2026](../../eng-us/bc/6.0/behaviour-changes-2026-07-08.md),
  [16. bis 30. September 2026](../../eng-us/bc/6.0/behaviour-changes-2026-09b.md),
  [1. und 2. Oktober 2026](../../eng-us/bc/6.0/behaviour-changes-2026-10.md),
  [Legacy-Extensions](../../eng-us/bc/6.0/extensions-behaviour-changes.md),
  [E-Mail-Einstellungen](../../eng-us/bc/6.0/mail-preferences.md), [Standardwerte des Static Cache](../../eng-us/bc/6.0/static-cache-defaults.md),
  [Upgrade-Hinweise zur Velocity-Engine](../../eng-us/bc/6.0/velocity-engine-upgrade-notes.md).
- PHP: [PHP-8-Unterstützung](../../eng-us/bc/6.0/php8.md), [PHP-8.0-Unterstützung](../../eng-us/bc/6.0/php-8.0-support.md),
  [5.90 und PHP 7](../../eng-us/bc/5.90/php7.md).
- Sicherheit: [Härtung in 6.0.13](../../eng-us/bc/6.0/hardening.md),
  [Sicherheitshärtung 6.0.13](../spezifikationen/6.0/sicherheitshaertung-6.0.13.md),
  [Sicherheitshärtung August 2026](../spezifikationen/6.0/sicherheitshaertung-2026-08.md),
  [Sicherheitsvorgaben](../spezifikationen/6.0/sicherheitsvorgaben-2026-09.md).
- [Audit](../../eng-us/bc/6.0/audit.md), [Wartungsmodus](../../eng-us/features/6.0/maintenance-mode.md),
  [Prüfung der Dateikonsistenz](../../eng-us/features/6.0/file-consistency-check.md),
  [Eine Installation reparieren](../../eng-us/bc/6.0/repair.md), [Velocity-Engines](../../eng-us/bc/6.0/velocity-engines.md).
- [Legacy Bridge](../../eng-us/features/6.0/legacy-bridge.md).
- [Eine Site betreiben](../anleitungen/betrieb-einer-website.md): Sicherungen, Caches und Reparaturen.
- [Geschichte](../../eng-us/history/README.md): die [Chronik 2026](../../eng-us/history/2026/) und das
  [Hauptbuch](../../eng-us/history/ledger/exponential-root.md).
- Code: `update/database/`, `update/common/scripts/`, `bin/php/checkdbfiles.php`, `bin/php/checkclasses.php`,
  `bin/php/ezsqldiff.php`, `lib/version.php`.

Extern:

- PHP-Migrationsanleitungen: [8.0](https://www.php.net/manual/en/migration80.php),
  [8.1](https://www.php.net/manual/en/migration81.php), [8.2](https://www.php.net/manual/en/migration82.php),
  [8.3](https://www.php.net/manual/en/migration83.php), [8.4](https://www.php.net/manual/en/migration84.php),
  [8.5](https://www.php.net/manual/en/migration85.php).
- Datenbanksicherungen: [mysqldump](https://dev.mysql.com/doc/refman/8.4/en/mysqldump.html),
  [pg_dump](https://www.postgresql.org/docs/current/app-pgdump.html),
  [SQLite-Befehl backup](https://sqlite.org/cli.html),
  [PostgreSQL pgcrypto](https://www.postgresql.org/docs/current/pgcrypto.html).
- Die Variable für die Storage-Engine: [Systemvariablen des MariaDB-Servers](https://mariadb.com/docs/server/server-management/variables-and-modes/server-system-variables)
  (`storage_engine`: in 5.5 als veraltet markiert, in 12.0 entfernt; `default_storage_engine`).
- Composer: [update](https://getcomposer.org/doc/03-cli.md#update-u-upgrade).
- GitHub: [Releases von se7enxweb/exponential](https://github.com/se7enxweb/exponential/releases).

[Inhalt](README.md) · Zurück: [10. Nach der Installation](10-nach-der-installation.md) · Weiter: [12. Fehlerbehebung](12-fehlerbehebung.md)
