---
title: Einen neuen Mitarbeiter hinzufügen
audience: government, admin
category: public-sector
order: 40
summary: Schritt für Schritt - das Konto eines neuen Mitarbeiters in der Benutzergruppe seiner Abteilung anlegen, ihm sicher ein erstes Passwort geben, um die zweistufige Anmeldung bitten und seinen Zugang prüfen.
keywords: neue Mitarbeiter, neuer Benutzer, Eintritt, Onboarding, Benutzer anlegen, Benutzergruppe, erstes Passwort, Konto, Abteilung, Redakteurskonto, new staff, new user, joiner, onboarding, create user, user group, first password, account, department, editor account
type: help
---
# Einen neuen Mitarbeiter hinzufügen

Diese Aufgabe ist für Administratoren und Mitarbeiter des Service Desks. Am Ende hat die neue Kollegin oder der neue
Kollege ein persönliches Konto in der richtigen Gruppe, mit dem Zugang, den die Rollen dieser Gruppe geben.
Hintergrund: [Eintritte, Wechsel und Austritte](../../../../../eng-us/help/government/operations/people-and-permissions/joiners-movers-leavers.md).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Das Konto anlegen](#das-konto-anlegen)
3. [Erste Anmeldung](#erste-anmeldung)
4. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
5. [Fallstricke](#fallstricke)
6. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Ein genehmigter Antrag, der Abteilung, Tätigkeit und Startdatum nennt.
- Die Benutzergruppe der Abteilung existiert und trägt die richtigen Rollen
  ([Eine Benutzergruppe für eine Abteilung anlegen](eine-benutzergruppe-fuer-eine-abteilung-anlegen.md)).
- Sie dürfen in dieser Gruppe Benutzer anlegen (`content/create` der Benutzerklasse dort; Administratoren dürfen es).

## Das Konto anlegen

1. Öffnen Sie in der Administration **Benutzer** und die Benutzergruppe der Abteilung.
2. Legen Sie in der Gruppe ein neues Element der Benutzerklasse an (meist „User“).
3. Füllen Sie Vorname, Nachname, den Login (nach der Namensregel Ihrer Organisation), die dienstliche E-Mail-Adresse
   und ein erstes Passwort aus. Verwenden Sie **Sicheres Passwort erzeugen**, wo das Formular es anbietet.
4. **Zur Veröffentlichung schicken** (oder veröffentlichen). Der Hauptort des Kontos liegt in der Gruppe, also gelten
   die Rollen der Gruppe.

Das Passwort muss `site.ini [UserSettings] MinPasswordLength` erfüllen (10 auf einer neuen Installation) und wird ab
6.0.15 gegen häufige Passwörter geprüft (`WeakPasswordCheck=enabled`)
([Das eigene Passwort ändern](../../../../../eng-us/features/6.0/modern-password-change.md)).

## Erste Anmeldung

1. Geben Sie das erste Passwort über einen getrennten Kanal weiter (nicht in derselben E-Mail wie den Login), oder
   bitten Sie die Person, auf der Anmeldeseite **Passwort vergessen?** zu verwenden.
2. Bitten Sie sie, es sofort unter **Mein Konto** > **Passwort** zu ändern.
3. Wenn Ihre Richtlinie es verlangt, bitten Sie sie, unter **Mein Konto > Zweistufige Anmeldung** die zweistufige
   Anmeldung einzurichten ([Die zweistufige Anmeldung für Mitarbeiter einschalten](die-zweistufige-anmeldung-fuer-mitarbeiter-einschalten.md)).

## Prüfen, ob es funktioniert hat

- Öffnen Sie die Kontoseite (`/user/edit/<id>`): Sie listet die Gruppen und Rollen und Hinweise wie „zweistufige
  Anmeldung ist aus“.
- **Benutzer > Benutzerkonten prüfen**: Das Konto erscheint bis zur ersten Anmeldung unter **Nie angemeldet**.
- Die neue Kollegin oder der neue Kollege sieht die erwarteten Schaltflächen zum Bearbeiten und nichts darüber hinaus.

Änderungsnachweis: Login (nicht das Passwort), Gruppe, Datum, Referenz des Antrags.

## Fallstricke

- Ein in der falschen Gruppe angelegtes Konto erhält die falschen Rollen; korrigieren Sie es mit einer Verschiebung,
  nicht mit zusätzlichen Rollen.
- Einen gemeinsamen Login für ein Team kann das Audit-Protokoll nicht unterscheiden; legen Sie ein Konto je Person an.
- Zwei Konten mit demselben Login können sich nicht zuverlässig beide anmelden; das Bearbeitungsformular lehnt einen
  Login ab, den ein anderes Konto hat.

## Verwandte Seiten
- [Einen Mitarbeiter in eine andere Abteilung verschieben](einen-mitarbeiter-in-eine-andere-abteilung-verschieben.md)
- [Das Passwort eines Mitarbeiters zurücksetzen](das-passwort-eines-mitarbeiters-zuruecksetzen.md)
- [Pflege der Benutzerkonten](../../../../../eng-us/help/government/operations/people-and-permissions/user-account-hygiene.md)
- [Konten und Anmeldung (Hilfe)](../../../konten-und-anmeldung.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
