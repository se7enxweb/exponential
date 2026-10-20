# Spezifikation: Installer-Protokolle und Startdaten

Diese Seite ist die Referenz für die Daten, mit denen eine neue Exponential-Installation beginnt (Startdaten-Dateien, der
Inhaltsbaum, IDs und URL-Aliase), und für die Dateien, die die Installer schreiben (`setup.log`, `kickstart.log` und weitere).
Lesen Sie sie, wenn Sie einen eigenen Installer oder ein eigenes Site-Paket bauen, eine frische Installation prüfen oder rund um den
Kickstarter Skripte schreiben. Die Anleitung finden Sie unter [Installation mit einem Befehl](../../../eng-us/features/6.0/install-in-one-command.md).

## Kurz gesagt

- Vier Quellen für Startdaten enthalten dieselben Zeilen; ändern Sie sie alle gemeinsam.
- Eine neue Installation hat zwei neue Ordner auf oberster Ebene: Configuration (Knoten 3) und Archives (Knoten 4).
- Jeder Lauf eines Installers schreibt `var/log/setup.log`, das mit einer Zeile `RESULT` und einer Zeile `NEXT` endet.

## Eine frische Installation prüfen

Lesen Sie im Wurzelverzeichnis der Installation das Ende des Setup-Protokolls:

```bash
tail -n 20 var/log/setup.log
```

Erwartet: eine eingerahmte Zeile `RESULT`, die Erfolg meldet, und eine Zeile `NEXT`, die sagt, was als Nächstes zu tun ist. Wenn
`var/log/initial-admin-password` existiert, melden Sie sich damit an und löschen Sie danach die Datei.

## Wo die Startdaten liegen

| Quelle | Verwendet von |
|---|---|
| `share/db_data.dba` (und `share/db_schema.dba`) | Dem Setup-Assistenten und dem Kickstarter auf jeder Engine: MySQL, PostgreSQL, SQLite, Oracle, MongoDB (über `eZDbSchema`). |
| `kernel/sql/common/cleandata.sql` | Installationen auf Basis eines SQL-Dumps auf MySQL und PostgreSQL. Die Anführungszeichen sind seit dem 30. September Standard-SQL (`''` und `""`), sodass auch PostgreSQL die Datei lädt. |
| `kernel/sql/sqlite/cleandata.sql` | SQL-Installation auf SQLite. |
| `update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql` | Upgrade einer vorhandenen Datenbank. |

Alle enthalten dieselben Zeilen. Wenn Sie Startdaten ändern, ändern Sie jede Datei,
laden Sie danach jede in eine Wegwerf-Datenbank und prüfen Sie den Baum. (Prüfen Sie eine Änderung an einer `.dba`
durch den Vergleich der Zeilen, nicht anhand ihres Diffs: Die Zeilen sind ein numerisch indiziertes Array, und
das Entfernen einer Zeile nummeriert alle nachfolgenden neu.)

### Änderungen vom September 2026

- SQLite lädt wieder das eigene Schema und die bereinigten Daten des Kernels, und die bereinigten SQL-Daten
  stimmen mit den Basisdaten überein, die Installationen erhalten. Die ungenutzten Demodaten-Dateien für SQLite
  wurden entfernt.
- SQL-Dumps, die aus den Schema- und Datendateien erzeugt werden, maskieren Anführungszeichen für MySQL,
  SQLite und PostgreSQL korrekt. Ein PostgreSQL-Dump, der auf einer Site mit einer anderen Datenbank erzeugt wird,
  fragt keine PostgreSQL-Kataloge mehr ab.
- Die Namen der ISBN-Gruppen tragen wieder ihren Apostroph.
- Sechzehn Links auf die eigene Website des früheren Produkts (Forum, Dokumentation, Vertrieb,
  Schulungen, Videos, Tutorials und zwei Konferenzfolien) wurden aus der Tabelle
  `ezurl` (**Setup > URL-Verwaltung**) der Basisdaten entfernt; kein Inhalt verwendete sie.
  XML-Namensraumadressen in gespeichertem Rich Text sind Bezeichner, keine Links, und bleiben.
- Die E-Mail-Adresse der eingebauten Benutzer und der Hostname im Suchindex verwenden
  die eigene Domain des Projekts, `exponential.earth`.

## Der Inhaltsbaum einer neuen Installation

Knoten 1 ist der virtuelle oberste Knoten. Direkt darunter:

| Ordner | Objekt | Knoten | Sektion | URL-Alias | Sortiert |
|---|---|---|---|---|---|
| Websites (die Inhaltswurzel, früher nach dem alten Produkt benannt; Titel „Welcome to Exponential“) | 1 | 2 | | (keiner, sie ist die Startseite) | nach Priorität |
| Configuration (neu) | 2 | 3 | Setup | `x-configuration` | nach Name, aufsteigend |
| Archives (neu) | 3 | 4 | Standard | `x-archives` | nach Veröffentlichungsdatum, neueste zuerst |
| Users, Media, Setup, Design | vorhanden | vorhanden | | Setup ist `x-setup` | |

(Sortierwerte gelesen aus den Knotenzeilen von `kernel/sql/common/cleandata.sql`:
Configuration `sort_field` 9, `sort_order` 1; Archives `sort_field` 2,
`sort_order` 0.)

Configuration und Archives sind veröffentlicht, sichtbar und in der Sprache der
Basisdaten immer verfügbar, mit eigenem Namen, eigener Version, Knotenzuweisung, eigenem Objektzustand (nicht
gesperrt), Eintrag im Suchindex und URL-Alias. Ihre IDs sind die niedrigsten
freien in jeder Startdaten-Datei und in der laufenden Installation, sodass eine aus den
Startdaten erstellte Installation und eine bestehende dieselben IDs tragen. Die Knotenpfade
(`path_identification_string`) verwenden Unterstriche, `x_setup`, `x_archives` und
`x_configuration`; die URL-Aliase verwenden Bindestriche:

| Ordner | Alias | Warum |
|---|---|---|
| Setup | `x-setup` (war `setup2`) | Der Kernel lässt nie zu, dass eine Adresse auf oberster Ebene einem Modulnamen gleicht, und `setup` ist das Setup-Modul. |
| Archives | `x-archives` | Steht daneben auf oberster Ebene. |
| Configuration | `x-configuration` | Dieselbe Konvention. |

Weitere Änderungen an den Startdaten:

- Eine Klassengruppe **Configuration** (Gruppe 5, nach Setup) existiert und ist leer.
- Version 1 des Objekts Users war auf 1970 datiert; sie trägt jetzt das Datum, an dem das Objekt
  veröffentlicht wurde (`1033917596`). Das `modified_subnode` des obersten Knotens rückt auf den Zeitpunkt vor, zu dem die
  zwei Ordner darunter veröffentlicht wurden.
- Der Wurzelordner heißt in beiden Dateien mit bereinigten Daten „Websites“ und trägt den Titel „Welcome to Exponential“,
  passend zu `share/db_data.dba`.
- **Bestellstatus.** Zusätzlich zu Pending, Processing und Delivered werden vierzehn Status für den Lebenszyklus
  angelegt; siehe
  [Shop-Dashboard und Bestellstatus](../../../eng-us/features/6.0/store-dashboard.md).

## Von den Installern geschriebene Protokolle

| Datei | Schreiber | Aufbewahrung | Steuerung |
|---|---|---|---|
| `var/log/setup.log` | `expSetupLog`, eine Datei pro Lauf des Kickstarters oder des Web-Assistenten (die Anfragen des Assistenten setzen einen Lauf fort; ein erneut gestarteter Assistent schließt den abgebrochenen) | Frühere Läufe rotieren nach `setup.log.1`, `.2` ... | `EXP_SETUP_LOG_DIR` |
| `var/log/kickstart.log` | Der Kickstarter (Kindprozess, dessen stdout und stderr durchgereicht werden) | `kickstart.log.1` bis `.9` | `EXP_KICKSTART_LOG=0` |
| `var/log/initial-admin-password` | Das Setup, wenn kein starkes Administratorpasswort angegeben wurde | Nach der ersten Anmeldung löschen | Modus nur für den Eigentümer |
| von `exp:install` verwendete Konfiguration in `var/log` | `exp:install` | Passwörter maskiert | |
| `var/log/error.log` | `eZDebug` | normal | Einträge `BEGIN` / `END` mit Lauf-ID und Ergebnis, solange ein Lauf andauert; Einträge tragen `(setup <id>, step N "...")` |

Aufbau von `setup.log`: Einleitung, Umgebung, ein Abschnitt pro Schritt mit
Dauer sowie Fehlern und Warnungen (gelesen aus `error.log`, `warning.log` und von
PHP; Wiederholungen gruppiert mit Anzahl, erstem und letztem Zeitpunkt; ein Hinweis neben bekannten
Problemen), Zustandsprüfungen (Datenbank, deklarierte Tabellen, Inhaltsklassen, Objekte,
die Inhaltswurzel, Namenslisten, Einstellungen, Siteaccesses, Dateieigentümer) und eine eingerahmte
Zeile `RESULT` und `NEXT`. Passwörter werden maskiert. Einträge aus dem Lauf tragen dessen
Kontextzeile; andere werden als „(not this run)“ aufgeführt und in einer `NOTE` zusammengefasst.
Das Protokoll hält die Site-Sprachen und die Sprachzuordnung der Pakete fest, auf die sich der Assistent
festgelegt hat. Ein Post-Install, der abbricht, meldet den Schritt, seine Nummer und die Anzahl
der Schritte (`eZSiteInstaller::abortedStep()`).

Anfragekontext in jeder Protokollzeile (seit 30. September): `eZLog::requestContext()`
liefert für jede Web-Anfrage den Siteaccess und die vollständige Adresse (Schema, Host, Port, Pfad) der
Anfrage, auch unter Velocity, wo die Worker unter der
Kommandozeilen-SAPI laufen und als die eigene Kommandozeile des Servers protokolliert wurden. `isWebRequest()`
unterscheidet unter jeder SAPI eine Web-Anfrage von einem Shell-Skript.

## Installer-Parameter, die ein Site-Paket erhält

`all_language_codes` (jede Site-Sprache, die primäre zuerst) bleibt, wie es war.
Neu: `primary_language`, `extra_language_codes` und `language_map` (was der
Sprachschritt für Pakete beantwortet hat), sodass sich ein Paket nicht mehr auf die Position der
primären Sprache in einer Liste verlassen muss. `eZStepInstaller::CLEAN_DATA_LANGUAGE` benennt die Sprache der bereinigten
Daten (`eng-US`) an einer einzigen Stelle.

## Datenbanktreiber und die Installer

- **Oracle** (`ezoracle`): `eZSetupDatabaseMap()` hat einen Eintrag `oci8` (Treiber
  `ezoracle`, 19c oder neuer); `eZSetupActivateDatabaseExtension()` lädt die Einstellungen dieser
  Extension für den Lauf. `DatabaseInit` nimmt den Connect-String als
  benannte Datenbank, wie bei MySQL und PostgreSQL. Tabellenaliase werden ohne
  `AS` geschrieben, das Oracle nicht akzeptiert.
- **PostgreSQL**: Der Installer legt die benötigte Erweiterung `pgcrypto` an,
  statt abzulehnen; der Schema-Handler legt die kleinen Integer-Spalten und
  Indexnamen an, die Schemas von Extensions verwenden; der Setup-Assistent installiert in die Datenbank,
  die auf der Datenbankseite angegeben ist (nicht in die erste Datenbank des Servers), und füllt Port
  5432 und Benutzer `postgres` vor; seine Meldungen nennen `pgcrypto` und die heutigen Ursachen.
- **SQLite**: `removeDatabase()` ist implementiert (löscht jedes Nicht-SQLite-Objekt in
  der Reihenfolge Trigger, View, Index, Tabelle und führt VACUUM aus), Sitzungsanweisungen `SET` werden
  angenommen und ignoriert, `BIT_OR` und `BIT_AND` sind registriert, und das
  `UPDATE ... JOIN` für die Sprachmaske wird in `UPDATE ... FROM` umgeschrieben. Der Treiber
  richtet seine Verbindung für eine Website ein und wartet auf eine Sperre, statt fehlzuschlagen, und
  maskiert `null` als leere Zeichenkette, wie es der MySQL-Treiber tut.
- **MySQL** meldet unter PHP 8.1 und neuer eine abgelehnte Anmeldung als fehlgeschlagene
  Verbindung.
- **MongoDB**: siehe [MongoDB-Unterstützung im Kernel](../../../eng-us/bc/6.0/MONGODB_KERNEL_SUPPORT_EXPANSION.md)
  und [Datenbanktreiber und Installer](datenbanktreiber-2026-09.md).

## Siehe auch

- Spezifikationen: [Datenbanktreiber und Installer, September 2026](datenbanktreiber-2026-09.md), [SQLite3-Datenbanktreiber](sqlite3-datenbanktreiber.md)
- Funktionen: [Installation mit einem Befehl](../../../eng-us/features/6.0/install-in-one-command.md), [Kickstarter: eine ganze Site aus einer Datei installieren](../../../eng-us/features/6.0/kickstarter-cli.md), [Ein Paket-Installer, der große Pakete übersteht](../../../eng-us/features/6.0/package-installer-batching.md), [In ein Paket hineinsehen, es mit Ihrer Site vergleichen, einzelne Elemente importieren](../../../eng-us/features/6.0/package-compare-and-import.md), [Wartungsmodus](../../../eng-us/features/6.0/maintenance-mode.md), [Shop-Dashboard und Bestellstatus](../../../eng-us/features/6.0/store-dashboard.md)
- Upgrade-Hinweise: [Kickstarter auf der Kommandozeile](../../../eng-us/bc/6.0/kickstartercli.md), [Verhaltensänderungen, 16. bis 30. September 2026](../../../eng-us/bc/6.0/behaviour-changes-2026-09b.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md), Chroniken [16. bis 30. September 2026](../../../eng-us/history/2026/2026-09b.md) und [Juni 2026, zweite Hälfte](../../../eng-us/history/2026/2026-06b.md)
