---
title: Die Absenderangaben in der E-Mail-Fußzeile festlegen
audience: government, admin
category: public-sector
order: 300
summary: Schritt für Schritt - Name der Organisation, Postanschrift und Link zum Datenschutzhinweis ausfüllen, die das Mail-Gate von Exponential jeder optionalen E-Mail hinzufügt (sie werden leer ausgeliefert), und sie auf der Statusseite und in einer Test-E-Mail prüfen.
keywords: E-Mail-Fußzeile, OrganisationName, OrganisationAddress, PrivacyURL, FooterSettings, mailpreferences.ini, Absenderangaben, Postanschrift, Impressum, CAN-SPAM, CASL, exp:mail:status, öffentlicher Sektor, mail footer, sender details, postal address, public sector
type: help
---
# Die Absenderangaben in der E-Mail-Fußzeile festlegen

Für Administratoren. Jede optionale E-Mail endet mit einer Fußzeile, die den Absender nennt, mit einem Link zum
Abbestellen. Name und Postanschrift der Organisation werden **leer** ausgeliefert; die Statusseite und
`exp:mail:status` warnen, solange sie es sind
([E-Mail-Einstellungen für Administratoren: die Fußzeile](../../../../anleitungen/mail-einstellungen-administrator.md#22-die-fußzeile-wer-sendet-und-von-wo)).

## Inhalt

1. [Schritte](#schritte)
2. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
3. [Verwandte Seiten](#verwandte-seiten)

## Schritte

1. Sehen Sie sich Name und Adresse in der Vorschau an und setzen Sie sie (ein Zeilenumbruch wird `\n` geschrieben):

   ```bash
   ./console exp:ini set mailpreferences.ini/FooterSettings/OrganisationName "Department of Example" global --dry-run --allow-root-user
   ./console exp:ini set mailpreferences.ini/FooterSettings/OrganisationName "Department of Example" global --allow-root-user
   ./console exp:ini set mailpreferences.ini/FooterSettings/OrganisationAddress "Example Street 1\n12345 Exampletown" global --allow-root-user
   ```

2. Der Link zum Datenschutzhinweis: `[FooterSettings] PrivacyURL` nimmt eine vollständige Adresse mit `https://` oder
   einen Pfad wie `/privacy`; leer verwendet den Knoten aus `menu.ini [SiteInfo] PrivacyPolicyID` des öffentlichen
   Siteaccess, falls es einen gibt.

   ```bash
   ./console exp:ini set mailpreferences.ini/FooterSettings/PrivacyURL /privacy global --allow-root-user
   ```

3. Leeren Sie den INI-Cache; laden Sie PHP neu; starten Sie Velocity neu.
4. Um den Wortlaut der Fußzeile zu ändern, überschreiben Sie `design:mailpreferences/mail/footer.tpl` in Ihrem Design
   und behalten Sie den Link zum Abbestellen, den Link zum Verwalten und die Adresse bei.

## Prüfen, ob es funktioniert hat

- `php bin/php/console exp:mail:status --allow-root-user` warnt nicht mehr wegen der Absenderangaben.
- Die Statusseite von **Setup > E-Mail-Einstellungen** zeigt die Absenderangaben.
- Eine Test-E-Mail einer optionalen Kategorie (an eine Testadresse, mit dem Datei-Transport auf einem Testsystem) endet
  mit der Fußzeile und trägt `List-Unsubscribe`.

## Verwandte Seiten

- [Abbestellen mit einem Klick](../../datenschutz/abmelden-mit-einem-klick.md)
- [13.13 E-Mail: Einwilligung und Sperrliste](../../../../installation/13-sicherheitshaertung.md#1313-mail-einwilligung-und-sperrliste)
- [E-Mail-Kategorien und Opt-in](../../datenschutz/e-mail-kategorien-und-opt-in.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
