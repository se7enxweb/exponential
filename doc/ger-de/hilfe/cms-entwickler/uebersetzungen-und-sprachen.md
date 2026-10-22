---
title: Übersetzungen und Sprachen
audience: cms-developer
category: cms-development
order: 18
summary: Für CMS-Entwickler, die mehrsprachige Websites bauen. Die Seite behandelt Inhaltssprachen gegenüber Oberflächensprachen, Übersetzungen als Teil eines Objekts, übersetzbare und gemeinsame Felder, SiteLanguageList und Ausweichsprachen, immer verfügbare Objekte, einen Siteaccess je Sprache, Sprachumschalter und die Übersetzung eigener Template-Texte.
keywords: Übersetzungen, Sprachen, mehrsprachig, Inhaltssprache, Oberflächensprache, SiteLanguageList, ShowUntranslatedObjects, ContentObjectLocale, immer verfügbar, Hauptsprache, Sprachmaske, übersetzbares Attribut, language_switcher, i18n, translation.ts, translations, languages, multilingual, content language, interface language, always available, main language
type: help
---
# Übersetzungen und Sprachen

Diese Seite richtet sich an Entwickler, die eine Website in mehr als einer Sprache bauen. In Exponential ist eine
Übersetzung keine Kopie einer Seite: Sie ist eine weitere Sprache **desselben Objekts**; Relationen, Platzierungen,
Berechtigungen und der Baum werden also von allen Sprachen geteilt, und nur die Texte unterscheiden sich.

## Inhalt

1. [Zwei Arten von Sprache](#zwei-arten-von-sprache)
2. [Übersetzungen gehören zum Objekt](#übersetzungen-gehören-zum-objekt)
3. [Welche Felder übersetzt werden](#welche-felder-übersetzt-werden)
4. [Welche Sprache ein Besucher sieht](#welche-sprache-ein-besucher-sieht)
5. [Immer verfügbare Objekte](#immer-verfügbare-objekte)
6. [Ein Siteaccess je Sprache](#ein-siteaccess-je-sprache)
7. [Template-Texte](#template-texte)
8. [Übersetzungen aus Code](#übersetzungen-aus-code)
9. [Caches nach Sprachänderungen](#caches-nach-sprachänderungen)

## Zwei Arten von Sprache

| | Inhaltssprache | Oberflächensprache |
|---|---|---|
| Was | die Sprache, in der Objekte geschrieben sind | die Texte der Templates und der Administration |
| Gespeichert | in der Datenbank, je Objekt | in `.ts`-Übersetzungsdateien (`share/translations/<locale>/`, `translations/` einer Erweiterung) |
| Gewählt über | `site.ini [RegionalSettings] SiteLanguageList[]` des Siteaccess | `[RegionalSettings] Locale` des Siteaccess |

Sie sind voneinander unabhängig: Ein Siteaccess kann deutsche Inhalte mit einer englischen Oberfläche zeigen
([Inhaltssprachen, Abschnitt 1](../../anleitungen/inhaltssprachen.md#1-inhaltssprachen-und-oberflächensprachen)).

## Übersetzungen gehören zum Objekt

Inhaltssprachen werden unter **Setup** > **Sprachen** hinzugefügt, bevor jemand in sie übersetzt. Jede Sprache ist ein
Bit in einer Maske (2 für die erste, 4 für die zweite und so weiter), und jedes Objekt vermerkt seine Sprachen in
seiner eigenen Maske; "Objekte auf Deutsch" ist also auf jeder Datenbank ein einziger Bit-Test. Jedes Objekt hat eine
**Hauptsprache**, die, in der es angelegt wurde, und die sich nicht entfernen lässt, solange sie die Hauptsprache ist.
Wie die Speicherung funktioniert und die Grenze von 62 Sprachen:
[Inhaltssprachen, Abschnitt 2](../../anleitungen/inhaltssprachen.md#2-wie-sprachen-gespeichert-werden).

Weil alle Übersetzungen Versionen eines Objekts sind, erzeugt das Veröffentlichen einer deutschen Übersetzung eine neue
Version des Objekts, und der englische Text in dieser Version bleibt unverändert
([Versionen, Entwürfe und Veröffentlichen](versionen-entwuerfe-und-veroeffentlichen.md)).

## Welche Felder übersetzt werden

Jedes Klassenattribut hat ein Kennzeichen **übersetzbar** (`can_translate`). Entfernen Sie es für Werte, die in jeder
Sprache gleich sein müssen: Daten, Preise, E-Mail-Adressen, Bilder ohne Text, Relationen zu gemeinsam genutzten
Objekten. Übersetzer sehen dann den Wert der Hauptsprache und können ihn nicht je Sprache ändern. Auch Klassen haben
übersetzbare Namen und Beschreibungen, die Redakteure im Menü zum Anlegen sehen
([Inhaltsklassen und Attribute](inhaltsklassen-und-attribute.md)).

## Welche Sprache ein Besucher sieht

Je Siteaccess, in `settings/siteaccess/<name>/site.ini.append.php`:

```ini
[RegionalSettings]
Locale=ger-DE
ContentObjectLocale=ger-DE
SiteLanguageList[]
SiteLanguageList[]=ger-DE
SiteLanguageList[]=eng-US
ShowUntranslatedObjects=disabled
TextTranslation=enabled
```

Ein Objekt wird in der ersten Sprache aus `SiteLanguageList[]` angezeigt, die es hat. Mit
`ShowUntranslatedObjects=disabled` (dem mitgelieferten Wert) werden Objekte, die in keiner der aufgeführten Sprachen
vorliegen, ausgeblendet, sofern sie nicht immer verfügbar sind. Die leere Zeile `SiteLanguageList[]` löscht, was andere
Dateien setzen. Jede Einstellung:
[Inhaltssprachen, Abschnitt 5](../../anleitungen/inhaltssprachen.md#5-eine-sprache-anzeigen-sitelanguagelist-und-siteaccesses).

Fetches folgen denselben Regeln: Eine deutsche Liste zeigt deutsche Titel, fällt auf Englisch zurück und lässt weg,
was keins von beiden hat. Der Fetch `list` hat die Parameter `language` und `only_translated` für Listen, die davon
abweichen müssen ([Inhalte in Templates abrufen](inhalte-in-templates-abrufen.md)).

## Immer verfügbare Objekte

**Immer verfügbar** ist ein Kennzeichen je Objekt (Bit 0 seiner Maske): Ein solches Objekt wird in seiner Hauptsprache
angezeigt, wo keine aufgeführte Sprache vorhanden ist. Ordner, Benutzer, Benutzergruppen und Medien sind meist immer
verfügbar, sodass eine deutsche Website trotzdem den Bilderordner und den Autor eines Artikels findet. Eine Klasse legt
den Standard für neue Objekte fest (`always_available`)
([Inhaltssprachen, Abschnitt 6](../../anleitungen/inhaltssprachen.md#6-hauptsprache-und-immer-verfügbar)).

## Ein Siteaccess je Sprache

Eine mehrsprachige Website hat meist einen öffentlichen Siteaccess je Sprache, zugeordnet über ein URL-Element (`/de/`)
oder einen Hostnamen, mit einer gemeinsamen Datenbank und aufgeführt in `[SiteAccessSettings] RelatedSiteAccessList[]`.
Jede Sprache hat eigene URL-Aliase für denselben Knoten ([URL-Aliase und Routing](url-aliase-und-routing.md)). Für
Links zwischen den Sprachfassungen einer Seite nennt `[RegionalSettings] TranslationSA[]` die Siteaccesses der
Übersetzungen, und der Template-Operator `language_switcher` mit dem Modul `switchlanguage/to` baut die Links.
Einrichtung im Installationsbuch: [10.14 Mehrsprachige Websites](../../installation/10-nach-der-installation.md#1014-mehrsprachige-sites)
und [Siteaccesses und Multisite](siteaccesses-und-multisite.md).

## Template-Texte

Feste Texte in Ihren Templates werden mit dem Operator `i18n` und einem Kontext übersetzt:

```smarty
<a href={'/content/search'|ezurl}>{'Search'|i18n( 'extension/exp_mysite' )}</a>
```

Die Übersetzungen liegen in `translations/<locale>/translation.ts` Ihrer Erweiterung, und die Erweiterung ist in
`site.ini [RegionalSettings] TranslationExtensions[]` aufgeführt
([Template-Texte übersetzen](../designer/vorlagen/template-texte-uebersetzen.md),
[Übersetzungen in einer Erweiterung](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/uebersetzungen-in-einer-erweiterung.md)).

## Übersetzungen aus Code

`eZContentFunctions::updateAndPublishObject()` nimmt einen Parameter `language`, sodass ein Import einem vorhandenen
Objekt eine Übersetzung hinzufügt, statt ein zweites Objekt anzulegen. Importieren Sie zuerst die Hauptsprache, dann
jede Übersetzung in dasselbe Objekt, gefunden über seine Remote-ID ([Inhalte aus PHP](inhalte-aus-php.md),
[Inhalte abbilden: Übersetzungen](../../installation/migration/inhaltsmodell-zuordnen.md#3-objekte-versionen-und-übersetzungen)).

## Caches nach Sprachänderungen

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user        # after changing [RegionalSettings]
php bin/php/ezcache.php --clear-tag=content --allow-root-user    # lists and pages drawn again
php bin/php/ezcache.php --clear-tag=i18n --allow-root-user       # after changing a .ts file
```

Mit Velocity zusätzlich `./console exp:velocity cache clear`. Die vollständige Liste, einschließlich des Neuaufbaus
der URL-Aliase: [Inhaltssprachen, Abschnitt 10](../../anleitungen/inhaltssprachen.md#10-befehle-für-die-shell).

## Verwandte Seiten

- [Inhaltssprachen](../../anleitungen/inhaltssprachen.md): die vollständige Anleitung
- [Sprachen](../sprachen.md): die Sicht der Redakteure
- [Rechts-nach-links](../../funktionen/6.0/rechts-nach-links.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
