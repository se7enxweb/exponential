---
title: Sitzungen, Abmeldung und gemeinsam genutzte Computer
audience: government, admin
category: public-sector
order: 80
summary: Wie lange Mitarbeiter in einem Exponential-Intranet angemeldet bleiben (SessionTimeout, ActivityTimeout), wie man das verkürzt, wie man Sitzungen unter Setup > Sitzungen mit dem Datenbank-Handler sieht und beendet, und Hinweise zu gemeinsam genutzten Computern und zur Sperre.
keywords: Sitzungen, Abmeldung, gemeinsam genutzte Computer, Setup Sitzungen, Sperre, sessions, SessionTimeout, ActivityTimeout, sign-out, logout, shared computers, Setup Sitzungen, ezpSessionHandlerDB, lockout, MaxNumberOfFailedLogin
type: help
---
# Sitzungen, Abmeldung und gemeinsam genutzte Computer

Diese Seite richtet sich an Administratoren. Eine Sitzung hält einen Browser angemeldet; sie endet
`site.ini [Session] SessionTimeout` Sekunden nach ihrer letzten Nutzung, voreingestellt 259200 (drei Tage)
([Sitzungen](../../../../anleitungen/sitzungen.md)).

## Inhalt

1. [Die Zeitlimits](#die-zeitlimits)
2. [Sitzungen sehen und beenden](#sitzungen-sehen-und-beenden)
3. [Gemeinsam genutzte Computer](#gemeinsam-genutzte-computer)
4. [Fehlgeschlagene Anmeldungen](#fehlgeschlagene-anmeldungen)

## Die Zeitlimits

| Einstellung (`site.ini [Session]`) | Mitgeliefert | Bedeutung |
|---|---|---|
| `SessionTimeout` | `259200` | Sekunden nach der letzten Nutzung, nach denen eine Sitzung endet |
| `ActivityTimeout` | `3600` | nach dieser Pause zählt die nächste Anfrage als neuer Besuch |

Drei Tage passen für die Mitglieder einer öffentlichen Site; für ein Intranet mit sensiblen Inhalten setzen Sie im
Intranet-Siteaccess einen kürzeren `SessionTimeout` und testen, dass Mitarbeiter nach der gewählten Pause erneut zur
Anmeldung aufgefordert werden.

## Sitzungen sehen und beenden

**Setup > Sitzungen** (`/setup/session`): Mit dem Standard-Handler (dem eigenen Speicher von PHP) zeigt die Seite, wer
sich kürzlich angemeldet hat, kann Sitzungen aber nicht einzeln auflisten oder entfernen. Mit
`[Session] Handler=ezpSessionHandlerDB` werden Sitzungen in der Datenbank gehalten, und die Seite listet sie und
entfernt sie nach einer Bestätigung, zum Beispiel alle Sitzungen eines ausscheidenden Mitarbeiters. Das Deaktivieren
eines Kontos beendet auch seine Sitzungen (**Benutzer > Benutzerkonten prüfen**, **Deaktivieren**).

## Gemeinsam genutzte Computer

- Schulen Sie Mitarbeiter darin, auf gemeinsam genutzten Computern **Abmelden** zu verwenden.
- Kürzere Zeitlimits für Sitzungen im Intranet-Siteaccess.
- Das Speichern von Passwörtern im Browser auf gemeinsam genutzten Geräten ausgeschaltet (eine IT-Richtlinie, keine
  Einstellung von Exponential).

## Fehlgeschlagene Anmeldungen

`site.ini [UserSettings] MaxNumberOfFailedLogin=0` (mitgeliefert) bedeutet keine Sperre. Eine Zahl sperrt ein Konto
nach so vielen fehlgeschlagenen Anmeldungen; `TrustedIPList[]` listet Adressen, die immer erlaubt sind.
**Benutzer > Benutzerkonten prüfen** zeigt gesperrte Konten und hat **Entsperren**. Brute-Force-Anmeldungen lösen
außerdem einen Alarm im Audit-Protokoll aus ([Audit-Protokoll](../../../../../eng-us/features/6.0/audit-trail.md)).

## Verwandte Seiten

- [Sitzungen](../../../../anleitungen/sitzungen.md)
- [Benutzerkonten prüfen](../../../../../eng-us/features/6.0/user-hygiene.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
