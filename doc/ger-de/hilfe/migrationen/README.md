---
title: "Migrationen"
audience: developer, admin, software-engineer, php-developer, hosting, dba, integrator, dxp-developer, trainer
category: migrations
order: 1
summary: Der Anfang von allem rund um Migrationen in Exponential 6.0.15 - Schema- und Inhaltsänderungen, einmal als Dateien unter Versionskontrolle geschrieben und auf jede Installation einer Site in derselben Reihenfolge angewendet, mit einem Sicherungspunkt vor jedem Lauf, einer automatischen Wiederherstellung nach einem Fehler und einer Aufzeichnung in Git. Wege für Einsteiger, Administratoren und Teams, 25 Rezepte, die Referenz und Hilfe, wenn etwas scheitert.
keywords: Migration, Migrationen, Schemaänderung, Datenbankänderung, Inhaltsänderung, Datenmigration, Datenbankmigration, Auslieferung, Deployment, Sicherungspunkt, Probelauf, Rücknahme, Upgrade-Schritt, Rezepte, Kochbuch, Anleitung, migration, migrations, schema change, database change, content change, data migration, database migration, deploy, deployment, exp:migration, exp:migration:migrate, exp:migration:status, exp:migration:generate, migrate, rollback, caption, upgrade step, Doctrine, Doctrine Migrations, Kaliop, kaliop, ezmigrationbundle, SQL update file, dbupdate, Setup > Migrations, migration.ini, recipes, cookbook, how to
featured: true
type: help
---
# Migrationen

Eine **Migration** ist eine Änderung an einer Installation (eine Tabelle, eine Spalte, eine Inhaltsklasse, Inhalte,
eine Rolle, eine Einstellung), einmal in einer PHP-Datei unter Versionskontrolle geschrieben und auf jede Installation
der Site (Entwicklung, Staging, Produktion) in derselben Reihenfolge angewendet. Exponential 6.0.15 führt Migrationen im
Kernel aus: Es hält fest, was wo gelaufen ist, nimmt vor jedem Lauf einen **Sicherungspunkt**, stellt ihn automatisch
wieder her, wenn eine Migration scheitert, und committet, taggt und pusht die Aufzeichnung jedes Laufs.

Suchen Sie etwas anderes? Eine Site auf einen anderen Server umziehen, sie auf ein neues Release bringen oder Inhalte
aus einem anderen System übernehmen heißt **eine Site migrieren** – eine andere Aufgabe mit eigenen Seiten:
[Migrationen oder Umzug einer Site](migrationen-oder-umzug-einer-site.md).

## Inhalt

1. [Hier beginnen](#hier-beginnen)
2. [Für wen](#für-wen)
3. [Rezepte](#rezepte)
4. [In der Administration](#in-der-administration)
5. [Referenz](#referenz)
6. [Wenn etwas schiefgeht](#wenn-etwas-schiefgeht)
7. [YAML-Migrationen](#yaml-migrationen)
8. [Was 6.0.15 bietet und was später kommt](#was-6015-bietet-und-was-später-kommt)
9. [Verwandte Seiten](#verwandte-seiten)

## Hier beginnen

| Sie wollen ... | Beginnen Sie mit | Danach |
|---|---|---|
| Ihre erste Migration schreiben (etwa 20 Minuten) | [dem Tutorial](../../anleitungen/migrationen-tutorial.md) | [PHP-Migrationen](../../anleitungen/migrationen-php.md), [den Rezepten](rezepte/README.md) |
| verstehen, was Migrationen sind und wie ein Lauf abläuft | [der Anleitung](../../anleitungen/migrationen.md) | [Zurücknehmen und Sicherungspunkte](../../anleitungen/migrationen-rueckgaengig-und-captions.md), [dem Migrations-Repository](../../anleitungen/migrationen-repositories.md) |
| Migrationen anwenden, die andere geschrieben haben | [Migrationen für Administratoren](migrationen-fuer-administratoren.md) | [In der Administration](in-der-administration.md), [Ausführen, Probelauf und Rücknahme](ausfuehren-probelauf-und-rueckgaengig.md) |
| sich in Setup > Migrationen zurechtfinden | [Ein Rundgang durch Setup > Migrationen](rundgang-durch-setup-migrationen.md) | [Stand und Verlauf](status-und-verlauf.md) |
| von Hand gemachte Änderungen zu einer Migration machen | [Änderungen aufzeichnen](aufzeichnung.md) | [Migrationen erzeugen](migrationen-erzeugen.md), [Der Migrationseditor](der-migrationseditor.md) |
| die Migrationen auf Staging und Produktion bringen | [Eine Gegenstelle anlegen, Schritt für Schritt](eine-gegenstelle-anlegen-schritt-fuer-schritt.md) | [Ausliefern, Schritt für Schritt](ausliefern-schritt-fuer-schritt.md) |
| Änderungen im Team über CI bis in die Produktion bringen | [Teamarbeit mit Git](rezepte/teamarbeit-mit-git.md) | [Migrationen testen](rezepte/migrationen-testen.md), [Mit Migrationen ausliefern](rezepte/mit-migrationen-ausliefern.md) |
| von Doctrine Migrations, dem Kaliop-Bundle oder SQL-Update-Dateien umsteigen | [Umstieg von Doctrine Migrations](rezepte/umstieg-von-doctrine-migrations.md) | [Kaliop](rezepte/umstieg-vom-kaliop-ezmigrationbundle.md), [SQL-Update-Dateien](rezepte/umstieg-von-sql-update-dateien.md) |

Die fünf Befehle für jeden Tag:

```bash
./console exp:migration:generate --skeleton add_news_table --apply   # eine neue PHP-Migration in migrations/
./console exp:migration:check                                         # die Prüfung: Namen, Kopf, Reihenfolge, PHP (ohne Datenbank)
./console exp:migration:migrate                                       # Probelauf: was sich ändern würde
./console exp:migration:migrate --apply                               # Sicherungspunkt, Lauf, neuer Sicherungspunkt, Commit, Tag, Push
./console exp:migration:status                                        # was erledigt ist und was aussteht
```

Führen Sie sie im Wurzelverzeichnis der Installation als der Benutzer aus, unter dem der Webserver läuft (als root mit
`--allow-root-user`). Jeder Befehl, der schreibt, ist ein Probelauf, bis er `--apply` bekommt.

## Für wen

| Sie sind ... | Ihre Seite |
|---|---|
| Softwareingenieur und gestalten, wie Änderungen in die Produktion kommen | [Migrationen für Softwareingenieure](../softwareingenieure/migrationen-fuer-softwareingenieure.md) |
| PHP-Entwickler und schreiben die Migrationsklassen | [Migrationen in PHP schreiben](../php-entwickler/migrationen-in-php-schreiben.md) |
| Administrator und führen sie auf Staging und Produktion aus | [Migrationen für Administratoren](migrationen-fuer-administratoren.md) |
| Hosting-Anbieter oder Datenbankadministrator | [Migrationen auf gehosteten Sites](../hosting/betrieb/migrationen-auf-gehosteten-sites.md) |
| Agentur oder Integrator mit vielen Kunden-Sites | [Migrationen in Kundenprojekten](../integratoren/migrationen-in-kundenprojekten.md) |
| Trainer | [Lektion 9.5: Migrationen](../trainer/entwickler/modul-09-die-konsole-und-persistente-objekte/migrationen.md), mit einer Übung |
| DXP-Entwickler und kennen Doctrine Migrations | [Exponential-Migrationen für Doctrine-Nutzer](../dxp-entwickler/exponential-migrationen-fuer-doctrine-nutzer.md) |

## Rezepte

Jedes Rezept ist eine Aufgabe: was Sie am Ende haben, die Schritte mit ihrer Ausgabe, die vollständige Migration, wie Sie
sie prüfen, wie Sie sie zurücknehmen und der nächste Schritt. [Alle Rezepte](rezepte/README.md).

**Das Datenbankschema**

- [Eine Datenbanktabelle anlegen](rezepte/eine-datenbanktabelle-anlegen.md)
- [Eine Spalte oder einen Index hinzufügen](rezepte/eine-spalte-oder-einen-index-hinzufuegen.md)
- [Hinweise zu den Datenbanken: MySQL, PostgreSQL, SQLite, Oracle und MongoDB](rezepte/hinweise-zu-den-datenbanken.md)

**Inhaltsklassen und Inhalte**

- [Eine Inhaltsklasse anlegen oder ändern](rezepte/eine-inhaltsklasse-anlegen-oder-aendern.md)
- [Ein Klassenattribut hinzufügen](rezepte/ein-klassenattribut-hinzufuegen.md)
- [Inhalte in großen Mengen ändern](rezepte/inhalte-in-grossen-mengen-aendern.md)
- [Daten korrigieren](rezepte/daten-korrigieren.md)
- [Inhalte für eine neue Site anlegen](rezepte/inhalte-fuer-eine-neue-site-anlegen.md)
- [Große Datenmigrationen](rezepte/grosse-datenmigrationen.md)

**Einstellungen, Rollen und Richtlinien**

- [INI-Einstellungen verschieben oder umbenennen](rezepte/ini-einstellungen-verschieben-oder-umbenennen.md)
- [Rollen und Richtlinien hinzufügen](rezepte/rollen-und-richtlinien-hinzufuegen.md)

**Wo Migrationen liegen und laufen**

- [Migrationen je Umgebung](rezepte/migrationen-je-umgebung.md)
- [Migrationen in einer Erweiterung oder einem Paket](rezepte/migrationen-in-einer-erweiterung-oder-einem-paket.md)
- [Upgrade-Schritt oder Migration?](rezepte/upgrade-schritt-oder-migration.md)

**Qualität**

- [Ein umkehrbares down() schreiben](rezepte/ein-umkehrbares-down-schreiben.md)
- [Migrationen testen](rezepte/migrationen-testen.md)
- [Eine Migration im Code-Review prüfen](rezepte/eine-migration-pruefen-im-code-review.md)

**Teams und Auslieferung**

- [Teamarbeit mit Git](rezepte/teamarbeit-mit-git.md)
- [Mit Migrationen ausliefern](rezepte/mit-migrationen-ausliefern.md)
- [Tags pushen](rezepte/tags-pushen.md)

**Sicherheit und die Aufzeichnung**

- [Sicherungspunkte und Rücknahme in der Praxis](rezepte/sicherungspunkte-und-ruecknahme-in-der-praxis.md)
- [Die Audit-Spur der Läufe](rezepte/die-audit-spur-der-laeufe.md)

**Umstieg von einem anderen Werkzeug**

- [Umstieg von Doctrine Migrations](rezepte/umstieg-von-doctrine-migrations.md)
- [Umstieg vom Kaliop-Migrations-Bundle](rezepte/umstieg-vom-kaliop-ezmigrationbundle.md)
- [Umstieg von SQL-Update-Dateien](rezepte/umstieg-von-sql-update-dateien.md)

## In der Administration

**Setup > Migrationen** zeigt jede Migration mit ihrem Zustand, führt die ausstehenden im Hintergrund aus, simuliert
einen Lauf und nimmt einen Lauf oder eine Migration zurück, zeichnet Änderungen auf, schreibt Migrationen in seinem
Editor und liefert sie an andere Installationen aus. Das Dashboard hat einen Block **Migrationen**, die
Website-Seitenleiste ein Panel. Jede Seite unten hat Anleitungen Schritt für Schritt mit Bildschirmfotos.

- [Ein Rundgang durch Setup > Migrationen](rundgang-durch-setup-migrationen.md): jeder Reiter mit Bildschirmfoto, und welche Seite für welche Aufgabe
- [Migrationen in der Administration](in-der-administration.md): die Seiten und wer sie nutzen darf
- [Stand und Verlauf](status-und-verlauf.md)
- [Ausführen, Probelauf und Rücknahme](ausfuehren-probelauf-und-rueckgaengig.md)
- [Migrationen in der Website-Seitenleiste](expwt-panel.md)
- [Änderungen aufzeichnen](aufzeichnung.md): die eigenen Änderungen oder in einem websiteweiten Fenster jede Änderung als Schritte einer Migration aufzeichnen
- [Gegenstellen und Deploys](gegenstellen-und-deploys.md): die anderen Installationen, an die diese ihre Migrationen ausliefert, Deploys gleichzeitig oder Stufe für Stufe mit einem Tor, live je Gegenstelle, Verlauf und Rücknahme
- [Eine Gegenstelle anlegen, Schritt für Schritt](eine-gegenstelle-anlegen-schritt-fuer-schritt.md): die fünf Schritte des Assistenten, Verbindung testen, bearbeiten und entfernen, FTP-Hosts mit dem Trigger, Selbstheilung
- [Ausliefern, Schritt für Schritt](ausliefern-schritt-fuer-schritt.md): Gegenstellen wählen, Preflight, ausliefern, live verfolgen, ein Tor freigeben, zurücknehmen, der Verlauf und die Hinweis-Mails
- [Der Migrationseditor](der-migrationseditor.md): eine YAML-Migration mit Formularen bauen, prüfen, ansehen und speichern
- [Migrationen erzeugen](migrationen-erzeugen.md): aus einer Aufzeichnung, im Editor, aus dem Vorhandenen, aus einem Unterschied, Gerüste

## Referenz

| Seite | Was sie auflistet |
|---|---|
| [Die Anleitung](../../anleitungen/migrationen.md) | das ganze Modell: Lebenszyklus, Dateien, Reihenfolge, Kopf, Umgebungen, Aufzeichnung, Transaktionen, Sperre, jeder Schalter |
| [PHP-Migrationen](../../anleitungen/migrationen-php.md) | die Klasse, ihre Methoden, der Kontext, Transaktionen, Fehler, Ereignisse, Tests |
| [Das Migrationsdateiformat](../../spezifikationen/6.0/migrationsdateiformat.md) | Dateinamen, der Kopf, die Reihenfolge, die Prüfsumme, die Aufzeichnungstabellen, die Statusdatei, das Laufprotokoll, die Maschinenausgabe |
| [Befehle](befehle.md) | jeder Befehl `exp:migration:*`, mit einem Link auf seine erzeugte Seite |
| [Einstellungen](einstellungen.md) | jeder Block von `migration.ini`, mit einem Link auf seine erzeugte Seite |
| [Richtlinien, Ansichten und Ereignisse](richtlinien-ansichten-und-ereignisse.md) | die Richtlinien, die Ansichten des Moduls `migration`, die Ereignisse und die Audit-Ereignisse |
| [Die Funktion](../../funktionen/6.0/migrationen.md) | was 6.0.15 hinzufügt, die Klassen, die Engines, die Grenzen |
| [Upgrade mit der Konsole](../../anleitungen/aktualisieren-mit-der-konsole.md) | `exp:upgrade`, dessen Schritte unter den Migrationen gelistet und ausgeführt werden |

## Wenn etwas schiefgeht

- [Wenn eine Migration scheitert](wenn-eine-migration-scheitert.md): Fehlerbehebung nach Symptom
- [Fehlermeldungen](../../anleitungen/migrationen-fehlerbehebung.md): jede Meldung, die die Befehle ausgeben
- [Häufige Fragen](faq.md)
- [Zurücknehmen und Sicherungspunkte](../../anleitungen/migrationen-rueckgaengig-und-captions.md)

## YAML-Migrationen

Eine Migration kann statt einer PHP-Klasse eine Liste von YAML-Schritten sein: Inhalte, Orte und Versionen, Klassen und
Klassengruppen, Benutzer, Benutzergruppen und Rollen, Sektionen, Sprachen und Objektzustände. Die Engine, ihre
Platzhalter, Bedingungen und Schleifen: [das Dateiformat](../../spezifikationen/6.0/migrationsdateiformat.md); die
Schritttypen: [Schritte von YAML-Migrationen](../../anleitungen/migrationen-yaml-schritte.md) und
[jeder Schritttyp](../../beispiele/migrationsschritte/README.md). Die Rezepte hier sind in PHP geschrieben; jedes lässt
sich auch als YAML-Schritte schreiben, wo ein Schritttyp es abdeckt.

## Was 6.0.15 bietet und was später kommt

| In 6.0.15 | Noch nicht |
|---|---|
| PHP-, YAML-, SQL- und INI-Migrationen in der Site, in aktiven Erweiterungen und in installierten Paketen | |
| der Probelauf und die Simulation mit Diff auf PostgreSQL und SQLite | eine Simulation auf MySQL/MariaDB, Oracle und MongoDB (der Plan wird gelistet) |
| Sicherungspunkte, die automatische Wiederherstellung, die Rücknahme eines Laufs, einer Migration und jedes Sicherungspunkts | |
| Generatoren aus einer Aufzeichnung, aus Inhalten, Klassen, Rollen, Sektionen und Einstellungen, aus einem Unterschied, und Gerüste; der Editor in der Administration | |
| Commit, Tag und Push der Aufzeichnung jedes Laufs; der Cronjob-Teil `migrationpush` | `exp:velocity deploy --migrate` |
| Setup > Migrationen, Läufe im Hintergrund, Aufzeichnung, der Editor, Gegenstellen und Auslieferungen, der Dashboard-Block und das Panel der Seitenleiste | |
| die Upgrade-Schritte des Kernels unter den Migrationen gelistet und ausgeführt (Stufen `database` und `scripts`) | die Stufen `code`, `libraries` und `finish` bleiben bei `exp:upgrade` |

## Verwandte Seiten

- [Migrationen: die Anleitung](../../anleitungen/migrationen.md), [das Tutorial](../../anleitungen/migrationen-tutorial.md),
  [PHP-Migrationen](../../anleitungen/migrationen-php.md)
- [Alle Rezepte](rezepte/README.md), [Häufige Fragen](faq.md)
- [Migrationen oder Umzug einer Site](migrationen-oder-umzug-einer-site.md)
- [Migration zu und von Exponential](../../installation/migration/README.md) (eine Site aus einem anderen System oder Release umziehen)
- [Upgrade mit der Konsole](../../anleitungen/aktualisieren-mit-der-konsole.md),
  [Upgrade-Schritte für Erweiterungen](../../anleitungen/upgrade-schritte-fuer-erweiterungen.md)
- [Schemaänderungen und Migrationen](../softwareingenieure/schemaaenderungen-und-migrationen.md)
- [Ein Rundgang durch die Administration](../rundgang-durch-die-administration.md)
- [Glossar](../../glossar.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
