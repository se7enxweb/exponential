---
title: "Lektion 7.5: Ein Template-Operator"
audience: trainer, developer
category: training
order: 75
summary: Die Teilnehmenden schreiben den Operator mysite_initials mit einem optionalen Parameter, registrieren ihn in autoloads/eztemplateautoload.php und ExtensionAutoloadPath und verwenden ihn in einem Template. 55 Minuten.
keywords: entwickler, template-operator, eigener operator, eztemplateautoload.php, eZTemplateOperatorArray, operatorList, namedParameterPerOperator, namedParameterList, modify, operatorValue, ExtensionAutoloadPath, TemplateSettings, RAD-assistent für template-erweiterungen, developers course, template operator, RAD template extension wizard
type: help
---
# Lektion 7.5: Ein Template-Operator

Ein Template-Operator ist die kleinste nützliche Erweiterung: ein neues Wort für Templates, etwa `|mysite_initials`.
Er ist auch das beste erste Stück PHP für Template-Entwickler, weil Eingabe und Ausgabe einfache Werte sind. In
dieser Lektion schreiben, registrieren und verwenden die Teilnehmenden einen Operator mit einem optionalen Parameter
und vergleichen ihre von Hand geschriebene Fassung mit dem RAD-Assistenten.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 7: Erweiterungen](README.md) |
| Dauer | 55 Minuten (15 Präsentation, 40 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Entwickler |
| Voraussetzungen | [Lektion 7.3](autoloads-und-aktivierung.md); [Lektion 2.4](../modul-02-die-template-sprache/operatoren-wash-und-uebersetzungen.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- eine Operatorklasse mit `operatorList()`, `namedParameterPerOperator()`, `namedParameterList()` und `modify()`
  schreiben;
- jedem optionalen Parameter einen Standardwert geben;
- den Operator in `autoloads/eztemplateautoload.php` und `[TemplateSettings] ExtensionAutoloadPath[]` registrieren;
- den Operator in der Pipe-Form und mit einem Parameter verwenden;
- den RAD-Assistenten finden, der Operatoren schreibt.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: eine echte Operator-Erweiterung, `extension/str_replace/` (falls installiert), die Warnung zu
Template-Operatoren in [Templates und Design, Teil 7](../../../../anleitungen/templates-und-design.md#ein-operator-parameter-ohne-standardwert)
und [RAD-Werkzeuge](../../../../../eng-us/features/6.0/rad-tools.md).

1. **Die Registrierung** (3 Minuten). `autoloads/eztemplateautoload.php` füllt `$eZTemplateOperatorArray[]` mit
   `script`, `class` und `operator_names`. Die `settings/site.ini.append.php` der Erweiterung fügt
   `[TemplateSettings] ExtensionAutoloadPath[]=<extension>` hinzu, damit der Kernel diese Datei liest.
2. **Die Klasse** (6 Minuten). Gehen Sie die Klasse der Übung durch: `operatorList()` nennt die Operatoren;
   `namedParameterPerOperator()` gibt `true` zurück; `namedParameterList()` listet jeden Parameter mit `type`,
   `required` und `default` auf; `modify()` erhält den Wert links von der Pipe in `$operatorValue` (als Referenz) und
   die Parameter in `$namedParameters` und ersetzt `$operatorValue` durch das Ergebnis.
3. **Standardwerte** (2 Minuten). Ein Parameter ohne Standardwert, den ein Template weglässt, kommt als `null` an,
   und die Debug-Ausgabe warnt "Operator '...' has no default value for parameter '...'".
4. **Escaping** (2 Minuten). Ein Operator gibt einen Wert zurück; das Template wäscht ihn, wenn es ihn ausgibt.
   Geben Sie aus einem Operator kein HTML zurück, es sei denn, das ist seine Aufgabe, und escapen Sie dann darin.
5. **Der Assistent** (2 Minuten). **Setup** > **RAD**; der Assistent für Template-Erweiterungen
   (`/setup/templateoperator`) schreibt Operatoren, Fetch-Funktionen und Template-Funktionen als vollständige
   Erweiterung.

### Häufige Fragen

**Lässt er sich in Funktionsform aufrufen?** Dieser Operator liest den Wert links von der Pipe, schreiben Sie also
`{$name|mysite_initials}`. Ein Operator, der auch als `{my_operator( $value )}` funktionieren soll, nimmt den Wert
zusätzlich als Parameter an und verwendet ihn, wenn er angegeben ist, wie es der Parameter `subject` von
`str_replace` tut.

**Kann eine Klasse mehrere Operatoren enthalten?** Ja: Listen Sie sie in `operatorList()` und `operator_names` auf
und unterscheiden Sie in `modify()` nach `$operatorName`.

**Wird ein Operator in den Template-Cache kompiliert?** Sein Aufruf schon; der Operator selbst läuft jedes Mal, wenn
das kompilierte Template läuft, es sei denn, er deklariert Hinweise für die Auswertung beim Kompilieren. Halten Sie
Operatoren schnell.

### Stolperfallen

- **`ExtensionAutoloadPath[]` vergessen.** Der Operator ist unbekannt, und die Debug-Ausgabe sagt es.
- **Caches nicht leeren.** Leeren Sie nach dem Registrieren die Caches `ini` und `template`.
- **Präfix.** Operatornamen gelten global für alle Templates; versehen Sie sie mit einem Präfix (`mysite_`), um
  Konflikte zu vermeiden.

## Praktische Übung

Auf der Entwicklungsinstallation, in `extension/mysite_hello`.

1. Legen Sie `autoloads/mysitehellooperators.php` an:

   ```php
   <?php
   /**
    * Template operators of mysite_hello: {$text|mysite_initials} and {$text|mysite_initials( '-' )}
    */
   class mysiteHelloOperators
   {
       public function operatorList()
       {
           return array( 'mysite_initials' );
       }

       public function namedParameterPerOperator()
       {
           return true;
       }

       public function namedParameterList()
       {
           return array( 'mysite_initials' => array( 'separator' => array( 'type' => 'string',
                                                                            'required' => false,
                                                                            'default' => '' ) ) );
       }

       public function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters )
       {
           if ( $operatorName === 'mysite_initials' )
           {
               $initials = array();
               foreach ( preg_split( '/\s+/u', trim( (string)$operatorValue ) ) as $word )
               {
                   if ( $word !== '' )
                       $initials[] = mb_strtoupper( mb_substr( $word, 0, 1 ) );
               }
               $operatorValue = implode( $namedParameters['separator'], $initials );
           }
       }
   }
   ```

2. Legen Sie `autoloads/eztemplateautoload.php` an:

   ```php
   <?php
   $eZTemplateOperatorArray = array();
   $eZTemplateOperatorArray[] = array( 'script' => 'extension/mysite_hello/autoloads/mysitehellooperators.php',
                                       'class' => 'mysiteHelloOperators',
                                       'operator_names' => array( 'mysite_initials' ) );
   ```

3. Registrieren Sie den Autoload-Pfad:
   `php bin/php/console exp:ini add site.ini/TemplateSettings/ExtensionAutoloadPath mysite_hello extension:mysite_hello`.
4. Prüfen Sie beide PHP-Dateien mit `php -l`; leeren Sie die Caches `ini` und `template`.
5. Verwenden Sie den Operator im Spielplatz:

   ```
   [{'Ada Lovelace'|mysite_initials}] [{'grace brewster hopper'|mysite_initials( '.' )}] [{$node.name|mysite_initials|wash}]
   ```

6. Entfernen Sie `'default' => ''` aus der Parameterdefinition, leeren Sie den Template-Cache, führen Sie den
   Spielplatz mit `--debug=warning` aus und lesen Sie die Warnung. Setzen Sie den Standardwert wieder ein.
7. Verwenden Sie den Operator in `greet.tpl` aus Lektion 6.2: `<p>Initials: {$name|mysite_initials|wash}</p>`, und
   öffnen Sie die View.
8. Öffnen Sie **Setup** > **RAD** und suchen Sie den Assistenten für Template-Erweiterungen; vergleichen Sie die
   Dateien, die er auflistet, mit Ihren.
9. Committen Sie die Dateien.

## Erwartetes Ergebnis

- Schritt 5 gibt `[AL] [G.B.H]` und die Initialen des Namens von Knoten 2 aus.
- Schritt 6: die Warnung "Operator 'mysite_initials' has no default value for parameter 'separator'", gefolgt von
  `(compiling <template>)`, weil das Template neu kompiliert wurde.
- Schritt 7: Die Begrüßungsseite zeigt die Initialen.
- Der Trainer prüft die Spielplatz-Ausgabe einer Person.

## Quiz

1. Welche Datei registriert die Template-Operatoren einer Erweiterung?
2. Welche Einstellung lässt den Kernel diese Datei lesen?
   A. `ActiveExtensions[]` B. `ExtensionAutoloadPath[]` C. `DesignExtensions[]` D. `ExtensionRepositories[]`
3. Wo steht in `modify()` der Wert links von der Pipe, und wie wird das Ergebnis zurückgegeben?
4. Was passiert, wenn ein Parameter keinen Standardwert hat und ein Template ihn weglässt?
5. Warum erhalten Operatornamen ein Präfix?

<details>
<summary>Antworten</summary>

1. `autoloads/eztemplateautoload.php` der Erweiterung.
2. **B.** `[TemplateSettings] ExtensionAutoloadPath[]`.
3. In `$operatorValue`, als Referenz übergeben; die Methode ersetzt den Wert durch das Ergebnis.
4. Der Operator erhält `null`, und die Debug-Ausgabe warnt einmal pro Operator und Parameter.
5. Alle Templates der Installation teilen sich die Operatornamen; ein Präfix vermeidet Konflikte.

</details>

## Weiterführende Literatur

- [Template-Operatoren in einem Theme](../../../../../eng-us/help/designers/extensions/template-operators-in-a-theme.md).
- [Templates und Design, Teil 7](../../../../anleitungen/templates-und-design.md#7-templates-debuggen): die Warnung bei
  fehlenden Standardwerten.
- [RAD-Werkzeuge](../../../../../eng-us/features/6.0/rad-tools.md): der Assistent für Template-Erweiterungen.

## Verwandte Seiten

- [Modul 7: Erweiterungen](README.md)
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
