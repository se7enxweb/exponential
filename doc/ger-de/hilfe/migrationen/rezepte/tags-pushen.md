---
title: "Die Aufzeichnung eines Laufs committen, taggen und pushen"
audience: developer, admin, software-engineer, hosting
category: migrations
order: 21
summary: Das Migrations-Repository so einrichten, dass Statusdatei und Protokoll jedes Laufs committet, als migrations/<umgebung>/<run-id> getaggt und gepusht werden; zwischen dem Repository der Site und einem eigenen wählen, Gescheitertes erneut pushen und die Tags lesen.
keywords: Migration, Migrationen, Git, Commit, Tag, Push, Migrations-Repository, Cronjob, migration, migrations, schema change, database change, git, commit, tag, push, migrations repository, TagPattern, Mode=site, Mode=separate, push_state, exp:migration:push, migrationpush, cronjob, remote, origin
type: help
---
# Die Aufzeichnung eines Laufs committen, taggen und pushen

Nach jedem Lauf mit `--apply`, auch nach gescheiterten, schreibt die Migrations-Engine die Statusdatei der Umgebung und
das Protokoll des Laufs, committet beide, taggt den Commit und pusht Commit und Tag. So bekommt jede Umgebung einen
Verlauf in Git, den jeder mit einem Klon lesen kann. Diese Seite richtet das ein und zeigt, was zu tun ist, wenn ein
Push scheitert.

## Was Sie am Ende haben

- `[RepositorySettings]` für Ihre Installation gesetzt, im Repository der Site oder in einem eigenen;
- Läufe, die mit `Repository: commit ..., tag ..., push pushed` enden;
- einen Cronjob oder Befehl, der pusht, was nicht gepusht werden konnte.

## Bevor Sie beginnen

- git_manager ist installiert und aktiv: Commits laufen über ihn. Ohne ihn meldet ein Lauf
  `git_manager is not active: the record of the run is not committed` und committet nichts.
- Der Systembenutzer, der Migrationen ausführt, darf zum Remote pushen (ein SSH-Schlüssel oder ein Credential-Helper).
  Die Migrationen speichern nichts davon.

## Schritte

### 1. Das Repository wählen

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[RepositorySettings]
Mode=site
Path=migrations
Remote=origin
Branch=
*/ ?>
```

| `Mode` | `Path` | Nehmen Sie es, wenn |
|---|---|---|
| `site` (Voreinstellung) | ein Ordner im eigenen Checkout der Installation | das Repository der Site die Laufaufzeichnungen neben den Migrationsdateien tragen darf; nur die zwei Dateien eines Laufs werden committet, alles andere Vorgemerkte bleibt |
| `separate` | ein eigenes Git-Repository (relativ zur Wurzel oder absolut) | der Verlauf der Site frei von Laufaufzeichnungen bleiben soll, oder mehrere Installationen an ein Repository berichten |

Ein leeres `Branch` heißt: der Branch, auf dem das Repository steht; in ein Repository auf einem anderen oder keinem
Branch wird nicht committet, und der Bericht sagt warum.

### 2. Die Tag-Namen wählen

```ini
[RepositorySettings]
Tag=enabled
TagPattern=migrations/<environment>/<run-id>
```

Die Platzhalter sind `<environment>` (`default`, wenn die Installation keine nennt), `<run-id>` und `<date>`
(`YYYYMMDD`). Behalten Sie `<run-id>` im Muster: **ein vorhandener Tag wird nie verschoben oder ersetzt**. Ein Muster,
das zweimal denselben Namen ergibt (nur `<date>`), lässt den zweiten Commit ungetaggt, mit der Warnung
`the tag <name> exists already: it is never moved, the commit stays untagged`.

### 3. Ausführen und die Repository-Zeile lesen

```bash
./console exp:migration:migrate --apply
```

```text
Repository: commit 4e1f0a9c2b7d, tag migrations/production/20261101-120000-b2c3d4, push pushed
```

Die Commit-Nachricht listet, was der Lauf getan hat:

```text
Updated: Migration run 20261101-120000-b2c3d4 in production: 1 applied

- applied site:20261101090000_news_note_priority
```

### 4. Gescheiterte Pushes erneut pushen

Ein gescheiterter Push lässt den Lauf nicht scheitern. Der `push_state` des Laufs ist `pending`, und
`exp:migration:status` warnt `1 run(s) wait for their push: ./console exp:migration:push --apply`.

```bash
./console exp:migration:push
./console exp:migration:push --apply --limit=20
```

```text
Exponential migrations: push
  pending  20261101-120000-b2c3d4
Pushed 1 of 1 tried, 0 still pending
```

| `push_state` | Bedeutet |
|---|---|
| `pushed` | Commit und Tag sind auf dem Remote |
| `pending` | der Push ist gescheitert; Commit und Tag sind lokal und warten |
| `disabled` | `Commit=disabled`, `Push=disabled`, `--no-push` oder `--no-commit` |
| `no-remote` | `Remote` ist leer oder kein Remote des Repositorys |
| `none` | es wurde nichts committet |

### 5. Den Cron pushen lassen

Der Cronjob-Teil `migrationpush` gehört zu keinem voreingestellten Teil. Fügen Sie ihn einem Teil hinzu, den die Site
ausführt, oder führen Sie ihn allein aus:

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CronjobPart-migrationpush]
Scripts[]=migrationpush.php
*/ ?>
```

```bash
php runcronjobs.php migrationpush
```

Solange kein Lauf wartet, tut er nichts.

### 6. Die Tags lesen

```bash
git fetch --tags
git tag -l 'migrations/production/*' --sort=version:refname | tail -5
git show migrations/production/20261101-120000-b2c3d4 --stat
```

Sortieren Sie mit `--sort=version:refname` (oder `sort -V`), nie nur nach dem Namen.

## Teile abschalten

| Für einen Lauf | Für die Installation |
|---|---|
| `--no-commit` (auch kein Tag und kein Push) | `Commit=disabled` |
| `--no-tag` | `Tag=disabled` |
| `--no-push` | `Push=disabled` |

Eine Entwicklungsinstallation läuft oft mit `Push=disabled`, Staging und Produktion mit allen dreien eingeschaltet.

## Prüfen

`exp:migration:status` zeigt keinen Lauf, der auf seinen Push wartet, und `git ls-remote --tags origin 'migrations/*'`
listet den Tag des letzten Laufs.

## Nächster Schritt

[Upgrade-Schritt oder Migration?](upgrade-schritt-oder-migration.md): in welches der beiden Werkzeuge eine Änderung
gehört.

## Verwandte Seiten

- [Migrationen: der Einstieg](../README.md) · [Alle Rezepte](README.md)
- [Die Anleitung](../../../anleitungen/migrationen.md), [das Tutorial](../../../anleitungen/migrationen-tutorial.md), [PHP-Migrationen](../../../anleitungen/migrationen-php.md)
- [In der Administration](../in-der-administration.md), [Befehle](../befehle.md), [Einstellungen](../einstellungen.md)
- [Das Dateiformat](../../../spezifikationen/6.0/migrationsdateiformat.md)
- [Wenn eine Migration scheitert](../wenn-eine-migration-scheitert.md), [Meldungen und Fehlerbehebung](../../../anleitungen/migrationen-fehlerbehebung.md)
- [Migrationen oder Umzug einer Site](../migrationen-oder-umzug-einer-site.md)
- [Das Migrations-Repository](../../../anleitungen/migrationen-repositories.md),
  [migration.ini \[RepositorySettings\]](../../../beispiele/ini-einstellungen/migration/RepositorySettings.md),
  [`exp:migration:push`](../../../beispiele/befehle/expmigrationpush.md),
  [der Cronjob-Teil migrationpush](../../../beispiele/cronjobs/migrationpush.md)
- [Teamarbeit mit Git](teamarbeit-mit-git.md), [Die Audit-Spur der Läufe](die-audit-spur-der-laeufe.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
