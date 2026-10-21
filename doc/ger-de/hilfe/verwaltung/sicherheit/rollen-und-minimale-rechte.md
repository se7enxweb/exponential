---
title: Rollen und minimale Rechte
audience: government, admin, project-manager
category: public-sector
order: 210
summary: Für öffentliche Stellen, die für die Beschäftigten ihrer Website das Prinzip der minimalen Rechte anwenden müssen - wie Rollen, Richtlinien und Einschränkungen in Exponential funktionieren, welche Richtlinien sicherheitsrelevant sind, ein Beispiel für ein Rollenkonzept einer öffentlichen Stelle, ausscheidende Beschäftigte und regelmäßige Zugriffsüberprüfungen mit Benutzerkonten prüfen.
keywords: Rollen, Richtlinien, minimale Rechte, Einschränkungen, Sektion, Teilbaum, Rolle Administrator, Zugriffsüberprüfung, ausscheidende Beschäftigte, Deaktivieren und verschieben, ein Konto je Person, öffentlicher Sektor, roles, policies, least privilege, limitations, section, subtree, Administrator role, audit/read, setup/setup, user/hygiene, access review, leavers, Deactivate and move, one account per person, public sector
type: help
---
# Rollen und minimale Rechte

Diese Seite richtet sich an Administratorinnen und Administratoren sowie Projektverantwortliche, die entscheiden, wer
auf einer öffentlichen Website was tun darf. In Exponential entscheiden **Rollen** über den Zugriff, die aus
**Richtlinien** (Modul, Funktion, Einschränkungen) bestehen und Benutzern oder Gruppen zugewiesen werden; niemand
erhält etwas, das keine Rolle gewährt
([Rollen, Richtlinien und nicht aktivierte Benutzer](../../../anleitungen/rollen-und-richtlinien.md),
[Sicherheit und Audit, 2](../../../anleitungen/sicherheit-und-audit.md#2-nur-den-zugriff-vergeben-den-personen-brauchen-10-minuten)).

## Inhalt

1. [Faustregeln](#faustregeln)
2. [Sicherheitsrelevante Richtlinien](#sicherheitsrelevante-richtlinien)
3. [Ein Beispiel für ein Rollenkonzept](#ein-beispiel-für-ein-rollenkonzept)
4. [Ausscheidende und wechselnde Beschäftigte](#ausscheidende-und-wechselnde-beschäftigte)
5. [Zugriffsüberprüfungen](#zugriffsüberprüfungen)
6. [Verwandte Seiten](#verwandte-seiten)

## Faustregeln

- Ein persönliches Konto je Person; legen Sie die gemeinsam genutzte Anmeldung `admin` still
  ([Kontenpflege](../../administratorpasswort-aendern.md#kontenhygiene)).
- Bearbeiten Sie nie die Rolle Administrator, um einer Kollegin oder einem Kollegen mehr zu geben; legen Sie eine neue
  Rolle an.
- Halten Sie die Zahl der Administratorinnen und Administratoren klein: Die Vergabe einer Rolle mit `*/*`, `setup/*`,
  `role/*` oder `audit/manage` löst den Alarm `admin_role_granted` aus.
- Schränken Sie nach **Sektion** oder **Teilbaum** ein, damit eine Abteilung nur ihre eigenen Inhalte bearbeitet
  ([Sektionen](../../../anleitungen/sektionen.md)).
- Eine Bearbeitungsrichtlinie öffnet auch die Versionen des Objekts; schränken Sie sie so ein wie die Leserichtlinie.

## Sicherheitsrelevante Richtlinien

Aus [13.5 Der Administrations-Siteaccess](../../../installation/13-sicherheitshaertung.md#135-der-administrations-siteaccess)
und den Funktionsseiten:

| Richtlinie | Erlaubt |
|---|---|
| `setup/setup` | Einstellungsansichten und Schreiben von Einstellungen |
| `setup/managecache` | Leeren von Caches |
| `setup/logs`, `setup/managelogs`, `setup/unmasklogs` | Lesen, Rotieren, Entmaskieren von Protokollen |
| `audit/read`, `audit/manage` | das Audit-Protokoll; Archive und Prüfung |
| `role/*` | Anlegen und Zuweisen von Rollen |
| `user/hygiene`, `user/deactivate` | Kontenprüfung, Deaktivieren, Deaktivieren und verschieben |
| `mailpreferences/administrate`, `mailpreferences/export` | die E-Mail-Einstellungen von Personen und das Einwilligungsprotokoll |
| `infocollector/read` | Formulareingaben |
| `contentfinder/search`, `contentfinder/delete` | Inhalte nach ID oder Pfad finden und löschen |
| `package/*` | Hochladen und Installieren von Paketen |

## Ein Beispiel für ein Rollenkonzept

| Rolle | Richtlinien (eingeschränkt) | Zugewiesen an |
|---|---|---|
| Redaktion der Abteilung | `content/read`, `content/create`, `content/edit` eingeschränkt auf die Sektion oder den Teilbaum der Abteilung | die Redaktionsgruppe jeder Abteilung |
| Veröffentlichende | wie Redaktion, zusätzlich Freigabe im Workflow | leitende Redakteurinnen und Redakteure |
| Web-Administration | Inhalte überall, `setup/managecache`, `user/hygiene` | Webteam |
| Prüfung | `audit/read` eingeschränkt nach `Channel` | Innenrevision, Sicherheitsbeauftragte |
| Systemadministration | Administrator | zwei oder drei namentlich benannte Personen |

## Ausscheidende und wechselnde Beschäftigte

- Deaktivieren Sie das Konto am Tag des Ausscheidens (Benutzer > Benutzerkonten prüfen, **Deaktivieren**); seine
  Sitzungen enden.
- Mit `[UserSettings] DeactivateAndMove=enabled` (auf Wunsch einzuschalten) deaktiviert **Deaktivieren und
  verschieben** das Konto und verschiebt es umkehrbar in eine verborgene Gruppe ohne Rollen, wobei Inhalte und Verlauf
  erhalten bleiben ([Deaktivieren und verschieben](../../../../eng-us/features/6.0/user-hygiene.md#deactivate-and-move)). Das
  Entfernen eines Benutzerobjekts ist endgültig: Benutzer kommen nie in den Papierkorb.
- Wechselnde Beschäftigte: Ändern Sie die Gruppenzugehörigkeit, nicht die Rollen der Gruppe.

## Zugriffsüberprüfungen

Zweimal im Jahr oder wie es Ihre Richtlinie vorsieht:

1. **Benutzer > Benutzerkonten prüfen**: die Filter **Nie angemeldet**, **Seit N Tagen nicht gesehen** (standardmäßig
   180), **Doppelter Benutzername**, **Doppelte E-Mail-Adresse**; **Liste als CSV exportieren** für den Nachweis der
   Überprüfung.
2. Rollen: Öffnen Sie jede Rolle (Benutzer > Rollen und Richtlinien, `/role/list`) und prüfen Sie ihre Zuweisungen.
3. Audit: `/audit/console/(name)/access.role.*` listet die Rollenänderungen seit der letzten Überprüfung.
4. Legen Sie die CSV-Datei und die Entscheidungen ab.

## Verwandte Seiten

- [Rollen, Richtlinien und nicht aktivierte Benutzer](../../../anleitungen/rollen-und-richtlinien.md)
- [Benutzerkonten prüfen](../../../../eng-us/features/6.0/user-hygiene.md)
- [Kurs Rollen und Richtlinien](../../trainer/website-administratoren/modul-03-rollen-und-richtlinien/README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
