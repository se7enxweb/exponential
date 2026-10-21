---
title: Besucher eine neue Sektion lesen lassen
audience: government, admin
category: public-sector
order: 270
summary: Schritt für Schritt - wenn eine neue öffentliche Sektion (zum Beispiel „Public documents“) Besuchern nichts zeigt, sie zur Richtlinie content/read der Rolle Anonymous hinzufügen und das Ergebnis in einem privaten Fenster prüfen.
keywords: Rolle Anonymous, Besucher, neue Sektion, content/read, öffentliche Sektion, unsichtbare Inhalte, Berechtigung, Einschränkung Section, nicht angezeigt, Anonymous role, visitors, new section, public section, invisible content, permission, Section limitation, not shown
type: help
---
# Besucher eine neue Sektion lesen lassen

Diese Aufgabe ist für Administratoren. Eine neue Sektion ist für Besucher unsichtbar, weil die Rolle Anonymous
`content/read` (und `content/pdf`) auf die Sektion Standard eingeschränkt hat. Am Ende lesen Besucher auch die neue
Sektion. Das ist das Beispiel aus
[Sektionen, Abschnitt 9.1](../../../../anleitungen/sektionen.md#91-eine-richtlinie-mit-einer-sektions-einschränkung).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
4. [Fallstricke](#fallstricke)
5. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Ein Konto mit dem Modul `role`.
- Die Sektion soll öffentlich sein: Prüfen Sie doppelt, dass nichts darin intern ist.

## Schritte

1. **Benutzer > Rollen und Richtlinien**, öffnen Sie **Anonymous**, klicken Sie auf **Bearbeiten**.
2. Suchen Sie die Richtlinie *Darf Inhalte lesen, in der Sektion Standard* (`content/read`) und klicken Sie daneben auf
   **Einschränkungen**.
3. Markieren Sie in der Liste der Sektionen zusätzlich zu Standard die neue Sektion (Strg oder Cmd). **OK**.
4. Wenn Besucher auch PDF-Fassungen erhalten sollen, tun Sie dasselbe für `content/pdf`.
5. **Speichern**.
6. Leeren Sie den View-Cache der Inhalte, wenn Seiten im Cache lagen, während sie versteckt waren:

   ```bash
   php bin/php/ezcache.php --clear-tag=content --allow-root-user
   ```

## Prüfen, ob es funktioniert hat

- Öffnen Sie in einem privaten Browserfenster ohne Anmeldung eine Seite der Sektion: Sie wird angezeigt und erscheint
  in Menüs und Suchergebnissen.
- Die Seite der Sektion (**Setup > Sektionen**) listet Anonymous unter den Rollen mit auf sie eingeschränkten
  Richtlinien.

## Fallstricke

- Wenn andere Siteaccesses (eine zweite öffentliche Site) andere anonyme Rollen oder Benutzer verwenden, wiederholen Sie
  es für sie.
- Wenn die öffentliche Leserichtlinie auch eine Einschränkung auf Zustände hat (zum Beispiel nur *Freigegeben*),
  brauchen neue Inhalte weiterhin diesen Zustand
  ([Objektzustände in Berechtigungen](../../../../../eng-us/help/government/operations/people-and-permissions/object-states-in-permissions.md)).
- Unter Velocity oder mit dem HTTP-Cache leeren Sie auch den Antwort-Cache
  ([Den Antwort-Cache von Velocity leeren](../betrieb/den-antwort-cache-von-velocity-leeren.md)).

## Verwandte Seiten
- [Eine Sektion für eingeschränkte Inhalte anlegen](eine-sektion-fuer-eingeschraenkte-inhalte-anlegen.md)
- [Sektionen und Einschränkungen auf Teilbäume](../../../../../eng-us/help/government/operations/people-and-permissions/sections-and-subtree-limits.md)
- [Caches nach einer Änderung an Einstellungen leeren](../betrieb/caches-nach-einer-aenderung-an-einstellungen-leeren.md)
- [Anleitung zu den Sektionen](../../../../anleitungen/sektionen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
