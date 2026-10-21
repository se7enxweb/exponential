---
title: "Lektion 2.5: Links und Design-Dateien mit ezurl, ezroot, ezdesign und ezimage"
audience: trainer, developer
category: training
order: 25
summary: Die Teilnehmenden bauen mit ezurl Links, die den Siteaccess berücksichtigen, wählen Anführungszeichen und absolute Adressen, verweisen mit ezroot auf Dateien der Installation und finden Stylesheets und Bilder mit ezdesign und ezimage über die Design-Kette. 40 Minuten.
keywords: entwickler, template-sprache, ezurl, ezroot, ezdesign, ezimage, url_alias, siteaccess, anführungszeichen, absolute adresse, design-kette, links, developers course, template language, quotes, absolute url, design chain
type: help
---
# Lektion 2.5: Links und Design-Dateien mit ezurl, ezroot, ezdesign und ezimage

Ein von Hand geschriebener Link, `<a href="/about">`, bricht, sobald eine Website unter einem Pfad wie `/site/` oder in
einer zweiten Sprache ausgeliefert wird. Die URL-Operatoren bauen Links und Dateiadressen so, wie der Kernel es tut,
damit sie in jedem Siteaccess und jedem Design funktionieren. Diese Lektion vermittelt die vier Operatoren, die jedes
Pagelayout verwendet.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 2: Die Template-Sprache](README.md) |
| Dauer | 40 Minuten (15 Präsentation, 25 Übung) |
| Niveau | Einsteiger |
| Zielgruppe | Entwickler und Template-Bauer |
| Voraussetzungen | [Lektion 2.4](operatoren-wash-und-uebersetzungen.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- Links zu Knoten mit `{$node.url_alias|ezurl}` und zu Modul-Views mit `{'content/view/full/2'|ezurl}` bauen;
- Anführungszeichen und absolute Adressen mit `ezurl( 'no' )` und `ezurl( 'no', 'full' )` steuern;
- eine Datei der Installation mit `ezroot` adressieren;
- ein Stylesheet oder Bild mit `ezdesign` und `ezimage` über die Design-Kette finden;
- erklären, warum von Hand geschriebene Adressen brechen, wenn sich der Siteaccess ändert.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Templates und Design, Teil 6](../../../../anleitungen/templates-und-design.md#6-die-template-sprache-in-zehn-minuten);
die Operatoren sind in `kernel/common/ezurloperator.php` definiert.

1. **Das Problem** (3 Minuten). Öffnen Sie die Website am Projektor unter `/site/` und unter der Adresse eines anderen
   Siteaccess. Dieselbe Seite hat verschiedene Adressen; ein von Hand geschriebener Link zeigt nur auf eine davon.
2. **`ezurl`** (4 Minuten). Es fügt bei Bedarf den Siteaccess-Teil der Adresse hinzu. Standardmäßig gibt es die
   Adresse **mit doppelten Anführungszeichen** aus, in einem Attribut wird es also ohne Anführungszeichen geschrieben:
   `<a href={$url|ezurl}>`. `ezurl( 'no' )` gibt keine Anführungszeichen aus (zum Beispiel in `concat`),
   `ezurl( 'no', 'full' )` eine absolute Adresse mit dem Host, für Feeds und E-Mails.
3. **`url_alias`** (2 Minuten). Jeder Knoten hat eine lesbare Adresse in `$node.url_alias`. Ziehen Sie sie für Links,
   die Besucher sehen, `content/view/full/<id>` vor.
4. **`ezroot`** (2 Minuten). Ein Pfad ab dem Wurzelverzeichnis der Installation, ohne den Siteaccess: `{'/'|ezroot}`
   ist die Startadresse der Installation.
5. **`ezdesign` und `ezimage`** (4 Minuten). `{'stylesheets/site.css'|ezdesign}` sucht die Datei in der Design-Kette
   (Modul 3) und gibt die Adresse des ersten Treffers zurück; `{'logo.png'|ezimage}` tut dasselbe in `images/`. Die
   meisten Stylesheets und Skripte eines Designs werden stattdessen über den Packer von ezjscore geladen (Kurs
   Theme-Designer).

### Häufige Fragen

**Warum stehen Anführungszeichen in meiner Ausgabe?** Das ist die Voreinstellung von `ezurl`. Schreiben Sie
`href={...|ezurl}` ohne eigene Anführungszeichen oder verwenden Sie `ezurl( 'no' )` in Ihren eigenen
Anführungszeichen.

**Brauche ich `wash` nach `ezurl`?** Nein: `ezurl` erzeugt eine Adresse; maskieren Sie den *Text* des Links.

**Welche Adresse sollte eine E-Mail oder ein RSS-Feed verwenden?** Eine absolute: `ezurl( 'no', 'full' )`.

### Stolperfallen

- **Doppelte Anführungszeichen.** `href="{$url|ezurl}"` gibt `href=""/about""` aus. Entfernen Sie die äußeren
  Anführungszeichen.
- **`ezurl` auf einer vollständigen Adresse.** Geben Sie Links zu anderen Websites so aus, wie sie sind (maskiert).
- **`ezdesign` und eine fehlende Datei.** Hat kein Design die Datei, gibt der Operator trotzdem eine Adresse zurück,
  die im Browser dann mit „nicht gefunden“ antwortet. Prüfen Sie den Netzwerk-Tab der Entwicklerwerkzeuge des
  Browsers.

## Praktische Übung

Verwenden Sie die Spielwiese aus Lektion 2.1 und führen Sie sie nach jedem Schritt aus.

1. Verlinken Sie eine Modul-View in den drei Formen:

   ```
   {'content/view/full/2'|ezurl}
   {'content/view/full/2'|ezurl( 'no' )}
   {'content/view/full/2'|ezurl( 'no', 'full' )}
   ```

2. Verlinken Sie den Knoten mit seiner lesbaren Adresse: `<a href={$node.url_alias|ezurl}>{$node.name|wash}</a>`.
3. Bauen Sie eine Linkliste der ersten drei Kinder:

   ```
   {foreach $node.children as $child max 3}
       <a href={$child.url_alias|ezurl}>{$child.name|wash}</a>
   {/foreach}
   ```

4. Geben Sie das Wurzelverzeichnis der Installation aus: `{'/'|ezroot}`.
5. Suchen Sie ein Stylesheet und ein Bild über die Design-Kette:

   ```
   {'stylesheets/core.css'|ezdesign}
   {'logo.png'|ezimage}
   ```

6. Führen Sie die Spielwiese mit der Standardoption `-s` für den Siteaccess editor aus und vergleichen Sie die Ausgabe
   von Schritt 1: `php bin/php/ezexec.php -s editor extension/mysite_hello/scripts/play.php` (die Ordner in
   `settings/siteaccess/` nennen die Siteaccesses Ihrer Installation).
7. Schreiben Sie absichtlich einen falschen Link `<a href="{$node.url_alias|ezurl}">` und sehen Sie sich die
   verdoppelten Anführungszeichen an.
8. Korrigieren Sie ihn und committen Sie das Template.

## Erwartetes Ergebnis

- Schritt 1 gibt die Adresse mit Anführungszeichen, ohne Anführungszeichen und mit `http://` und dem Host aus.
- Schritt 3 gibt drei Links mit lesbaren Adressen aus.
- Schritt 5 gibt Adressen unterhalb von `/design/<design>/` oder `/extension/<name>/design/<design>/` aus, je nachdem,
  wo die erste passende Datei gefunden wurde.
- Schritt 6: Die Adressen unterscheiden sich, wenn der andere Siteaccess über einen Pfad oder einen Host erkannt wird.
- Schritt 7 zeigt `href=""...""` in der Ausgabe.
- Der Trainer fragt alle Teilnehmenden, welche Form von `ezurl` in eine E-Mail gehört.

## Quiz

1. Warum wird `href={$url|ezurl}` ohne Anführungszeichen geschrieben?
2. Welche Form liefert eine absolute Adresse mit dem Host?
   A. `ezurl` B. `ezurl( 'no' )` C. `ezurl( 'no', 'full' )` D. `ezroot`
3. Was ist der Unterschied zwischen `ezurl` und `ezroot`?
4. Wo sucht `ezimage` nach `logo.png`?
5. Welches Attribut eines Knotens liefert seine lesbare Adresse?

<details>
<summary>Antworten</summary>

1. `ezurl` gibt die Adresse standardmäßig mit doppelten Anführungszeichen aus.
2. **C.** `ezurl( 'no', 'full' )`.
3. `ezurl` fügt den Siteaccess-Teil der Adresse hinzu; `ezroot` liefert einen Pfad ab dem Wurzelverzeichnis der
   Installation ohne ihn.
4. In den Ordnern `images/` der Design-Kette, vom Design der Website bis hinunter zu `standard`.
5. `url_alias`.

</details>

## Weiterführende Literatur

- [Templates und Design](../../../../anleitungen/templates-und-design.md): die Design-Kette und Overrides.
- [URLs und Aliase](../../../../anleitungen/urls-und-aliase.md): wie lesbare Adressen entstehen.
- [Sichere Weiterleitungen](../../../../../eng-us/features/6.0/safe-redirects.md): Adressen, die von Benutzern kommen.

## Verwandte Seiten

- [Modul 2: Die Template-Sprache](README.md)
- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
