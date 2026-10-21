---
title: Einem Prüfer Lesezugriff auf das Audit-Protokoll geben
audience: government, admin
category: public-sector
order: 150
summary: Schritt für Schritt - eine schreibgeschützte Rolle „Auditor“ mit audit/read je Kanal, maskiertem Zugriff auf Logs und Lesen von Versionen anlegen, sie einer Gruppe der Prüfer zuweisen und prüfen, dass nichts geändert werden kann.
keywords: Prüfer, Auditor, audit/read, Einschränkung Channel, schreibgeschützte Rolle, Audit-Protokoll, Reiter Audit, Innenrevision, Sicherheitsbeauftragter, setup/logs, maskierte Logs, auditor, Channel limitation, read-only role, audit trail, Audit tab, internal audit, security officer, masked logs
type: help
---
# Einem Prüfer Lesezugriff auf das Audit-Protokoll geben

Diese Aufgabe ist für Administratoren. Am Ende kann ein Prüfer oder Sicherheitsbeauftragter den Reiter **Audit** und die
maskierten Logs lesen und weder Inhalte noch Einstellungen noch die Aufzeichnung ändern. Gestaltung:
[Rollengestaltung: Prüfer](../../betrieb/personen-und-berechtigungen/rollenentwurf-pruefende.md).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Die Rolle anlegen](#die-rolle-anlegen)
3. [Sie zuweisen](#sie-zuweisen)
4. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
5. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Ein Administratorkonto (in einer neuen Installation hat nur Administrator `audit/read` und `audit/manage`; mit dem
  Schutz vor Rechteausweitung können Sie nur vergeben, was Sie selbst haben).
- Der Prüfer hat ein persönliches Konto; für einen externen Prüfer ein Konto, das am Ende des Auftrags deaktiviert wird.
- Eine Entscheidung über die Kanäle: `access`, `content`, `system`, `commerce` (und `read`, wenn Lesezugriffe
  aufgezeichnet werden; der eigene Kanal einer Erweiterung wie `ai`, wenn relevant).

## Die Rolle anlegen

1. **Benutzer > Rollen und Richtlinien > Neue Rolle**, nennen Sie sie „Auditor (read only)“.
2. **Neue Richtlinie**: Modul `audit`, Funktion `read`, **Eingeschränkten Zugriff gewähren**, Channel: die gewählten
   Kanäle. **OK**.
3. **Neue Richtlinie**: `setup` / `logs`, **Vollen Zugriff gewähren** (für die Funktion).
4. **Neue Richtlinie**: `setup` / `system_info`.
5. **Neue Richtlinie**: `content` / `read` sowie `content` / `versionread` und `diff`, eingeschränkt auf die geprüften
   Bereiche oder uneingeschränkt.
6. **Neue Richtlinie**: `user` / `login`, SiteAccess: der Siteaccess der Administration.
7. **Speichern**.

Fügen Sie weder `audit/manage` noch `setup/unmasklogs`, das Modul `role` oder `user/hygiene` hinzu (das Konten
deaktivieren kann).

## Sie zuweisen

Legen Sie bei Bedarf eine Benutzergruppe „Auditors“ an, dann auf der Seite der Rolle **Zuweisen** und die Gruppe wählen.
Nehmen Sie das Konto des Prüfers in die Gruppe auf.

## Prüfen, ob es funktioniert hat

1. Melden Sie sich als Prüfer in einem privaten Fenster an.
2. Der Reiter **Audit** ist da: **Dashboard**, **Konsole**, **Diagramme**, **Alarme**, **Exportieren**. Nicht gewährte
   Kanäle werden nicht angezeigt.
3. **Archive** und **Einstellungen** (die `audit/manage` brauchen) werden abgelehnt; **Jetzt prüfen** wird nicht
   angeboten.
4. **Setup > Logdateien** öffnet sich mit maskierten Adressen; es gibt keinen Schalter **Unmaskiert zeigen**.
5. Keine Schaltfläche **Bearbeiten** an Inhalten.

Änderungsnachweis: Name der Rolle, gewährte Kanäle, Gruppe, Prüfer (Funktion und Organisation), Beginn und geplantes
Ende.

## Verwandte Seiten
- [Audit-Einträge für einen Prüfer exportieren](../betrieb/audit-eintraege-fuer-einen-pruefer-exportieren.md)
- [Zugriff auf Logs ohne Administratorrechte geben](zugriff-auf-logs-ohne-administratorrechte-geben.md)
- [Audit-Protokoll](../../../../funktionen/6.0/pruefprotokoll.md#wer-hineinsehen-darf)
- [Verifikation des Audit-Protokolls](../../betrieb/ueberwachung/pruefung-des-audit-trails.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
