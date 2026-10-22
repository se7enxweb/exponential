---
title: "Strukturierte Daten: was es gibt"
audience: local-business, businesses, customer, website-creator
category: local-business
order: 25
summary: Welche strukturierten Daten nach schema.org (JSON-LD) Exponential und seine mitgelieferten Themes heute ausgeben - Brotkrumenpfade, Artikel, Rezepte, die Inhaltslizenz - und was ein lokales Unternehmen für einen LocalBusiness-Block mit Adresse und Öffnungszeiten ergänzen müsste, als Template-Arbeit eines Erstellers.
keywords: strukturierte Daten, schema.org, JSON-LD, LocalBusiness, Öffnungszeiten, OpeningHoursSpecification, Brotkrumenpfad, Rich Results, xrowmetadata, sevenx_themes_media, lokales Unternehmen, structured data, opening hours specification, breadcrumbs, rich results, local business
type: help
---
# Strukturierte Daten: was es gibt

Diese Seite ist für Inhaber und Ersteller von Websites lokaler Unternehmen, die gehört haben, dass Suchmaschinen
**strukturierte Daten** lesen: einen Block maschinenlesbarer Fakten (im Format JSON-LD, mit dem Vokabular von
schema.org) im Kopf der Seite. Sie sagt klar, was mitgeliefert wird und was nicht.

## Inhalt

1. [Wozu strukturierte Daten dienen](#wozu-strukturierte-daten-dienen)
2. [Was heute mitgeliefert wird](#was-heute-mitgeliefert-wird)
3. [Was nicht mitgeliefert wird](#was-nicht-mitgeliefert-wird)
4. [Einen LocalBusiness-Block ergänzen](#einen-localbusiness-block-ergänzen)
5. [Checkliste](#checkliste)

## Wozu strukturierte Daten dienen

Strukturierte Daten wiederholen Fakten, die schon auf der Seite stehen (Name, Adresse, Öffnungszeiten, Preis), in einer
Form, die Maschinen ohne Raten lesen. Suchmaschinen können sie für reichhaltigere Ergebnisse nutzen. Sie ersetzen weder
eine korrekte Seite noch einen korrekten Unternehmenseintrag auf Kartenplattformen
([Unternehmenseinträge außerhalb von Exponential](unternehmenseintraege-ausserhalb-von-exponential.md)).

## Was heute mitgeliefert wird

Gesucht in `extension/` am 20. Oktober 2026:

| Strukturierte Daten | Wo | Gilt für |
|---|---|---|
| `BreadcrumbList` auf jeder Seite | `extension/sevenx_themes_media/design/media/templates/pagelayout/head/meta.tpl` | Websites, die das Media-Theme verwenden |
| `Article` mit Herausgeber `Organization` | die Vollansichten des Media-Themes für Artikel, Nachrichten, Blogbeiträge | diese Inhaltstypen |
| `Recipe` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` (Klasse `ng_recipe`) | Rezeptseiten des Media-Themes |
| eine Stellenanzeige mit einstellender `Organization` | `.../content/views/full/ng_job_position.tpl` | Stellenseiten des Media-Themes |
| schema.org-`license` der Inhalte | `xrowmetadata` (`xrowmetadata/license_head.tpl`, ab Version 1.4.9) | Websites, die eine Inhaltslizenz festlegen |

Ob Ihre Website überhaupt strukturierte Daten ausgibt, hängt also vom **Design** ab, das sie verwendet. Beim
öffentlichen Standarddesign (`sevenx_themes_simple`) wurde keine Ausgabe von JSON-LD gefunden.

## Was nicht mitgeliefert wird

- Kein Block `LocalBusiness` (oder `Restaurant`, `Store`, `HairSalon` und so weiter).
- Keine `OpeningHoursSpecification`; es gibt auch keinen Datentyp für Öffnungszeiten, aus dem sie gelesen werden
  könnte ([Öffnungszeiten ohne besonderen Datentyp](../karten-und-oeffnungszeiten/oeffnungszeiten-ohne-besonderen-datentyp.md)).
- Keine strukturierten Daten vom Typ `Event` für die Klasse `event` von `ezwebin`.
- Keine strukturierten Daten vom Typ `Product` oder `Offer` für Shop-Produkte.
- Kein `AggregateRating` aus `ezstarrating`.

Jedes davon kann ein Ersteller in einem Template ergänzen. Keines braucht eine neue Erweiterung.

## Einen LocalBusiness-Block ergänzen

Das ist Template-Arbeit für Ihren Ersteller, meist ein bis zwei Stunden, sobald das Inhaltsmodell klar ist. Die Idee:

1. Halten Sie Name, Adresse, Telefon und Öffnungszeiten in einem Inhaltsobjekt
   ([Ihre Seiten planen](../einstieg/ihre-seiten-planen.md#schreiben-sie-jede-tatsache-nur-einmal)); mit strukturierten Feldern
   für die Öffnungszeiten, wie in [Öffnungszeiten ohne besonderen Datentyp](../karten-und-oeffnungszeiten/oeffnungszeiten-ohne-besonderen-datentyp.md#möglichkeit-2-eine-kleine-klasse-für-öffnungszeiten)
   beschrieben.
2. Geben Sie im Pagelayout, oder nur auf der Startseite und der Kontaktseite, einen JSON-LD-Block aus, der aus diesen
   Feldern gebaut wird. Die `meta.tpl` des Media-Themes zeigt, wie das Projekt JSON-LD mit dem Operator `json_ld`
   sicher ausgibt.
3. Verwenden Sie die Koordinaten eines Feldes `ezgmaplocation` für `geo`, falls die Website eines hat
   ([Ihren Standort auf einer Karte zeigen](../karten-und-oeffnungszeiten/ihren-standort-auf-einer-karte-zeigen.md)).
4. Testen Sie die Seite mit den eigenen Testwerkzeugen der Suchmaschinen für strukturierte Daten (außerhalb von
   Exponential).
5. Denken Sie an die Feiertagszeiten: Wenn die strukturierten Daten „geöffnet“ sagen und die Tür verschlossen ist,
   ärgern sich die Besucher. Nehmen Sie entweder Sondertermine auf, oder beschränken Sie den Block auf die regulären
   Öffnungszeiten und kündigen Sie Ausnahmen auf der Seite an.

## Checkliste

- [ ] Sie wissen, welches Design Ihre Website verwendet und ob es JSON-LD ausgibt.
- [ ] Name, Adresse, Telefon und Öffnungszeiten liegen in einem Objekt.
- [ ] Wenn ein LocalBusiness-Block gewünscht ist, steht er im Angebot des Erstellers.
- [ ] Der Block ist getestet und stimmt mit der sichtbaren Seite überein.
- [ ] Feiertagszeiten sind berücksichtigt.

## Verwandte Seiten

- [Grundlagen der lokalen Suchmaschinenoptimierung](grundlagen-der-lokalen-suchmaschinenoptimierung.md)
- [Seitenpakete und die mitgelieferten Themes](../../ersteller/website-ersteller/seitenpakete-und-mitgelieferte-themes.md)
- [Ein Design oder Theme wählen](../../ersteller/website-ersteller/ein-design-oder-theme-waehlen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
