---
title: Alle Sitzungen eines Benutzers beenden
audience: government, admin
category: public-sector
order: 320
summary: Schritt für Schritt - jede Browsersitzung eines Kontos beenden (ein verlorener Laptop, eine vermutete Kompromittierung, ein ausscheidender Mitarbeiter), indem Sie das Konto deaktivieren, sein Passwort ändern oder seine Sitzungen unter Setup > Sitzungen mit dem Datenbank-Session-Handler entfernen.
keywords: Sitzungen, abmelden, überall abmelden, Setup Sitzungen, ezpSessionHandlerDB, Konto deaktivieren, Passwortänderung, EndOtherSessions, verlorenes Gerät, Kompromittierung, sessions, sign out, log out everywhere, disable account, password change, lost device, compromise
type: help
---
# Alle Sitzungen eines Benutzers beenden

Diese Aufgabe ist für Administratoren, wenn ein Konto überall auf einmal abgemeldet werden muss. Welcher Weg
funktioniert, hängt vom Session-Handler ab ([Sitzungen](../../../../anleitungen/sitzungen.md)).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Möglichkeit 1: das Konto deaktivieren](#möglichkeit-1-das-konto-deaktivieren)
3. [Möglichkeit 2: das Passwort ändern](#möglichkeit-2-das-passwort-ändern)
4. [Möglichkeit 3: die Sitzungen entfernen](#möglichkeit-3-die-sitzungen-entfernen)
5. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
6. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Entscheiden Sie, ob die Person sich sofort wieder anmelden darf (verlorener Laptop) oder nicht (Ausscheiden,
  Kompromittierung).
- `user/hygiene` oder das Recht, das Konto zu bearbeiten; ein Administratorkonto für **Setup > Sitzungen**.

## Möglichkeit 1: das Konto deaktivieren

**Benutzer > Benutzerkonten prüfen**, markieren Sie das Konto, **Deaktivieren**, **Bestätigen**: Es kann sich nicht mehr
anmelden, und seine Sitzungen enden ([Benutzerkonten prüfen](../../../../../eng-us/features/6.0/user-hygiene.md#the-actions)).
Verwenden Sie es bei Ausscheidenden und vermuteter Kompromittierung; später gegebenenfalls **Aktivieren**.

## Möglichkeit 2: das Passwort ändern

Eine Passwortänderung meldet die anderen Sitzungen des Kontos bei ihrer nächsten Anfrage ab, gleich welchen
Session-Handler die Site verwendet ([Das eigene Passwort ändern](../../../../../eng-us/features/6.0/modern-password-change.md#in-short));
mit `[PasswordSettings] EndOtherSessions=enabled` (der Voreinstellung). Setzen Sie ein neues Passwort auf der Kontoseite
oder mit `php bin/php/resetuserpassword.php -u <login> -g --allow-root-user`
([Ein Passwort auf der Kommandozeile zurücksetzen](../personen-und-berechtigungen/ein-passwort-auf-der-kommandozeile-zuruecksetzen.md)).
Verwenden Sie es bei einem verlorenen Gerät: Die Person meldet sich mit dem neuen Passwort wieder an.

## Möglichkeit 3: die Sitzungen entfernen

Nur mit dem Datenbank-Session-Handler (`site.ini [Session] Handler=ezpSessionHandlerDB`); mit dem Standardhandler zeigt
**Setup > Sitzungen**, wer sich kürzlich angemeldet hat, kann aber Sitzungen nicht einzeln entfernen.

1. **Setup > Sitzungen** (`/setup/session`), **Benutzer suchen**, **Liste aktualisieren**.
2. Auf der Karte des Benutzers **Sitzungen**: eine Karte je Browser oder Gerät.
3. Markieren Sie sie, **Ausgewählte entfernen**, bestätigen Sie **Die Sitzungen entfernen**.

Das Entfernen einer Sitzung meldet einen Browser ab; es hindert die Person nicht daran, sich erneut anzumelden.
Kombinieren Sie es mit Möglichkeit 1 oder 2.

## Prüfen, ob es funktioniert hat

- Die Sitzungen des Kontos sind aus **Setup > Sitzungen** verschwunden (Datenbankhandler).
- Der Kanal `access` des Audit-Protokolls zeigt keine neue Aktivität des Kontos und ein Ereignis zum Abmelden oder
  Deaktivieren.

## Verwandte Seiten
- [Vermutete Kompromittierung eines Kontos](../../../../../eng-us/help/government/operations/incidents/suspected-account-compromise.md)
- [Einen ausscheidenden Mitarbeiter entfernen](../personen-und-berechtigungen/einen-ausscheidenden-mitarbeiter-entfernen.md)
- [Anleitung zu den Sitzungen](../../../../anleitungen/sitzungen.md)
- [Sicherheitshärtung](../../../sicherheitshaertung.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
