---
title: "Lektion 3.1: Die Design-Kette"
audience: trainer, developer
category: training
order: 31
summary: Die Teilnehmenden finden mit exp:ini where das Website-Design eines Siteaccess, folgen der Kette vom Website-Design über die zusätzlichen Designs bis zu standard und geben ihrer Kurserweiterung einen Design-Ordner, der sich in die Kette einreiht. 45 Minuten.
keywords: entwickler, design, design-kette, SiteDesign, AdditionalSiteDesignList, StandardDesign, standard, DesignExtensions, design.ini, ActiveExtensions, exp:ini where, fallback, fallback-kette, developers course, design chain, fallback chain
type: help
---
# Lektion 3.1: Die Design-Kette

Ein Template wird nicht in einem Ordner gesucht, sondern in einer Kette von Designs: zuerst das Website-Design, dann
die zusätzlichen Designs, dann `standard`. Wer die Kette kennt, versteht, warum ein geändertes Template keine Wirkung
hat und wohin die eigenen Templates gehören. In dieser Lektion lesen die Teilnehmenden die Kette ihres Siteaccess und
fügen ihr den Design-Ordner ihrer Erweiterung hinzu.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 3: Designs, Overrides und Fehlersuche](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Entwickler und Template-Bauer |
| Voraussetzungen | [Modul 2](../modul-02-die-template-sprache/README.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die Reihenfolge nennen, in der ein Template gesucht wird: Overrides, Website-Design, zusätzliche Designs,
  `standard`;
- das Website-Design und die zusätzlichen Designs eines Siteaccess mit `exp:ini where` und `exp:ini get` finden;
- erklären, was ein Design-Ordner enthält (`templates/`, `override/templates/`, `stylesheets/`, `images/`,
  `javascript/`);
- den Design-Ordner einer Erweiterung mit `DesignExtensions[]` in die Kette aufnehmen und die Erweiterung einschalten.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Templates und Design, Teile 1 und 2](../../../../anleitungen/templates-und-design.md#1-wie-ein-template-gefunden-wird)
und [Ladereihenfolge der Erweiterungen](../../../../../eng-us/features/6.0/extension-loading-order.md).

1. **Die Suche** (4 Minuten). Für ein Template wie `node/view/full.tpl` gewinnt die erste gefundene Datei:
   1. Override-Templates, deren Regeln in `override.ini` passen (Lektion 3.2);
   2. `templates/` des Website-Designs;
   3. dasselbe in den zusätzlichen Designs;
   4. `design/standard/templates/`, der Fallback, der nie bearbeitet wird.
2. **Die Einstellungen** (3 Minuten). `site.ini [DesignSettings]`: `SiteDesign`, `AdditionalSiteDesignList[]` und
   `StandardDesign=standard`. Jeder Siteaccess setzt seine eigenen in `settings/siteaccess/<sa>/site.ini.append.php`.
3. **Wo Designs liegen** (3 Minuten). In `design/<design>/` der Installation oder in
   `extension/<name>/design/<design>/`. Die Design-Ordner einer Erweiterung reihen sich in die Kette ein, wenn die
   Erweiterung aktiv ist und ihre `settings/design.ini.append.php` `[ExtensionSettings] DesignExtensions[]=<name>`
   enthält.
4. **Reihenfolge zwischen Erweiterungen** (3 Minuten). Eine früher geladene Erweiterung gewinnt bei Templates und
   Einstellungen. Zeigen Sie `exp:ini get site.ini/ExtensionSettings/ActiveExtensions`.
5. **Website- und Admin-Designs** (2 Minuten). Der Siteaccess der Administration hat ein eigenes Design (`admin4` bei
   neuen Installationen, `admin3` und andere sind verfügbar); Website-Templates wirken sich nie auf ihn aus und
   umgekehrt.

### Häufige Fragen

**Warum nicht `design/standard/` bearbeiten?** Es ist das Fallback-Design des Kernels und wird bei jedem Update
ersetzt. Legen Sie Ihr Template in Ihren eigenen Design-Ordner.

**Kann ein Siteaccess das Design eines anderen verwenden?** Ja: Führen Sie es in `AdditionalSiteDesignList[]` auf.
Viele Websites teilen sich so ein Basisdesign.

**Was ist `design_base` in der Cache-Liste?** Die zwischengespeicherte Liste der Design-Ordner; leeren Sie sie nach dem
Hinzufügen eines Design-Ordners mit `php bin/php/ezcache.php --clear-id=design_base`.

### Stolperfallen

- **Falscher Design-Name.** Ein Ordner `extension/mysite_hello/design/mysite/` bewirkt nichts, solange `mysite` nicht
  das Website-Design oder ein zusätzliches Design des Siteaccess ist. Verwenden Sie den Namen, den `exp:ini where`
  ausgegeben hat.
- **Erweiterung nicht aktiv.** `DesignExtensions[]` wird nur aus aktiven Erweiterungen gelesen.
- **Veraltete Caches.** Neue Template-Pfade verlangen, dass die Caches `template-override`, `design_base` und der
  Template-Cache geleert werden.

## Praktische Übung

Auf der Entwicklungsinstallation. Verwenden Sie dort, wo die Schritte `<sa>` sagen, den Namen Ihres öffentlichen
Siteaccess (bei einem `exp:install` mit Standardwerten `site`).

1. Suchen Sie das Website-Design: `php bin/php/console exp:ini where site.ini/DesignSettings/SiteDesign <sa>`.
2. Listen Sie die zusätzlichen Designs auf: `php bin/php/console exp:ini get site.ini/DesignSettings/AdditionalSiteDesignList <sa>`.
3. Schreiben Sie die Kette auf Papier: Website-Design, zusätzliche Designs, `standard`.
4. Suchen Sie, wo der Ordner des Website-Designs liegt: `ls -d design/<design> extension/*/design/<design> 2>/dev/null`.
5. Legen Sie den Design-Ordner Ihrer Erweiterung mit dem Namen des Website-Designs an:

   ```bash
   mkdir -p extension/mysite_hello/design/<design>/templates
   mkdir -p extension/mysite_hello/design/<design>/override/templates
   mkdir -p extension/mysite_hello/settings
   ```

6. Registrieren Sie das Design und schalten Sie die Erweiterung ein (Modul 7 erklärt beides ausführlich):

   ```bash
   php bin/php/console exp:ini add design.ini/ExtensionSettings/DesignExtensions mysite_hello extension:mysite_hello
   php bin/php/console exp:ini add site.ini/ExtensionSettings/ActiveExtensions mysite_hello global
   php bin/php/ezcache.php --clear-tag=ini
   php bin/php/ezcache.php --clear-id=design_base
   ```

7. Prüfen Sie, dass die Erweiterung aufgeführt ist: `php bin/php/console exp:ini get site.ini/ExtensionSettings/ActiveExtensions`.
8. Kopieren Sie die Zeilenansicht des Kernels in Ihr Design: `mkdir -p extension/mysite_hello/design/<design>/templates/node/view`
   und `cp design/standard/templates/node/view/line.tpl extension/mysite_hello/design/<design>/templates/node/view/`.
9. Fügen Sie oben in der Kopie eine sichtbare Zeile `<p>mysite_hello line view</p>` ein, leeren Sie die
   Template-Caches (`php bin/php/ezcache.php --clear-tag=template` und `--clear-id=template-override`) und öffnen Sie
   eine Seite, die Elemente in der Zeilenansicht auflistet. Liefert das Website-Design oder ein Override bereits ein
   `line.tpl`, wird Ihre Kopie nicht verwendet: Notieren Sie, welche Datei stattdessen antwortet (Lektion 3.4 zeigt,
   wie).
10. Committen Sie die neuen Dateien im Repository der Erweiterung.

## Erwartetes Ergebnis

- Schritt 1 listet `settings/site.ini` und die Datei des Siteaccess und endet mit `In effect:` und dem Namen des
  Designs.
- Schritt 6 hat `extension/mysite_hello/settings/design.ini.append.php` angelegt und
  `settings/override/site.ini.append.php` eine Zeile hinzugefügt.
- Schritt 9: Entweder erscheint die Zeile in Listen, oder die Teilnehmenden können die Datei nennen, die zuerst
  geantwortet hat, und sagen, warum.
- Der Trainer prüft die aufgeschriebene Kette aus Schritt 3 bei allen Teilnehmenden.

## Quiz

1. In welcher Reihenfolge werden Designs nach einem Template durchsucht?
2. Welche Einstellung nennt die Erweiterungen, deren Design-Ordner sich in die Kette einreihen?
   A. `ActiveExtensions[]` B. `DesignExtensions[]` C. `AdditionalSiteDesignList[]` D. `StandardDesign`
3. Was bedeutet `StandardDesign=standard`?
4. Ihre Kopie von `line.tpl` hat keine Wirkung. Nennen Sie zwei mögliche Gründe.
5. Richtig oder falsch: Der Admin-Siteaccess verwendet dieselbe Design-Kette wie die öffentliche Website.

<details>
<summary>Antworten</summary>

1. Passende Overrides, das Website-Design, die zusätzlichen Designs in ihrer Reihenfolge, dann `standard`.
2. **B.** `[ExtensionSettings] DesignExtensions[]` in der `design.ini.append.php` der Erweiterung; die Erweiterung muss
   außerdem aktiv sein.
3. `standard` ist das Fallback-Design, das zuletzt durchsucht wird.
4. Der Name des Design-Ordners steht nicht in der Kette des Siteaccess; die Erweiterung ist nicht aktiv; ein Override
   oder ein früheres Design liefert das Template; die Caches wurden nicht geleert.
5. **Falsch.** Jeder Siteaccess hat seine eigenen `[DesignSettings]`.

</details>

## Weiterführende Literatur

- [Templates und Design](../../../../anleitungen/templates-und-design.md): das Design finden, Overrides, Fehlersuche.
- [Ladereihenfolge der Erweiterungen](../../../../../eng-us/features/6.0/extension-loading-order.md): welche Erweiterung gewinnt.
- [Das Design registrieren](../../../../../eng-us/help/designers/extensions/registering-the-design.md): dieselben Schritte für ein Theme.
- [Zusätzliche Erweiterungsverzeichnisse](../../../../../eng-us/features/6.0/additional-extension-directories.md): Erweiterungen
  außerhalb von `extension/`.

## Verwandte Seiten

- [Modul 3: Designs, Overrides und Fehlersuche](README.md)
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
