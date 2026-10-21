---
title: "Lektion 1.2: Die Design-Kette eines Siteaccess lesen"
audience: trainer, designer, developer
category: training
order: 12
summary: Die Teilnehmenden lesen die Design-Kette eines Siteaccess mit exp:ini (SiteDesign, AdditionalSiteDesignList, StandardDesign, DesignExtensions) und sagen vorher, welche Datei eine Template-Suche findet. 45 Minuten.
keywords: theme-design, design-kette, fallback, SiteDesign, AdditionalSiteDesignList, StandardDesign, DesignExtensions, siteaccess, exp:ini, where, get, reihenfolge der erweiterungen, theme design, design chain, extension order
type: help
---
# Lektion 1.2: Die Design-Kette eines Siteaccess lesen

Jeder Siteaccess hat seine eigene Kette von Designs, und Exponential nimmt die erste Datei, die es entlang dieser Kette
findet. Wer als Designer die Kette lesen kann, weiß schon vor dem Öffnen des Browsers, welches Template, welches
Stylesheet oder welches Bild eine Seite verwenden wird. In dieser Lektion lernen die Teilnehmenden, die Kette mit einem
einzigen Befehl aus den Einstellungen zu lesen.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Theme-Design](../README.md) |
| Modul | [Modul 1: Designs und die Fallback-Kette](README.md) |
| Dauer | 45 Minuten (20 Präsentation, 25 Übung) |
| Niveau | Einsteiger |
| Zielgruppe | Designer und Frontend-Entwickler |
| Voraussetzungen | [Lektion 1.1](was-ein-design-ist.md); Shell-Zugang zu einer Schulungsinstallation |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die vier Einstellungen nennen, aus denen die Design-Kette eines Siteaccess besteht;
- mit `exp:ini where` lesen, woher eine Einstellung stammt, und mit `exp:ini get` ihren Wert;
- die Stationen einer Suche in die richtige Reihenfolge bringen: Overrides, Website-Design, zusätzliche Designs,
  Standard-Design;
- erklären, wie die Reihenfolge der Erweiterungen zwischen zwei Dateien mit demselben Design-Namen entscheidet.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Designs, Siteaccesses und die Fallback-Kette der Designs](../../../../../eng-us/help/creators/website-creators/designs-and-the-fallback-chain.md)
und [Der Befehl exp:ini](../../../../../eng-us/features/6.0/exp-ini-command.md).

1. **Die Reihenfolge** (6 Minuten). Für ein Template wie `node/view/full.tpl` verwendet Exponential den ersten Treffer
   in:
   1. den Override-Templates, deren Regeln in `override.ini` auf die Seite passen;
   2. `templates/` des **Website-Designs**, `site.ini [DesignSettings] SiteDesign`;
   3. denselben Ordnern jedes **zusätzlichen Designs**, `AdditionalSiteDesignList[]`, in der aufgeführten Reihenfolge;
   4. dem **Standard-Design**, `StandardDesign=standard` in `settings/site.ini`.

   Jeder Schritt sucht in `design/<name>/` und in jeder Design-Erweiterung (`DesignExtensions[]`), die Erweiterungen
   in ihrer Ladereihenfolge.
2. **Lesen, nicht raten** (6 Minuten). Führen Sie am Projektor mit einem Siteaccess Ihrer Installation aus:

   ```bash
   php bin/php/console exp:ini where site/DesignSettings/SiteDesign <site>
   ```

   Die Ausgabe nennt jede Datei, die den Wert setzt, in Ladereihenfolge mit ihrem Bereich und endet mit `In effect:`.
   Weisen Sie darauf hin, dass `settings/site.ini` `SiteDesign=admin` sagt: Die Siteaccess-Datei überschreibt den
   Standardwert.
3. **Der Rest der Kette** (4 Minuten). `exp:ini get site/DesignSettings/AdditionalSiteDesignList <site>` gibt die
   zusätzlichen Designs aus; `exp:ini where design/ExtensionSettings/DesignExtensions <site>` nennt jede Erweiterung,
   die Design-Ordner hinzufügt. `get` gibt nur den Wert aus; `where` nennt jede Datei, die ihn setzt, und genau das
   brauchen Sie bei Arrays, zu denen viele Erweiterungen etwas hinzufügen.
4. **Reihenfolge der Erweiterungen** (4 Minuten). Zwei Erweiterungen können beide
   `design/simple/templates/pagelayout.tpl` mitbringen. Die zuerst geladene gewinnt. Die Reihenfolge ändern Sie unter
   **Setup > Extensions** oder mit `ActiveAccessExtensions[]`
   ([Ladereihenfolge der Erweiterungen](../../../../../eng-us/features/6.0/extension-loading-order.md)); Modul 7 kommt darauf
   zurück.

### Häufige Fragen

**Warum sagt `settings/site.ini` `SiteDesign=admin`?** Das ist der eingebaute Standardwert. Jeder Siteaccess setzt
seinen eigenen in `settings/siteaccess/<site>/site.ini.append.php`.

**Was ist die leere Zeile `AdditionalSiteDesignList[]`?** Ein Zurücksetzen des Arrays: Sie löscht, was frühere Dateien
hinzugefügt haben, sodass die folgende Liste genau die des Siteaccess ist.

**Kann ich das Design für alle Siteaccesses auf einmal setzen?** Ja, in `settings/override/`, aber dann bekommt auch
die Administration Ihr öffentliches Design. Setzen Sie es pro Siteaccess.

### Stolperfallen

- **`get` meldet „Not found“.** `exp:ini get` liest den globalen Bereich und den Siteaccess-Bereich; einen Wert, den
  nur Erweiterungen setzen (wie `DesignExtensions[]`), listet `where` auf. Verwenden Sie im Zweifel `where`.
- **Der Befehl läuft im falschen Verzeichnis.** Er muss im Stammverzeichnis der Installation laufen.
- **Als root.** Auf einem gemeinsamen Schulungsserver kann der Befehl nach `--allow-root-user` verlangen. Besser:
  Führen Sie ihn als der Benutzer aus, dem die Dateien gehören.

## Praktische Übung

Auf Ihrer Schulungsinstallation, im Stammverzeichnis der Installation. Ersetzen Sie `<site>` durch den Namen eines
öffentlichen Siteaccess (ein Ordner in `settings/siteaccess/`, der keine Administration ist). Nichts in dieser Übung
schreibt eine Datei.

1. Listen Sie die Siteaccesses auf: `ls settings/siteaccess/`. Wählen Sie einen öffentlichen Siteaccess und notieren
   Sie seinen Namen.
2. Lesen Sie das Website-Design:

   ```bash
   php bin/php/console exp:ini where site/DesignSettings/SiteDesign <site>
   ```

3. Notieren Sie den Wert nach `In effect:` und die Datei, die ihn gesetzt hat.
4. Lesen Sie die zusätzlichen Designs:

   ```bash
   php bin/php/console exp:ini get site/DesignSettings/AdditionalSiteDesignList <site>
   ```

5. Lesen Sie das Standard-Design: `php bin/php/console exp:ini get site/DesignSettings/StandardDesign <site>`.
6. Listen Sie die Design-Erweiterungen auf:

   ```bash
   php bin/php/console exp:ini where design/ExtensionSettings/DesignExtensions <site>
   ```

7. Schreiben Sie die Kette von oben nach unten auf ein Blatt: Website-Design, zusätzliche Designs, `standard`; und
   neben jeden Design-Namen die Orte, an denen ein Ordner dieses Namens existiert (`design/<name>/` und
   `extension/*/design/<name>/`; verwenden Sie `ls -d design/<name> extension/*/design/<name>`).
8. Wählen Sie das Template `pagelayout.tpl`. Sagen Sie anhand Ihres Blatts vorher, welche Datei der Siteaccess
   verwendet. Prüfen Sie Ihre Vorhersage mit `ls` an jedem Ort der Reihe nach; der erste, der
   `templates/pagelayout.tpl` enthält, ist die Antwort.
9. Wiederholen Sie die Schritte 2 bis 8 für den Siteaccess der Administration (meist `admin`) und vergleichen Sie die
   beiden Ketten.

## Erwartetes Ergebnis

- Das Blatt zeigt zwei verschiedene Ketten: den öffentlichen Siteaccess mit seinem Website-Design (zum Beispiel
  `plain` oder das Design eines Themes) und die Administration mit einem Administrationsdesign und dessen zusätzlichen
  Administrationsdesigns.
- Schritt 8: Alle Teilnehmenden nennen einen konkreten Dateipfad für `pagelayout.tpl` und können sagen, warum die
  Dateien weiter unten in der Kette nicht verwendet werden.
- Der Trainer prüft die Blätter von zwei Teilnehmenden am Projektor und bestätigt eine Vorhersage in Lektion 1.3 mit
  Template-Pfadkommentaren.

## Quiz

1. Bringen Sie in die Reihenfolge der Suche: Standard-Design, zusätzliche Designs, Override-Templates, Website-Design.
2. Welcher Befehl zeigt jede Einstellungsdatei, die `SiteDesign` für einen Siteaccess setzt, und den wirksamen Wert?
3. Richtig oder falsch: `StandardDesign` ist meist `standard`.
4. Zwei aktive Erweiterungen bringen beide `design/simple/templates/pagelayout.tpl` mit. Welche wird verwendet?
   A. Die mit der neueren Datei B. Die zuerst geladene C. Keine; das Standard-Design D. Beide werden zusammengeführt
5. Warum sollte `SiteDesign` für eine öffentliche Website nicht in `settings/override/` gesetzt werden?

<details>
<summary>Antworten</summary>

1. Override-Templates, Website-Design, zusätzliche Designs, Standard-Design
   ([Templates und Design, Teil 1](../../../../anleitungen/templates-und-design.md#1-wie-ein-template-gefunden-wird)).
2. `php bin/php/console exp:ini where site/DesignSettings/SiteDesign <site>`.
3. **Richtig.** `settings/site.ini` setzt `StandardDesign=standard`, den Fallback, den Sie nie bearbeiten.
4. **B.** Erweiterungen werden in ihrer Ladereihenfolge durchsucht.
5. `settings/override/` gilt für jeden Siteaccess, also bekäme auch die Administration das öffentliche Design.

</details>

## Weiterführende Literatur

- [Designs, Siteaccesses und die Fallback-Kette der Designs](../../../../../eng-us/help/creators/website-creators/designs-and-the-fallback-chain.md):
  die Kette lesen und ändern, mit typischen Fehlern.
- [Einstellungen über die Kommandozeile ändern: exp:ini](../../../../../eng-us/features/6.0/exp-ini-command.md): jede Aktion und
  jeder Bereich.
- [Ladereihenfolge der Erweiterungen](../../../../../eng-us/features/6.0/extension-loading-order.md): welche Erweiterung gewinnt.
- [Ladereihenfolge für Themes](../../../../../eng-us/help/designers/extensions/loading-order-for-themes.md): dieselbe Frage aus der Sicht
  eines Theme-Autors.

## Verwandte Seiten

- [Modul 1: Designs und die Fallback-Kette](README.md)
- [Theme-Design: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
