---
title: Muster in extension/expquery_translator
audience: developer
category: standards
order: 427
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/expquery_translator mit den Entwurfsmustern.
keywords: extension/expquery_translator, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/expquery_translator

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/expquery_translator` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (53)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expQueryTranslator` | class | – | `extension/expquery_translator/classes/expquerytranslator.php:9` | – |
| `expquery_translatorInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/expquery_translator/ezinfo.php:12` | – |
| `QueryTranslator\Languages\Galach\Generators\Common\Aggregate` | class extends Visitor | – | `extension/expquery_translator/lib/Languages/Galach/Generators/Common/Aggregate.php:11` | Gemeinsame Aggregate-Visitor-Implementierung. |
| `QueryTranslator\Languages\Galach\Generators\Common\Visitor` | abstract class | – | `extension/expquery_translator/lib/Languages/Galach/Generators/Common/Visitor.php:10` | Gemeinsame Basisklasse für AST-Visitor-Implementierungen. |
| `QueryTranslator\Languages\Galach\Generators\ExtendedDisMax` | class | – | `extension/expquery_translator/lib/Languages/Galach/Generators/ExtendedDisMax.php:13` | Der ExtendedDisMax-Generator erzeugt eine Abfragezeichenkette im Format des Solr-Abfrageparsers Extended DisMax. |
| `QueryTranslator\Languages\Galach\Generators\Lucene\Common\Group` | class extends Visitor | – | `extension/expquery_translator/lib/Languages/Galach/Generators/Lucene/Common/Group.php:14` | Implementierung des Group-Node-Visitors. |
| `QueryTranslator\Languages\Galach\Generators\Lucene\Common\LogicalAnd` | class extends Visitor | – | `extension/expquery_translator/lib/Languages/Galach/Generators/Lucene/Common/LogicalAnd.php:13` | Node-Visitor-Implementierung für den Operator LogicalAnd. |
| `QueryTranslator\Languages\Galach\Generators\Lucene\Common\LogicalNot` | class extends Visitor | – | `extension/expquery_translator/lib/Languages/Galach/Generators/Lucene/Common/LogicalNot.php:13` | Node-Visitor-Implementierung für den Operator LogicalNot. |
| `QueryTranslator\Languages\Galach\Generators\Lucene\Common\LogicalOr` | class extends Visitor | – | `extension/expquery_translator/lib/Languages/Galach/Generators/Lucene/Common/LogicalOr.php:13` | Node-Visitor-Implementierung für den Operator LogicalOr. |
| `QueryTranslator\Languages\Galach\Generators\Lucene\Common\Mandatory` | class extends Visitor | – | `extension/expquery_translator/lib/Languages/Galach/Generators/Lucene/Common/Mandatory.php:13` | Node-Visitor-Implementierung für den Operator Mandatory. |
| `QueryTranslator\Languages\Galach\Generators\Lucene\Common\Phrase` | class extends Visitor | – | `extension/expquery_translator/lib/Languages/Galach/Generators/Lucene/Common/Phrase.php:14` | Implementierung des Phrase Node Visitor. |
| `QueryTranslator\Languages\Galach\Generators\Lucene\Common\Prohibited` | class extends Visitor | – | `extension/expquery_translator/lib/Languages/Galach/Generators/Lucene/Common/Prohibited.php:13` | Node-Visitor-Implementierung für den Operator Prohibited. |
| `QueryTranslator\Languages\Galach\Generators\Lucene\Common\Query` | class extends Visitor | – | `extension/expquery_translator/lib/Languages/Galach/Generators/Lucene/Common/Query.php:13` | Implementierung des Query Node Visitor. |
| `QueryTranslator\Languages\Galach\Generators\Lucene\Common\Tag` | class extends Visitor | – | `extension/expquery_translator/lib/Languages/Galach/Generators/Lucene/Common/Tag.php:14` | Implementierung des User-Node-Visitors. |
| `QueryTranslator\Languages\Galach\Generators\Lucene\Common\User` | class extends Visitor | – | `extension/expquery_translator/lib/Languages/Galach/Generators/Lucene/Common/User.php:14` | Implementierung des User-Node-Visitors. |
| `QueryTranslator\Languages\Galach\Generators\Lucene\Common\WordBase` | abstract class extends Visitor | [Schablonenmethode / abstrakte Basisklasse](schablonenmethode.md) | `extension/expquery_translator/lib/Languages/Galach/Generators/Lucene/Common/WordBase.php:14` | Basisimplementierung des Word-Node-Visitors. |
| `QueryTranslator\Languages\Galach\Generators\Lucene\ExtendedDisMax\Word` | class extends WordBase | – | `extension/expquery_translator/lib/Languages/Galach/Generators/Lucene/ExtendedDisMax/Word.php:10` | Implementierung des Word-Node-Visitors. |
| `QueryTranslator\Languages\Galach\Generators\Lucene\QueryString\Word` | class extends WordBase | – | `extension/expquery_translator/lib/Languages/Galach/Generators/Lucene/QueryString/Word.php:10` | Implementierung des Word-Node-Visitors. |
| `QueryTranslator\Languages\Galach\Generators\Native` | class | – | `extension/expquery_translator/lib/Languages/Galach/Generators/Native.php:11` | Der native Galach-Generator erzeugt eine Abfragezeichenkette im Galach-Format. |
| `QueryTranslator\Languages\Galach\Generators\Native\BinaryOperator` | class extends Visitor | – | `extension/expquery_translator/lib/Languages/Galach/Generators/Native/BinaryOperator.php:14` | Node-Visitor-Implementierung für den Operator BinaryOperator. |
| `QueryTranslator\Languages\Galach\Generators\Native\Group` | class extends Visitor | – | `extension/expquery_translator/lib/Languages/Galach/Generators/Native/Group.php:13` | Implementierung des Group-Node-Visitors. |
| `QueryTranslator\Languages\Galach\Generators\Native\Phrase` | class extends Visitor | – | `extension/expquery_translator/lib/Languages/Galach/Generators/Native/Phrase.php:14` | Implementierung des Phrase Node Visitor. |
| `QueryTranslator\Languages\Galach\Generators\Native\Query` | class extends Visitor | – | `extension/expquery_translator/lib/Languages/Galach/Generators/Native/Query.php:13` | Implementierung des Query Node Visitor. |
| `QueryTranslator\Languages\Galach\Generators\Native\Tag` | class extends Visitor | – | `extension/expquery_translator/lib/Languages/Galach/Generators/Native/Tag.php:14` | Implementierung des User-Node-Visitors. |
| `QueryTranslator\Languages\Galach\Generators\Native\UnaryOperator` | class extends Visitor | – | `extension/expquery_translator/lib/Languages/Galach/Generators/Native/UnaryOperator.php:16` | Node-Visitor-Implementierung für unäre Operatoren. |
| `QueryTranslator\Languages\Galach\Generators\Native\User` | class extends Visitor | – | `extension/expquery_translator/lib/Languages/Galach/Generators/Native/User.php:14` | Implementierung des User-Node-Visitors. |
| `QueryTranslator\Languages\Galach\Generators\Native\Word` | class extends Visitor | – | `extension/expquery_translator/lib/Languages/Galach/Generators/Native/Word.php:14` | Implementierung des Word-Node-Visitors. |
| `QueryTranslator\Languages\Galach\Generators\QueryString` | class | – | `extension/expquery_translator/lib/Languages/Galach/Generators/QueryString.php:13` | Der QueryString-Generator erzeugt eine Abfragezeichenkette im Format Elasticsearch Query String Query. |
| `QueryTranslator\Languages\Galach\Parser` | class implements Parsing | – | `extension/expquery_translator/lib/Languages/Galach/Parser.php:24` | Galach-Implementierung der Schnittstelle Parsing. |
| `QueryTranslator\Languages\Galach\TokenExtractor` | abstract class | [Schablonenmethode / abstrakte Basisklasse](schablonenmethode.md) | `extension/expquery_translator/lib/Languages/Galach/TokenExtractor.php:14` | Der Token-Extraktor wird vom Tokenizer verwendet, um Tokens aus der Eingabezeichenkette zu extrahieren. |
| `QueryTranslator\Languages\Galach\TokenExtractor\Full` | class extends TokenExtractor | – | `extension/expquery_translator/lib/Languages/Galach/TokenExtractor/Full.php:18` | Vollständige Implementierung des Galach-Token-Extraktors. |
| `QueryTranslator\Languages\Galach\TokenExtractor\Text` | class extends TokenExtractor | – | `extension/expquery_translator/lib/Languages/Galach/TokenExtractor/Text.php:17` | Text-Implementierung des Galach-Token-Extraktors. |
| `QueryTranslator\Languages\Galach\Tokenizer` | class implements Tokenizing | – | `extension/expquery_translator/lib/Languages/Galach/Tokenizer.php:11` | Galach-Implementierung der Schnittstelle Tokenizing. |
| `QueryTranslator\Languages\Galach\Values\Node\Group` | class extends Node | – | `extension/expquery_translator/lib/Languages/Galach/Values/Node/Group.php:12` | Implementierung des Group-Node-Visitors. |
| `QueryTranslator\Languages\Galach\Values\Node\LogicalAnd` | class extends Node | – | `extension/expquery_translator/lib/Languages/Galach/Values/Node/LogicalAnd.php:8` | – |
| `QueryTranslator\Languages\Galach\Values\Node\LogicalNot` | class extends Node | – | `extension/expquery_translator/lib/Languages/Galach/Values/Node/LogicalNot.php:8` | – |
| `QueryTranslator\Languages\Galach\Values\Node\LogicalOr` | class extends Node | – | `extension/expquery_translator/lib/Languages/Galach/Values/Node/LogicalOr.php:8` | – |
| `QueryTranslator\Languages\Galach\Values\Node\Mandatory` | class extends Node | – | `extension/expquery_translator/lib/Languages/Galach/Values/Node/Mandatory.php:8` | – |
| `QueryTranslator\Languages\Galach\Values\Node\Prohibited` | class extends Node | – | `extension/expquery_translator/lib/Languages/Galach/Values/Node/Prohibited.php:8` | – |
| `QueryTranslator\Languages\Galach\Values\Node\Query` | class extends Node | – | `extension/expquery_translator/lib/Languages/Galach/Values/Node/Query.php:7` | – |
| `QueryTranslator\Languages\Galach\Values\Node\Term` | class extends Node | – | `extension/expquery_translator/lib/Languages/Galach/Values/Node/Term.php:8` | – |
| `QueryTranslator\Languages\Galach\Values\Token\GroupBegin` | class extends Token | – | `extension/expquery_translator/lib/Languages/Galach/Values/Token/GroupBegin.php:11` | Das GroupBegin-Token stellt die Domäne der Gruppe und das linke Trennzeichen dar. |
| `QueryTranslator\Languages\Galach\Values\Token\Phrase` | class extends Token | – | `extension/expquery_translator/lib/Languages/Galach/Values/Token/Phrase.php:13` | Phrasen-Term-Token. |
| `QueryTranslator\Languages\Galach\Values\Token\Tag` | class extends Token | – | `extension/expquery_translator/lib/Languages/Galach/Values/Token/Tag.php:13` | Tag-Term-Token. |
| `QueryTranslator\Languages\Galach\Values\Token\User` | class extends Token | – | `extension/expquery_translator/lib/Languages/Galach/Values/Token/User.php:13` | Benutzer-Term-Token. |
| `QueryTranslator\Languages\Galach\Values\Token\Word` | class extends Token | – | `extension/expquery_translator/lib/Languages/Galach/Values/Token/Word.php:13` | Wort-Term-Token. |
| `QueryTranslator\Parsing` | interface | [Schnittstelle (Vertrag)](schnittstelle.md) | `extension/expquery_translator/lib/Parsing.php:10` | Schnittstelle zum Parsen einer Folge von Tokens in einen Syntaxbaum. |
| `QueryTranslator\Tokenizing` | interface | [Schnittstelle (Vertrag)](schnittstelle.md) | `extension/expquery_translator/lib/Tokenizing.php:8` | Schnittstelle zum Zerlegen einer Zeichenkette in eine Folge von Tokens. |
| `QueryTranslator\Values\Correction` | class | – | `extension/expquery_translator/lib/Values/Correction.php:11` | Stellt eine Korrektur dar, die beim Parsen der Token-Folge angewendet wurde. |
| `QueryTranslator\Values\Node` | abstract class | – | `extension/expquery_translator/lib/Values/Node.php:10` | Der Knoten ist ein grundlegendes Bauelement des Syntaxbaums. |
| `QueryTranslator\Values\SyntaxTree` | class | – | `extension/expquery_translator/lib/Values/SyntaxTree.php:11` | Der Syntaxbaum ist eine abstrakte hierarchische Darstellung der Abfragesyntax, gedacht für die einfache Umwandlung in verschiedene konkrete Formate. |
| `QueryTranslator\Values\Token` | class | – | `extension/expquery_translator/lib/Values/Token.php:8` | Ein Token stellt eine Zeichenfolge dar, die eine syntaktische Einheit bildet. |
| `QueryTranslator\Values\TokenSequence` | class | – | `extension/expquery_translator/lib/Values/TokenSequence.php:11` | Die Token-Folge enthält ein Array der Tokens, die aus der Abfragezeichenkette extrahiert wurden. |
