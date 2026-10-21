---
title: Ein gesperrtes Konto entsperren
audience: government, admin
category: public-sector
order: 210
summary: Schritt für Schritt - herausfinden, warum ein Konto gesperrt ist, die fehlgeschlagenen Anmeldungen im Audit-Protokoll prüfen und es unter Benutzerkonten prüfen oder in den Kontoeinstellungen entsperren, ohne einen Angreifer zu entsperren.
keywords: gesperrtes Konto, entsperren, fehlgeschlagene Anmeldungen, MaxNumberOfFailedLogin, fehlgeschlagene Anmeldungen zurücksetzen, Entsperren, Brute Force, Benutzerkonten prüfen, locked account, unlock, failed logins, failed sign-ins, Reset failed logins, Unlock, brute force, Check user accounts
type: help
---
# Ein gesperrtes Konto entsperren

Diese Aufgabe ist für Mitarbeiter des Service Desks und Administratoren. Ein Konto ist gesperrt, wenn es mehr
fehlgeschlagene Anmeldungen hat, als `site.ini [UserSettings] MaxNumberOfFailedLogin` erlaubt. Die Sperre ist
standardmäßig aus (der Wert ist `0`), also kommt das nur vor, wo sie eingeschaltet wurde
([Die Kontosperre einschalten](die-kontosperre-einschalten.md)).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Hinsehen, bevor Sie entsperren](#hinsehen-bevor-sie-entsperren)
3. [Entsperren](#entsperren)
4. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
5. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- `user/hygiene` für die Sammelseite oder das Recht, die Einstellungen des Kontos zu bearbeiten; `audit/read` für
  `access`, um die fehlgeschlagenen Anmeldungen anzusehen.
- Prüfen Sie, dass die anfragende Person die Inhaberin des Kontos ist.

## Hinsehen, bevor Sie entsperren

1. **Benutzer > Benutzerkonten prüfen**, Filter **Gesperrt**: Das Konto ist gelistet.
2. Sehen Sie sich die fehlgeschlagenen Anmeldungen an: auf dem Reiter **Audit**, **Konsole**, Kanal `access`, nach dem
   Konto suchen; oder

   ```bash
   ./console exp:audit search --name='access.*' --query=<login> --limit=20 --allow-root-user
   ```

3. Viele Fehlschläge von Adressen, die die Person nicht kennt, bedeuten einen Angriff, kein vergessenes Passwort: Ändern
   Sie das Passwort vor dem Entsperren und siehe
   [Vermutete Kompromittierung eines Kontos](../../betrieb/vorfaelle/verdacht-auf-kompromittiertes-konto.md).
   Audit-Warnungen `brute_force_user` (10 Fehlschläge für ein Konto in 15 Minuten) haben vielleicht schon ausgelöst.

Falsche Codes der zweistufigen Anmeldung zählen ebenfalls als fehlgeschlagene Anmeldungen des Kontos.

## Entsperren

Entweder:

- **Benutzer > Benutzerkonten prüfen**: Markieren Sie das Konto, **Entsperren**, **Bestätigen**. Aufgezeichnet als
  `access.user.unlock`.
- Oder auf der Kontoseite (`/user/edit/<id>`) unter **Kontoeinstellungen** die fehlgeschlagenen Anmeldungen
  zurücksetzen.

Wenn die Person auch das Passwort vergessen hat, folgen Sie
[Das Passwort eines Mitarbeiters zurücksetzen](das-passwort-eines-mitarbeiters-zuruecksetzen.md).

## Prüfen, ob es funktioniert hat

- Das Konto steht nicht mehr unter **Gesperrt**.
- Die Person meldet sich an.

## Verwandte Seiten
- [Die Kontosperre einschalten](die-kontosperre-einschalten.md)
- [Pflege der Benutzerkonten](../../betrieb/personen-und-berechtigungen/pflege-von-benutzerkonten.md)
- [Audit-Warnungen](../../betrieb/ueberwachung/audit-warnungen.md)
- [Benutzerkonten prüfen](../../../../funktionen/6.0/benutzerpflege.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
