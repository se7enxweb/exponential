---
title: Funktionstrennung
audience: government, admin, project-manager
category: public-sector
order: 140
summary: Wie Sie die Befugnisse über eine öffentliche Website auf Personen aufteilen - Autorinnen und Autoren, Freigebende, Kontenadministratoren, Systemadministratoren und Prüfer -, damit kein einzelnes Konto ungeprüfte Inhalte veröffentlichen, sich selbst Rechte geben und den Nachweis löschen kann, und wo Exponential Ihren Prozess braucht, um eine Lücke zu schließen.
keywords: Funktionstrennung, Aufgabentrennung, vier Augen, Vier-Augen-Prinzip, Interessenkonflikt, Administrator, Freigebende, Prüfer, Kontenadministrator, interne Kontrolle, Prüfungsfeststellung, separation of duties, segregation of duties, four eyes, two-person rule, conflict of interest, approver, auditor, account administrator, internal control, audit finding
type: help
---
# Funktionstrennung

Diese Seite richtet sich an Führungskräfte und Administratoren, die einem Prüfer zeigen müssen, dass keine einzelne Person eine öffentliche
Website von Anfang bis Ende kontrolliert. Sie nennt die Aufgaben, die Rechte in Exponential, die jede davon tragen, und die Stellen, an denen
ein Prozess die Software absichern muss.

## Inhalt
1. [Die Aufgaben](#die-aufgaben)
2. [Welche Rechte welche Aufgabe tragen](#welche-rechte-welche-aufgabe-tragen)
3. [Zu vermeidende Kombinationen](#zu-vermeidende-kombinationen)
4. [Wo die Software Ihren Prozess braucht](#wo-die-software-ihren-prozess-braucht)
5. [Kleine Teams](#kleine-teams)
6. [Verwandte Seiten](#verwandte-seiten)

## Die Aufgaben

| Aufgabe | Typische Inhaber |
|---|---|
| Inhalte erstellen | Redaktionen der Fachbereiche |
| Veröffentlichung freigeben | Freigebende, rechtlich Prüfende |
| Konten verwalten | Service-Desk oder Kontenadministratoren |
| Rollen vergeben und Einstellungen ändern | Systemadministratoren (wenige) |
| Die Plattform betreiben (Caches, Cronjobs, Logs) | Web-Team oder Betrieb |
| Den Nachweis prüfen | Prüfer, Informationssicherheitsbeauftragte |

## Welche Rechte welche Aufgabe tragen

| Aufgabe | Rechte in Exponential | Rollenseite |
|---|---|---|
| Erstellen | `content/create`, `edit` (und damit Veröffentlichen im Bearbeitungsformular) innerhalb eines Teilbaums | [Redaktionen der Fachbereiche](rollenentwurf-fachredaktionen.md) |
| Freigeben | in einem Freigabe-Ereignis genannt; Modul collaboration; `content/versionread` | [Freigebende](rollenentwurf-freigebende.md) |
| Konten verwalten | `user/hygiene`, `user/deactivate`, `content/edit` an den Benutzerobjekten | [Pflege der Benutzerkonten](pflege-von-benutzerkonten.md) |
| Rollen vergeben, Einstellungen | das Modul `role`, `setup/administrate`, voller Zugriff | [Grundsätze der geringsten Rechte](prinzip-der-geringsten-rechte.md) |
| Betreiben | `setup/managecache`, `managecronjobs`, `logs`, `system_info` | [Web-Team](rollenentwurf-webteam.md) |
| Prüfen | `audit/read` nach Kanal, `setup/logs`, `content/versionread` | [Prüfer](rollenentwurf-pruefende.md) |

## Zu vermeidende Kombinationen

| Kombination | Risiko | Kontrolle |
|---|---|---|
| Autorin oder Autor und einzige freigebende Person desselben Bereichs | Selbstfreigabe | eigene Inhalte von Freigebenden lässt das Freigabe-Ereignis durch; halten Sie Freigebende dort vom Erstellen fern, oder geben Sie ihnen einen getrennten Weg über ein Autorenkonto, den eine andere freigebende Person abdeckt ([Vier-Augen-Prinzip](../../../../../eng-us/help/government/operations/editorial-workflows/two-person-rule-for-publishing.md)) |
| Kontenadministration und Rollenvergabe | ein Konto anlegen und ihm beliebige Rechte geben | Modul `role` nur für Systemadministratoren; `PreventPrivilegeEscalation=enabled` begrenzt, was andere vergeben könnten |
| Voller Zugriff und Verwaltung des Audits | Einstellungen ändern und den Nachweis darüber auch | das Audit ist hashverkettet und wird geprüft (`exp:audit verify`); senden Sie Einträge an ein Syslog- oder Webhook-Ziel außerhalb des Servers ([Audit-Trail](../../../../../eng-us/features/6.0/audit-trail.md#sinks-alerts-and-archives)) |
| Betrieb und unmaskierte Logs | beiläufiger Zugriff auf personenbezogene Daten | `setup/unmasklogs` nur während Untersuchungen |

## Wo die Software Ihren Prozess braucht

- **Administratoren können alles.** Eine Richtlinie über alle Module kann nicht eingeschränkt werden. Halten Sie Administratorkonten
  persönlich und wenige, verlangen Sie die zweistufige Anmeldung, und verlassen Sie sich auf den Audit-Trail und den Alarm `admin_role_granted`.
- **Der Serverbetreiber steht außerhalb der Rollen.** Befehle auf der Kommandozeile prüfen keine Richtlinie; zum Beispiel ist
  `./console exp:logs` „der Betreiber des Servers: Es wird keine Richtlinie geprüft“ ([Log-Viewer](../../../../../eng-us/features/6.0/log-viewer.md#on-the-command-line)).
  Der Zugang zur Kommandozeile ist eine eigene Aufgabe, geregelt durch Ihre Regeln für den Serverzugang.
- **Der Audit-Trail macht Manipulationen sichtbar, verhindert sie aber nicht.** Wer Zugang zur Kommandozeile hat, könnte Dateien löschen; die
  Hash-Kette und signierte Manifeste zeigen das, und ein externes Ziel bewahrt eine Kopie auf
  ([Überprüfung des Audit-Trails](../ueberwachung/pruefung-des-audit-trails.md)).
- **Die Freigabe von Einstellungsänderungen** liegt außerhalb von Exponential: Legen Sie Einstellungen in die Versionsverwaltung und sperren Sie
  sie auf der Live-Website mit [ManagedByDeployment](../../../../../eng-us/help/government/operations/upgrades-and-change/managed-by-deployment.md), damit Änderungen durch Ihren
  Änderungsbeirat gehen.

## Kleine Teams

Ein Team aus zwei oder drei Personen kann nicht alles trennen. Übliche Kompromisse: Eine Person hat vollen Zugriff, veröffentlicht aber über
ein Autorenkonto, das eine Freigabe braucht; die zweite Person gibt frei und prüft monatlich den Audit-Export; eine externe Stelle (ein anderer
Fachbereich, die oder der Informationssicherheitsbeauftragte) liest vierteljährlich den Kanal `access`. Schreiben Sie den Kompromiss auf,
damit ein Prüfer sieht, dass er entschieden und nicht übersehen wurde.

## Verwandte Seiten
- [Rollen, Richtlinien und Einschränkungen](rollen-richtlinien-und-einschraenkungen.md)
- [Überprüfung der Administrationszugänge](ueberpruefung-administrativer-zugaenge.md)
- [Änderungsmanagement und Änderungsbeiräte](../../../../../eng-us/help/government/operations/upgrades-and-change/change-management-and-advisory-boards.md)
- [Leitfaden zu Sicherheit und Audit](../../../../anleitungen/sicherheit-und-audit.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
