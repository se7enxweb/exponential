---
title: Abmeldung mit einem Klick und das Mail-Gateway
audience: government, admin
category: public-sector
order: 70
summary: Wie jede optionale E-Mail, die eine Exponential-Site sendet, vom Mail-Gateway des Kernels einen Abmeldelink und die Header List-Unsubscribe (RFC 8058) erhält, was die Site ergänzen muss (Postanschrift des Absenders, Kategorien, DKIM) und wie Sie es ohne Versand testen.
keywords: Abmeldung, Abmeldung mit einem Klick, Mail-Gateway, E-Mail-Einstellungen, Fußzeile, Widerspruch, unsubscribe, one-click unsubscribe, List-Unsubscribe, List-Unsubscribe-Post, RFC 8058, mail gate, CAN-SPAM, mail preferences, footer, DKIM, opt-out
type: help
---
# Abmeldung mit einem Klick und das Mail-Gateway

Diese Seite richtet sich an Administratoren. Jede Mail, die die Installation sendet, durchläuft das **Mail-Gateway** in
`eZMailTransport::send()`; optionale Mails erhalten eine Fußzeile mit dem Grund und einem Abmeldelink sowie die Header
`List-Unsubscribe` und `List-Unsubscribe-Post: List-Unsubscribe=One-Click`
([Standards zum Datenschutz](../../../../standards/datenschutz.md#can-spam-act-und-abmeldung-mit-einem-klick),
[E-Mail-Einstellungen für Administratoren](../../../../anleitungen/mail-einstellungen-administrator.md)).

## Inhalt

1. [Was das Gateway tut](#was-das-gateway-tut)
2. [Was die Site ergänzen muss](#was-die-site-ergänzen-muss)
3. [Die Statusseite](#die-statusseite)
4. [Testen ohne Versand](#testen-ohne-versand)

## Was das Gateway tut

- Fügt optionalen Mails den Abmeldelink (auf `mailpreferences/unsubscribe/<token>`) und die beiden Header hinzu.
- Die Abmeldung braucht keine Anmeldung, kein Formular-Token und kein Cookie; ein POST mit einem Klick wirkt sofort.
- Schreibt `From`, `Reply-To` oder `Subject` nie um.
- Hält notwendige Mails (Zurücksetzen des Passworts, Aktivierung) von optionalen Mails getrennt.

Der Datenschutzstandard bewertet das für Mails, die durch das Gateway gehen, als **Erfüllt**, wobei die folgenden
Punkte der Site überlassen bleiben.

## Was die Site ergänzen muss

- Die Postanschrift und den Namen der Organisation des Absenders in der Fußzeile (Absenderangaben auf der Statusseite).
- Eine Kategorie für jede optionale Mail: Mails ohne Kategorie erhalten keine Fußzeile und keinen Abmeldelink.
- Die DKIM-Signatur der beiden Header, durch den Mailserver.
- Behalten Sie den voreingestellten Versand je Empfänger; `SplitRecipients=disabled` sendet an mehrere Empfänger auf
  einmal, ohne `List-Unsubscribe`.

## Die Statusseite

**Setup > E-Mail-Einstellungen** (`/mailpreferences/admin/status`) zeigt den Zustand des Gateways und listet Probleme
zuerst, etwa einen leeren Organisationsnamen oder eine leere Postanschrift in der Fußzeile.

## Testen ohne Versand

Die Anleitung zu den E-Mail-Einstellungen hat einen Abschnitt zum Testen des Gateways ohne Versand von Mails; testen
Sie mit dem Datei-Transport und Adressen auf einer reservierten Testdomain, nie mit echten Menschen
([E-Mail-Einstellungen für Administratoren, Abschnitt 10](../../../../anleitungen/mail-einstellungen-administrator.md#10-die-schranke-testen-ohne-e-mails-zu-senden)).

## Verwandte Seiten

- [E-Mail-Einstellungen](../../../../funktionen/6.0/e-mail-einstellungen.md)
- [Rückläufer, Sperrliste und Listenpflege](ruecklaeufer-sperrliste-und-listenpflege.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
