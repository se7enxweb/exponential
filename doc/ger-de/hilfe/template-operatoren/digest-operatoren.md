---
title: Digest-Operatoren
audience: developer, designer, app-creator
category: development
order: 230
summary: crc32, md5 und rot13 aus eZTemplateDigestOperator, dazu sha1, das die Klasse anbietet, das aber erst funktioniert, nachdem ein anderer Digest-Operator sie geladen hat; wofür sie in Templates gedacht sind und wofür nicht, mit Beispielen, die auf alpha gerendert wurden.
keywords: crc32, md5, sha1, rot13, Hash, Prüfsumme, Digest, Hashwert, hash, checksum, digest, eZSys::ezcrc32, 64bitCompatibilityMode, eZTemplateDigestOperator
type: help
---
# Digest-Operatoren

Prüfsummen und Hashwerte einer Zeichenkette, um IDs, Cache-Schlüssel oder Klassennamen zu bilden. Sie werden von
`eZTemplateDigestOperator` (`lib/eztemplate/classes/eztemplatedigestoperator.php`) über
`lib/eztemplate/classes/eztemplateautoload.php` registriert. Alle arbeiten mit der gepipten Eingabe und nehmen keine
Parameter entgegen.

## Inhalt

1. [Die Operatoren](#die-operatoren)
2. [sha1 und die Autoload-Liste](#sha1-und-die-autoload-liste)
3. [Wofür sie nicht gedacht sind](#wofür-sie-nicht-gedacht-sind)
4. [Verwandte Seiten](#verwandte-seiten)

## Die Operatoren

### md5

PHP `md5()`: 32 Hexadezimalziffern. Generierte Referenz: [md5](../../beispiele/template-operatoren/md5.md).

### crc32

`eZSys::ezcrc32()` (`lib/ezutils/classes/ezsys.php`): PHP `crc32()`, als vorzeichenlose Zahl ausgegeben, wenn in
`site.ini` `[SiteSettings] 64bitCompatibilityMode=enabled` gesetzt ist, sodass der Wert auf 32-Bit- und 64-Bit-PHP
gleich ist. Generierte Referenz: [crc32](../../beispiele/template-operatoren/crc32.md).

### rot13

PHP `str_rot13()`: Jeder Buchstabe wird um 13 Stellen verschoben, andere Zeichen bleiben erhalten. Es verbirgt nichts;
es verhindert nur, dass Text auf einen Blick gelesen wird. Generierte Referenz:
[rot13](../../beispiele/template-operatoren/rot13.md).

### sha1

PHP `sha1()`: 40 Hexadezimalziffern. Lesen Sie den nächsten Abschnitt, bevor Sie ihn verwenden.

Für alle vier gilt: Eingabe ist die Zeichenkette (über die Pipe), keine Parameter, Rückgabe ist eine Zeichenkette
(bei `crc32` eine Zahl).

```tpl
'Fit & Healthy'|md5 = {'Fit & Healthy'|md5}
'Fit & Healthy'|crc32 = {'Fit & Healthy'|crc32}
'Fit & Healthy'|rot13 = {'Fit & Healthy'|rot13}
'Fit & Healthy'|sha1 = {'Fit & Healthy'|sha1}
```

Ausgabe auf alpha:

```text
'Fit & Healthy'|md5 = 230d0ad476984ff3dba19ec739b09733
'Fit & Healthy'|crc32 = 2208743097
'Fit & Healthy'|rot13 = Svg & Urnygul
'Fit & Healthy'|sha1 = 2441fb64e95685d5f0064f97e86d06048e57a499
```

Eine kurze, stabile ID für ein Element, gebildet aus einem Knoten von alpha (Knoten 89) und seiner aktuellen Version:

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 89 ) )}
id-{concat( $node.node_id, '-', $node.object.current_version )|md5|extract_left( 8 )}
```

Ausgabe auf alpha:

```text
id-0c7e77db
```

## sha1 und die Autoload-Liste

Die Klasse fügt `sha1` ihrer Operatorliste hinzu, wenn PHP die Funktion hat, aber der Eintrag `operator_names` in
`lib/eztemplate/classes/eztemplateautoload.php` führt nur `crc32`, `md5` und `rot13` auf. Die Engine lädt eine
Operatorklasse bei der ersten Verwendung eines ihrer aufgeführten Namen, und das Laden registriert jeden Namen, den die
Klasse anbietet. `sha1` funktioniert also, nachdem `md5`, `crc32` oder `rot13` in derselben Anfrage verwendet wurde,
wie im ersten Beispiel, und ist davor "not registered", wobei der Wert unverändert bleibt:

```tpl
[{'Fit & Healthy'|sha1}]
```

Ausgabe auf alpha:

```text
[Fit & Healthy]
```

Von der Template-Engine protokolliert:

```text
WARNING : Operator 'sha1' is not registered
```

Verwenden Sie `md5`, wenn Sie in einem Template einen Hashwert brauchen, oder registrieren Sie einen eigenen Operator
([Operatoren registrieren](operatoren-registrieren.md)).

## Wofür sie nicht gedacht sind

Sie sind nicht für Passwörter, Signaturen oder irgendetwas gedacht, das ein Angreifer nicht fälschen darf: MD5 und
SHA-1 sind für diese Zwecke gebrochen, und ein Template ist ohnehin der falsche Ort für Geheimnisse. Verwenden Sie sie
nur für IDs und Cache-Schlüssel.

Die Beispiele lieferten auf alpha interpretiert und kompiliert dieselbe Ausgabe.

## Verwandte Seiten

- [Text-Operatoren](text-operatoren.md): `concat`, um die zu hashende Zeichenkette zu bilden
- [Cache-Blöcke](../designer/vorlagen/cache-block.md): Schlüssel für cache-block
- [Alle Template-Operatoren (generiert)](../../beispiele/template-operatoren/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
