---
title: Eine Sitzung in der Administration als Migration aufzeichnen
audience: developer, admin
category: examples
order: 140
summary: Was Sie in der Administration einrichten - eine Sektion, einen Ordner, eine Rollenzuweisung - als Schritte einer Migration aufzeichnen, ansehen, was erfasst wurde, und die Migration sehen, die daraus würde, auf der Konsole und in Setup > Migrationen > Aufzeichnung.
keywords: Migrationen, Aufzeichnung, Änderungen aufzeichnen, Aufzeichnungsbalken, Remote-ID, migrations, recording, record changes, exp:migration:record:start, exp:migration:record:stop, exp:migration:record:show, exp:migration:generate --from-recording, Setup > Migrationen > Aufzeichnung
type: example
---
# Eine Sitzung in der Administration als Migration aufzeichnen

Sie richten auf einer Staging-Installation einen News-Bereich ein: eine Sektion, einen Ordner dafür, eine
Redakteursrolle auf dem Ordner, und den Ordner verborgen bis zum Start. Statt das als Migration von Hand zu schreiben,
zeichnen Sie es auf, während Sie klicken, und machen aus der Aufzeichnung eine Migration für die Produktion.

Was Sie brauchen: Exponential 6.0.15 mit dem ausgeführten Upgrade-Schritt `6.0.15-migration-recording-tables`
(`./console exp:upgrade --apply`) und die Richtlinie `migration/edit` (die Rolle Administrator hat sie). Die
Ausgaben unten entstanden am 1. November 2026 auf SQLite; die Befehle geben englischen Text aus. Die Anleitung:
[Generatoren und das Aufzeichnen von Änderungen](../../anleitungen/migrationen-erzeugen-und-aufzeichnen.md).

## 1. Die Aufzeichnung starten

In der Administration: **Setup > Migrationen > Aufzeichnung**, Bezeichnung „news section“,
**Aufzeichnung meiner Änderungen starten**. Ein roter Balken **Aufzeichnung…** erscheint nun auf jeder Seite. Auf der
Konsole dasselbe:

```text
$ ./console exp:migration:record:start --label="news section" --apply
Exponential migrations: start a recording
Recording 20261101-124707-de8bac started: the changes of admin, "news section"
Next: make the changes, then ./console exp:migration:record:stop --user=admin --apply
PASS
```

## 2. Die Änderungen machen

Wie gewohnt, in der Administration:

1. **Setup > Sektionen**: eine neue Sektion „News“ mit dem Bezeichner `news`.
2. **Inhaltsstruktur**: ein Ordner „News“ unter der Wurzel (geben Sie ihm auf dem Reiter Details die Remote-ID
   `news-folder`, wenn Sie in der Migration einen Namen Ihrer Wahl möchten).
3. Am Ordner, **Sortierung**: nach Veröffentlichung, neueste zuerst.
4. **Rollen und Richtlinien > Editor > Mit Einschränkung zuweisen**: die Redakteursgruppe, eingeschränkt auf den
   Teilbaum des Ordners.
5. Am Ordner: **Verbergen**.

## 3. Beenden und ansehen

Wählen Sie **Aufzeichnung beenden** im Balken. Dann **Zeigen** an der Aufzeichnung, oder auf der Konsole:

```text
$ ./console exp:migration:record:show 20261101-124707-de8bac
Exponential migrations: a recording of changes
  20261101-124707-de8bac stopped   session     5 event(s)  2026-11-01 12:47  user #14  "news section"

  12:47:07  content.section.change           section/ensure section news
  12:47:07  content.object.create            content/create object remote:news-folder
  12:47:07  content.node.sort                location/update location remote:89e19c337bfb26c11d3d7a34a71a6618
  12:47:07  access.role.assign               role/assign role Editor
  12:47:07  content.node.hide                location/hide location remote:89e19c337bfb26c11d3d7a34a71a6618

Summary: 5 event(s)
PASS
```

Jede Änderung ist über das benannt, was auf der Produktion gleich bleibt: der Ordner über seine Remote-ID, sein Ort
über die Remote-ID des Orts, die Sektion über ihren Bezeichner, die Rolle über ihren Namen. `--format=json` zeigt die
Werte jedes Schritts, etwa die Felder des Ordners in jeder Sprache.

## 4. Die Migration, die daraus würde

```text
$ ./console exp:migration:record:show 20261101-124707-de8bac --plan
Exponential migrations: the steps of a recording
  20261101-124707-de8bac stopped   session     5 event(s)  2026-11-01 12:47  user #14  "news section"

    1  section/ensure section news  (from content.section.change)
    2  content/create object remote:news-folder  (from content.object.create)
    3  location/update location remote:89e19c337bfb26c11d3d7a34a71a6618  (from content.node.sort)
    4  role/assign role Editor  (from access.role.assign)
    5  location/hide location remote:89e19c337bfb26c11d3d7a34a71a6618  (from content.node.hide)

Summary: 5 event(s), 5 step(s)
PASS
```

Hätten Sie den Ordner nach dem Veröffentlichen umbenannt, wäre Schritt 2 immer noch ein Anlegen, mit dem letzten
Namen.

## 5. Die Migration erzeugen

```text
$ ./console exp:migration:generate --from-recording=20261101-124707-de8bac news_section --apply
Exponential migrations: generate from a recording
  20261101-124707-de8bac stopped   session     5 event(s)  2026-11-01 12:47  user #14  "news section"
    1  section/ensure section news
    2  content/create object remote:news-folder
    3  location/update location remote:89e19c337bfb26c11d3d7a34a71a6618
    4  role/assign role Editor
    5  location/hide location remote:89e19c337bfb26c11d3d7a34a71a6618
Wrote migrations/20261101125500_news_section.yml
Next: ./console exp:migration:check and ./console exp:migration:dry-run
PASS
```

Die Datei hat einen Schritt je Zeile oben, in der Form der Schritttypen `section`, `content`, `location` und `role`
([die Datei, die er schreibt](../../anleitungen/migrationen-erzeugen-und-aufzeichnen.md#eine-migration-aus-einer-aufzeichnung-erzeugen)).
Prüfen Sie sie, committen Sie sie und führen Sie sie auf der Produktion aus wie jede Migration
([Migrationen](../../anleitungen/migrationen.md)). In der Administration macht **Migration erzeugen** auf der Seite
der Aufzeichnung dasselbe.

## 6. Aufräumen

Eine Aufzeichnung, die Sie nicht mehr brauchen:

```text
$ ./console exp:migration:record:discard 20261101-124707-de8bac --apply
Exponential migrations: discard a recording
  20261101-124707-de8bac stopped   session     5 event(s)  2026-11-01 12:47  user #14  "news section"
Discarded: the recording and its events are removed
PASS
```

## Was am häufigsten schiefgeht

- **Änderungen eines Kollegen fehlen.** Eine Sitzungsaufzeichnung hält nur Ihre eigenen Änderungen fest. Öffnen Sie
  das websiteweite Fenster (Setup > Migrationen > Aufzeichnung), wenn mehrere zusammen arbeiten.
- **Benutzer stehen ohne ihre Namen in der Aufzeichnung.** Absichtlich: Konten und persönliche Felder werden nie
  aufgezeichnet; die Migration wird Konten über Geheimnis-Referenzen nennen.
- **Der Balken bleibt, nachdem die Aufzeichnung anderswo beendet wurde.** Laden Sie die Seite neu; bleibt er,
  schreibt `./console exp:migration:record:status` die Datei der laufenden Aufzeichnungen neu.

## Quelldateien

`kernel/classes/migration/expmigrationrecording.php`, `expmigrationrecorder.php`, `expmigrationrecordingevent.php`,
`expmigrationrecordinggenerator.php`, `kernel/private/classes/commands/expmigrationrecord*.php`,
`kernel/private/classes/views/migration/recording.php`, `settings/migration.ini [RecordingSettings]`.
