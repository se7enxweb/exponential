---
title: Eine neue Inhaltssprache hinzufügen
audience: government, admin
category: public-sector
order: 30
summary: Aufgabe für Administratoren - eine Inhaltssprache unter Setup > Sprachen hinzufügen, sie dann über einen Siteaccess sichtbar machen und Übersetzern Zugang geben.
keywords: Sprache hinzufügen, Inhaltssprache, Setup Sprachen, neue Sprache, Übersetzer, add language, content language, Setup Languages, SiteLanguageList, new language, translators
type: help
---
# Eine neue Inhaltssprache hinzufügen

Diese Aufgabe richtet sich an Administratoren. Hintergrund:
[Eine Inhaltssprache hinzufügen](../../sprachen/eine-inhaltssprache-hinzufuegen.md) und
[Inhaltssprachen](../../../../anleitungen/inhaltssprachen.md#4-eine-sprache-hinzufügen).

## Bevor Sie beginnen

- Die Richtlinie `content/translations` (Administratoren haben sie).
- Der Locale-Code, zum Beispiel `cym-GB` für Walisisch; prüfen Sie, dass es ihn gibt, in
  [Sprachen in Exponential](../../../../../eng-us/languages.md#3-every-locale).

## Schritte

1. **Setup > Sprachen**, drücken Sie **Sprache hinzufügen**.
2. Geben Sie einen Teil des Namens oder Codes in **Sprache suchen** ein, wählen Sie den Eintrag und drücken Sie
   **Sprache hinzufügen**.
3. Die Übersicht bestätigt, dass die Sprache hinzugefügt wurde; ihre Karte sagt, dass sie nicht verwendet wird und in
   keiner `SiteLanguageList` steht.
4. Machen Sie sie sichtbar: Fügen Sie die Locale der `SiteLanguageList[]` eines Siteaccess hinzu oder legen Sie einen
   Siteaccess für sie an (siehe „Einer Sprache eine eigene Adresse geben“).
5. Leeren Sie die Caches: `php bin/php/ezcache.php --clear-tag=ini,content`, und mit Velocity
   `./console exp:velocity cache clear`.
6. Geben Sie Übersetzern eine Rolle, die auf die Sprache eingeschränkt ist (siehe „Einen Übersetzer auf eine Sprache
   beschränken“).

## Prüfen

Die Karte der Sprache zeigt **Shown first by** oder das Etikett des Siteaccess; wenn Sie eine Seite in die Sprache
übersetzen, erscheint die Seite auf diesem Siteaccess.

## Probleme

| Problem | Lösung |
|---|---|
| *There is no locale ... in share/locale* | eine individuelle Sprache braucht zuerst eine Locale-Datei (siehe „Eine eigene Locale für eine Minderheitensprache hinzufügen“) |
| Der Editor bietet die Sprache nicht an | fügen Sie den Richtlinien des Redakteurs die Einschränkung **Sprache** hinzu |
| Das Formular lehnt ab: Grenze erreicht | alle 62 Plätze sind belegt; entfernen Sie eine ungenutzte Sprache |

## Verwandte Seiten

- [Ein Siteaccess pro Sprache](../../sprachen/ein-siteaccess-pro-sprache.md)
- [Die Grenze von 62 Sprachen](../../sprachen/die-grenze-von-62-sprachen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
