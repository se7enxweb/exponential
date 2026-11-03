---
title: "Wenn eine Migration scheitert: Fehlerbehebung nach Symptom"
audience: developer, admin, software-engineer, hosting, dba
category: migrations
order: 30
summary: Fehlerbehebung bei Migrationen nach dem, was Sie sehen - ein gescheiterter Lauf, ein abgelehnter Lauf, eine Migration, die nicht läuft oder dort läuft, wo sie nicht soll, eine belegte Sperre, nichts wiederhergestellt, ein wartender Push, ein Lauf in der Administration, der nicht startet, Änderungen, die die Site nicht zeigt - mit der Ursache, dem, was schon automatisch geschehen ist, und dem, was zu tun ist.
keywords: Migration gescheitert, Migrationsfehler, Fehlerbehebung, Problem, Hilfe, abgelehnt, Sperre belegt, Prüfsumme, Sicherungspunkt, Wiederherstellung gescheitert, Push gescheitert, ausstehend, verwaist, fortsetzen, migration failed, migration error, troubleshooting, FAIL, exit 1, exit 2, refused, lock held, upgrade.lock, checksum, changed after it was applied, --force --reason, not-needed, blocked, deferred, orphan, pending, no migrations found, exp_migration does not exist, restore failed, caption failed, push failed, pending push, proc_open, PhpCliPath, running, resume, cache, Velocity, problem, help
featured: true
type: help
---
# Wenn eine Migration scheitert: Fehlerbehebung nach Symptom

Suchen Sie in der Liste unten, was Sie sehen. Jeder Eintrag sagt, was geschehen ist, was Exponential schon von selbst
getan hat und was zu tun ist. Den genauen Text einer Meldung finden Sie unter
[Fehlermeldungen](../../anleitungen/migrationen-fehlerbehebung.md); die beiden Seiten verweisen aufeinander.

Zuerst drei Befehle, die die meisten Fragen beantworten:

```bash
./console exp:migration:status            # jede Migration und ihr Zustand, die letzten Läufe, die Sperre, wartende Pushes
./console exp:migration:migrate           # der Probelauf: was der nächste Lauf täte und warum etwas nicht liefe
./console exp:migration:check             # die Dateien: Namen, Kopf, Reihenfolge, PHP, ohne Datenbank
```

## Inhalt

1. [Ein Lauf endete mit FAIL](#ein-lauf-endete-mit-fail)
2. [Ein Lauf wird vor dem Start abgelehnt](#ein-lauf-wird-vor-dem-start-abgelehnt)
3. [Eine Migration läuft nicht](#eine-migration-läuft-nicht)
4. [Eine Migration lief, wo sie nicht sollte](#eine-migration-lief-wo-sie-nicht-sollte)
5. [Sicherungspunkte und Wiederherstellung](#sicherungspunkte-und-wiederherstellung)
6. [Das Repository](#das-repository)
7. [In der Administration](#in-der-administration)
8. [Nach einem Lauf](#nach-einem-lauf)
9. [Wann Sie innehalten und fragen sollten](#wann-sie-innehalten-und-fragen-sollten)
10. [Verwandte Seiten](#verwandte-seiten)

## Ein Lauf endete mit FAIL

### Eine Migration ist `failed`, und der Lauf meldet `Restore: restored`

**Was geschehen ist.** Die Migration hat eine Ausnahme geworfen. Ihre Transaktion wurde zurückgerollt (vollständig auf
PostgreSQL und SQLite, nur die Daten auf MySQL/MariaDB und Oracle, nichts auf MongoDB), der Lauf hielt an, der
gescheiterte Zustand wurde in einem Sicherungspunkt der Art `migration-failure` festgehalten, und der Sicherungspunkt von
vor dem Lauf wurde wiederhergestellt. Die Aufzeichnung des gescheiterten Laufs wurde committet und gepusht. Die
Installation ist so, wie sie vor dem Lauf war.

**Was zu tun ist.** Lesen Sie die Meldung auf der Seite des Laufs (Setup > Migrationen > Verlauf) oder in
`migrations/runs/<run-id>.log`. Beheben Sie die Ursache: in der Migration (ein neuer Commit; die gescheiterte Migration
ist nie gelaufen, darf also geändert werden) oder in der Installation (ein fehlendes Elternobjekt, eine Einstellung).
Dann starten Sie erneut oder führen `./console exp:migration:resume --apply` aus.
[Sicherungspunkte und Rücknahme in der Praxis](rezepte/sicherungspunkte-und-ruecknahme-in-der-praxis.md).

### Eine Migration ist `failed`, und nichts wurde wiederhergestellt

**Was geschehen ist.** Der Lauf hatte keinen Sicherungspunkt (`--no-caption`, `[CaptionSettings] Before=disabled` oder
der Sicherungspunkt konnte nicht genommen werden), oder er lief mit `--no-restore`. Auf MySQL/MariaDB und Oracle bleiben
die Schemaänderungen der gescheiterten Migration, auf MongoDB bleibt alles, was sie geschrieben hat.

**Was zu tun ist.** Der Bericht listet die Schritte für die Wiederherstellung von Hand. Ohne eigene Sicherung machen Sie
die halbe Änderung mit einer neuen Migration oder von Hand rückgängig, bevor Sie erneut starten. Lassen Sie beim nächsten
Mal die Sicherungspunkte eingeschaltet.

### `FAIL: ... changed after it was applied (checksum ... -> ...)`

**Was geschehen ist.** Die Datei einer Migration, die schon gelaufen ist, wurde bearbeitet. Eine gelaufene Migration
läuft nie wieder, also lehnt der Lauf sie ab, statt Installationen zu hinterlassen, die sich unterscheiden.

**Was zu tun ist.** Machen Sie die Bearbeitung rückgängig und schreiben Sie die Änderung in eine neue Migration. Wenn die
Bearbeitung nichts daran ändert, was die Migration getan hat (ein Kommentar, ein Tippfehler):
`./console exp:migration:migrate --apply --force --reason="a comment only"`. Die neue Prüfsumme wird aufgezeichnet und
die Annahme protokolliert. [Eine Migration im Code-Review prüfen](rezepte/eine-migration-pruefen-im-code-review.md).

### Eine Migration ist `unknown`: `skipped, its state cannot be told`

**Was geschehen ist.** Ihr `detect()` hat eine Ausnahme geworfen, zum Beispiel weil es eine Tabelle liest, die die
Migration selbst anlegt.

**Was zu tun ist.** Lassen Sie `detect()` `null` zurückgeben, wenn es den Zustand nicht erkennen kann, und lassen Sie es
nie etwas brauchen, das die Migration erst anlegt ([PHP-Migrationen](../../anleitungen/migrationen-php.md)).

## Ein Lauf wird vor dem Start abgelehnt

### `another run holds the lock: ...`

**Was geschehen ist.** Ein `exp:upgrade` oder ein anderer Migrationslauf schreibt gerade. Nur einer darf zur Zeit
schreiben; die Sperre ist die Datei `var/<site>/upgrade.lock`, und ihre Halter-Datei sagt, wer sie hält.

**Was zu tun ist.** Warten. `./console exp:migration:status` nennt den Halter und seit wann. Eine Sperre, deren Prozess
beendet ist, wird als veraltet gemeldet und vom nächsten Lauf übernommen; nichts muss von Hand entfernt werden.

### `... change(s) that are not committed in the code trees (...)` (Exit 2)

**Was geschehen ist.** `settings/`, `extension/` oder `design/` haben Änderungen, die Git nicht kennt. Ein
Sicherungspunkt hält den Commit des Codes fest, nicht den Code selbst; eine Wiederherstellung könnte diese Änderungen
also nicht zurückbringen.

**Was zu tun ist.** Committen Sie sie zuerst. Oder starten Sie mit `--include-uncommitted`, das sie im Sicherungspunkt
festhält.

### `the table exp_migration does not exist yet`

**Was geschehen ist.** Die Aufzeichnungstabellen kommen mit dem Upgrade-Schritt `6.0.15-migration-tables`, der noch
nicht gelaufen ist.

**Was zu tun ist.** `./console exp:upgrade --apply` (oder `./console exp:upgrade:database --apply`).

### `the user to run as does not exist: [RunSettings] RunAsUser=admin`

**Was zu tun ist.** Setzen Sie `RunAsUser` in `settings/override/migration.ini.append.php` auf den Login eines
vorhandenen Administrators, oder übergeben Sie `--login=<login>` ([Einstellungen](einstellungen.md)).

### `the migration <id> requires ..., which does not exist` / `a cycle in requires` / `defined twice`

**Was geschehen ist.** Die Reihenfolge lässt sich nicht bilden: Ein `requires:` nennt eine ID, die keine Datei hat, zwei
Migrationen warten aufeinander, oder eine ID kommt in zwei Quellen vor. Solange das so ist, läuft nichts.

**Was zu tun ist.** `./console exp:migration:check` listet jedes Problem. Korrigieren Sie den Kopf, lösen Sie den
Zyklus auf, oder benennen Sie die zweite Datei um (nie eine, die schon gelaufen ist)
([Teamarbeit mit Git](rezepte/teamarbeit-mit-git.md)).

## Eine Migration läuft nicht

### `no migrations found`

Prüfen Sie Ordner und Namen: Dateien direkt in `migrations/` (nicht in einem Unterordner), benannt
`YYYYMMDDHHMMSS_name.php`. Die Migrationen einer Erweiterung werden nur gefunden, solange die Erweiterung aktiv ist, die
eines Pakets erst, wenn es installiert ist. `[MigrationSettings] Enabled=disabled` findet gar keine.
`exp:migration:check` gibt die durchsuchten Ordner aus.

### Sie ist `not-needed`

Der Kopf grenzt sie ein: `environments:` enthält die Umgebung dieser Installation nicht (`site.ini [AdminEnvironment]
Level` oder `EXP_ENV` mit `Level=auto`), oder `engines:` enthält diese Datenbank nicht. Oder ihr `detect()` sagt, dass
die Änderung schon da ist. Prüfen Sie mit `./console exp:migration:status --environment=<name>`
([Migrationen je Umgebung](rezepte/migrationen-je-umgebung.md)).

### Sie ist `blocked` oder `waits for <id>`

Sie braucht eine Migration, die gescheitert oder noch nicht gelaufen ist. Beheben Sie zuerst diese. Mit `--only` oder
`--path` nehmen Sie die Voraussetzung in die Auswahl auf.

### Sie ist `deferred`

Sie ist einer der Upgrade-Schritte des Kernels der Stufe `code`, `libraries` oder `finish`, die ein Migrationslauf
`exp:upgrade` überlässt; die Warnung nennt den Befehl (`./console exp:upgrade:code --apply` ...). Eine Migration, die sie
braucht, wartet ([Upgrade-Schritt oder Migration?](rezepte/upgrade-schritt-oder-migration.md)).

### `no statements for <engine>` (eine SQL-Migration)

Die Migration hat keine Datei für die Engine dieser Installation, und ihre portable `.sql`-Datei (falls es sie gibt)
gilt nicht für sie (MongoDB nimmt eine `.mongodb.json`). Ergänzen Sie die Datei der Engine, oder `missing: skip` bei einem
Schritt `sql`, der dort nichts zu tun hat ([Migrationen: SQL und Einstellungen](../../anleitungen/migrationen-sql-und-einstellungen.md)).

### Die PHP-Datei wird abgelehnt: keine Klasse, zwei Klassen, eine doppelt deklarierte Klasse

Jede Datei deklariert genau eine nicht abstrakte Klasse, die `\expMigrationPhp` erweitert, in einem Namensraum, mit
einem Namen, den keine andere Datei nutzt. `exp:migration:generate --skeleton` schreibt sie so
([PHP-Migrationen](../../anleitungen/migrationen-php.md)).

### `...::definition() has the name of a method of expMigrationPhp that a migration cannot declare`

Die Klasse deklariert eine Methode, die PHP beim Laden der Datei ablehnen würde: eine Hilfsmethode mit dem Namen einer
der eigenen Methoden der Basisklasse (`definition()`, `file()`, `id()`, `source()`, `checksum()`, `state()` ...), oder
`plan()`, `up()`, `down()`, `detect()` als private, statisch, mit einem Typ für `$context` oder mit mehr Pflichtparametern.
`exp:migration:check` und jeder Lauf lesen die Klasse, bevor sie sie laden, und nennen die Methode; die anderen
Migrationen werden wie sonst geprüft und ausgeführt. Benennen Sie die Hilfsmethode um, oder deklarieren Sie die Methode
so, wie [PHP-Migrationen](../../anleitungen/migrationen-php.md#die-methoden) es zeigt.

## Eine Migration lief, wo sie nicht sollte

### Sie lief auf der Produktion, war aber nur für die Entwicklung gedacht

Ihr Kopf hatte keine Zeile `environments:`, oder die Umgebung der Installation ist nicht gesetzt (`Level` leer: Jede
Migration ohne `environments:` läuft). Schreiben Sie eine **neue** Migration, die die Änderung dort zurücknimmt, wo sie
nicht hingehört, und setzen Sie `Level` auf jeder Installation. [Migrationen je Umgebung](rezepte/migrationen-je-umgebung.md).

### Sie lief zweimal, oder nach einer Wiederherstellung erneut

Eine Wiederherstellung setzt die Aufzeichnung mit der Datenbank zurück; die Migrationen nach dem Sicherungspunkt stehen
also wieder aus und laufen beim nächsten Lauf. Das ist so gewollt. Eine Migration muss auf jeder Installation sicher
laufen, die sie nicht aufgezeichnet hat: Legen Sie nur an, was fehlt, und finden Sie Dinge über die Remote-ID.

## Sicherungspunkte und Wiederherstellung

### `no caption could be taken, the run goes on without a way back`

Der Sicherungspunkt ist gescheitert (kein Dump-Werkzeug für die Engine, eine volle Platte, ein Fehler in git_manager),
und der Lauf ging weiter. Testen Sie mit `./console exp:migration:caption --apply`, beheben Sie die Ursache, oder machen
Sie vor dem nächsten Lauf eine eigene Sicherung.

### `no built-in restore for oracle: restore it by hand`

Sicherungspunkte auf Oracle brauchen einen eigenen Export, und die Wiederherstellung geschieht von Hand (die Schritte
werden ausgegeben) ([Hinweise zu den Datenbanken](rezepte/hinweise-zu-den-datenbanken.md)).

### `the restore of the caption <id> failed`

Der gescheiterte Zustand steckt im Sicherungspunkt `migration-failure`. Stellen Sie von Hand wieder her:
`./console exp:migration:rollback <run-id> --apply` (oder `./console exp:upgrade:rollback <caption> --apply`), danach
`./console exp:upgrade:finish --apply`.

## Das Repository

### `1 run(s) wait for their push`

Der Push ist gescheitert (Netzwerk, abgelaufener Schlüssel, ein Remote, das ablehnte). Commit und Tag sind lokal.
Beheben Sie das Remote oder seine Zugangsdaten, dann `./console exp:migration:push --apply`, oder lassen Sie den
Cronjob-Teil `migrationpush` das erledigen ([Tags pushen](rezepte/tags-pushen.md)).

### `git_manager is not active` / `is not a git repository` / `is on the branch ...`

Die Aufzeichnung wurde nicht committet. Installieren und aktivieren Sie git_manager, machen Sie den Ordner zu einem
Repository, oder checken Sie den Branch aus, den `[RepositorySettings] Branch` nennt; auf einer Installation ohne Git
setzen Sie `Commit=disabled`.

### `the tag <name> exists already`

Tags werden nie verschoben. Behalten Sie `<run-id>` in `[RepositorySettings] TagPattern`.

## In der Administration

### Die Schaltfläche Ausführen wird abgelehnt, oder die Seite sagt, der Lauf könne nicht starten

Ein anderer Lauf läuft, oder `exp:upgrade` hält die Sperre; oder der Server kann keinen Hintergrundprozess starten: Die
Seite braucht `proc_open` und ein PHP-Kommandozeilenprogramm (`migration.ini [AdminSettings] PhpCliPath`). Führen Sie
den Lauf dann auf der Konsole aus
([Ausführen, Probelauf und Rücknahme](ausfuehren-probelauf-und-rueckgaengig.md#wenn-ein-lauf-nicht-starten-kann)).

### „Identität bestätigen“ fragt immer wieder

Jeder Lauf, jede Fortsetzung, Simulation und Rücknahme fragt einmal; das ist die Gruppe `site.ini [ReauthGroup_system]`
([Identität bestätigen](../../anleitungen/identitaet-bestaetigen.md)).

### Ein Lauf bleibt für immer `running`

Sein Prozess wurde beendet (ein Neustart des Servers, ein Zeitlimit des Prozessmanagers). `./console
exp:migration:resume` findet ihn und schließt ihn mit `--apply` als gescheitert ab und führt aus, was noch aussteht, in
einem neuen Lauf.

### Die Seite sagt „Sie haben keinen Zugriff“

Der Rolle fehlt `migration/read` (zum Ansehen), `migration/run` (zum Ausführen oder Simulieren) oder `migration/rollback`
([Richtlinien, Ansichten und Ereignisse](richtlinien-ansichten-und-ereignisse.md)).

## Nach einem Lauf

### Die Site zeigt die Änderung nicht

Eine neue Klasse, Einstellung oder ein neues Template braucht ein Deploy wie jede Code-Änderung: `./console exp:velocity
deploy` (Caches, PHP-FPM, Velocity). Nach einem Lauf, der Kernel-Schritte angewendet hat, oder nach einer
Wiederherstellung: `./console exp:upgrade:finish --apply`.

### Eine Migration wird als `orphan` gelistet

Sie ist als erledigt aufgezeichnet, aber ihre Datei fehlt (gelöscht oder umbenannt). Legen Sie die Datei unter ihrem
alten Namen zurück; eine gelaufene Migration wird nie umbenannt. Die Zeile schadet nicht.

### Inhalte wurden vom falschen Benutzer angelegt, oder ein Recht fehlt

Ein Lauf läuft in der Administration unter dem angemeldeten Benutzer und auf der Konsole als `[RunSettings] RunAsUser`
(oder `--login`). Führen Sie ihn als einen Benutzer mit den Rechten aus, die die Migration braucht.

## Wann Sie innehalten und fragen sollten

Halten Sie inne und fragen Sie den Autor der Migration (oder Ihren Hosting-Anbieter), bevor Sie erneut starten, wenn:
die Wiederherstellung gescheitert ist; die Engine Oracle oder MongoDB ist und eine Migration auf halbem Weg gescheitert
ist; eine Migration Inhalte geändert hat, die Redakteure seitdem bearbeitet haben; oder `exp:upgrade:verify` nach einer
Wiederherstellung Abweichungen meldet. Schicken Sie die Lauf-ID, das Laufprotokoll und die Ausgabe von
`./console exp:migration:status --format=json`; keines davon enthält Geheimnisse oder E-Mail-Adressen.

## Verwandte Seiten

- [Migrationen: der Einstieg](README.md), [Alle Rezepte](rezepte/README.md), [Häufige Fragen](faq.md)
- Wenn eine Auslieferung an Gegenstellen scheitert: [Ausliefern, Schritt für Schritt](ausliefern-schritt-fuer-schritt.md#wenn-etwas-schiefgeht); eine Aufzeichnung oder der Editor: [Änderungen aufzeichnen](aufzeichnung.md#wenn-etwas-schiefgeht), [Der Migrationseditor](der-migrationseditor.md#wenn-etwas-schiefgeht)
- [Die Anleitung](../../anleitungen/migrationen.md), [das Tutorial](../../anleitungen/migrationen-tutorial.md),
  [PHP-Migrationen](../../anleitungen/migrationen-php.md),
  [Zurücknehmen und Sicherungspunkte](../../anleitungen/migrationen-rueckgaengig-und-captions.md),
  [Das Migrations-Repository](../../anleitungen/migrationen-repositories.md)
- [In der Administration](in-der-administration.md), [Befehle](befehle.md), [Einstellungen](einstellungen.md),
  [Richtlinien, Ansichten und Ereignisse](richtlinien-ansichten-und-ereignisse.md)
- [Das Dateiformat](../../spezifikationen/6.0/migrationsdateiformat.md),
  [Die Funktion](../../funktionen/6.0/migrationen.md)
- [Fehlermeldungen](../../anleitungen/migrationen-fehlerbehebung.md)
- [Migrationen oder Umzug einer Site](migrationen-oder-umzug-einer-site.md)
- [Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
