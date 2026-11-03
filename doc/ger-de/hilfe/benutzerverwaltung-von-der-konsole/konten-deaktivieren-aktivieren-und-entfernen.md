---
title: "Konten deaktivieren, aktivieren und entfernen"
audience: admin, developer, hosting
category: administration
order: 3
summary: exp:user:disable stoppt ein Konto sofort und beendet seine Sitzungen, exp:user:enable startet es wieder, exp:user:delete entfernt es endgültig; geschützte Konten und der letzte Administrator werden verweigert, und wie das mit Deaktivieren und verschieben in der Administration zusammenhängt.
keywords: Benutzer deaktivieren, Benutzer aktivieren, Benutzer entfernen, Konto löschen, deaktivieren und verschieben, Sitzungen beenden, geschütztes Konto, letzter Administrator, Ausscheiden, jemand geht, übernommenes Konto, exp:user:disable, exp:user:enable, exp:user:delete, disable user, delete account, deactivate and move
type: help
---
# Konten deaktivieren, aktivieren und entfernen

Deaktivieren Sie ein Konto, wenn jemand geht oder ein Konto übernommen worden sein könnte. Entfernen Sie es erst,
wenn nichts mehr darauf verweist.

## Was Sie am Ende haben

Ein Konto, das sich nicht mehr anmelden kann, ein Konto, das wieder funktioniert, oder ein Konto, das weg ist.

## Ein Konto deaktivieren

```bash
./console exp:user:disable jane
./console exp:user:disable jane --apply
```

`exp:user:disable` tut, was **Benutzereinstellungen bearbeiten** in der Administration tut. Das Konto wird
deaktiviert, und alle seine Sitzungen enden, auch die erfassten unter **Ihre Geräte**. Nichts wird entfernt: Inhalte,
Gruppen und Rollen bleiben. Das Audit hält `access.user.disable` fest.

## Ein Konto wieder aktivieren

```bash
./console exp:user:enable jane --apply
```

`exp:user:enable` verweigert ein Konto, das nie aktiviert wurde (es hat noch seinen Aktivierungsschlüssel). Geben Sie
seine Registrierung stattdessen mit `exp:user:approve` frei, damit sie mit ihren Mails abgeschlossen wird
([Registrierungen freigeben](registrierungen-freigeben.md)).

## Ein Konto entfernen

1. Prüfen Sie, was das Konto getan hat und noch besitzt.

   ```bash
   ./console exp:user:show jane
   ./console exp:user:audit jane
   ```

2. Führen Sie den Befehl ohne `--apply` aus.

   ```bash
   ./console exp:user:delete jane
   ```

   Der Befehl nennt das Konto und sagt, ob es geschützt ist.
3. **Achtung:** Benutzerkonten kommen nie in den Papierkorb. Das Konto, seine Einstellungen und seine Sitzungen sind
   endgültig weg.
   Führen Sie den Befehl mit `--apply` aus.

   ```bash
   ./console exp:user:delete jane --apply
   ```

Die Inhalte, die die Person angelegt hat, bleiben, mit dem Namen des entfernten Kontos.

## Geschützte Konten

`exp:user:disable` und `exp:user:delete` verweigern diese Konten, mit Exit-Code 1:

| Konto | Warum |
|---|---|
| der anonyme Benutzer (`[UserSettings] AnonymousUserID`) | jeder Besucher ist dieser Benutzer |
| der Benutzer aus `[UserSettings] UserCreatorID` | der Kernel legt Konten als dieser Benutzer an |
| das Administratorkonto (ID 14 oder der Anmeldename `admin`) | das Konto einer neuen Installation |
| der handelnde Benutzer (`--as`) | Sie sperren sich nicht selbst aus |
| der letzte aktivierte Administrator | ein Konto mit allen Rechten muss bleiben |

`exp:user:show` sagt, ob ein Konto geschützt ist und warum. In JSON nennt der Schlüssel `refusal` den Grund
(`anonymous`, `creator`, `admin`, `self`, `last_administrator`).

## Deaktivieren oder „Deaktivieren und verschieben“?

Die Administration hat einen zweiten Weg, ein Konto stillzulegen: **Deaktivieren und verschieben**
([Benutzerkonten prüfen](../../funktionen/6.0/benutzerpflege.md#deaktivieren-und-verschieben)). Er ist standardmäßig
aus (`[UserSettings] DeactivateAndMove`). Er deaktiviert das Konto, verschiebt zusätzlich alle seine Platzierungen in
eine verborgene Gruppe ohne Rollen und merkt sich, wo sie waren.

| | `exp:user:disable` | Deaktivieren und verschieben |
|---|---|---|
| Konto kann sich anmelden | nein | nein |
| Sitzungen enden | ja | ja |
| Gruppen und Platzierungen | bleiben, wie sie sind | in die Gruppe für deaktivierte Konten verschoben |
| Rollen aus Gruppen | bleiben zugewiesen | fehlen, solange es in dieser Gruppe ist |
| Rückgängig machen | `exp:user:enable` | **Reaktivieren und zurückverschieben** in der Administration |

Die Konsole deaktiviert nur; sie verschiebt nie ein Konto. Nutzen Sie **Deaktivieren und verschieben** in der
Administration, wenn ein Konto auch die Rollen seiner Gruppen verlieren soll. Ein dorthin verschobenes Konto wird nur
mit **Reaktivieren und zurückverschieben** wieder aktiviert, damit es in seine Gruppen zurückkehrt.

## Verwandte Seiten

- [Den Zugang beim Ausscheiden schließen](den-zugang-beim-ausscheiden-schliessen.md): der ganze Ablauf.
- [Sammeländerungen](import-export-und-sammelaenderungen.md#sammeländerungen): viele Konten auf einmal deaktivieren.
- [Mitglieder verwalten](../mitglieder-verwalten.md): dasselbe für Mitglieder in der Administration.
