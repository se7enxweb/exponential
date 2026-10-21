---
title: "Lektion 1.4: Ein Siteaccess pro Sprache"
audience: trainer, admin, developer
category: training
order: 14
summary: Die Teilnehmenden geben der zweiten Website einen deutschen Siteaccess, setzen seine Sprachen und seine Fallback-Reihenfolge und prüfen das Ergebnis auf der Seite Inhaltssprachen. 50 Minuten.
keywords: multisite und seitenpakete, sprache, übersetzungs-siteaccess, inhaltssprachen, fallback, multisite and site packages, language, translation siteaccess, SiteLanguageList, Locale, ContentObjectLocale, ShowUntranslatedObjects, ExtensionSettingsSiteAccess, Content languages, ger-DE
type: help
---
# Lektion 1.4: Ein Siteaccess pro Sprache

Die meisten Multisites sind auch mehrsprachig: Jede Sprache einer Website ist ein eigener Siteaccess mit eigener
Adresse, eigener Locale und eigener Reihenfolge der Inhaltssprachen. Diese Lektion fügt der Website aus Lektion 1.3
einen deutschen Siteaccess hinzu und zeigt, wie Exponential entscheidet, welche Übersetzung ein Besucher sieht.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Multisite und Seitenpakete](../README.md) |
| Modul | [Modul 1: Siteaccesses für mehrere Websites](README.md) |
| Dauer | 50 Minuten (15 Präsentation, 35 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Administratoren und Entwickler |
| Voraussetzungen | [Lektion 1.3](eine-zweite-website-hinzufuegen.md): Der Siteaccess `site_b` funktioniert |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- erklären, dass das Hinzufügen einer Inhaltssprache nichts übersetzt und keinen Siteaccess ändert;
- einen Sprach-Siteaccess mit `Locale`, `ContentObjectLocale` und `SiteLanguageList[]` anlegen;
- eine Fallback-Reihenfolge und `ShowUntranslatedObjects` für einen Sprach-Siteaccess wählen;
- die Tabelle **Siteaccesses und ihre Sprachen** auf der Seite **Inhaltssprachen** lesen.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Eine mehrsprachige Website aufbauen](../../../ersteller/website-ersteller/mehrsprachige-websites.md) und
[Inhaltssprachen](../../../../anleitungen/inhaltssprachen.md).

1. **Drei Regeln** (5 Minuten), von der Seite für Website-Ersteller:
   - Das Hinzufügen einer Inhaltssprache übersetzt nichts und ändert keinen Siteaccess; es macht die Sprache nur für
     Übersetzungen verfügbar;
   - ein Siteaccess zeigt jedes Objekt in der ersten Sprache seiner `SiteLanguageList[]`, die das Objekt hat;
   - Objekte in keiner dieser Sprachen werden verborgen, sofern sie nicht als immer verfügbar markiert sind oder der
     Siteaccess `ShowUntranslatedObjects=enabled` hat.
2. **Die Einstellungen eines Sprach-Siteaccess** (5 Minuten). Zeigen Sie den Block am Beamer:

   ```ini
   # settings/siteaccess/site_b_ger/site.ini.append.php
   [RegionalSettings]
   Locale=ger-DE
   ContentObjectLocale=ger-DE
   SiteLanguageList[]
   SiteLanguageList[]=ger-DE
   SiteLanguageList[]=eng-US
   ShowUntranslatedObjects=disabled
   TextTranslation=enabled
   ```

   Die leere Zeile `SiteLanguageList[]` setzt die Liste zurück, die die Kopie mitgebracht hat.
3. **Erweiterungen und Design für eine Übersetzung** (3 Minuten). Ein Übersetzungs-Siteaccess braucht oft die
   Erweiterungseinstellungen des Siteaccess, aus dem er entstanden ist. `site.ini [SiteAccessSettings]
   ExtensionSettingsSiteAccess=site_b`, gesetzt in der eigenen `site.ini.append.php` des Übersetzungs-Siteaccess, lässt
   ihn diese ebenfalls verwenden, sodass er mit demselben Design in seiner eigenen Sprache dargestellt wird.
4. **Wo man nachsieht** (2 Minuten). **Setup** > **Sprachen** öffnet **Inhaltssprachen**, deren Tabelle
   **Siteaccesses und ihre Sprachen** jeden Siteaccess mit seinen Sprachen in der richtigen Reihenfolge auflistet.

### Häufige Fragen

**Was ist der Unterschied zwischen `Locale` und `ContentObjectLocale`?** `Locale` bestimmt Datumsangaben, Zahlen und
die Oberflächentexte der Website; `ContentObjectLocale` ist die Sprache, in der auf diesem Siteaccess neue Inhalte
angelegt werden.

**Warum zeigt die deutsche Website englische Artikel?** Weil `eng-US` der zweite Eintrag ihrer `SiteLanguageList[]`
ist: Er ist der Fallback. Entfernen Sie ihn, um nicht übersetzte Inhalte zu verbergen.

**Wie viele Inhaltssprachen kann eine Installation haben?** Mehr als 30 brauchen ein 64-Bit-PHP, weil die Sprachen
eines Objekts als Bits einer einzigen Ganzzahl gespeichert werden.

**Gibt es eine Weiterleitung von `/` zur Sprache des Besuchers?** Ja, mit `DefaultHostUriMatchMapItems[]`: Lektion 2.4.

### Stolperfallen

- **Die Sprache selbst vergessen.** Ohne die Inhaltssprache `ger-DE` (Schritt 1) können Redakteure nicht in sie
  übersetzen, und der Siteaccess zeigt nur den Fallback.
- **`ContentObjectLocale` später ändern.** Auf einer Website mit Inhalten braucht das Sorgfalt; lesen Sie zuerst die
  Warnung in `settings/site.ini`. In der Übungsumgebung spielt es keine Rolle.
- **Eine Übersetzung entfernen.** Das Entfernen einer Übersetzung eines Objekts löscht sie endgültig. Lassen Sie die
  Teilnehmenden damit nicht an gemeinsamen Inhalten experimentieren.

## Praktische Übung

Verwenden Sie Ihre Schulungsinstallation mit dem Siteaccess `site_b` aus Lektion 1.3.

1. Öffnen Sie in der Administration **Setup** > **Sprachen**. Wenn `ger-DE` nicht aufgeführt ist, klicken Sie auf
   **Sprache hinzufügen**, wählen Sie Deutsch (Deutschland) und bestätigen Sie.
2. Kopieren Sie den Siteaccess der zweiten Website:

   ```bash
   cp -r settings/siteaccess/site_b settings/siteaccess/site_b_ger
   ```

3. Setzen Sie die regionalen Einstellungen, beginnend mit einer leeren Liste:

   ```bash
   php bin/php/console exp:ini set site.ini/RegionalSettings/Locale ger-DE siteaccess:site_b_ger
   php bin/php/console exp:ini set site.ini/RegionalSettings/ContentObjectLocale ger-DE siteaccess:site_b_ger
   php bin/php/console exp:ini clear 'site.ini/RegionalSettings/SiteLanguageList[]' siteaccess:site_b_ger
   php bin/php/console exp:ini add 'site.ini/RegionalSettings/SiteLanguageList[]' ger-DE siteaccess:site_b_ger
   php bin/php/console exp:ini add 'site.ini/RegionalSettings/SiteLanguageList[]' eng-US siteaccess:site_b_ger
   ```

4. Geben Sie ihm einen deutschen Namen: `exp:ini set site.ini/SiteSettings/SiteName "Seite B" siteaccess:site_b_ger`.
5. Registrieren Sie ihn in `AvailableSiteAccessList[]` und `RelatedSiteAccessList[]` mit `exp:ini add ... global`, wie
   in Lektion 1.3, Schritt 7.
6. Leeren Sie den INI-Cache: `php bin/php/ezcache.php --clear-tag=ini` (und starten Sie Velocity neu, wenn er die
   Übungsumgebung ausliefert).
7. Öffnen Sie `/site_b_ger` im Browser. Der Ordner *Site B* wird angezeigt, auf Englisch, weil er noch keine deutsche
   Übersetzung hat und `eng-US` der Fallback ist.
8. Öffnen Sie in der Administration den Ordner *Site B*, wählen Sie in der Sprachliste neben **Bearbeiten** Deutsch,
   übersetzen Sie den Namen in `Seite B` und veröffentlichen Sie.
9. Laden Sie `/site_b_ger` neu: Der deutsche Name erscheint. `/site_b` zeigt weiterhin den englischen.
10. Öffnen Sie **Setup** > **Sprachen** und lesen Sie die Tabelle **Siteaccesses und ihre Sprachen**.

## Erwartetes Ergebnis

- `/site_b_ger` antwortet und zeigt nach Schritt 9 *Seite B*, während `/site_b` *Site B* zeigt.
- Die Tabelle auf **Inhaltssprachen** listet `site_b_ger` mit `ger-DE` vor `eng-US`.
- `exp:ini where 'site.ini/RegionalSettings/SiteLanguageList[]' site_b_ger` zeigt die Zeile zum Zurücksetzen und die
  beiden Werte in der Datei des Siteaccess.
- Der Trainer prüft das, indem er beide Adressen einer Person nebeneinander öffnet.

## Quiz

1. Sie fügen die Inhaltssprache Französisch hinzu. Welche Siteaccesses zeigen jetzt französische Inhalte?
2. Ein deutscher Siteaccess hat `SiteLanguageList[]=ger-DE` und `eng-US`. Ein Artikel existiert nur auf Englisch. Was
   sieht ein Besucher des deutschen Siteaccess?
   A. Nichts B. Den englischen Artikel C. Einen Fehler D. Eine maschinelle Übersetzung
3. Was bewirkt die leere Zeile `SiteLanguageList[]`?
4. Welche Einstellung bestimmt die Sprache, in der auf einem Siteaccess neue Inhalte angelegt werden?
5. Welche Einstellung lässt einen Übersetzungs-Siteaccess die Erweiterungseinstellungen des Siteaccess verwenden, aus
   dem er entstanden ist?

<details>
<summary>Antworten</summary>

1. Keiner: Das Hinzufügen einer Inhaltssprache ändert keinen Siteaccess
   ([Eine mehrsprachige Website aufbauen](../../../ersteller/website-ersteller/mehrsprachige-websites.md#wie-sprachen-funktionieren)).
2. **B.** `eng-US` ist der Fallback; der Artikel wird auf Englisch angezeigt.
3. Sie setzt das Array zurück, sodass die Werte, die die Datei von früheren Dateien geerbt hat, wegfallen und nur die
   folgenden Zeilen zählen.
4. `ContentObjectLocale` in `site.ini [RegionalSettings]`.
5. `ExtensionSettingsSiteAccess` in `site.ini [SiteAccessSettings]`, gesetzt in der eigenen `site.ini.append.php` des
   Übersetzungs-Siteaccess (Kommentar in `settings/site.ini`).

</details>

## Weiterführende Informationen

- [Eine mehrsprachige Website aufbauen](../../../ersteller/website-ersteller/mehrsprachige-websites.md): Fallbacks,
  Übersetzer, Oberflächentexte.
- [Inhaltssprachen](../../../../anleitungen/inhaltssprachen.md): die vollständige Referenz.
- [Sprachumschalter und Adressen pro Sprache](../../../ersteller/website-ersteller/sprachumschalter-und-sprach-urls.md).
- [Übersetzungen und Sprachen](../../../../funktionen/6.0/uebersetzungen-und-sprachen.md).

## Verwandte Seiten

- [Modul 1: Siteaccesses für mehrere Websites](README.md)
- [Multisite und Seitenpakete: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
