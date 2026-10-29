---
title: Rechts-nach-links-Sprachen auf öffentlichen Websites
audience: government, website-creator, admin
category: public-sector
order: 150
summary: Wie eine öffentliche Stelle mit Exponential auf Arabisch, Hebräisch, Persisch, Urdu oder in einer anderen Sprache mit Schreibrichtung von rechts nach links veröffentlicht - woher die Schreibrichtung kommt, was das öffentliche Design ergänzen muss (dir auf der Seite, logisches CSS), Inhalte mit gemischter Schreibrichtung und Tests.
keywords: von rechts nach links, RTL, Arabisch, Hebräisch, Persisch, Urdu, Jiddisch, dir, TextDirection, text_direction, logische Eigenschaften, Bidi, öffentliche Website, Design, right to left, Arabic, Hebrew, Persian, logical properties, bidi, public site, design
type: help
---
# Rechts-nach-links-Sprachen auf öffentlichen Websites

Diese Seite richtet sich an Websiteersteller und Administratoren öffentlicher Websites, die Gemeinschaften bedienen,
die von rechts nach links lesen. Die Administration von Exponential spiegelt sich bei Oberflächensprachen mit
Schreibrichtung von rechts nach links selbst; ein öffentliches Design tut, was seine Templates und Stylesheets sagen,
deshalb muss das Design einige Dinge ergänzen
([Websites mit Schreibrichtung von rechts nach links](../../ersteller/website-ersteller/websites-von-rechts-nach-links.md)).

## Inhalt

1. [Welche Sprachen](#welche-sprachen)
2. [Woher die Schreibrichtung kommt](#woher-die-schreibrichtung-kommt)
3. [Was das öffentliche Design tun muss](#was-das-öffentliche-design-tun-muss)
4. [Gemischte Inhalte](#gemischte-inhalte)
5. [Tests](#tests)

## Welche Sprachen

43 Locale-Dateien in `share/locale/` tragen `TextDirection=rtl`: Arabisch für viele Länder, Hebräisch, Jiddisch,
Persisch und Dari, Urdu, Paschtu, Westpanjabi, Sindhi, Kaschmiri, Uigurisch, Zentralkurdisch (Sorani), Syrisch, N'Ko
und Dhivehi ([Sprachen in Exponential, Abschnitt 9](../../../sprachen.md#9-sprachen-die-von-rechts-nach-links-geschrieben-werden)). Sprachen, die
in lateinischer Schrift geschrieben werden, wie das kurmandschische Kurdisch, laufen von links nach rechts.

## Woher die Schreibrichtung kommt

Die einzige Quelle ist die Locale-Datei. Der Kernel stellt sie als `$site.text_direction` (`rtl` oder `ltr`) für die
`Locale` des Siteaccess bereit, und `$code|locale().is_right_to_left` für jede Locale in einem Template.

## Was das öffentliche Design tun muss

1. Geben Sie die Schreibrichtung im Pagelayout an `<html>` aus:

   ```text
   <html lang="{$site.http_equiv.Content-language|wash}"{if eq( first_set( $site.text_direction, 'ltr' ), 'rtl' )} dir="rtl"{/if}>
   ```

2. Verwenden Sie logische CSS-Eigenschaften (`margin-inline-start`, `padding-inline-end`, `inset-inline-start`,
   `text-align: start`) statt links und rechts, damit ein Stylesheet beide Richtungen bedient.
3. Spiegeln Sie Symbole, die entlang der Zeile zeigen (Pfeile, „weiter“).
4. Prüfen Sie, ob die Schriften die Schrift der Sprache abdecken.

## Gemischte Inhalte

Eine Seite kann Inhalte in einer anderen Schreibrichtung als die Seite zeigen, etwa ein arabisches Zitat auf einer
englischen Seite. `design/standard/templates/parts/exp_content_direction.tpl` gibt für eine Inhalts-Locale von rechts
nach links ` lang="..." dir="rtl"` aus und für eine von links nach rechts nichts; binden Sie es an dem Element ein,
das den Inhalt umschließt. Die Online-Editoren stellen ihre Schreibrichtung für Texte von rechts nach links um, sodass
Redakteure in der richtigen Richtung schreiben
([Sprachen mit Schreibrichtung von rechts nach links in der Administration](../../../funktionen/6.0/rechts-nach-links.md#inhalte)).

## Tests

- [ ] `<html dir="rtl">` im Siteaccess mit Schreibrichtung von rechts nach links; Spalten und Menüs sind gespiegelt.
- [ ] Zahlen und lateinische Wörter im Text lesen sich richtig.
- [ ] Formulare: Beschriftungen, Eingabefelder und Fehlermeldungen richten sich an der Anfangsseite aus.
- [ ] Siteaccesses von links nach rechts sehen genau wie vorher aus.
- [ ] Bei schmaler Breite und bei 200 % Zoom geprüft.

## Verwandte Seiten

- [Websites mit Schreibrichtung von rechts nach links](../../ersteller/website-ersteller/websites-von-rechts-nach-links.md)
- [Rechts nach links in der Administration](rechts-nach-links-in-der-administration.md)
- [Stylesheets und Skripte](../../ersteller/website-ersteller/stylesheets-und-skripte.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
