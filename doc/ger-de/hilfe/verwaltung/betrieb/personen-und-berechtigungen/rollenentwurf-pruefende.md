---
title: "Rollenentwurf: Prüfer"
audience: government, admin, project-manager
category: public-sector
order: 90
summary: Eine nur lesende Rolle für die interne Revision, Informationssicherheitsbeauftragte und Schriftgutverantwortliche - der Audit-Trail nach Kanal, maskierte Logs, Rollenseiten und Versionsgeschichte - ohne jedes Recht, Inhalte, Einstellungen oder den Nachweis selbst zu ändern.
keywords: Prüfer, interne Revision, Informationssicherheitsbeauftragte, nur lesende Rolle, audit/read, Einschränkung Channel, Audit-Export, setup/logs, maskierte Logs, Beleg, Rechenschaft, Rollenentwurf, auditor, internal audit, information security officer, read-only role, Channel limitation, audit export, masked logs, evidence, accountability, role design
type: help
---
# Rollenentwurf: Prüfer

Diese Seite richtet sich an Administratoren, die einer internen Revision, einer oder einem Informationssicherheitsbeauftragten oder
den Schriftgutverantwortlichen Zugang zu den Belegen geben müssen, ohne jedes Recht, sie zu ändern. Der Audit-Trail von Exponential
ist dafür gemacht: zwei Richtlinien, `audit/read` (optional nach Kanal eingeschränkt) und `audit/manage`, und ein Log-Viewer, der
personenbezogene Daten standardmäßig maskiert ([Audit-Trail](../../../../../eng-us/features/6.0/audit-trail.md#who-may-look), [Log-Viewer](../../../../../eng-us/features/6.0/log-viewer.md#who-may-use-it)).

## Inhalt
1. [Was eine Prüferin oder ein Prüfer sehen muss](#was-eine-prüferin-oder-ein-prüfer-sehen-muss)
2. [Die Rolle „Prüfer (nur lesend)“](#die-rolle-prüfer-nur-lesend)
3. [Engere Varianten](#engere-varianten)
4. [Was außer Reichweite bleibt](#was-außer-reichweite-bleibt)
5. [Belege übergeben](#belege-übergeben)
6. [Verwandte Seiten](#verwandte-seiten)

## Was eine Prüferin oder ein Prüfer sehen muss

| Frage | Wo die Antwort steht |
|---|---|
| Wer sich angemeldet hat, bei wem es fehlschlug, wer abgewiesen wurde | Audit-Kanal `access` |
| Wer veröffentlicht, verschoben, entfernt, Sektionen oder Zustände geändert hat | Audit-Kanal `content` |
| Wer Einstellungen geändert, Caches geleert, eine Bereitstellung oder Wartung ausgeführt hat | Audit-Kanal `system` |
| Ob der Nachweis intakt ist | die Hash-Kette; „Jetzt prüfen“ braucht `audit/manage`, der Zustand wird mit `audit/read` angezeigt |
| Wer welche Rolle hat | **Benutzer > Rollen und Richtlinien**, Rollenseiten |
| Was eine Seite an einem bestimmten Datum aussagte | die Versionsgeschichte des Objekts |

## Die Rolle „Prüfer (nur lesend)“

| Modul | Funktion | Einschränkungen | Warum |
|---|---|---|---|
| user | login | SiteAccess: der Administrations-Siteaccess | an der Administration anmelden |
| audit | read | Channel: `access`, `content`, `system` (fügen Sie `commerce` hinzu, wenn Sie einen Shop betreiben) | der Tab **Audit**: Dashboard, Konsole, Ereignis, Diagramme, Alarme, Export |
| setup | logs | keine | **Setup > Logdateien**, Fehlerübersicht und Zustand der Logs, maskiert |
| setup | system_info | keine | **Setup > Systeminformationen** (liest, ändert nichts) |
| content | read | keine, oder die geprüften Bereiche | Inhalte sehen |
| content | versionread, diff | keine, oder die geprüften Bereiche | frühere Versionen lesen und vergleichen |

Zu den Rollen: Die Rollenseiten gehören zum Modul `role`, das keine getrennte Lesefunktion definiert, daher würde eine Richtlinie dafür
auch Änderungen erlauben. Geben Sie der prüfenden Person stattdessen einen Export der Rollenseiten oder lassen Sie sie sich von einem
Administrator zeigen; siehe [Überprüfung der Administrationszugänge](ueberpruefung-administrativer-zugaenge.md). Ebenso ist die Kontenliste
(**Benutzerkonten prüfen**, `user/hygiene`) nicht nur lesend: Dieselbe Funktion deaktiviert und aktiviert Konten, daher gehört sie
nicht zu dieser Rolle.

## Engere Varianten

| Prüfende Stelle | Änderung |
|---|---|
| Informationssicherheitsbeauftragte | `audit/read` eingeschränkt auf Channel `access` und `system`; fügen Sie `setup/unmasklogs` nur für eine Untersuchung hinzu und entfernen Sie es danach wieder |
| Schriftgutverantwortliche | `audit/read` eingeschränkt auf Channel `content`; `content/versionread` |
| Externe Prüfer für einen festen Zeitraum | dieselbe Rolle, einem persönlichen Konto zugewiesen, das bei Ende des Auftrags deaktiviert wird ([Rollenentwurf: externe Dienstleister](rollenentwurf-externe-dienstleister.md)) |

## Was außer Reichweite bleibt

- `audit/manage`: Archive, Audit-Einstellungen und **Jetzt prüfen**. Mit `[AuditConsoleSettings] ReauthForManage`
  (ausgeliefert als `disabled`) fragt „Jetzt prüfen“ erneut nach dem Passwort.
- Jedes Recht zum Schreiben von Inhalten, für Einstellungen, Cache oder Cronjobs.
- `setup/unmasklogs`: Ohne dieses Recht bleiben E-Mail- und IP-Adressen, Passwörter, Tokens und Sitzungskennungen maskiert.
- Lesezugriff auf nicht aufgeführte Kanäle: Ein Benutzer ohne `audit/read` für einen Kanal sieht ihn nicht, und eine eingetippte URL
  wird abgewiesen und als `access.permission.refused` erfasst.

## Belege übergeben

- **Audit > Export** (`/audit/export`) schreibt den Filter der Konsole als CSV, JSON Lines oder JSON: **Jetzt herunterladen**
  liefert ihn direkt aus, **Im Hintergrund exportieren** bereitet eine große Datei vor, die nur der anfordernde Benutzer herunterladen
  kann. CSV-Exporte werden selbst im Audit-Trail erfasst.
- Auf der Kommandozeile kann ein Administrator `./console exp:audit verify --allow-root-user` ausführen und die Ausgabe zusammen mit dem
  Export übergeben, als Beleg, dass die Kette intakt war ([Überprüfung des Audit-Trails](../ueberwachung/pruefung-des-audit-trails.md)).
- Personenbezogene Daten: Das Audit kürzt Adressen, hasht Sitzungen, entfernt Werte aus Abfragen und erfasst nie Passwörter oder Tokens
  ([Audit-Trail, „Warum Sie ihn wollen“](../../../../../eng-us/features/6.0/audit-trail.md#why-you-want-it)). Exporte können dennoch
  Benutzernamen enthalten; behandeln Sie sie nach Ihren Datenschutzregeln.

## Verwandte Seiten
- [Einer prüfenden Person Lesezugriff auf den Audit-Trail geben](../../../../../eng-us/help/government/tasks/people-and-permissions/give-an-auditor-read-access-to-the-audit-trail.md)
- [Audit-Einträge für eine Prüfung exportieren](../../../../../eng-us/help/government/tasks/operations/export-audit-records-for-an-auditor.md)
- [Audit-Alarme](../ueberwachung/audit-warnungen.md)
- [Leitfaden zu Sicherheit und Audit](../../../../anleitungen/sicherheit-und-audit.md#3-das-audit-protokoll-lesen-5-minuten)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
