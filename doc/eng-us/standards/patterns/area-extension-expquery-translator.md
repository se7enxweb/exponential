---
title: Patterns in extension/expquery_translator
audience: developer
category: standards
order: 426
summary: Every class, view, fetch function, operator and plugin point of extension/expquery_translator with its design patterns.
keywords: extension/expquery_translator, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/expquery_translator

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/expquery_translator` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (53)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expQueryTranslator` | class | – | `extension/expquery_translator/classes/expquerytranslator.php:9` | – |
| `expquery_translatorInfo` | class | [Value object / struct](value-object.md) | `extension/expquery_translator/ezinfo.php:12` | – |
| `QueryTranslator\Languages\Galach\Generators\Common\Aggregate` | class extends Visitor | – | `extension/expquery_translator/lib/Languages/Galach/Generators/Common/Aggregate.php:11` | Common Aggregate Visitor implementation. |
| `QueryTranslator\Languages\Galach\Generators\Common\Visitor` | abstract class | – | `extension/expquery_translator/lib/Languages/Galach/Generators/Common/Visitor.php:10` | Common base class for AST visitor implementations. |
| `QueryTranslator\Languages\Galach\Generators\ExtendedDisMax` | class | – | `extension/expquery_translator/lib/Languages/Galach/Generators/ExtendedDisMax.php:13` | ExtendedDisMax generator generates query string in Solr Extended DisMax query parser format. |
| `QueryTranslator\Languages\Galach\Generators\Lucene\Common\Group` | class extends Visitor | – | `extension/expquery_translator/lib/Languages/Galach/Generators/Lucene/Common/Group.php:14` | Group Node Visitor implementation. |
| `QueryTranslator\Languages\Galach\Generators\Lucene\Common\LogicalAnd` | class extends Visitor | – | `extension/expquery_translator/lib/Languages/Galach/Generators/Lucene/Common/LogicalAnd.php:13` | LogicalAnd operator Node Visitor implementation. |
| `QueryTranslator\Languages\Galach\Generators\Lucene\Common\LogicalNot` | class extends Visitor | – | `extension/expquery_translator/lib/Languages/Galach/Generators/Lucene/Common/LogicalNot.php:13` | LogicalNot operator Node Visitor implementation. |
| `QueryTranslator\Languages\Galach\Generators\Lucene\Common\LogicalOr` | class extends Visitor | – | `extension/expquery_translator/lib/Languages/Galach/Generators/Lucene/Common/LogicalOr.php:13` | LogicalOr operator Node Visitor implementation. |
| `QueryTranslator\Languages\Galach\Generators\Lucene\Common\Mandatory` | class extends Visitor | – | `extension/expquery_translator/lib/Languages/Galach/Generators/Lucene/Common/Mandatory.php:13` | Mandatory operator Node Visitor implementation. |
| `QueryTranslator\Languages\Galach\Generators\Lucene\Common\Phrase` | class extends Visitor | – | `extension/expquery_translator/lib/Languages/Galach/Generators/Lucene/Common/Phrase.php:14` | Phrase Node Visitor implementation. |
| `QueryTranslator\Languages\Galach\Generators\Lucene\Common\Prohibited` | class extends Visitor | – | `extension/expquery_translator/lib/Languages/Galach/Generators/Lucene/Common/Prohibited.php:13` | Prohibited operator Node Visitor implementation. |
| `QueryTranslator\Languages\Galach\Generators\Lucene\Common\Query` | class extends Visitor | – | `extension/expquery_translator/lib/Languages/Galach/Generators/Lucene/Common/Query.php:13` | Query Node Visitor implementation. |
| `QueryTranslator\Languages\Galach\Generators\Lucene\Common\Tag` | class extends Visitor | – | `extension/expquery_translator/lib/Languages/Galach/Generators/Lucene/Common/Tag.php:14` | User Node Visitor implementation. |
| `QueryTranslator\Languages\Galach\Generators\Lucene\Common\User` | class extends Visitor | – | `extension/expquery_translator/lib/Languages/Galach/Generators/Lucene/Common/User.php:14` | User Node Visitor implementation. |
| `QueryTranslator\Languages\Galach\Generators\Lucene\Common\WordBase` | abstract class extends Visitor | [Template Method / abstract base class](template-method.md) | `extension/expquery_translator/lib/Languages/Galach/Generators/Lucene/Common/WordBase.php:14` | Base Word Node Visitor implementation. |
| `QueryTranslator\Languages\Galach\Generators\Lucene\ExtendedDisMax\Word` | class extends WordBase | – | `extension/expquery_translator/lib/Languages/Galach/Generators/Lucene/ExtendedDisMax/Word.php:10` | Word Node Visitor implementation. |
| `QueryTranslator\Languages\Galach\Generators\Lucene\QueryString\Word` | class extends WordBase | – | `extension/expquery_translator/lib/Languages/Galach/Generators/Lucene/QueryString/Word.php:10` | Word Node Visitor implementation. |
| `QueryTranslator\Languages\Galach\Generators\Native` | class | – | `extension/expquery_translator/lib/Languages/Galach/Generators/Native.php:11` | Native Galach generator generates query string in Galach format. |
| `QueryTranslator\Languages\Galach\Generators\Native\BinaryOperator` | class extends Visitor | – | `extension/expquery_translator/lib/Languages/Galach/Generators/Native/BinaryOperator.php:14` | BinaryOperator operator Node Visitor implementation. |
| `QueryTranslator\Languages\Galach\Generators\Native\Group` | class extends Visitor | – | `extension/expquery_translator/lib/Languages/Galach/Generators/Native/Group.php:13` | Group Node Visitor implementation. |
| `QueryTranslator\Languages\Galach\Generators\Native\Phrase` | class extends Visitor | – | `extension/expquery_translator/lib/Languages/Galach/Generators/Native/Phrase.php:14` | Phrase Node Visitor implementation. |
| `QueryTranslator\Languages\Galach\Generators\Native\Query` | class extends Visitor | – | `extension/expquery_translator/lib/Languages/Galach/Generators/Native/Query.php:13` | Query Node Visitor implementation. |
| `QueryTranslator\Languages\Galach\Generators\Native\Tag` | class extends Visitor | – | `extension/expquery_translator/lib/Languages/Galach/Generators/Native/Tag.php:14` | User Node Visitor implementation. |
| `QueryTranslator\Languages\Galach\Generators\Native\UnaryOperator` | class extends Visitor | – | `extension/expquery_translator/lib/Languages/Galach/Generators/Native/UnaryOperator.php:16` | Unary operator Node Visitor implementation. |
| `QueryTranslator\Languages\Galach\Generators\Native\User` | class extends Visitor | – | `extension/expquery_translator/lib/Languages/Galach/Generators/Native/User.php:14` | User Node Visitor implementation. |
| `QueryTranslator\Languages\Galach\Generators\Native\Word` | class extends Visitor | – | `extension/expquery_translator/lib/Languages/Galach/Generators/Native/Word.php:14` | Word Node Visitor implementation. |
| `QueryTranslator\Languages\Galach\Generators\QueryString` | class | – | `extension/expquery_translator/lib/Languages/Galach/Generators/QueryString.php:13` | QueryString generator generates query string in Elasticsearch Query String Query format. |
| `QueryTranslator\Languages\Galach\Parser` | class implements Parsing | – | `extension/expquery_translator/lib/Languages/Galach/Parser.php:24` | Galach implementation of the Parsing interface. |
| `QueryTranslator\Languages\Galach\TokenExtractor` | abstract class | [Template Method / abstract base class](template-method.md) | `extension/expquery_translator/lib/Languages/Galach/TokenExtractor.php:14` | Token extractor is used by Tokenizer to extract tokens from the input string. |
| `QueryTranslator\Languages\Galach\TokenExtractor\Full` | class extends TokenExtractor | – | `extension/expquery_translator/lib/Languages/Galach/TokenExtractor/Full.php:18` | Full implementation of the Galach token extractor. |
| `QueryTranslator\Languages\Galach\TokenExtractor\Text` | class extends TokenExtractor | – | `extension/expquery_translator/lib/Languages/Galach/TokenExtractor/Text.php:17` | Text implementation of the Galach token extractor. |
| `QueryTranslator\Languages\Galach\Tokenizer` | class implements Tokenizing | – | `extension/expquery_translator/lib/Languages/Galach/Tokenizer.php:11` | Galach implementation of the Tokenizing interface. |
| `QueryTranslator\Languages\Galach\Values\Node\Group` | class extends Node | – | `extension/expquery_translator/lib/Languages/Galach/Values/Node/Group.php:12` | Group Node Visitor implementation. |
| `QueryTranslator\Languages\Galach\Values\Node\LogicalAnd` | class extends Node | – | `extension/expquery_translator/lib/Languages/Galach/Values/Node/LogicalAnd.php:8` | – |
| `QueryTranslator\Languages\Galach\Values\Node\LogicalNot` | class extends Node | – | `extension/expquery_translator/lib/Languages/Galach/Values/Node/LogicalNot.php:8` | – |
| `QueryTranslator\Languages\Galach\Values\Node\LogicalOr` | class extends Node | – | `extension/expquery_translator/lib/Languages/Galach/Values/Node/LogicalOr.php:8` | – |
| `QueryTranslator\Languages\Galach\Values\Node\Mandatory` | class extends Node | – | `extension/expquery_translator/lib/Languages/Galach/Values/Node/Mandatory.php:8` | – |
| `QueryTranslator\Languages\Galach\Values\Node\Prohibited` | class extends Node | – | `extension/expquery_translator/lib/Languages/Galach/Values/Node/Prohibited.php:8` | – |
| `QueryTranslator\Languages\Galach\Values\Node\Query` | class extends Node | – | `extension/expquery_translator/lib/Languages/Galach/Values/Node/Query.php:7` | – |
| `QueryTranslator\Languages\Galach\Values\Node\Term` | class extends Node | – | `extension/expquery_translator/lib/Languages/Galach/Values/Node/Term.php:8` | – |
| `QueryTranslator\Languages\Galach\Values\Token\GroupBegin` | class extends Token | – | `extension/expquery_translator/lib/Languages/Galach/Values/Token/GroupBegin.php:11` | GroupBegin token represents group's domain and left side delimiter. |
| `QueryTranslator\Languages\Galach\Values\Token\Phrase` | class extends Token | – | `extension/expquery_translator/lib/Languages/Galach/Values/Token/Phrase.php:13` | Phrase term token. |
| `QueryTranslator\Languages\Galach\Values\Token\Tag` | class extends Token | – | `extension/expquery_translator/lib/Languages/Galach/Values/Token/Tag.php:13` | Tag term token. |
| `QueryTranslator\Languages\Galach\Values\Token\User` | class extends Token | – | `extension/expquery_translator/lib/Languages/Galach/Values/Token/User.php:13` | User term token. |
| `QueryTranslator\Languages\Galach\Values\Token\Word` | class extends Token | – | `extension/expquery_translator/lib/Languages/Galach/Values/Token/Word.php:13` | Word term token. |
| `QueryTranslator\Parsing` | interface | [Interface (contract)](interface.md) | `extension/expquery_translator/lib/Parsing.php:10` | Interface for parsing a sequence of tokens into a syntax tree. |
| `QueryTranslator\Tokenizing` | interface | [Interface (contract)](interface.md) | `extension/expquery_translator/lib/Tokenizing.php:8` | Interface for tokenizing a string into a sequence of tokens. |
| `QueryTranslator\Values\Correction` | class | – | `extension/expquery_translator/lib/Values/Correction.php:11` | Represents a correction applied during parsing of the token sequence. |
| `QueryTranslator\Values\Node` | abstract class | – | `extension/expquery_translator/lib/Values/Node.php:10` | Node is a basic building element of the syntax tree. |
| `QueryTranslator\Values\SyntaxTree` | class | – | `extension/expquery_translator/lib/Values/SyntaxTree.php:11` | Syntax tree is an abstract hierarchical representation of the query syntax, intended for easy conversion into different concrete formats. |
| `QueryTranslator\Values\Token` | class | – | `extension/expquery_translator/lib/Values/Token.php:8` | Token represents a sequence of characters which forms a syntactic unit. |
| `QueryTranslator\Values\TokenSequence` | class | – | `extension/expquery_translator/lib/Values/TokenSequence.php:11` | Token sequence holds an array of tokens extracted from the query string. |
