---
title: Kartenzahlungen und PCI-Grundlagen
audience: businesses, admin, developer
category: business-solutions
order: 150
summary: Grundlagen, keine Compliance-Beratung - wie Kartenzahlungen einen Exponential-Shop erreichen, der Unterschied zwischen Zahlungsanbietern mit Weiterleitung und einem Kartenformular auf der eigenen Website, was die Erweiterung ezauthorize laut ihrem Code mit Kartendaten macht, und eine Checkliste für kleine Unternehmen.
keywords: Kartenzahlungen, Kreditkarte, PCI, PCI DSS, Kartensicherheit, ezauthorize, Authorize.Net, Weiterleitung, Kartenformular, CVV, Kartenprüfnummer, HTTPS, Zahlungssicherheit, card payments, credit card, card security, redirect, card form, security code, payment security
type: help
---
# Kartenzahlungen und PCI-Grundlagen

Diese Seite richtet sich an Geschäftsinhaber, die Kartenzahlungen annehmen möchten und verstehen müssen, was das für
die Sicherheit ihrer Website bedeutet. Sie erklärt die zwei Wege, auf denen Kartenzahlungen einen Exponential-Shop
erreichen, was die Zahlungsanbieter-Anbindungen des Projekts laut ihrem Code tatsächlich mit Kartendaten machen und was
Sie Ihren Zahlungsanbieter fragen sollten.

> **Warnung**
> **Grundlagen, keine Rechts- oder Compliance-Beratung.** Pflichten zur Kartensicherheit ergeben sich aus dem Payment
> Card Industry Data Security Standard (PCI DSS) der Kartenbranche und aus Ihrem Vertrag mit Ihrem Zahlungsanbieter
> oder Ihrer abwickelnden Bank. Lesen Sie den Text des Standards selbst, fragen Sie Ihren Anbieter, welche Pflichten
> für Ihre Anbindung gelten, und ziehen Sie einen qualifizierten Prüfer oder Berater hinzu, wo sie es verlangen. Diese
> Seite beschreibt nur, was die Software tut.

## Inhalt

1. [Wo Kartendaten eingegeben werden](#wo-kartendaten-eingegeben-werden)
2. [Zahlungsanbieter mit Weiterleitung](#zahlungsanbieter-mit-weiterleitung)
3. [Kartenformular auf der eigenen Website mit ezauthorize](#kartenformular-auf-der-eigenen-website-mit-ezauthorize)
4. [Was ezauthorize speichert](#was-ezauthorize-speichert)
5. [Die Wahl zwischen beiden](#die-wahl-zwischen-beiden)
6. [Grundlagen für jeden Shop, der Karten annimmt](#grundlagen-für-jeden-shop-der-karten-annimmt)
7. [Fragen an Ihren Zahlungsanbieter](#fragen-an-ihren-zahlungsanbieter)

## Wo Kartendaten eingegeben werden

Die wichtigste einzelne Frage der Kartensicherheit lautet: **Wo gibt der Kunde die Kartennummer ein?**

| Wo | Art der Anbindung | Kartendaten auf Ihrem Server |
|---|---|---|
| Auf der Seite des Zahlungsanbieters | Weiterleitung (`ezpaypal`) | Nein: Ihre Website schickt den Kunden weg und erhält eine Zahlungsbenachrichtigung |
| In einem Formular auf Ihrer Website | Formular auf der Website (`ezauthorize`) | Ja: Die Kartennummer läuft auf dem Weg zum Anbieter über Ihren Webserver |

Beide Arten werden auf dieselbe Weise in die Kasse eingebunden, als Workflow-Ereignis an `shop_checkout`
([Zahlungsanbieter für Unternehmen](zahlungsanbieter-fuer-unternehmen.md)).

## Zahlungsanbieter mit Weiterleitung

Bei einem Anbieter mit Weiterleitung wie ezpaypal bezahlt der Kunde auf der Seite des Anbieters; Ihre Website erhält
nur eine Benachrichtigung, die die Erweiterung beim Anbieter überprüft und mit Betrag und Währung der Bestellung
abgleicht ([Zahlungen mit PayPal](zahlungen-mit-paypal.md#was-die-erweiterung-prüft)). Ihre Website sieht die Kartennummer
nie.

Für ein kleines Unternehmen bedeutet das meist deutlich weniger, das abgesichert werden muss. Ihre Website bleibt
trotzdem wichtig, weil sie den Kunden zum Anbieter schickt und die Zahlung festhält: Halten Sie sie aktuell, auf HTTPS
und gegen Manipulation geschützt ([Sicherheitscheckliste für Shops](sicherheitscheckliste-fuer-shops.md)).

## Kartenformular auf der eigenen Website mit ezauthorize

`ezauthorize` (im `composer.json` des Hauptverzeichnisses als `se7enxweb/ezauthorize` vorgeschlagen, Version 1.4.3 in
seiner `ezinfo.php`) nimmt Kartenzahlungen über Authorize.Net an. Laut seinen Dateien gilt:

- Der Workflow zeigt auf Ihrer Website ein Kartenformular, `design/standard/templates/workflow/eventtype/result/ezauthorize_form.tpl`,
  das nach dem Namen auf der Karte, dem Kartentyp, der Kartennummer, dem Ablaufdatum und der Kartenprüfnummer fragt.
- Die Anbindung sendet die Daten mit cURL an die AIM-Schnittstelle von Authorize.Net (`classes/ezauthorizeaim.php`),
  wobei in der ausgelieferten `settings/ezauthorize.ini.append.php` die Prüfung der Antwort (`SSLVerify=true`) und die
  Hash-Prüfung (`MD5HashVerification=true`) eingeschaltet sind.
- Die ausgelieferten Einstellungen haben `TestMode=true`, wodurch Testtransaktionen gesendet werden, bis Sie es
  ausschalten.
- `doc/FEATURES` nennt Adressprüfung (AVS), die Behandlung der Kartenprüfnummer, die Prüfung des Transaktions-Hashs und
  „Requires usage of SSL to transmit payment information“.
- Ein Modul `refund` erlaubt den Mitarbeitern, eine Bestellung aus der Administration heraus zu erstatten.

Das README der Erweiterung stammt aus den Jahren von eZ Publish (es nennt Version 1.3.0 vom Oktober 2006 und PHP 4.4 als
Mindestanforderung); die Releases von 7x halten sie installierbar, aber lesen Sie ihre Dokumentation im Bewusstsein
dieses Alters und klären Sie mit Authorize.Net, ob die Schnittstelle und die Prüfverfahren, die sie verwendet, für Ihr
Konto noch unterstützt werden.

## Was ezauthorize speichert

Beim Lesen von `classes/ezauthorizegateway.php` und `classes/ezcurlgateway.php` haben wir Folgendes festgestellt:

1. Name auf der Karte, Kartennummer, Ablaufdatum und Kartenprüfnummer aus dem Formular werden in die gespeicherten
   Kontodaten der Bestellung geschrieben (das Feld `data_text_1` der Bestellung), damit die Anbindung sie im nächsten
   Schritt lesen und das Erstattungsmodul die Kartennummer später verwenden kann.
2. Sie werden **nur dann** mit GPG verschlüsselt, wenn `StoreTransactionInformation` auf `true` steht (der
   ausgelieferte Wert) **und** die separate Erweiterung `ezgpg` installiert und mit einem Schlüssel konfiguriert ist.
   Andernfalls behält der Code die Werte so, wie sie eingegeben wurden.
3. Die Erweiterung `ezgpg` ist nicht Teil des Erweiterungsverzeichnisses dieser Installation.

> **Warnung**
> Nach dem jetzigen Stand des Codes hält eine ezauthorize-Einrichtung ohne funktionierendes `ezgpg` Kartennummern und
> Kartenprüfnummern lesbar in der Bestelltabelle Ihrer Datenbank. Standards zur Kartensicherheit setzen dem Speichern
> von Kartendaten enge Grenzen, insbesondere bei der Kartenprüfnummer. Nehmen Sie ezauthorize nicht in Betrieb, ohne
> dass ein Entwickler dieses Verhalten anhand der Anforderungen Ihres Anbieters prüft und es wo nötig ändert (zum
> Beispiel die gespeicherten Werte nach der Autorisierung entfernt).

Ersetzen Sie außerdem den ausgelieferten Platzhalterwert von `MD5HashSecretWord` durch das in Ihrem Authorize.Net-Konto
konfigurierte Geheimnis, und ersetzen Sie das Kartenbild im Formular-Template, das auf eine externe `http://`-Adresse
verweist, die eine Website auf HTTPS mit einer Content Security Policy blockiert.

## Die Wahl zwischen beiden

| Frage | Weiterleitung (PayPal) | Formular auf der Website (ezauthorize) |
|---|---|---|
| Kartennummern auf Ihrem Server | Nein | Ja |
| Kunde verlässt zum Bezahlen Ihre Website | Ja | Nein |
| Arbeit vor dem Start | Konfigurieren und testen | Konfigurieren, Umgang mit Kartendaten prüfen, testen |
| Laufender Sicherheitsaufwand | Geringer | Höher |

Für die meisten kleinen Unternehmen ist ein Anbieter mit Weiterleitung die einfachere Wahl. Wenn es Ihnen wichtig ist,
auf Ihrer eigenen Website zu bleiben, fragen Sie einen Partner, ob Ihr Anbieter eine gehostete Zahlungsseite oder
gehostete Eingabefelder anbietet und ob es dafür eine Anbindung gibt oder sie als Erweiterung im Stil einer
Weiterleitung geschrieben werden kann ([Zahlungsanbieter für Unternehmen](zahlungsanbieter-fuer-unternehmen.md#andere-anbieter)).

## Grundlagen für jeden Shop, der Karten annimmt

- [ ] Die ganze Website läuft auf HTTPS ([TLS und HTTPS](../../hosting/sicherheit/tls-und-https.md)).
- [ ] Exponential und seine Erweiterungen werden aktuell gehalten ([Sicherheitsupdates und Offenlegung](../sicherheit/sicherheitsupdates-und-offenlegung.md)).
- [ ] Administratoren verwenden starke Passwörter und die zweistufige Anmeldung ([Zweistufige Anmeldung](../../../anleitungen/zwei-faktor-anmeldung.md)).
- [ ] Die Administration ist eingeschränkt ([Die Administration einschränken](../../hosting/sicherheit/die-administration-einschraenken.md)).
- [ ] Logs und Debug-Ausgaben zeigen nie Kartendaten; die Debug-Ausgabe ist im Produktivbetrieb aus.
- [ ] Backups, die Bestelldaten enthalten, sind wie die Datenbank selbst geschützt.
- [ ] Der Testmodus wird erst am Starttag nach einem vollständigen Test ausgeschaltet.

## Fragen an Ihren Zahlungsanbieter

- Welche Pflichten zur Kartensicherheit gelten für eine Website, die zu Ihnen weiterleitet, und für eine, die
  Kartendaten an Sie sendet?
- Welche Selbstauskunft oder welche Nachweise verlangen Sie von uns, und wie oft?
- Bieten Sie eine gehostete Zahlungsseite oder gehostete Kartenfelder an?
- Welche Integrationsverfahren und Signaturprüfungen unterstützen Sie noch?
- Wer bearbeitet Reklamationen und Rückbuchungen, und welche Unterlagen brauchen Sie von uns?

## Verwandte Seiten

- [Zahlungsanbieter](../../../../eng-us/help/creators/website-creators/payment-gateways.md)
- [Sicherheit für Anwendungen](../../../../eng-us/help/creators/app-creators/security-for-applications.md)
- [Sicherheitsgrundlagen für Unternehmen](../sicherheit/sicherheitsgrundlagen-fuer-unternehmen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
