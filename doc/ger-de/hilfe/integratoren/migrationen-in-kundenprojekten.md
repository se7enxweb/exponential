---
title: "Migrationen in Kundenprojekten"
audience: integrator, partner, developer
category: migrations
order: 550
summary: Für Agenturen und Integratoren, die viele Kunden-Sites bauen und pflegen - Änderungen als Migrationen in einer Site oder in Ihrer eigenen Erweiterung ausliefern, Umgebungen je Kunde, eine Übergabe, die der Kunde in Git lesen kann, den Aufwand schätzen, Importe über die Remote-ID, eine Projekt-Checkliste und der Livegang.
keywords: Migration, Migrationen, Schemaänderung, Datenbankänderung, Auslieferung, Agentur, Integrator, Partner, Kundenprojekt, Erweiterung, Übergabe, Livegang, Aufwandsschätzung, Remote-ID, migration, migrations, schema change, database change, deploy, exp:migration:migrate, agency, integrator, client project, extension, extension:<name>, handover, go-live, estimate, remote id, status file, tags
type: help
---
# Migrationen in Kundenprojekten

Diese Seite ist für Agenturen und Integratoren, die Exponential-Sites für Kunden bauen und betreiben. Seit 6.0.15
können die Änderungen, die Sie an Datenbank, Inhalten und Einstellungen eines Kunden vornehmen, als Migrationen reisen:
einmal geschrieben, auf Ihrer Entwicklungskopie getestet, auf Staging und Produktion des Kunden in derselben
Reihenfolge angewendet und in Git aufgezeichnet, wo der Kunde sie sehen kann.

## Was sich für Ihre Projekte ändert

| Vor 6.0.15 | Mit Migrationen |
|---|---|
| eine Liste von Schritten in einem Ticket, auf jeder Umgebung durchgeklickt | eine Datei je Änderung, im Repository des Projekts |
| „Haben wir das auf Produktion ausgeführt?“ | `exp:migration:status` auf jeder Umgebung und `migrations/status/<umgebung>.json` in Git |
| ein Update-Skript, das jemand zweimal ausgeführt hat | die Aufzeichnung: eine Migration läuft einmal je Installation |
| ein Backup von Hand, vielleicht | ein Sicherungspunkt vor jedem Lauf, nach einem Fehler wiederhergestellt |
| Änderungen für jeden Kunden von Hand wiederholt | Migrationen in Ihrer eigenen Erweiterung, angewendet auf jeder Kunden-Site, die sie aktiviert |

## Zwei Orte für eine Migration

| Wo | Quelle | Namensraum aus `--skeleton` | Wofür |
|---|---|---|---|
| `migrations/` der Site | `site` | `Site\Migrations` | Änderungen für diesen einen Kunden |
| `extension/<name>/migrations/` Ihrer Erweiterung | `extension:<name>` | `Extension\<Name>\Migrations` | Änderungen, die jeder Kunde dieser Erweiterung bekommt |

```bash
./console exp:migration:generate --skeleton news_archive_folder --apply
./console exp:migration:generate --skeleton widget_table --source=extension:acmewidgets --apply
```

Die Migrationen einer Erweiterung werden nur gefunden, solange die Erweiterung aktiv ist. Eine Migration der Site, die
auf einer aufbaut, nennt sie im Kopf: `requires: 20261101090000_widget_table` oder `requires: extension:acmewidgets`.
Braucht jede Installation der Erweiterung die Änderung beim Upgrade, ob eine Site sie gewählt hat oder nicht, liefern
Sie stattdessen einen Upgrade-Schritt aus
([Upgrade-Schritt oder Migration?](../migrationen/rezepte/upgrade-schritt-oder-migration.md)).

## Umgebungen je Kunde

Jede Installation nennt ihre Umgebung in `site.ini [AdminEnvironment] Level` (`development`, `test`, `staging`,
`production` oder `auto` mit `EXP_ENV`). Ein Migrationskopf kann eine Migration einschränken:

```php
// environments: staging, production
// environments: !development
```

Startdaten für Ihre Entwicklungskopien, Testbenutzer, Demo-Inhalte: geben Sie diesen Migrationen
`environments: development`. [Migrationen je Umgebung](../migrationen/rezepte/migrationen-je-umgebung.md) erklärt die
Einzelheiten.

## Importe über die Remote-ID

Kunden-Sites unterscheiden sich in ihren Objekt- und Knoten-IDs. Finden Sie alles über Remote-ID oder Bezeichner,
und geben Sie allem, was eine Migration anlegt, eine eigene Remote-ID (`acme-news-folder`). Dieselbe Regel macht
Importe wiederholbar: [Idempotente Importe über die Remote-ID](idempotente-importe-ueber-die-remote-id.md). Eine so
geschriebene Migration läuft auf der Installation jedes Kunden und verträgt nach einem Fehler einen halb erledigten
Zustand.

## Übergabe

Nach jedem Lauf werden Statusdatei und Laufprotokoll committet, als `migrations/<umgebung>/<run-id>` getaggt und
gepusht. Bei der Übergabe bekommt der Kunde im Repository, das ihm gehört:

- `migrations/`: jede Änderung, mit Beschreibung und Autor im Kopf;
- `migrations/status/<umgebung>.json`: was jede Umgebung angewendet hat;
- `migrations/runs/`: das Protokoll jedes Laufs;
- die Tags: einer je Lauf, nie verschoben.

Setzen Sie `[RepositorySettings] Remote` auf das Repository des Kunden und geben Sie dem Systembenutzer jeder
Installation die Push-Zugangsdaten (einen Deploy-Schlüssel). Übergeben Sie die Richtlinien: wer in der Administration
des Kunden `migration/run` und `migration/rollback` hat
([Richtlinien, Ansichten und Ereignisse](../migrationen/richtlinien-ansichten-und-ereignisse.md)).

## Aufwand schätzen

| Arbeit | Was zu zählen ist |
|---|---|
| Schreiben | eine Migration je Änderung; Schema und Inhalte getrennt; `plan()`, `up()`, `detect()`, `down()` |
| Testen | `exp:migration:check` in der CI; eine Simulation auf SQLite oder PostgreSQL; ein Test von `up()`, `down()`, `up()` |
| Anwenden | ein Probelauf und ein Lauf je Umgebung; auf MySQL/MariaDB die Zeit eines Sicherungspunkts (ein Datenbank-Dump) |
| Große Änderungen | Stapel und Fortsetzen bei vielen Objekten ([Große Datenmigrationen](../migrationen/rezepte/grosse-datenmigrationen.md)) |

## Checkliste für Kundenprojekte

- [ ] Die Aufzeichnungstabellen gibt es auf jeder Umgebung (`6.0.15-migration-tables` ist in `exp:upgrade:status` `done`).
- [ ] Jede Installation nennt ihre Umgebung; `RunAsUser` ist der Login eines vorhandenen Administrators.
- [ ] git_manager ist aktiv, und das Remote nimmt Pushes von jeder Installation an; `migrationpush` ist geplant.
- [ ] Die CI führt `./console exp:migration:check` bei jedem Pull Request aus.
- [ ] Jede Migration findet Dinge über Remote-ID oder Bezeichner und sagt, wie sie zurückgenommen wird.
- [ ] Richtlinien: nur wer auf Produktion Migrationen ausführen soll, hat `migration/run`.
- [ ] Plattenplatz für Sicherungspunkte ist mit dem Hoster abgestimmt.

## Livegang

1. Frieren Sie die Migrationen ein, die live gehen; `exp:migration:check` besteht.
2. Auf Staging, aus einer Kopie der Produktion wiederhergestellt: `exp:migration:migrate`, dann `--apply`; prüfen
   Sie die Site.
3. Auf Produktion: den Code ausliefern, `./console exp:migration:migrate --apply`, die Caches ausliefern
   ([Mit Migrationen ausliefern](../migrationen/rezepte/mit-migrationen-ausliefern.md)).
4. `./console exp:migration:status --fail-on-pending` endet mit 0; der Push ist durchgegangen.
5. Nehmen Sie den Tag des Laufs in die Release-Notizen für den Kunden auf.

Die ganze Site eines Kunden von einem anderen System oder einem anderen Hoster umzuziehen, ist keine Migration in
diesem Sinn: [Migrationen oder Umzug einer Site](../migrationen/migrationen-oder-umzug-einer-site.md).

## Wie es weitergeht

- [Migrationen in einer Erweiterung oder einem Paket](../migrationen/rezepte/migrationen-in-einer-erweiterung-oder-einem-paket.md)
- [Teamarbeit mit Git](../migrationen/rezepte/teamarbeit-mit-git.md)
- [Tags pushen](../migrationen/rezepte/tags-pushen.md)
- Jetzt beginnen: [das Tutorial](../../anleitungen/migrationen-tutorial.md)

## Verwandte Seiten

- [Migrationen: der Einstieg](../migrationen/README.md)
- [Migrationen: die Anleitung](../../anleitungen/migrationen.md)
- [Migrationen: das Tutorial](../../anleitungen/migrationen-tutorial.md)
- [PHP-Migrationen](../../anleitungen/migrationen-php.md)
- [Migrationen in der Administration](../migrationen/in-der-administration.md)
- [Die Migrationsbefehle](../migrationen/befehle.md)
- [Die Migrationseinstellungen](../migrationen/einstellungen.md)
- [Das Migrationsdateiformat](../../spezifikationen/6.0/migrationsdateiformat.md)
- [Wenn eine Migration scheitert](../migrationen/wenn-eine-migration-scheitert.md)
- [Fehlerbehebung bei Migrationen](../../anleitungen/migrationen-fehlerbehebung.md)
- [Migrationen oder Umzug einer Site](../migrationen/migrationen-oder-umzug-einer-site.md)
- [Integratoren: hier beginnen](integratoren-hier-beginnen.md)
- [Eine Integration als Erweiterung bauen](eine-integration-als-erweiterung-bauen.md),
  [Übergabe einer Integration](uebergabe-einer-integration.md),
  [Integrationsaufwand schätzen](integrationsaufwand-schaetzen.md),
  [Checkliste für den Livegang einer Integration](checkliste-fuer-den-livegang-einer-integration.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
