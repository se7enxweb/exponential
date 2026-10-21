---
title: Einen ausscheidenden Mitarbeiter entfernen
audience: government, admin
category: public-sector
order: 50
summary: Schritt für Schritt - am letzten Tag eines Mitarbeiters das Konto deaktivieren, sodass es sich nicht anmelden kann, Entwürfe und Freigaben übergeben, direkte Zuweisungen entfernen und entscheiden, ob das Konto stillgelegt oder endgültig entfernt wird.
keywords: ausscheidender Mitarbeiter, Offboarding, letzter Tag, Konto deaktivieren, Benutzerkonten prüfen, Deaktivieren, Sitzungen enden, Übergabe, Benutzer entfernen, Konto stilllegen, leaver, offboarding, last day, disable account, Check user accounts, Disable, sessions end, handover, remove user, retire account
type: help
---
# Einen ausscheidenden Mitarbeiter entfernen

Diese Aufgabe ist für Administratoren und Mitarbeiter des Service Desks. Am Ende kann sich eine Kollegin oder ein Kollege,
die ausgeschieden sind, nicht mehr anmelden, und ihre Inhalte, Entwürfe und Freigabeaufgaben sind übergeben. „Entfernen“
bedeutet hier, den Zugang zu entfernen: Das Entfernen des Kontos selbst ist endgültig und selten die richtige Wahl.
Hintergrund: [Eintritte, Wechsel und Austritte](../../../../../eng-us/help/government/operations/people-and-permissions/joiners-movers-leavers.md).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Das Konto deaktivieren](#das-konto-deaktivieren)
3. [Übergeben](#übergeben)
4. [Stilllegen oder entfernen](#stilllegen-oder-entfernen)
5. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
6. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Das Datum des Ausscheidens, von der Personalabteilung oder der Führungskraft.
- Die Funktion `user/hygiene` (Benutzer > Benutzerkonten prüfen) und `content/edit` für das Benutzerkonto;
  Administratoren haben beides.

## Das Konto deaktivieren

1. Öffnen Sie **Benutzer > Benutzerkonten prüfen** (`/user/hygiene`).
2. Tippen Sie den Namen oder Login unter **Konto suchen** ein.
3. Markieren Sie das Konto und klicken Sie auf **Deaktivieren**. Die Bestätigung sagt, wie viele Konten sich ändern
   werden; klicken Sie auf **Bestätigen**.
4. Das Konto kann sich nicht mehr anmelden, und seine Sitzungen enden. Inhalte, Gruppen und Rollen bleiben. Das
   Audit-Protokoll zeichnet `access.user.disable` auf.

## Übergeben

1. **Entwürfe**: Entwürfe sind unter **Meine Entwürfe** nur für ihren Autor sichtbar; bitten Sie die Person, sie vor
   dem Ausscheiden zu veröffentlichen oder zu entfernen. Danach kann ein Administrator Entwürfe auf der Versionsseite
   jedes Objekts finden.
2. **Freigaben**: Entfernen Sie die Person aus den Gruppen der Freigebenden und aus **Benutzer, die den Inhalt
   bewilligen** jedes Ereignisses Approve (**Setup > Workflows**). Wartende Anfragen, die nur sie nannten, müssen
   abgebrochen und neu gesendet werden
   ([Fehlersuche bei Workflows](../../../../../eng-us/help/government/operations/editorial-workflows/workflow-troubleshooting.md)).
3. **Direkte Rollenzuweisungen**: Markieren Sie auf jeder Seite einer Rolle, die die Person listet, die Zuweisung und
   wählen Sie **Ausgewählte entfernen**.
4. **Eigentümerschaft**: Wenn Rollen `Owner = Self` verwenden, ändern Sie den Eigentümer ihrer Inhalte mit
   **Change owner** ([Eigentümerschaft von Inhalten](../../../../../eng-us/help/government/operations/editorial-workflows/content-ownership.md)).
5. **Shell- und Serverzugang**, Einträge für Debug-IPs und API-Schlüssel sind vom Konto getrennt; entfernen Sie sie
   nach Ihrem Prozess für Serverzugänge.

## Stilllegen oder entfernen

| Wahl | Wie | Wirkung |
|---|---|---|
| Deaktiviert lassen | nichts weiter | das Konto bleibt, wo es ist, und kann sich nicht anmelden |
| Deaktivieren und verschieben | [Einen ausscheidenden Mitarbeiter deaktivieren und verschieben](einen-ausscheidenden-mitarbeiter-deaktivieren-und-verschieben.md) | deaktiviert, in eine versteckte Gruppe ohne Rollen verschoben; umkehrbar |
| Entfernen | **Entfernen** am Knoten des Kontos | endgültig: „Ein Benutzerobjekt kommt in Exponential nie in den Papierkorb“ ([Benutzerkonten prüfen](../../../../../eng-us/features/6.0/user-hygiene.md#in-short)) |

Die meisten Behörden lassen das Konto deaktiviert oder stillgelegt, weil Inhalte und das Audit-Protokoll darauf
verweisen.

## Prüfen, ob es funktioniert hat

- **Benutzerkonten prüfen**, Filter **Deaktiviert**: Das Konto ist gelistet.
- Ein Anmeldeversuch mit dem Konto schlägt fehl.
- Die Seiten der Rollen listen die Person nicht mehr direkt.

Änderungsnachweis: Datum der Deaktivierung, erledigte Übergabe, Referenz des Antrags.

## Verwandte Seiten
- [Einen ausscheidenden Mitarbeiter deaktivieren und verschieben](einen-ausscheidenden-mitarbeiter-deaktivieren-und-verschieben.md)
- [Eine Rollenzuweisung entfernen](eine-rollenzuweisung-entfernen.md)
- [Alle Sitzungen eines Benutzers beenden](../betrieb/alle-sitzungen-eines-benutzers-beenden.md)
- [Benutzerkonten prüfen](../../../../../eng-us/features/6.0/user-hygiene.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
