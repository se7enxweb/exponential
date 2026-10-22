---
title: OEM-Produkt-Wiederverkäufer
audience: reseller, partner, developer
category: reselling
order: 100
summary: Wie Sie Ihr eigenes Produkt auf Basis von Exponential verkaufen - eine Paketlösung, ein gehostetes Produkt, eine Appliance oder ein Image - im Rahmen der OEM-Wiederverkaufsmöglichkeit des Partnerprogramms, mit den Pflichten aus der GPL bei seiner Weitergabe, seiner Benennung, wie Sie es auf unterstützten Releases halten, und den Fragen an das Partnerteam.
keywords: OEM, OEM-Produkt, Wiederverkauf, Paketlösung, SaaS, Appliance, Image, Seitenpaket, Weitergabe, GPL, Quellcode, Produktname, Support des Kerns, OEM product, resale, packaged solution, appliance, image, site package, distribution, source code, product name, support of the core
type: help
---
# OEM-Produkt-Wiederverkäufer

Diese Seite ist für Softwareunternehmen und Agenturen, die aus ihrer Arbeit mit Exponential ein Produkt machen, das
sie viele Male verkaufen. „OEM-Optionen stehen ebenfalls zur Verfügung, um Ihre eigenen Produkte auf Grundlage von
Exponential weiterzuverkaufen“, und „OEM Resale Opportunities“ ist ein Vorteil auf jeder Partnerstufe
(<https://exponential.earth/partner/benefits>, gelesen am 18. Oktober 2026). Die Programmseite formuliert es so:
„Partner können auch ihre eigenen Produkte auf Grundlage von Exponential weiterverkaufen (OEM-Optionen)“.

## Inhalt

1. [Produktformen](#produktformen)
2. [Das Produkt auf Exponential aufbauen](#das-produkt-auf-exponential-aufbauen)
3. [Die GPL und Ihr Produkt](#die-gpl-und-ihr-produkt)
4. [Das Produkt benennen](#das-produkt-benennen)
5. [Fragen an das Partnerteam](#fragen-an-das-partnerteam)
6. [Das Produkt gesund halten](#das-produkt-gesund-halten)

## Produktformen

| Form | Beispiel | Weitergabe? |
|---|---|---|
| Paketlösung | Ein Intranet, eine Schul-Website oder ein Newsroom, eingerichtet aus Ihrem Seitenpaket und Ihren Erweiterungen | Ja, wenn der Kunde die Dateien erhält |
| Gehostetes Produkt (SaaS) | Sie betreiben die Lösung für viele Kunden; diese nutzen sie im Browser | Nicht, solange Sie sie nur betreiben |
| Appliance oder Image | Ein vorinstalliertes Server-Image oder ein Container | Ja |
| Erweiterungspaket | Ihre Erweiterungen mit Installation und Support | Ja, wenn Sie den Code ausliefern ([Erweiterungs- und Theme-Pakete](erweiterungs-und-theme-pakete.md)) |

## Das Produkt auf Exponential aufbauen

| Bedarf | Was es gibt | Seite |
|---|---|---|
| Ein wiederholbarer Ausgangspunkt | Ein **Seitenpaket**: ein Exponential-Paket vom Typ `site`, installiert vom Setup-Assistenten, vom Kickstarter oder mit `exp:install --package=<name>` | [Seitenpakete für Hoster](../../hosting/bereitstellung/seitenpakete-fuer-hoster.md) |
| Unbeaufsichtigte Installationen | Der Kickstarter und `exp:install` | [Der Kickstarter für Hoster](../../hosting/bereitstellung/der-kickstarter-fuer-hoster.md), [exp:install](../../hosting/bereitstellung/exp-install-fuer-hoster.md) |
| Ihre eigenen Funktionen | Erweiterungen, je Installation geladen | [Erweiterungen](../../../anleitungen/extensions.md) |
| Mehrere Websites aus einem Produkt | Siteaccesses auf einer Installation | [Multisite-Angebote](../angebote/multisite-angebote.md) |
| Eine Offline- oder kontrollierte Installation | Ein lokales Paket-Repository unterhalb von `var/storage/packages/` | [Seitenpakete für Hoster](../../hosting/bereitstellung/seitenpakete-fuer-hoster.md#installationen-ohne-internet-und-unter-kontrolle) |

## Die GPL und Ihr Produkt

Exponential steht unter der GPL v2 oder später. Sie dürfen Geld für Ihr Produkt, Ihre Arbeit und Ihre
Dienstleistungen verlangen. Wenn Sie das Produkt weitergeben, erhalten die Empfänger die Lizenz, die
Urheberrechtshinweise und den Quellcode und dürfen es weitergeben.

- [ ] Liefern Sie `LICENSE`, `LICENSE.md` und `COPYRIGHT.md` unverändert mit.
- [ ] Stellen Sie den Quellcode von allem bereit, was Sie weitergeben, einschließlich Ihrer Änderungen an
      Exponential.
- [ ] Versehen Sie geänderte Dateien mit einem Hinweis und dem Datum (GPL Abschnitt 2a).
- [ ] Prüfen Sie die eigene Lizenz jeder Erweiterung unter **Info > Über**, bevor Sie „alles steht unter der GPL“
      schreiben.
- [ ] Fragen Sie einen Rechtsberater, ob Ihre eigenen Erweiterungen unter der GPL stehen müssen, wenn Sie sie mit
      Exponential weitergeben.

Vollständige Liste: [Checkliste der Pflichten bei der Weitergabe](../verantwortlichkeiten/checkliste-der-pflichten-bei-der-weitergabe.md).
Dies ist keine Rechtsberatung.

## Das Produkt benennen

- Geben Sie dem Produkt einen eigenen Namen und sagen Sie klar, dass es „auf Exponential basiert“.
- Ob das Produkt „Exponential“ in seinem Namen tragen darf und wie, ist nicht veröffentlicht: Fragen Sie das
  Partnerteam ([Namen und Marke für Wiederverkäufer](../verkauf/namen-und-marke.md)).
- Benennen Sie es nie nach dem alten Produktnamen und deuten Sie nie an, es sei von 7x oder der Foundation
  hergestellt oder empfohlen, ohne deren schriftliche Zustimmung.

```text
<Product> is a <kind of solution> by <Your company>, based on Exponential, the open source content
management system led by 7x and operated by the Exponential Foundation. Exponential is free software under the
GNU General Public License, version 2 or later.
```

## Fragen an das Partnerteam

Der OEM-Vorteil ist eine Zusage des Programms; seine Details sind nicht veröffentlicht. Fragen Sie
<partners@exponential.earth>:

- [ ] Darf das Produkt „Exponential“ in seinem Namen oder seiner Beschreibung tragen, und wie?
- [ ] Führt 7x OEM-Produkte auf oder bewirbt sie?
- [ ] Zählen OEM-Verkäufe als Umsatz für unsere Partnerstufe?
- [ ] Wer unterstützt den Kern von Exponential in unserem Produkt: wir allein oder 7x als unsere Rückendeckung im
      Rahmen unseres Tarifs?

## Das Produkt gesund halten

- **Bleiben Sie auf der gepflegten Linie.** Nur das neueste 6.0.x erhält Fehlerbehebungen (`SECURITY.md`); ein
  Produkt, das auf einem alten Tag eingefroren ist, altert schnell ([Kunden aktuell halten](../kunden/kunden-aktuell-halten.md)).
- **Geben Sie allgemeine Fehlerbehebungen an das Projekt zurück**, damit Ihr Produkt nicht für immer einen privaten
  Fork mitschleppt ([Code zurückgeben](../../partner/code-zurueckgeben.md)).
- **Versionieren Sie Ihr Produkt getrennt** und veröffentlichen Sie, auf welchem Exponential-Release jede Version
  aufbaut.
- **Testen Sie jedes Exponential-Release**, bevor Ihre Kunden es erhalten ([Releases testen](../../partner/releases-testen.md)).

## Verwandte Seiten

- [OEM und Wiederverkauf](../../partner/oem-und-wiederverkauf.md)
- [Vertriebspartner und Wiederverkäufer](../../partner/vertriebspartner.md)
- [Paketersteller: hier beginnen](../../../../eng-us/help/bundlers/bundlers-start-here.md)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
