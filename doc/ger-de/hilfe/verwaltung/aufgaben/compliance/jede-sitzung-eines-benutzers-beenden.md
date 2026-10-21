---
title: Jede Sitzung eines Benutzers beenden
audience: government, admin
category: public-sector
order: 40
summary: Schritt für Schritt - nach einer vermuteten Kontoübernahme oder einem verlorenen Gerät jede Sitzung eines Benutzers in Exponential beenden, indem Sie das Passwort ändern (EndOtherSessions), das Konto deaktivieren oder Sitzungen unter Setup > Sitzungen mit dem Datenbank-Sitzungshandler entfernen.
keywords: abmelden, Sitzungen beenden, Session Hijacking, Kontoübernahme, verlorener Laptop, EndOtherSessions, Passwortstempel, Setup Sitzungen, ezpSessionHandlerDB, Konto deaktivieren, Vorfall, öffentlicher Sektor, sign out, end sessions, session hijacking, account takeover, lost laptop, password stamp, disable account, incident, public sector
type: help
---
# Jede Sitzung eines Benutzers beenden

Für Administratoren, die eine vermutete Übernahme, einen gestohlenen Laptop oder einen ausscheidenden Mitarbeiter
behandeln, der irgendwo noch angemeldet ist. Ein Sitzungscookie ist so viel wert wie das Passwort; diese Aufgabe beendet
die Sitzungen ([Sitzungen und Cookies](../../../../../eng-us/help/government/security/sessions-and-cookies.md)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Möglichkeit 1: das Passwort ändern](#möglichkeit-1-das-passwort-ändern)
3. [Möglichkeit 2: das Konto deaktivieren](#möglichkeit-2-das-konto-deaktivieren)
4. [Möglichkeit 3: Sitzungen auf der Seite Sitzungen entfernen](#möglichkeit-3-sitzungen-auf-der-seite-sitzungen-entfernen)
5. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
6. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Rechte, den Benutzer zu bearbeiten (und `user/hygiene` für Benutzerkonten prüfen).
- Halten Sie fest, was Sie wann tun: Es kann sich um einen Vorfall handeln.

## Möglichkeit 1: das Passwort ändern

Mit `[PasswordSettings] EndOtherSessions=enabled` (der Voreinstellung) meldet eine Passwortänderung jede andere Sitzung
dieses Benutzers bei ihrer nächsten Anfrage ab, mit jedem Sitzungshandler; die Sitzung, in der es geändert wurde,
erhält eine neue ID. Setzen Sie das Passwort auf der Kontoseite des Benutzers zurück (**Passwort**) oder auf der
Kommandozeile:

```bash
./console exp:resetuserpassword --allow-root-user -u <login> -g -l 24
```

Geben Sie das neue Passwort der Person über einen getrennten Kanal. Sitzungen, die vor 6.0.15 begonnen haben, tragen
keinen Stempel und werden erst ab ihrer nächsten Anmeldung geprüft.

## Möglichkeit 2: das Konto deaktivieren

**Benutzer > Benutzerkonten prüfen**, markieren Sie das Konto, **Deaktivieren**, **Bestätigen** (oder über die
Einstellungen des Kontos). Ein deaktiviertes Konto kann sich nicht anmelden, und seine Sitzungen enden bei ihrer
nächsten Anfrage. Aufgezeichnet als `access.user.disable`.

## Möglichkeit 3: Sitzungen auf der Seite Sitzungen entfernen

Nur mit dem Datenbank-Sitzungshandler (`[Session] Handler=ezpSessionHandlerDB`): **Setup > Sitzungen**, den Benutzer
finden, seine Sitzungen markieren, **Ausgewählte entfernen**, bestätigen ([Sitzungen](../../../../anleitungen/sitzungen.md)).
Mit dem Standardhandler kann die Seite Sitzungen nicht einzeln entfernen; verwenden Sie Möglichkeit 1 oder 2. Das
Entfernen von Sitzungen hindert den Benutzer nicht daran, sich erneut anzumelden.

## Prüfen, ob es funktioniert hat

- Im Audit zeigt die nächste Anfrage des Benutzers keine Aktivität aus der alten Sitzung; `/audit/console/(user)/<id>`.
- Mit dem Datenbankhandler listet Setup > Sitzungen für den Benutzer keine Sitzungen außer neuen.

## Verwandte Seiten

- [Sitzungen und Cookies](../../../../../eng-us/help/government/security/sessions-and-cookies.md)
- [Reaktion auf Vorfälle](../../../../../eng-us/help/government/security/incident-response.md)
- [Das Administratorpasswort ändern](../../../administratorpasswort-aendern.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
