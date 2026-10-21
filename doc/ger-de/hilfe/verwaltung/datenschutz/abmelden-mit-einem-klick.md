---
title: Abmelden mit einem Klick
audience: government, admin, project-manager
category: public-sector
order: 130
summary: Für öffentliche Stellen, die Massen-E-Mails versenden - wie Exponential den Widerruf so einfach macht wie die Einwilligung: ein Abmeldelink und List-Unsubscribe-Header (RFC 8058) in jeder optionalen E-Mail, ohne Anmeldung, mit sofortiger Wirkung, und was die Website hinzufügen muss (Postanschrift, DKIM, Kategorien).
keywords: Abmelden, Abmelden mit einem Klick, List-Unsubscribe, List-Unsubscribe-Post, RFC 8058, RFC 2369, Widerruf der Einwilligung, DSGVO Artikel 7 Absatz 3, CAN-SPAM, E-Mail-Gate, Fußzeile, DKIM, Massenversender, öffentlicher Sektor, unsubscribe, one-click unsubscribe, withdrawal of consent, bulk sender
type: help
---
# Abmelden mit einem Klick

Diese Seite richtet sich an Administratoren und Kommunikationsteams. Der Widerruf einer Einwilligung muss so einfach
sein wie ihre Erteilung, und große Postfachanbieter verlangen von Massenversendern das Abmelden mit einem Klick. Das
E-Mail-Gate von Exponential fügt beides jeder optionalen E-Mail hinzu
([Datenschutzstandards: CAN-SPAM Act und Abmelden mit einem Klick](../../../standards/datenschutz.md#can-spam-act-und-abmeldung-mit-einem-klick)).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Was jede optionale E-Mail enthält](#was-jede-optionale-e-mail-enthält)
2. [Was bei einem Klick geschieht](#was-bei-einem-klick-geschieht)
3. [Was die Website hinzufügen muss](#was-die-website-hinzufügen-muss)
4. [Prüfen](#prüfen)
5. [Verwandte Seiten](#verwandte-seiten)

## Was jede optionale E-Mail enthält

Hinzugefügt von `expMailGate::decorate()`:

- eine Fußzeile mit dem Grund, aus dem die Person die E-Mail erhält, einem Link **Abmelden** und einem Link zu ihrer
  Einstellungsseite;
- `List-Unsubscribe`: ein HTTPS-Link zu `mailpreferences/unsubscribe/<token>`;
- `List-Unsubscribe-Post: List-Unsubscribe=One-Click` (RFC 8058, aufbauend auf RFC 2369).

Das Gate schreibt `From`, `Reply-To` oder `Subject` nie um. Notwendige E-Mails (Passwort zurücksetzen, Aktivierung,
Bestellungen) werden getrennt gehalten und enthalten keine Werbung. Das Projekt bewertet dies für E-Mails, die über das
E-Mail-Gate gehen, mit **„Erfüllt“**.

## Was bei einem Klick geschieht

- Keine Anmeldung, kein Formular-Token und kein Cookie nötig; ein POST mit `List-Unsubscribe=One-Click` wirkt sofort.
- Links laufen standardmäßig nicht ab.
- Die Änderung wird mit ihrer Quelle im Einwilligungsprotokoll aufgezeichnet.
- Auf der Einstellungsseite stoppt **Alle optionalen E-Mails ausschalten** jede optionale Art auf einmal.

## Was die Website hinzufügen muss

- **Die Postanschrift des Absenders** in der Fußzeile: `mailpreferences.ini [FooterSettings] OrganisationName` und
  `OrganisationAddress` (Absenderangaben auf der Statusseite). Ohne sie gehen optionale E-Mails ohne Anschrift hinaus.
- **Eine Kategorie für jede kommerzielle oder optionale E-Mail**: E-Mails, die ohne `eZMail::setCategory()` versendet
  werden, erhalten weder Fußzeile noch Abmeldelink.
- **DKIM-Signatur** der Header: erledigt der Mailserver.
- **Versand pro Empfänger**: Behalten Sie die Voreinstellung bei; `SplitRecipients=disabled` sendet an mehrere
  Empfänger gleichzeitig ohne `List-Unsubscribe`.
- **Das Website-Geheimnis** in `settings/override/mailpreferences.ini.append.php` gesichert: Ohne es funktionieren die
  Links in bereits versendeten E-Mails nicht mehr.

## Prüfen

```bash
php bin/php/console exp:mail:status --allow-root-user     # five times ok
```

Senden Sie dann eine Test-E-Mail einer optionalen Kategorie an ein Testpostfach und prüfen Sie ihre Header auf
`List-Unsubscribe` und `List-Unsubscribe-Post`. Testen Sie das Gate ohne Versand, wie unter
[Das Gate testen, ohne E-Mails zu versenden](../../../anleitungen/mail-einstellungen-administrator.md#10-die-schranke-testen-ohne-e-mails-zu-senden)
beschrieben.

## Verwandte Seiten

- [Datenschutzstandards](../../../standards/datenschutz.md)
- [E-Mail-Einstellungen für Administratoren](../../../anleitungen/mail-einstellungen-administrator.md)
- [Die Sperrliste](sperrliste.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
