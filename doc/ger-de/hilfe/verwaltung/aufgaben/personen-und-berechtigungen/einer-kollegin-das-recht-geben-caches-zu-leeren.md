---
title: Einer Kollegin oder einem Kollegen das Recht geben, Caches zu leeren
audience: government, admin
category: public-sector
order: 320
summary: Schritt für Schritt - ein Mitglied des Web-Teams mit der einzigen Funktion setup/managecache Caches unter Setup > Cache-Verwaltung leeren lassen, ohne Einstellungen oder vollen Zugriff, und erklären, was es leeren sollte und was nicht.
keywords: Cache, Cache leeren, setup/managecache, Cache-Verwaltung, Web-Team, minimale Rechte, Betriebsrechte, Rolle, Setup, cache, clear cache, Cache management, web team, least privilege, operations rights, role
type: help
---
# Einer Kollegin oder einem Kollegen das Recht geben, Caches zu leeren

Diese Aufgabe ist für Administratoren. Am Ende kann eine Kollegin oder ein Kollege Caches aus der Administration leeren,
ohne irgendein anderes Setup-Recht. **Setup > Cache-Verwaltung** (`/setup/cache`) braucht die Richtlinie
`setup/managecache` ([Anleitung zu den Caches](../../../../anleitungen/caches.md)).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Was Sie der Kollegin oder dem Kollegen sagen](#was-sie-der-kollegin-oder-dem-kollegen-sagen)
4. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
5. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Ein Konto mit dem Modul `role` und selbst `setup/managecache`.
- Die Kollegin oder der Kollege meldet sich in der Administration an (`user/login` auf diesem Siteaccess).

## Schritte

1. **Benutzer > Rollen und Richtlinien > Neue Rolle**, nennen Sie sie „Cache operator“ (oder fügen Sie die Richtlinie
   Ihrer bestehenden Rolle „Web team“ hinzu:
   [Rollengestaltung: das zentrale Web-Team](../../../../../eng-us/help/government/operations/people-and-permissions/role-design-web-team.md)).
2. **Neue Richtlinie**: Modul `setup`, Funktion `managecache`, **Vollen Zugriff gewähren** für diese Funktion.
3. Optional `setup` / `system_info`, damit sie **Setup > Systeminformationen** lesen können; deren Schaltflächen für
   HTTP-Cache, Abfrage-Cache und SQL-Profil brauchen ebenfalls `setup/managecache`.
4. **Speichern**, dann der Gruppe der Kollegin oder des Kollegen **Zuweisen**.

## Was Sie der Kollegin oder dem Kollegen sagen

- Leeren Sie den kleinsten Cache, der das Problem behebt: den View-Cache der Inhalte bei veralteten Seiten, die
  Template-Caches nach einer Änderung an Templates; **Gesamter Cache** nur im Zweifel
  ([Caches nach einer Änderung an Templates leeren](../betrieb/caches-nach-einer-aenderung-an-templates-leeren.md)).
- Nach einer Änderung an Code oder Einstellungen müssen die Caches geleert werden, **nachdem** PHP und Velocity den
  neuen Code ausführen; das ist ein Deployment, keine Schaltfläche für den Cache
  ([Eine PHP-Änderung ausrollen](../betrieb/eine-php-aenderung-ausrollen.md)).
- Jedes Leeren wird im Kanal `system` des Audit-Protokolls aufgezeichnet.

## Prüfen, ob es funktioniert hat

- Als die Kollegin oder der Kollege angemeldet zeigt das Setup-Menü **Cache-Verwaltung**, und **Leeren** funktioniert.
- Andere Setup-Seiten (Wartung, Cronjobs, Ini Einstellungen) werden nicht angeboten.

## Verwandte Seiten
- [Veralteter Cache](../../../../../eng-us/help/government/operations/incidents/stale-cache.md)
- [Grundsätze der minimalen Rechte](../../../../../eng-us/help/government/operations/people-and-permissions/least-privilege-principles.md)
- [Caches leeren und vorwärmen (Hosting)](../../../hosting/leistung/caches-leeren-und-vorwaermen.md)
- [Anleitung zu den Caches](../../../../anleitungen/caches.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
