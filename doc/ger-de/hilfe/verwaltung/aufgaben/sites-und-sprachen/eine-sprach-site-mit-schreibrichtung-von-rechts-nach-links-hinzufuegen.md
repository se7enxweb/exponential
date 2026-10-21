---
title: Eine Sprach-Site mit Schreibrichtung von rechts nach links hinzufügen
audience: government, admin, website-creator
category: public-sector
order: 110
summary: Aufgabe für Administratoren und Site-Ersteller - eine Sprach-Site für Arabisch, Hebräisch, Persisch oder Urdu hinzufügen, mit der im Pagelayout ausgegebenen Schreibrichtung und logischem CSS, und sie testen.
keywords: von rechts nach links, Schreibrichtung, Arabisch, Hebräisch, Persisch, Urdu, Sprach-Site, logische Eigenschaften, right to left, RTL, Arabic, Hebrew, Persian, dir rtl, text_direction, language site, logical properties
type: help
---
# Eine Sprach-Site mit Schreibrichtung von rechts nach links hinzufügen

Diese Aufgabe richtet sich an Administratoren und Site-Ersteller. Hintergrund:
[Sprachen mit Schreibrichtung von rechts nach links auf öffentlichen Sites](../../sprachen/rechts-nach-links-sprachen-auf-oeffentlichen-websites.md).

## Bevor Sie beginnen

- Die Locale hat `TextDirection=rtl` (zum Beispiel `ara-SA`, `heb-IL`, `fas-IR`, `urd-PK`).
- Die Schriften Ihres Designs decken die Schrift ab.

## Schritte

1. Fügen Sie die Inhaltssprache und einen Siteaccess mit `Locale=<die Locale>` hinzu (siehe „Eine neue Inhaltssprache
   hinzufügen“ und „Einer Sprache eine eigene Adresse geben“).
2. Geben Sie im Pagelayout Ihres Designs die Schreibrichtung aus:

   ```text
   <html lang="{$site.http_equiv.Content-language|wash}"{if eq( first_set( $site.text_direction, 'ltr' ), 'rtl' )} dir="rtl"{/if}>
   ```

3. Ersetzen Sie links und rechts in den Stylesheets durch logische Eigenschaften (`margin-inline-start`,
   `text-align: start` ...); spiegeln Sie Icons, die entlang der Zeile zeigen.
4. Leeren Sie den Template- und den INI-Cache.

## Prüfen

- `<html dir="rtl">` auf der neuen Site; Spalten und Menüs sind gespiegelt; Formulare richten sich an der Startseite
  der Zeile aus.
- Zahlen und lateinische Wörter im Text lesen sich richtig.
- Die Sites mit Schreibrichtung von links nach rechts sehen aus wie zuvor.

## Probleme

| Problem | Lösung |
|---|---|
| Kein `dir`-Attribut | das Pagelayout gibt es nicht aus, oder die `Locale` des Siteaccess ist eine Locale mit Schreibrichtung von links nach rechts |
| Teile des Layouts bleiben links nach rechts | physisches CSS mit `left`/`right` ist geblieben |

## Verwandte Seiten

- [Sites mit Schreibrichtung von rechts nach links](../../../../../eng-us/help/creators/website-creators/right-to-left-sites.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
