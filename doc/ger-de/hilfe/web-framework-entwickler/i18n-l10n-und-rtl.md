---
title: Übersetzungen, Locales und Rechts-nach-links
audience: web-framework-developer, developer
category: web-frameworks
order: 300
summary: Internationalisierung in Exponential für Entwickler, die Symfony Translation, die lang-Dateien von Laravel, gettext in Django und Rails I18n gewohnt sind - Texte der Oberfläche in Qt-Linguist-Katalogen (.ts), der i18n-Operator und ezpI18n::tr, Locales und die Operatoren l10n und datetime, übersetzte Inhalte mit einer Version pro Sprache, Sprach-Siteaccesses und Seiten mit Rechts-nach-links-Schrift.
keywords: i18n, l10n, Übersetzung, translation, Internationalisierung, internationalization, Lokalisierung, localization, ezpI18n, i18n-Operator, i18n operator, .ts, Qt Linguist, TranslationExtensions, Locale, locale, eZLocale, datetime, l10n-Operator, SiteLanguageList, Rechts-nach-links, right-to-left, RTL, dir, gettext, Symfony Translation, Web-Framework-Entwickler, web framework developer
type: help
---
# Übersetzungen, Locales und Rechts-nach-links

Diese Seite richtet sich an Entwickler, die eine Anwendung mit Nachrichtenkatalogen übersetzen und Datumsangaben und
Zahlen nach Locale formatieren. Exponential trennt zwei Dinge, die Frameworks oft vermischen: die **Oberfläche** (Texte
in Templates und PHP, mit Katalogen übersetzt) und die **Inhalte** (von Redakteuren übersetzt, wobei jede Objektversion
einen Satz von Werten pro Sprache enthält).

## Inhalt

1. [Texte der Oberfläche](#texte-der-oberfläche)
2. [Locales und Formatierung](#locales-und-formatierung)
3. [Inhalte in mehreren Sprachen](#inhalte-in-mehreren-sprachen)
4. [Rechts-nach-links](#rechts-nach-links)
5. [Im Vergleich mit anderen Frameworks](#im-vergleich-mit-anderen-frameworks)

## Texte der Oberfläche

| Wo | Wie |
|---|---|
| Templates | `{'Latest items'\|i18n( 'extension/expdemo' )}`; Platzhalter: `{'%count items'\|i18n( 'extension/expdemo',, hash( '%count', $count ) )}` |
| PHP | `ezpI18n::tr( 'extension/expdemo', 'Latest items' )` (`kernel/common/ezpi18n.php`) |
| Kataloge | `extension/<ext>/translations/<locale>/translation.ts`, Qt-Linguist-XML; die des Kernels in `share/translations/<locale>/translation.ts` |
| Registrierung | `site.ini [RegionalSettings] TranslationExtensions[]=<ext>` |

Das erste Argument ist der **Kontext**, der Zeichenketten so gruppiert wie eine Übersetzungsdomäne. Der Quelltext ist
der Schlüssel, wie bei gettext ([View-Ergebnisse: übersetzte Texte](../ersteller/anwendungsentwickler/ergebnisse-von-ansichten-und-das-pagelayout.md#übersetzte-texte),
[i18n-Operator](../designer/vorlagen/der-i18n-operator.md), [Übersetzungskataloge](../../standards/internationalisierung.md#übersetzungskataloge-qt-linguist-ts)).
Die Sprache der Oberfläche eines Siteaccess ist `site.ini [RegionalSettings] Locale`.

## Locales und Formatierung

Locales sind INI-Dateien in `share/locale/` (`eng-GB.ini`, `ger-DE.ini`, `ara-SA.ini` ...), die `eZLocale` liest. Sie
enthalten Formate für Datum, Uhrzeit, Zahlen und Währungen sowie die Schreibrichtung. Templates formatieren mit den
Locale-Operatoren `l10n`, `datetime` und `locale` ([Datumsangaben, Zahlen und l10n](../designer/vorlagen/datum-zahlen-und-l10n.md)):

```text
{$node.object.published|l10n( 'shortdate' )}
{$price|l10n( 'currency' )}
```

Locale-Codes haben die Form aus dreibuchstabiger Sprache plus Land (`eng-US`, `ger-DE`), nicht `en_US`; die
Standard-Seite erklärt, wie sie mit BCP-47-Tags zusammenhängen
([Sprach-Tags](../../standards/internationalisierung.md#sprach-tags-bcp-47-und-iso-639)).

## Inhalte in mehreren Sprachen

- Jedes Inhaltsobjekt kann in die Sprachen der Installation übersetzt werden; jede Version enthält einen Satz von
  Attributwerten pro Sprache. Redakteure übersetzen in der Administrationsoberfläche; es gibt keine Übersetzungstabelle,
  die Sie entwerfen müssten.
- Ein Siteaccess zeigt Inhalte in den Sprachen von `site.ini [RegionalSettings] SiteLanguageList[]`, in dieser
  Reihenfolge, und weicht jeweils auf die nächste aus ([Inhaltssprachen](../../anleitungen/inhaltssprachen.md)).
- Eine typische mehrsprachige Site verwendet einen Siteaccess pro Sprache, zugeordnet über Pfad oder Host
  ([Siteaccesses](siteaccesses-hosts-und-mandanten.md), [Mehrsprachige Sites](../ersteller/website-ersteller/mehrsprachige-websites.md)).
- URL-Aliase gibt es pro Übersetzung ([Sprechende URLs](url-aliase-und-platzhalter.md)).
- In PHP liest `$object->fetchDataMap( false, 'ger-DE' )` eine einzelne Sprache.

## Rechts-nach-links

Die Schreibrichtung kommt ausschließlich aus der Locale-Datei (`[LanguageSettings] TextDirection=rtl`);
`eZLocale::isRightToLeft()` und `eZLocale::textDirectionOf( 'heb-IL' )` liefern sie in PHP, `$site.text_direction` in
Templates. Geben Sie sie in Ihrem Pagelayout am Element `<html>` aus:

```text
<html lang="{$site.http_equiv.Content-language|wash}"{if eq( first_set( $site.text_direction, 'ltr' ), 'rtl' )} dir="rtl"{/if}>
```

Schreiben Sie Stylesheets mit logischen Eigenschaften (`margin-inline-start`, `text-align: start`), damit ein
Stylesheet beide Richtungen bedient ([Sites mit Rechts-nach-links-Schrift](../ersteller/website-ersteller/websites-von-rechts-nach-links.md),
[Rechts-nach-links-Sprachen in der Administrationsoberfläche](../../funktionen/6.0/rechts-nach-links.md)).

## Im Vergleich mit anderen Frameworks

| Framework | Texte der Oberfläche | Übersetzungen von Inhalten | Exponential |
|---|---|---|---|
| Symfony | Translation-Komponente, XLIFF oder YAML | separate Bundles | `.ts`-Kataloge; Übersetzungen in die Inhalte eingebaut |
| Laravel | `lang/`-Dateien, `__()` | Pakete | dasselbe |
| Django | gettext `.po`, `gettext()` | Pakete wie Apps für Modellübersetzungen | dasselbe |
| Rails | I18n-YAML, `t()` | Gems | dasselbe |
| Spring | `MessageSource`, Properties | Ihr eigener Entwurf | dasselbe |

## Verwandte Seiten

- [Standard für Internationalisierung](../../standards/internationalisierung.md)
- [Übersetzungen und Sprachen](../../funktionen/6.0/uebersetzungen-und-sprachen.md)
- [Sprachen](../sprachen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
