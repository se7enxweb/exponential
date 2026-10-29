---
title: Erweiterungen erklärt
audience: non-technical, customer, editor
category: plain-language
order: 16
summary: Was Erweiterungen, Designs, Themes und Templates sind, eine einfache Liste der Zusätze, die mit Exponential ausgeliefert werden, und was jeder für einen nicht technischen Menschen tut, was es nicht als Erweiterung gibt und wie Sie nach einer fragen.
keywords: Erweiterung, Zusatz, Add-on, Plugin, Design, Theme, Template, Newsletter, Karten, zweistufige Anmeldung, CAPTCHA, Schreibassistent, Layouts, Shop, was nicht enthalten ist, erklärt, extension, add-on, plugin, design, theme, template, newsletter, maps, two-step sign-in, writing assistant, layouts, shop, what is not included, explained
type: help
---
# Erweiterungen erklärt

## Inhalt

1. [Der Vergleich: Küchengeräte](#der-vergleich-küchengeräte)
2. [Zusätze, die mit Exponential ausgeliefert werden](#zusätze-die-mit-exponential-ausgeliefert-werden)
3. [Was Sie nicht finden werden](#was-sie-nicht-finden-werden)
4. [Designs, Themes und Templates](#designs-themes-und-templates)
5. [Nach einer Erweiterung fragen](#nach-einer-erweiterung-fragen)

## Der Vergleich: Küchengeräte

Eine Küche funktioniert ohne Spülmaschine, aber wenn Sie eine hinzufügen, ändert sich, was Sie tun können. Eine
**Erweiterung** ist ein solches Gerät: ein Zusatz, der der Website eine zusätzliche Fähigkeit gibt. Ein Entwickler
installiert sie und schaltet sie ein; danach erscheint sie als neue Schaltflächen, Reiter oder Felder in der
Administration. Sie installieren nie selbst eine.

## Zusätze, die mit Exponential ausgeliefert werden

Exponential wird mit vielen Erweiterungen verteilt (die Liste steht in der Datei `composer.json` der Installation).
Welche auf Ihrer Website **eingeschaltet** sind, entscheidet, wer sie gebaut hat. Die wichtigsten für Menschen ohne
technische Vorkenntnisse:

| Erweiterung | Was sie für Sie tut | Mehr lesen |
|---|---|---|
| Exponential Layouts (`explayouts`) | Seiten per Ziehen und Ablegen mit Blöcken anordnen | [Eine Seite mit Layouts gestalten](../erste-schritte/eine-seite-mit-layouts-gestalten.md) |
| Newsletter (`cjw_newsletter`) | Listen von Abonnenten und das Versenden von Newslettern | [Newsletter](../../kunden/wachstum/newsletter.md) |
| Zweistufige Anmeldung (`sevenx_authentication_2fa`) | Ein Code von Ihrem Telefon oder per E-Mail nach Ihrem Passwort | [Ihr Passwort und die zweistufige Anmeldung](../erste-schritte/ihr-passwort-und-die-zweistufige-anmeldung.md) |
| Exponential Intelligence (`expai`) | Ein Schreibassistent, der Entwürfe vorschlägt | [Den Schreibassistenten nutzen](../erste-schritte/den-schreibassistenten-nutzen.md) |
| Karten (`ezgmaplocation`) | Ein Feld für einen Kartenstandort bei einem Objekt | [Lokale Unternehmen](../../lokale-unternehmen/lokale-unternehmen-hier-beginnen.md) |
| Spamschutz (`hcaptcha`, `recaptcha`) | Eine Prüfung bei Formularen, ob ein Mensch und kein Roboter sie ausgefüllt hat | [Spamschutz für Formulare](../../ersteller/website-ersteller/spamschutz-fuer-formulare.md) |
| Tags (`eztags`) | Schlagwörter, um Inhalte zu gruppieren | [Wie verschlagworte ich Inhalte?](../../handbuch/wie-verschlagworte-ich-inhalte.md) |
| Sitemaps für Suchmaschinen (`bcgooglesitemaps`) | Eine Liste der Seiten für Suchmaschinen | [Sitemaps](../../ersteller/website-ersteller/sitemaps.md) |
| Zahlung im Shop (`ezpaypal`, `ezauthorize`) | Zahlung per Karte oder PayPal im eingebauten Shop | [Zahlungsanbieter](../../kunden/handel/zahlungsanbieter.md) |
| Mehrfach-Upload (`ezmultiupload`) | Viele Dateien auf einmal hochladen | [Bilder, Medien und Uploads](../../medien-und-uploads.md#dateien-hochladen) |

**Setup > Systeminformationen** (wenn Ihre Rolle es erlaubt) listet die eingeschalteten Erweiterungen auf
([Wie sehe ich aktive Erweiterungen und Systeminformationen?](../../handbuch/wie-sehe-ich-aktive-erweiterungen-und-systeminformationen.md)).

## Was Sie nicht finden werden

Seien Sie vorsichtig bei jedem, der Ihnen das "fertig eingebaut" verspricht; es braucht einen externen Dienst oder
individuelle Arbeit eines Entwicklers:

- Terminbuchung, Tischreservierung oder Ticketverkauf für Veranstaltungen;
- ein eigenes Spendenmodul mit wiederkehrenden Spenden
  ([Spenden- und Fundraising-Seiten](../../unternehmen/gemeinnuetzige/spenden-und-spendenaufrufe.md));
- ein Import per Mausklick aus einem anderen Blog- oder Websitesystem;
- ein fertiges Kommentarsystem für Leser auf jeder Website: Die Demonstrationsinstallation hat einen Inhaltstyp
  *Kommentar*, aber ob und wie Kommentare funktionieren, hängt davon ab, wie Ihre Website gebaut wurde
  ([Blog: hier beginnen](../../kunden/typen/blogs-hier-beginnen.md)); fragen Sie also, bevor Sie damit planen.

Brauchen Sie etwas davon, bitten Sie einen Entwickler oder [Partner](../../partner/partner-hier-beginnen.md), einen
externen Dienst, den Sie verlinken oder einbetten können, mit individueller Arbeit zu vergleichen.

## Designs, Themes und Templates

Wie die Website **aussieht**, ist keine Einstellung einer Erweiterung, die Sie im Bearbeitungsformular ändern:

- Ein **Design** oder **Theme** ist das Aussehen der ganzen Website: Farben, Schriften, Aufbau von Kopf- und
  Fußbereich. Exponential bringt fertige mit ([Ein Design oder Theme wählen](../../ersteller/website-ersteller/ein-design-oder-theme-waehlen.md)).
- **Templates** sind die Dateien, die Ihre Inhalte in Webseiten verwandeln. Ein Designer oder Entwickler bearbeitet sie.

Sie ändern den **Inhalt**. Ein Designer ändert das **Aussehen**. Wenn etwas überall falsch aussieht, zum Beispiel der
Fußbereich, ist das eine Frage des Designs.

## Nach einer Erweiterung fragen

1. Beschreiben Sie den Bedarf, nicht das Werkzeug: "Besucher sollen unseren Laden auf einer Karte finden".
2. Fragen Sie, ob eine mitgelieferte Erweiterung das schon kann (siehe die Tabelle oben).
3. Fragen Sie, was sie für Redakteure ändert und ob sie ein externes Konto braucht (einen Anbieter für Karten oder
   CAPTCHA, einen Zahlungsanbieter, einen KI-Anbieter) mit eigenen Kosten und Bedingungen.
4. Bitten Sie darum, dass sie zuerst auf einer Kopie der Website getestet wird.

## Verwandte Seiten

- [Begriffe erklärt](README.md)
- [Was Sie nie tun müssen](../was-sie-nie-tun-muessen.md)
- [Funktionen, die Kunden oft übersehen](../../kunden/nutzen/oft-uebersehene-funktionen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
