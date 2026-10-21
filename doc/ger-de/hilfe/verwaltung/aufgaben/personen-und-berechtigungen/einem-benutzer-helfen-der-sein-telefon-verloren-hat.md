---
title: Einem Benutzer helfen, der sein Telefon verloren hat
audience: government, admin
category: public-sector
order: 240
summary: Schritt für Schritt - nach der Prüfung der Person ihre zweistufige Anmeldung auf Codes per E-Mail umstellen oder ausschalten, indem Sie das Feld Zweistufige Anmeldung ihres Benutzerobjekts bearbeiten, sie sich anmelden und die App neu einrichten lassen und es festhalten.
keywords: verlorenes Telefon, neues Telefon, zweistufige Anmeldung, 2FA zurücksetzen, Authenticator, Code per E-Mail, Service Desk, Prüfung der Identität, Wiederherstellung des Kontos, lost phone, new phone, two-step sign-in, 2FA reset, authenticator, e-mail code, service desk, identity verification, account recovery
type: help
---
# Einem Benutzer helfen, der sein Telefon verloren hat

Diese Aufgabe ist für Mitarbeiter des Service Desks und Administratoren. Am Ende kann sich eine Kollegin oder ein
Kollege, deren Telefon mit dem Authenticator verloren oder ersetzt ist, wieder anmelden und hat die App auf dem neuen
Telefon eingerichtet. Das Verfahren ist das aus
[Zweistufige Anmeldung, „Was ein Benutzer sieht“](../../../../anleitungen/zwei-faktor-anmeldung.md#was-ein-benutzer-sieht).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Wenn Codes per E-Mail erlaubt sind](#wenn-codes-per-e-mail-erlaubt-sind)
3. [Den zweiten Schritt umstellen](#den-zweiten-schritt-umstellen)
4. [Das neue Telefon einrichten](#das-neue-telefon-einrichten)
5. [Festhalten und nachverfolgen](#festhalten-und-nachverfolgen)
6. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- **Prüfen Sie die Person** nach dem Verfahren Ihres Service Desks (ein Rückruf an eine bekannte Nummer, eine
  Bestätigung der Führungskraft). Einen zweiten Faktor für einen ungeprüften Anrufer zurückzusetzen, hebelt ihn aus.
- Das Recht, das Objekt des Benutzers zu bearbeiten (`content/edit` daran; Administratoren haben es).

## Wenn Codes per E-Mail erlaubt sind

Mit eingeschaltetem `AllowEmailFallback` (der Voreinstellung, wie die
[Anleitung zu Sicherheit und Audit](../../../../anleitungen/sicherheit-und-audit.md#5-zwei-faktor-anmeldung-optional-10-minuten)
vermerkt) kann die Person bei der Anmeldung vielleicht einen Code per E-Mail nehmen und das neue Telefon ohne Ihre Hilfe
einrichten. Probieren Sie das zuerst.

## Den zweiten Schritt umstellen

1. Öffnen Sie das Objekt des Benutzers in der Administration (Benutzerbaum) und **Bearbeiten**.
2. Setzen Sie das Feld *Zweistufige Anmeldung* auf *E-mail one-time code* oder *Deaktiviert*.
3. Veröffentlichen Sie.

## Das neue Telefon einrichten

1. Die Person meldet sich an (mit einem Code per E-Mail oder nur mit dem Passwort, wenn Sie *Deaktiviert* gewählt
   haben).
2. Sie öffnet **Mein Konto > Zweistufige Anmeldung** (`/user2fa/setup`), wählt *Authenticator app*, scannt den QR-Code
   und gibt den Code ein, den die App zeigt. Nichts ändert sich, bis dieser Code eingegeben ist.
3. Wenn `Enforce2FA=enabled` gesetzt ist, wird ein Benutzer ohne zweiten Schritt ohnehin direkt nach dem Passwort auf
   die Einrichtungsseite geschickt.

## Festhalten und nachverfolgen

- Halten Sie fest: Datum, wer die Person wie geprüft hat, was umgestellt wurde.
- Wenn das Telefon gestohlen statt verloren wurde, behandeln Sie es als mögliche Kompromittierung: Ändern Sie das
  Passwort und prüfen Sie die jüngsten Anmeldungen des Kontos
  ([Vermutete Kompromittierung eines Kontos](../../../../../eng-us/help/government/operations/incidents/suspected-account-compromise.md)).
- Prüfen Sie, dass die Person nicht auf *Deaktiviert* geblieben ist: Ihre Kontoseite zeigt einen Hinweis, wenn die
  zweistufige Anmeldung aus ist.

## Verwandte Seiten
- [Die zweistufige Anmeldung für Mitarbeiter einschalten](die-zweistufige-anmeldung-fuer-mitarbeiter-einschalten.md)
- [Das Passwort eines Mitarbeiters zurücksetzen](das-passwort-eines-mitarbeiters-zuruecksetzen.md)
- [Zweistufige Anmeldung für Mitarbeiter](../../../../../eng-us/help/government/operations/people-and-permissions/two-factor-for-staff.md)
- [Anleitung zur zweistufigen Anmeldung](../../../../anleitungen/zwei-faktor-anmeldung.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
