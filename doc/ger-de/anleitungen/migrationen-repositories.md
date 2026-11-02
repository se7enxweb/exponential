# Migrationen: das Migrations-Repository, Commit, Tag und Push

Jeder Migrationslauf hinterlässt eine Aufzeichnung in Git: eine Statusdatei, die sagt, was eine Umgebung angewendet hat,
und das Protokoll des Laufs. Nach jedem Lauf, auch nach einem gescheiterten, werden beide committet, der Commit wird
getaggt und gepusht. Diese Seite erklärt, wo dieses Repository ist, wie es eingerichtet wird, wie ein Commit und ein Tag
aussehen und was passiert, wenn ein Push scheitert.

[Anleitungen](README.md) · [Migrationen: der Einstieg](../hilfe/migrationen/README.md) · [Migrationen](migrationen.md) · Siehe auch: [Zurücknehmen und Sicherungspunkte](migrationen-rueckgaengig-und-captions.md),
[Fehlerbehebung](migrationen-fehlerbehebung.md), [migration.ini \[RepositorySettings\]](../beispiele/ini-einstellungen/migration/RepositorySettings.md)

## Kurz

- Die Aufzeichnung geht in den Ordner `migrations/` des eigenen Git-Checkouts der Installation (`Mode=site`, die
  Voreinstellung) oder in ein eigenes Git-Repository (`Mode=separate`).
- Ein Lauf ist eine Revision: ein Commit und ein Tag, `migrations/<umgebung>/<run-id>`.
- Ein gescheiterter Push bleibt lokal; der Cronjob-Teil `migrationpush` und `./console exp:migration:push --apply`
  pushen ihn erneut.
- Ohne git_manager wird nichts committet, und der Bericht sagt das.

## Was geschrieben wird

| Datei | Inhalt |
|---|---|
| `migrations/status/<umgebung>.json` | die Zeilen von `exp_migration` und die letzten Läufe dieser Umgebung (Schema `exp-migration-status/1`) |
| `migrations/runs/<run-id>.log` | eine Zeile je Ereignis des Laufs: `<UTC-Zeit> <Stufe> <Text>` |

Keine der beiden Dateien enthält ein Geheimnis oder personenbezogene Daten: Der Status lässt weg, wer eine Migration
ausgeführt hat, und jede Meldung; eine Protokollzeile wird vor dem Schreiben bereinigt von geheimen Werten, Zugangsdaten
in URLs, Paaren `password=...`, Bearer-Tokens und E-Mail-Adressen. Die Statusdatei wird ganz, sortiert und nur mit den
Zeiten der Aufzeichnung geschrieben, sodass erneutes Schreiben ohne Änderung dieselben Bytes und keinen Diff ergibt.
Eine Installation ohne Umgebung schreibt `default.json`.

Eine Statusdatei aus dem [Tutorial](migrationen-tutorial.md), gekürzt:

```json
{
    "schema": "exp-migration-status/1",
    "environment": "development",
    "engine": "sqlite",
    "updated_at": "2026-11-01T02:30:20Z",
    "summary": { "done": 2, "total": 2 },
    "migrations": [
        {
            "source": "site",
            "id": "20261031120000_news_note_table",
            "state": "done",
            "file": "migrations/20261031120000_news_note_table.php",
            "checksum": "5b5c92d15691663414cea9eca82de62e44f0fc1e890d0d2c8939e7728615060d",
            "stage": "migration",
            "version": "",
            "run_id": "20261031-193020-1356d6",
            "started_at": "2026-11-01T02:30:20Z",
            "finished_at": "2026-11-01T02:30:20Z",
            "duration_ms": 3,
            "caption_id": "",
            "revision": ""
        }
    ],
    "runs": [
        {
            "run_id": "20261031-193020-1356d6",
            "state": "done",
            "caption_pre": "",
            "caption_post": "",
            "commit": "",
            "tag": "",
            "push_state": "",
            "log": "runs/20261031-193020-1356d6.log"
        }
    ]
}
```

Das JSON-Schema der Datei ist `share/migration/schema/exp-migration-status-1.json`.

## Die zwei Modi

```ini
# settings/override/migration.ini.append.php
[RepositorySettings]
Mode=site
Path=migrations
Remote=origin
Branch=
```

| Modus | `Path` ist | Commits gehen in |
|---|---|---|
| `site` (Voreinstellung) | ein Ordner des eigenen Checkouts der Installation | das Repository der Installation; nur die zwei Dateien des Laufs werden committet (`git commit -- <dateien>`), was sonst vorgemerkt ist, bleibt, wie es ist |
| `separate` | ein eigenes Git-Repository (relativ zur Wurzel oder absolut) | dieses Repository; die Historie der Site bleibt frei von Laufaufzeichnungen |

Mit `Mode=site` liegen die Migrationsdateien und die Aufzeichnung ihrer Läufe nebeneinander in `migrations/`; der
Locator liest nur die Dateien direkt im Ordner, nie `status/` oder `runs/`.

`Branch` ist der Branch, auf dem ein Lauf committet; leer heißt der Branch, auf dem das Repository steht. In ein
Repository auf einem anderen Branch, oder auf keinem, wird nicht committet, und der Bericht sagt warum. Einen Checkout,
der einem anderen Systembenutzer als dem Prozess gehört, lässt der Lauf in Ruhe, weil die Dateien, die Git schriebe, dem
falschen Benutzer gehören würden.

## Der Commit und der Tag

Nach dem Lauf werden die zwei Dateien hinzugefügt und mit einer Nachricht wie dieser committet:

```text
Updated: Migration run 20261031-193020-1356d6 in development: 2 applied

- applied site:20261031120000_news_note_table
- applied site:20261031120100_news_folder
```

Ein gescheiterter Lauf sagt `... in development failed` und nennt die Wiederherstellung. Hat das Repository keine
`user.email`, wird der Commit als `Exponential migrations` angelegt. Danach wird der Commit mit `[RepositorySettings]
TagPattern` getaggt (annotiert), standardmäßig `migrations/<environment>/<run-id>`; die Platzhalter sind
`<environment>` (`default`, wenn die Installation keine nennt), `<run-id>` und `<date>` (`YYYYMMDD`). **Ein
vorhandener Tag wird nie verschoben oder ersetzt**: Der Commit bleibt dann ohne Tag, und der Bericht sagt das. Die
Zeile des Laufs hält `commit_id`, `tag_name` und `push_state` fest, und jede angewendete Migration bekommt den Commit
als ihre `revision`.

Die Ausgabe eines Laufs endet mit einer Zeile dazu:

```text
Repository: commit 1a2b3c4d5e6f, tag migrations/production/20261031-120000-a1b2c3, push pushed
```

## Der Push

Branch und Tag werden zu `[RepositorySettings] Remote` gepusht (standardmäßig `origin`): ein beliebiges Git-Remote, auf
GitHub, GitLab oder einem eigenen Server. Die Zugangsdaten sind die des Remotes, wie git_manager und der Systembenutzer
sie sehen (ein SSH-Schlüssel, ein Credential-Helper); die Migrationen speichern nichts. Jeder Git-Befehl läuft über den
Runner von git_manager: keine Shell, ein Zeitlimit.

| `push_state` | Bedeutet |
|---|---|
| `pushed` | Commit und Tag sind auf dem Remote |
| `pending` | der Push ist gescheitert; Commit und Tag sind lokal und warten auf den nächsten Versuch |
| `disabled` | `Commit=disabled`, `Push=disabled` oder `--no-push` / `--no-commit` |
| `no-remote` | `Remote` ist leer oder kein Remote des Repositorys |
| `none` | es wurde nichts committet |

### Ein gescheiterter Push

Ein Netzwerkfehler, ein abgelaufener Schlüssel, ein Remote, das ablehnt: Der Lauf scheitert deswegen nicht. Der
Push-Zustand ist `pending`, `exp:migration:status` führt den Lauf unter den wartenden Pushes und warnt
`1 run(s) wait for their push: ./console exp:migration:push --apply`.

```bash
./console exp:migration:push              # die wartenden Läufe
./console exp:migration:push --apply      # sie pushen, ältester zuerst (--limit, Voreinstellung 20)
php runcronjobs.php migrationpush         # dasselbe, aus cron
```

Der Cronjob-Teil `migrationpush` steht in keinem Standardteil: Fügen Sie ihn einem Teil hinzu, den die Site ausführt,
oder führen Sie ihn allein aus ([der Cronjob-Teil](../beispiele/cronjobs/migrationpush.md)). Solange kein Lauf wartet,
tut er nichts.

## Abschalten

| Für einen Lauf | Für die Installation |
|---|---|
| `--no-commit`: kein Commit, kein Tag, kein Push | `[RepositorySettings] Commit=disabled` |
| `--no-tag`: Commit und Push, kein Tag | `Tag=disabled` |
| `--no-push`: Commit und Tag, kein Push | `Push=disabled` |

Eine Entwicklungsinstallation läuft oft mit `Push=disabled`, Produktion mit allen dreien eingeschaltet.

## Die Aufzeichnung anderer Umgebungen lesen

Weil jede Umgebung ihre eigene `status/<umgebung>.json` pusht, sagt ein Klon des Repositorys, was jede Umgebung
angewendet hat, ohne Zugriff auf ihre Datenbank:

```bash
git fetch --tags
git tag -l 'migrations/production/*' --sort=version:refname | tail -5
git show origin/main:migrations/status/production.json | head -20
```

## Die Migrationen in ein eigenes Repository verschieben

`exp:migration:repo:split` trennt den Migrationsordner des Checkouts der Installation mit seiner Historie in ein eigenes
Repository ab: Jeder Commit, der den Ordner geändert hat, wird zu einem Commit des Ordners allein, mit demselben Autor,
Committer, Datum und derselben Nachricht. Es braucht nur git (kein `git subtree`) und ändert weder die Einstellungen
noch die Dateien der Website.

```text
$ ./console exp:migration:repo:split
Exponential migrations: split the migrations repository, dry run (--apply makes it)
Folder migrations of /srv/site: 2 commit(s) changed it
Would make the branch migrations-split
Then: Make the new repository from the branch: git init --initial-branch=main <the new repository> && git -C <the new repository> pull /srv/site migrations-split
Then: Add its remote and push it: git -C <the new repository> remote add origin <url> && git -C <the new repository> push -u origin main
Then: Point the installation at it in settings/override/migration.ini.append.php: [RepositorySettings] Mode=separate, Path=<the new repository> (and [MigrationSettings] DirectoryList[]=<the new repository> if the migrations move there too)
Then: Take the folder out of the site's checkout when the installation reads the new one: git rm -r --cached migrations and add it to .gitignore
PASS

$ ./console exp:migration:repo:split --to=/srv/site-migrations --remote=git@github.com:example/site-migrations.git --apply
Exponential migrations: split the migrations repository
Folder migrations of /srv/site: 2 commit(s) changed it
Branch migrations-split at 3140e7bc1ec4, new repository /srv/site-migrations
Then: Push it when it looks right: git -C /srv/site-migrations push -u origin main
...
PASS

$ git -C /srv/site-migrations log --format='%h %ad %s' --date=short
3140e7b 2026-10-04 Added: the news class migration
afa0a06 2026-10-02 Added: the news section migration
```

- `--directory` nennt den Ordner (Vorgabe der erste Eintrag von `[MigrationSettings] DirectoryList`).
- Die Historie kommt in den Branch `--branch` des Checkouts (Vorgabe `migrations-split`); ein vorhandener Branch wird
  nie verschoben. Später erneut abtrennen mit einem anderen Branch-Namen: Dieselbe Historie ergibt dieselben Commits.
- `--to` legt das neue Repository in einem neuen oder leeren Ordner an, mit der Historie als Branch `main`; `--remote`
  fügt das Remote `origin` hinzu. Gepusht wird nichts: Der erste Push ist Ihrer.
- Danach die Installation auf den getrennten Modus umstellen ([Die zwei Modi](#die-zwei-modi)) und den Ordner aus dem
  Checkout der Website nehmen.

## Verwandte Seiten

- [Migrationen: der Einstieg](../hilfe/migrationen/README.md), [Alle Rezepte](../hilfe/migrationen/rezepte/README.md),
  [Häufige Fragen](../hilfe/migrationen/faq.md)
- [Die Anleitung](migrationen.md), [das Tutorial](migrationen-tutorial.md), [PHP-Migrationen](migrationen-php.md),
  [Zurücknehmen und Sicherungspunkte](migrationen-rueckgaengig-und-captions.md)
- [In der Administration](../hilfe/migrationen/in-der-administration.md), [Befehle](../hilfe/migrationen/befehle.md),
  [Einstellungen](../hilfe/migrationen/einstellungen.md),
  [Richtlinien, Ansichten und Ereignisse](../hilfe/migrationen/richtlinien-ansichten-und-ereignisse.md)
- [Das Dateiformat](../spezifikationen/6.0/migrationsdateiformat.md), [Die Funktion](../funktionen/6.0/migrationen.md)
- [Wenn eine Migration scheitert](../hilfe/migrationen/wenn-eine-migration-scheitert.md),
  [Fehlermeldungen](migrationen-fehlerbehebung.md)
- [Migrationen oder Umzug einer Site](../hilfe/migrationen/migrationen-oder-umzug-einer-site.md)
- [`exp:migration:push`](../beispiele/befehle/expmigrationpush.md), [migration.ini \[RepositorySettings\]](../beispiele/ini-einstellungen/migration/RepositorySettings.md)
- [`exp:migration:repo:split`](../beispiele/befehle/expmigrationreposplit.md)
