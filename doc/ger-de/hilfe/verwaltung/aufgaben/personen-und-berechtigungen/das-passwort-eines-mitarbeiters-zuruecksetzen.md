---
title: Das Passwort eines Mitarbeiters zurücksetzen
audience: government, admin
category: public-sector
order: 80
summary: Schritt für Schritt - einer Kollegin oder einem Kollegen helfen, die sich nicht anmelden können, entweder über Passwort vergessen oder durch Setzen eines neuen Passworts auf der Kontoseite, nachdem geprüft wurde, wer fragt, mit dem, was das System danach für Sie erledigt.
keywords: Passwort zurücksetzen, Passwort vergessen, kann sich nicht anmelden, neues Passwort, Kontoseite, Service Desk, Prüfung der Identität, andere Sitzungen, E-Mail Passwort geändert, password reset, forgot password, cannot sign in, new password, account page, service desk, identity check, other sessions, password changed mail
type: help
---
# Das Passwort eines Mitarbeiters zurücksetzen

Diese Aufgabe ist für Mitarbeiter des Service Desks und Administratoren. Am Ende meldet sich die Kollegin oder der
Kollege mit einem neuen Passwort an, und ihre anderen Sitzungen sind abgemeldet. Für ein Zurücksetzen in der Shell
(verlorenes Administratorpasswort) siehe
[Ein Passwort auf der Kommandozeile zurücksetzen](ein-passwort-auf-der-kommandozeile-zuruecksetzen.md).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Möglichkeit 1: Passwort vergessen](#möglichkeit-1-passwort-vergessen)
3. [Möglichkeit 2: es auf der Kontoseite setzen](#möglichkeit-2-es-auf-der-kontoseite-setzen)
4. [Was danach geschieht](#was-danach-geschieht)
5. [Fallstricke](#fallstricke)
6. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- **Prüfen Sie, wer fragt**, nach dem Verfahren Ihres Service Desks. Ein Zurücksetzen für einen ungeprüften Anrufer ist
  eine Kontoübernahme.
- Prüfen Sie, ob das Konto **Gesperrt** oder **Deaktiviert** ist (**Benutzer > Benutzerkonten prüfen**); ein neues
  Passwort hilft einem deaktivierten Konto nicht ([Ein gesperrtes Konto entsperren](ein-gesperrtes-konto-entsperren.md)).

## Möglichkeit 1: Passwort vergessen

Der sicherste Weg, weil das Passwort nie durch Ihre Hände geht:

1. Bitten Sie die Kollegin oder den Kollegen, die Anmeldeseite zu öffnen und **Passwort vergessen?** zu verwenden.
2. Sie erhalten an der E-Mail-Adresse des Kontos eine E-Mail mit einem Link und wählen ein neues Passwort.

Das braucht einen funktionierenden E-Mail-Transport ([Probleme mit E-Mails](../../../../../eng-us/help/government/operations/incidents/mail-problems.md)).

## Möglichkeit 2: es auf der Kontoseite setzen

1. Öffnen Sie die Kontoseite der Kollegin oder des Kollegen, `/user/edit/<id>` (aus dem Benutzerbaum).
2. Verwenden Sie die Karte **Passwort** (angezeigt, wenn Sie es ändern dürfen) und setzen Sie ein neues Passwort;
   **Sicheres Passwort erzeugen** bietet eines an.
3. Geben Sie das Passwort über einen getrennten Kanal weiter und bitten Sie die Person, es sofort unter **Mein Konto** >
   **Passwort** (`/user/password`) zu ändern.

## Was danach geschieht

- Das Passwort muss `MinPasswordLength` erfüllen und wird gegen häufige Passwörter geprüft; alle Regeln aus
  `site.ini [PasswordSettings]` gelten ([Das eigene Passwort ändern](../../../../../eng-us/features/6.0/modern-password-change.md)).
- Die anderen Sitzungen des Kontos werden bei ihrer nächsten Anfrage abgemeldet.
- Eine E-Mail „Ihr Passwort wurde geändert“ geht an die Adresse des Kontos; niemand kann das abschalten.
- Die Änderung wird im Audit-Protokoll aufgezeichnet; das Passwort nie.

## Fallstricke

- Zweistufige Anmeldung: Ein neues Passwort setzt den zweiten Schritt nicht zurück. Ein verlorenes Telefon ist eine
  eigene Aufgabe ([Einem Benutzer helfen, der sein Telefon verloren hat](einem-benutzer-helfen-der-sein-telefon-verloren-hat.md)).
- Senden Sie niemals Login und Passwort in derselben Nachricht.
- Wenn viele Mitarbeiter gleichzeitig fragen, vermuten Sie ein Problem (E-Mail, Anmeldehandler, ein Angriff), bevor Sie
  jedes einzeln zurücksetzen.

## Verwandte Seiten
- [Ein Passwort auf der Kommandozeile zurücksetzen](ein-passwort-auf-der-kommandozeile-zuruecksetzen.md)
- [Zweistufige Anmeldung für Mitarbeiter](../../../../../eng-us/help/government/operations/people-and-permissions/two-factor-for-staff.md)
- [Das Administratorpasswort ändern (Hilfe)](../../../administratorpasswort-aendern.md)
- [Konten und Anmeldung (Hilfe)](../../../konten-und-anmeldung.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
