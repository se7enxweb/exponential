---
title: "Aufbewahrung personenbezogener Daten: ein Überblick"
audience: government, admin, project-manager
category: public-sector
order: 80
summary: Für öffentliche Stellen, die Speicherfristen festlegen - jeder Aufbewahrungsmechanismus von Exponential (Audit, Protokolle, Einwilligungsprotokoll, ausstehende Bestätigungen, Sperrliste, Sitzungen, Versionen, Entwürfe) mit seiner Voreinstellung, wo nichts Daten automatisch entfernt, und wie Sie Ihren Aufbewahrungsplan in Einstellungen umsetzen.
keywords: Aufbewahrung, Speicherbegrenzung, DSGVO Artikel 5 Absatz 1 Buchstabe e, Aufbewahrungsplan, Aufbewahrung von Audit-Einträgen, Aufbewahrung von Protokollen, MaxAgeDays, Einwilligungsprotokoll RetentionDays, PendingDays, Sperrliste RetentionDays, SessionTimeout, Versionen, Entwürfe, Formulareinsendungen, öffentlicher Sektor, retention, storage limitation, retention schedule
type: reference
---
# Aufbewahrung personenbezogener Daten: ein Überblick

Diese Seite richtet sich an Datenschutzbeauftragte, Schriftgutverantwortliche und Administratoren. Speicherbegrenzung
bedeutet, dass personenbezogene Daten nicht länger als nötig aufbewahrt werden. Exponential hat
Aufbewahrungsmechanismen für seine eigenen Aufzeichnungen; mehrere Orte haben keine und brauchen eine manuelle Routine.
Nutzen Sie diese Tabelle, um Ihren Aufbewahrungsplan in Einstellungen umzusetzen.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Automatische Aufbewahrung](#automatische-aufbewahrung)
2. [Keine automatische Aufbewahrung](#keine-automatische-aufbewahrung)
3. [Vom Aufbewahrungsplan zu den Einstellungen](#vom-aufbewahrungsplan-zu-den-einstellungen)
4. [Verwandte Seiten](#verwandte-seiten)

## Automatische Aufbewahrung

| Daten | Mechanismus | Voreinstellung | Einstellung |
|---|---|---|---|
| Prüfprotokoll | täglicher Cronjob-Teil: archivieren, bereinigen | 90 Tage live, 730 archiviert (Kanal read 30/90) | `[AuditChannel_<channel>] LiveDays`, `ArchiveDays` |
| Audit-Index | täglich | Zeilen 730 Tage aufbewahrt, nach 90 pseudonymisiert | `[AuditIndexSettings] KeepDays`, `[AuditPrivacySettings] PseudonymiseAfterDays` |
| `404.log`, `security.log`, `csp.log` | Bereinigung nach Alter | 30, 90, 30 Tage | `logviewer.ini [RotationSettings] MaxAgeDays[<key>]` |
| Andere Kernel-Protokolle | Rotation nur nach Größe | 2 MB, 3 Archive; keine Altersgrenze | `site.ini [FileSettings] LogFileMaxSize`, `LogFileRotateCount`, `LogFileMaxAge`; pro Protokoll `MaxAgeDays[<key>]` |
| Einwilligungsprotokoll von Personen, die nicht mehr da sind | Cronjob-Teil `mailpreferences` (Gruppe `infrequent`) | 1095 Tage | `mailpreferences.ini [ConsentSettings] RetentionDays` |
| Ausstehende E-Mail-Bestätigungen | ebenso | 7 Tage | `[ConsentSettings] PendingDays` |
| Sperrliste | ebenso, wenn gesetzt | unbegrenzt aufbewahrt (`0`) | `[SuppressionSettings] RetentionDays` |
| Sitzungen | Ablauf | 3 Tage | `site.ini [Session] SessionTimeout` |
| Inhaltsversionen | wenn eine neue Version die Grenze überschreitet, wird die älteste archivierte Version entfernt | 45 pro Objekt (75 für Klasse 1) | `content.ini [VersionManagement]` |

Quellen: [Audit-Leitfaden 4.3](../../../bc/6.0/audit.md#43-aufbewahrung), [Protokollrotation](../../../funktionen/6.0/log-rotation.md),
[E-Mail-Einstellungen: Aufbewahrung](../../../anleitungen/mail-einstellungen-administrator.md#9-aufbewahrung-und-der-cronjob),
[Sitzungen](../../../anleitungen/sitzungen.md), [Versionen eines Objekts](../../../anleitungen/inhaltsverlauf.md).

## Keine automatische Aufbewahrung

| Daten | Was zu tun ist |
|---|---|
| Formulareinsendungen (Informationssammlungen) | nach Plan unter Setup > Gesammelte Informationen entfernen, bei Bedarf nach einem Export |
| Nicht mehr genutzte Benutzerkonten | mit „Benutzerkonten prüfen“ überprüfen; deaktivieren oder entfernen |
| Suchstatistiken | unter Setup > Suchstatistiken zurücksetzen |
| Papierkorb | nach Plan leeren (`content/trash`) |
| Exporte (CSV, JSON), die Menschen heruntergeladen haben | nach der Aufgabe löschen |
| Protokolle von Webserver, Velocity und request-shield | in diesen Programmen konfigurieren |
| Datensicherungen | Ihre Sicherungsrotation |

## Vom Aufbewahrungsplan zu den Einstellungen

1. Suchen Sie für jede Art von Unterlagen in Ihrem Aufbewahrungsplan die passende Zeile oben.
2. Wo es eine Einstellung gibt, setzen Sie sie in `settings/override/<file>.ini.append.php` (nie in der
   mitgelieferten Datei) und dokumentieren Sie die Änderung.
3. Stellen Sie sicher, dass die Cronjobs laufen: `frequent` (Audit) und `infrequent` (E-Mail-Einstellungen,
   Linkprüfung); fügen Sie den Teil `logs` hinzu, wenn Sie sich auf Altersgrenzen verlassen
   (`php runcronjobs.php logs`).
4. Schreiben Sie für Zeilen ohne Mechanismus eine Routine mit einer zuständigen Person und einer Häufigkeit.
5. Prüfen Sie jährlich, ob die Daten wirklich entfernt werden (Probeläufe: `./console exp:logs --prune --dry-run`,
   `./console exp:mail:consent cleanup --dry-run`, `./console exp:audit purge --dry-run`).

## Verwandte Seiten

- [Aufbewahrung von Audit-Einträgen](aufbewahrung-von-audit-eintraegen.md)
- [Aufbewahrung von Protokollen](aufbewahrung-von-protokollen.md)
- [Aufbewahrung von Protokollen und Daten für Hoster](../../hosting/sicherheit/aufbewahrung-von-logs-und-daten.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
