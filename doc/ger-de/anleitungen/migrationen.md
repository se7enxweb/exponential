# Migrationen: Änderungen an einer Installation, einmal geschrieben und überall angewendet

Diese Anleitung erklärt die Migrationen des Kernels von Exponential 6.0.15: eine Änderung an einer Installation (eine
Tabelle, Inhalte, eine Rolle, eine Einstellung), einmal in einer Datei unter Versionskontrolle geschrieben und auf
jede Installation der Site angewendet, in derselben Reihenfolge, mit einer Aufzeichnung, was wo gelaufen ist. Sie ist
der Überblick; die am Ende verlinkten Seiten erklären jeden Teil.

Sie richtet sich an Entwickler, die die Datenbank oder die Inhalte einer Site im Code ändern, und an Administratoren,
die diese Änderungen auf Staging und Produktion anwenden. Die Befehle unten wurden am 31. Oktober 2026 auf SQLite
ausgeführt.

Der Bereich [Migrationen](../hilfe/migrationen/README.md) des Hilfezentrums beginnt hier: 25
[Rezepte](../hilfe/migrationen/rezepte/README.md) (eine Tabelle, eine Klasse, Inhalte in großen Mengen, Rollen,
Einstellungen, CI und Auslieferung), Seiten für jede Zielgruppe, [häufige Fragen](../hilfe/migrationen/faq.md) und
[Fehlerbehebung nach Symptom](../hilfe/migrationen/wenn-eine-migration-scheitert.md). Eine Site auf einen anderen
Server oder ein anderes Release umzuziehen ist eine andere Aufgabe:
[Migrationen oder Umzug einer Site](../hilfe/migrationen/migrationen-oder-umzug-einer-site.md).

[Anleitungen](README.md) · [Migrationen: der Einstieg](../hilfe/migrationen/README.md) · Siehe auch: [Migrationen: eine erste PHP-Migration, Schritt für Schritt](migrationen-tutorial.md),
[PHP-Migrationen](migrationen-php.md), [Zurücknehmen und Sicherungspunkte](migrationen-rueckgaengig-und-captions.md),
[Das Migrations-Repository](migrationen-repositories.md), [Fehlerbehebung](migrationen-fehlerbehebung.md),
[Die Funktion](../funktionen/6.0/migrationen.md), [Das Dateiformat](../spezifikationen/6.0/migrationsdateiformat.md)

## Kurz

```bash
./console exp:migration:generate --skeleton add_news_class --apply   # eine neue PHP-Migration in migrations/
./console exp:migration:check                                         # die Prüfung: Namen, Kopf, Reihenfolge, PHP
./console exp:migration:status                                        # was erledigt ist, was aussteht
./console exp:migration:migrate                                       # Probelauf: was sich ändern würde
./console exp:migration:migrate --apply                               # Sicherungspunkt, Lauf, neuer Sicherungspunkt, Commit, Tag, Push
./console exp:migration:rollback <run-id> --apply                     # zurück zum Sicherungspunkt vor dem Lauf
```

Führen Sie die Befehle im Wurzelverzeichnis der Installation als der Benutzer aus, unter dem der Webserver läuft (als
root mit `--allow-root-user`). Jeder Befehl, der schreibt, ist ein Probelauf, bis er `--apply` bekommt.

## Was eine Migration ist und wann man sie nimmt

Eine Migration ist eine Datei namens `YYYYMMDDHHMMSS_name.<typ>`, zum Beispiel `20261031120000_add_news_class.php`.
Ihre ID ist der Dateiname ohne den Typ. Sie enthält eine Änderung und, wo es geht, den Weg zurück. Ist sie auf einer
Installation gelaufen, hält die Aufzeichnung das fest, und sie läuft dort nie wieder.

| Sie wollen ... | Nehmen Sie |
|---|---|
| Datenbank und Einstellungen einer Installation auf ein neues **Kernel-Release** bringen | [`exp:upgrade`](aktualisieren-mit-der-konsole.md) (die Upgrade-Schritte des Kernels) |
| eine Tabelle, Spalte oder Standardzeile mit einer **Erweiterung** ausliefern, bei jedem Upgrade angewendet | [einen Upgrade-Schritt der Erweiterung](upgrade-schritte-fuer-erweiterungen.md) oder eine Migration in `extension/<name>/migrations/` |
| **eine Site** ändern: eine Klasse, Inhalte, eine Rolle, eine Einstellung, eine Tabelle der Site, nacheinander auf Entwicklung, Staging und Produktion | eine Migration in `migrations/` der Site |
| eine ganze Site mit ihren Inhalten auf eine andere Installation kopieren | ein [Site-Paket](pakete.md) |

Migrationen und `exp:upgrade` teilen sich die Aufzeichnungstabelle `exp_migration`, die Sperre der Installation und die
Sicherungspunkte, sodass ein Upgrade und ein Migrationslauf nie gleichzeitig schreiben und `exp:upgrade:captions` die
Sicherungspunkte beider zeigt. Die Upgrade-Schritte sind ebenfalls Migrationen, der Quelle `kernel`:
`exp:migration:status` listet sie zuerst, und `exp:migration:migrate --apply` führt die ausstehenden der Stufen
`database` und `scripts` vor den Migrationen der Site aus; die Stufen `code`, `libraries` und `finish` bleiben bei
`exp:upgrade` ([Die Schritte unter den Migrationen](aktualisieren-mit-der-konsole.md#die-schritte-unter-den-migrationen)).
Eine Migration, die die Änderung eines Schritts braucht, nennt den Schritt in `requires`.

## Der Lebenszyklus

1. **Schreiben**: `exp:migration:generate --skeleton <name> --apply` schreibt eine leere PHP-Migration mit Kopf,
   `plan()`, `up()` und `down()`; füllen Sie sie aus ([PHP-Migrationen](migrationen-php.md)).
2. **Prüfen**: `exp:migration:check` prüft die Dateien ohne Datenbank: die Namen, den Kopf, die Reihenfolge, die
   PHP-Syntax und die Klasse. Es ist die Prüfung für CI.
3. **Probelauf**: `exp:migration:migrate` (oder `exp:migration:dry-run`) zeigt jede ausstehende Migration mit dem, was
   ihr `plan()` ändern würde. `exp:migration:dry-run --simulate --diff` führt sie in einer Transaktion aus, die
   zurückgerollt wird, und zeigt, was sich in der Datenbank geändert hat, auf PostgreSQL und SQLite.
4. **Ausführen**: `exp:migration:migrate --apply`. Unter der Sperre der Installation, als der Benutzer des Laufs:
   - die Code-Bäume (`settings/`, `extension/`, `design/`) werden auf nicht committete Änderungen geprüft;
   - ein **Sicherungspunkt** der Art `migration-pre` wird angelegt (oder der neueste wiederverwendet, solange sich
     seitdem nichts geändert hat);
   - die ausstehenden Migrationen laufen der Reihe nach, jede in einer Transaktion, wo die Datenbank eine hat;
   - der Lauf hält beim ersten Fehler an, und der Sicherungspunkt von vorher wird wiederhergestellt;
   - nach einem erfolgreichen Lauf wird ein Sicherungspunkt der Art `migration-post` angelegt;
   - die Statusdatei und das Laufprotokoll werden im Migrations-Repository **committet**, der Commit wird
     **getaggt** und **gepusht**.
5. **Ansehen**: `exp:migration:status` zeigt jede Migration mit ihrem Zustand, die letzten Läufe und was wartet.
6. **Zurück**, wenn nötig: `exp:migration:rollback <run-id>` stellt den Sicherungspunkt vor einem Lauf wieder her,
   `exp:migration:rollback <migration-id>` führt das `down()` einer Migration aus.

## Wo Migrationsdateien liegen

| Quelle | Ordner | Einstellung |
|---|---|---|
| `site` | `migrations/` der Installation | `migration.ini [MigrationSettings] DirectoryList[]` |
| `extension:<name>` | `extension/<name>/migrations/` jeder **aktiven** Erweiterung | `ExtensionDirectory` |
| `package:<name>` | `migrations/` jedes installierten Pakets unter `var/<storage>/packages/` | `ExtensionDirectory` |

Die Migrationen eines Pakets laufen auch bei seiner Installation, und der Kickstarter führt jede ausstehende Migration
aus, nachdem er die Websites angelegt hat: [Pakete, Deploys und lange Läufe](migrationen-pakete-deploy-und-lange-laeufe.md).

Nur die Dateien direkt in einem Ordner sind Migrationen; Unterordner (die Statusdateien und Laufprotokolle) werden
nicht durchsucht. Eine Datei, deren Name nicht mit `YYYYMMDDHHMMSS_` beginnt (eine README, eine `.gitkeep`), wird
übersprungen. `[MigrationSettings] Enabled=disabled` findet gar keine. Jede Einstellung steht in
[der Referenz zu migration.ini](../beispiele/ini-einstellungen/migration/README.md).

## Typen

Der Typ ist die Endung der Datei: `php`, `yml`, `sql`, `<engine>.sql` (`mysql`, `pgsql` oder `postgresql`, `sqlite`,
`oracle`), `mongodb.json` und `ini`. Eine Migration wird in **PHP**, in **YAML**, in **SQL** (eine Datei je Engine oder
eine portable Datei) oder als **INI**-Einstellungen geschrieben; SQL- und INI-Migrationen laufen als YAML-Migration
ihrer Schritte ([Migrationen: SQL und Einstellungen](migrationen-sql-und-einstellungen.md)). Migrationen jedes Typs
können einander voraussetzen und sich im selben Lauf Werte weitergeben (Referenzen).

## YAML-Migrationen

Eine YAML-Migration ist eine Liste von Schritten; jeder nennt seinen Schritttyp, und die Engine führt sie der Reihe
nach aus, mit Platzhaltern, Bedingungen und Schleifen dazwischen:

```yaml
# description: The languages of the news, checked before the import
# author: Site team
steps:
    - type: reference
      set:
          news_languages: [ eng-US, ger-DE ]
          news_parent: "${idmap.node.news}"
    - foreach: "${ref.news_languages}"
      as: language
      steps:
          - type: assert
            that: { value: "${loop.language}", matches: "/^[a-z]{3}-[A-Z]{2}$/" }
            message: "${loop.language} is not a language code"
    - type: assert
      if: { environment: production }
      that: { value: "${ref.news_parent}", not_equals: 2 }
      message: production names its own news node in its id map
```

- Der Kopf besteht aus denselben `#`-Kommentarzeilen wie bei jeder Migration. `exp:migration:generate --skeleton
  <Name> --type=yml` schreibt eine leere.
- `type` nennt einen Schritttyp aus `migration.ini [StepSettings] StepTypeList`. Der Kernel hat `reference`
  (Referenzen setzen), `assert` (die Migration scheitern lassen, wenn eine Bedingung falsch ist), `content`, `location`
  und `content_version` (Objekte, ihre Übersetzungen, Orte und Versionen), `content_class` und `content_class_group`
  (Klassen mit ihren Attributen), `user`, `user_group` und `role` (Konten, Gruppen, Rollen mit Richtlinien,
  Einschränkungen und Zuweisungen), `section`, `language`, `object_state_group`, `tag` (eztags), `url_alias`
  (Aliase und Platzhalter), `trash`, `file` und `image` (Dateien des Migrationsordners), `cache`, `search` und `autoload`,
  `layout`, `layout_block` und `layout_rule` (Exponential Layouts), `http`, `mail` und `command` (Konsolenbefehle der
  Positivliste) ([Schritte von YAML-Migrationen](migrationen-yaml-schritte.md)); eine Erweiterung fügt ihre eigenen
  genauso hinzu. Die übrigen Schlüssel eines Schritts werden gegen das Schema seines Typs geprüft, bevor irgendetwas
  läuft.
- `${ref.<Name>}` ist eine Referenz (gesetzt von `references:` eines früheren Schritts, von einem `reference`-Schritt
  oder von einer früheren PHP- oder YAML-Migration des Laufs), `${loop.<Name>}` eine Schleifenvariable,
  `${idmap.<Art>.<Name>}` eine ID der ID-Zuordnung `migrations/idmap/<Umgebung>.json`, `${run.environment}` die
  Umgebung des Laufs.
- `if:` überspringt einen Schritt, `environments:` und `engines:` beschränken ihn; `foreach:` und `while:` (mit einem
  Höchstwert) wiederholen Schritte.
- `down:` listet die Schritte, die die Migration für `exp:migration:rollback <id>` zurücknehmen.
- YAML wird mit symfony/yaml gelesen, das composer.json verlangt; eine Installation, die es woanders hält, nennt den
  Ordner in `[YamlSettings] LibraryPath`.

[Das Dateiformat](../spezifikationen/6.0/migrationsdateiformat.md#die-yaml-migration) nennt jede Regel, und
[das Tutorial](migrationen-tutorial.md#12-eine-yaml-migration) führt eine aus.

## Reihenfolge

Migrationen laufen in der Reihenfolge ihrer IDs, also zuerst nach dem Zeitstempel, dann nach dem Namen. Eine
Migration, die in ihrer Kopfzeile `requires:` andere nennt, läuft nach ihnen, wohin deren Zeitstempel sie auch stellen.
Eine Anforderung `extension:<name>` verlangt, dass diese Erweiterung aktiv ist. Eine zweimal gefundene ID (die zuerst
gefundene zählt: Site vor Erweiterungen vor Paketen), eine Anforderung, die keine Migration nennt, und ein Zyklus sind
Fehler, die `exp:migration:check` und jeder Lauf melden; solange einer da ist, läuft nichts.

## Der Kopf

Die Kommentarzeilen am Anfang der Datei sind ihr Kopf, eine Zeile `schlüssel: wert` je Angabe:

```php
<?php
// description: The folder News below the content root
// requires: 20261031120000_news_note_table
// environments: staging, production
// maintenance: no
// author: Site team
```

Schlüssel: `id`, `description`, `requires`, `environments`, `engines`, `maintenance`, `author`. Ein unbekannter
Schlüssel ist ein Fehler, damit ein vertippter nicht still durchgeht.
[Das Dateiformat](../spezifikationen/6.0/migrationsdateiformat.md) nennt jede Regel.

## Umgebungen

Die Umgebung einer Installation ist `site.ini [AdminEnvironment] Level` (mit `Level=auto` die Umgebungsvariable
`EXP_ENV`). Eine Migration, deren Kopf `environments: staging, production` nennt, läuft nur dort;
`environments: !development` läuft überall außer auf Entwicklung. `--environment=<name>` nimmt für einen Befehl eine
andere. Eine Migration, die nicht für die Umgebung oder nicht für die Datenbank-Engine (`engines:`) ist, hat den
Zustand `not-needed`.

## Die Aufzeichnung

| Wo | Was |
|---|---|
| Tabelle `exp_migration` | eine Zeile je Migration (Quelle + ID): Zustand, Lauf, Prüfsumme der Datei, Dauer, der Sicherungspunkt davor, der Commit danach; auch die Aufzeichnung von `exp:upgrade` (Quelle `kernel`) |
| Tabelle `exp_migration_run` | eine Zeile je Lauf: Zustand, Benutzer, Umgebung, die Sicherungspunkte davor und danach, Commit, Tag, Push-Zustand |
| `migrations/status/<umgebung>.json` | die Aufzeichnung, gespiegelt ins Migrations-Repository (keine Benutzer, keine Meldungen) |
| `migrations/runs/<run-id>.log` | eine Zeile je Ereignis eines Laufs, bereinigt von Geheimnissen und E-Mail-Adressen |

Der Upgrade-Schritt `6.0.15-migration-tables` legt beide Tabellen an; auf einer Installation einer Vorabversion von
6.0.15 kopiert der Schritt `6.0.15-upgrade-record-table` danach die Zeilen ihrer älteren Tabelle
`expupgrade_step` und löscht diese Tabelle; `./console exp:upgrade --apply` führt beide aus. Bis dahin wird nichts
gelesen oder geschrieben, und der Status sagt das.

Zustände: `done`, `pending`, `not-needed`, `failed`, `unknown`. Eine als erledigt aufgezeichnete Migration, deren
Datei sich seitdem geändert hat, wird abgelehnt, und der Lauf mit ihr, es sei denn, der Lauf bekommt
`--force --reason=<text>`: Dann wird die neue Prüfsumme aufgezeichnet, die Änderung im Audit festgehalten, und die
Migration läuft nicht noch einmal.

## Transaktionen und Engines

| Datenbank | Eine gescheiterte Migration wird zurückgenommen | Simulation (`--simulate`) |
|---|---|---|
| PostgreSQL, SQLite | vollständig (`transactions: full`) | ja |
| MySQL/MariaDB, Oracle | nur ihre Daten; eine Schemaänderung wird sofort committet (`transactions: data`) | nein: der Plan wird mit einer Warnung gezeigt |
| MongoDB | gar nicht (`transactions: none`) | nein |

Was eine Transaktion nicht zurücknehmen kann, deckt der nach einem Fehler wiederhergestellte Sicherungspunkt ab.

## Die Laufsperre und der Benutzer

Ein Lauf, der schreibt, hält die Sperre der Installation `var/<site>/upgrade.lock` (gemeinsam mit `exp:upgrade`), mit
einer Halter-Datei daneben, die sagt, wer sie hält. Ein zweiter Lauf endet sofort; `exp:migration:status` nennt den
Halter. Ein Probelauf nimmt keine Sperre. Ein Lauf läuft als der angemeldete Benutzer, auf der Konsole als
`[RunSettings] RunAsUser` (`admin`), nach Login; `--login=<login>` nimmt einen anderen.

## Voreinstellungen und jeder Schalter

| Voreinstellung | Für einen Lauf aus | In `migration.ini` aus |
|---|---|---|
| Sicherungspunkt vor dem Lauf | `--no-caption` | `[CaptionSettings] Before=disabled` |
| Sicherungspunkt nach einem erfolgreichen Lauf | `--no-caption` | `After=disabled` |
| einen unveränderten Sicherungspunkt wiederverwenden | | `ReuseUnchanged=disabled` |
| nach einem Fehler den Sicherungspunkt von vorher wiederherstellen | `--no-restore` | `RestoreOnFailure=disabled` |
| nicht committete Änderungen in den Code-Bäumen ablehnen | `--include-uncommitted` (im Sicherungspunkt aufbewahrt) | `CodeTreeList[]` |
| Statusdatei und Laufprotokoll committen | `--no-commit` (dann auch kein Tag und kein Push) | `[RepositorySettings] Commit=disabled` |
| den Commit taggen | `--no-tag` | `Tag=disabled` |
| Commit und Tag pushen | `--no-push` | `Push=disabled` |
| beim ersten Fehler anhalten | | `[RunSettings] StopOnFailure=disabled` |
| jede ausstehende Migration in einem Lauf | `--max-migrations=<n>`, `--max-time=<sekunden>` (der Rest verschoben) | `MaxMigrations`, `MaxTime` |
| keine Pause, die normale Priorität | `--throttle=<sekunden>`, `--nice=<n>` | `Throttle`, `Nice` |
| die Migrationen eines Pakets laufen bei seiner Installation | | `[InstallSettings] RunOnPackageInstall=disabled` |
| ein Deploy führt keine Migration aus | `exp:velocity deploy --migrate` | `velocity.ini [DeploySettings] Migrate=enabled` |

## Jeder Befehl

| Befehl | Was er tut |
|---|---|
| [`exp:migration:status`](../beispiele/befehle/expmigrationstatus.md) | jede Migration mit ihrem Zustand, die Verwaisten, die letzten Läufe, die wartenden Pushes, die Sperre |
| [`exp:migration:migrate`](../beispiele/befehle/expmigrationmigrate.md) | der Probelauf, und mit `--apply` der Lauf |
| [`exp:migration:dry-run`](../beispiele/befehle/expmigrationdryrun.md) | der Plan; `--simulate --diff` läuft in einer Transaktion, die zurückgerollt wird |
| [`exp:migration:rollback`](../beispiele/befehle/expmigrationrollback.md) | ein Lauf, eine Migration oder (für Fachleute) ein beliebiger Sicherungspunkt |
| [`exp:migration:resume`](../beispiele/befehle/expmigrationresume.md) | ein neuer Lauf dessen, was ein gescheiterter oder unterbrochener Lauf offen ließ |
| [`exp:migration:caption`](../beispiele/befehle/expmigrationcaption.md) | jetzt einen Sicherungspunkt, oder die Liste |
| [`exp:migration:push`](../beispiele/befehle/expmigrationpush.md) | die Läufe, deren Push scheiterte, noch einmal gepusht |
| [`exp:migration:check`](../beispiele/befehle/expmigrationcheck.md) | die Prüfung für CI |
| [`exp:migration:generate`](../beispiele/befehle/expmigrationgenerate.md) | eine neue PHP- oder (`--type=yml`) YAML-Migration aus dem Gerüst |

Alle nehmen `--format=json` (die Maschinenausgabe `exp-migration/1`) und enden mit den Exit-Codes von `exp:upgrade`:
0 in Ordnung, 1 gescheitert (oder ausstehend mit `exp:migration:status --fail-on-pending`), 2 Bedienfehler, 3 die
Datenbank ist nicht erreichbar, 4 interner Fehler.

## In der Administration

**Setup > Migrationen** (das Modul `migration`) zeigt und führt dieselben Migrationen aus:

| Seite | Was sie tut | Richtlinie |
|---|---|---|
| Stand (`/migration/status`) | jede Migration mit ihrem Stand, die Sperre, die letzten Läufe, wie `exp:migration:status` | `migration/read` |
| Verlauf (`/migration/history`) | jeder Lauf mit Sicherungspunkten, Commit, Tag und Push; fortsetzen und zurücknehmen | `migration/read` |
| Die Seite eines Laufs (`/migration/log/<run-id>`) | sein Eintrag, seine Migrationen, das Ende seines Protokolls und sein Fortschritt, solange er läuft | `migration/read` |
| Probelauf (`/migration/dryrun`) | der Plan, wie `exp:migration:dry-run`; **Simulieren** wie `--simulate --diff` | `migration/read`, `migration/run` zum Simulieren |
| Ausführen (`/migration/run`) | die ausstehenden Migrationen, einzeln wählbar, mit den Schaltern der Tabelle oben und `--force --reason` | `migration/run` |
| Rücknahme (`/migration/rollback`) | ein Lauf, eine Migration oder (für Fachleute) ein beliebiger Sicherungspunkt | `migration/rollback` |

In einer neuen Installation hat nur die Rolle Administrator diese Richtlinien. Ein Lauf, eine Fortsetzung, eine
Simulation und eine Rücknahme fragen zuerst nach „Identität bestätigen“ (`site.ini [ReauthGroup_system]`). Das
Dashboard hat für Benutzer mit `migration/read` einen Block **Migrationen**, die Website-Seitenleiste (expwt) ein
schreibgeschütztes Panel Migrationen.

Ein dort gestarteter Lauf, eine Fortsetzung oder eine Rücknahme läuft im Hintergrund: Die Seite startet den
Konsolenbefehl (`exp:migration:migrate --apply`, `exp:migration:resume --apply` oder `exp:migration:rollback --apply`)
als eigenen Prozess unter dem angemeldeten Benutzer und öffnet die Seite des Laufs, die ihn verfolgt. Der Lauf geht
weiter, wenn die Seite geschlossen wird. Die Seite benennt den Lauf mit `--run-id=<run-id>`, sodass Lauf, Eintrag und
Protokoll diese Kennung haben, und verfolgt ihn über die Datei aus `--progress=<datei>`, an die der Lauf je Ereignis
eine JSON-Zeile anhängt. Beide Optionen lassen sich auch auf der Konsole nutzen. Die Dateien dieser Läufe liegen in
`var/<site>/migration/jobs/`; `migration.ini [AdminSettings]` nennt das PHP-Kommandozeilenprogramm (`PhpCliPath`), wie
viele Läufe ihre Dateien behalten (`JobHistory`, 50) und wie viel eines Laufprotokolls eine Seite zeigt (`LogBytes`,
256 KB). Immer nur ein Lauf: Solange einer im Gang ist oder ein anderer Lauf oder ein Upgrade die Sperre hält, lehnt
die Seite einen neuen ab.

Die Seiten: [Migrationen in der Administration](../hilfe/migrationen/in-der-administration.md),
[Stand und Verlauf](../hilfe/migrationen/status-und-verlauf.md),
[Ausführen, Probelauf und Rücknahme](../hilfe/migrationen/ausfuehren-probelauf-und-rueckgaengig.md).

## Noch nicht in 6.0.15

Der Editor und die Generatoren in der Administration
(Generatoren außer dem Gerüst), parallele Worker, die Schritte, die Code- und Designdateien ändern, und ein
websiteweites Aufzeichnungsfenster sind geplant und nicht Teil dieses Releases.

## Verwandte Seiten

- [Migrationen: der Einstieg](../hilfe/migrationen/README.md), [Alle Rezepte](../hilfe/migrationen/rezepte/README.md),
  [Häufige Fragen](../hilfe/migrationen/faq.md)
- In der Administration, Schritt für Schritt mit Bildschirmfotos: [Ein Rundgang durch Setup > Migrationen](../hilfe/migrationen/rundgang-durch-setup-migrationen.md),
  [Der Migrationseditor](../hilfe/migrationen/der-migrationseditor.md), [Änderungen aufzeichnen](../hilfe/migrationen/aufzeichnung.md),
  [Eine Gegenstelle anlegen](../hilfe/migrationen/eine-gegenstelle-anlegen-schritt-fuer-schritt.md), [Ausliefern](../hilfe/migrationen/ausliefern-schritt-fuer-schritt.md)
- [Das Tutorial](migrationen-tutorial.md), [PHP-Migrationen](migrationen-php.md),
  [Pakete, Deploys und lange Läufe](migrationen-pakete-deploy-und-lange-laeufe.md),
  [Deploy auf entfernte Installationen](migrationen-remote-deploy.md),
  [Engines und Leistung](migrationen-engines-und-leistung.md),
  [Zurücknehmen und Sicherungspunkte](migrationen-rueckgaengig-und-captions.md),
  [Das Migrations-Repository](migrationen-repositories.md)
- [In der Administration](../hilfe/migrationen/in-der-administration.md), [Befehle](../hilfe/migrationen/befehle.md),
  [Einstellungen](../hilfe/migrationen/einstellungen.md),
  [Richtlinien, Ansichten und Ereignisse](../hilfe/migrationen/richtlinien-ansichten-und-ereignisse.md)
- [Das Dateiformat](../spezifikationen/6.0/migrationsdateiformat.md), [Die Funktion](../funktionen/6.0/migrationen.md)
- [Wenn eine Migration scheitert](../hilfe/migrationen/wenn-eine-migration-scheitert.md),
  [Fehlermeldungen](migrationen-fehlerbehebung.md)
- [Migrationen oder Umzug einer Site](../hilfe/migrationen/migrationen-oder-umzug-einer-site.md)
- [migration.ini](../beispiele/ini-einstellungen/migration/README.md), [der Cronjob-Teil migrationpush](../beispiele/cronjobs/migrationpush.md)
