---
title: Mail an Empfänger
audience: government, website-creator, admin
category: public-sector
order: 70
summary: Wer eine Mail erhält, wenn ein mit dem Information Collector von Exponential gebautes Formular gesendet wird - der Standardempfänger, ein Empfänger je Formular, die Mail-Templates, Versandeinstellungen, Testen mit dem Datei-Transport und was nicht in Mails gehört.
keywords: Mail, E-Mail-Empfänger, Empfängerattribut, Datei-Transport, gemeinsames Postfach, Testen, mail, e-mail receiver, EmailReceiver, recipient attribute, collectedinfomail, SendEmail, MailSettings, Transport, file transport, SMTP, shared mailbox, testing
type: help
---
# Mail an Empfänger

Diese Seite richtet sich an Site-Ersteller und Administratoren, die entscheiden, wohin Formulareinsendungen per Mail
gehen. Sammlungen werden immer in der Datenbank gespeichert (**Setup > Gesammelte Informationen**); die Mail ist eine
Kopie für das Team, das sie bearbeitet. Quellen:
[Formulare mit dem Information Collector](../../../../../eng-us/help/creators/website-creators/forms-with-the-information-collector.md#mail-to-a-receiver)
und [10.4 Mail](../../../../installation/10-nach-der-installation.md#104-mail-der-datei-transport-und-echter-versand).

## Inhalt

1. [Wer die Mail erhält](#wer-die-mail-erhält)
2. [Die Mail-Templates](#die-mail-templates)
3. [Versandeinstellungen](#versandeinstellungen)
4. [Testen ohne Versand](#testen-ohne-versand)
5. [Gute Praxis](#gute-praxis)

## Wer die Mail erhält

| Ebene | Einstellung | Verwendung |
|---|---|---|
| Ob überhaupt eine Mail gesendet wird | `collect.ini [EmailSettings] SendEmail` (eingeschaltet), `SendEmailList[<type>]` (Umfragen: ausgeschaltet) | je Formulartyp |
| Standardempfänger | `site.ini [InformationCollectionSettings] EmailReceiver` | ein allgemeines Postfach |
| Je Formular | ein Attribut `recipient` (Email) am Formularobjekt, von den Mail-Templates von ezwebin verwendet | jedes Formular schreibt an sein eigenes Team |
| In Ihren eigenen Templates | `{set-block scope=root variable=email_receiver}...{/set-block}` im Mail-Template | jede Regel, die Sie brauchen |

Die mitgelieferte Klasse Feedback form (`feedback_form`) hat ein Attribut **Recipient**, das Redakteure ausfüllen; mit
dem Mail-Template von ezwebin (`extension/ezwebin/design/ezwebin/templates/content/collectedinfomail/feedback.tpl`) geht
die Mail dorthin.

## Die Mail-Templates

| Template | Zweck |
|---|---|
| `content/collectedinfomail/<type>.tpl` | der Text der Mail für den Typ (form, feedback, poll) |
| `content/collectedinfo/<type>.tpl` | die Seite, die der Besucher nach dem Senden sieht (mit `Display=result`) |

Überschreiben Sie sie in Ihrem Design
([Template-Overrides](../../../../../eng-us/help/creators/website-creators/template-overrides.md)), um der Mail einen klaren Betreff
(„Neue Meldung: <Formularname>“), die Werte in einer lesbaren Reihenfolge und einen Link auf die Sammlung in der
Administration zu geben.

## Versandeinstellungen

`site.ini [MailSettings]`:

| Schlüssel | Werte |
|---|---|
| `Transport` | `sendmail`, `SMTP` oder `file` |
| `TransportServer`, `TransportPort`, `TransportConnectionType`, `TransportUser`, `TransportPassword` | Ihr SMTP-Relay |
| `AdminEmail`, `EmailSender` | die Absenderadresse |

Verwenden Sie das Mail-Relay Ihrer Organisation; die Absenderdomain muss senden dürfen (SPF, DKIM und DMARC werden auf
der Mailseite konfiguriert). Legen Sie Passwörter in `settings/override/` ab und halten Sie sie aus der
Versionsverwaltung heraus.

## Testen ohne Versand

`Transport=file` schreibt jede Mail nach `var/log/mail/` (oder `[MailSettings] FileTransportDirectory`), statt sie zu
senden. Verwenden Sie das auf Test- und Staging-Installationen und prüfen Sie die Dateien:

```bash
ls -lt var/log/mail/ | head
```

Verwenden Sie in Testformularen Testadressen auf einer reservierten Domain (zum Beispiel `example.invalid`); nie die
Adressen echter Menschen.

## Gute Praxis

| Praxis | Warum |
|---|---|
| Mails an gemeinsame Postfächer von Teams, nicht an persönliche | Mitarbeiter wechseln; Urlaub |
| Halten Sie die Mail kurz, mit einem Link auf die Sammlung, wenn Einsendungen sensible Daten enthalten | Mails werden kopiert, weitergeleitet und lange aufbewahrt |
| Senden Sie die Nachricht des Besuchers nicht automatisch an die Adresse des Besuchers zurück | jeder kann die Adresse eines anderen eingeben; das macht Ihr Formular zu einem Spam-Relay |
| Entfernen Sie die Kopien in den Postfächern nach demselben Plan wie die Sammlungen | [Aufbewahrung gesammelter Informationen](aufbewahrung-gesammelter-informationen.md) |

## Verwandte Seiten

- [Formulare aufbauen](README.md)
- [Grundlagen des Information Collectors](grundlagen-des-information-collectors.md)
- [E-Mail-Einstellungen für Administratoren](../../../../anleitungen/mail-einstellungen-administrator.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
