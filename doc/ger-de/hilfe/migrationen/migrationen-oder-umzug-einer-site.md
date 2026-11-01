---
title: "Migrationen oder Umzug einer Site"
audience: developer, admin, software-engineer, hosting, dba, adopter, integrator, trainer, project-manager
category: migrations
order: 2
summary: Das Wort Migration steht in der Exponential-Dokumentation für zwei verschiedene Aufgaben - Migrationen (Schema- und Inhaltsänderungen einer Site, als Dateien geschrieben und mit exp:migration:migrate angewendet) und das Migrieren einer Site (eine ganze Installation umziehen oder aktualisieren oder Inhalte aus einem anderen System übernehmen). Welche Sie brauchen und wo jede beschrieben ist.
keywords: Migration, Migrationen, migrieren, Site migrieren, Site umziehen, Umzug, Upgrade, aktualisieren, Site-Migration, Inhaltsmigration, Schemaänderung, Datenbankänderung, anderer Hoster, anderes System, Begriffsklärung, welche Migration, migration, migrations, migrating, migrate a site, move a site, moving a site, upgrade, upgrading, site migration, content migration, schema change, database change, exp:migration, exp:upgrade, import, another host, another system, disambiguation, which migration
featured: true
type: help
---
# Migrationen oder Umzug einer Site

Die Dokumentation verwendet das Wort *Migration* für zwei verschiedene Aufgaben. Diese Seite trennt sie und führt Sie zu
den richtigen Seiten.

| | **Migrationen** (Schema- und Inhaltsänderungen) | **Eine Site migrieren** (eine Installation umziehen oder aktualisieren) |
|---|---|---|
| Was sich ändert | Datenbank, Inhaltsklassen, Inhalte, Rollen oder Einstellungen einer Site, Stück für Stück | wo eine ganze Installation läuft, welches Release sie nutzt oder aus welchem System ihre Inhalte kommen |
| Wie oft | viele Male, mit jeder Änderung des Site-Teams | einmal je Umzug, je Upgrade oder je Import |
| Geschrieben als | eine PHP-Datei `YYYYMMDDHHMMSS_name.php` in `migrations/`, unter Versionskontrolle | ein Plan, eine Checkliste und Werkzeuge wie Dumps, Exporte und das Upgrade |
| Ausgeführt mit | `./console exp:migration:migrate --apply` oder Setup > Migrationen | `./console exp:upgrade`, Datenbank-Dumps, Paketexport und -import, Importwerkzeuge |
| Aufgezeichnet in | den Tabellen `exp_migration` und `exp_migration_run` und einer Statusdatei in Git | den eigenen Notizen des Projekts; `exp:upgrade` zeichnet seine Schritte auf |
| Angewendet auf | jede Installation der Site: Entwicklung, Staging, Produktion | die eine Installation, die umgezogen, aktualisiert oder befüllt wird |
| Hier beginnen | [Migrationen](README.md) | [Migration zu und von Exponential](../../installation/migration/README.md) |

## Welche brauchen Sie?

| Sie wollen ... | Es ist | Lesen Sie |
|---|---|---|
| eine Tabelle, eine Spalte oder einen Index für Ihre Site anlegen | eine Migration | [Eine Datenbanktabelle anlegen](rezepte/eine-datenbanktabelle-anlegen.md) |
| eine Inhaltsklasse oder ein Attribut auf jeder Installation hinzufügen | eine Migration | [Eine Inhaltsklasse anlegen oder ändern](rezepte/eine-inhaltsklasse-anlegen-oder-aendern.md) |
| Inhalte auf jeder Installation gleich ändern oder korrigieren | eine Migration | [Inhalte in großen Mengen ändern](rezepte/inhalte-in-grossen-mengen-aendern.md), [Daten korrigieren](rezepte/daten-korrigieren.md) |
| einer Rolle auf Staging und Produktion neue Richtlinien geben | eine Migration | [Rollen und Richtlinien hinzufügen](rezepte/rollen-und-richtlinien-hinzufuegen.md) |
| eine Installation auf ein neues Exponential-Release bringen | eine Site migrieren (ein Upgrade) | [Upgrade](../../anleitungen/aktualisieren.md), [Upgrade mit der Konsole](../../anleitungen/aktualisieren-mit-der-konsole.md) |
| eine Site auf einen anderen Server oder zu einem anderen Hoster umziehen | eine Site migrieren | [Eine Website zwischen Hostern umziehen](../hosting/migrationen/eine-website-zwischen-hostern-umziehen.md) |
| eine Site auf eine andere Datenbank-Engine umziehen | eine Site migrieren | [Eine Website auf eine andere Engine umziehen](../datenbankadministratoren/zwischen-engines-wechseln.md), [Die Datenbank-Engine einer Kunden-Website wechseln](../hosting/migrationen/die-datenbank-engine-einer-kunden-website-wechseln.md) |
| eine Site aus WordPress, Drupal oder einem anderen System übernehmen | eine Site migrieren | [Migration zu und von Exponential](../../installation/migration/README.md), [Von einem anderen System](../anwender-umsteiger/umziehen/von-einem-anderen-system.md) |
| eine Site aus eZ Platform oder Ibexa übernehmen | eine Site migrieren | [Von eZ Platform oder Ibexa](../anwender-umsteiger/umziehen/von-ez-platform-oder-ibexa.md) |
| von einem älteren Exponential-Release umsteigen | eine Site migrieren | [Von einer älteren Exponential-Version](../anwender-umsteiger/umziehen/von-einer-aelteren-exponential-version.md) |
| Layouts, Einstellungen und Templates zwischen Sites verschieben | keines von beiden: die Layouts-Beispiele | [Beispiele zur Migration](../zwei-wege-zum-bauen/beispiele/migrieren/README.md) |

## Wo sich beide treffen

- **Nach einem Upgrade übernehmen die Migrationen.** `exp:upgrade` bringt eine Installation mit den Upgrade-Schritten
  des Kernels auf ein neues Release. Diese Schritte werden auch unter den Migrationen gelistet (als Migrationen der
  Quelle `kernel`), und ein Migrationslauf führt die ausstehenden der Stufen `database` und `scripts` vor den
  Migrationen der Site aus
  ([Die Schritte unter den Migrationen](../../anleitungen/aktualisieren-mit-der-konsole.md#die-schritte-unter-den-migrationen)).
  Eine Migration der Site, die die Tabelle eines Schritts braucht, nennt ihn in `requires:`.
- **Nach einem Umzug oder Import halten Migrationen die Kopien gleich.** Sobald eine migrierte Site auf Entwicklung,
  Staging und Produktion läuft, ist jede weitere Änderung (eine neue Klasse, eine Rolle, eine Korrektur) eine Migration,
  damit alle drei gleich bleiben ([Migrationen je Umgebung](rezepte/migrationen-je-umgebung.md)).
- **Ein Wechsel der Datenbank-Engine ist keine Migration.** Migrationen ändern eine Site auf der Engine, auf der sie
  läuft; der Wechsel zwischen Engines ist ein Dump und ein Laden
  ([Eine Website auf eine andere Engine umziehen](../datenbankadministratoren/zwischen-engines-wechseln.md)). Die
  Migrationen selbst funktionieren auf der neuen Engine weiter, wenn sie `$context->db()` und die Schema-Handler nutzen
  ([Hinweise zu den Datenbanken](rezepte/hinweise-zu-den-datenbanken.md)).
- **Sicherungspunkte dienen beiden.** Ein Migrationslauf und `exp:upgrade` nehmen dieselbe Art von Sicherungspunkt und
  teilen die Sperre der Installation, sodass ein Upgrade und ein Migrationslauf nie gleichzeitig schreiben
  ([Sicherungspunkte und Rücknahme in der Praxis](rezepte/sicherungspunkte-und-ruecknahme-in-der-praxis.md)).

## Die Seiten jeder Familie

**Migrationen** (dieser Bereich der Hilfe):
[Migrationen](README.md) · [die Anleitung](../../anleitungen/migrationen.md) · [das Tutorial](../../anleitungen/migrationen-tutorial.md) ·
[die Rezepte](rezepte/README.md) · [In der Administration](in-der-administration.md) · [Häufige Fragen](faq.md)

**Eine Site migrieren:**

- [Migration zu und von Exponential](../../installation/migration/README.md): der Einstieg, mit einer Anleitung je Quellsystem
- [Zu Exponential umziehen](../anwender-umsteiger/umziehen/README.md): für Organisationen, die Exponential einführen
- [Kunden migrieren: Überblick](../hosting/migrationen/README.md): für Hosting-Anbieter
- [Migration zu Exponential: Kursüberblick](../trainer/migration/README.md): der Kurs für Trainer
- [Upgrade](../../anleitungen/aktualisieren.md) und [Upgrade mit der Konsole](../../anleitungen/aktualisieren-mit-der-konsole.md)

## Verwandte Seiten

- [Migrationen: der Einstieg](README.md), [Alle Rezepte](rezepte/README.md), [Häufige Fragen](faq.md)
- [Die Anleitung](../../anleitungen/migrationen.md), [das Tutorial](../../anleitungen/migrationen-tutorial.md),
  [PHP-Migrationen](../../anleitungen/migrationen-php.md),
  [Zurücknehmen und Sicherungspunkte](../../anleitungen/migrationen-rueckgaengig-und-captions.md),
  [Das Migrations-Repository](../../anleitungen/migrationen-repositories.md)
- [In der Administration](in-der-administration.md), [Befehle](befehle.md), [Einstellungen](einstellungen.md),
  [Richtlinien, Ansichten und Ereignisse](richtlinien-ansichten-und-ereignisse.md)
- [Das Dateiformat](../../spezifikationen/6.0/migrationsdateiformat.md),
  [Die Funktion](../../funktionen/6.0/migrationen.md)
- [Wenn eine Migration scheitert](wenn-eine-migration-scheitert.md),
  [Fehlermeldungen](../../anleitungen/migrationen-fehlerbehebung.md)
- [Upgrade-Schritt oder Migration?](rezepte/upgrade-schritt-oder-migration.md)
- [Glossar](../../glossar.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
