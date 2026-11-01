# Migrationen: Zurücknehmen und Sicherungspunkte

Ein Migrationslauf lässt eine Installation nie ohne Weg zurück. Bevor er schreibt, legt er einen **Sicherungspunkt**
(Caption) an, einen sicheren Stand von Datenbank, Speicher und Einstellungen; scheitert eine Migration, setzt der Lauf
die Installation von selbst darauf zurück. Diese Seite erklärt die Sicherungspunkte von Migrationsläufen, die
automatische Wiederherstellung und die drei Wege, Migrationen von Hand zurückzunehmen.

[Anleitungen](README.md) · [Migrationen: der Einstieg](../hilfe/migrationen/README.md) · [Migrationen](migrationen.md) · Siehe auch: [Upgrade mit der Konsole: Sicherungspunkte](aktualisieren-mit-der-konsole.md#sicherungspunkte),
[Das Migrations-Repository](migrationen-repositories.md), [Fehlerbehebung](migrationen-fehlerbehebung.md)

## Kurz

```bash
./console exp:migration:caption --list                                   # die Sicherungspunkte, neueste zuerst
./console exp:migration:caption --label="before the news import" --apply  # jetzt einen Sicherungspunkt
./console exp:migration:rollback 20261031-120000-a1b2c3                   # was die Wiederherstellung eines Laufs täte
./console exp:migration:rollback 20261031-120000-a1b2c3 --apply           # wiederherstellen
./console exp:migration:rollback 20261031120000_add_news_class --apply    # das down() einer Migration
./console exp:upgrade:finish --apply                                      # eine Wiederherstellung wirksam machen: Caches, PHP-FPM, Velocity
```

## Was ein Sicherungspunkt ist

Ein Sicherungspunkt ist der von `exp:upgrade`, es gibt also eine Liste von Sicherungspunkten für Upgrades und
Migrationen (`./console exp:upgrade:captions` und `./console exp:migration:caption --list` zeigen dieselben):

- mit installiertem git_manager: die vollständige Caption von git_manager (der Datenbankabzug und der Speicher unter
  `var/`) im Sicherungsordner von git_manager;
- ohne ihn, oder für eine Engine, die git_manager nicht sichern kann: der Datenbankabzug von `exp:upgrade` in
  `var/backup/upgrade/` (oder `[CaptionSettings] BackupDirectory`), ohne Speicher;
- in beiden: `settings/override`, die Commits des Codes und eine Prüfsumme jeder Datei.

[Upgrade mit der Konsole](aktualisieren-mit-der-konsole.md#sicherungspunkte) nennt jede Datei eines Sicherungspunkts und
wie jede Engine gesichert wird. Ein Migrationslauf fügt dem Sicherungspunkt einen eigenen Ordner `migration/` hinzu:

| Im Sicherungspunkt | Was es ist |
|---|---|
| `migration/marker.json` | wie die Installation aussah, als der Sicherungspunkt angelegt wurde: die Datenbank (siehe Wiederverwendung unten), Speicher unter `var/` und `settings/override` als Dateilisten, der Commit des Wurzel-Checkouts |
| `migration/uncommitted.json`, `migration/uncommitted/` | mit `--include-uncommitted`: die Checkouts, deren Code-Bäume nicht committete Änderungen hatten, ein Patch von jedem und eine Kopie seiner neuen Dateien |

### Die Arten

| Art | Angelegt | Bezeichnung |
|---|---|---|
| `migration-pre` | vor der ersten Migration eines Laufs mit `--apply` | `migration run <run-id> in <environment> pre: 2 migration(s) <ids>` |
| `migration-post` | nach einem erfolgreichen Lauf | `... post: <die angewendeten Migrationen>` |
| `migration-failure` | nachdem eine Migration gescheitert ist, vor der Wiederherstellung: der gescheiterte Zustand, zum späteren Ansehen | `... failure: <die gescheiterte Migration zuerst>` |
| `manual` | `exp:migration:caption --apply` | Ihr `--label` oder `manual caption <Zeit>` |

Jeder Sicherungspunkt wird in der Zeile des Laufs aufgezeichnet (`exp_migration_run.caption_pre`, `caption_post`), in
den Migrationen, denen er vorausging (`exp_migration.caption_id`), und im Audit-Protokoll (`system.migration.caption`).

### Wiederverwendung

Ein Sicherungspunkt kostet Zeit und Platz. Mit `[CaptionSettings] ReuseUnchanged=enabled` (die Voreinstellung)
verwendet ein Lauf den neuesten Sicherungspunkt einer Migration wieder, statt einen neuen anzulegen, solange die
Installation genau so ist wie damals: die Datenbank, der Speicher unter `var/`, `settings/override` und der Commit der
Installation. Die Datenbank wird genau verglichen auf PostgreSQL (die Änderungszähler jeder Tabelle), MySQL/MariaDB
(`CHECKSUM TABLE` jeder Tabelle) und SQLite (die Zeilen jeder Tabelle), ohne die Tabellen aus `ReuseIgnoreTableList[]`
(die Aufzeichnung der Läufe, Sitzungen, der Audit-Index). MongoDB und Oracle legen immer einen neuen an. Die Ausgabe
sagt `pre <id> (reused)`.

### Wenn kein Sicherungspunkt angelegt werden kann

Eine Datenbank ohne Sicherungswerkzeug, eine volle Platte, ein scheiternder git_manager: Der Lauf geht weiter mit der
Warnung `no caption could be taken, the run goes on without a way back: ...`. Dann gibt es keine automatische
Wiederherstellung; ein Fehler zeigt stattdessen die Schritte zur Wiederherstellung von Hand. `--no-caption` (oder
`[CaptionSettings] Before=disabled`) läuft absichtlich ohne Sicherungspunkt, mit der Warnung
`no caption before the run (--no-caption or [CaptionSettings] Before=disabled): a failure cannot be restored automatically`.

(Das ist die Regel der Migrationen. `exp:upgrade` behält seine eigene: Ein scheiternder Sicherungspunkt hält ein
Upgrade an.)

### Nicht committeter Code

Ein Sicherungspunkt enthält Datenbank, Speicher und Einstellungen und den Commit des Codes, nicht den Code selbst. Ein
Lauf mit `--apply` wird daher abgelehnt (Exit-Code 2), solange die Code-Bäume aus `[CaptionSettings] CodeTreeList[]`
(`settings`, `extension`, `design`) nicht committete Änderungen haben:

```text
FAIL: 1 change(s) that are not committed in the code trees (settings/...): commit them first, or run with --include-uncommitted to keep them in the caption
```

Committen Sie sie, oder geben Sie `--include-uncommitted` an: Die Änderungen kommen dann als Patch und als Kopie der
neuen Dateien in den Sicherungspunkt.

## Die automatische Wiederherstellung nach einem Fehler

Scheitert eine Migration (`[CaptionSettings] RestoreOnFailure=enabled`, die Voreinstellung):

1. wird die Transaktion der gescheiterten Migration zurückgerollt (soweit die Datenbank kann);
2. wird ein Sicherungspunkt des gescheiterten Zustands angelegt (Art `migration-failure`);
3. wird der Sicherungspunkt von vor dem Lauf wiederhergestellt: die Teile aus `[CaptionSettings] RestoreParts[]`
   (`database`, `storage`, `settings`), über das Zurückrollen von `exp:upgrade`;
4. wird die Aufzeichnung der gescheiterten Migration erneut geschrieben (die Wiederherstellung hat die Aufzeichnung auf
   den Stand des Sicherungspunkts zurückgesetzt) und der Lauf mit dem Zustand `failed` committet und gepusht.

```text
Captions: pre 20261031-193128, failure 20261031-193128-2
Restore: restored (caption 20261031-193128)
WARN restored the caption 20261031-193128 taken before the run (database, storage, settings); the failed state is kept in the caption 20261031-193128-2
FAIL (exit 1)
```

`--no-restore` (oder `RestoreOnFailure=disabled`) lässt die Installation, wie der Fehler sie hinterlassen hat; der
Bericht sagt `Restore: off`. Oracle hat keine eingebaute Wiederherstellung: Der Lauf hält an, und der Bericht nennt die
Schritte zur Wiederherstellung von Hand (`Restore: manual`). Die Zustände der Wiederherstellung sind `restored`,
`failed`, `manual`, `none` (kein Sicherungspunkt) und `off`.

Machen Sie eine Wiederherstellung danach wirksam: `./console exp:upgrade:finish --apply` leert die Caches und lädt
PHP-FPM und Velocity neu ([Bereitstellung](bereitstellung.md)).

## Von Hand zurücknehmen

`exp:migration:rollback <ziel>` nimmt ein Argument und sucht, was es nennt, in dieser Reihenfolge: eine Lauf-ID, eine
Migrations-ID, eine Sicherungspunkt-ID. Ohne `--apply` zeigt es, was es täte.

### Ein Lauf: sein Sicherungspunkt von vorher

```bash
./console exp:migration:rollback 20261031-120000-a1b2c3 --apply
```

Der vor dem Lauf angelegte Sicherungspunkt (`exp_migration_run.caption_pre`) wird über das Zurückrollen von
`exp:upgrade` wiederhergestellt: zuerst ein Sicherungspunkt `safety` des aktuellen Zustands (damit sich das Zurückrollen
selbst rückgängig machen lässt; `--no-safety-caption` überspringt ihn, auf Produktion abgelehnt ohne
`--force-no-caption`), dann die Teile (`--parts=database,storage,settings,libraries,code`, Voreinstellung
`[CaptionSettings] RestoreParts[]`), dann die Prüfung (`--no-verify` überspringt sie). Die Zeile des Laufs wird mit dem
Zustand `rolled-back` erneut geschrieben. Was jeder Teil wiederherstellt, steht in
[Upgrade mit der Konsole: Zurückrollen](aktualisieren-mit-der-konsole.md#zurückrollen).

Alles nach dem Sicherungspunkt ist weg, auch Änderungen, die Redakteure seitdem gemacht haben. Nehmen Sie einen Lauf bald
danach zurück, oder nutzen Sie `down()`.

### Eine Migration: ihr down()

```bash
./console exp:migration:rollback 20261031120000_add_news_class --apply
```

Das `down()` der Migration läuft (in einer Transaktion, wo die Datenbank eine hat), und ihre Aufzeichnung wird entfernt,
sie steht also wieder aus. Nur eine Migration, die `done` ist und ein `down()` hat, lässt sich so zurücknehmen; sonst
sagt der Befehl das und verweist auf den Sicherungspunkt. Haben zwei Quellen dieselbe ID, nennen Sie eine mit
`--source=site` oder `--source=extension:<name>`.

### Ein beliebiger früherer Sicherungspunkt: für Fachleute

```bash
./console exp:migration:caption --list
./console exp:migration:rollback 2026-10-29_10-15-00 --expert --confirm=2026-10-29_10-15-00 --apply
```

Jeder Sicherungspunkt der Liste lässt sich wiederherstellen, auch ein manueller oder einer von `exp:upgrade`, mit
`--expert` und der in `--confirm` erneut eingegebenen ID des Sicherungspunkts. Derselbe Sicherungspunkt `safety` und
dieselben Teile wie oben gelten.

### In der Administration

**Setup > Migrationen > Rücknahme** (`/migration/rollback`, Richtlinie `migration/rollback`) bietet dieselben drei
Wege: die Läufe mit einem Sicherungspunkt davor, die angewendeten Migrationen mit `down()` und die früheren
Sicherungspunkte (für Fachleute: das Kästchen **Ich weiß, was das wiederherstellt** und die erneut eingegebene ID des
Sicherungspunkts, wie `--expert --confirm`). Für einen Lauf oder einen Sicherungspunkt stellt sie die Teile Datenbank,
gespeicherte Dateien und Einstellungen wieder her, vorab angehakt wie in `[CaptionSettings] RestoreParts[]`;
Bibliotheken und Code stellt nur die Konsole wieder her. **Zurücknehmen** fragt nach „Identität bestätigen“ und
startet `exp:migration:rollback --apply` im Hintergrund unter dem angemeldeten Benutzer, mit dem Sicherungspunkt
`safety` zuerst; seine Seite zeigt Fortschritt und Bericht. Auch **Zurücknehmen** im Verlauf und **Diesen Lauf
zurücknehmen** auf der Seite eines Laufs führen dorthin.
[Ausführen, Probelauf und Rücknahme](../hilfe/migrationen/ausfuehren-probelauf-und-rueckgaengig.md#zurücknehmen)
zeigt die Seite.

### Was ein Zurücknehmen berührt

| Bereich | Wiederherstellung eines Sicherungspunkts | `down()` |
|---|---|---|
| Datenbank | zurück auf den Abzug (seitdem angelegte Tabellen werden gelöscht) | was `down()` zurücknimmt |
| Speicher unter `var/` | aus der Caption von git_manager (seitdem hinzugefügte Dateien bleiben) | was `down()` zurücknimmt |
| `settings/override` | zurückkopiert (seitdem hinzugefügte Dateien bleiben) | was `down()` zurücknimmt |
| Caches, Suchindex | veraltet: `./console exp:upgrade:finish --apply` ausführen | wie nach jeder Änderung: leeren, was die Migration berührt hat |
| PHP-FPM, Velocity-Worker | behalten die alten Klassen bis `exp:upgrade:finish --apply` | ebenso |

Jedes Zurücknehmen wird im Audit festgehalten: `system.migration.rollback` mit dem Verb `rollback-run`,
`rollback-caption` oder `rollback` (eine Migration).

## Engines

| Datenbank | Sicherungspunkt | Wiederherstellung |
|---|---|---|
| SQLite | eine konsistente Kopie | die Kopie zurückgeschrieben (SQLite-Backup-API) |
| MySQL/MariaDB | `mysqldump` | über `mysql` |
| PostgreSQL | `pg_dump --clean` | über `psql` |
| MongoDB | `mongodump` | über das Zurückrollen von `exp:upgrade` |
| Oracle | keiner eingebaut (Export mit `expdp`) | von Hand: der Lauf zeigt die Schritte |

## Verwandte Seiten

- [Migrationen: der Einstieg](../hilfe/migrationen/README.md), [Alle Rezepte](../hilfe/migrationen/rezepte/README.md),
  [Häufige Fragen](../hilfe/migrationen/faq.md)
- [Die Anleitung](migrationen.md), [das Tutorial](migrationen-tutorial.md), [PHP-Migrationen](migrationen-php.md),
  [Das Migrations-Repository](migrationen-repositories.md)
- [In der Administration](../hilfe/migrationen/in-der-administration.md), [Befehle](../hilfe/migrationen/befehle.md),
  [Einstellungen](../hilfe/migrationen/einstellungen.md),
  [Richtlinien, Ansichten und Ereignisse](../hilfe/migrationen/richtlinien-ansichten-und-ereignisse.md)
- [Das Dateiformat](../spezifikationen/6.0/migrationsdateiformat.md), [Die Funktion](../funktionen/6.0/migrationen.md)
- [Wenn eine Migration scheitert](../hilfe/migrationen/wenn-eine-migration-scheitert.md),
  [Fehlermeldungen](migrationen-fehlerbehebung.md)
- [Migrationen oder Umzug einer Site](../hilfe/migrationen/migrationen-oder-umzug-einer-site.md)
- [`exp:migration:rollback`](../beispiele/befehle/expmigrationrollback.md), [`exp:migration:caption`](../beispiele/befehle/expmigrationcaption.md)
- [migration.ini \[CaptionSettings\]](../beispiele/ini-einstellungen/migration/CaptionSettings.md)
