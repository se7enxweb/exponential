---
title: Eine Deployment-Pipeline
audience: app-creator, developer, admin
category: app-creation
order: 540
summary: Eine Anwendung sicher vom Entwicklerrechner über den Test bis in die Produktivumgebung bringen - Umgebungen mit EXP_ENV, Git und Composer für den Code, Pakete und Upgrade-Schritte für Daten, der Deploy-Befehl, Wartungsmodus, Rollback und Prüfung.
keywords: Deployment-Pipeline, Staging, Produktivumgebung, Umgebungen, Release, Upgrade-Schritt, Wartungsmodus, Rollback, Prüfung, Anwendungsentwickler, deployment pipeline, staging, production, environments, EXP_ENV, Git, Composer, release, upgrade step, maintenance mode, rollback, verification, app creator
type: help
---
# Eine Deployment-Pipeline

Diese Seite ist für Anwendungsentwickler, die festlegen, wie Änderungen in die Produktivumgebung gelangen. Das Ziel:
derselbe Code und dieselben Schritte in jeder Umgebung, Einstellungen, die sich je Rechner unterscheiden, getrennt
gehalten, und ein Weg zurück, wenn etwas schiefgeht.

## Inhalt

1. [Umgebungen](#umgebungen)
2. [Was wie übertragen wird](#was-wie-übertragen-wird)
3. [Ein Release, Schritt für Schritt](#ein-release-schritt-für-schritt)
4. [Rollback](#rollback)
5. [Prüfung](#prüfung)

## Umgebungen

| Umgebung | Zweck | Einstellungen |
|---|---|---|
| Entwicklung | der Rechner jedes Entwicklers | `EXP_ENV=dev`: Debug an, Mailversand in Dateien |
| Test / Staging | eine Kopie der Produktivumgebung für die Abnahme | `EXP_ENV=test` |
| Produktivumgebung | die Live-Website | die Basiseinstellungen |

`EXP_ENV` benennt die Umgebung, und jede Einstellungsdatei kann eine Variante dafür haben (`site.ini.test.append.php`)
([Umgebungseinstellungen und Geheimnisse](umgebungseinstellungen-und-geheimnisse.md), [Einstellungen pro Umgebung](../../../../eng-us/features/6.0/environment-settings.md)).

## Was wie übertragen wird

| Gegenstand | Übertragen per |
|---|---|
| Kernel und Erweiterungen | Git und Composer (`composer.lock` im Projekt committet) |
| Ihre Erweiterung | ihr Git-Tag, per Version eingebunden |
| Einstellungen | Git, ohne Geheimnisse; Geheimnisse pro Rechner gesetzt |
| Inhaltsklassen | Pakete oder Upgrade-Skripte, nie durch doppeltes Klicken ([Inhaltspakete](pakete-und-ezpkg.md)) |
| Tabellen | Ihre Installations- und Upgrade-Befehle ([Datenbankschema und .dba-Dateien](schema-und-dba-dateien.md)) |
| Inhalte | bleiben in der Produktivumgebung; kopieren Sie die Produktivumgebung nach Staging, nie umgekehrt |

## Ein Release, Schritt für Schritt

1. Die Tests laufen in der CI durch ([Continuous Integration für Erweiterungen](ci-fuer-erweiterungen.md)).
2. Auf Staging ausliefern, die Upgrade-Schritte ausführen, testen.
3. Produktivumgebung: ein Backup anlegen ([Backups und Wiederherstellungen](../website-ersteller/backups-und-wiederherstellungen.md)).
4. Wartungsmodus, wenn das Release Daten ändert (`php bin/php/maintenance.php on --allow-admin`).
5. Den Code aktualisieren (Git pull oder `composer update <package>` des Projekts), die Upgrade-Schritte ausführen.
6. `./console exp:velocity deploy` (`--kernel`, wenn sich Kernel-Klassen geändert haben) ([Mit Velocity ausliefern](mit-velocity-ausliefern.md)).
7. Wartungsmodus aus; prüfen.

## Rollback

- Code: das vorherige Tag auschecken und erneut ausliefern.
- Daten: das vor dem Release angelegte Backup wiederherstellen, wenn Upgrade-Schritte Daten geändert haben; deshalb
  kommt Schritt 3 zuerst.
- Schreiben Sie Upgrade-Schritte nach Möglichkeit so, dass sie nur hinzufügen (Spalten, Klassen), damit der vorherige
  Code auch mit den neuen Daten noch läuft.

## Prüfung

- [ ] `exp:velocity status`: Der Server antwortet; `exp:cache status` endet mit PASS.
- [ ] Die Startseite, eine Seite jedes geänderten Typs, die Administration.
- [ ] Das Fehlerlog bleibt ruhig.
- [ ] **Setup > Upgrade-Prüfung** für die Dateikonsistenz ([Upgrade-Prüfung](../../../anleitungen/upgrade-pruefung.md)).
- [ ] Die Seite "Über" zeigt die erwarteten Versionen.

## Verwandte Seiten

- [Mit Velocity ausliefern](mit-velocity-ausliefern.md)
- [Ein Team mit Git führen](../../fuehrung/ein-team-mit-git-fuehren.md)
- [Kompatibilität, Releases und Deployment](../../../../eng-us/standards/architecture/compatibility-releases-deployment.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
