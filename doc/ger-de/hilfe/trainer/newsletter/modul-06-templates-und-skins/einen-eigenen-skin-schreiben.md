---
title: "Lektion 6.2: Einen eigenen Skin schreiben"
audience: trainer, designer, developer
category: training
order: 62
summary: Designer und Entwickler legen in ihrem eigenen Design einen Skin an (html.tpl und text.tpl), registrieren ihn in AvailableSkinArray, leeren die Caches und prüfen ihn in der Skin-Vorschau und in einem Testversand, der in eine Datei geschrieben wird. 60 Minuten.
keywords: newsletter mit cjw newsletter, skin, template, design-override, betreff, template-cache, html.tpl, text.tpl, outputformat, AvailableSkinArray, set-block, newsletters with cjw newsletter, design override, subject, template cache
type: help
---
# Lektion 6.2: Einen eigenen Skin schreiben

Die meisten Organisationen wollen, dass ihr Newsletter wie ihre Marke aussieht. Ein Skin besteht aus zwei Templates in
einem Design: eines für den HTML-Teil und eines für den Textteil. In dieser Lektion erstellen Designer einen kleinen
eigenen Skin, ausgehend von einem mitgelieferten, und prüfen ihn, ohne eine einzige echte Mail zu senden.

> **Hinweis**
> Arbeiten Sie auf Ihrer eigenen Schulungsinstallation. Der Testversand in Schritt 10 schreibt eine Datei, weil
> `TransportMethodPreview=file` gilt (Lektion 1.2), und geht nur an eine Adresse unter `example.invalid`.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Newsletter mit CJW Newsletter](../README.md) |
| Modul | [Modul 6: Templates und Skins](README.md) |
| Dauer | 60 Minuten (15 Präsentation, 45 Übung) |
| Niveau | Experten |
| Zielgruppe | Designer und Entwickler, die die Template-Sprache kennen |
| Voraussetzungen | [Lektion 6.1](skins-und-ausgabeformate.md); ein eigenes Design auf der Schulungsinstallation; Shell-Zugang |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die zwei Template-Dateien eines Skins nennen und sagen, wo sie in einem Design liegen;
- den Betreff der Mail aus einem Skin-Template setzen;
- einen neuen Skin in einem Override in `[NewsletterSettings] AvailableSkinArray[]` registrieren;
- die richtigen Caches leeren und den Skin in der Vorschau und in einer Testdatei prüfen;
- die Regeln für Mail-HTML befolgen (Tabellen, Inline-Styles, keine Webfonts).

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: die Rendering-Anleitung der Erweiterung („Writing a skin“, verlinkt von der
[Funktionsseite](../../../../../eng-us/features/6.0/extensions/cjw_newsletter.md#the-features-of-420)),
[Templates und Design](../../../../anleitungen/templates-und-design.md) und [Caches](../../../../anleitungen/caches.md).

1. **Die Dateien** (3 Minuten). Ein Skin `<name>` besteht aus
   `design:newsletter/skin/<name>/outputformat/html.tpl` und `.../text.tpl`, in einem beliebigen Design des
   Siteaccess, der die Liste rendert. Legen Sie Ihren in Ihr eigenes Design, niemals in den Ordner der Erweiterung.
2. **Der Betreff** (2 Minuten). Beide Templates setzen den Betreff mit
   `{set-block variable=$subject scope=root}...{/set-block}`.
3. **Die Variablen** (3 Minuten). Die Templates bekommen `$contentobject` (die Version der Ausgabe),
   `$newsletter_list`, `$newsletter_language`, `$newsletter_skin` und `$newsletter_site_url`. Die Rendering-Anleitung
   führt die Hilfsoperatoren auf (zum Beispiel `cjwnl_abs_url` für absolute Adressen).
4. **Registrieren** (2 Minuten). Fügen Sie `AvailableSkinArray[]=<name>` in
   `settings/override/cjw_newsletter.ini.append.php` hinzu; dann bieten das Formular der Liste und die Versandseite
   ihn an.
5. **Regeln für Mail-HTML** (3 Minuten). Ein 600 Pixel breites Tabellenlayout, Inline-Styles, keine Webfonts, keine
   Hintergrundbilder, absolute Adressen für Links und Bilder. Kopieren Sie lieber einen mitgelieferten Skin, statt bei
   null anzufangen.
6. **Caches** (2 Minuten). Neue Template-Dateien erfordern das Leeren des Template- und des Template-Override-Caches;
   Einstellungen erfordern den INI-Cache.

### Häufige Fragen

**Kann ich stattdessen einen mitgelieferten Skin überschreiben?** Ja: Legen Sie
`newsletter/skin/default/outputformat/html.tpl` in Ihr Design, und es hat Vorrang vor dem der Erweiterung. Ein neuer
Name ist sicherer, weil der mitgelieferte Skin zum Vergleich verfügbar bleibt.

**Warum sind die Bilder in der Mail kaputt?** Relative Adressen funktionieren in einer Mail nicht. Bauen Sie absolute
Adressen aus der Website-URL.

**Wie schreibe ich den Textteil?** Mit `TextFormat=plain` in der Gruppe `[Skin_<name>]` des Skins schreibt das
Text-Template ihn selbst; siehe den Skin company und die Rendering-Anleitung. Zeilenumbrüche neben Block-Tags brauchen
`{"\n"}`.

### Stolperfallen

- **Die Templates der Erweiterung bearbeiten.** Ein Update der Erweiterung überschreibt sie. Verwenden Sie immer Ihr
  Design.
- **Das falsche Design.** Die Liste rendert mit ihrem Siteaccess; der Skin muss in einem Design dieses Siteaccess
  liegen.
- **Template-Cache.** Ein neues Template wird erst gefunden, wenn die Caches geleert sind; die Vorschau zeigt den
  alten Skin.
- **Externe Schriften oder Skripte einbinden.** Mailprogramme ignorieren oder blockieren sie.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation, in ihrem Wurzelverzeichnis. Ersetzen Sie `<mydesign>` durch den
Ordner Ihres Designs und `<name>` durch Ihren Vornamen (für den Skin-Namen nur Kleinbuchstaben, zum Beispiel
`brand_anna`).

1. Kopieren Sie den mitgelieferten Standard-Skin unter einem neuen Namen in Ihr Design:

   ```bash
   mkdir -p design/<mydesign>/templates/newsletter/skin/brand_<name>/outputformat
   cp extension/cjw_newsletter/design/standard/templates/newsletter/skin/default/outputformat/*.tpl \
      design/<mydesign>/templates/newsletter/skin/brand_<name>/outputformat/
   ```

   (Wenn Ihr Design in einer Erweiterung liegt, verwenden Sie stattdessen den Ordner `design/<mydesign>/` dieser
   Erweiterung.)
2. Öffnen Sie die neue `html.tpl`. Suchen Sie den `set-block`, der `$subject` setzt, und stellen Sie dem Titel den
   Kurznamen Ihrer Organisation voran.
3. Ändern Sie einen sichtbaren Style inline, zum Beispiel die Farbe der Überschrift, und behalten Sie das
   Tabellenlayout bei.
4. Öffnen Sie `text.tpl` und nehmen Sie dieselbe Änderung am Betreff vor.
5. Registrieren Sie den Skin in `settings/override/cjw_newsletter.ini.append.php`:

   ```ini
   [NewsletterSettings]
   AvailableSkinArray[]=default
   AvailableSkinArray[]=brand_<name>
   ```

6. Leeren Sie die Caches:

   ```bash
   php bin/php/ezcache.php --clear-tag=ini
   php bin/php/ezcache.php --clear-tag=template
   php bin/php/ezcache.php --clear-id=content
   ```

7. Öffnen Sie **Vorschau der Layouts** und suchen Sie `brand_<name>`; sehen Sie sich seinen **HTML-Teil** und
   **Text-Teil** an.
8. Bearbeiten Sie Ihre Liste und erlauben Sie `brand_<name>`. Veröffentlichen Sie.
9. Öffnen Sie **Newsletter versenden** bei einer ungesendeten Ausgabe, wählen Sie `brand_<name>` in **Layout dieses
   Versands**, sehen Sie sich **Vorschau als Abonnent** an und klicken Sie dann auf **Abbrechen**.
10. Senden Sie mit **Testnewsletter versenden** einen Test der Ausgabe an `test-<name>@example.invalid` und öffnen Sie
    die Datei in `var/log/mail`: Prüfen Sie die Betreffzeile und den geänderten Style.

## Erwartetes Ergebnis

- Der Skin `brand_<name>` existiert im Design der Person, wird in der Skin-Vorschau aufgeführt und auf der
  Versandseite angeboten.
- Der Betreff der Testdatei beginnt mit `[Test]` und dann dem Kurznamen der Organisation.
- Der eigene Ordner der Erweiterung ist unverändert.
- Der Trainer prüft die Testdatei einer Person und den Ort ihrer Templates.

## Quiz

1. Welche zwei Dateien bilden einen Skin?
2. Wo müssen die Templates Ihres Skins liegen?
   A. im Design-Ordner der Erweiterung B. in einem Design des Siteaccess, der die Liste rendert C. in `var/` D. in
   `settings/override/`
3. Wie setzt ein Skin-Template den Betreff der Mail?
4. Welche Einstellung macht einen neuen Skin auswählbar?
5. Warum brauchen Bilder in einem Newsletter absolute Adressen?

<details>
<summary>Antworten</summary>

1. `outputformat/html.tpl` und `outputformat/text.tpl` unterhalb von `newsletter/skin/<name>/` (Rendering-Anleitung,
   verlinkt von der [Funktionsseite](../../../../../eng-us/features/6.0/extensions/cjw_newsletter.md#the-features-of-420)).
2. **B.** In Ihrem eigenen Design, niemals in dem der Erweiterung ([Templates und Design](../../../../anleitungen/templates-und-design.md)).
3. Mit `{set-block variable=$subject scope=root}...{/set-block}` (Rendering-Anleitung).
4. `[NewsletterSettings] AvailableSkinArray[]` in einem Override ([Funktionsseite](../../../../../eng-us/features/6.0/extensions/cjw_newsletter.md#settings-you-will-use)).
5. Eine Mail wird außerhalb der Website gelesen; relative Adressen haben nichts, worauf sie sich beziehen könnten.

</details>

## Weiterführende Informationen

- [Templates und Design](../../../../anleitungen/templates-und-design.md): Designs, Overrides und die Template-Sprache.
- [Caches](../../../../anleitungen/caches.md): welche Caches nach einer Template-Änderung zu leeren sind.
- [cjw_newsletter: Newsletter in der Administration](../../../../../eng-us/features/6.0/extensions/cjw_newsletter.md#the-features-of-420):
  die Rendering-Anleitung mit Variablen und Operatoren.

## Verwandte Seiten

- [Modul 6: Templates und Skins](README.md)
- [Newsletter mit CJW Newsletter: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
