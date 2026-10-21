---
title: "Rollenentwurf: das zentrale Web-Team"
audience: government, admin, project-manager
category: public-sector
order: 60
summary: Eine Rolle nach dem Prinzip der geringsten Rechte für das zentrale Web-Team einer öffentlichen Stelle - volle Inhaltsarbeit auf der ganzen Website, Caches, Cronjobs und Logs, aber keine Rollen, Einstellungen oder Verwaltung des Audits - aufgebaut nur aus Richtlinienfunktionen, die es in Exponential gibt.
keywords: Web-Team, Rollenentwurf, zentrales Team, Webmaster, Inhaltsverwaltung, setup/managecache, setup/logs, section/assign, state/assign, geringste Rechte, ohne vollen Zugriff, web team, role design, central team, content management, least privilege, without full access
type: help
---
# Rollenentwurf: das zentrale Web-Team

Diese Seite richtet sich an den Administrator, der die Rolle des zentralen Web-Teams einrichtet: die Personen, die die Struktur der Website
betreuen und den Fachbereichen helfen, aber keine Systemadministratoren sind. Sie gibt eine Rolle an, die nur aus Richtlinienfunktionen
besteht, die in `kernel/<module>/module.php` definiert sind, mit der Begründung für jede Zeile. Passen Sie die Einschränkungen an Ihren
Baum an.

## Inhalt
1. [Was das Web-Team tut](#was-das-web-team-tut)
2. [Die Rolle „Web-Team“](#die-rolle-web-team)
3. [Was bewusst weggelassen wird](#was-bewusst-weggelassen-wird)
4. [Einrichten](#einrichten)
5. [Prüfen](#prüfen)
6. [Verwandte Seiten](#verwandte-seiten)

## Was das Web-Team tut

Typische Aufgaben: Seiten überall auf der öffentlichen Website anlegen und anordnen, Inhalte anderer Teams korrigieren, die Medienbibliothek
verwalten, Inhalte zwischen Sektionen und Zuständen bewegen, nach einer Änderung Caches leeren, prüfen, ob Cronjobs gelaufen sind, und die
Logs lesen, wenn etwas falsch aussieht. Es legt in der Regel **keine** Rollen an, ändert keine Einstellungen, installiert keine Extensions
und verwaltet nicht den Audit-Trail; das bleibt bei den Systemadministratoren (siehe [Funktionstrennung](funktionstrennung.md)).

## Die Rolle „Web-Team“

| Modul | Funktion | Einschränkungen | Warum |
|---|---|---|---|
| user | login | SiteAccess: die Administration und die öffentlichen Siteaccesses | an der Administration anmelden |
| content | read | keine | alles sehen, um die Fachbereiche zu unterstützen |
| content | create, edit, publish, hide, move, manage_locations, translate | Sektion: Standard, Media | Inhaltsarbeit auf der öffentlichen Website und in der Medienbibliothek, nicht an Benutzerkonten (Sektion Users) |
| content | remove | Sektion: Standard, Media | Seiten entfernen; der Papierkorb erlaubt weiterhin die Wiederherstellung |
| content | versionread, diff | Sektion: Standard, Media | Versionen lesen und vergleichen, wenn Redaktionen Hilfe brauchen |
| content | restore | keine | Elemente aus dem Papierkorb zurückholen |
| content | dashboard, pendinglist | keine | das Dashboard und die Liste der ausstehenden Elemente |
| section | view, assign | assign mit NewSection, eingeschränkt auf Ihre öffentlichen und internen Sektionen | Inhalte in die richtige Sektion legen |
| state | assign | NewState: die Zustände Ihrer Prüfgruppe | Inhalte durch die Prüfung bewegen |
| setup | managecache | keine | nach einer Änderung Caches leeren ([Caches](../../../../anleitungen/caches.md)) |
| setup | managecronjobs | keine | Cronjob-Teile unter **Setup > Cronjobs** sehen und ausführen |
| setup | logs | keine | **Setup > Logdateien** lesen, maskiert |
| setup | system_info | keine | **Setup > Systeminformationen** lesen |

Die Funktionsnamen stammen aus `kernel/content/module.php`, `kernel/section/module.php`, `kernel/state/module.php`,
`kernel/setup/module.php` und `kernel/user/module.php`. Prüfen Sie nach dem Speichern die Rollenseite: Jede Richtlinie ist als Satz
geschrieben, und das ist der Nachweis, den Sie aufbewahren.

Fügen Sie `content/publish_without_notification` nur hinzu, wenn `notification.ini [NotificationSettings] PublishWithoutNotification`
aktiviert ist und das Team häufig Tippfehler korrigiert
([Ohne Benachrichtigung veröffentlichen](../../../../../eng-us/features/6.0/publish-without-notification.md)).

## Was bewusst weggelassen wird

| Weggelassen | Warum |
|---|---|
| das Modul `role` | würde das Team Rollen ändern und sich selbst beliebige Rechte geben lassen |
| `setup/administrate`, `setup/setup` | Einstellungen und die Setup-Seiten bleiben bei den Systemadministratoren |
| `setup/managelogs`, `setup/unmasklogs` | Logs rotieren, leeren und unmaskierte Adressen sind Aufgaben bei Vorfällen |
| `audit/read`, `audit/manage` | das Audit ist für Prüfer und Systemadministratoren ([Rollenentwurf: Prüfer](rollenentwurf-pruefende.md)) |
| `user/hygiene`, `user/deactivate` | die Kontenadministration ist eine getrennte Aufgabe |
| `content/cleantrash` | das Leeren des Papierkorbs ist endgültig |
| Inhalte in der Sektion Users | Benutzerkonten zu bearbeiten ist Kontenadministration |

## Einrichten

1. **Benutzer > Rollen und Richtlinien > Neue Rolle**, nennen Sie sie „Web-Team“.
2. Fügen Sie jede Zeile der Tabelle mit **Neue Richtlinie** hinzu: Wählen Sie das Modul, die Funktion und dann **Eingeschränkten Zugriff
   gewähren**, wo eine Einschränkung aufgeführt ist, oder **Vollen Zugriff gewähren** für diese Funktion, wo keine aufgeführt ist.
3. **Speichern**.
4. Legen Sie im Baum Benutzer eine Benutzergruppe „Web-Team“ an, falls Sie keine haben
   ([Eine Benutzergruppe für einen Fachbereich anlegen](../../../../../eng-us/help/government/tasks/people-and-permissions/create-a-user-group-for-a-department.md)).
5. **Weisen** Sie die Rolle auf der Rollenseite der Gruppe **zu**.

## Prüfen

- Melden Sie sich in einem privaten Browserfenster als Mitglied an: Das Setup-Menü zeigt Cache-Verwaltung, Cronjobs, Logdateien und
  Systeminformationen, und unter Benutzer gibt es kein Rollen und Richtlinien.
- Öffnen Sie `/role/list`, indem Sie es eintippen: Die Seite wird abgewiesen, und die Abweisung wird im Audit-Trail als
  `access.permission.refused` erfasst ([Audit-Trail](../../../../../eng-us/features/6.0/audit-trail.md#who-may-look)).
- Die Rollenseite zeigt kein Kennzeichen *Voller Zugriff* oder *Kann Rollen ändern*.

## Verwandte Seiten
- [Rollenentwurf: Redaktionen der Fachbereiche](rollenentwurf-fachredaktionen.md)
- [Grundsätze der geringsten Rechte](prinzip-der-geringsten-rechte.md)
- [Einer Kollegin oder einem Kollegen das Recht zum Leeren des Caches geben](../../../../../eng-us/help/government/tasks/people-and-permissions/give-a-colleague-cache-clearing-rights.md)
- [Rollen, Richtlinien und nicht aktivierte Benutzer](../../../../anleitungen/rollen-und-richtlinien.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
