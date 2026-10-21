---
title: Einstellungen in einer Erweiterung
audience: developer, app-creator
category: extension-development
order: 70
summary: Einstellungen, die eine Erweiterung mitbringt - Registrierungszeilen in INI-Dateien des Kernels, Ihre eigene INI-Datei mit dokumentierten Standardwerten, sicheres Lesen mit eZINI, Einstellungen pro Siteaccess und Geheimnisse.
keywords: Einstellungen, INI-Datei, Erweiterungseinstellungen, eigene INI-Datei, Standardwerte, Registrierung, Geheimnisse, settings, ini, extension settings, own ini file, eZINI, variable, hasVariable, defaults, override, ini.append.php, registration, design.ini, module.ini
type: help
---
# Einstellungen in einer Erweiterung

Teil 3, Kapitel 7. Fast jede Erweiterung hat einen Ordner `settings/`. Er erfüllt zwei Aufgaben: Er **registriert** die
Teile der Erweiterung beim Kernel (Module, Designs, Datentypen ...), und er liefert die **eigenen Optionen** der
Erweiterung mit sinnvollen Standardwerten aus. Dieses Kapitel behandelt beides.

## Registrierungseinstellungen

Das sind Zeilen in INI-Dateien des Kernels, die den Kernel auf Ihre Erweiterung verweisen. Ein typischer Satz:

`settings/module.ini.append.php`:

```php
<?php /* #?ini charset="utf-8"?

[ModuleSettings]
ExtensionRepositories[]=mysite_hello

*/ ?>
```

`settings/design.ini.append.php`:

```php
<?php /* #?ini charset="utf-8"?

[ExtensionSettings]
DesignExtensions[]=mysite_hello

*/ ?>
```

`settings/site.ini.append.php` (Template-Operatoren):

```php
<?php /* #?ini charset="utf-8"?

[TemplateSettings]
ExtensionAutoloadPath[]=mysite_hello

*/ ?>
```

Die vollständige Tabelle der Registrierungseinstellungen steht in
[Der Verzeichnisaufbau einer Erweiterung](verzeichnisaufbau-einer-erweiterung.md). Sie werden nur gelesen, solange die
Erweiterung aktiv ist, denn nur das `settings/` einer aktiven Erweiterung ist ein Override-Verzeichnis.

## Ihre eigene Einstellungsdatei

Geben Sie Ihren Optionen eine eigene Datei, die nach der Erweiterung benannt ist,
`settings/mysite_hello.ini.append.php`, und dokumentieren Sie jede Option mit einem sicheren Standardwert:

```php
<?php /* #?ini charset="utf-8"?

[HelloSettings]
# The greeting shown when no name is given.
DefaultName=world
# How many greetings the list view shows at most.
ListLimit=10
# Names that may not be greeted (one per line).
BlockedNames[]

*/ ?>
```

In einem Einstellungsverzeichnis liest der Kernel `<file>`, `<file>.php`, `<file>.append.php` und das veraltete
`<file>.append` (`eZINI` in `lib/ezutils/classes/ezini.php`). Verwenden Sie `<file>.append.php`, wie es jede
ausgelieferte Erweiterung tut; das schützt die Datei davor, an einen Browser ausgeliefert zu werden.

Eine Installation ändert Ihre Optionen, ohne Ihre Erweiterung anzufassen, in ihrem Override- oder
Siteaccess-Verzeichnis:

```bash
./console exp:ini set mysite_hello.ini/HelloSettings/ListLimit 25 global
```

## Einstellungen in PHP lesen

```php
<?php
$ini = eZINI::instance( 'mysite_hello.ini' );

$name  = $ini->variable( 'HelloSettings', 'DefaultName' );
$limit = $ini->hasVariable( 'HelloSettings', 'ListLimit' )
       ? (int)$ini->variable( 'HelloSettings', 'ListLimit' )
       : 10;
$blocked = $ini->variable( 'HelloSettings', 'BlockedNames' );   // an array
```

| Regel | Warum |
|---|---|
| Verwenden Sie bei optionalen Einstellungen `hasVariable()` vor `variable()` | eine fehlende Variable oder ein fehlender Block wird ins Fehlerprotokoll geschrieben („Undefined variable ... in group ...“), und `variable()` gibt `false` zurück |
| Wandeln Sie Zahlen um (`(int)`) | Werte sind Zeichenketten |
| Vergleichen Sie Schalter mit der genauen Zeichenkette (`=== 'enabled'`) | INI kennt keinen booleschen Typ |
| Speichern Sie einen `eZINI`-Wert nie in einer eigenen statischen Eigenschaft zwischen | unter einem persistenten Server (Velocity) überdauert eine statische Variable den Request; `eZINI` verwaltet seinen eigenen Cache |

Die Signatur ist `eZINI::instance( $fileName = 'site.ini', $rootDir = 'settings', ... )`; die Standardwerte sind fast
immer das, was Sie wollen. Die vollständige API steht in
[eZINI: Einstellungen lesen](../teil-8-referenz/ezini.md).

## Einstellungen in Templates lesen

```text
{ezini( 'HelloSettings', 'DefaultName', 'mysite_hello.ini' )|wash}
```

## Einstellungen pro Siteaccess in einer Erweiterung

Eine Erweiterung kann Einstellungen für einen Siteaccess in `settings/siteaccess/<sa>/` und Gruppeneinstellungen in
`settings/override__<group>/` mitbringen. Siehe
[Geltungsbereiche von Einstellungen und ihre Rangfolge](../teil-2-builder/einstellungsebenen-und-ihre-reihenfolge.md).

## Geheimnisse

Eine Erweiterung, die einen API-Schlüssel braucht, liefert die Einstellung **leer** mit einem Kommentar aus, und die
Installation füllt sie in `settings/override/`. Benennen Sie sie so, dass sie maskiert wird (`...Key`, `Token`,
`Secret`, `Password`). Committen Sie nie einen echten Schlüssel; der Paket-Download weigert sich, eine Erweiterung zu
packen, die einen enthält ([die Prüfung auf Geheimnisse](../../../bc/6.0/geheimnispruefung-fuer-erweiterungspakete.md)).

## Einstellungen, die die RAD-Werkzeuge schreiben

Der **Assistent für Einstellungs-Extensions** (`/setup/settingsextension`) schreibt eine Erweiterung, die nur aus Einstellungen
besteht, einschließlich Event-Listenern und Anpassungspunkten für Siteaccesses; `exp:ini move-all ...
--create-extension` verschiebt vorhandene Einstellungen in eine solche Erweiterung. Siehe
[Mit RAD: eine Erweiterung nur mit Einstellungen](tutorials/rad-einstellungs-erweiterung.md).

## Verwandte Seiten

- Zurück: [composer.json für eine Erweiterung](composer-json-fuer-erweiterungen.md)
- Weiter: [Autoloads und Klassenbenennung](autoloads-und-klassennamen.md)
- [Wie INI-Einstellungen funktionieren](../teil-2-builder/wie-ini-einstellungen-funktionieren.md)
- [Umgebungseinstellungen](../../../../eng-us/features/6.0/environment-settings.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
