# Unterstützung für PHP 7

## Unterstützung für PHP 7.0

Für das [Release 2017.10](https://github.com/ezsystems/exponential-legacy/releases/tag/v2017.10.0) wurde Exponential
an vielen Stellen der Codebasis geändert, wobei die Konstruktormethoden der Objekte auf den Stil von PHP 5 umgestellt
wurden.

Der Grund ist volle Unterstützung für PHP 7.0, indem die Warnungen über veraltete Konstruktoren im Stil von PHP 4
vermieden werden.

Es wurde darauf geachtet, in allen bekannten Fällen Kompatibilitätsfunktionen beizubehalten, um fatale Fehler in
eigenen Erweiterungen zu vermeiden; um Warnungen zu vermeiden, müssen Sie Ihren Code unter Umständen aber ebenfalls
anpassen.

Häufige Fälle sind Klassen, die `eZPersistentObject` oder `eZDataType` erweitern.

Hier ist ein Beispiel dafür, wie Sie Ihren Code an die Änderung der Konstruktoren anpassen sollten:

```diff
diff --git a/classes/ezfindresultnode.php b/classes/ezfindresultnode.php
index fafca310..b6462159 100644
--- a/classes/ezfindresultnode.php
+++ b/classes/ezfindresultnode.php
@@ -12,7 +12,7 @@ class eZFindResultNode extends eZContentObjectTreeNode
     */
     function eZFindResultNode( $rows = array() )
     {
-        $this->eZContentObjectTreeNode( $rows );
+        parent::__construct( $rows );
         if ( isset( $rows['id'] ) )
         {
             $this->ContentObjectID = $rows['id'];
```

Am besten stellen Sie auch Ihren eigenen Code auf Konstruktoren im Stil von PHP 5 um. Im obigen Beispiel hieße das,
`function eZFindResultNode` in `function __construct` umzubenennen und, falls Sie annehmen, dass anderer Code die
Klasse `eZFindResultNode` erweitert, aus Gefälligkeit wieder eine Funktion `eZFindResultNode` hinzuzufügen, die nichts
anderes tut, als `__construct` aufzurufen.

Weiterführende Lektüre:
- http://php.net/manual/en/language.oop5.decon.php
- https://www.php.net/manual/en/migration70.incompatible.php
- https://www.php.net/manual/en/migration71.incompatible.php

## Unterstützung für PHP 7.2

Ab dem Release 2019.03 sind Probleme unter PHP 7.2 und PHP 7.3 behoben, aber in Ihrem eigenen Code müssen Sie einige
davon ebenfalls behandeln.

Am auffälligsten ist die in PHP 7.2 hinzugekommene Änderung `Warn when counting non-countable types`.

Um das in allen unterstützten PHP-Versionen zu behandeln, haben wir das Paket
[symfony/polyfill-php73](https://github.com/symfony/polyfill-php73) eingeführt, das die Funktion
[is_countable](https://www.php.net/is_countable) von PHP 7.3 zurückportiert.

Hier ist ein Beispiel für Änderungen, die Sie in Ihrem eigenen Code vornehmen müssen, um das zu umgehen:

```diff
diff --git a/kernel/common/eztemplatedesignresource.php b/kernel/common/eztemplatedesignresource.php
index b0fc28faa9a..9b8ca2a8d94 100644
--- a/kernel/common/eztemplatedesignresource.php
+++ b/kernel/common/eztemplatedesignresource.php
@@ -86,7 +86,7 @@ function templateNodeTransformation( $functionName, &$node,
                 $matchCount = 0;
                 foreach ( $customMatchList as $customMatch )
                 {
-                    $matchConditionCount = count( $customMatch['conditions'] );
+                    $matchConditionCount = is_countable( $customMatch['conditions'] ) ? count( $customMatch['conditions'] ) : 0;
                     $code = '';
                     if ( $matchCount > 0 )
                     {
```

Weiterführende Lektüre:
- https://www.php.net/manual/en/migration72.incompatible.php
- https://www.php.net/manual/en/migration73.incompatible.php
