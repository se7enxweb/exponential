# PHP-Zeichenkettenfunktionen als Template-Operatoren

Diese Seite ist für Template-Entwickler, die in einem Template mit Text arbeiten müssen. Sechzig PHP-Funktionen für
Zeichenketten (62 unter PHP 8.3 oder neuer, siehe unten) stehen in Templates als Operatoren zur Verfügung, sodass ein
Template Text messen, durchsuchen, zuschneiden, vergleichen und formatieren kann, ohne eigene Extension. Hinzugefügt im
März 2026 (Release 6.0.13) in der Klasse `eZTemplateStringsOperator`
(`lib/eztemplate/classes/eztemplatestringsoperator.php`), mit einer PHPUnit-Suite von
etwa tausend Zeilen (`tests/tests/lib/eztemplate/eZTemplateStringsOperatorTest.php`,
siehe [PHPUnit 13](../../bc/6.0/phpunitv13.md)).

## Wie ein Operator auf PHP abgebildet wird

Der weitergeleitete Wert ist das erste PHP-Argument (das „Subjekt“). Die Template-Argumente sind die übrigen
PHP-Argumente, in der Reihenfolge, die PHP festlegt.
`{$text|substr(0, 5)}` ist `substr( $text, 0, 5 )`.

```
{def $text = 'Hello, Exponential'}
{$text|strlen}                       {* 18 *}
{$text|strpos('Exp')}                {* 7 *}
{$text|substr(7)}                    {* Exponential *}
{$text|substr(0, 5)}                 {* Hello *}
{$text|str_contains('Expo')}         {* true *}
{$text|str_starts_with('Hello')}     {* true *}
{$text|lcfirst}                      {* hello, Exponential *}
{1234567.891|number_format(2, '.', ',')}   {* 1,234,567.89 *}
{'%s has %d items'|sprintf($name, $count)}
{'<b>bold</b> text'|strip_tags}      {* bold text *}
```

`strpos`, `stripos`, `strrpos` und `strripos` geben `false` zurück, wenn nichts gefunden wird; prüfen Sie mit
`eq( $pos, false() )` und nicht mit `eq( $pos, 0 )`. Ein fehlendes Pflichtargument erzeugt eine Template-Warnung, die
das Argument nennt (zum Beispiel „Missing required parameter: start“), statt eines fatalen PHP-Fehlers.

## Die beiden Ersetzungs-Operatoren: einmal lesen

```
{'Red red RED'|ristring('red', 'blue')}   {* Red blue RED   case-sensitive *}
{'Red red RED'|rstring('red', 'blue')}    {* blue blue blue case-insensitive *}
```

`ristring` ist `str_replace` von PHP (unterscheidet Groß- und Kleinschreibung) und `rstring` ist `str_ireplace`
(unterscheidet sie nicht). Die Namen lassen sich leicht verwechseln; beide nehmen Arrays von Such- und
Ersetzungszeichenketten an. Ein Subjekt null (ein nicht gesetztes Attribut) wird in `ristring` zu einer leeren
Zeichenkette.

## Die vollständige Liste

Ohne Parameter (der Compiler setzt Konstanten direkt ein und erzeugt einen einfachen PHP-Aufruf):
`addslashes`, `bin2hex`, `convert_uudecode`, `convert_uuencode`, `hex2bin`,
`lcfirst`, `quoted_printable_decode`, `quoted_printable_encode`, `quotemeta`,
`soundex`, `str_shuffle`, `stripcslashes`, `strlen`, `stripslashes`.

Mit Parametern: `addcslashes`, `chunk_split`, `hebrev`, `html_entity_decode`,
`htmlentities`, `htmlspecialchars_decode`, `levenshtein`, `ltrim`, `metaphone`,
`number_format`, `rtrim`, `similar_text`, `sprintf`, `str_contains`,
`str_ends_with`, `str_getcsv`, `str_split`, `str_starts_with`, `str_word_count`,
`strcasecmp`, `strcmp`, `strcoll`, `strcspn`, `strip_tags`, `stripos`, `stristr`,
`strnatcasecmp`, `strnatcmp`, `strncasecmp`, `strncmp`, `strpbrk`, `strpos`,
`strrchr`, `strripos`, `strrpos`, `strspn`, `strstr`, `strtok`, `strtr`,
`substr`, `substr_compare`, `substr_count`, `substr_replace`, `vsprintf`.

Dazu die kreativen Ergänzungen `ristring` und `rstring` und, nur registriert, wenn PHP die Funktionen bereitstellt (8.3
oder neuer), `str_increment` und `str_decrement` (ohne Parameter). Prüfen Sie die Liste, die Ihre Installation
tatsächlich hat, mit dem [Operator expinfo](../../bc/6.0/expinfo-operator.md).

`number_format` folgt PHP: Mit einem Parameter gibt er die Dezimalstellen an; mit drei Dezimalstellen, Dezimalzeichen und
Tausendertrennzeichen (PHP braucht die letzten beiden zusammen). `sprintf` verwendet die weitergeleitete Zeichenkette
als Format und übergibt ihr die Template-Argumente.

## Was absichtlich nicht dabei ist

Operatoren, die schon existierten, behalten ihr altes Verhalten und werden nicht neu definiert:
`chr`, `ord`, `trim`, `nl2br`, `rot13`, `crc32`, `md5`, `sha1`, `concat`,
`indent`, `upcase`, `downcase`, `count_chars`, `count_words`, `break`, `wrap`,
`shorten`, `pad`, `upfirst`, `upword`, `simplify`, `wash`, `append`, `prepend`,
`merge`, `contains`, `compare`, `extract`, `extract_left`, `extract_right`,
`begins_with`, `ends_with`, `implode`, `explode`, `repeat`, `reverse`, `insert`,
`remove`, `replace`, `unique`, `array_sum`. Verwenden Sie diese bevorzugt für das, was sie tun; zum Beispiel
`{$text|upcase}` und keine Schreibweise im Stil von PHP.

## Vor der Aktualisierung

Operatornamen sind global. Wenn eine Ihrer Extensions bereits einen Operator mit einem der neuen Namen definiert
(`strpos`, `substr`, `strlen` und `sprintf` sind die wahrscheinlichen), testen Sie nach der Aktualisierung die Seiten, die
ihn verwenden: Listen Sie die Operatoren, die eine Installation bereitstellt, mit dem
[Operator expinfo](../../bc/6.0/expinfo-operator.md) auf und vergleichen Sie.

## Verwandte Seiten

- [Operatoren für Rollen und Richtlinien](template-operatoren-fuer-rollen-und-richtlinien.md)
- [Der Operator expinfo](../../bc/6.0/expinfo-operator.md), [PHPUnit 13](../../bc/6.0/phpunitv13.md)
- [Changelog 6.0.13](../../../changelogs/6.0/6.0.13.md)
- [Chronik: März 2026](../../geschichte/2026/2026-03.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
