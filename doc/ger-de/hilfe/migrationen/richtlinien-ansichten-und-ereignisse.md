---
title: "Migrationen: Richtlinien, Ansichten und Ereignisse"
audience: developer, admin, software-engineer, php-developer
category: migrations
order: 22
summary: Die Richtlinien des Moduls migration und welche Ansichten sie prüfen, die sieben Ansichten mit ihren Adressen, der Dashboard-Block und seine Fetch-Funktion, die ezpEvent-Ereignisse eines Laufs mit ihren Parametern und die Audit-Ereignisse, die er schreibt - mit einem Listener-Beispiel und Links auf jede erzeugte Referenzseite.
keywords: Richtlinien, Richtlinienfunktionen, Rollen, Modul migration, Ansichten, Dashboard-Block, Ereignisse, Listener, Audit-Ereignisse, Identität bestätigen, migration/read, migration/run, migration/rollback, migration/edit, policies, policy functions, roles, module migration, views, status, history, log, stream, dryrun, run, rollback, dashboard block, fetch migration summary, ezpEvent, events, migration/run/before, migration/step/before, migration/step/after, migration/run/after, listener, audit events, system.migration.run, system.migration.apply, system.migration.fail, system.migration.rollback, system.migration.caption, system.migration.push, system.migration.http, system.migration.mail, system.migration.command, verify it's you
type: help
---
# Migrationen: Richtlinien, Ansichten und Ereignisse

Diese Seite listet alles, was ein Administrator rund um Migrationen vergibt und worauf ein Entwickler hört: die
Richtlinien des Moduls `migration`, seine Ansichten, den Dashboard-Block, die Ereignisse eines Laufs und die
Audit-Ereignisse, die er schreibt.

## Inhalt

1. [Richtlinien](#richtlinien)
2. [Ansichten](#ansichten)
3. [Der Dashboard-Block und die Fetch-Funktion](#der-dashboard-block-und-die-fetch-funktion)
4. [Ereignisse eines Laufs](#ereignisse-eines-laufs)
5. [Audit-Ereignisse](#audit-ereignisse)
6. [Verwandte Seiten](#verwandte-seiten)

## Richtlinien

| Richtlinie | Erlaubt | Geprüft von |
|---|---|---|
| `migration/read` | den Stand, den Verlauf, die Seite eines Laufs, den Probelauf, den Dashboard-Block, das Panel der Seitenleiste | den Ansichten `status`, `history`, `log`, `stream`, `dryrun` |
| `migration/run` | Ausführen, Fortsetzen und **Simulieren** im Probelauf | der Ansicht `run`, und `dryrun` für die Simulation |
| `migration/rollback` | einen Lauf, eine Migration oder einen früheren Sicherungspunkt zurücknehmen | der Ansicht `rollback` |
| `migration/edit` | Änderungen aufzeichnen (Setup > Migrationen > Aufzeichnung, siehe [Änderungen aufzeichnen](aufzeichnung.md)), Migrationen daraus erzeugen und YAML-Migrationen im Editor schreiben und ändern (Setup > Migrationen > Editor) | den Ansichten `recording` und `editor` |
| `migration/remotes` | Gegenstellen anlegen, ändern und entfernen, Verbindung testen ([Gegenstellen und Deploys](gegenstellen-und-deploys.md)) | der Ansicht `remote`, und `remotes` für Verbindung testen |
| `migration/deploy` | ein Preflight, ein Deploy, ein Tor freigeben, einen Deploy zurücknehmen | den Ansichten `deploy` und `deployment` für ihre Schaltflächen |

Keine von ihnen hat Einschränkungen: Jede gilt ganz oder gar nicht. In einer neuen Installation hat sie nur die Rolle
Administrator. Ausführen, Fortsetzen, Simulieren und Zurücknehmen fragen zuerst nach „Identität bestätigen“
(`site.ini [ReauthGroup_system]`, [Identität bestätigen](../../anleitungen/identitaet-bestaetigen.md)). Die Konsole prüft
keine Richtlinie: Wer `./console` auf dem Server ausführen kann, kann Migrationen ausführen, als der Benutzer
`migration.ini [RunSettings] RunAsUser` oder `--login=<login>`.

Wie eine eigene Migration einer Rolle Leserechte gibt, zeigt [Rollen und Richtlinien hinzufügen](rezepte/rollen-und-richtlinien-hinzufuegen.md):

```php
$role = \eZRole::fetchByName( 'Site operators' );
$role->appendPolicy( 'migration', 'read' );
\eZRole::expireCache();
```

Erzeugte Referenz: [Richtlinien des Moduls migration](../../beispiele/richtlinien/migration.md).

## Ansichten

| Ansicht | Adresse | Richtlinie | Was sie zeigt | Hilfe |
|---|---|---|---|---|
| [status](../../beispiele/modulansichten/migration/status.md) | `/migration/status` | read | jede Migration und ihren Zustand, die Sperre, die letzten Läufe | [Der Stand](status-und-verlauf.md#der-stand) |
| [history](../../beispiele/modulansichten/migration/history.md) | `/migration/history` | read | jeden Lauf mit seinen Sicherungspunkten, Commit, Tag und Push | [Der Verlauf](status-und-verlauf.md#der-verlauf) |
| [log](../../beispiele/modulansichten/migration/log.md) | `/migration/log/<run-id>` | read | einen Lauf: seine Aufzeichnung, Migrationen und sein Laufprotokoll | [Die Seite eines Laufs](status-und-verlauf.md#die-seite-eines-laufs) |
| [stream](../../beispiele/modulansichten/migration/stream.md) | `/migration/stream/<run-id>` | read | den Live-Fortschritt eines Laufs, wie ihn die Seite des Laufs liest | [Die Seite eines Laufs](status-und-verlauf.md#die-seite-eines-laufs) |
| [dryrun](../../beispiele/modulansichten/migration/dryrun.md) | `/migration/dryrun` | read (run zum Simulieren) | den Plan und die Simulation | [Probelauf](ausfuehren-probelauf-und-rueckgaengig.md#probelauf) |
| [run](../../beispiele/modulansichten/migration/run.md) | `/migration/run` | run | die ausstehenden Migrationen, die Schalter, den Start des Laufs | [Ausführen](ausfuehren-probelauf-und-rueckgaengig.md#ausführen) |
| [rollback](../../beispiele/modulansichten/migration/rollback.md) | `/migration/rollback/<target>` | rollback | einen Lauf, eine Migration oder einen Sicherungspunkt zum Wiederherstellen | [Zurücknehmen](ausfuehren-probelauf-und-rueckgaengig.md#zurücknehmen) |
| [remotes](../../beispiele/modulansichten/migration/remotes.md) | `/migration/remotes` | read (remotes zum Testen) | die Gegenstellen, Verbindung testen | [Gegenstellen](gegenstellen-und-deploys.md#gegenstellen) |
| [remote](../../beispiele/modulansichten/migration/remote.md) | `/migration/remote/<id>` | remotes | der Assistent einer Gegenstelle (neu oder gespeichert) | [Der Assistent](gegenstellen-und-deploys.md#der-assistent) |
| [deploy](../../beispiele/modulansichten/migration/deploy.md) | `/migration/deploy` | read (deploy, um einen zu starten) | das Formular eines Deploys und der Verlauf | [Deploys](gegenstellen-und-deploys.md#deploys) |
| [deployment](../../beispiele/modulansichten/migration/deployment.md) | `/migration/deployment/<deploy-id>` | read (deploy für ein Tor oder eine Rücknahme) | ein Deploy, live je Gegenstelle, sein Bericht, Tor und Rücknahme | [Die Seite eines Deploys](gegenstellen-und-deploys.md#die-seite-eines-deploys) |

Das Modul ist `Setup > Migrationen` im Menü (`menu.ini`, gezeigt für Benutzer mit `migration/read`). Erzeugte Referenz:
[Das Modul migration](../../beispiele/modulansichten/migration/README.md). Die Hilfe-Schaltfläche jeder Ansicht öffnet
die Seite in der letzten Spalte.

## Der Dashboard-Block und die Fetch-Funktion

Der Dashboard-Block `migrations` ([seine Einstellungen](../../beispiele/ini-einstellungen/dashboard/DashboardBlock_migrations.md))
zeigt, wie viele Migrationen ausstehen oder gescheitert sind, die verwaisten, den laufenden und den letzten Lauf, für
Benutzer mit `migration/read`. Er liest die Fetch-Funktion `migration/summary`
([Referenz](../../beispiele/fetch-funktionen/migration/summary.md)), die auch ein eigenes Template nutzen kann:

```tpl
{def $migrations = fetch( 'migration', 'summary' )}
{if $migrations.failed}
    <p>{$migrations.failed} migration(s) failed: see Setup > Migrations.</p>
{elseif $migrations.pending}
    <p>{$migrations.pending} migration(s) pending.</p>
{/if}
{undef $migrations}
```

Die Schlüssel sind `pending`, `failed`, `orphans`, `total`, `ready` (die Aufzeichnungstabellen existieren), `run` (der
letzte Lauf), `running` (ein in der Administration gestarteter Lauf, der noch läuft), `lock` (wer die Sperre der
Installation hält) und `problem`. Das Migrations-Panel der Website-Seitenleiste zeigt dasselbe
([Migrationen in der Website-Seitenleiste](expwt-panel.md)).

## Ereignisse eines Laufs

Der Runner benachrichtigt `ezpEvent`-Listener, registriert in `site.ini [Event] Listeners[]=<ereignis>@<callback>`. Ein
Listener, der eine Ausnahme wirft, hält den Lauf nicht an. Die Parameter, nach Position:

| Ereignis | Parameter | Wann |
|---|---|---|
| `migration/run/before` | `string $runId`, `string[] $ids`, `expMigrationContext $context` | vor der ersten Migration eines Laufs |
| `migration/step/before` | `string $runId`, `expMigration $migration`, `expMigrationContext $context` | vor jeder Migration (und vor jedem `down()` einer Rücknahme) |
| `migration/step/after` | `string $runId`, `expMigration $migration`, `array $entry`, `expMigrationContext $context` | nach jeder, mit ihrem Eintrag (Zustand, Aktion, Meldungen, Dauer) |
| `migration/run/after` | `string $runId`, `array $report`, `expMigrationContext $context` | nach dem Lauf, mit seinem Bericht (`exp-migration/1`) |

Ein Listener in einer Erweiterung, zum Beispiel einer, der das Ende jedes Laufs protokolliert:

```ini
<?php /* #?ini charset="utf-8"?
# extension/mysite/settings/site.ini.append.php
[Event]
Listeners[]=migration/run/after@mysiteMigrationNotifier::runAfter
*/ ?>
```

```php
<?php
class mysiteMigrationNotifier
{
    public static function runAfter( $runId, array $report, $context )
    {
        $summary = isset( $report['summary'] ) ? $report['summary'] : array();
        eZLog::write( sprintf( 'migration run %s in %s: exit %d, %d applied, %d failed', $runId,
                               $context->environment(), (int)$report['exit'],
                               isset( $summary['applied'] ) ? (int)$summary['applied'] : 0,
                               isset( $summary['failed'] ) ? (int)$summary['failed'] : 0 ), 'mysite-migrations.log' );
    }
}
```

## Audit-Ereignisse

Jeder Lauf schreibt in das Prüfprotokoll (Kanal `system`), eingeschaltet in `settings/audit.ini [AuditEventSettings]`
([Referenz](../../beispiele/ini-einstellungen/audit/AuditEventSettings.md)):

| Ereignis | Geschrieben, wenn |
|---|---|
| `system.migration.run` | ein Lauf endet, mit seiner ID, Umgebung, den Zahlen und dem Exit-Code |
| `system.migration.apply` | eine Migration angewendet wird; auch, wenn eine geänderte Datei mit `--force --reason` angenommen wird |
| `system.migration.fail` | eine Migration scheitert, mit ihrer Meldung |
| `system.migration.rollback` | ein Lauf, eine Migration oder ein Sicherungspunkt zurückgenommen wird |
| `system.migration.caption` | ein Sicherungspunkt genommen wird |
| `system.migration.push` | die Aufzeichnung eines Laufs gepusht wird oder der Push scheitert |
| `system.migration.http` | ein `http`-Schritt eine Anfrage sendet: Methode, Host und Pfad, Status (nie die Abfrage, ein Header oder der Inhalt) |
| `system.migration.mail` | ein `mail`-Schritt eine E-Mail sendet: wie viele Empfänger (nie eine Adresse oder der Text) |
| `system.migration.command` | ein `command`-Schritt einen Konsolenbefehl ausführt: sein Name und Exit-Code (nie seine Argumente oder Ausgabe) |

`exp:upgrade` schreibt eigene Ereignisse `system.upgrade.*`. Lesen Sie sie in der Audit-Konsole, oder leiten Sie sie über
die Senken des Prüfprotokolls weiter (E-Mail, Webhook) ([Die Audit-Spur der Läufe](rezepte/die-audit-spur-der-laeufe.md),
[Das Prüfprotokoll](../../funktionen/6.0/pruefprotokoll.md)).

## Verwandte Seiten

- [Migrationen: der Einstieg](README.md), [Alle Rezepte](rezepte/README.md), [Häufige Fragen](faq.md)
- [Die Anleitung](../../anleitungen/migrationen.md), [das Tutorial](../../anleitungen/migrationen-tutorial.md),
  [PHP-Migrationen](../../anleitungen/migrationen-php.md),
  [Zurücknehmen und Sicherungspunkte](../../anleitungen/migrationen-rueckgaengig-und-captions.md),
  [Das Migrations-Repository](../../anleitungen/migrationen-repositories.md)
- [In der Administration](in-der-administration.md), [Befehle](befehle.md), [Einstellungen](einstellungen.md)
- [Das Dateiformat](../../spezifikationen/6.0/migrationsdateiformat.md),
  [Die Funktion](../../funktionen/6.0/migrationen.md)
- [Wenn eine Migration scheitert](wenn-eine-migration-scheitert.md),
  [Fehlermeldungen](../../anleitungen/migrationen-fehlerbehebung.md)
- [Migrationen oder Umzug einer Site](migrationen-oder-umzug-einer-site.md)
- [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
