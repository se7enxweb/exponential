---
title: "Eine Migration im Code-Review prüfen"
audience: developer, software-engineer, php-developer, integrator
category: migrations
order: 14
summary: Eine Checkliste für das Review einer Migration in einem Pull Request - Datei und Kopf, Reihenfolge, plan(), up() und down(), Engines und Transaktionen, IDs, die sich zwischen Installationen unterscheiden, personenbezogene Daten und was die Prüferin vor der Freigabe ausführt.
keywords: Migration, Migrationen, Code-Review, Review, Pull Request, Checkliste, Prüfung, Kopf, Schemaänderung, Datenbankänderung, Auslieferung, migration, migrations, code review, review, pull request, checklist, reviewer, header, requires, down, plan, schema change, database change, deploy, exp:migration:check, exp:migration:dry-run
type: help
---
# Eine Migration im Code-Review prüfen

Eine Migration ist Code, der einmal in der Produktion läuft und sich danach nie mehr ändern darf. Darum ist das
Review der letzte günstige Moment, einen Fehler zu finden. Diese Seite ist die Checkliste, die eine Prüferin bei
einem Pull Request durchgeht, der eine Datei in `migrations/` oder `extension/<name>/migrations/` hinzufügt.

## Was Sie am Ende haben

- ein Review, das die Datei, ihre Reihenfolge, ihre Änderung, ihre Rücknahme und ihre Umgebungen abdeckt;
- die Befehle, die die Prüferin vor der Freigabe auf einer Entwicklungsinstallation ausführt.

## Bevor Sie beginnen

Checken Sie den Branch des Pull Requests auf einer Entwicklungsinstallation mit einer aktuellen Kopie der Daten der
Site aus.

## Schritte

### 1. Die Datei und der Kopf

- [ ] Der Name ist `YYYYMMDDHHMMSS_name.php`, der Name sagt, was sie tut (`news_note_priority`, nicht `fix2`).
- [ ] Die Datei ist **neu**. Ein Pull Request, der eine Migrationsdatei ändert oder umbenennt, die irgendwo gelaufen
      ist, wird abgelehnt: Die Prüfsummen-Sperre hielte dort jeden Lauf an. Die Änderung gehört in eine neue
      Migration.
- [ ] `description:` ist eine klare Zeile; sie erscheint im Status, in der Administration und in der Commit-Meldung
      des Laufs.
- [ ] `requires:` nennt jede Migration (und jeden Kernel-Schritt und jedes `extension:<name>`), deren Ergebnis diese
      nutzt.
- [ ] `environments:` und `engines:` stehen nur da, wo die Änderung wirklich beschränkt ist. Beispieldaten kommen
      nie aus Versehen in die Produktion: Achten Sie dort auf `environments: development, test`.
- [ ] `maintenance: yes`, wo Besucher die Änderung nicht halb fertig sehen dürfen.
- [ ] Eine Klasse in einem Namensraum, die `\expMigrationPhp` erweitert, nichts außerhalb der Klasse.

### 2. Die Reihenfolge

- [ ] Der Zeitstempel ist später als jede Migration, die schon auf dem Hauptbranch ist. Ein Branch, der Wochen
      gewartet hat, kann einen alten Zeitstempel tragen: Er sortiert dann vor Migrationen, die schon gelaufen sind;
      das ist nur in Ordnung, wenn darin nichts von ihnen abhängt. Sonst verlangen Sie `requires:` (siehe
      [Teamarbeit mit Git](teamarbeit-mit-git.md)).

### 3. plan(), up() und down()

- [ ] `plan()` schreibt nichts und ist günstig: Es läuft bei jedem Probelauf und auf jeder Statusseite.
- [ ] `up()` nutzt `$context->db()` und den Benutzer des Laufs, nie eine eigene Verbindung oder einen eigenen
      Benutzer.
- [ ] `up()` wirft bei einem Fehler eine Ausnahme, statt still zurückzukehren; Rückgabewerte von Kernel-Aufrufen
      werden geprüft (`createAndPublishObject()`, `updateAndPublishObject()` geben bei einem Fehler `false` oder
      `null` zurück).
- [ ] Was `up()` anlegt, bekommt eine Remote-ID oder einen Bezeichner, damit `down()` und spätere Migrationen es auf
      jeder Installation finden. Keine Objekt-, Knoten- oder Klassen-IDs in der Datei.
- [ ] `down()` nimmt genau `up()` zurück und ist zweimal ausführbar, oder es fehlt absichtlich, mit einem Grund in
      der Beschreibung ([Ein umkehrbares down() schreiben](ein-umkehrbares-down-schreiben.md)).
- [ ] Die zurückgegebenen Meldungen sagen, was getan wurde, und enthalten kein Geheimnis, kein Passwort und keine
      E-Mail-Adresse.

### 4. Engines, Transaktionen und Größe

- [ ] Rohes SQL funktioniert auf jeder Engine, die der Kopf zulässt, oder `engines:` beschränkt es
      ([Hinweise zu den Datenbanken](hinweise-zu-den-datenbanken.md)).
- [ ] Auf MySQL/MariaDB und Oracle wird eine Schemaänderung sofort committet: Ist die Reihenfolge der Anweisungen die
      am wenigsten schädliche, wenn sie mittendrin scheitert?
- [ ] Eine Migration über Tausende Zeilen arbeitet in Stapeln, gibt bei `transactional()` `false` zurück und ist
      wiederholbar ([Große Datenmigrationen](grosse-datenmigrationen.md)).

### 5. Daten und Datenschutz

- [ ] Keine personenbezogenen Daten echter Menschen in der Datei: Sie wird committet und gepusht. Konten werden mit
      Platzhalteradressen angelegt oder lesen ihre Werte aus Einstellungen, die nicht in Git liegen.
- [ ] Kein Passwort, kein Token, kein Schlüssel in der Datei.

## Prüfen

Die Prüferin führt auf dem Branch aus:

```bash
./console exp:migration:check
./console exp:migration:migrate
./console exp:migration:dry-run --simulate --diff      # SQLite oder PostgreSQL
./console exp:migration:migrate --apply --no-commit
./console exp:migration:rollback 20261101110000_news_note_priority --apply
./console exp:migration:migrate --apply --no-commit
```

und liest den Diff der Simulation: Jede geänderte Tabelle muss eine sein, die die Beschreibung verspricht.

## Zurücknehmen

Die lokalen Läufe einer Prüferin werden mit `exp:migration:rollback <migration-id> --apply` zurückgenommen, eine
Migration ohne `down()` mit dem Sicherungspunkt des Laufs: `./console exp:migration:rollback <run-id> --apply`.

## Nächster Schritt

[Teamarbeit mit Git](teamarbeit-mit-git.md): Branches, Konflikte und die Reihenfolge der Migrationen mehrerer
Entwicklerinnen und Entwickler.

## Verwandte Seiten

- [Migrationen: der Einstieg](../README.md) · [Alle Rezepte](README.md)
- [Die Anleitung](../../../anleitungen/migrationen.md), [das Tutorial](../../../anleitungen/migrationen-tutorial.md), [PHP-Migrationen](../../../anleitungen/migrationen-php.md)
- [In der Administration](../in-der-administration.md), [Befehle](../befehle.md), [Einstellungen](../einstellungen.md)
- [Das Dateiformat](../../../spezifikationen/6.0/migrationsdateiformat.md)
- [Wenn eine Migration scheitert](../wenn-eine-migration-scheitert.md), [Meldungen und ihre Behebung](../../../anleitungen/migrationen-fehlerbehebung.md)
- [Migrationen oder Umzug einer Site](../migrationen-oder-umzug-einer-site.md)
- [Migrationen testen](migrationen-testen.md), [Mit Git entwickeln](../../mit-git-entwickeln.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
