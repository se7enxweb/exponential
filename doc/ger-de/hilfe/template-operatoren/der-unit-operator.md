---
title: Der Einheitenoperator si
audience: developer, designer, app-creator
category: development
order: 240
summary: si aus eZTemplateUnitOperator - eine Zahl mit einer Einheit und einem dezimalen oder binären Präfix formatieren (Dateigrößen, Längen, Gewichte) anhand von units.ini, mit jedem Parameter, der Regel für das Präfix auto und Beispielen, die auf alpha gerendert wurden.
keywords: si, Einheit, Dateigröße, Bytes, Präfix, Binärpräfix, unit, file size, bytes, kB, MB, prefix, kibi, units.ini, UnitSettings, BinaryUnits, UseSIUnits, eZTemplateUnitOperator
type: help
---
# Der Einheitenoperator si

`si` schreibt eine Zahl mit einer Einheit und einem Präfix: `5242880` Bytes als `5.00 MB`, `1500` Meter als `1.50 km`.
Er wird von `eZTemplateUnitOperator` (`lib/eztemplate/classes/eztemplateunitoperator.php`) über
`lib/eztemplate/classes/eztemplateautoload.php` registriert. Einheiten und Präfixe kommen aus `settings/units.ini`, die
Wahl zwischen binären und dezimalen Präfixen aus `[UnitSettings]` in `site.ini`.

## Inhalt

1. [Parameter](#parameter)
2. [Einheiten und Präfixe](#einheiten-und-präfixe)
3. [Beispiele](#beispiele)
4. [Verwandte Seiten](#verwandte-seiten)

## Parameter

### si

| Parameter | Typ | Erforderlich | Standardwert |
|---|---|---|---|
| `unit` | Zeichenkette, ein Schlüssel von `[Base]` in `units.ini` | ja | `false` |
| `prefix` | `auto`, `decimal`, `binary`, `none` oder ein Präfixname wie `kilo`, `mebi` | nein | `auto` |
| `decimal_count` | Ganzzahl | nein | die Anzahl der Nachkommastellen des Locales (0 bei `none`) |
| `decimal_symbol` | Zeichenkette | nein | das Dezimalzeichen des Locales |
| `thousands_separator` | Zeichenkette | nein | das Tausendertrennzeichen des Locales |

Eingabe: die Zahl, über die Pipe. Gibt eine Zeichenkette zurück: die Zahl, ein Leerzeichen, das Präfixsymbol und das
Einheitensymbol. Eine unbekannte Einheit protokolliert "No such unit" und lässt die Zahl unverändert; ein unbekanntes
Präfix protokolliert "Prefix ... not found". Generierte Referenz: [si](../../../eng-us/examples/template-operators/si.md).

## Einheiten und Präfixe

`[Base]` in `units.ini` ordnet Einheitennamen Symbole zu: `meter` m, `gram` g, `second` s, `ampere` A, `kelvin` K,
`mole` mol, `candela` cd, `byte` B und `bit` b.

Das Präfix `auto` wählt `binary` für die Einheiten in `[UnitSettings] BinaryUnits` (`byte;bit` auf alpha) und
`decimal` für alle anderen. Danach gilt:

- **decimal** verwendet `[DecimalPrefixes]` (Zehnerpotenzen von yocto bis yotta, **einschließlich hecto und deca**) und
  nimmt das größte Präfix, das nicht größer als die Zahl ist. Deshalb ergibt `150|si( 'meter' )` den Wert `1.50 hm`:
  Nennen Sie ein Präfix, wenn Sie ein vertrautes wollen.
- **binary** verwendet Zweierpotenzen. Mit `[UnitSettings] UseSIUnits=false`, wie auf alpha, verwendet es
  `[FakeBinaryPrefixes]`, die durch 1024 teilen, aber die vertrauten `k`, `M`, `G` ausgeben; mit `true` verwendet es
  `[BinaryPrefixes]` und gibt `Ki`, `Mi`, `Gi` aus.
- Zahlen, deren Betrag kleiner als 10 ist, erhalten kein Präfix und werden so ausgegeben, wie sie sind.
- Ein benanntes Präfix (`kilo`, `mebi`...) wird zuerst in `[BinaryPrefixes]` gesucht, dann in `[DecimalPrefixes]`;
  `none` gibt die ganze Zahl mit Trennzeichen aus.

Negative Zahlen erhalten das Präfix ihres Betrags (`-2000` Sekunden ergeben `-2.00 ks`).

## Beispiele

Dateigrößen:

```tpl
5|si( 'byte' ) = {5|si( 'byte' )}
2048|si( 'byte' ) = {2048|si( 'byte' )}
5242880|si( 'byte' ) = {5242880|si( 'byte' )}
5242880|si( 'byte', 'kibi' ) = {5242880|si( 'byte', 'kibi' )}
5242880|si( 'byte', 'mega' ) = {5242880|si( 'byte', 'mega' )}
5242880|si( 'byte', 'none' ) = {5242880|si( 'byte', 'none' )}
5242880|si( 'byte', 'auto', 1 ) = {5242880|si( 'byte', 'auto', 1 )}
```

Ausgabe auf alpha:

```text
5|si( 'byte' ) = 5 B
2048|si( 'byte' ) = 2.00 kB
5242880|si( 'byte' ) = 5.00 MB
5242880|si( 'byte', 'kibi' ) = 5,120.00 KiB
5242880|si( 'byte', 'mega' ) = 5.24 MB
5242880|si( 'byte', 'none' ) = 5,242,880 B
5242880|si( 'byte', 'auto', 1 ) = 5.0 MB
```

Dezimale Einheiten, ein festes Präfix und ein eigenes Dezimalzeichen:

```tpl
1500|si( 'meter' ) = {1500|si( 'meter' )}
150|si( 'meter' ) = {150|si( 'meter' )}
150|si( 'meter', 'kilo', 3 ) = {150|si( 'meter', 'kilo', 3 )}
2500|si( 'gram', 'kilo', 1, ',' ) = {2500|si( 'gram', 'kilo', 1, ',' )}
-2000|si( 'second' ) = {-2000|si( 'second' )}
```

Ausgabe auf alpha:

```text
1500|si( 'meter' ) = 1.50 km
150|si( 'meter' ) = 1.50 hm
150|si( 'meter', 'kilo', 3 ) = 0.150 km
2500|si( 'gram', 'kilo', 1, ',' ) = 2,5 kg
-2000|si( 'second' ) = -2.00 ks
```

Eine unbekannte Einheit:

```tpl
[{100|si( 'furlong' )}]
```

Ausgabe auf alpha:

```text
[100]
```

Von der Template-Engine protokolliert:

```text
WARNING eZTemplateUnitOperator: No such unit 'furlong'
```

Die Beispiele lieferten auf alpha interpretiert und kompiliert dieselbe Ausgabe.

## Verwandte Seiten

- [Locale-Operatoren](locale-operatoren.md): `l10n( 'number' )` ohne Einheit
- [PHP-Zeichenkettenfunktionen: kodieren und formatieren](php-zeichenkettenfunktionen-kodieren-und-formatieren.md): `number_format`
- [Alle Template-Operatoren (generiert)](../../../eng-us/examples/template-operators/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
