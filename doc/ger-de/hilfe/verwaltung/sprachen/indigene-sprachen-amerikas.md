---
title: Indigene Sprachen Amerikas
audience: government, project-manager, admin, editor
category: public-sector
order: 135
summary: Wie man mit Exponential in einer indigenen Sprache Amerikas veröffentlicht - die 22 mitgelieferten Locales (Sprachen Alaskas, Navajo, Cherokee, Lakota, Ojibwe, Cree, Inuktitut, Mohawk, Nahuatl, Maya, Guaraní, Aymara und weitere), die Sprache hinzufügen, Inhalte übersetzen, URL-Aliase, die Oberflächensprache und Schriften für die Silbenschriften und Cherokee.
keywords: indigene Sprachen, Sprachen der amerikanischen Ureinwohner, Sprachen Alaskas, First Nations, Inuit, Aleutisch, Yupik, Inupiaq, Alutiiq, Tlingit, Haida, Gwich'in, Tsimshian, Navajo, Cherokee, Lakota, Ojibwe, Cree, Inuktitut, Mohawk, Nahuatl, Yukatekisches Maya, K'iche', Guaraní, Aymara, Mapudungun, Silbenschrift, Locale, indigenous languages, Native American languages, Alaska Native languages, syllabics
type: help
---
# Indigene Sprachen Amerikas

Diese Seite richtet sich an Stammesregierungen, Schulbezirke, öffentliche Stellen und Organisationen, die in einer
indigenen Sprache Amerikas veröffentlichen. Exponential 6.0.15 liefert für 22 von ihnen eine Locale mit, sodass jede
ohne eigene Datei eine Inhaltssprache sein kann. Die allgemeinen Hinweise aus
[Minderheiten- und Regionalsprachen](minderheiten-und-regionalsprachen.md) gelten ebenso.

## Inhalt

1. [Die mitgelieferten Sprachen](#die-mitgelieferten-sprachen)
2. [Die Sprache hinzufügen](#die-sprache-hinzufügen)
3. [Inhalte übersetzen](#inhalte-übersetzen)
4. [Adressen](#adressen)
5. [Die Oberflächensprache](#die-oberflächensprache)
6. [Schriften für die Silbenschriften und Cherokee](#schriften-für-die-silbenschriften-und-cherokee)
7. [Eine Sprache, die nicht in der Liste steht](#eine-sprache-die-nicht-in-der-liste-steht)

## Die mitgelieferten Sprachen

| Region | Sprachen (Locale-Code) |
|---|---|
| Alaska | Zentral-Yupik (`esu-US`), Inupiaq (`ipk-US`), Aleutisch (`ale-US`), Alutiiq (`ems-US`), Tlingit (`tli-US`), Haida (`hai-US`), Gwich'in (`gwi-US`), Tsimshian (`tsi-US`) |
| USA | Navajo (`nav-US`), Cherokee (`chr-US`), Lakota (`lkt-US`), Ojibwe (`oji-US`) |
| Kanada | Cree (`cre-CA`), Inuktitut (`iku-CA`), Ojibwe (`oji-CA`), Mohawk (`moh-CA`) |
| Mexiko und Mittelamerika | Nahuatl (`nah-MX`), Yukatekisches Maya (`yua-MX`), K'iche' (`quc-GT`) |
| Südamerika | Guaraní (`grn-PY`), Aymara (`aym-BO`), Mapudungun (`arn-CL`), Quechua (`que-PE`) |

Grönländisch (`kal-GL`) und, aus dem Pazifik, Hawaiisch (`haw-US`) werden ebenfalls mitgeliefert. Jede Locale gibt
der Sprache ihren eigenen Namen (zum Beispiel *Diné Bizaad*, *ᏣᎳᎩ*, *Unangam Tunuu*) sowie Datum, Zahlen und Währung
ihres Landes. Wo eine Sprache in den Quellen der Locale keine festen Monats- und Tagesnamen hat, verwendet die Datei
die englischen oder spanischen Namen und sagt das in ihrem Kopf; welche, steht in
[Sprachen in Exponential](../../../sprachen.md#indigene-sprachen-amerikas).

## Die Sprache hinzufügen

1. Öffnen Sie **Setup > Sprachen** (`/content/translations`) und klicken Sie auf **Sprache hinzufügen**.
2. Tippen Sie einen Teil des Namens, des eigenen Namens oder des Codes in **Sprache suchen**: `navajo`, `diné` und
   `nav` finden alle *Navajo · Diné Bizaad · nav-US*.
3. Klicken Sie auf **Sprache hinzufügen**. Die Schritte sind dieselben wie für jede Sprache; siehe
   [Eine Inhaltssprache hinzufügen](eine-inhaltssprache-hinzufuegen.md).

## Inhalte übersetzen

Übersetzen Sie einen Eintrag in die neue Sprache wie jeden anderen
([Einen Eintrag Schritt für Schritt übersetzen](einen-eintrag-schritt-fuer-schritt-uebersetzen.md)). Die meisten
Websites veröffentlichen nur einen Teil ihrer Inhalte in der indigenen Sprache. Ein Siteaccess, der die Sprache
zuerst und Englisch (oder Spanisch oder Französisch) als zweite nennt, zeigt die übersetzten Seiten in der indigenen
Sprache und den Rest in der anderen:

```ini
# settings/siteaccess/site_nv/site.ini.append.php
[RegionalSettings]
Locale=nav-US
ContentObjectLocale=nav-US
SiteLanguageList[]
SiteLanguageList[]=nav-US
SiteLanguageList[]=eng-US
TextTranslation=enabled
```

Ojibwe hat zwei Locales: `oji-US` für eine Gemeinschaft in den USA und `oji-CA` in Kanada. Sie unterscheiden sich
nur in Land, Währung und Datumsreihenfolge.

## Adressen

Der URL-Alias eines Eintrags entsteht aus seinem Namen. Mit der mitgelieferten Umwandlung `urlalias` werden
lateinische Buchstaben mit diakritischen Zeichen zu einfachen ASCII-Buchstaben (ñ wird n); ein Name, der ganz in
Cherokee oder in Silbenschrift geschrieben ist, behält seine Schrift, weil ein Titel, der jeden Buchstaben verlöre,
auf die IRI-Umwandlung zurückfällt
([URLs und Aliase, Abschnitt 10](../../../anleitungen/urls-und-aliase.md#10-titel-in-schriften-ohne-transliterationstabelle)).
Um in allen Aliasen eines Siteaccess jeden Buchstaben zu behalten, setzen Sie in dessen `site.ini`
`[URLTranslator] TransformationGroup=urlalias_iri`. Entscheiden Sie das vor dem Veröffentlichen; bestehende Aliase
ändern sich erst, wenn Einträge erneut veröffentlicht werden.

## Die Oberflächensprache

Inhalte, Seitentitel und der Sprachumschalter verwenden die Sprache; die Administrationsoberfläche und die festen
Texte des öffentlichen Designs erscheinen in der Oberflächensprache des Siteaccess, meist Englisch. Vollständige
Übersetzungen der Administrationsoberfläche gibt es für Quechua, Grönländisch und Hawaiisch, Guaraní ist in Arbeit;
für die anderen Sprachen bleibt die Administrationsoberfläche auf Englisch oder, wenn Ihre Mitarbeitenden es
vorziehen, auf Spanisch (`esl-ES`) oder Französisch (`fre-CA`)
([Sprachen der Administrationsoberfläche](sprachen-der-administrationsoberflaeche.md)). Die wenigen festen Texte
eines öffentlichen Designs (Menübeschriftungen, „Suche“, die Fußzeile) lassen sich in der eigenen `.ts`-Datei des
Designs übersetzen.

## Schriften für die Silbenschriften und Cherokee

Cherokee wird in seiner eigenen Silbenschrift geschrieben (U+13A0 bis U+13FF und U+AB70 bis U+ABBF), Inuktitut und,
wo eine Gemeinschaft sie bevorzugt, Cree und Ojibwe in der kanadischen Silbenschrift (U+1400 bis U+167F). Windows,
macOS, iOS und Android liefern Schriften für beide mit. Ein öffentliches Design, das eine eigene Webschrift setzt,
muss eine Ersatzschrift nennen, die die Zeichen hat, zum Beispiel:

```css
:root {
    --site-font: "Your Brand Font", "Noto Sans Cherokee", "Noto Sans Canadian Aboriginal", system-ui, sans-serif;
}
```

Sprachen in lateinischer Schrift verwenden Sonderbuchstaben wie ł, ŋ, x̱, ẽ und den Knacklaut ʼ (U+02BC); prüfen Sie,
ob Ihre Schrift sie enthält. Zeichen oberhalb von U+FFFF brauchen unter MySQL und MariaDB `utf8mb4`; keine dieser
Sprachen braucht sie, ein Emoji in einem Titel aber schon.

## Eine Sprache, die nicht in der Liste steht

In Amerika werden viel mehr indigene Sprachen gesprochen, als mit einer Locale mitgeliefert werden. Eine fehlende ist
eine einzige Datei in `share/locale`
([Eigene Locales für nicht gelistete Sprachen](eigene-locales-fuer-nicht-gelistete-sprachen.md)); kopieren Sie die
Datei einer Sprache desselben Landes, ändern Sie die Namen und den Code und fügen Sie sie wie jede andere hinzu.

## Verwandte Seiten

- [Minderheiten- und Regionalsprachen](minderheiten-und-regionalsprachen.md)
- [Sprachen in Exponential](../../../sprachen.md#indigene-sprachen-amerikas)
- [Eine Inhaltssprache hinzufügen](eine-inhaltssprache-hinzufuegen.md)
- [Eigene Locales für nicht gelistete Sprachen](eigene-locales-fuer-nicht-gelistete-sprachen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
