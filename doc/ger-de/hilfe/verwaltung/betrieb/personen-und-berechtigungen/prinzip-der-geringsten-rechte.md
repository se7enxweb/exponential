---
title: Grundsätze der geringsten Rechte
audience: government, admin, project-manager
category: public-sector
order: 50
summary: Sieben Arbeitsregeln, um Beschäftigten im öffentlichen Sektor in Exponential nur den Zugang zu geben, den sie brauchen, jeweils verknüpft mit der Funktion, die sie unterstützt, und mit den Rechten, die besondere Sorgfalt verlangen.
keywords: geringste Rechte, minimaler Zugang, Kenntnis nur bei Bedarf, Administratorrolle, voller Zugriff, Rollenentwurf, Rechteausweitung, Sicherheitsrichtlinie, Richtlinie zur Zugriffskontrolle, NIST, ISO 27001, least privilege, minimum access, need to know, administrator role, full access, role design, privilege escalation, security policy, access control policy
type: help
---
# Grundsätze der geringsten Rechte

Diese Seite richtet sich an Administratoren und Informationssicherheitsbeauftragte, die eine Zugriffsrichtlinie für eine öffentliche
Website schreiben oder anwenden. Geringste Rechte bedeutet, dass jedes Konto nur das hat, was seine Arbeit braucht. Viele öffentliche
Stellen haben das in ihrer Sicherheitsrichtlinie, oft aus Rahmenwerken wie dem [NIST Cybersecurity Framework](https://www.nist.gov/cyberframework)
oder [ISO/IEC 27001](https://www.iso.org/standard/27001). Unten stehen Arbeitsregeln für Exponential, jeweils mit der Funktion,
die sie unterstützt.

## Inhalt
1. [Sieben Regeln](#sieben-regeln)
2. [Rechte, die besondere Sorgfalt verlangen](#rechte-die-besondere-sorgfalt-verlangen)
3. [Was die Administration für Sie tut](#was-die-administration-für-sie-tut)
4. [Eine Zugriffsrichtlinie auf einer Seite](#eine-zugriffsrichtlinie-auf-einer-seite)
5. [Verwandte Seiten](#verwandte-seiten)

## Sieben Regeln

1. **Beginnen Sie bei null.** Ein Benutzer hat nur, was Rollen ihm geben; standardmäßig wird nichts gewährt außer über die Rollen
   der Gruppen, in denen der Benutzer ist. Entwerfen Sie Rollen, indem Sie Richtlinien hinzufügen, niemals, indem Sie eine Kopie
   von Administrator beschneiden.
2. **Erweitern Sie Administrator nie.** „Bearbeiten Sie nie die Rolle Administrator, um einer Kollegin oder einem Kollegen etwas
   mehr zu geben; legen Sie eine neue Rolle an“ ([Sicherheit und Audit, Schritt 2](../../../../anleitungen/sicherheit-und-audit.md#2-nur-den-zugriff-vergeben-den-personen-brauchen-10-minuten)).
   Halten Sie die Zahl der Administratoren mit vollem Zugriff klein; die Vergabe der Rolle löst einen Audit-Alarm aus.
3. **Schränken Sie jede Inhaltsrichtlinie ein.** Schränken Sie `content/edit`, `create`, `remove` und `publish` nach Teilbaum,
   Sektion oder Klasse ein. Ein Bearbeitungsrecht öffnet auch die Versionen des Objekts, schränken Sie Bearbeiten also so ein wie Lesen
   ([Sicherheit und Audit, Schritt 2](../../../../anleitungen/sicherheit-und-audit.md#2-nur-den-zugriff-vergeben-den-personen-brauchen-10-minuten)).
4. **Weisen Sie Gruppen zu, mit Einschränkungen.** Eine Rolle je Tätigkeit, der Gruppe eines Fachbereichs mit **Mit Einschränkungen
   zuordnen** zugewiesen; Personen treten Gruppen bei und verlassen sie, Rollen bleiben stabil.
5. **Trennen Sie die Anmeldung.** `user/login` nimmt eine Einschränkung `SiteAccess` an: Beschäftigten, die nur bearbeiten, kann der
   Redaktions- oder öffentliche Siteaccess erlaubt und der Administrations-Siteaccess verwehrt werden
   ([Redaktionen aus dem Administrations-Siteaccess heraushalten](../../../../../eng-us/help/government/tasks/people-and-permissions/keep-editors-out-of-the-admin-siteaccess.md)).
6. **Vergeben Sie Betriebsrechte einzeln.** Das Modul `setup` hat getrennte Funktionen: `logs` (Logs lesen), `managelogs`,
   `unmasklogs`, `managecache`, `managecronjobs`, `system_info`, `administrate`. Wer Caches leert, braucht `setup/managecache`,
   nicht das ganze Modul.
7. **Überprüfen Sie nach Plan.** Zugriffsrechte wuchern mit der Zeit; eine [Überprüfung der Administrationszugänge](ueberpruefung-administrativer-zugaenge.md)
   zweimal im Jahr (oder so, wie Ihre Richtlinie es vorgibt) deckt das auf.

## Rechte, die besondere Sorgfalt verlangen

| Recht | Warum | Vergeben an |
|---|---|---|
| jede Richtlinie über alle Module | voller Zugriff, Kennzeichen *Voller Zugriff* | wenige namentlich benannte Administratorkonten |
| das Modul `role` | lässt die Inhaberin oder den Inhaber Rollen ändern und sich damit mehr geben; Kennzeichen *Kann Rollen ändern* | nur Administratoren |
| `setup/unmasklogs` | zeigt E-Mail- und IP-Adressen in Logs unmaskiert | die Personen, die Vorfälle untersuchen, wenn nötig |
| `user/hygiene` | wer es hat, sieht Name, Anmeldenamen, Zustand, Gruppen und Rollen jedes Kontos | Kontenadministratoren, nicht Redaktionen ([Benutzerkonten prüfen](../../../../../eng-us/features/6.0/user-hygiene.md#who-may-use-it)) |
| `role/assign` | nötig, um unter Benutzerkonten prüfen E-Mail-Adressen zu sehen; das Modul `role` definiert in `kernel/role/module.php` keine getrennten Funktionen, in der Praxis bedeutet das also eine Richtlinie über das ganze Modul `role` | nur Administratoren; Kontenadministratoren ohne dieses Recht sehen Konten ohne Adressen |
| `audit/manage` | Archive, Audit-Einstellungen, „Jetzt prüfen“ | die verantwortliche Person für das Audit |
| `content/cleantrash` | leert den Papierkorb endgültig | Administratoren |
| `section/edit` | legt Sektionen an und entfernt sie; das Entfernen kann nicht rückgängig gemacht werden | Administratoren |

## Was die Administration für Sie tut

- **Keine Rechteausweitung:** Mit `PreventPrivilegeEscalation=enabled` (Standard) kann niemand mehr vergeben, als er selbst hat
  ([Leitfaden zu Rollen, „Wer das tun darf“](../../../../anleitungen/rollen-und-richtlinien.md#wer-das-darf)).
- **Kennzeichen:** Die Rollenliste markiert *Voller Zugriff*, *Kann Rollen ändern* und *Nicht zugewiesen*.
- **Audit:** Änderungen an Berechtigungen werden erfasst; die Regel `admin_role_granted` sendet einen Alarm
  ([Audit-Alarme](../ueberwachung/audit-warnungen.md)).
- **Kontenprüfung:** **Benutzer > Benutzerkonten prüfen** listet Konten, die nie angemeldet waren, seit N Tagen nicht gesehen wurden,
  gesperrt sind oder eine Adresse teilen ([Pflege der Benutzerkonten](pflege-von-benutzerkonten.md)).

## Eine Zugriffsrichtlinie auf einer Seite

Eine Vorlage für die Regeln, die Sie übernehmen (tragen Sie Ihre eigenen Werte ein):

| Thema | Ihre Regel |
|---|---|
| Wer vollen Zugriff haben darf | z. B. zwei namentlich benannte Administratorkonten des Web-Teams, persönlich, nie geteilt |
| Wie Rollen beantragt werden | z. B. Ticket, freigegeben von der für den Bereich inhaltlich verantwortlichen Person |
| Standardrolle für Redaktionen | z. B. „Fachbereichsredaktion“, eingeschränkt auf den Teilbaum des Fachbereichs |
| Anmeldung an der Administration | z. B. nur Web-Team und Freigebende; Redaktionen nutzen den Redaktions-Siteaccess |
| Zweistufige Anmeldung | z. B. Pflicht für jede Rolle, die veröffentlichen oder Rollen ändern kann |
| Überprüfungsintervall | z. B. alle sechs Monate und wenn ein Vertrag endet |
| Austritte | z. B. am letzten Arbeitstag deaktiviert, innerhalb einer Woche deaktiviert und verschoben |

## Verwandte Seiten
- [Rollen, Richtlinien und Einschränkungen](rollen-richtlinien-und-einschraenkungen.md)
- [Funktionstrennung](funktionstrennung.md)
- [Die Administration einschränken (Hosting)](../../../hosting/sicherheit/die-administration-einschraenken.md)
- [Sicherheitsstandard](../../../../../eng-us/standards/security.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
