---
title: Checkliste Mehrsprachigkeit
audience: businesses, project-manager, website-creator
category: business-solutions
order: 70
summary: Eine Checkliste, um einer Exponential-Website oder einem Shop eine Sprache oder einen Markt hinzuzufügen – Entscheidungen, Einrichtung, Übersetzung, Formate und Preise, Rechts-nach-links, Suchmaschinen, Start und die Routine danach.
keywords: Checkliste, mehrsprachig, neue Sprache, Start, Übersetzung, Sprachumschalter, Währungen, Rechts-nach-links, Unternehmen, checklist, multilingual, new language, launch, translation, SiteLanguageList, language switcher, hreflang, currencies, right to left, business
type: help
---
# Checkliste Mehrsprachigkeit

Diese Seite richtet sich an Projektleiter, Sprachverantwortliche und Administratoren, die eine Sprache oder einen Markt
hinzufügen. Übernehmen Sie sie in Ihr Projektwerkzeug und haken Sie sie ab; jeder Punkt verlinkt auf die Seite, die
ihn erklärt. Sie setzt voraus, dass die Entscheidungen aus [Mehrsprachigkeit planen](mehrsprachigkeit-planen.md)
getroffen sind.

## Inhalt

1. [Entscheidungen](#entscheidungen)
2. [Einrichtung](#einrichtung)
3. [Übersetzung](#übersetzung)
4. [Formate, Preise und Rechtstexte](#formate-preise-und-rechtstexte)
5. [Rechts-nach-links](#rechts-nach-links)
6. [Suchmaschinen und Links](#suchmaschinen-und-links)
7. [Start](#start)
8. [Nach dem Start](#nach-dem-start)

## Entscheidungen

- [ ] Der Markt, die Sprache und ihr Locale-Code sind schriftlich festgehalten
      ([Sprachen und Locales](sprachen-und-locales.md#eine-sprache-prüfen-bevor-sie-sich-festlegen)).
- [ ] Die Unterstützungsstufe der Sprache ist bekannt, und es ist entschieden, ob Redakteure in ihr oder auf Englisch
      arbeiten.
- [ ] Das Adressschema ist gewählt: Pfad, Host oder beides
      ([Adressen je Sprache](mehrsprachigkeit-planen.md#adressen-je-sprache)).
- [ ] Ausweichsprache und Umgang mit nicht übersetzten Inhalten sind je Siteaccess entschieden
      ([Was Besucher sehen, wenn eine Übersetzung fehlt](mehrsprachigkeit-planen.md#was-besucher-sehen-wenn-eine-übersetzung-fehlt)).
- [ ] Übersetzer, Freigebende und ein Sprachverantwortlicher sind benannt.
- [ ] Die Zahl der genutzten Inhaltssprachen bleibt deutlich unter der Grenze von 62
      ([Sprachen, Abschnitt 11](../../../sprachen.md#11-die-grenze-von-62-inhaltssprachen)).

## Einrichtung

- [ ] Die Inhaltssprache ist unter Setup > Sprachen hinzugefügt.
- [ ] Ein Siteaccess für die Sprache existiert, mit `Locale`, `SiteLanguageList[]` und Website-Namen
      ([Schritt für Schritt: eine zweite Sprache hinzufügen](../../ersteller/website-ersteller/mehrsprachige-websites.md#schritt-für-schritt-eine-zweite-sprache-hinzufügen)).
- [ ] Er ist registriert, wird erkannt und ist bei seinen Schwester-Siteaccesses aufgeführt.
- [ ] Gemeinsam genutzte Ordner, Benutzer und Medien sind als immer verfügbar markiert.
- [ ] Die Rollen der Übersetzer sind nach Sprache beschränkt (und wo nötig nach Teilbaum).
- [ ] Die Freigabe für die Sprache ist eingerichtet, falls gewünscht
      ([Freigabe je Sprache](uebersetzungsablauf.md#freigabe-je-sprache)).
- [ ] Die eigenen Oberflächentexte des Designs sind übersetzt.

## Übersetzung

- [ ] Die Startseite, die Menüordner und die wichtigsten Seiten werden zuerst übersetzt.
- [ ] Bei einem Shop sind Produktseiten sowie die Texte von Warenkorb und Kasse übersetzt.
- [ ] Maschinell vorbereitete Entwürfe werden, falls verwendet, von jemandem geprüft, der die Sprache liest
      ([Unterstützte Übersetzung mit Exponential Intelligence](unterstuetzte-uebersetzung-mit-exponential-intelligence.md#ein-prüfablauf)).
- [ ] Mailtexte (Bestellbestätigung, Registrierung, Newsletter) gibt es in der Sprache.

## Formate, Preise und Rechtstexte

- [ ] Datumsangaben, Zahlen und Beträge sehen auf Produktseiten, im Warenkorb und in der Bestätigungsmail richtig aus
      ([Währungen, Datumsangaben und Formate](waehrungen-datumsangaben-und-formate.md)).
- [ ] Die berechnete Währung, Preise je Währung oder Wechselkurse und die Rundung sind entschieden und getestet.
- [ ] Die Mehrwertsteuerregeln für den Markt sind eingerichtet und von einem Steuerberater bestätigt.
- [ ] AGB, Rückgaberegeln, Datenschutzhinweis und Impressum sind übersetzt und von einer qualifizierten Person geprüft.
- [ ] Lieferländer und Versandkosten für den Markt sind festgelegt ([Versand und Bearbeitung](../shops/versand-und-bearbeitung.md)).

## Rechts-nach-links

Nur wenn die Sprache von rechts nach links geschrieben wird ([Märkte mit Rechts-nach-links-Schrift](maerkte-mit-rechts-nach-links-schrift.md)):

- [ ] Der Seitenrahmen gibt für den Siteaccess `dir="rtl"` aus.
- [ ] Stylesheets verwenden logische Eigenschaften; Symbole entlang der Zeile sind gespiegelt.
- [ ] Gemischter Text und Formulare wurden von einem muttersprachlichen Leser geprüft, auch in Telefonbreite.
- [ ] Die Websites mit Links-nach-rechts-Schrift sind unverändert.

## Suchmaschinen und Links

- [ ] Der Sprachumschalter verlinkt jede Seite mit derselben Seite in den anderen Sprachen
      ([Der Sprachumschalter](../../ersteller/website-ersteller/sprachumschalter-und-sprach-urls.md#der-sprachumschalter)).
- [ ] `hreflang`-Alternativen werden im Pagelayout erzeugt, falls gewünscht
      ([Suchmaschinen informieren](../../ersteller/website-ersteller/sprachumschalter-und-sprach-urls.md#suchmaschinen-informieren)).
- [ ] Die Sprache hat eigene Einträge in der Sitemap ([Sitemaps](../../ersteller/website-ersteller/sitemaps.md)).
- [ ] Feeds der Sprache verweisen auf ihre eigene Adresse ([Feeds und Syndication](../mobil-und-headless/feeds-und-syndication.md)).
- [ ] Links zwischen Marken bleiben wo möglich in der Sprache des Besuchers
      ([Links zwischen Ihren Websites](../multisite/links-zwischen-ihren-websites.md#wie-ein-link-seine-website-findet)).

## Start

- [ ] Die Caches sind nach der letzten Änderung der Einstellungen geleert (INI und Inhalte; der Antwort-Cache von
      Velocity, wenn Velocity die Website ausliefert).
- [ ] Jede Adresse der Sprache antwortet über HTTPS.
- [ ] Eine Testbestellung, eine Registrierung oder ein Kontaktformular funktioniert in der Sprache.
- [ ] Die anderen Sprachen zeigen genau das, was sie vorher gezeigt haben.

## Nach dem Start

- [ ] Setup > Sprachen wird monatlich auf Sprachen mit wenigen Objekten oder ohne Siteaccess geprüft.
- [ ] Bei geänderten Quellseiten werden die Übersetzungen aktualisiert
      ([Übersetzungen aktuell halten](uebersetzungsablauf.md#übersetzungen-aktuell-halten)).
- [ ] Suchanfragen und Kontaktanfragen in der Sprache werden auf fehlende Inhalte geprüft.
- [ ] Eine Übersetzung entfernen nur Personen, die wissen, dass das nicht rückgängig gemacht werden kann.

## Verwandte Seiten

- [In mehreren Sprachen und Märkten verkaufen](README.md)
- [Checkliste für mehrere Marken](../multisite/checkliste-fuer-mehrere-marken.md)
- [Multisite- und mehrsprachige Projekte planen: Checklisten](../../fuehrung/multisite-und-mehrsprachigkeit-planen.md#checklisten)
- [Eine mehrsprachige Website aufbauen: Checkliste](../../ersteller/website-ersteller/mehrsprachige-websites.md#checkliste)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
