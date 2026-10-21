---
title: Digitale Produkte und Downloads
audience: businesses, website-creator, developer
category: business-solutions
order: 170
summary: Dateien wie E-Books, Software, Schnittmuster oder Musik mit Exponential verkaufen - was der eingebaute Shop und das Download-System bieten, was für die automatische Auslieferung nach der Zahlung fehlt, und drei ehrliche Wege, die Lücke zu schließen.
keywords: digitale Produkte, Downloads, E-Books, Softwareverkauf, Dateiverkauf, ezbinaryfile, content/download, geschützte Downloads, Auslieferung nach Zahlung, Lizenzen, USt auf digitale Waren, digital products, downloads, e-books, software sales, file sales, protected downloads, delivery after payment, licenses, digital goods VAT
type: help
---
# Digitale Produkte und Downloads

Diese Seite richtet sich an Geschäftsinhaber, die Dateien verkaufen möchten: E-Books, Software, Vorlagen,
Schnittmuster, Musik, Kursmaterial. Sie erklärt, welche Teile Exponential bereits hat, was nicht enthalten ist und wie
ein Unternehmen die Lücke mit einer kleinen Erweiterung oder einem manuellen Ablauf schließen kann.

## Inhalt

1. [Die kurze Antwort](#die-kurze-antwort)
2. [Was es bereits gibt](#was-es-bereits-gibt)
3. [Was fehlt](#was-fehlt)
4. [Drei Wege, Dateien auszuliefern](#drei-wege-dateien-auszuliefern)
5. [Die Produkte einrichten](#die-produkte-einrichten)
6. [Steuern auf digitale Produkte](#steuern-auf-digitale-produkte)
7. [Checkliste](#checkliste)

## Die kurze Antwort

Exponential kann ein Produkt verkaufen, und es kann eine Datei so schützen, dass nur bestimmte Benutzer sie
herunterladen dürfen. Was der Kernel **nicht** tut, ist, beides zu verbinden: Es gibt keinen eingebauten Schritt, der
dem Käufer Zugriff auf eine Datei gibt, sobald die Bestellung bezahlt ist. Wir haben das Shop-Modul (`kernel/shop/`)
und die Download-Ansicht nach einer solchen Verbindung durchsucht und keine gefunden. Die Lücke ist klein und gut
verstanden, und die Möglichkeiten stehen unten.

## Was es bereits gibt

| Baustein | Was er tut | Lesen Sie |
|---|---|---|
| Dateifeld (`ezbinaryfile`) | Hält eine Datei an einem beliebigen Inhaltsobjekt, auch an einem Produkt | [Dateien und Downloads](../../../../eng-us/help/creators/website-creators/files-and-downloads.md) |
| `content/download` | Sendet eine Datei erst nach der Prüfung, ob der Besucher das Objekt lesen darf | dieselbe Seite, „How a download works“ |
| Sektionen und Rollen | Legen fest, wer welche Inhalte lesen darf | [Sektionen und Objektzustände](../../../../eng-us/help/creators/website-creators/sections-and-object-states.md), [Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md) |
| Benutzergruppen | Sammeln die Kunden, die herunterladen dürfen | [Mitgliederregistrierung und Mitgliederbereiche](../../../../eng-us/help/creators/website-creators/member-registration-and-areas.md) |
| Workflow-Auslöser im Shop | Führen an der Kasse einen eigenen Schritt aus | [Warenkorb, Kasse und Bestellungen](../../../../eng-us/help/creators/website-creators/basket-checkout-and-orders.md#workflow-triggers) |
| Dauerhafte Belege | Ein signierter Link zur Bestellung, jahrelang gültig | [Bestellbelege und Bestätigungsmails](bestellbelege-und-bestaetigungsmails.md) |

Die Berechtigungsprüfung der Download-Ansicht bedeutet, dass eine Datei in einer Sektion nur für Mitglieder auch nur
von Mitgliedern heruntergeladen werden kann, und Dateien dürfen nie direkt aus dem Speicherverzeichnis verlinkt
werden, was die Prüfung umgehen würde
([Dateien und Downloads](../../../../eng-us/help/creators/website-creators/files-and-downloads.md#protected-downloads)).

## Was fehlt

- Ein Schritt, der dem Käufer Zugriff auf die gekaufte Datei gewährt, wenn die Zahlung bestätigt ist.
- Download-Links für **Gäste**, die kein Konto haben, dem man Zugriff gewähren könnte.
- Begrenzungen der Anzahl der Downloads oder Links, die ablaufen.
- Lizenzschlüssel für Software.

## Drei Wege, Dateien auszuliefern

### 1. Manuelle Auslieferung

Wenn eine Bestellung den Status **Paid** erreicht, schickt ein Mitarbeiter die Datei oder fügt das Konto des Kunden der
Benutzergruppe hinzu, die sie lesen darf, und setzt die Bestellung auf **Completed**
([Bestellungen und Bestellstatus](bestellungen-und-bestellstatus.md)).

- Gut für: eine Handvoll Bestellungen pro Woche.
- Kosten: Arbeitszeit der Mitarbeiter; Wartezeit für den Kunden.

### 2. Zugang als Mitgliedschaft

Verkaufen Sie Zugang statt Dateien: Das Produkt ist eine Mitgliedschaft, die Dateien liegen in einer geschützten
Sektion, und der Kauf der Mitgliedschaft bringt den Kunden in die Gruppe, die sie lesen darf. Die Gruppe kann anfangs
von Hand zugewiesen werden.

- Gut für: Sammlungen von Schnittmustern, Kurse, ältere Ausgaben.
- Lesen Sie: [Inhalte nur für Mitglieder](../mitglieder/inhalte-nur-fuer-mitglieder.md).

### 3. Eine kleine Erweiterung

Ein Entwickler schreibt ein Workflow-Ereignis für die Kasse, das nach bestätigter Zahlung den Käufer entweder einer
Benutzergruppe mit Lesezugriff auf die Dateien des gekauften Produkts hinzufügt oder einen signierten, zeitlich
begrenzten Download-Link schickt (in der Idee ähnlich dem signierten Beleg-Link). Anleitung:
[Einen Workflow-Ereignistyp schreiben](../../../../eng-us/help/creators/app-creators/writing-a-workflow-event-type.md),
[Workflows und Ereignisse](../../../../eng-us/help/creators/app-creators/workflows-and-events.md),
[Zugriffskontrolle im Code](../../../../eng-us/help/creators/app-creators/access-control-in-code.md).

- Gut für: Shops, in denen Downloads das Hauptgeschäft sind.
- Bitten Sie den Entwickler, sie als Erweiterung zu veröffentlichen, damit andere sie gemeinsam mit Ihnen nutzen und
  pflegen können ([Erweiterungen zurückgeben: Ihre Arbeit mit allen teilen](../../erweiterungen-zurueckgeben.md)).

## Die Produkte einrichten

- Verwenden Sie eine Produktklasse mit einem Preis und, getrennt davon, ein geschütztes Dateiobjekt für die
  Lieferung: Die öffentliche Produktseite darf die Datei nicht selbst tragen, sonst kann jeder, der die Seite lesen
  kann, sie herunterladen.
- Lassen Sie den Versand bei rein digitalen Bestellungen weg ([Versand und Bearbeitung](versand-und-bearbeitung.md)).
- Nennen Sie den Kunden Format und Größe vor dem Kauf; für viele Websites ist das auch eine Anforderung der
  Barrierefreiheit
  ([Dateien und Downloads](../../../../eng-us/help/creators/website-creators/files-and-downloads.md#download-links-in-templates)).
- Große Dateien: Für Redakteure gelten die Upload-Grenzen von PHP, und sehr große Dateien liegen besser bei einem
  Dienst, der dafür gebaut ist ([Dateien und Downloads](../../../../eng-us/help/creators/website-creators/files-and-downloads.md#large-files)).
- Die Erweiterung `enhancedezbinaryfile` fügt dem Dateifeld Optionen hinzu
  ([enhancedezbinaryfile](../../../../eng-us/features/6.0/extensions/enhancedezbinaryfile.md)).

## Steuern auf digitale Produkte

Digitale Produkte werden oft anders besteuert als physische, manchmal nach dem Land des Käufers. Die USt-Regeln des
Shops können einen Satz pro Land des Kunden und Produktkategorie ausdrücken ([USt-Regeln und USt-Typen](../../../../eng-us/help/businesses/stores/vat-rules-and-vat-types.md)),
eine Produktkategorie „Digital“ mit eigenen Regeln ist also möglich. Welche Sätze gelten, ist eine Frage für Ihren
Steuerberater.

## Checkliste

- [ ] Auslieferungsweg gewählt: manuell, Mitgliedschaft oder Erweiterung.
- [ ] Die Dateien liegen in einer Sektion, die Besucher nicht lesen können; abgemeldet getestet.
- [ ] Produktseiten tragen nicht die zu liefernde Datei.
- [ ] Kunden kennen Format, Größe und den Weg, auf dem sie die Datei erhalten.
- [ ] Digitale Produkte haben eine eigene Produktkategorie für die USt-Regeln, wenn Ihr Berater das sagt.
- [ ] Ein Testkauf liefert die Datei innerhalb der Zeit, die Sie versprechen.

## Verwandte Seiten

- [Dateien und Downloads](../../../../eng-us/help/creators/website-creators/files-and-downloads.md)
- [Dateien und digitale Sammlungen](../bibliotheken/dateien-und-digitale-sammlungen.md)
- [Was der eingebaute Shop kann](../../../../eng-us/help/businesses/stores/what-the-built-in-shop-does.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
