---
title: Aufgaben im Betrieb
audience: government, admin, hosting, project-manager
category: public-sector
order: 10
summary: Index der Schritt-für-Schritt-Aufgaben für den Betrieb einer Exponential-Site im öffentlichen Sektor - Wartungsfenster, Sicherungen und Wiederherstellungen, Caches, Cronjobs, Logs, das Audit-Protokoll, Upgrade-Prüfungen, Deployments, Zertifikate, Datenbanken, Sicherheitsprüfungen und die monatliche Überprüfung.
keywords: Aufgaben, Anleitung, Betrieb, Wartung, Sicherung, Wiederherstellung, Cache, Cronjobs, Logs, Audit, Upgrade-Prüfung, Deployment, Zertifikat, Lese-Replikat, Failover, statischer Cache, Sicherheitsheader, Berechtigungen, Index, tasks, how to, operations, maintenance, backup, restore, upgrade check, deploy, certificate, read replica, failover, static cache, security headers, permissions
type: help
---
# Aufgaben im Betrieb

Dieser Ordner enthält kurze, nummerierte Abläufe für die Administratoren, die eine öffentliche Website betreiben. Jede
Aufgabe nennt, was Sie vor dem Beginn brauchen, gibt die genauen Befehle und Seiten der Administration an, sagt, wie Sie
das Ergebnis prüfen, und vermerkt, was in einen Änderungsnachweis gehört. Die Erklärungen dahinter stehen in
[Exponential im öffentlichen Sektor betreiben](../../../../../eng-us/help/government/operations/README.md); die Fassungen für Hosting-Unternehmen in
[Betrieb für Hoster](../../../hosting/betrieb/README.md).

## Inhalt
1. [Wartung, Sicherung und Wiederherstellung](#wartung-sicherung-und-wiederherstellung)
2. [Caches und Deployments](#caches-und-deployments)
3. [Cronjobs](#cronjobs)
4. [Logs und das Audit-Protokoll](#logs-und-das-audit-protokoll)
5. [Prüfungen und Nachweise](#prüfungen-und-nachweise)
6. [Zertifikate, Datenbanken und Konfiguration](#zertifikate-datenbanken-und-konfiguration)
7. [Empfohlene Reihenfolge](#empfohlene-reihenfolge)
8. [Verwandte Seiten](#verwandte-seiten)

## Wartung, Sicherung und Wiederherstellung

| Aufgabe | Die Antwort in einer Zeile |
|---|---|
| [Die Site für ein Upgrade in den Wartungsmodus versetzen](die-site-fuer-ein-upgrade-in-den-wartungsmodus-versetzen.md) | **Setup > Wartung** oder `maintenance.php on` mit einer Meldung, einer Dauer und erlaubten Adressen. |
| [Die Site wieder online bringen](die-site-wieder-online-bringen.md) | Ausschalten, die vorgeschalteten Caches leeren, 200 prüfen, Cron fortsetzen. |
| [Vor einer Änderung eine Sicherung erstellen](vor-einer-aenderung-eine-sicherung-erstellen.md) | Datenbank-Dump und Dateiarchiv innerhalb des Fensters. |
| [Eine SQLite-Site sicher sichern](eine-sqlite-site-sicher-sichern.md) | `sqlite3 ... ".backup ..."`, niemals eine Dateikopie. |
| [Die Sicherung der letzten Nacht auf einem Testserver wiederherstellen](die-sicherung-der-letzten-nacht-auf-einem-testserver-wiederherstellen.md) | Wiederherstellen mit dem Datei-Transport für E-Mails; die benötigte Zeit festhalten. |
| [Cronjobs während der Wartung anhalten](cronjobs-waehrend-der-wartung-anhalten.md) | **Anhalten** unter Setup > Cronjobs, mit einer Notiz; danach fortsetzen. |

## Caches und Deployments

| Aufgabe | Die Antwort in einer Zeile |
|---|---|
| [Caches nach einer Änderung an Einstellungen leeren](caches-nach-einer-aenderung-an-einstellungen-leeren.md) | `--clear-tag=ini`, neu laden oder ausrollen, Seiten-Caches zuletzt. |
| [Caches nach einer Änderung an Templates leeren](caches-nach-einer-aenderung-an-templates-leeren.md) | Template-Caches, Override-Cache für neue Templates, dann Seiten-Caches. |
| [Den Antwort-Cache von Velocity leeren](den-antwort-cache-von-velocity-leeren.md) | `exp:velocity cache clear`, nachdem der neue Code läuft. |
| [Die Caches nach einem Deployment vorwärmen](die-caches-nach-einem-deployment-vorwaermen.md) | `preload.php`, `warm.php` oder **Seiten vorladen**. |
| [Eine PHP-Änderung ausrollen](eine-php-aenderung-ausrollen.md) | `exp:velocity deploy --dry-run`, dann wirklich. |
| [Den statischen Cache einschalten](den-statischen-cache-einschalten.md) | Erzeugen, prüfen, `StaticCache=enabled`. |

## Cronjobs

| Aufgabe | Die Antwort in einer Zeile |
|---|---|
| [Prüfen, ob die Cronjobs laufen](pruefen-ob-die-cronjobs-laufen.md) | `crontab:list`, **Setup > Cronjobs**, die Statusleiste der Workflows. |
| [Einen Teil der Cronjobs von Hand ausführen](einen-teil-der-cronjobs-von-hand-ausfuehren.md) | **Teil starten** oder `runcronjobs.php --script=workflow.php`. |

## Logs und das Audit-Protokoll

| Aufgabe | Die Antwort in einer Zeile |
|---|---|
| [Die Fehlerübersicht lesen](die-fehleruebersicht-lesen.md) | Die täglichen fünf Minuten auf der **Fehlerübersicht**. |
| [Den Zustand der Logs prüfen](den-zustand-der-logs-pruefen.md) | Speicherbudget, Wachstum, Berechtigungen, wirksame Rotation. |
| [Logs rotieren und bereinigen](logs-rotieren-und-bereinigen.md) | `exp:logs --rotate-all`, `--prune`, immer zuerst `--dry-run`. |
| [Das Sicherheits-Log wöchentlich prüfen](das-sicherheits-log-woechentlich-pruefen.md) | Regeln, 200er mit hoher Konfidenz, Fluten, eine kurze Notiz. |
| [Einen Fehler anhand seiner Referenz finden](einen-fehler-anhand-seiner-referenz-finden.md) | `exp:logs --reference=ERR-...` oder die Suche des Viewers. |
| [Das Audit-Protokoll verifizieren](das-audit-protokoll-verifizieren.md) | `exp:audit verify`; Exit-Code 1 bedeutet gebrochen. |
| [Prüfen, wer Audit-Warnungen erhält](pruefen-wer-audit-warnungen-erhaelt.md) | `exp:audit alerts recipients`; ein Team-Postfach. |
| [Audit-Einträge für einen Prüfer exportieren](audit-eintraege-fuer-einen-pruefer-exportieren.md) | **Audit > Exportieren** oder `exp:audit export --format=bundle`. |

## Prüfungen und Nachweise

| Aufgabe | Die Antwort in einer Zeile |
|---|---|
| [Die Konsistenz nach einem Upgrade prüfen](die-konsistenz-nach-einem-upgrade-pruefen.md) | **Überprüfe Dateikonsistenz** oder `checkmanifest.php --all`. |
| [Das Datenbankschema prüfen](das-datenbankschema-pruefen.md) | **Überprüfe Datenbankkonsistenz**; SQL erst nach einer Sicherung anwenden. |
| [Systeminformationen für eine Supportanfrage lesen](systeminformationen-fuer-eine-supportanfrage-lesen.md) | **Bericht herunterladen (.txt)**, Geheimnisse ausgelassen. |
| [Die Sicherheitsheader prüfen](die-sicherheitsheader-pruefen.md) | Fünf Header, 404 für Punkt-Dateien, Cookie-Flags. |
| [Datei- und Verzeichnisberechtigungen prüfen](datei-und-verzeichnisberechtigungen-pruefen.md) | Nur `var/` beschreibbar, ein Eigentümer, gehärtete Modi. |
| [PHP-Erweiterungen vor einem Upgrade prüfen](php-erweiterungen-vor-einem-upgrade-pruefen.md) | `exp:velocity ext check` und das Log der Deprecations. |
| [Monatliche Betriebsüberprüfung](monatliche-betriebsueberpruefung.md) | Eine Stunde, fünfzehn Punkte, ein Nachweis. |

## Zertifikate, Datenbanken und Konfiguration

| Aufgabe | Die Antwort in einer Zeile |
|---|---|
| [Ein HTTPS-Zertifikat in Velocity erneuern](ein-https-zertifikat-in-velocity-erneuern.md) | `exp:velocity ssl show` und `ssl renew`; kein Neustart. |
| [Velocity auf Let's Encrypt umstellen](velocity-auf-lets-encrypt-umstellen.md) | `mode: letsencrypt`, zuerst das Staging-Verzeichnis. |
| [Ein Lese-Replikat hinzufügen](ein-lese-replikat-hinzufuegen.md) | `UseSlaveServer` bei MySQL oder MariaDB. |
| [Auf eine Standby-Datenbank umschalten](auf-eine-standby-datenbank-umschalten.md) | Kein automatisches Failover: `Server` ändern, INI leeren, ausrollen. |
| [Alle Sitzungen eines Benutzers beenden](alle-sitzungen-eines-benutzers-beenden.md) | Deaktivieren, das Passwort ändern oder Sitzungen entfernen. |
| [Umgebungsspezifische Einstellungen einrichten](umgebungsspezifische-einstellungen-einrichten.md) | `EXP_ENV` und Variantendateien. |
| [Die Konfiguration mit ManagedByDeployment sperren](die-konfiguration-mit-managedbydeployment-sperren.md) | Ein Schalter; Änderungen nur noch über das Deployment. |

## Empfohlene Reihenfolge

Vor dem Go-live: umgebungsspezifische Einstellungen, Berechtigungen, Sicherheitsheader, Empfänger der Audit-Warnungen,
Cronjobs, eine Sicherung und ein Test der Wiederherstellung, ManagedByDeployment. Dann die Routine: täglich die
Fehlerübersicht, wöchentlich das Sicherheits-Log, die monatliche Überprüfung. Halten Sie die Aufgaben für Wartung,
Deployment und Wiederherstellung für Änderungsfenster bereit.

Nach oben: [Aufgaben](../README.md) und [Exponential im öffentlichen Sektor betreiben](../../../../../eng-us/help/government/operations/README.md). Die
Einstiegsseite des Bereichs ist [Behörden: hier beginnen](../../verwaltungen-hier-beginnen.md).

## Verwandte Seiten
- [Aufgaben zu Personen und Berechtigungen](../personen-und-berechtigungen/README.md)
- [Vorfälle](../../../../../eng-us/help/government/operations/incidents/README.md)
- [Eine Site betreiben](../../../../anleitungen/betrieb-einer-website.md)
- [Checklisten für Hoster](../../../hosting/checklisten/README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
