---
title: "Lektion 8.4: Eigene Einstellungen"
audience: trainer, developer
category: training
order: 84
summary: Die Teilnehmenden geben ihrer Erweiterung eine eigene INI-Datei mit dokumentierten Standardwerten, lesen sie in PHP mit eZINI und in Templates mit ezini, lassen einen Siteaccess sie überschreiben und gehen sicher mit fehlenden Einstellungen um. 45 Minuten.
keywords: entwickler, eigene ini-datei, eigene einstellungen, mysite.ini, eZINI instance, variable, hasVariable, hasGroup, variableArray, ezini-operator, ezini_hasvariable, standardwerte, einstellungen einer erweiterung, siteaccess-override, developers course, own ini file, defaults, extension settings, siteaccess override
type: help
---
# Lektion 8.4: Eigene Einstellungen

Code, der eine Zahl oder eine Knoten-ID fest einprogrammiert, muss für jede Website bearbeitet werden. Eine gute
Erweiterung liest solche Werte aus einer eigenen INI-Datei mit dokumentierten Standardwerten, die eine Website
überschreiben kann. In dieser Lektion fügen die Teilnehmenden ihrer Erweiterung `mysite.ini` hinzu, lesen sie aus PHP
und aus Templates und überschreiben einen Wert für einen Siteaccess.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 8: Einstellungen und Siteaccesses](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Entwickler |
| Voraussetzungen | [Lektion 8.3](einstellungen-mit-exp-ini-aendern.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- in einer Erweiterung eine eigene INI-Datei mit einem Kommentar zu jeder Einstellung anlegen;
- sie in PHP mit `eZINI::instance()`, `variable()`, `hasVariable()` und `variableArray()` lesen;
- sie in Templates mit `ezini()` und `ezini_hasvariable()` lesen;
- einen Wert für einen Siteaccess überschreiben und das Ergebnis mit `exp:ini where` prüfen;
- auf einen sicheren Standardwert zurückfallen, wenn eine Einstellung fehlt.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Eine eigene INI-Datei](../../../designer/erweiterungen/eine-eigene-ini-datei.md) und
[Einstellungsdateien in einer Erweiterung](../../../designer/erweiterungen/einstellungsdateien-in-einer-erweiterung.md).

1. **Die Datei** (3 Minuten). Eine Erweiterung kann unter `settings/` eine neue INI-Datei mitbringen, hier
   `extension/mysite_hello/settings/mysite.ini.append.php`. Jede Einstellung erhält einen Kommentar, der sagt, was
   sie tut und welche Werte erlaubt sind: Die Datei ist die Dokumentation.
2. **PHP** (4 Minuten). `$ini = eZINI::instance( 'mysite.ini' );`, dann `$ini->variable( 'GreetSettings',
   'MaxTimes' )`. Prüfen Sie vorher mit `hasVariable()`, oder verwenden Sie im Code einen Standardwert.
   `variableArray()` zerlegt Werte, die mit `;` getrennte Listen enthalten.
3. **Templates** (2 Minuten). `{ezini( 'GreetSettings', 'MaxTimes', 'mysite.ini' )}`; `ezini_hasvariable()` prüft
   vorher.
4. **Overrides** (3 Minuten). Eine Website ändert einen Wert mit `exp:ini set mysite.ini/GreetSettings/MaxTimes 3
   siteaccess:<sa>`; die Datei der Erweiterung wird nie für eine einzelne Website bearbeitet.
5. **Präfixe** (2 Minuten). Alle Erweiterungen teilen sich die Namen der INI-Dateien; benennen Sie Ihre nach Ihrer
   Erweiterung oder Ihrem Präfix (`mysite.ini`), nicht `settings.ini`.

### Häufige Fragen

**Kann ich stattdessen einen Block zu `site.ini` hinzufügen?** Das geht, aber eine eigene Datei hält Ihre
Einstellungen zusammen und kann nicht mit einem Kernel-Block kollidieren.

**Kennen Einstellungsdateien Typen?** Werte sind Strings; `enabled`/`disabled` und `true`/`false` werden nach
Konvention als Text verglichen. Wandeln Sie Zahlen mit `(int)` um.

**Gehören Geheimnisse in die Einstellungen einer Erweiterung?** Nein: Geheimnisse gehören in die eigenen
Override-Dateien der Installation, niemals in ein Repository.

### Stolperfallen

- **Lesen im Konstruktor einer Klasse, die beim Warm-up läuft.** Unter Velocity bleibt ein Wert, der einmal beim
  Warm-up gelesen wurde, bis zum Neustart bestehen. Lesen Sie Einstellungen erst, wenn sie gebraucht werden.
- **Keine Standardwerte.** Eine fehlende Einstellung gibt `false` oder einen leeren Wert zurück und schreibt eine
  Warnung; geben Sie jeder Einstellung in der Datei der Erweiterung einen Standardwert.
- **Templates, die Einstellungen ausgeben.** Geben Sie niemals Einstellungen aus, die Geheimnisse enthalten könnten.

## Praktische Übung

Auf der Entwicklungsinstallation, in `extension/mysite_hello`.

1. Legen Sie `settings/mysite.ini.append.php` an:

   ```ini
   <?php /* #?ini charset="utf-8"?

   [GreetSettings]
   # The highest number of greetings /hello/greet/<name>/(times)/<n> shows.
   # A whole number from 1 to 20.
   MaxTimes=5
   # Text in front of the name.
   Greeting=Hello

   */ ?>
   ```

2. Lesen Sie sie: `php bin/php/console exp:ini where mysite.ini/GreetSettings/MaxTimes <sa>`.
3. Verwenden Sie sie in `modules/hello/greet.php`: Ersetzen Sie die feste `5` durch

   ```php
   $ini = eZINI::instance( 'mysite.ini' );
   $maxTimes = $ini->hasVariable( 'GreetSettings', 'MaxTimes' ) ? (int)$ini->variable( 'GreetSettings', 'MaxTimes' ) : 5;
   $times = isset( $Params['UserParameters']['times'] ) ? max( 1, min( $maxTimes, (int)$Params['UserParameters']['times'] ) ) : 1;
   ```

4. Verwenden Sie den Begrüßungstext in `greet.tpl`:
   `{ezini( 'GreetSettings', 'Greeting', 'mysite.ini' )|wash}, {$name|wash}!`.
5. Prüfen Sie mit `php -l`, leeren Sie die Caches `ini` und Template, und öffnen Sie `/hello/greet/Ada/(times)/9`.
6. Überschreiben Sie den Wert nur für Ihre Website:
   `php bin/php/console exp:ini set mysite.ini/GreetSettings/MaxTimes 2 siteaccess:<sa>`, und öffnen Sie die Adresse
   erneut.
7. Führen Sie `exp:ini where` aus Schritt 2 erneut aus und lesen Sie beide Dateien.
8. Entfernen Sie den Override mit `exp:ini rem mysite.ini/GreetSettings/MaxTimes siteaccess:<sa>` und committen Sie
   die Dateien der Erweiterung.

## Erwartetes Ergebnis

- Schritt 2: die Datei der Erweiterung mit `MaxTimes=5` und `In effect:`.
- Schritt 5: fünf Begrüßungen (das Maximum), jede beginnt mit „Hello“.
- Schritt 6: zwei Begrüßungen.
- Schritt 7: zwei Dateien in Ladereihenfolge, der Wert des Siteaccess ist wirksam.
- Der Trainer prüft die `mysite.ini.append.php` einer Person auf Kommentare zu jeder Einstellung.

## Quiz

1. Wo bewahrt eine Erweiterung eine eigene INI-Datei auf?
2. Welche Methode prüft, ob eine Einstellung existiert, bevor sie gelesen wird?
   A. `variable()` B. `hasVariable()` C. `instance()` D. `variableArray()`
3. Wie liest ein Template `MaxTimes` aus `mysite.ini`?
4. Eine Website braucht einen anderen Wert. Welche Datei ändert sich?
5. Warum heißt die Datei `mysite.ini` und nicht `settings.ini`?

<details>
<summary>Antworten</summary>

1. In ihrem Ordner `settings/`, als `<name>.ini.append.php`.
2. **B.** `hasVariable( 'GreetSettings', 'MaxTimes' )`.
3. `{ezini( 'GreetSettings', 'MaxTimes', 'mysite.ini' )}`.
4. Ein Override im Geltungsbereich der Website (ein Siteaccess oder der globale Override), niemals die Datei der
   Erweiterung.
5. Alle Erweiterungen teilen sich die Namen der INI-Dateien; ein Name mit Präfix kann nicht kollidieren.

</details>

## Weiterführende Literatur

- [Eine eigene INI-Datei](../../../designer/erweiterungen/eine-eigene-ini-datei.md).
- [Einstellungsdateien in einer Erweiterung](../../../designer/erweiterungen/einstellungsdateien-in-einer-erweiterung.md).
- [Siteaccess-Einstellungen in einer Erweiterung](../../../designer/erweiterungen/siteaccess-einstellungen-in-einem-theme.md).

## Verwandte Seiten

- [Modul 8: Einstellungen und Siteaccesses](README.md)
- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
