---
title: Ein Benutzer hat sein Telefon verloren - wie meldet er sich wieder an?
audience: admin
category: manual
order: 290
summary: Bearbeiten Sie das Benutzerobjekt, stellen Sie das Feld Zweistufige Anmeldung auf E-mail one-time code oder Deaktiviert und veröffentlichen Sie es; der Benutzer meldet sich an und richtet die Authenticator-App neu ein.
keywords: zweistufige Anmeldung, 2FA, Telefon verloren, 2FA zurücksetzen, ausgesperrt, Authenticator-App, Einmalcode per E-Mail, Benutzerkonto, two-step sign-in, lost phone, reset 2FA, locked out, authenticator app, e-mail one-time code, user account
type: help
---
# Ein Benutzer hat sein Telefon verloren - wie meldet er sich wieder an?

Ein Benutzer, der eine Authenticator-App eingerichtet hat, kann sich ohne sie nicht anmelden. Ein Administrator ändert
den zweiten Schritt des Benutzers, damit dieser sich anmelden und ihn neu einrichten kann.

## Wer das tun kann

Benutzer, die Benutzerobjekte bearbeiten dürfen: `content/edit` für die Gruppe des Benutzers, meist die Rolle
Administrator. Prüfen Sie zuerst, ob die Anfrage wirklich vom Benutzer kommt, zum Beispiel per Telefon.

## Schritte

1. Klicken Sie im oberen Menü auf **Benutzer** und öffnen Sie die Gruppe des Benutzers, oder suchen Sie den Benutzer
   nach Name oder Login.
2. Öffnen Sie den Benutzer und klicken Sie auf **Bearbeiten**.
3. Suchen Sie das Feld **Zweistufige Anmeldung** und wählen Sie **E-mail one-time code** (der Code geht an die
   E-Mail-Adresse des Benutzers) oder **Deaktiviert** (nur das Passwort).
4. Klicken Sie auf **Zur Veröffentlichung schicken**.
5. Bitten Sie den Benutzer, sich anzumelden und die App unter **Mein Konto** > **Zweistufige Anmeldung** neu
   einzurichten.

## Was schiefgehen kann

- **Der Benutzer erhält den E-Mail-Code nicht.** Seine E-Mail-Adresse ist falsch oder veraltet. Korrigieren Sie sie im
  selben Bearbeitungsformular, oder wählen Sie für die erste Anmeldung **Deaktiviert**.
- **Der Benutzer ist nach zu vielen falschen Passwörtern gesperrt.** Entsperren Sie das Konto ebenfalls: siehe
  [Wie deaktiviere oder entsperre ich ein Benutzerkonto?](wie-deaktiviere-oder-entsperre-ich-ein-benutzerkonto.md)
- **Sie haben den zweiten Schritt ausgeschaltet und es vergessen.** Konten ohne zweiten Schritt werden in den
  Sicherheitsprüfungen aufgeführt. Bitten Sie den Benutzer, ihn noch am selben Tag wieder einzuschalten.

## Verwandte Seiten

- [Wie schalte ich die zweistufige Anmeldung für mein Konto ein?](wie-schalte-ich-die-zweistufige-anmeldung-ein.md)
- [Zweistufige Anmeldung](../../anleitungen/zwei-faktor-anmeldung.md)
- [Das Administratorpasswort ändern: zweistufige Anmeldung und ein verlorenes Telefon](../administratorpasswort-aendern.md#zweistufige-anmeldung-und-ein-verlorenes-telefon)
- [Administratorkonten und Zugriff](../kunden/gesund-halten/administratorkonten-und-zugriff.md)
- [Benutzerhandbuch](README.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
