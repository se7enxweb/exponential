# Upgrade: von 4.x, 5.x oder einer früheren 6.0.x auf den aktuellen Stand

Diese Anleitung richtet sich an alle, die eine bestehende Installation betreiben und sie auf die aktuelle Linie
Exponential 6.0 bringen möchten. Diese Linie ist 6.0.15, noch in Entwicklung; der neueste Release-Tag zum Zeitpunkt
des Schreibens ist `v6.0.14` (siehe [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)). Ermitteln Sie Ihre Version,
folgen Sie dem Teil dafür und schließen Sie mit den Prüfungen am Ende ab. Sie zeigen Ihnen, dass das Ergebnis in
Ordnung ist.

Planen Sie für eine kleine Site zwanzig Minuten ein. Der riskante Schritt ist die Datenbank. Deshalb ist Schritt 1
eine Datensicherung.

## Welchen Teil lese ich?

Ermitteln Sie Ihre Version: In der Administration nennt sie die Seite **Setup** > **Systeminformationen**; auf der
Kommandozeile:

```bash
mysql -u USER -p DATABASE -e "SELECT name, value FROM ezsite_data WHERE name LIKE 'ezpublish-%';"
```

(Verwenden Sie das Entsprechende für PostgreSQL oder SQLite; die Zeilennamen `ezpublish-version` und
`ezpublish-release` behalten ihre historische Schreibweise absichtlich.) Erwartet: ein Wert wie `5.4.0` oder `6.0.14`.

| Sie betreiben | Lesen Sie |
|---|---|
| 3.10 bis 5.3 | Teil A, dann B |
| 5.4.0 oder 5.90 (2017.08 und später) | Teil B |
| 6.0.0 bis 6.0.14 oder einen früheren Checkout der Linie 6.0.15 | Teil C |
| `lovestack` (LoveStack, die Distribution desselben Kernels von Mugo Web, `mugoweb/ezpublish-legacy`) | [Umstieg von LoveStack](#umstieg-von-lovestack), dann Teil C, dann `exp:lovestack migrate` |

### Umstieg von LoveStack

LoveStack hat denselben Ursprung wie Exponential, und seine Datenbanken stehen denen von Exponential nahe. Ein
einziger Befehl, der nur liest, listet auf, was eine solche Site mitbringt und was mit jedem Punkt zu tun ist:

```bash
./console exp:lovestack check --allow-root-user          # add --json for a machine-readable report
```

Er liest die Datenbank und die Einstellungen und schreibt nichts. Jede Zeile ist markiert:

| Markierung | Bedeutung |
|---|---|
| `FOUND` | etwas zu tun; die Zeile darunter sagt, was (zum Beispiel die Versionszeile `lovestack`, die Extension `legacy_2018`, `[Session] SameSite`, umzubenennen in `CookieSameSite`, `image.ini [ValidUploadFormats] MIMEList[]`, umzubenennen in `[ImageUploadSettings] AllowedMIMETypes[]`) |
| `info` | vorhanden, und Exponential funktioniert damit so, wie es ist: Die Zeile `db-schema-version` von LoveStack bleibt als Aufzeichnung erhalten, sein eindeutiger Index auf `ezuser.login` und seine Spalte `data_text NOT NULL DEFAULT ''` können bleiben (Exponential speichert dort eine leere Zeichenkette, nie NULL), Einstellungen, die Exponential unter demselben Namen liest |
| `clear` | nicht vorhanden |
| `unknown` | die Datenbank-Engine gibt keine Auskunft (MongoDB hat keine Indizes oder Spalten, die gelesen werden könnten) |

Er endet mit `OK` (Exit-Code 0), wenn nichts zu tun ist, oder mit `NEEDS ATTENTION` (Exit-Code 1).

Erstellen Sie dann eine Datensicherung (Schritt 1), bringen Sie den neuen Code an seinen Platz und wenden Sie das
Datenbank-Update aus Teil C an (C1 und C2). Danach kennzeichnet ein weiterer Befehl die Datenbank als die von
Exponential und listet die Einstellungen auf, die von Hand zu ändern sind:

```bash
./console exp:lovestack migrate --allow-root-user            # a dry run: prints what it would write, writes nothing
./console exp:lovestack migrate --apply --allow-root-user    # writes it
```

Was er schreibt, in der Tabelle `ezsite_data` und nirgendwo sonst:

| Zeile | Wert |
|---|---|
| `ezpublish-version` | die Version dieser Release (`6.0.15stable`), wenn die Zeile noch `lovestack` lautet. Die Versionsprüfungen von Exponential (Setup > Upgrade-Prüfung, Info > Über) lesen diese Zeile. Das Update-SQL von C2 setzt sie ebenfalls; jeden anderen Wert überlässt er dieser Datei |
| `lovestack-schema-version` | der Wert der Zeile `db-schema-version` von LoveStack (`unknown`, wenn es keine gibt), sodass die Datenbank festhält, welche Schemaschritte von LoveStack sie durchlaufen hat. Die eigene Zeile von LoveStack bleibt, wie sie ist |

Er ändert niemals Inhalte, Benutzer oder Einstellungen, und ein erneuter Aufruf ändert nichts. Eine Datenbank, die
nicht von LoveStack stammt (keine Versionszeile `lovestack` und keine Zeile `db-schema-version`), wird mit einer
entsprechenden Meldung abgelehnt, und es wird nichts geschrieben. `--json` liefert denselben Bericht als ein
JSON-Objekt.

Unter den Zeilen gibt er die von Hand zu ändernden Einstellungen und Extensions aus, übernommen aus `check`:

- Einstellungen, die es nur in LoveStack gibt, jeweils mit dem zu verwendenden Namen in Exponential oder dem Hinweis,
  dass Exponential sie nicht liest. Benennen Sie `site.ini [Session] SameSite` in `CookieSameSite` um. Bis dahin liest
  Exponential `SameSite` als `CookieSameSite` und protokolliert einen Hinweis auf die veraltete Einstellung (sind beide
  gesetzt, wird `CookieSameSite` verwendet).
- Die Extension `legacy_2018`, falls sie aktiv ist: Entfernen Sie sie aus `ActiveExtensions[]` und
  `ActiveAccessExtensions[]`. Exponential behält den Auswahl-Datentyp (`ezenum`) im Kernel, und die Extension würde ihn
  ein zweites Mal definieren.
- Das Paket `mugoweb/ezpublish-legacy`, falls `composer.json` es noch verlangt: Ersetzen Sie es durch das von
  Exponential.

Dank an Mugo Web, dessen dokumentierte Schemaschritte in LoveStack (`update/database/mysql/lovestack/1.sql` bis `4.sql`)
es möglich gemacht haben, eine LoveStack-Datenbank so genau zu erkennen.

## Schritt 1: immer eine Datensicherung

```bash
mysqldump -u USER -p DATABASE > backup-before-upgrade.sql      # MySQL or MariaDB
pg_dump -U USER DATABASE > backup-before-upgrade.sql           # PostgreSQL
sqlite3 var/storage/sqlite3/exponential.db ".backup 'backup-before-upgrade.db'"  # SQLite: use the path of your database file
tar czf backup-var-and-settings.tgz var settings/override settings/siteaccess extension design
```

Halten Sie `settings/override/` sowie Ihre eigenen Extensions und Designs aus jedem Befehl heraus, der Dateien ersetzt.
Die eine Prüfung, die belegt, dass die Datensicherung brauchbar ist: Stellen Sie sie auf einer Testmaschine wieder
her, niemals auf der Live-Datenbank.

## Teil A: von 3.10 bis 5.3 (die alte Kette)

Die Datenbank-Update-Dateien bilden eine Kette; jede bringt Sie einen Schritt weiter. Wenden Sie sie **der Reihe nach,
jede genau einmal**, für Ihre Engine an (`mysql`, `postgresql`, `sqlite`, wo vorhanden), aus dem Verzeichnis
`update/database/<engine>/` des neuen Codes:

| Von | Nach | MySQL-Datei |
|---|---|---|
| 3.10 | 4.0 | `4.0/dbupdate-3.10.0-to-4.0.0.sql` |
| 4.0 | 4.1 | `4.1/dbupdate-4.0.0-to-4.1.0.sql` |
| 4.1 | 4.2 | `4.2/dbupdate-4.1.0-to-4.2.0.sql` |
| 4.2 | 4.3 | `4.3/dbupdate-4.2.0-to-4.3.0.sql` |
| 4.3 | 4.4 | `4.4/dbupdate-4.3.0-to-4.4.0.sql` |
| 4.4 | 4.5 | `4.5/dbupdate-4.4.0-to-4.5.0.sql` |
| 4.5 | 4.6 | `4.6/dbupdate-4.5.0-to-4.6.0.sql` |
| 4.6 | 4.7 | `4.7/dbupdate-4.6.0-to-4.7.0.sql` |
| 4.7 | 5.0 | `5.0/dbupdate-4.7.0-to-5.0.0.sql` |
| 5.0 | 5.1 | `5.1/dbupdate-5.0.0-to-5.1.0.sql` |
| 5.1 | 5.2 | `5.2/dbupdate-5.1.0-to-5.2.0.sql` |
| 5.2 | 5.3 | `5.3/dbupdate-5.2.0-to-5.3.0.sql` |
| 5.3 | 5.4 | `5.4/dbupdate-5.3.0-to-5.4.0.sql` |

Wenden Sie eine Datei mit dem Client Ihrer Datenbank an, zum Beispiel:

```bash
mysql -u USER -p DATABASE < update/database/mysql/4.1/dbupdate-4.0.0-to-4.1.0.sql
```

Regeln, die dies sicher machen:

- Überspringen Sie keine Datei und wenden Sie keine zweimal an. Schlägt eine Anweisung fehl, weil eine Spalte bereits
  existiert, wurde die Datei schon einmal angewendet: Halten Sie an und vergleichen Sie mit Ihrer Datensicherung.
- Wenn Sie den Datenbank-Cluster (DFS oder Datenbank-Dateihandler) verwenden, wenden Sie zusätzlich die Datei
  `dbupdate-cluster-...` desselben Schritts an, wo es eine gibt (4.3, 4.7, 5.2, 5.4).
- Die Verzeichnisse mit dem Namen `unstable` enthalten Dateien von Vorabversionen; wenden Sie sie nicht an.
- Die Verzeichnisse `6.12` und `7.3` gehören nicht zu diesem Weg; die 6.0-Dateien in Teil B bringen eine 5.4-Datenbank
  auf den aktuellen Stand.
- Mehrere Schritte liefern ein PHP-Skript mit, das Daten repariert; führen Sie die Skripte der Versionen aus, die Sie
  durchlaufen, aus dem Projektverzeichnis, zum Beispiel `php update/common/scripts/4.1/updateimagesystem.php`. Listen
  Sie sie mit `ls update/common/scripts/*` auf, und lesen Sie die ersten Zeilen eines Skripts (`--help`, wo es das
  anbietet), bevor Sie es ausführen.
  Was sich in jeder Release geändert hat und warum, steht in ihren Hinweisen:
  [4.1](../../en/us/bc/4.1/changes-4.1.0.txt), [4.2](../../en/us/bc/4.2/changes-4.2.0.txt), [4.3](../../en/us/bc/4.3/changes-4.3.0.txt),
  [4.4](../../en/us/bc/4.4/changes-4.4.0.txt), [4.5](../../en/us/bc/4.5/changes-4.5.0.txt), [4.6](../../en/us/bc/4.6/changes-4.6.0.txt),
  [4.7](../../en/us/bc/4.7/changes-4.7.0.txt), [5.0](../../en/us/bc/5.0/changes-5.0.txt), [5.1](../../en/us/bc/5.1/changes-5.1.txt),
  [5.2](../../en/us/bc/5.2/changes-5.2.txt), [5.3](../../en/us/bc/5.3/changes-5.3.txt), [5.4](../../en/us/bc/5.4/changes-5.4.txt).
- Die Update-Kette wird von `./console exp:checkdbfiles --allow-root-user` geprüft, das fehlende oder überzählige
  Dateien des Upgrade-Wegs auflistet.

Lesen Sie dann die Hinweise der Linie 5.90, die den Wechsel zu PHP 7 und eine längere Passwortregel vorbereitet hat:
[Übersicht 5.90](../../en/us/bc/5.90/README.md), [PHP 7](../../en/us/bc/5.90/php7.md), [Passwortlänge](../../en/us/bc/5.90/password_length.md),
[Indizierung von Relationen](../../en/us/bc/5.90/relation_indexing.md).

## Teil B: von 5.4 (oder 5.90) auf Exponential 6.0.0

1. Bringen Sie den neuen Code an seinen Platz. Entweder ein neuer Checkout, in den Sie Ihre `settings/override/`,
   `settings/siteaccess/`, `extension/` und `design/` kopieren, oder, wenn Sie die Site mit Composer verwalten, ändern
   Sie die Versionsbedingung von `se7enxweb/exponential` in der `composer.json` Ihres Projekts und führen
   `composer update se7enxweb/exponential` aus.
   Lassen Sie kein Werkzeug Ihre eigenen Einstellungen oder Extensions ersetzen.
2. Tragen Sie die neue Version in die Datenbank ein. Dieselbe Datei bringt das Schema auf den Stand, den der 6.0-Kernel
   verwendet: Sie verbreitert `ezuser.password_hash` vom `varchar(50)` jedes 5.x-Schemas auf 255 (mit dem Standard
   `HashType=php_default` und `UpdateHash=true` wird der Hash jedes Benutzers bei der ersten Anmeldung zu einem
   60 Zeichen langen bcrypt-Hash, den die alte Spalte nicht aufnehmen kann), fügt `ezcontentobject_trash.trashed` hinzu
   (ohne diese Spalte schlägt das Verschieben von Inhalten in den Papierkorb fehl) und benennt unter PostgreSQL die
   Sequenzen in `<table>_<column>_seq` um (die Namen, aus denen der Kernel neue IDs liest). Jede Änderung lässt eine
   Datenbank, die sie bereits hat, unverändert:

   ```bash
   mysql -u USER -p DATABASE < update/database/mysql/6.0/dbupdate-5.4.0-6.0.0.sql
   ```

   PostgreSQL: `update/database/postgresql/6.0/dbupdate-5.4-to-6.0.sql`. Die MySQL-Datei beginnt mit
   `SET default_storage_engine=InnoDB;`, was MySQL (ab 5.5.3) und MariaDB akzeptieren, führen Sie also die ganze Datei
   aus. Kopien der Datei von vor Oktober 2026 begannen mit `SET storage_engine=InnoDB;`, was MySQL ab 5.7.5 und
   MariaDB ab 12.0 ablehnen; nehmen Sie die aktuelle Datei. Details: [Changelog 6.0.0](../../changelogs/6.0/6.0.0.md).
3. PHP: Exponential 6.0 benötigt auf der aktuellen Linie PHP 8.0 oder neuer (8.1 oder neuer für Exponential Velocity;
   siehe [Unterstützung für PHP 8.0](../../en/us/bc/6.0/php-8.0-support.md)). Eine Site, die bei PHP 7.4 bleiben muss, bleibt bei 6.0.7; lesen Sie [Unterstützung für PHP 8](../../en/us/bc/6.0/php8.md) für die Reihenfolge der Upgrade-Schritte, und prüfen Sie Ihre eigenen Extensions
   auf PHP-8-Warnungen (Klassen, die `eZPersistentObject` oder `eZDataType` erweitern, sind die üblichen Fälle).
4. Fahren Sie mit Teil C fort, der für jeden 6.0.x-Schritt dasselbe Verfahren ist.

## Teil C: von einer beliebigen 6.0.x auf den aktuellen Stand

### C1. Den neuen Code an seinen Platz bringen

Holen Sie den neuen Code wie in Teil B, Schritt 1 (Checkout des Tags oder Branches oder `composer update`). Erzeugen
Sie dann die Class-Map neu und leeren Sie die Caches, in dieser Reihenfolge:

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all --allow-root-user
```

Erwartet: Der erste Befehl gibt aus, wie viele Klassen er gefunden hat; der zweite endet mit einer Zeile, die besagt,
dass die Caches geleert wurden.
Verwenden Sie `-k` nicht auf einer Installation, die zusätzliche Arbeitskopien des Codes in ihrem Wurzelverzeichnis hält (zum Beispiel Ordner von git worktrees), ohne diese auszuschließen, z. B. mit `--exclude='<folder>'`; andernfalls werden Kernel-Klassen in diese Kopien gemappt.
Laden Sie dann das PHP-FPM neu, das die Site ausliefert (`systemctl reload <your-php-fpm-service>`), und wenn Sie
Velocity betreiben, zeigt `./console exp:velocity deploy --dry-run --allow-root-user` jeden Schritt, den der echte
Befehl ausführen würde.

### C2. Das Datenbank-Update anwenden

Die Datei für die Linie ist `update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql` (es gibt Dateien für MySQL,
PostgreSQL und SQLite). Sie trägt die Version ein, nimmt unter MySQL und PostgreSQL die Schemaänderungen der Datei aus
Teil B vor (für eine Site, die mit einer älteren Kopie dieser Datei von 5.x gekommen ist; eine Datenbank, die sie
bereits hat, bleibt unverändert) und fügt die Spalten hinzu, die spätere Änderungen benötigen (Fußzeile des PDF-Exports,
OPML- und Podcast-Felder und mehr). Wenden Sie sie **einmal, vollständig und der Reihe nach** an; hat Ihre Datenbank
bereits eine Spalte, schlägt die Anweisung fehl und nennt Ihnen die Spalte; überspringen Sie nur diese Anweisung. Eine
Site von 5.x, die eine ältere Kopie dieser Datei angewendet hat und noch einen 50 Zeichen langen `password_hash`, kein
`ezcontentobject_trash.trashed` oder (PostgreSQL) Sequenzen `<table>_s` hat, wendet sie nicht erneut an: Sie wendet die
aktuelle Datei aus Teil B an und setzt die Versionszeile zurück mit
`UPDATE ezsite_data SET value='6.0.15stable' WHERE name='ezpublish-version';`. Andernfalls:

```bash
mysql -u USER -p DATABASE < update/database/mysql/6.0/dbupdate-6.0.0-6.0.15.sql
```

Legen Sie dann einmalig die Index-Tabellen des Audit-Protokolls an (das Audit-Protokoll ist ab 6.0.15 standardmäßig
eingeschaltet), und kopieren Sie, wer was in den Papierkorb gelegt hat, aus `<VarDir>/trash/trashed.json` in die neuen
Spalten `ezcontentobject_trash.trashed_by` und `trashed_via` (einmal je var-Verzeichnis, auf jedem Webserver eines
Clusters):

```bash
php update/common/scripts/6.0/createaudittables.php
php update/common/scripts/6.0/movetrashrecords.php --remove-file
```

Solange `trashed_by` und `trashed_via` nicht existieren, schreibt der Kernel weiterhin `trashed.json`; siehe
[die Papierkorbansicht](../../en/us/bc/6.0/trash.md#where-trashed-by-is-kept).

Prüfen Sie die Versionszeile (dieselbe Abfrage wie oben; erwartet wird nach dieser Datei `6.0.15stable`).

Eine neue Installation hat einen eindeutigen Index auf `ezuser.login`; die Datei fügt ihn bei einem Upgrade nicht
hinzu, da er auf einer Datenbank fehlschlägt, in der sich zwei Konten einen Login teilen. Listen Sie solche Logins auf
und fügen Sie den Index hinzu, wenn es keine gibt (andernfalls verweigert der Befehl die Ausführung und ändert nichts):

```bash
./console exp:user-login-check --allow-root-user
./console exp:user-login-check --fix-index --allow-root-user
```

Siehe [ein Konto je Login](sicherheit-und-audit.md#7-ein-konto-pro-login-2-minuten).

### C3. Die Checkliste jedes durchlaufenen Zeitraums lesen

Die Upgrade-Hinweise sind nach Zeiträumen geschrieben. Lesen Sie jeden zwischen Ihrer Version und heute; jeder beginnt
mit einer kurzen Checkliste und einer Spalte „Was zu tun ist“.

| Ihre Version ist älter als | Lesen Sie |
|---|---|
| 6.0.8 | [Changelog 6.0.8](../../changelogs/6.0/6.0.8.md) und [Unterstützung für PHP 8](../../en/us/bc/6.0/php8.md) |
| 6.0.10 | [Changelog 6.0.10](../../changelogs/6.0/6.0.10.md), [Das Produkt heißt Exponential](../../en/us/features/6.0/rebranding-to-exponential.md) |
| 6.0.13 | [Changelog 6.0.13](../../changelogs/6.0/6.0.13.md), [Sicherheitshärtung 6.0.13](../spezifikationen/6.0/sicherheitshaertung-6.0.13.md) |
| Juli 2026 | [Verhaltensänderungen von Juli und August 2026](../../en/us/bc/6.0/behaviour-changes-2026-07-08.md) |
| 16. September 2026 | [Verhaltensänderungen vom 16. bis 30. September 2026](../../en/us/bc/6.0/behaviour-changes-2026-09b.md) und [Sicherheitsvorgaben](../spezifikationen/6.0/sicherheitsvorgaben-2026-09.md) |
| 1. Oktober 2026 | [Verhaltensänderungen vom 1. bis 2. Oktober 2026](../../en/us/bc/6.0/behaviour-changes-2026-10.md) |

Die drei Änderungen, auf die man am häufigsten stößt:

- Konten ohne eine Zeile in `ezuser_setting` können sich nicht anmelden (August 2026): Stellen Sie sicher, dass jeder
  Benutzer eine hat.
- Overrides folgen `Priority` (Juli 2026): Überprüfen Sie jede `override.ini`, in der nur einige Blöcke eine
  `Priority` haben.
- Sicherheits-Header, Sitzungs-Cookies mit `HttpOnly`/`SameSite` und eine strengere Upload-Prüfung sind standardmäßig
  eingeschaltet (September und Oktober 2026): Wenn ein Host Ihre Administration in einem Frame einbettet, setzen Sie den
  Header bewusst.

Was sich Monat für Monat geändert hat, erzählt die [Chronik](../../en/us/history/2026/); jede einzelne Änderung steht im
[Verzeichnis des Root-Repositorys](../../en/us/history/ledger/exponential-root.md).

### C4. Ihre eigenen Extensions prüfen

```bash
php bin/php/ezpgenerateautoloads.php -e
./console exp:checkclasses --allow-root-user
```

`exp:checkclasses` lädt jede deklarierte Klasse und meldet diejenigen, die PHP nicht laden will; so zeigt sich eine
PHP-8-Inkompatibilität in einer Extension. Extensions tragen ihre eigene Version in `ezinfo.php` und `extension.xml`;
die Extension-Liste der Administration (**Setup** > **Extensions**) zeigt jede an, siehe
[Extension-Liste](../../en/us/features/6.0/extension-list-and-downloads.md). Die Reihenfolge, in der Extensions geladen werden,
hat sich 2026 geändert: [Ladereihenfolge der Extensions](../../en/us/features/6.0/extension-loading-order.md).

## Das Upgrade überprüfen

Führen Sie diese Befehle aus; jeder gibt an, wie ein gutes Ergebnis aussieht.

```bash
php bin/php/ezcache.php --clear-all --allow-root-user         # ends without an error
./console exp:checkdbfiles --allow-root-user                   # no file marked as missing for your line
```

Dann im Browser: Die Startseite lädt; melden Sie sich in der Administration an; öffnen Sie **Setup** >
**Systeminformationen** und lesen Sie die Version ab; veröffentlichen Sie ein Testelement und sehen Sie es auf der Site.
In der Administration listen **Setup** > **Upgrade-Prüfung** und
**Überprüfe Dateikonsistenz** Dateien des Codes auf, die von der Release abweichen
([Prüfung der Dateikonsistenz](../../en/us/features/6.0/file-consistency-check.md)). Dateien, die Sie absichtlich geändert
haben, werden dort erwartungsgemäß aufgeführt.
[Die Upgrade-Prüfung](upgrade-pruefung.md) erklärt jeden Befund und die Datenbankprüfung auf derselben Seite.

Leeren Sie zuletzt den Inhalts-Cache und, wenn Sie Velocity verwenden, dessen Antwort-Cache, nach dem Neuladen von
PHP-FPM, damit eine von altem Code gerenderte Seite nicht erneut ausgeliefert wird.

## Wenn etwas schiefgeht

| Symptom | Was zu tun ist |
|---|---|
| Eine weiße Seite nach dem Codewechsel | `var/log/error.log`; führen Sie C1 erneut aus; `vendor/` muss zur neuen `composer.json` passen |
| `Class ... not found` | `php bin/php/ezpgenerateautoloads.php -e`, dann die Caches leeren |
| Bibliotheken fehlen nach einem fehlgeschlagenen Composer-Lauf | [Eine Installation reparieren](../../en/us/bc/6.0/repair.md) |
| Eine SQL-Datei ist auf halbem Weg fehlgeschlagen | Stellen Sie die Datensicherung aus Schritt 1 wieder her und wenden Sie die Datei erneut von Anfang an an; reparieren Sie nie von Hand, um sie dann erneut auszuführen |
| Jemand kann sich nicht anmelden | [Verhaltensänderungen von Juli und August 2026](../../en/us/bc/6.0/behaviour-changes-2026-07-08.md), der Punkt zu `ezuser_setting` |

Eine veröffentlichte Release kann nicht geändert werden; der Weg zu einer Korrektur ist die nächste Version. Um zu
sehen, was eine Version enthält, öffnen Sie ihr Changelog: [6.0.0 bis 6.0.15](../../changelogs/6.0/6.0.15.md).

**Demo-Modus (6.0.15).** Die Umgebungsvariable `EXP_DEMO_MODE=1` kennzeichnet eine öffentliche Demo; ohne sie ändert sich nichts. Upgrade einer Demo: Behalten Sie die Variable für den Webserver, Cron und die Konsole bei, und prüfen Sie danach das Banner. Upgrade einer echten Site: Stellen Sie sicher, dass die Variable für keinen ihrer Prozesse gesetzt ist. [Demo-Modus](../../en/us/bc/6.0/demo-mode.md).

## Siehe auch

- [Erste Schritte](erste-schritte.md): eine saubere Installation derselben Version, für eine Testkopie.
- [Betrieb einer Site](betrieb-einer-website.md): Datensicherungen, Caches und Reparaturen nach dem Upgrade.
- [Bereitstellung](bereitstellung.md) und [Velocity-Engines](../../en/us/bc/6.0/velocity-engines.md): das richtige PHP-FPM neu laden oder auf einen schnelleren Server wechseln.
- [Extensions](extensions.md): Versionen, Ladereihenfolge und Releases Ihrer eigenen Extensions.
- Release-Hinweise: [Changelogs von Exponential 6.0](../../changelogs/6.0/6.0.15.md) und [die Release-Hinweise der Extensions](../../changelogs/extensions/README.md).
