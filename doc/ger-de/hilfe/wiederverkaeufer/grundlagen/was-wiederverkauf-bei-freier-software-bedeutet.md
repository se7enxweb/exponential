---
title: Was Wiederverkauf bei freier Software bedeutet
audience: reseller, partner
category: reselling
order: 10
summary: Warum Sie für Angebote auf Basis von Exponential Geld verlangen können, obwohl die Software frei ist, was die GNU GPL v2 oder später einem Wiederverkäufer erlaubt, was sie verlangt, wenn Sie Kopien weitergeben, und wo die Lizenz der Dokumentation hineinpasst. Keine Rechtsberatung.
keywords: GPL, GPL v2 oder später, freie Software, Wiederverkauf, Geld verlangen, weitergeben, Quellcode, Urheberrechtshinweise, Lizenz, GFDL, Rechtsberatung, keine Lizenzgebühr, GPL v2 or later, free software, reselling, charge, distribute, source code, copyright notices, license, legal advice, no license fee
type: help
---
# Was Wiederverkauf bei freier Software bedeutet

Diese Seite ist für Inhaber und Vertriebsmitarbeiter von Wiederverkäufern, die sich selbst und ihren Kunden erklären
müssen, was eigentlich verkauft wird, wenn die Software selbst frei ist. Sie fasst die Lizenzen zusammen, soweit sie
das Geschäft eines Wiederverkäufers betreffen. Sie ist **keine Rechtsberatung**: Es entscheiden die Lizenztexte, und
Ihr eigener Berater legt sie für Ihr Angebot aus.

## Inhalt

1. [Die Kurzfassung](#die-kurzfassung)
2. [Frei heißt Freiheit, nicht Preis](#frei-heißt-freiheit-nicht-preis)
3. [Was Sie tun dürfen](#was-sie-tun-dürfen)
4. [Was Sie tun müssen, wenn Sie weitergeben](#was-sie-tun-müssen-wenn-sie-weitergeben)
5. [Eine Website für einen Kunden zu betreiben ist keine Weitergabe](#eine-website-für-einen-kunden-zu-betreiben-ist-keine-weitergabe)
6. [Die Lizenz der Dokumentation](#die-lizenz-der-dokumentation)
7. [Formulierungen für Kunden](#formulierungen-für-kunden)

## Die Kurzfassung

| Frage | Antwort | Quelle |
|---|---|---|
| Unter welcher Lizenz steht Exponential? | GNU General Public License, Version 2 oder jede spätere Version | `composer.json` (`"license": "GPL-2.0-or-later"`), `COPYRIGHT.md`, `LICENSE` |
| Kostet die Nutzung eine Lizenzgebühr? | Nein: Die Nutzung von Exponential unter den Bedingungen der GNU GPL ist kostenlos | `README.md`, Abschnitt „License“ |
| Wird die alte kommerzielle Lizenz noch verkauft? | Nein: „The Professional License is no longer available“ | `README.md` |
| Darf ein Wiederverkäufer Geld verlangen? | Ja, für Dienstleistungen, Produkte, Ihre eigene Arbeit und für die Weitergabe von Kopien | `LICENSE`, Präambel und Abschnitt 1 |
| Muss ein Wiederverkäufer etwas zurückgeben? | Wenn Sie Kopien weitergeben: die Lizenz, die Hinweise und den Quellcode | `LICENSE`, Abschnitte 1 bis 3 |
| Unter welcher Lizenz steht diese Hilfe? | GNU Free Documentation License 1.3 oder später | [Die Lizenz dieser Dokumentation](../../../README-LIZENZ.md) |

## Frei heißt Freiheit, nicht Preis

Die Präambel der GPL sagt sinngemäß: Wenn wir von freier Software sprechen, meinen wir Freiheit, nicht den Preis.
Die Lizenz erlaubt ausdrücklich, Geld zu verlangen: Sie dürfen für die physische Übergabe einer Kopie eine Gebühr
verlangen und nach Ihrer Wahl gegen eine Gebühr eine Gewährleistung anbieten (Abschnitt 1).

Für einen Wiederverkäufer bedeutet das drei Dinge:

1. **Sie verkaufen nie das Recht, Exponential zu nutzen.** Dieses Recht hat jeder bereits. Eine Rechnungsposition
   „Exponential-Lizenz“ wäre falsch ([Lizenzen für Käufer erklärt](../../geschaeft/lizenzen-fuer-kaeufer.md)).
2. **Sie verkaufen, was Arbeit kostet**: Server, Einrichtung, Support, Updates, Design, Erweiterungen, Schulung,
   Gewährleistung und Ihre Erreichbarkeit.
3. **Ihr Kunde behält die Freiheiten.** Er darf seine Website jederzeit zu einem anderen Anbieter mitnehmen. Bauen
   Sie das in Ihr Angebot ein, statt dagegen anzukämpfen ([Verlängerungen und Abwanderung](../kunden/verlaengerungen-und-abwanderung.md)).

## Was Sie tun dürfen

- Exponential für beliebig viele Kunden, zu jedem Zweck, installieren, betreiben und ändern, ohne jemanden zu fragen.
- Geld für Hosting, Support, Entwicklung, Schulung, Beratung und Design verlangen.
- Geld für die Weitergabe von Kopien verlangen, zum Beispiel für ein vorinstalliertes Image oder ein paketiertes
  Produkt.
- Gegen eine Gebühr eine Gewährleistung anbieten (GPL Abschnitt 1), zu Bedingungen, die Sie selbst festlegen.
- Ihre eigenen Erweiterungen, Themes und Dienstleistungen neben Exponential verkaufen, im Rahmen der Lizenz jedes
  einzelnen Teils ([Was Sie verkaufen können und was nicht](was-sie-verkaufen-koennen-und-was-nicht.md)).
- Diese Dokumentation unter der GNU FDL für Ihre Kunden kopieren, anpassen und mit eigener Marke versehen
  ([Dokumentation, die Sie Ihren Kunden geben können](../kunden/dokumentation-fuer-ihre-kunden.md)).

## Was Sie tun müssen, wenn Sie weitergeben

Wenn Sie Exponential oder darauf basierende Software an jemand anderen geben (ein Download, ein Image, eine
Appliance, Dateien, die Eigentum des Kunden werden), gelten die Bedingungen der GPL:

- [ ] den Lizenztext weitergeben und jeden Urheberrechtshinweis erhalten (`LICENSE`, `LICENSE.md`, `COPYRIGHT.md`);
- [ ] den Quellcode einschließlich Ihrer Änderungen bereitstellen; Exponential ist PHP, die Dateien sind also der
      Quellcode, aber geänderte Dateien müssen mit einem Hinweis und dem Datum der Änderung versehen werden (GPL
      Abschnitt 2a);
- [ ] keine Einschränkung hinzufügen, die dem Empfänger seine Rechte aus der GPL nimmt.

Die vollständige Liste mit den Lizenzen der Erweiterungen und Komponenten steht in der
[Checkliste der Pflichten bei der Weitergabe](../verantwortlichkeiten/checkliste-der-pflichten-bei-der-weitergabe.md).

## Eine Website für einen Kunden zu betreiben ist keine Weitergabe

Abschnitt 0 der GPL sagt sinngemäß: Das Ausführen des Programms ist nicht eingeschränkt. Wenn Sie eine Website auf Ihren eigenen Servern hosten und der Kunde sie nur über einen Browser
nutzt, betreiben Sie die Software; Sie geben sie nicht weiter. Die GPL v2 hat keine Klausel über Software, die über
ein Netzwerk genutzt wird, und Exponential selbst steht nicht unter der AGPL
([Lizenzen für Käufer erklärt](../../geschaeft/lizenzen-fuer-kaeufer.md#eine-website-zu-betreiben-ist-keine-weitergabe)).

| Situation | Weitergabe? |
|---|---|
| Sie hosten die Website, der Kunde bearbeitet sie im Browser | Nein |
| Sie übergeben dem Kunden am Ende des Projekts die Dateien | Ja: Der Kunde erhält die Rechte aus der GPL und den Quellcode |
| Sie verkaufen ein herunterladbares Image oder Produkt | Ja |
| Sie geben dem Entwickler eines Kunden eine Kopie Ihrer geänderten Version | Ja |

Ob eine bestimmte Vereinbarung als Weitergabe gilt, ist eine Frage für Ihren Rechtsberater.

## Die Lizenz der Dokumentation

Die Hilfe steht unter der GNU FDL 1.3 oder später ([Freie Dokumentation](../../freie-dokumentation.md)). Sie dürfen
sie kopieren, verkaufen, übersetzen und anpassen, solange Kopien die Hinweise behalten, die Lizenz mitführen und
geänderte Fassungen unter derselben Lizenz bleiben und angeben, was geändert wurde.

## Formulierungen für Kunden

```text
Exponential is free software under the GNU General Public License, version 2 or later.
You pay us for <hosting / support / development / training>, not for a license.
You may take your site to another provider at any time; we will hand over everything you need.
```

## Verwandte Seiten

- [Was Sie verkaufen können und was nicht](was-sie-verkaufen-koennen-und-was-nicht.md)
- [Lizenzierung für Partner: GPL-Code, GFDL-Dokumentation](../../partner/lizenzierung-fuer-partner.md)
- [Lizenzierung für Hoster](../../hosting/geschaeft/lizenzierung-fuer-hoster.md)
- [Fragen zur Lizenzierung](../../projekt/lizenz-faq.md)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
