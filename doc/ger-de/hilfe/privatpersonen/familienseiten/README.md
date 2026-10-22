---
title: Familienseiten
audience: individual, website-creator, customer
category: personal-sites
order: 1700
summary: Der Bereich für Menschen, die mit Exponential eine private Website für ihre Familie betreiben - Fotos, Neuigkeiten, Rezepte und Familiengeschichte hinter einer Anmeldung - mit Planung, privaten Bereichen über Sektionen und Rollen, dem Einladen von Verwandten, Geburtsdaten und Privatsphäre sowie den ehrlichen Grenzen.
keywords: Familienseite, private Website, Familienfotos, Familiennachrichten, Familiengeschichte, Genealogie, privater Bereich, Anmeldung, Sektionen, Rollen, einladen, Verwandte, Geburtstag, Privatsphäre, Kinder, family site, private site, invite, relatives, privacy, children
type: help
---
# Familienseiten

Dieser Bereich ist für Privatpersonen, die einen Ort im Web für ihre Familie möchten: Neuigkeiten, Fotos, Rezepte,
eine Familiengeschichte, die wichtigen Termine. Anders als eine persönliche Website ist eine Familienseite
überwiegend **privat**: Sie ist für Verwandte, die sich anmelden, nicht für die Öffentlichkeit und nicht für
Suchmaschinen.

## Inhalt

1. [Was eine Familienseite meist enthält](#was-eine-familienseite-meist-enthält)
2. [Die Seiten dieses Bereichs](#die-seiten-dieses-bereichs)
3. [Was sie in Exponential trägt](#was-sie-in-exponential-trägt)
4. [Was es nicht gibt](#was-es-nicht-gibt)

## Was eine Familienseite meist enthält

| Teil | Typische Klasse | Wer sie sieht |
|---|---|---|
| Eine öffentliche Startseite oder gar keine | *Frontpage* oder *Folder* | Alle oder niemand |
| Familiennachrichten | *Blog* mit *Blog post* | Familie |
| Fotoalben | *Gallery* mit *Image* | Familie |
| Rezepte, Geschichten, Familiengeschichte | *Article* in Ordnern | Familie |
| Familienfeiern (Treffen, Feste) | *Event* in einem *Event calendar* | Familie |
| Eine Liste der Verwandten mit Geburtsdaten | Eine Klasse mit einem Feld `ezbirthday` oder Benutzerprofile | Familie oder nur Sie |

## Die Seiten dieses Bereichs

| Seite | Lesen Sie sie, wenn |
|---|---|
| [Eine Familienseite planen](eine-familienseite-planen.md) | Bevor Sie irgendetwas installieren |
| [Private Bereiche mit Rollen und Sektionen](private-bereiche-mit-rollen-und-sektionen.md) | Sie die Website oder einen Teil davon privat machen |
| [Familienmitglieder einladen](familienmitglieder-einladen.md) | Sie Verwandten ihre Konten geben |
| [Geburtstage und Familientermine](geburtstage-und-familientermine.md) | Sie Geburtsdaten und Jahrestage festhalten möchten |
| [Privatsphäre für Ihre Familie](privatsphaere-fuer-ihre-familie.md) | Bevor das erste Foto eines Kindes online geht |

Die [Checkliste zur Privatsphäre der Familie](../checklisten/checkliste-zur-privatsphaere-der-familie.md) fasst alles für den Starttag
zusammen.

## Was sie in Exponential trägt

- **Sektionen und Rollen** entscheiden, wer was lesen darf. Das Standard-Seitenpaket legt bereits eine Sektion
  namens *Restricted* an, die anonyme Besucher nicht lesen können (`sevenx-multi-site-installer.php`).
- **Ein ganzer Siteaccess kann eine Anmeldung verlangen**, mit `[SiteAccessSettings] RequireUserLogin=true`
  ([Mitgliederregistrierung und -bereiche](../../ersteller/website-ersteller/mitgliederregistrierung-und-mitgliederbereiche.md#ein-bereich-nur-für-mitglieder)).
- **Konten** legen Sie selbst an, oder Verwandte über die Registrierung, wenn Sie es erlauben.
- **Die zweistufige Anmeldung** schützt Konten ([Zwei-Faktor-Anmeldung](../im-betrieb/zwei-faktor-anmeldung.md)).
- **Galerien, Blogs, Veranstaltungen** stammen aus denselben Klassen wie auf öffentlichen Websites.

## Was es nicht gibt

- **Keine Geburtstagserinnerungen**: Die Erweiterung `birthday` speichert ein Geburtsdatum und sonst nichts
  ([Geburtstage und Familientermine](geburtstage-und-familientermine.md)).
- **Kein Modul für Stammbäume oder Genealogie.** Eine Familiengeschichte besteht aus Artikeln und Beziehungen, die Sie
  selbst aufbauen.
- **Standardmäßig keine privaten Nachrichten**; `ezpm` wird in `composer.json` nur vorgeschlagen.
- **Keine Synchronisation eines gemeinsamen Familienkalenders** mit Smartphones; Termine sind Seiten auf der Website.

## Verwandte Seiten

- [Privatpersonen: hier beginnen](../privatpersonen-hier-beginnen.md)
- [Vereinsseiten](../vereinsseiten/README.md)
- [Mitgliederregistrierung und Bereiche nur für Mitglieder](../../ersteller/website-ersteller/mitgliederregistrierung-und-mitgliederbereiche.md)
- [Nicht-technische Menschen: hier beginnen](../../../../eng-us/help/non-technical-people/non-technical-people-start-here.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
