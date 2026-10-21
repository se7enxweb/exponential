---
title: Auftragnehmern Zugang zum Extranet geben
audience: government, admin
category: public-sector
order: 190
summary: Aufgabe für Administratoren - den Mitarbeitern eines Auftragnehmers nur Zugang zu ihrem eigenen Projektbereich geben, mit einer Benutzergruppe, einer Sektion, einer eingeschränkten Rollenzuweisung und persönlichen Konten, und das Enddatum festhalten.
keywords: Extranet-Zugang, Auftragnehmer, Benutzergruppe, Sektion, Einschränkung der Rollenzuweisung, externe Konten, Enddatum, extranet access, contractor, user group, section, role assignment limitation, external accounts, end date
type: help
---
# Auftragnehmern Zugang zum Extranet geben

Diese Aufgabe richtet sich an Administratoren. Hintergrund:
[Benutzergruppen und Sektionen für Extranets](../../../../../eng-us/help/government/building/extranets/user-groups-and-sections-for-extranets.md) und
[Einem Auftragnehmer Zugang geben](../../../../../eng-us/help/government/building/extranets/giving-a-contractor-access.md).

## Bevor Sie beginnen

Ein schriftlicher Antrag, der den Auftragnehmer, die Personen, den Projektbereich und das Enddatum nennt.

## Schritte

1. **Benutzer**: Legen Sie unter einer Gruppe „Extern“ eine Benutzergruppe für den Auftragnehmer an.
2. **Setup > Sektionen**: Legen Sie eine Sektion für den Projektbereich an; **Inhalt zuweisen** zu dessen Ordner.
3. **Benutzer > Rollen und Richtlinien**: Weisen Sie die Rolle „Extranet-Leser“ (`content/read`, `user/login`) der
   Gruppe des Auftragnehmers zu, eingeschränkt auf die Sektion des Projekts.
4. Legen Sie in der Gruppe ein Konto je Person an (**Neu erstellen**, Benutzerklasse); lassen Sie jede Person mit
   **Passwort vergessen** ein Passwort setzen.
5. Halten Sie Personen und Enddatum in Ihrem Zugangsregister fest: Exponential hat kein Ablaufdatum für Konten.

## Prüfen

Ein Testkonto in der Gruppe öffnet den Projektbereich und sonst nichts; abgemeldet öffnet sich der Bereich nicht.

## Probleme

| Problem | Lösung |
|---|---|
| Der Auftragnehmer sieht andere Projekte | die Rolle wurde ohne die Einschränkung auf die Sektion zugewiesen, oder eine andere Rolle gibt mehr |
| Eine Person kann keine Dateien herunterladen | die Dateien liegen außerhalb der Sektion des Projekts |

## Verwandte Seiten

- [Verwaiste Konten von Auftragnehmern prüfen und deaktivieren](verwaiste-konten-von-auftragnehmern-pruefen-und-deaktivieren.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
