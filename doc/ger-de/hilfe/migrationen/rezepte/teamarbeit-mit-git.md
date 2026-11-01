---
title: "Teamarbeit mit Git: Branches, Konflikte und Reihenfolge"
audience: developer, software-engineer, php-developer, integrator
category: migrations
order: 15
summary: Wie mehrere Entwicklerinnen und Entwickler Migrationen auf Branches schreiben, ohne sich in die Quere zu kommen - Zeitstempel und Reihenfolge, requires für echte Abhängigkeiten, zwei Migrationen mit demselben Zeitstempel, Rebasing, Umbenennen nur vor einem Merge und die Statusdateien nach einem Merge.
keywords: Migration, Migrationen, Git, Branch, Branches, Merge, Rebase, Konflikt, Team, Reihenfolge, Zeitstempel, Statusdatei, Schemaänderung, Datenbankänderung, Auslieferung, migration, migrations, git, branch, merge, rebase, conflict, team, order, timestamp, requires, pull request, status file, schema change, database change, deploy, exp:migration:check, exp:migration:status
featured: true
type: help
---
# Teamarbeit mit Git: Branches, Konflikte und Reihenfolge

Migrationen sind Dateien in Git, also arbeitet ein Team an ihnen wie am Code: auf Branches, über Pull Requests. Zwei
Dinge sind anders. Ihre **Reihenfolge** ergibt sich aus ihren Namen, und eine Migration, die **irgendwo gelaufen**
ist, darf sich nie wieder ändern. Dieses Rezept zeigt den Arbeitsablauf, der beides achtet.

## Was Sie am Ende haben

- einen Branch je Änderung, dessen Migration zur Zeit ihres Schreibens benannt ist;
- `requires:`, wo eine Migration wirklich von einer anderen abhängt;
- klare Regeln für Umbenennen, Rebasing und Merge-Konflikte.

## Bevor Sie beginnen

- Jede Entwicklerin und jeder Entwickler hat eine Entwicklungsinstallation der Site und führt Migrationen dort mit
  `--no-commit` oder mit `[RepositorySettings] Push=disabled` aus, damit keine Aufzeichnung den Laptop verlässt.
- Der Hauptbranch ist geschützt, und die CI führt bei jedem Pull Request `./console exp:migration:check` aus
  ([Migrationen testen](migrationen-testen.md)).

## Schritte

### 1. Ein Branch, eine Änderung, eine eigene Migration

```bash
git switch -c feature/news-priority
./console exp:migration:generate --skeleton news_note_priority --description="News notes get a priority (default 0)" --apply
```

Die ID beginnt mit der aktuellen Zeit, auf die Sekunde: `20261101110000_news_note_priority`. Zwei Personen, die am
selben Tag erzeugen, bekommen verschiedene IDs, und beide Dateien lassen sich ohne Konflikt zusammenführen, weil es
verschiedene Dateien sind.

### 2. Reihenfolge: zuerst Zeitstempel, requires für Abhängigkeiten

Migrationen laufen in der Reihenfolge ihrer IDs: Zeitstempel, dann Name. Werden zwei Branches zusammengeführt,
verzahnen sich ihre Migrationen nach der Zeit. Das stimmt, solange sie unabhängig sind. Nutzt eine, was die andere
anlegt, sagen Sie es:

```php
// requires: 20261101090000_news_note_table
```

`requires:` hat Vorrang vor den Zeitstempeln: Die Migration läuft nach der genannten, wohin ihre Namen sie auch
stellen. Eine Anforderung, die keine Migration nennt, und ein Zyklus lassen `exp:migration:check` in der CI vor dem
Merge scheitern.

### 3. Zwei Migrationen mit demselben Zeitstempel

Das passiert, wenn jemand eine Datei kopiert, statt sie zu erzeugen. Die IDs unterscheiden sich noch im Namen
(`20261101110000_news_note_priority` und `20261101110000_news_tags`) und sortieren nach dem Namen; müssen sie in einer
bestimmten Reihenfolge laufen, fügen Sie `requires:` hinzu. Zwei Dateien mit **derselben ID** in verschiedenen
Quellen (der Site und einer Erweiterung) sind ein Fehler: Benennen Sie die um, die noch nirgends gelaufen ist.

### 4. Einen langlebigen Branch rebasen

Ein Branch, der Wochen gewartet hat, trägt eine Migration mit altem Zeitstempel. Nach `git rebase main` sortiert sie
vor Migrationen, die andere zusammengeführt haben und die die Produktion schon ausgeführt hat. Der Runner wendet sie
trotzdem an (eine ausstehende Migration läuft, wo immer sie steht), aber auf einer frischen Installation liefe sie
vor ihnen. Hängt sie von ihnen ab, fügen Sie `requires:` hinzu. Soll der Name die Wahrheit sagen, benennen Sie sie
**jetzt** um, vor dem Merge:

```bash
git mv migrations/20261015090000_news_tags.php migrations/20261101120500_news_tags.php
```

Benennen Sie nur eine Migration um, die auf keiner gemeinsam genutzten Installation gelaufen ist. Ihre eigene
Entwicklungsinstallation listet die alte ID dann als verwaist (`recorded as done, but its file is not found any
more`); nehmen Sie sie vor dem Umbenennen zurück, oder lassen Sie die harmlose Zeile stehen.

### 5. Nach dem Merge: nie ändern, immer hinzufügen

Sobald eine Migration auf dem Hauptbranch ist, können Staging oder die Produktion sie ausgeführt haben. Eine
Fehlerkorrektur ist eine **neue** Migration:

```text
FAIL: 20261101110000_news_note_priority changed after it was applied (checksum 3c1f9a0b2d41 -> 8e77d0c45a19); a run with --force --reason=<text> accepts the change
FAIL (exit 1)
```

`--force --reason=<text>` ist nur für Änderungen da, die nicht ändern, was die Migration tut, etwa einen Kommentar.

### 6. Konflikte in den Statusdateien

Die Aufzeichnung jeder Umgebung wird von den Läufen selbst nach `migrations/status/<umgebung>.json` und
`migrations/runs/` geschrieben, nie von Hand. Jede Umgebung schreibt nur ihre eigene Datei, zwei Umgebungen geraten
also nie in Konflikt. Gibt es doch einen Merge-Konflikt in einer Statusdatei, nehmen Sie die Fassung des Branches,
auf den die Läufe dieser Umgebung committen, und lassen Sie den nächsten Lauf sie neu schreiben; die Aufzeichnung ist
die Datenbank, nicht die Datei.

## Prüfen

Auf dem Ergebnis des Merges:

```bash
./console exp:migration:check
./console exp:migration:status
```

```text
Exponential migrations: status
Run 20261101-121530-0a8c3e, environment development, database sqlite (transactions: full)

  done        20261101090000_news_note_table               site               A table for news notes, with a first note
  pending     20261101110000_news_note_priority            site               News notes get a priority (default 0)
  pending     20261101120500_news_tags                     site               Tags for news notes

Summary: 3 migration(s): 1 done, 2 pending, 0 not needed, 0 failed, 0 unknown; 2 would-apply
PASS
```

## Zurücknehmen

Ein aufgegebener Branch, dessen Migration auf der Entwicklung gelaufen ist:
`./console exp:migration:rollback <id> --apply`, bevor Sie zurück auf main wechseln, sonst behält die Aufzeichnung
eine verwaiste Zeile.

## Nächster Schritt

Das zusammengeführte Ergebnis ausliefern: [Mit Migrationen ausliefern](mit-migrationen-ausliefern.md).

## Verwandte Seiten

- [Migrationen: der Einstieg](../README.md) · [Alle Rezepte](README.md)
- [Die Anleitung](../../../anleitungen/migrationen.md), [das Tutorial](../../../anleitungen/migrationen-tutorial.md), [PHP-Migrationen](../../../anleitungen/migrationen-php.md)
- [In der Administration](../in-der-administration.md), [Befehle](../befehle.md), [Einstellungen](../einstellungen.md)
- [Das Dateiformat](../../../spezifikationen/6.0/migrationsdateiformat.md)
- [Wenn eine Migration scheitert](../wenn-eine-migration-scheitert.md), [Meldungen und ihre Behebung](../../../anleitungen/migrationen-fehlerbehebung.md)
- [Migrationen oder Umzug einer Site](../migrationen-oder-umzug-einer-site.md)
- [Eine Migration im Code-Review prüfen](eine-migration-pruefen-im-code-review.md), [Das Migrations-Repository](../../../anleitungen/migrationen-repositories.md),
  [Mit Git entwickeln](../../mit-git-entwickeln.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
