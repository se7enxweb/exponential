---
title: Eigene Wörter zur Liste schwacher Passwörter hinzufügen
audience: government, admin
category: public-sector
order: 30
summary: Schritt für Schritt - Exponential Passwörter ablehnen lassen, die aus eigenen Wörtern Ihrer Organisation gebildet sind (ihr Name, die Site, die Stadt), zusätzlich zur mitgelieferten Liste bekannter Passwörter, mit WeakPasswordList[] oder WeakPasswordFile.
keywords: schwache Passwörter, WeakPasswordCheck, WeakPasswordList, WeakPasswordFile, verbotene Wörter, Passwortrichtlinie, ForbidLogin, ForbidCurrentPassword, öffentlicher Sektor, weak passwords, forbidden words, password policy, public sector
type: help
---
# Eigene Wörter zur Liste schwacher Passwörter hinzufügen

Für Administratoren. Angreifer probieren zuerst den eigenen Namen und die Stadt einer Organisation. Exponential lehnt
standardmäßig bekannte und häufige Passwörter ab (`[UserSettings] WeakPasswordCheck=enabled`) und lässt Sie eigene
Wörter hinzufügen ([13.6 Passwörter und Anmeldung](../../../../installation/13-sicherheitshaertung.md#136-passwörter-und-anmeldung)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
4. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Shell-Zugang oder Bearbeitungsrechte für `settings/override/site.ini.append.php`.
- Eine kurze Liste von Wörtern: Name und Abkürzung der Organisation, der Name der Site, die Stadt oder Region, häufige
  Namen von Abteilungen.

## Schritte

1. Fügen Sie die Wörter in `settings/override/site.ini.append.php` hinzu:

   ```ini
   [UserSettings]
   WeakPasswordCheck=enabled
   WeakPasswordList[]=exampletown
   WeakPasswordList[]=departmentofexample
   ```

   oder, für eine längere Liste, eine Datei mit einem Wort je Zeile, lesbar für den Benutzer der Site:

   ```ini
   [UserSettings]
   WeakPasswordFile=settings/override/weak-passwords.txt
   ```

2. Verbieten Sie außerdem den Anmeldenamen und die Wiederverwendung des aktuellen Passworts:

   ```ini
   [PasswordSettings]
   ForbidLogin=enabled
   ForbidCurrentPassword=enabled
   ```

3. `php bin/php/ezcache.php --clear-tag=ini --allow-root-user` und PHP neu laden (oder auf Velocity ein Deployment).

## Prüfen, ob es funktioniert hat

Versuchen Sie als Testbenutzer eines der Wörter als neues Passwort: Es wird abgelehnt. Versuchen Sie den Anmeldenamen:
abgelehnt. Bestehende Passwörter funktionieren weiter, bis sie geändert werden.

## Verwandte Seiten

- [Passwortrichtlinie](../../../../../eng-us/help/government/security/password-policy.md)
- [Die Mindestlänge für Passwörter auf 12 setzen](die-mindestlaenge-fuer-passwoerter-auf-12-setzen.md)
- [Das eigene Passwort ändern](../../../../../eng-us/features/6.0/modern-password-change.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
