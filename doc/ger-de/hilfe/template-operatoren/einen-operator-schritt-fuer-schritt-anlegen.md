---
title: Einen Operator Schritt für Schritt anlegen
audience: developer, app-creator
category: development
order: 520
summary: Der ganze Weg von einer Idee zu einem funktionierenden Template-Operator in einer Erweiterung - einen freien Namen wählen, die Klasse schreiben, sie registrieren, Caches leeren, sie interpretiert und kompiliert ausprobieren, ausliefern - oder den Assistenten für Template-Erweiterungen die Arbeit tun lassen; jeder Schritt auf alpha geprüft.
keywords: Operator anlegen, neuer Template-Operator, Erweiterung, Operatorklasse, Assistent, Template-Operator-Assistent, kompiliert, Test, eztemplateautoload.php, ExtensionAutoloadPath, setup/templateoperator, RAD, create operator, new template operator, template extension wizard, compiled
type: help
---
# Einen Operator Schritt für Schritt anlegen

Diese Seite geht das Hinzufügen eines Template-Operators zu einer Erweiterung in der Reihenfolge durch, in der Sie es
tun, mit den Prüfungen, die die üblichen Fehler aufdecken. Der Code einer vollständigen kleinen Operatorklasse steht
im Kochbuch unter [Ein eigener Template-Operator](../../../eng-us/examples/cookbook/custom-template-operator.md); jede Methode und
jedes Argument erklärt [Die Schnittstelle der Operatorklasse](referenz-der-operatorklassen.md). Hier geht es um den Ablauf.

## Inhalt

1. [Entscheiden, ob es ein Operator sein soll](#1-entscheiden-ob-es-ein-operator-sein-soll)
2. [Einen freien Namen wählen](#2-einen-freien-namen-wählen)
3. [Den Assistenten die Dateien schreiben lassen oder sie selbst schreiben](#3-den-assistenten-die-dateien-schreiben-lassen-oder-sie-selbst-schreiben)
4. [Die Klasse schreiben](#4-die-klasse-schreiben)
5. [Ihn registrieren](#5-ihn-registrieren)
6. [Ihn bekannt machen](#6-ihn-bekannt-machen)
7. [Ihn ausprobieren, interpretiert und kompiliert](#7-ihn-ausprobieren-interpretiert-und-kompiliert)
8. [Ihn ausliefern](#8-ihn-ausliefern)

## 1. Entscheiden, ob es ein Operator sein soll

Ein Operator nimmt einen Wert und gibt einen zurück: `{$value|my_operator( 3 )}`. Verwenden Sie etwas anderes, wenn:

| Sie möchten | Verwenden Sie |
|---|---|
| Daten lesen (Inhalte, Benutzer, Ihre eigenen Tabellen) | eine [Fetch-Funktion](wie-fetch-funktionen-arbeiten.md) oder einen [Fetch-Alias](fetch-aliase.md) |
| Ausgabe mit einem Rumpf schreiben oder steuern, was ausgegeben wird | eine [Template-Funktion](../template-sprache/eigene-template-funktionen.md) |
| eine PHP-Funktion mit einem Argument aufrufen | `template.ini` `[PHP] PHPOperatorList[]` ([PHP-Funktionsoperatoren](php-funktionsoperatoren.md)) |
| vorhandene Operatoren kombinieren | ein Template-Include oder ein `set-block` |

Ein Operator darf keine Daten ändern: Er läuft, während Templates kompiliert und ausgegeben werden, seine Ergebnisse
werden mit der Seite gecacht, und er läuft für jeden Besucher, der das Template sehen kann.

## 2. Einen freien Namen wählen

Operatornamen gelten global je Siteaccess, und ein Name, den auch eine andere Klasse registriert, führt zu den
Problemen, die [Registrierungsreihenfolge und Überschreibungen](registrierungsreihenfolge-und-ueberschreibungen.md) beschreibt.
Verwenden Sie das Präfix Ihrer Erweiterung (`myext_initials` oder `exp_` im eigenen Code von Exponential) und prüfen
Sie, dass der Name in jedem Siteaccess frei ist, der ihn verwenden wird: Schlagen Sie ihn im
[Operatorenverzeichnis](operatorenverzeichnis.md) nach, und geben Sie `{'x'|myext_initials}` aus, bevor Sie irgendetwas
registrieren. Ein nicht registrierter Name lässt das Tag leer und protokolliert
`Operator 'myext_initials' is not registered`; erhalten Sie ein anderes Ergebnis, ist der Name vergeben.

## 3. Den Assistenten die Dateien schreiben lassen oder sie selbst schreiben

Der Assistent für Template-Erweiterungen in der Administration (Setup, RAD, „Template-Operator“; Modulansicht
`setup/templateoperator`, Code in `kernel/setup/exptemplateextensionwizard.php`) schreibt eine funktionierende
Erweiterung mit einer beliebigen Mischung aus Operatoren, Template-Funktionen, Fetch-Funktionen und Fetch-Aliasen: die
Operatorklasse mit ihrer Parameterliste, Hinweisen für die Kompilierung und einem `modify()`, das nach dem
Operatornamen verzweigt, `autoloads/eztemplateautoload.php`, `settings/site.ini.append.php`, `ezinfo.php`,
`extension.xml` und `composer.json`. Siehe [RAD-Werkzeuge](../../../eng-us/features/6.0/rad-tools.md). Der Rest dieser Seite
zeigt, was diese Dateien enthalten und warum, für den Fall, dass Sie sie von Hand schreiben oder prüfen, was der
Assistent geschrieben hat.

## 4. Die Klasse schreiben

Legen Sie die Klasse in Ihre Erweiterung, zum Beispiel `extension/myextension/autoloads/myextensionoperators.php`.
Das Minimum sind `operatorList()`, `namedParameterPerOperator()`, `namedParameterList()` und `modify()`. Eine Klasse mit
zwei Operatoren, `exp_initials` und `exp_reading_time( words_per_minute )`, arbeitet auf alpha so:

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 89 ) )
     $text = 'one two three four five six seven eight nine ten eleven twelve'}
{$node.name|wash} -> {$node.name|exp_initials}
{$text|exp_reading_time} minute(s) at 200 words a minute, {$text|exp_reading_time( 5 )} at 5
{exp_initials( 'Bold Agency' )}
```

Ausgabe auf alpha:

```text
Fit &amp; Healthy -> FH
1 minute(s) at 200 words a minute, 3 at 5
```

(Nicht als installierte Erweiterung ausgeführt: Das Skript, das das Beispiel ausgab, registrierte die Klasse im
Speicher bei der Engine, aus einem Arbeitsordner; siehe
[Die Schnittstelle der Operatorklasse](referenz-der-operatorklassen.md#die-beispielklasse).)

Prüfungen beim Schreiben:

- Prüfen Sie die Datei mit `php -l`.
- Weisen Sie das Ergebnis `$operatorValue` zu; geben Sie Text unmaskiert zurück (das Template maskiert ihn: Beachten
  Sie, dass `{$node.name|wash}` oben `&amp;` ausgibt, während `exp_initials` mit dem rohen Namen arbeitet).
- Geben Sie jedem optionalen Parameter einen `default`, und prüfen Sie Pflichtparameter in `modify()` mit einem
  `$tpl->warning( $operatorName, ..., $placement )`.
- Lassen Sie `operatorTemplateHints()` in einer ersten Version weg. Fügen Sie Hinweise später nur mit einem Test des
  kompilierten Ergebnisses hinzu ([warum](referenz-der-operatorklassen.md#operatortemplatehints)).

## 5. Ihn registrieren

`extension/myextension/autoloads/eztemplateautoload.php`:

```php
<?php
$eZTemplateOperatorArray = array();
$eZTemplateOperatorArray[] = array( 'script' => 'extension/myextension/autoloads/myextensionoperators.php',
                                    'class' => 'myExtensionOperators',
                                    'operator_names' => array( 'exp_initials', 'exp_reading_time' ) );
```

`extension/myextension/settings/site.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?

[TemplateSettings]
ExtensionAutoloadPath[]=myextension

*/ ?>
```

und die Erweiterung in `[ExtensionSettings] ActiveExtensions[]` (oder in `ActiveAccessExtensions[]` eines
Siteaccess). Die Einzelheiten, einschließlich der Registrierung über `template.ini` und Zugriffserweiterungen, stehen
unter [Operatoren registrieren](operatoren-registrieren.md).

## 6. Ihn bekannt machen

Der INI-Cache enthält die Liste der Autoload-Ordner, und kompilierte Templates enthalten das Ergebnis der Kompilierung
gegen die alte Operatorliste:

```bash
php bin/php/ezpgenerateautoloads.php -e        # only if other classes of the extension changed
php bin/php/ezcache.php --clear-tag=ini,template --allow-root-user
```

Auf alpha, wo Velocity die Registrierungen der Template-Engine aus seinem Warm-up behält, verwenden Sie den einen
Deploy-Befehl, der diese Caches leert, PHP-FPM neu lädt und Velocity neu startet:

```bash
./console exp:velocity deploy --allow-root-user
```

## 7. Ihn ausprobieren, interpretiert und kompiliert

Alpha kompiliert Templates (`[TemplateSettings] TemplateCompile=enabled`), und das kompilierte Template ist das, was
Besucher erhalten. Probieren Sie den Operator auf beide Arten aus:

- Schalten Sie in einer Entwicklungsinstallation `TemplateCompile` in einem Siteaccess-Override aus, laden Sie die
  Seite neu, schalten Sie es wieder ein, leeren Sie den Template-Cache und laden Sie neu: Beide Ergebnisse müssen gleich
  sein;
- verwenden Sie jede Parameterform: mit der Pipe und als Aufruf (`{exp_initials( 'Bold Agency' )}`), mit und ohne
  optionale Parameter, mit einem fehlenden Pflichtparameter (erwarten Sie eine Warnung, keinen PHP-Fehler);
- suchen Sie in der Debug-Ausgabe nach `Operator '...' is not registered`, `Object problem with operator '...'` (die
  Klasse wurde nicht gefunden oder hat kein `modify()`) und `Parameter '...' missing`.

Ein weggelassener Pflichtparameter, auf alpha:

```tpl
{150|exp_clamp( 0, 100 )} {-5|exp_clamp( 0, 100 )} {42|exp_clamp( 0, 100 )}
{42|exp_clamp( 0 )}
```

Ausgabe auf alpha:

```text
100 0 42
42
```

Von der Template-Engine protokolliert:

```text
WARNING eZTemplateOperatorElement: Parameter 'max' (1) missing
WARNING exp_clamp: min and max must be numbers
```

## 8. Ihn ausliefern

- Halten Sie die Klasse des Operators klein und frei von Nebenwirkungen; legen Sie gemeinsam genutzte Logik in eine
  eigene Klasse, die ein Unit-Test ohne Template aufrufen kann.
- Dokumentieren Sie jeden Operator: Name, Eingabe, Parameter, Rückgabewert, ein Beispiel. Die Seiten zu den
  Operatorfamilien in diesem Abschnitt sind das Vorbild.
- Führen Sie die Operatoren im README der Erweiterung mit dem gewählten Präfix auf, damit andere die Namen meiden können.
- Prüfen Sie bei einem Upgrade von Exponential erneut, dass kein neuer Kernel-Operator einen Ihrer Namen übernommen hat
  ([Registrierungsreihenfolge und Überschreibungen](registrierungsreihenfolge-und-ueberschreibungen.md#regeln)).

## Verwandte Seiten

- [Die Schnittstelle der Operatorklasse](referenz-der-operatorklassen.md)
- [Operatoren registrieren](operatoren-registrieren.md)
- [Ein eigener Template-Operator](../../../eng-us/examples/cookbook/custom-template-operator.md) (Kochbuch)
- [Eigene Operatoren mit OW Simple Operator](eigene-operatoren-mit-owsimpleoperator.md): die Abkürzung und ihre Grenzen
- [Kernel-Operatoren überschreiben](kernel-operatoren-ueberschreiben.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
