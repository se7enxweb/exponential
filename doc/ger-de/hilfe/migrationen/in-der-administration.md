---
title: "Migrationen in der Administration"
audience: admin, developer
category: migrations
order: 10
summary: Wo die Migrationen einer Installation in der Administration erscheinen - Setup > Migrationen, der Dashboard-Block und die Website-Seitenleiste -, welche Seite was erklärt und welche Richtlinie jede braucht.
keywords: Migrationen, Migration, Setup > Migrationen, Modul migration, Dashboard-Block, Website-Seitenleiste, Richtlinien, Identität bestätigen, Lauf im Hintergrund, Administration, migrations, migration, Setup > Migrations, migration module, dashboard block, website sidebar, policies, migration/read, migration/run, migration/rollback, migration/edit, verify it's you, background run, administration
type: help
---
# Migrationen in der Administration

Migrationen sind Änderungen an einer Installation, einmal als Dateien geschrieben und auf jede Installation der Site in
derselben Reihenfolge angewendet ([Migrationen](README.md)). Die Administration zeigt und führt sie in
**Setup > Migrationen** aus (das Modul `migration`) und zeigt ihren Stand in einem Dashboard-Block und in der
Website-Seitenleiste.

## Inhalt

1. [Die Seiten](#die-seiten)
2. [Wer was darf](#wer-was-darf)
3. [Hier gestartete Läufe](#hier-gestartete-läufe)
4. [Was auf der Konsole geschieht](#was-auf-der-konsole-geschieht)
5. [Verwandte Seiten](#verwandte-seiten)

## Die Seiten

| Seite | Adresse | Wobei sie hilft | Hilfe |
|---|---|---|---|
| Stand | `/migration/status` | jede Migration und ihren Zustand sehen, die Sperre, die letzten Läufe | [Stand und Verlauf](status-und-verlauf.md) |
| Verlauf | `/migration/history` | jeder Lauf mit seinen Sicherungspunkten, Commit, Tag und Push; fortsetzen und zurücknehmen | [Der Verlauf](status-und-verlauf.md#der-verlauf) |
| Die Seite eines Laufs | `/migration/log/<run-id>` | seine Aufzeichnung, seine Migrationen, sein Laufprotokoll und sein Fortschritt, solange er läuft | [Die Seite eines Laufs](status-und-verlauf.md#die-seite-eines-laufs) |
| Probelauf | `/migration/dryrun` | was ein Lauf ändern würde; **Simulieren** führt ihn in einer Transaktion aus, die zurückgerollt wird | [Probelauf](ausfuehren-probelauf-und-rueckgaengig.md#probelauf) |
| Ausführen | `/migration/run` | die ausstehenden Migrationen ausführen, einzeln ausgewählt | [Ausführen](ausfuehren-probelauf-und-rueckgaengig.md#ausführen) |
| Rücknahme | `/migration/rollback/<target>` | einen Lauf, eine Migration oder einen früheren Sicherungspunkt zurücknehmen | [Zurücknehmen](ausfuehren-probelauf-und-rueckgaengig.md#zurücknehmen) |
| Aufzeichnung | `/migration/recording`, `/migration/recording/<id>` | von Hand gemachte Änderungen aufzeichnen, ein Fenster für die ganze Website, Migration erzeugen | [Änderungen aufzeichnen](aufzeichnung.md) |
| Redaktion (Editor) | `/migration/editor`, `/migration/editor/<migration-id>` | eine YAML-Migration mit Formularen schreiben, prüfen, ansehen, speichern | [Der Migrationseditor](der-migrationseditor.md) |
| Gegenstellen | `/migration/remotes`, `/migration/remote/<id>` | die Installationen, an die diese ihre Migrationen ausliefert: der Assistent, Verbindung testen | [Eine Gegenstelle anlegen, Schritt für Schritt](eine-gegenstelle-anlegen-schritt-fuer-schritt.md) |
| Deploys | `/migration/deploy`, `/migration/deployment/<deploy-id>` | an Gegenstellen ausliefern, gleichzeitig oder Stufe für Stufe, jede Gegenstelle live verfolgen, der Verlauf, ein Tor, eine Rücknahme | [Ausliefern, Schritt für Schritt](ausliefern-schritt-fuer-schritt.md) |
| Dashboard-Block | das Dashboard | ausstehend, fehlgeschlagen, verwaist, der laufende und der letzte Lauf | [Der Dashboard-Block](status-und-verlauf.md#der-dashboard-block) |
| Website-Seitenleiste | die Seiten der Site | derselbe Stand, nur lesend | [Migrationen in der Website-Seitenleiste](expwt-panel.md) |

Der Stand listet außerdem zuerst die Upgrade-Schritte des Kernels, als Migrationen der Quelle `kernel`: Ein Lauf führt
die ausstehenden der Stufen `database` und `scripts` vor den Migrationen der Site aus
([Die Schritte unter den Migrationen](../../anleitungen/aktualisieren-mit-der-konsole.md#die-schritte-unter-den-migrationen)).

## Wer was darf

| Richtlinie | Erlaubt |
|---|---|
| `migration/read` | den Stand, den Verlauf, die Seite eines Laufs, den Probelauf, den Dashboard-Block |
| `migration/run` | Ausführen, Fortsetzen und das Simulieren im Probelauf |
| `migration/rollback` | einen Lauf, eine Migration oder einen früheren Sicherungspunkt zurücknehmen |
| `migration/edit` | die Seite Aufzeichnung und Migration erzeugen, und der Migrationseditor |
| `migration/remotes` | die Gegenstellen anlegen, ändern und entfernen, an die Deploys gehen, und ihre Verbindung testen |
| `migration/deploy` | an Gegenstellen ausliefern, das Tor eines Deploys in Stufen freigeben, einen Deploy zurücknehmen |

In einer neuen Installation hat sie nur die Rolle Administrator. Ausführen, Fortsetzen, Simulieren und Zurücknehmen
fragen zuerst nach [„Identität bestätigen“](../../anleitungen/identitaet-bestaetigen.md). Die Richtlinien im Einzelnen:
[Richtlinien des Moduls migration](../../beispiele/richtlinien/migration.md); jede Ansicht, Richtlinie und jedes Ereignis
an einer Stelle: [Richtlinien, Ansichten und Ereignisse](richtlinien-ansichten-und-ereignisse.md).

## Hier gestartete Läufe

Ein Lauf, eine Fortsetzung oder eine Rücknahme, die in der Administration gestartet wird, läuft im Hintergrund: Die
Seite startet den Konsolenbefehl (`exp:migration:migrate --apply`, `exp:migration:resume --apply` oder
`exp:migration:rollback --apply`) als eigenen Prozess unter dem angemeldeten Benutzer und öffnet die Seite des Laufs,
die ihn live verfolgt. Der Lauf geht weiter, wenn die Seite geschlossen wird. Seine Dateien liegen in
`var/<site>/migration/jobs/`. Er braucht `proc_open` und ein PHP-Kommandozeilenprogramm
(`migration.ini [AdminSettings] PhpCliPath`); fehlen sie, sagt die Seite das, und die Konsole führt ihn aus. Ein Lauf
zur Zeit: Solange einer läuft oder `exp:upgrade` oder ein anderer Lauf die Sperre der Installation hält, lehnt die Seite
einen neuen ab.

## Was auf der Konsole geschieht

Migrationen sind Dateien in `migrations/` (oder `extension/<name>/migrations/`). Die Administration schreibt
YAML-Migrationen (der Editor, eine Aufzeichnung); PHP-, SQL- und INI-Migrationen, die Generatoren aus Inhalten,
Klassen, Rollen, Sektionen, Einstellungen und Unterschieden sowie Gerüste entstehen auf der Konsole mit
`./console exp:migration:generate` ([Migrationen erzeugen](migrationen-erzeugen.md)). Die Einstellungen stehen in
`settings/migration.ini` ([Befehle](befehle.md), [Einstellungen](einstellungen.md)).

Ein Rundgang durch jede Seite mit Bildschirmfotos: [Ein Rundgang durch Setup > Migrationen](rundgang-durch-setup-migrationen.md).

## Verwandte Seiten

- [Migrationen: der Einstieg](README.md), [Alle Rezepte](rezepte/README.md), [Häufige Fragen](faq.md)
- [Die Anleitung](../../anleitungen/migrationen.md), [das Tutorial](../../anleitungen/migrationen-tutorial.md),
  [PHP-Migrationen](../../anleitungen/migrationen-php.md),
  [Zurücknehmen und Sicherungspunkte](../../anleitungen/migrationen-rueckgaengig-und-captions.md),
  [Das Migrations-Repository](../../anleitungen/migrationen-repositories.md)
- [Befehle](befehle.md), [Einstellungen](einstellungen.md),
  [Richtlinien, Ansichten und Ereignisse](richtlinien-ansichten-und-ereignisse.md)
- [Das Dateiformat](../../spezifikationen/6.0/migrationsdateiformat.md),
  [Die Funktion](../../funktionen/6.0/migrationen.md)
- [Wenn eine Migration scheitert](wenn-eine-migration-scheitert.md),
  [Fehlermeldungen](../../anleitungen/migrationen-fehlerbehebung.md)
- [Migrationen oder Umzug einer Site](migrationen-oder-umzug-einer-site.md)
- [Migrationen für Administratoren](migrationen-fuer-administratoren.md)
- [Ein Rundgang durch die Administration](../rundgang-durch-die-administration.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
