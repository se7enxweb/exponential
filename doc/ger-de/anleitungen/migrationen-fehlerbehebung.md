# Migrationen: Fehlerbehebung

Diese Seite nennt die Meldungen, die die Migrationsbefehle ausgeben, was jede bedeutet und was zu tun ist. Die
Meldungen sind Englisch. Meldungen, die mit `FAIL:` beginnen, sind Fehler (der Exit-Code ist nicht 0); Zeilen mit
`WARN` sind Warnungen, der Befehl ist trotzdem bestanden. Jeder Befehl kann seinen Bericht auch als JSON ausgeben
(`--format=json`), mit denselben Texten in `errors` und `warnings`.

Sie suchen nach dem, was Sie sehen, statt nach der Meldung (ein gescheiterter Lauf, eine Migration, die nicht läuft,
ein Push, der wartet, ein Lauf in der Administration, der nicht startet)?
[Wenn eine Migration scheitert: Fehlerbehebung nach Symptom](../hilfe/migrationen/wenn-eine-migration-scheitert.md).

[Anleitungen](README.md) · [Migrationen: der Einstieg](../hilfe/migrationen/README.md) · [Migrationen](migrationen.md) · Siehe auch: [Zurücknehmen und Sicherungspunkte](migrationen-rueckgaengig-und-captions.md),
[Das Migrations-Repository](migrationen-repositories.md), [PHP-Migrationen](migrationen-php.md)

## Exit-Codes

| Code | Bedeutet |
|---|---|
| 0 | erledigt, oder nichts zu tun |
| 1 | eine Migration ist gescheitert, eine Prüfung hat ein Problem gefunden, die Sperre war belegt, eine geänderte Datei wurde abgelehnt, oder etwas steht aus mit `exp:migration:status --fail-on-pending` |
| 2 | Bedienung: eine Option ist falsch oder fehlt (`--apply` mit `--dry-run`, `--force` ohne `--reason`, ein unbekanntes Ziel), oder nicht committeter Code hat einen Lauf abgelehnt |
| 3 | die Datenbank ist nicht erreichbar |
| 4 | interner Fehler |

## Vor einem Lauf

**`the table exp_migration does not exist yet: run ./console exp:upgrade --apply first (step 6.0.15-migration-tables)`**
Die Aufzeichnungstabellen legt ein Upgrade-Schritt an. Führen Sie `./console exp:upgrade --apply` aus (oder nur die
Datenbankstufe, `./console exp:upgrade:database --apply`). `exp:migration:status` zeigt die Migrationen trotzdem und
warnt stattdessen.

**`no migrations found`** (Warnung)
Keine Datei `YYYYMMDDHHMMSS_name.<typ>` in den durchsuchten Ordnern. Sehen Sie in `exp:migration:check` nach, das die
durchsuchten Ordner nennt; `[MigrationSettings] Enabled=disabled` findet keine; die Migrationen einer Erweiterung
werden nur gefunden, solange die Erweiterung aktiv ist.

**`no migrations directory: [MigrationSettings] DirectoryList in migration.ini names none`**
`exp:migration:generate` hat keinen Ordner zum Schreiben. Setzen Sie `DirectoryList[]=migrations` in
`settings/override/migration.ini.append.php`.

**`<file> is not a migration file name (YYYYMMDDHHMMSS_name.type, ...)`, `<file> does not start with a valid date and time (YYYYMMDDHHMMSS)`, `<file> has the unknown type ...`, `unknown header key ...`, `the header key ... is given twice`, `maintenance: ... is not yes or no`**
Die Prüfung der Dateinamen und Köpfe ([das Dateiformat](../spezifikationen/6.0/migrationsdateiformat.md)). Beheben
Sie die Datei; `exp:migration:check` zeigt jedes Problem auf einmal.

**`the migration <id> requires <dep>, which does not exist`**, **`... which does not run in the environment <env>`**, **`the migration <id> requires the extension <name>, which is not active`**
Eine Anforderung im Kopf nennt eine ID, die keine Datei hat, eine Migration, die auf andere Umgebungen beschränkt ist,
oder eine Erweiterung, die nicht aktiv ist. Korrigieren Sie den Kopf oder aktivieren Sie die Erweiterung.

**`the migrations <ids> wait for each other (a cycle in requires ...)`**, **`the migration <id> is defined twice (...)`**
Zwei Migrationen verlangen einander, oder eine ID gibt es in zwei Quellen (die zuerst gefundene wird verwendet: Site
vor Erweiterungen vor Paketen). Solange einer davon da ist, läuft nichts. Lösen Sie den Zyklus auf oder benennen Sie die
zweite Datei um.

**`... requires ..., which is pending and not selected: add it to --only or --path`**
`--only` oder `--path` hat eine Migration ausgewählt, deren Anforderung noch nicht gelaufen ist. Nehmen Sie die
Anforderung in die Auswahl auf oder führen Sie den Lauf ohne Auswahl aus.

**`no statements for <engine>; missing: skip lets the step pass`**
Eine SQL-Migration oder ein Schritt `sql` hat nichts für die Engine dieser Installation: Ergänzen Sie die Datei
(`<id>.<engine>.sql`, `<id>.mongodb.json`) oder den Schlüssel der Engine, oder `missing: skip`, wenn dort nichts zu tun
ist ([SQL und Einstellungen](migrationen-sql-und-einstellungen.md)).

**INI: `line N: [Block] does not name its file`**
Ein Block einer INI-Migration nennt die Datei, die er ändert: `[site.ini/Block]` oder
`[siteaccess:<name> site.ini/Block]` ([SQL und Einstellungen](migrationen-sql-und-einstellungen.md#einstellungen-ini-migrationen-und-der-schritttyp-settings)).

**PHP: `no class: a PHP migration declares one class that extends expMigrationPhp`, `more than one class extends expMigrationPhp`, `the class ... is declared by ... too`, `PHP syntax: ...`**
Jede PHP-Datei deklariert genau eine nicht abstrakte Klasse, die `\expMigrationPhp` erweitert, mit einem Namen, den
keine andere Datei deklariert; nutzen Sie einen Namensraum ([PHP-Migrationen](migrationen-php.md#die-datei-und-die-klasse)).

**`no namespace: two migrations of different sites or extensions could declare the same class`** (Warnung)
Legen Sie die Klasse in einen Namensraum, wie es das Gerüst tut.

**`<n> change(s) that are not committed in the code trees (...): commit them first, or run with --include-uncommitted to keep them in the caption`** (Exit-Code 2)
`settings/`, `extension/` oder `design/` (`[CaptionSettings] CodeTreeList[]`) haben Änderungen, die Git nicht kennt.
Ein Sicherungspunkt bewahrt den Commit des Codes auf, nicht den Code. Committen Sie die Änderungen oder führen Sie den
Lauf mit `--include-uncommitted` aus.

**`the user to run as does not exist: [RunSettings] RunAsUser=admin (migration.ini)`**
Die Konsole läuft als `RunAsUser`, gefunden über das Login. Setzen Sie es auf einen vorhandenen Administrator, oder
geben Sie `--login=<login>` an.

## Die Sperre

**`another run holds the lock: migration run <run-id> by <user> (pid <pid> on <host>) since <time>`**
Ein `exp:upgrade` oder ein anderer Migrationslauf schreibt gerade. Warten Sie; `./console exp:migration:status` zeigt
den Halter.

**Eine verwaiste Sperre: `..., stale: its process has ended`**
Die Sperre ist eine Dateisperre, die der Prozess hält, ein abgebrochener Lauf gibt sie also sofort frei.
Zurückbleiben kann die Halter-Datei `var/<site>/upgrade.lock.holder`; der Status meldet den Halter dann als verwaist,
und der nächste Lauf übernimmt und schreibt seine eigene. Von Hand muss nichts entfernt werden.

## Während eines Laufs

**`<id> changed after it was applied (checksum ... -> ...); a run with --force --reason=<text> accepts the change`**
Die Datei einer gelaufenen Migration wurde seitdem bearbeitet. Nehmen Sie die Bearbeitung zurück und schreiben Sie die
Änderung in eine neue Migration, oder nehmen Sie sie an, wenn sie nicht ändert, was die Migration getan hat (ein
Kommentar, ein Tippfehler): `./console exp:migration:migrate --apply --force --reason="a comment only"`. Die Migration
läuft nicht noch einmal; die neue Prüfsumme wird aufgezeichnet und die Annahme im Audit festgehalten.

**`--force needs a --reason=<text>: why the changed migrations are accepted`**, **`--reason goes with --force`** (Exit-Code 2)
Die beiden Optionen gehören zusammen.

**Eine Migration `failed` mit einer Meldung**
Die Ausnahme, die `up()` geworfen hat. Der Lauf hat angehalten, die Transaktion der Migration wurde zurückgerollt und
der Sicherungspunkt von vor dem Lauf wiederhergestellt (`Restore: restored`). Beheben Sie die Ursache und führen Sie den
Lauf erneut aus, oder `./console exp:migration:resume --apply`.

**`<id>: skipped, its state cannot be told: ...`** (Warnung, Zustand `unknown`)
`detect()` hat geworfen. Die Migration läuft nicht; beheben Sie `detect()` (es darf nichts brauchen, was die Migration
erst anlegt).

**`blocked` / `waits for <id>`**
Mit `[RunSettings] StopOnFailure=disabled` läuft eine Migration nicht, die eine gescheiterte verlangt.

**Teilweise Änderungen auf MySQL/MariaDB, Oracle und MongoDB**
Die Schemaänderungen einer gescheiterten Migration bleiben auf MySQL/MariaDB und Oracle (sie werden sofort committet),
und auf MongoDB bleibt jede Änderung: Dort ist die Wiederherstellung des Sicherungspunkts der Weg zurück. Mit
`--no-restore` oder ohne Sicherungspunkt nehmen Sie die Änderungen von Hand zurück oder stellen den Sicherungspunkt
wieder her: `./console exp:migration:rollback <run-id> --apply`.

## Sicherungspunkte und Wiederherstellung

**`no caption could be taken, the run goes on without a way back: ...`** (Warnung)
Der Sicherungspunkt ist gescheitert (kein Sicherungswerkzeug für die Engine, eine volle Platte, ein scheiternder
git_manager). Der Lauf ging weiter. Beheben Sie die Ursache (`./console exp:migration:caption --apply` legt zum Testen
einen an) oder halten Sie an und machen Sie selbst eine Sicherung.

**`no caption before the run (--no-caption or [CaptionSettings] Before=disabled): a failure cannot be restored automatically`** (Warnung)
Wie es dasteht. Bei einem Fehler nennt der Bericht dann die Schritte zur Wiederherstellung von Hand.

**`no caption was taken before the run: nothing was restored; restore by hand:`** gefolgt von nummerierten Schritten
Ein Fehler ohne Sicherungspunkt. Folgen Sie den Schritten: Wartungsmodus ein, die Datenbank aus Ihrem eigenen Abzug
wiederherstellen, `settings/override` und `var/` zurücklegen, `./console exp:velocity deploy`,
`./console exp:upgrade:verify --after`, Wartungsmodus aus.

**`no built-in restore for oracle: restore it by hand`**
Sicherungspunkte auf Oracle haben keine eingebaute Wiederherstellung. Der Bericht nennt die Schritte (ein `impdp` Ihres
eigenen Exports).

**`the restore of the caption <id> failed: ...`**
Die automatische Wiederherstellung ist nicht durchgelaufen. Der gescheiterte Zustand steckt im Sicherungspunkt
`migration-failure`. Stellen Sie von Hand wieder her: `./console exp:migration:rollback <run-id> --apply` (oder
`exp:upgrade:rollback <caption> --apply`), dann `./console exp:upgrade:finish --apply`.

**`the caption after the run could not be taken: ...`** (Warnung)
Der Lauf ist bestanden; nur der Sicherungspunkt danach ist gescheitert. Der nächste Lauf legt einen neuen
Sicherungspunkt vorher an.

## Das Repository

Das sind Warnungen: Der Lauf selbst ist für sich bestanden oder gescheitert.

**`git_manager is not active: the record of the run is not committed`**
Commits gehen über git_manager. Installieren Sie ihn, oder setzen Sie `[RepositorySettings] Commit=disabled`.

**`<path> is not a git repository ([RepositorySettings] Mode=...)`**
Mit `Mode=site` ist die Installation kein Git-Checkout; mit `Mode=separate` ist der Ordner `Path` kein Repository.
Führen Sie dort `git init` aus, oder setzen Sie `Commit=disabled`.

**`<path> is on the branch <x>, not <y> ([RepositorySettings] Branch)`**, **`... is on no branch`**
Checken Sie den Branch aus, oder leeren Sie `Branch`.

**`git add failed for ... (ignored by .gitignore?)`**
`migrations/status/` oder `migrations/runs/` werden ignoriert. Entfernen Sie sie aus `.gitignore`.

**`the tag <name> exists already: it is never moved, the commit stays untagged`**
Eine Lauf-ID wurde wiederverwendet, oder das `TagPattern` ergibt zweimal denselben Namen (etwa nur `<date>`). Nehmen
Sie `<run-id>` auf.

**`the push to origin failed (exit 128): the commit stays local and is pushed again by the cronjob part migrationpush`**
Beheben Sie das Remote oder seine Zugangsdaten, dann `./console exp:migration:push --apply`, oder überlassen Sie es dem
Cronjob-Teil `migrationpush`.

**`not pushed: the repository has no remote origin`**
Fügen Sie das Remote hinzu, oder setzen Sie `[RepositorySettings] Remote` auf den richtigen Namen (leer für keinen Push).

## Nach einem Lauf

**Velocity oder PHP-FPM sehen eine Änderung nicht**
Eine Migration, die eine Klasse, eine Einstellung oder ein Template hinzufügt, braucht eine Bereitstellung wie jede
Code-Änderung: `./console exp:velocity deploy` (Caches, PHP-FPM, Velocity), oder `./console exp:upgrade:finish --apply`
nach einer Wiederherstellung.

**`<id> (site) is recorded as done, but its file is not found any more`** (Warnung, `orphan`)
Die Aufzeichnung hat eine Migration, deren Datei gelöscht oder umbenannt wurde. Benennen Sie sie zurück; eine
gelaufene Migration sollte nicht umbenannt werden. Die Zeile bleibt in der Aufzeichnung und schadet nicht.

**Ein Lauf, dessen Zeile noch `running` sagt**
Der Prozess wurde beendet. `./console exp:migration:resume` findet ihn (kein Prozess hält die Sperre mehr) und schließt
ihn mit `--apply` als gescheitert ab und führt aus, was noch aussteht, in einem neuen Lauf.

## Verwandte Seiten

- [Migrationen: der Einstieg](../hilfe/migrationen/README.md), [Alle Rezepte](../hilfe/migrationen/rezepte/README.md),
  [Häufige Fragen](../hilfe/migrationen/faq.md)
- [Die Anleitung](migrationen.md), [das Tutorial](migrationen-tutorial.md), [PHP-Migrationen](migrationen-php.md),
  [Zurücknehmen und Sicherungspunkte](migrationen-rueckgaengig-und-captions.md),
  [Das Migrations-Repository](migrationen-repositories.md)
- [In der Administration](../hilfe/migrationen/in-der-administration.md), [Befehle](../hilfe/migrationen/befehle.md),
  [Einstellungen](../hilfe/migrationen/einstellungen.md),
  [Richtlinien, Ansichten und Ereignisse](../hilfe/migrationen/richtlinien-ansichten-und-ereignisse.md)
- [Das Dateiformat](../spezifikationen/6.0/migrationsdateiformat.md), [Die Funktion](../funktionen/6.0/migrationen.md)
- [Wenn eine Migration scheitert](../hilfe/migrationen/wenn-eine-migration-scheitert.md)
- [Migrationen oder Umzug einer Site](../hilfe/migrationen/migrationen-oder-umzug-einer-site.md)
