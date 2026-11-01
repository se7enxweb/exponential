---
title: "Migrationen in der Administration"
audience: admin, developer
category: administration
order: 1
summary: Wo die Migrationen einer Installation in der Administration erscheinen - Setup > Migrationen, der Dashboard-Block und die Website-Seitenleiste - und welche Seite was erklärt.
keywords: Migrationen, Setup > Migrationen, Modul migration, Dashboard-Block, Website-Seitenleiste, Richtlinien, migrations, migration module, dashboard block, website sidebar, policies, migration/read, migration/run, migration/rollback, migration/edit
type: help
---
# Migrationen in der Administration

Migrationen sind Änderungen an einer Installation, einmal als Dateien geschrieben und auf jede Installation der Site
in derselben Reihenfolge angewendet ([die Anleitung](../../anleitungen/migrationen.md)). Die Administration zeigt und
führt sie in **Setup > Migrationen** aus (das Modul `migration`) und zeigt ihren Stand in einem Dashboard-Block und in
der Website-Seitenleiste.

| Seite | Wobei sie hilft |
|---|---|
| [Stand und Verlauf](status-und-verlauf.md) | Jede Migration und ihren Stand sehen, die Läufe mit ihren Sicherungspunkten und Commits, einen Lauf live verfolgen, den Dashboard-Block lesen |
| [Ausführen, Probelauf und Rücknahme](ausfuehren-probelauf-und-rueckgaengig.md) | Sehen, was ein Lauf täte, ihn simulieren, die ausstehenden Migrationen ausführen, einen fehlgeschlagenen Lauf fortsetzen, einen Lauf, eine Migration oder einen früheren Sicherungspunkt zurücknehmen |
| [Migrationen in der Website-Seitenleiste](expwt-panel.md) | Den Stand der Migrationen auf den Seiten der Site lesen |
| [Änderungen aufzeichnen](aufzeichnung.md) | Die eigenen Änderungen oder in einem websiteweiten Fenster jede Änderung als Schritte einer Migration aufzeichnen; sehen, was eine Aufzeichnung erfasst hat; beenden und verwerfen |

## Wer was darf

| Richtlinie | Erlaubt |
|---|---|
| `migration/read` | den Stand, den Verlauf, die Seite eines Laufs, den Probelauf, den Dashboard-Block |
| `migration/run` | Ausführen, Fortsetzen und das Simulieren im Probelauf |
| `migration/rollback` | einen Lauf, eine Migration oder einen früheren Sicherungspunkt zurücknehmen |
| `migration/edit` | Änderungen aufzeichnen (Setup > Migrationen > Aufzeichnung); später auch das Schreiben und Erzeugen von Migrationen in der Administration |

In einer neuen Installation hat sie nur die Rolle Administrator. Ausführen, Fortsetzen, Simulieren, Zurücknehmen und
das Öffnen des websiteweiten Aufzeichnungsfensters fragen zuerst nach [„Identität bestätigen“](../../anleitungen/identitaet-bestaetigen.md). Die Richtlinien im
Einzelnen: [Richtlinien des Moduls migration](../../beispiele/richtlinien/migration.md).

Das Schreiben von Migrationen, ihr Editor und ihre Generatoren in der Administration und die Einstellungen des
Migrations-Repositorys gehören in 6.0.15 nicht zu Setup > Migrationen: Migrationen werden als Dateien geschrieben und
mit `./console exp:migration:generate` erzeugt, ihre Einstellungen stehen in `settings/migration.ini`. Was Sie ändern,
lässt sich aber als Schritte einer Migration aufzeichnen: [Änderungen aufzeichnen](aufzeichnung.md).

## Verwandte Seiten

- [Migrationen: die Anleitung](../../anleitungen/migrationen.md) und [das Tutorial](../../anleitungen/migrationen-tutorial.md)
- [Rücknahme und Sicherungspunkte](../../anleitungen/migrationen-rueckgaengig-und-captions.md)
- [Die Funktion Migrationen](../../funktionen/6.0/migrationen.md)
- [Ein Rundgang durch die Administration](../rundgang-durch-die-administration.md)
