---
title: "Mit Migrationen ausliefern: CI/CD und GitHub Actions"
audience: developer, software-engineer, admin, hosting
category: migrations
order: 16
summary: Migrationen bei jeder Auslieferung ausführen - die Reihenfolge der Schritte, ein vollständiger GitHub-Actions-Workflow, der Pull Requests prüft und über SSH ausliefert, die Exit-Codes, auf die eine Pipeline reagiert, die Sperre der Installation und in Setup > Migrationen gestartete Läufe.
keywords: Migration, Migrationen, Auslieferung, Bereitstellung, Pipeline, Workflow, Exit-Code, Sperre, Lauf im Hintergrund, Schemaänderung, Datenbankänderung, migration, migrations, deploy, deployment, CI, CD, continuous delivery, GitHub Actions, pipeline, workflow, SSH, exit code, lock, upgrade.lock, background run, schema change, database change, exp:migration:migrate, exp:migration:status, exp:migration:check, exp:velocity deploy
featured: true
type: help
---
# Mit Migrationen ausliefern: CI/CD und GitHub Actions

Eine Auslieferung bringt neuen Code auf eine Installation. Kommt der Code mit Migrationen, führt die Auslieferung
sie auch aus, an der richtigen Stelle zwischen den anderen Schritten, und hält an, wenn eine scheitert. Dieses Rezept
gibt die Reihenfolge der Schritte, einen vollständigen GitHub-Actions-Workflow und die Regeln, die eine Pipeline
kennen muss: Exit-Codes, die Sperre und Läufe, die in der Administration gestartet werden.

## Was Sie am Ende haben

- einen Job `check`, der die Migrationen jedes Pull Requests prüft;
- einen Job `deploy`, der Staging (oder die Produktion) über SSH aktualisiert und die ausstehenden Migrationen
  ausführt;
- eine Pipeline, die bei einer gescheiterten Migration anhält, mit der Installation wiederhergestellt aus ihrem
  Sicherungspunkt.

## Bevor Sie beginnen

- Das Ziel ist ein Git-Checkout der Site, der dem Benutzer gehört, unter dem der Webserver läuft; ein
  Auslieferungsbenutzer darf über SSH Befehle als dieser Benutzer ausführen.
- `site.ini [AdminEnvironment] Level` ist auf dem Ziel gesetzt (`staging`, `production`), damit der Lauf
  `status/<umgebung>.json` committet und `migrations/<umgebung>/<run-id>` taggt.
- git_manager ist aktiv, und das Ziel kann zu seinem Remote pushen, oder dort ist
  `[RepositorySettings] Push=disabled` gesetzt.

## Schritte

### 1. Die Reihenfolge einer Auslieferung

| Schritt | Befehl | Warum hier |
|---|---|---|
| 1 | `git pull --ff-only` | der neue Code und die neuen Migrationsdateien; lehnt einen abgewichenen Checkout ab |
| 2 | `php bin/php/ezpgenerateautoloads.php -e` | Migrationen können Klassen nutzen, die der neue Code mitbringt |
| 3 | `./console exp:migration:status` | das Protokoll zeigt, was gleich läuft |
| 4 | `./console exp:migration:migrate --apply --format=json` | Sicherungspunkt, Kernel-Schritte der Stufen `database` und `scripts`, die Migrationen, Aufzeichnung, Commit, Tag, Push |
| 5 | `./console exp:velocity deploy` | Caches, PHP-FPM, Velocity: die laufende Site sieht den neuen Code und die neuen Daten |

Schritt 4 startet nicht (Exit 2), wenn `settings/`, `extension/` oder `design/` nicht committete Änderungen haben:
Ein Sicherungspunkt hält den Commit des Codes fest, nicht nicht committete Dateien. Ein sauberes `git pull`
hinterlässt keine. Hat der Lauf Upgrade-Schritte des Kernels angewendet, führen Sie zusätzlich
`./console exp:upgrade:finish --apply` aus.

### 2. Die Workflow-Datei

`.github/workflows/deploy.yml` im Repository der Site:

```yaml
name: Migrations and deploy

on:
  pull_request:
  push:
    branches: [main]

jobs:
  check:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
          extensions: sqlite3, intl, mbstring, xsl
      - run: composer install --no-interaction --no-progress
      - run: php bin/php/ezpgenerateautoloads.php -e
      - name: Lint the migrations (no database)
        run: ./console exp:migration:check

  deploy:
    if: github.event_name == 'push'
    needs: check
    runs-on: ubuntu-latest
    environment: staging
    concurrency: deploy-staging
    steps:
      - name: Deploy over SSH and run the migrations
        env:
          SSH_KEY: ${{ secrets.DEPLOY_SSH_KEY }}
          HOST: ${{ secrets.DEPLOY_HOST }}
        run: |
          mkdir -p ~/.ssh
          printf '%s\n' "$SSH_KEY" > ~/.ssh/id_deploy
          chmod 600 ~/.ssh/id_deploy
          ssh -i ~/.ssh/id_deploy -o StrictHostKeyChecking=accept-new "deploy@$HOST" '
            set -e
            cd /var/www/site
            git pull --ff-only
            php bin/php/ezpgenerateautoloads.php -e
            ./console exp:migration:status
            ./console exp:migration:migrate --apply --format=json > var/log/migration-last-run.json
            ./console exp:velocity deploy
          '
```

`set -e` hält das entfernte Skript beim ersten Befehl an, der nicht mit 0 endet, eine gescheiterte Migration
erreicht also nie den Schritt deploy. `concurrency:` verhindert, dass GitHub zwei Auslieferungen derselben Umgebung
startet; die Sperre der Installation lehnt einen zweiten Lauf ohnehin ab. Für die Produktion kopieren Sie den Job mit
`environment: production` und einer verlangten Freigabe auf dieser GitHub-Umgebung.

### 3. Die Exit-Codes, auf die die Pipeline reagiert

| Code | Bedeutet | Die Pipeline |
|---|---|---|
| 0 | erledigt, oder nichts zu tun | macht weiter |
| 1 | eine Migration ist gescheitert (der Sicherungspunkt von vorher wurde wiederhergestellt), eine geänderte Migrationsdatei wurde abgelehnt, oder die Sperre ist belegt | hält an; lesen Sie `var/log/migration-last-run.json` und das Laufprotokoll |
| 2 | Bedienung: eine falsche Option oder nicht committeter Code in den Code-Bäumen | hält an; bringen Sie den Checkout in Ordnung |
| 3 | die Datenbank ist nicht erreichbar | hält an; nichts ist gelaufen |
| 4 | interner Fehler | hält an; lesen Sie `var/log/migration.log` |

`--format=json` schreibt die Maschinenausgabe `exp-migration/1`: jede Migration mit ihrer Aktion und ihren
Meldungen, die Sicherungspunkte, die Wiederherstellung, den Stand des Repositorys, `errors`, `warnings` und `exit`.
Bewahren Sie sie als Build-Artefakt oder im Protokoll des Ziels auf.

### 4. Ein Lauf, der während einer Auslieferung scheitert

```text
Exponential migrations: run
Run 20261101-140210-b81f4d, environment staging, database mysql (transactions: data), as admin

  applied     20261101110000_news_note_priority            site               News notes get a priority (default 0)
              added exp_news_note.priority
  failed      20261101120500_news_tags                     site               Tags for news notes
              the table exp_news_tag could not be created: the table exp_news_tag exists already

Captions: pre 20261101-140209, failure 20261101-140211-2
Restore: restored (caption 20261101-140209)
Repository: commit 4d2e8a1c7b90, tag migrations/staging/20261101-140210-b81f4d, push pushed
Summary: 2 migration(s): 0 done, 1 pending, 0 not needed, 1 failed, 0 unknown
FAIL (exit 1)
```

Die Installation ist wieder dort, wo sie vor dem Lauf war; die Aufzeichnung des gescheiterten Laufs ist committet
und gepusht. Korrigieren Sie die Migration in einem neuen Commit und liefern Sie erneut aus, oder setzen Sie auf dem
Ziel mit `./console exp:migration:resume --apply` fort.

### 5. Die Sperre und Läufe aus der Administration

Ein Lauf, der schreibt, hält die Sperre der Installation `var/<site>/upgrade.lock`, geteilt mit `exp:upgrade`.
Daneben sagt die Halter-Datei `upgrade.lock.holder`, wer sie hält. Ein zweiter Lauf, aus der Pipeline, von der
Konsole oder aus der Administration, hält sofort mit `another run holds the lock: ...` an;
`./console exp:migration:status` nennt den Halter. Ein Prozess, der stirbt, gibt die Sperre frei; eine veraltete
Halter-Datei übernimmt der nächste Lauf.

Ein Administrator kann einen Lauf auch in **Setup > Migrationen** starten. Er läuft im Hintergrund als eigener
Konsolenprozess unter dem angemeldeten Benutzer, nach „Identität bestätigen“; seine Dateien liegen in
`var/<site>/migration/jobs/`. Er braucht `proc_open` und `migration.ini [AdminSettings] PhpCliPath`. Vereinbaren Sie
im Team, wer Migrationen auf welcher Umgebung ausführt; die Sperre verhindert zwei gleichzeitige Läufe, nicht zwei
Personen mit verschiedenen Erwartungen.

## Prüfen

- Ein Pull Request mit einem kaputten Kopf lässt den Job `check` scheitern.
- Nach einer Auslieferung listet `./console exp:migration:status` auf dem Ziel nichts Ausstehendes, und
  `git tag -l 'migrations/staging/*'` im Repository zeigt den Tag des Laufs.

## Zurücknehmen

Nach einem Fehler hat die Auslieferung den Sicherungspunkt selbst wiederhergestellt. Einen erfolgreichen Lauf nehmen
Sie mit `./console exp:migration:rollback <run-id> --apply` auf dem Ziel zurück, dann `git revert` des Codes und
erneut ausliefern.

## Nächster Schritt

Migrationen, die viele Objekte anfassen: [Große Datenmigrationen](grosse-datenmigrationen.md).

## Verwandte Seiten

- [Migrationen: der Einstieg](../README.md) · [Alle Rezepte](README.md)
- [Die Anleitung](../../../anleitungen/migrationen.md), [das Tutorial](../../../anleitungen/migrationen-tutorial.md), [PHP-Migrationen](../../../anleitungen/migrationen-php.md)
- [In der Administration](../in-der-administration.md), [Befehle](../befehle.md), [Einstellungen](../einstellungen.md)
- [Das Dateiformat](../../../spezifikationen/6.0/migrationsdateiformat.md)
- [Wenn eine Migration scheitert](../wenn-eine-migration-scheitert.md), [Meldungen und ihre Behebung](../../../anleitungen/migrationen-fehlerbehebung.md)
- [Migrationen oder Umzug einer Site](../migrationen-oder-umzug-einer-site.md)
- [Migrationen testen](migrationen-testen.md), [Migrationen je Umgebung](migrationen-je-umgebung.md),
  [Bereitstellung](../../../anleitungen/bereitstellung.md), [Upgrade mit der Konsole](../../../anleitungen/aktualisieren-mit-der-konsole.md),
  [exp:migration:migrate](../../../beispiele/befehle/expmigrationmigrate.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
