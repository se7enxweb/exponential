---
title: Ausgaben aus PHP maskieren
audience: php-developer, developer
category: php-development
order: 340
summary: Wie Sie Werte maskieren, die PHP-Code in HTML, Attribute, URLs, JSON, CSV, Shell-Befehle und Logs schreibt - htmlspecialchars() mit ausdrücklichen Flags für PHP 8.0, der Operator wash und warum Templates der bessere Ort sind, Data-Attribute statt Inline-Skripten und die Fehler, die Cross-Site-Scripting verursachen.
keywords: Maskierung, Ausgabemaskierung, Cross-Site-Scripting, Data-Attribute, CSV-Injection, escaping, output escaping, XSS, cross-site scripting, htmlspecialchars, ENT_QUOTES, ENT_SUBSTITUTE, ENT_HTML401, wash, json_encode, JSON_HEX_TAG, rawurlencode, escapeshellarg, CSV injection, data attributes, Content Security Policy
type: help
---
# Ausgaben aus PHP maskieren

Diese Seite richtet sich an PHP-Entwickler, deren Code Werte in Ausgaben schreibt: HTML aus einer View oder einem
Operator, JSON aus einem Endpunkt, CSV aus einem Export, einen Shell-Befehl, eine Log-Zeile. Die Regel ist einfach:
Maskieren Sie jeden Wert dort, wo er Ihren Code verlässt, im Format des Ortes, an den er geht. Die Template-Sicht auf
dieselbe Regel finden Sie unter [Eingabevalidierung und Ausgabemaskierung](../ersteller/anwendungsentwickler/eingabepruefung-und-escaping.md).

## Inhalt

1. [Bevorzugen Sie Templates](#bevorzugen-sie-templates)
2. [HTML aus PHP](#html-aus-php)
3. [Attribute, URLs und Daten für Skripte](#attribute-urls-und-daten-für-skripte)
4. [JSON](#json)
5. [CSV, Shell und Logs](#csv-shell-und-logs)
6. [Die Kontexttabelle](#die-kontexttabelle)
7. [Fehler, die Cross-Site-Scripting verursachen](#fehler-die-cross-site-scripting-verursachen)

## Bevorzugen Sie Templates

HTML gehört in Templates, wo `|wash` jeden Wert an der Stelle maskiert, an der er ausgegeben wird:

```text
<h2>{$item.title|wash}</h2>
<a href={concat( 'acmereport/item/', $item.id )|ezurl} title="{$item.title|wash}">{'Open'|i18n( 'extension/acmereport' )}</a>
```

Eine View übergibt dem Template Werte (`$tpl->setVariable()`), kein HTML. Dann ist die Maskierung an einer Stelle
sichtbar, und ein Reviewer kann sie prüfen.

## HTML aus PHP

Wenn PHP Markup bauen muss (ein Template-Operator, der HTML zurückgibt, eine Fehlermeldung, der Text einer E-Mail),
maskieren Sie jeden Wert mit ausdrücklichen Flags:

```php
function acmeEscape( $value )
{
    return htmlspecialchars( (string)$value, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8' );
}

$html = '<span class="acme-status">' . acmeEscape( $item->attribute( 'status' ) ) . '</span>';
```

Übergeben Sie die Flags jedes Mal. PHP 8.1 hat den Standard auf `ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401` geändert;
der Standard von PHP 8.0 lässt `'` unmaskiert, sodass ein Wert in einem Attribut mit einfachen Anführungszeichen unter
PHP 8.0 ausbrechen würde. Der Kernel übergibt die Flags seit 6.0.15 ausdrücklich in `wash` und 27 weiteren Dateien
([Unterstützung für PHP 8.0](../../bc/6.0/php-8.0-unterstuetzung.md)).

## Attribute, URLs und Daten für Skripte

- **Attribute**: Setzen Sie jedes Attribut in Anführungszeichen und maskieren Sie den Wert wie oben.
- **URLs**: Bauen Sie Site-URLs mit `eZURI::transformURI()` (PHP) oder `|ezurl` (Templates); kodieren Sie einen Teil
  einer URL mit `rawurlencode()`, und erlauben Sie für Adressen aus Eingaben nur `http` und `https`.
- **Daten für JavaScript**: Geben Sie Werte niemals in ein Inline-`<script>` aus. Legen Sie sie in ein `data-*`-Attribut
  oder einen Block `<script type="application/json">`, den Ihr externes Skript liest. Die Content Security Policy der
  Administration lehnt Inline-Skripte und `style=""` ab ([Content Security Policy für Ihren Code](../ersteller/anwendungsentwickler/content-security-policy.md)).

```php
$attr = acmeEscape( json_encode( array( 'id' => (int)$item->attribute( 'id' ) ) ) );
$html = '<div class="acme-chart" data-conf="' . $attr . '"></div>';
```

## JSON

```php
header( 'Content-Type: application/json; charset=utf-8' );
echo json_encode( $data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT );
```

Die Flags `JSON_HEX_*` machen die Ausgabe sicher, selbst wenn sie in HTML landet. Setzen Sie den `Content-Type`, damit
ein Browser die Antwort niemals als HTML rendert. Über ezjscore oder REST geben Sie Arrays zurück und lassen die Schicht
sie kodieren.

## CSV, Shell und Logs

| Ausgabe | Maskierung |
|---|---|
| CSV, das in Tabellenkalkulationen geöffnet wird | `fputcsv()` für die Anführung; stellen Sie Werten, die mit `=`, `+`, `-` oder `@` beginnen, ein Präfix voran, damit sie nicht als Formeln ausgeführt werden (der CSV-Export des Kernels tut das) |
| Shell-Befehle | vermeiden Sie sie; andernfalls `escapeshellarg()` für jedes Argument |
| Log-Zeilen | entfernen Sie Zeilenumbrüche (`str_replace( array( "\r", "\n" ), ' ', $value )`), damit ein Eintrag keinen anderen vortäuschen kann; protokollieren Sie niemals Geheimnisse oder personenbezogene Daten |
| E-Mail-Header | entfernen Sie Zeilenumbrüche aus jedem Wert |

## Die Kontexttabelle

| Ausgabe geht in | In PHP | In Templates |
|---|---|---|
| HTML-Text | `htmlspecialchars( $v, ENT_QUOTES \| ENT_SUBSTITUTE \| ENT_HTML401, 'UTF-8' )` | `\|wash` |
| HTML-Attribut | dasselbe, Attribut in Anführungszeichen | `\|wash`, Attribut in Anführungszeichen |
| Site-URL | `eZURI::transformURI()` | `\|ezurl` |
| Teil einer URL | `rawurlencode()` | `\|rawurlencode` |
| JSON | `json_encode()` mit `JSON_HEX_*` | `json_encode()\|wash` in einem Attribut |
| SQL | `$db->escapeString()`, Casts | (kein SQL in Templates) |

## Fehler, die Cross-Site-Scripting verursachen

- Eine Zahl, die ohne Maskierung ausgegeben wird, „weil sie eine Zahl ist“: Eine spätere Änderung macht Text daraus.
- `htmlspecialchars( $v )` ohne Flags in Code, der unter PHP 8.0 laufen muss.
- Maskieren bei der Eingabe und Speichern maskierten Texts: doppelte Maskierung an einer Stelle, keine bei der nächsten
  Ausgabe, die es vergisst.
- HTML in einer View bauen und an ein Template übergeben, das es roh ausgibt.
- Ein Wert aus der Anfrage, der in einer Fehlermeldung wiedergegeben wird.
- Ein JSON-Wert, der in einem Inline-Skript ausgegeben wird.

## Verwandte Seiten

- [Sicherheit in PHP-Code](sicherheit-in-php-code.md)
- [Code schreiben, der unter PHP 8.0 läuft](code-der-unter-php-8-0-laeuft.md#geänderte-standardwerte-html-maskierung)
- [String-Template-Operatoren](../../funktionen/6.0/template-operatoren-fuer-zeichenketten.md)
- [Freie Dokumentation](../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
