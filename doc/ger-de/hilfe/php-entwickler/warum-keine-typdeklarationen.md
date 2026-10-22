---
title: Warum der Code keine Typdeklarationen hat
audience: php-developer, developer
category: php-development
order: 90
summary: Eine Erklärung einer Regel, die PHP-Entwickler überrascht - Exponential-Code wird ohne skalare, Rückgabe- und Eigenschaftstypen und ohne strict_types geschrieben - was stattdessen erlaubt ist, wie Typen dokumentiert und geprüft werden und wo die Ausnahmen liegen.
keywords: Typdeklarationen, Rückgabetypen, skalare Typen, Eigenschaftstypen, Coding-Standard, Erklärung, type declarations, strict_types, return types, scalar types, property types, union types, PHPDoc, NoTypeDeclarationsSniff, PHPStan, ReturnTypeWillChange, nullable class hints, coding standard, explanation
type: help
---
# Warum der Code keine Typdeklarationen hat

Diese Seite richtet sich an PHP-Entwickler, die aus Codebasen kommen, in denen `declare( strict_types=1 )` und typisierte
Signaturen die Regel sind. In Exponential sind sie in neuem Code nicht erlaubt. Die Regel ist bewusst gewählt; diese
Seite erklärt sie, damit Sie ihr folgen können, ohne gegen sie anzukämpfen.

## Inhalt

1. [Die Regel](#die-regel)
2. [Warum](#warum)
3. [Wie Typen stattdessen dokumentiert werden](#wie-typen-stattdessen-dokumentiert-werden)
4. [Wie Typen trotzdem geprüft werden](#wie-typen-trotzdem-geprüft-werden)
5. [Die Ausnahmen](#die-ausnahmen)
6. [Häufige Fragen](#häufige-fragen)

## Die Regel

Der Sniff `bin/phpcs/Exponential/Sniffs/TypeDeclarations/NoTypeDeclarationsSniff.php` meldet, jeweils mit eigenem Code:

| Code | Beispiel, das er meldet |
|---|---|
| `StrictTypes` | `declare( strict_types=1 );` |
| `ParameterType` | `function foo( int $a )`, `function foo( string\|array $a )`, auch beförderte Konstruktorparameter |
| `ReturnType` | `function foo(): array` |
| `PropertyType` | `public string $name;` |
| `ConstantType` | `const string NAME = '...';` |

Erlaubt, weil es sie in PHP 5 gab und der Kernel sie durchgehend verwendet: `array`, `callable`, `self` und Klassen- oder
Interface-Namen, auch nullbar:

```php
public function render( array $items, ?eZINI $ini = null, ?callable $filter = null )
```

## Warum

1. **Konsistenz mit dem vorhandenen Code.** Der Kernel und die Bibliotheken von 6.0.15 sind ohne diese Deklarationen
   geschrieben. Eine Datei, die zwei Stile mischt, ist schwerer zu lesen und zu prüfen als jeder Stil für sich.
2. **Das Verhalten ändert sich nicht versehentlich.** Wer einem Parameter `int` hinzufügt, in Code, der schon immer
   numerische Strings, `null` oder `false` erhalten hat, macht aus einem funktionierenden Aufruf einen `TypeError`. In
   einer Codebasis mit Tausenden von Aufrufern, einschließlich Erweiterungen, die das Projekt nie gesehen hat, ist dieses
   Risiko real; die PHP-8-Arbeit am Kernel selbst zeigt, wie viele Werte als `null` oder Strings ankommen
   ([Hinweise zur PHP-8-Migration für Ihren Code](hinweise-zur-php-8-migration-fuer-ihren-code.md)).
3. **Signaturen bleiben kompatibel.** Erweiterungen leiten von Kernel-Klassen ab (`eZDataType`, `eZPersistentObject`,
   `eZWorkflowEventType`). Ein Rückgabetyp, der einer Kernel-Methode hinzugefügt wird, zwingt jede Unterklasse, ihn
   ebenfalls hinzuzufügen, sonst lädt die Unterklasse nicht mehr.
4. **PHP 8.0.** Mehrere Deklarationsformen (`never`, Schnittmengentypen, typisierte Konstanten, `readonly`) gibt es
   unter 8.0, der ältesten unterstützten Version, nicht.

## Wie Typen stattdessen dokumentiert werden

Jede Klasse und jede öffentliche Methode trägt einen Docblock mit `@param` und `@return`, Eigenschaften mit `@var`:

```php
/**
 * The items of one user, newest first.
 *
 * @param int $userID
 * @param int $limit at most 100
 * @return acmeReportItem[]
 */
public static function fetchByUser( $userID, $limit = 20 )
{
    $userID = (int)$userID;
    $limit = max( 1, min( 100, (int)$limit ) );
    ...
}
```

Beachten Sie die Casts am Anfang der Methode: Eingaben werden dort umgewandelt, wo sie hereinkommen; so sorgt der
Kernel dafür, dass ein Wert den Typ hat, den er haben soll ([Eingaben validieren und casten](eingaben-validieren-und-casten.md)).

## Wie Typen trotzdem geprüft werden

- **PHPStan** liest die Docblocks. `phpstan.neon.dist` setzt `treatPhpDocTypesAsCertain: false`, sodass ein Docblock-Typ
  die Analyse leitet, ohne jede defensive Prüfung zu einem Fehler zu machen
  ([Statische Analyse mit PHPStan](statische-analyse-mit-phpstan.md)).
- **Tests** prüfen die Werte, nicht nur die Typen ([PHPUnit ohne Datenbank](phpunit-ohne-datenbank.md)).
- **Casts und Prüfungen** an der Grenze: `(int)` für IDs, Allow-Listen für Auswahlwerte, `is_array()` vor `count()`.

## Die Ausnahmen

| Wo | Erlaubt |
|---|---|
| Dateien unter `tests/` | `: void` bei `setUp()`, `tearDown()` und den anderen Template-Methoden von PHPUnit, die PHPUnit verlangt |
| Methoden, die SPL-Interfaces implementieren | kein Rückgabetyp, aber das Attribut `#[\ReturnTypeWillChange]` |
| Code von Drittanbietern in `vendor/` | was immer seine Autoren geschrieben haben; er wird nicht geprüft |
| eine Erweiterung mit eigenem Standard | die Regel gilt für Code, der dem Standard des Kernels folgt; eine Erweiterung kann ihr eigenes `phpcs.xml` angeben |

## Häufige Fragen

**Kann ich einen Code des Sniffs für meine Erweiterung abschalten?** Ja: Jede Meldung hat ihren eigenen Code, sodass ein
Regelsatz einen davon ausschließen kann. Der Kernel tut das nicht.

**Wird sich die Regel ändern?** Der Standard beschreibt den Code, wie er ist. Ihn anzuheben wäre eine
Projektentscheidung, die in kleinen Schritten getroffen würde, wie das Anheben des PHPStan-Levels; bis dahin folgt neuer
Kernel-Code ihm.

## Verwandte Seiten

- [Der Coding-Standard (phpcs.xml.dist)](der-coding-standard.md)
- [Coding-Standards](../../standards/programmierstandards.md)
- [Qualitätsprüfungen](../../funktionen/6.0/qualitaetspruefungen.md)
- [Freie Dokumentation](../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
