---
title: Operatoren kleinerer Erweiterungen
audience: developer, designer, app-creator
category: development
order: 440
summary: Die Template-Operatoren von ezoe, ezwt, enhancedezbinaryfile, recaptcha, xrowmetadata und bcwebsitestatistics - ezoe_ini_section, ezcreateclasslistgroups, parsexml, recaptcha_get_html, metadata, die sechs xrowmetadata_license-Operatoren und die sechs bc_ga-Operatoren - mit Parametern, Rückgabewerten, auf alpha gefundenen Stolperfallen (Operatornamen mit Großbuchstaben, ein die() bei fehlendem Schlüssel) und auf alpha gerenderten Beispielen.
keywords: Erweiterungen, Operatoren, Lizenz, Metadaten, Captcha, Statistik, Stolperfallen, ezoe_ini_section, ezcreateclasslistgroups, parsexml, recaptcha_get_html, metadata, xrowmetadata_license, xrowmetadata_license_enabled, xrowmetadata_license_choices, xrowmetadata_license_info, xrowmetadata_license_inherited, xrowmetadata_license_class_settings, bc_ga_urchin, bc_ga_urchinOrder, bc_ga_jsEscapedString, bc_ga_formatNumericDecimal, bc_ga_xmlAttributeValue, bc_ga_script_url
type: help
---
# Operatoren kleinerer Erweiterungen

Jede der folgenden Erweiterungen registriert ihre Operatoren in ihrer eigenen `autoloads/eztemplateautoload.php`; die Operatoren
existieren nur, solange die Erweiterung aktiv ist. Fünf davon werden über `[TemplateSettings] ExtensionAutoloadPath[]` gefunden;
bcwebsitestatistics fügt seinen Ordner stattdessen zu `AutoloadPathList[]` hinzu (in seiner `settings/site.ini.append.php`).

| Erweiterung | Klasse (in `extension/<name>/autoloads/`) | Operatoren |
|---|---|---|
| ezoe | `eZOETemplateUtils` (`ezoetemplateutils.php`) | `ezoe_ini_section` |
| ezwt | `eZCreateClassListGroups` (`ezcreateclasslistgroups.php`) | `ezcreateclasslistgroups` |
| enhancedezbinaryfile | `TemplateParseXMLOperator` (`templateparsexmloperator.php`) | `parsexml` |
| recaptcha | `reCAPTCHATemplateOperator` (`recaptchatemplateoperator.php`) | `recaptcha_get_html` |
| xrowmetadata | `xrowMetaDataOperator`, `xrowMetaDataLicenseOperator`, `xrowMetaDataLicensePickerOperator` | `metadata`, `xrowmetadata_license`, fünf `xrowmetadata_license_*` |
| bcwebsitestatistics | `BCWebsiteStatisticsOperators` (`bcwebsitestatisticsoperators.php`) | sechs `bc_ga_*` |

## Inhalt

1. [ezoe, ezwt, enhancedezbinaryfile](#ezoe-ezwt-enhancedezbinaryfile): [ezoe_ini_section](#ezoe_ini_section),
   [ezcreateclasslistgroups](#ezcreateclasslistgroups), [parsexml](#parsexml)
2. [recaptcha](#recaptcha): [recaptcha_get_html](#recaptcha_get_html)
3. [xrowmetadata](#xrowmetadata): [metadata](#metadata), [xrowmetadata_license](#xrowmetadata_license),
   [xrowmetadata_license_enabled](#xrowmetadata_license_enabled),
   [xrowmetadata_license_choices](#xrowmetadata_license_choices),
   [xrowmetadata_license_info](#xrowmetadata_license_info),
   [xrowmetadata_license_inherited](#xrowmetadata_license_inherited),
   [xrowmetadata_license_class_settings](#xrowmetadata_license_class_settings)
4. [bcwebsitestatistics](#bcwebsitestatistics): [bc_ga_urchin](#bc_ga_urchin), [bc_ga_urchinOrder](#bc_ga_urchinorder),
   [bc_ga_jsEscapedString](#bc_ga_jsescapedstring), [bc_ga_formatNumericDecimal](#bc_ga_formatnumericdecimal),
   [bc_ga_xmlAttributeValue](#bc_ga_xmlattributevalue), [bc_ga_script_url](#bc_ga_script_url)

## ezoe, ezwt, enhancedezbinaryfile

### ezoe_ini_section

Generierte Seite: [ezoe_ini_section](../../beispiele/template-operatoren/ezoe_ini_section.md).

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `section` | Zeichenkette | ja | leer |
| `file` | Zeichenkette | nein | `site.ini` |

Keine Eingabe. Gibt `true` zurück, wenn die INI-Datei die Gruppe enthält (`eZINI::hasSection()`). Der Online-Editor verwendet ihn in
`ezoe/generalattributes.tpl` und `ezoe/customattributes.tpl`, bevor er eine Gruppe mit `ezini` liest.

### ezcreateclasslistgroups

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `can_create_class_list` | Array | ja | leer |

Keine Eingabe. Nimmt eine Liste von Hashes mit `id` (in der Form der `can_create_class_list` eines Knotens) und gibt sie nach
Klassengruppen gruppiert zurück: Gruppen-ID => `group_name` und `items` (Klassenobjekte). Klassen in `websitetoolbar.ini`
`[WebsiteToolbarSettings] HiddenContentClasses[]` werden weggelassen. Eine nicht existierende Klassen-ID führt dazu, dass der
Operator für die ganze Liste `false` zurückgibt. Die Website-Toolbar verwendet ihn für ihr Menü „hier anlegen“.

### parsexml

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `first_param` | Zeichenkette | nein | `default text` |

Eingabe: eine XML-Zeichenkette (die gespeicherten Daten einer erweiterten Binärdatei). Gibt den Text des ersten Elements mit dem
Namen `first_param` zurück oder sein Attribut `value`, wenn das Element keinen Text hat. Eine leere Eingabe wird unverändert
zurückgegeben; XML, das sich nicht parsen lässt, hinterlässt keinen Wert (die Eingabe wird durch nichts ersetzt). Die Klasse kennt
auch einen Fall `filecheck`, die Autoload-Datei registriert aber nur `parsexml`, daher lässt sich `filecheck` aus einem Template
nicht aufrufen.

```tpl
1 ezoe_ini_section: {ezoe_ini_section( 'EditorSettings', 'ezoe.ini' )|choose( 'no', 'yes' )} / {ezoe_ini_section( 'NoSuchGroup' )|choose( 'no', 'yes' )}

{def $groups = ezcreateclasslistgroups( array( hash( 'id', 1 ), hash( 'id', 2 ) ) )}
2 ezcreateclasslistgroups: {foreach $groups as $id => $g}group {$id} "{$g.group_name}": {foreach $g.items as $c}{$c.identifier}{delimiter}, {/delimiter}{/foreach}{delimiter}; {/delimiter}{/foreach}

3 parsexml: {'<binaryfile><Filename>report.pdf</Filename><Size value="1024"/></binaryfile>'|parsexml( 'Filename' )} / {'<binaryfile><Size value="1024"/></binaryfile>'|parsexml( 'Size' )}
```

Ausgabe auf alpha:

```text
1 ezoe_ini_section: yes / no
2 ezcreateclasslistgroups: group 1 "Content": folder, article
3 parsexml: report.pdf / 1024
```

## recaptcha

### recaptcha_get_html

Keine Parameter, keine Eingabe. Gibt das Markup des reCAPTCHA-Widgets für den öffentlichen Schlüssel aus `recaptcha.ini`
`[Keys] PublicKey` zurück (eine nach Hostnamen geschlüsselte Liste wählt den aktuellen Host, sonst den ersten Eintrag) oder den Text
„User bypasses CAPTCHA“, wenn der Benutzer die Richtlinie `recaptcha/bypass_captcha` hat. Der Operator wählt die sichere Adresse,
wenn `$_SERVER['SERVER_PORT']` 443 ist.

Bei leerem öffentlichem Schlüssel ruft das mitgelieferte `recaptcha_get_html()` (`extension/recaptcha/classes/recaptchalib.php`)
`die()` auf und **beendet die Anfrage** mit einer Meldung: Konfigurieren Sie den Schlüssel, bevor der Operator in ein Template
gelangt. Das Widget lädt ein Skript von einem Fremd-Host, das eine strenge Content Security Policy erlauben muss. Im Beispiel wird
nur ausgegeben, dass Markup zurückkam, nicht der Schlüssel:

```tpl
1 recaptcha_get_html: {recaptcha_get_html()|count_chars|gt( 0 )|choose( 'empty', 'markup returned' )}
2 xrowmetadata_license_enabled: {xrowmetadata_license_enabled()|choose( 'no', 'yes' )}

{def $choices = xrowmetadata_license_choices()}
3 xrowmetadata_license_choices: {$choices|count} groups{if $choices|count}, first: {$choices[0].label|wash} ({$choices[0].options|count} options){/if}


{def $mit = xrowmetadata_license_info( 'MIT' )}
4 xrowmetadata_license_info MIT: {if $mit}{$mit.name|wash}, url {$mit.url|wash}{else}false{/if}


{def $inh = xrowmetadata_license_inherited( '' )}
5 xrowmetadata_license_inherited: {if $inh}{$inh.id|wash} from {$inh.source}{else}false{/if}


{def $lic = xrowmetadata_license( 90 )}
6 xrowmetadata_license node 90: {if $lic}{$lic.id|wash} ({$lic.kind}), meta {$lic.meta_name}{else}false{/if}


7 metadata node 90: {metadata( 90 )|get_type}
```

Ausgabe auf alpha:

```text
1 recaptcha_get_html: markup returned
2 xrowmetadata_license_enabled: yes
3 xrowmetadata_license_choices: 9 groups, first: Public domain (23 options)
4 xrowmetadata_license_info MIT: MIT License, url https://opensource.org/license/MIT
5 xrowmetadata_license_inherited: GPL-2.0-or-later from installation
6 xrowmetadata_license node 90: GPL-2.0-or-later (license), meta dcterms.license

7 metadata node 90: object[xrowMetaData]
```

## xrowmetadata

### metadata

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `node_id` | int | ja | `null` |

Keine Eingabe. Gibt das für das Inhaltsobjekt des Knotens gespeicherte Objekt `xrowMetaData` zurück
(`xrowMetaDataFunctions::fetchByObject()`) oder `false`. Platzhalter in seinem `title` und seiner `description` werden ersetzt:
`[name]`, `[count]` (Größe des Teilbaums), `[count:localbusiness]`, `[path]` (die Pfadnamen in umgekehrter Reihenfolge, verbunden
durch ` | `), `[parentname]` und `[pagecount]` (Seite n von m, aus `[EditorInputSettings] MaxPageLimit` und dem Ansichtsparameter
`offset`). Zeile 7 oben.

### xrowmetadata_license

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `node` | gemischt (Knoten, Objekt oder Knoten-ID) | nein | `null` (oder die Eingabe) |

Gibt die Lizenz zurück, die für den Knoten gilt (`xrowMetaDataLicense::resolve()`; ohne Knoten der aktuelle), als Hash mit `id`,
`name`, `url` (nur `http`/`https`, validiert), `type`, `source`, `holder`, `year`, `attribution`, `groups`, `kind` (`license`,
`dedication`, `mark` oder `reserved`), `page_url`, `show` (`link_rel`, `meta`, `json_ld` aus `[LicenseSettings]`), `meta_name`,
`meta_content` und fertigem JSON-LD (`json_ld_value`, `json_ld_property`, `json_ld_node`, `json_ld_script`). `false`, wenn die
Lizenzierung deaktiviert ist oder nichts zutrifft. Zeile 6 oben.

### xrowmetadata_license_enabled

Keine Parameter. `true`, sofern `xrowmetadata.ini` `[LicenseSettings] Enabled` nichts anderes festlegt. Zeile 2.

### xrowmetadata_license_choices

Parameter `selected` (Zeichenkette, Standard leer). Die Lizenzliste für ein Bearbeitungsformular, gruppiert (jede Gruppe: `key`,
`label`, `options`), pro Locale gecacht. Zeile 3.

### xrowmetadata_license_info

Parameter `id` (Zeichenkette). Der Katalogeintrag einer Lizenz-ID oder `false`. Zeile 4.

### xrowmetadata_license_inherited

Parameter `class_default` (Zeichenkette). Die Lizenz, die ein neues Objekt erben würde: der Standard der Klasse, sonst der Standard
der Installation, wobei `source` auf `class` oder `installation` gesetzt ist. Zeile 5.

### xrowmetadata_license_class_settings

Parameter `class_attribute` (Objekt, erforderlich). Für ein Klassenattribut des Metadaten-Datentyps dessen `default_license` und
`allow_object_license`; für alles andere die Standardwerte `''` und `true`. Er braucht ein Klassenattribut-Objekt und wird in den
Templates zur Bearbeitung von Klassen verwendet; im Beispiel kommt er nicht vor.

## bcwebsitestatistics

Vier dieser Operatornamen enthalten Großbuchstaben. Der Template-Parser erkennt ein Tag, das mit einem Operatoraufruf **beginnt**,
nur dann, wenn der Name auf `[a-z0-9_-]+(` passt (`eZTemplateMultiPassParser`), daher wird `{bc_ga_jsEscapedString( ... )}` als
eigenständiges Tag als unbekannte **Funktion** gelesen und scheitert mit „Function "bc_ga_jsEscapedString" is not registered“.
Innerhalb eines Ausdrucks (in einem `def`, einem Parameter, nach `|`) funktionieren die Namen:

```tpl
{def $escaped = bc_ga_jsEscapedString( concat( "It's ", '"quoted"' ) )
     $price = bc_ga_formatNumericDecimal( 12.5, 2 )}
1 bc_ga_jsEscapedString: {$escaped}
2 bc_ga_formatNumericDecimal: {$price} / {''|bc_ga_formatNumericDecimal( 0, 2 )}
3 bc_ga_urchin: {bc_ga_urchin()|count_chars} characters
```

Ausgabe auf alpha:

```text
1 bc_ga_jsEscapedString: It\'s '+String.fromCharCode(34)+'quoted'+String.fromCharCode(34)+'
2 bc_ga_formatNumericDecimal: 12.50 / 0.00
3 bc_ga_urchin: 225 characters
```

### bc_ga_urchin

Keine Parameter. Mit `bcwebsitestatistics.ini` `[BCWebsiteStatisticsSettings] PageSubmit=enabled` und einem Konto in `Urchin` gibt
er die Script-Tags der Webanalyse zurück (das Tag-Manager-Skript mit der Konto-ID oder `Script`/`SecureScript`, dazu
`bcwebsitestatistics_gtag.js` der Erweiterung mit `data-bc-ga-id`); mit `OrderSubmit=enabled` zusätzlich das Skript zur
Bestellverfolgung. Zeile 3 oben gibt nur seine Länge aus, nicht die Konto-ID.

### bc_ga_urchinOrder

Parameter `order` (ein Bestellobjekt). Baut das versteckte Formular zur Bestellverfolgung mit Bestellnummer, Summen, Stadt,
Bundesland und Land des Kunden sowie den Produktzeilen. Auf alpha nicht ausgeführt: Er braucht eine echte Bestellung und gibt
Kundendaten aus.

### bc_ga_jsEscapedString

Parameter `string`. Maskiert einfache Anführungszeichen als `\'` und ersetzt doppelte Anführungszeichen durch
`'+String.fromCharCode(34)+'`, für eine JavaScript-Zeichenkette in einfachen Anführungszeichen; `false` bei einer leeren
Zeichenkette. Er ist kein allgemeiner Maskierer für JavaScript oder HTML. Zeile 1.

### bc_ga_formatNumericDecimal

Parameter `number` und `places`. Gibt die Zahl mit zwei Nachkommastellen zurück (`number_format( $n, 2, '.', '' )`; `places` wird
ignoriert); null ergibt `0.00`. Zeile 2.

### bc_ga_xmlAttributeValue

Parameter `name` und `data`. Gibt den Text des ersten Elements `name` im XML `data` zurück (über `eZXML`) oder `false`. Wird intern
für die Bestelladresse verwendet; im Beispiel kommt er nicht vor.

### bc_ga_script_url

Eingabe: JavaScript-Code. Gibt eine Adresse dafür zurück: über `ezjscPacker::generatedContentURL()`, wenn ezjscore diese Methode
anbietet, andernfalls **schreibt** er `bcwebsitestatistics_<md5>.js` in den öffentlichen Cache-Ordner. Auf alpha nicht ausgeführt,
weil er eine Datei schreibt.

## Verwandte Seiten

- [URL-, Design-, INI- und HTTP-Operatoren](url-operatoren.md) (`ezini` für einzelne Einstellungen)
- [Content Security Policy für Anwendungen](../ersteller/anwendungsentwickler/content-security-policy.md)
- [Alle Template-Operatoren (generiert)](../../beispiele/template-operatoren/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
