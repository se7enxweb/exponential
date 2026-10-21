---
title: Wie ändere ich den Eigentümer eines Objekts?
audience: admin
category: manual
order: 345
summary: Wählen Sie im Kontextmenü des Objekts Change owner, wählen Sie den neuen Eigentümer und bestätigen Sie; Eigentümer-Einschränkungen von Rollen gelten dann für den neuen Eigentümer.
keywords: Eigentümer, Eigentümer ändern, ezownerchange, owner/change, Autor, Übergabe, Ausscheiden von Mitarbeitern, owner, change owner, author, handover, offboarding
type: help
---
# Wie ändere ich den Eigentümer eines Objekts?

Der Eigentümer ist der Benutzer, dem ein Objekt gehört. Richtlinien, die auf „eigene“ Inhalte beschränkt sind (zum
Beispiel „die eigenen Artikel bearbeiten“), folgen dem Eigentümer; ändern Sie ihn also, wenn jemand seine Arbeit
übergibt.

## Wer das tun kann

Benutzer, deren Rolle `owner/change` hat (in einer neuen Installation die Erweiterung ezownerchange).

## Schritte

1. Öffnen Sie in der **Inhaltsstruktur** das Kontextmenü des Objekts (oder das Menü einer Zeile in den
   **Unterelementen** seines übergeordneten Elements) und wählen Sie **Change owner**.
2. Suchen und wählen Sie auf der Seite **Choose the new owner of** *Name* den Benutzer.
3. Bestätigen Sie.
   Die **Details** des Objekts zeigen den neuen Eigentümer.

## Was schiefgehen kann

- **Der Menüeintrag fehlt.** Ihrer Rolle fehlt `owner/change`.
- **Der neue Eigentümer kann es immer noch nicht bearbeiten.** Seine Rolle erlaubt das Bearbeiten in dieser Sektion
  oder Klasse nicht. Eigentum fügt keine Rechte hinzu; es zählt nur für Einschränkungen auf „Owner“.
- **Sie müssen viele Objekte auf einmal ändern.** Die Anleitung zum Ändern von Eigentümern enthält den Befehl für einen
  ganzen Teilbaum.

## Verwandte Seiten

- [Eigentümer ändern](../../anleitungen/eigentuemer-aendern.md)
- [Wenn Mitarbeiter ausscheiden](../kunden/alltag/ausscheiden-von-mitarbeitenden.md)
- [Benutzerhandbuch](README.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
