---
title: Erklärung zur Barrierefreiheit für Non-Profit-Organisationen
audience: non-profit, government, editor, project-manager
category: non-profits
order: 40
summary: Wie eine Non-Profit-Organisation eine ehrliche Erklärung zur Barrierefreiheit für ihre Website mit Exponential schreibt und pflegt - was sie enthält, wie Sie den Anteil der Software beschreiben, ohne zu übertreiben (das Projekt stuft admin4 als Teilweise erfüllt ein und veröffentlicht kein VPAT und keinen ACR), bekannte Probleme, ein Weg für Rückmeldungen, ein Termin zur Überprüfung und eine Vorlage zum Ausfüllen.
keywords: Erklärung zur Barrierefreiheit, Ehrlichkeit, WCAG 2.2, Teilweise erfüllt, bekannte Probleme, Rückmeldung, Kontakt, Überprüfungstermin, Vorlage, Non-Profit, Konformität, accessibility statement, Partly complies, known issues, feedback, VPAT, ACR
type: help
---
# Erklärung zur Barrierefreiheit für Non-Profit-Organisationen

Diese Seite richtet sich an die Leitung der Kommunikation oder die Geschäftsführung, die die Erklärung unterzeichnet.
Eine Erklärung zur Barrierefreiheit sagt Besuchern, wie barrierefrei Ihre Website ist, was bekanntermaßen nicht
funktioniert und wie sie Sie erreichen, wenn sie etwas daran hindert. Öffentliche Stellen sind oft verpflichtet, eine
zu veröffentlichen; für eine Non-Profit-Organisation ist sie gute Praxis und manchmal eine Bedingung von Geldgebern.
Der ausführliche Leitfaden ist [Eine Erklärung zur Barrierefreiheit schreiben](../../verwaltung/barrierefreiheit/eine-erklaerung-zur-barrierefreiheit-schreiben.md).

## Inhalt

1. [Was sie enthält](#was-sie-enthält)
2. [Die Software ehrlich beschreiben](#die-software-ehrlich-beschreiben)
3. [Bekannte Probleme](#bekannte-probleme)
4. [Rückmeldung und Antwort](#rückmeldung-und-antwort)
5. [Eine Vorlage zum Ausfüllen](#eine-vorlage-zum-ausfüllen)
6. [Aktuell halten](#aktuell-halten)

## Was sie enthält

| Teil | Inhalt |
|---|---|
| Selbstverpflichtung | Dass Sie möchten, dass alle die Website nutzen können |
| Standard | Der Standard, an dem Sie messen, zum Beispiel WCAG 2.2 Stufe AA |
| Stand | Ihre ehrliche Einschätzung: vollständig, teilweise oder nicht konform, gestützt auf Tests, die Sie tatsächlich durchgeführt haben |
| Bekannte Probleme | Was nicht funktioniert, und die Alternative bis dahin |
| Rückmeldung | Wie man ein Problem meldet und den Inhalt auf anderem Weg erhält |
| Methode und Datum | Wie und wann Sie getestet haben; wann Sie die Erklärung überprüfen |

## Die Software ehrlich beschreiben

Ihre Erklärung handelt von **Ihrer Website**. Wenn Sie die Software erwähnen, verwenden Sie die Worte des Projekts
selbst und werten Sie sie nicht auf ([Was Exponential bereitstellt](../../verwaltung/barrierefreiheit/was-exponential-bietet.md)):

- Die Administrationsoberfläche admin4 ist nach WCAG 2.2 AA gestaltet und mit Regressionstests geprüft; das Projekt
  stuft sie als „Teilweise erfüllt“ ein und gibt keine Konformitätserklärung ab.
- Für Exponential gibt es kein VPAT, keinen Accessibility Conformance Report und keinen WCAG-EM-Bericht.
- Öffentliche Designs werden vom Projekt nicht geprüft; Ihr öffentliches Design liegt in Ihrer Verantwortung.

Schreiben Sie nie „unser CMS ist WCAG-zertifiziert“. Wenn Sie einen formalen Bericht brauchen, siehe
[Einen ACR erstellen](../../verwaltung/barrierefreiheit/einen-acr-vorbereiten.md).

## Bekannte Probleme

Führen Sie sie klar auf, zusammen mit dem, was Sie dagegen tun. Typische Einträge für die Website einer
Non-Profit-Organisation:

| Problem | Alternative bis dahin | Geplante Behebung |
|---|---|---|
| Ältere Jahresveröffentlichungen als PDF sind nicht getaggt | Zusammenfassungen auf HTML-Seiten; Zusendung auf Anfrage | Barrierefreie PDFs ab dem nächsten Jahr |
| Einigen älteren Videos fehlen Untertitel | Transkript auf Anfrage | Untertitel bis zu einem Termin |
| Das CAPTCHA im Formular für Ehrenamtliche ist eventuell schwer zu lösen | Bewerbung per E-Mail oder Telefon | Einstellungen des Anbieters überprüfen |
| Das eingebettete Spendenformular (falls verwendet) haben wir nicht getestet | Der Spendenlink öffnet die eigene Seite der Plattform | Testen oder durch einen Link ersetzen |

Schreiben Sie Ihre eigene Liste aus Ihren eigenen Tests ([Grundlagen der Barrierefreiheit für Non-Profit-Organisationen](barrierefreiheit-grundlagen-fuer-non-profit-organisationen.md#testen-ohne-budget)).

## Rückmeldung und Antwort

- Ein Kontaktformular oder eine Adresse auf der Seite mit der Erklärung und ein Link auf die Erklärung in der Fußzeile
  jeder Seite.
- Versprechen Sie eine Antwortzeit, die Sie einhalten können.
- Halten Sie fest, was Menschen melden und was Sie behoben haben ([Überwachung und Rückmeldungen](../../verwaltung/barrierefreiheit/ueberwachung-und-rueckmeldungen.md)).
- Ein Kontaktformular wird mit dem Informationssammler gebaut ([Ein Kontaktformular bauen](../../ersteller/website-ersteller/ein-kontaktformular-bauen.md)).

## Eine Vorlage zum Ausfüllen

```text
Accessibility statement for [site address]

[Organization] wants as many people as possible to be able to use this website.

Standard: We aim to meet WCAG 2.2 level AA.

Status: This website is [fully / partly / not] conforming. We last tested it on [date]
by [method: keyboard testing, automated checks, testing with users, outside audit].

What does not work yet:
- [issue] - meanwhile [alternative] - we plan to fix it by [date]

Feedback: If you cannot use part of this site, contact [address / form / phone].
We reply within [days].

This statement was prepared on [date] and will be reviewed by [date].
```

Übertragen Sie den Text der Vorlage für eine deutschsprachige Website sinngemäß ins Deutsche.

## Aktuell halten

- Überprüfen Sie die Erklärung jedes Jahr und nach einer Neugestaltung ([Jährliche Checkliste](../checklisten/jaehrliche-checkliste.md)).
- Legen Sie sie in einem Bereich ab, den nur Mitarbeitende bearbeiten dürfen, damit Ehrenamtliche sie nicht
  versehentlich ändern ([Ehrenamtliche als Redakteure](../ehrenamtliche/ehrenamtliche-als-redakteure.md#seiten-die-nur-mitarbeitende-ändern-dürfen)).
- Versionen bewahren den Verlauf der Erklärung ([Versionen und das Audit-Protokoll](../transparenz/versionen-und-das-audit-protokoll.md)).

## Verwandte Seiten

- [Barrierefreiheit für Non-Profit-Organisationen](README.md)
- [Eine Erklärung zur Barrierefreiheit schreiben](../../verwaltung/barrierefreiheit/eine-erklaerung-zur-barrierefreiheit-schreiben.md)
- [Was nicht abgedeckt ist](../../verwaltung/barrierefreiheit/was-nicht-abgedeckt-ist.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
