---
title: "Rezepte für Migrationen"
audience: developer, software-engineer, php-developer, admin, integrator
category: migrations
order: 40
summary: Fünfundzwanzig Aufgabenseiten für Migrationen in Exponential 6.0.15, jede mit dem, was Sie am Ende haben, den Schritten und ihrer Ausgabe, der vollständigen PHP-Migration, der Prüfung, der Rücknahme und dem nächsten Schritt - von einer neuen Tabelle bis zur Deploy-Pipeline, und für Teams, die von Doctrine Migrations, dem Kaliop-Bundle oder SQL-Update-Dateien kommen.
keywords: Rezepte, Kochbuch, Anleitung, Migration, Migrationen, Schemaänderung, Datenbankänderung, Tabelle anlegen, Spalte hinzufügen, Index hinzufügen, Inhaltsklasse, Klassenattribut, Inhalte in großen Mengen, Daten korrigieren, INI-Einstellungen, Rollen, Richtlinien, Inhalte anlegen, Umgebungen, Erweiterung, Paket, Testen, Code-Review, Auslieferung, große Datenmengen, Stapel, Sicherungspunkt, Rücknahme, Audit, Tags, Upgrade-Schritt, migration recipes, cookbook, how to, migration, migrations, schema change, database change, add table, add column, add index, content class, class attribute, bulk content change, fix data, INI settings, roles, policies, seed content, environments, extension, package, down(), testing, code review, git, deploy, CI, GitHub Actions, large data, batches, engines, captions, rollback, audit, tags, upgrade step, Doctrine, Kaliop, SQL update files
type: help
---
# Rezepte für Migrationen

Jedes Rezept ist eine Aufgabe, immer gleich aufgebaut: **was Sie am Ende haben**, die **Schritte** mit den Befehlen und
ihrer Ausgabe, die vollständige **Migration**, wie Sie sie **prüfen**, wie Sie sie **zurücknehmen** und der **nächste
Schritt**. Sie setzen Exponential 6.0.15 mit den Aufzeichnungstabellen voraus (`./console exp:upgrade:status` zeigt
`6.0.15-migration-tables` als `done`) und eine Entwicklungsinstallation in einem Git-Checkout. Neu bei Migrationen?
Machen Sie zuerst [das Tutorial](../../../anleitungen/migrationen-tutorial.md); es dauert etwa 20 Minuten.

## Das Datenbankschema

| Rezept | Sie haben dann |
|---|---|
| [Eine Datenbanktabelle anlegen](eine-datenbanktabelle-anlegen.md) | eine eigene Tabelle auf jeder Engine, angelegt aus einer Definition im Stil von `.dba` |
| [Eine Spalte oder einen Index hinzufügen](eine-spalte-oder-einen-index-hinzufuegen.md) | eine neue Spalte und einen Index an einer bestehenden Tabelle, geschrieben vom Schema-Handler jeder Engine |
| [Hinweise zu den Datenbanken](hinweise-zu-den-datenbanken.md) | was sich zwischen MySQL/MariaDB, PostgreSQL, SQLite, Oracle und MongoDB unterscheidet |

## Inhaltsklassen und Inhalte

| Rezept | Sie haben dann |
|---|---|
| [Eine Inhaltsklasse anlegen oder ändern](eine-inhaltsklasse-anlegen-oder-aendern.md) | eine Inhaltsklasse mit ihren Attributen und ihrer Klassengruppe, angelegt oder erweitert auf jeder Installation |
| [Ein Klassenattribut hinzufügen](ein-klassenattribut-hinzufuegen.md) | ein neues Attribut an einer bestehenden Klasse, mit initialisierten vorhandenen Objekten |
| [Inhalte in großen Mengen ändern](inhalte-in-grossen-mengen-aendern.md) | viele Objekte in einer Migration aktualisiert und veröffentlicht, gefunden nach Klasse und Teilbaum |
| [Daten korrigieren](daten-korrigieren.md) | eine Korrektur falscher Daten, so geschrieben, dass sie auf jeder Installation sicher ist |
| [Inhalte für eine neue Site anlegen](inhalte-fuer-eine-neue-site-anlegen.md) | die Ordner und Seiten, mit denen eine neue Site beginnt, wiedergefunden über die Remote-ID |
| [Große Datenmigrationen](grosse-datenmigrationen.md) | eine lange Migration in Stapeln, die sich nach einer Unterbrechung fortsetzen lässt |

## Einstellungen, Rollen und Richtlinien

| Rezept | Sie haben dann |
|---|---|
| [INI-Einstellungen verschieben oder umbenennen](ini-einstellungen-verschieben-oder-umbenennen.md) | eine Einstellung in `settings/override` verschoben, mit erhaltenem alten Wert und einem Weg zurück |
| [Rollen und Richtlinien hinzufügen](rollen-und-richtlinien-hinzufuegen.md) | eine Rolle mit ihren Richtlinien, einer Benutzergruppe über die Remote-ID zugewiesen |

## Wo Migrationen liegen und laufen

| Rezept | Sie haben dann |
|---|---|
| [Migrationen je Umgebung](migrationen-je-umgebung.md) | Migrationen, beschränkt auf Entwicklung, Staging oder Produktion mit der Kopfzeile `environments:` |
| [Migrationen in einer Erweiterung oder einem Paket](migrationen-in-einer-erweiterung-oder-einem-paket.md) | Migrationen, ausgeliefert in einer Erweiterung oder einem installierten Paket, eingeordnet mit denen der Site |
| [Upgrade-Schritt oder Migration?](upgrade-schritt-oder-migration.md) | das richtige Werkzeug für jede Änderung und eine Migration, die einen Upgrade-Schritt des Kernels voraussetzt |

## Qualität

| Rezept | Sie haben dann |
|---|---|
| [Ein umkehrbares down() schreiben](ein-umkehrbares-down-schreiben.md) | ein `down()`, das `up()` wirklich zurücknimmt, und den Beweis dafür |
| [Migrationen testen](migrationen-testen.md) | die Prüfung in CI, eine Simulation mit Diff und einen PHPUnit-Test auf SQLite |
| [Eine Migration im Code-Review prüfen](eine-migration-pruefen-im-code-review.md) | eine Checkliste für das Code-Review einer Migration |

## Teams und Auslieferung

| Rezept | Sie haben dann |
|---|---|
| [Teamarbeit mit Git](teamarbeit-mit-git.md) | Branches, Reihenfolge, `requires:` und Merge-Konflikte im Griff |
| [Mit Migrationen ausliefern](mit-migrationen-ausliefern.md) | eine Pipeline (mit einem Beispiel für GitHub Actions), die Migrationen prüft und anwendet |
| [Tags pushen](tags-pushen.md) | jeden Lauf committet, getaggt und gepusht, und gescheiterte Pushes nachgeholt |

## Sicherheit und die Aufzeichnung

| Rezept | Sie haben dann |
|---|---|
| [Sicherungspunkte und Rücknahme in der Praxis](sicherungspunkte-und-ruecknahme-in-der-praxis.md) | einen gescheiterten Lauf wiederhergestellt, einen Lauf und eine Migration zurückgenommen |
| [Die Audit-Spur der Läufe](die-audit-spur-der-laeufe.md) | die Antwort auf „was lief wo, wann und durch wen“ |

## Umstieg von einem anderen Werkzeug

| Rezept | Sie haben dann |
|---|---|
| [Umstieg von Doctrine Migrations](umstieg-von-doctrine-migrations.md) | die Befehle und Klassen von Doctrine denen von Exponential zugeordnet |
| [Umstieg vom Kaliop-Migrations-Bundle](umstieg-vom-kaliop-ezmigrationbundle.md) | YAML-Schritte von Kaliop als PHP-Migrationen geschrieben |
| [Umstieg von SQL-Update-Dateien](umstieg-von-sql-update-dateien.md) | eine von Hand geschriebene Update-Datei als aufgezeichnete, Engine-bewusste Migration |

## YAML-Migrationen

> **Kommt mit der YAML-Engine.** Rezepte für Migrationen aus YAML-Schritten werden hier gelistet, sobald die YAML-Engine
> erscheint. In 6.0.15 ist jedes Rezept in PHP geschrieben.

## Verwandte Seiten

- [Migrationen: der Einstieg](../README.md)
- [Die Anleitung](../../../anleitungen/migrationen.md), [das Tutorial](../../../anleitungen/migrationen-tutorial.md),
  [PHP-Migrationen](../../../anleitungen/migrationen-php.md)
- [In der Administration](../in-der-administration.md), [Befehle](../befehle.md), [Einstellungen](../einstellungen.md)
- [Das Dateiformat](../../../spezifikationen/6.0/migrationsdateiformat.md)
- [Wenn eine Migration scheitert](../wenn-eine-migration-scheitert.md), [Fehlermeldungen](../../../anleitungen/migrationen-fehlerbehebung.md)
- [Migrationen oder Umzug einer Site](../migrationen-oder-umzug-einer-site.md)
- [Häufige Fragen](../faq.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
