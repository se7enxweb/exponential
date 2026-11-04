Sprachumschalter
================

Mit dem Sprachumschalter wechselt man von der Ansicht einer Übersetzung einer
Site zu einer anderen. Heute geschieht das durch den Wechsel in einen anderen
Übersetzungs-Siteaccess (es geht hier nicht um den Benutzerparameter locale).

**Problem**: Das ist heute schwierig mit dem mehrsprachigen URL-Alias-System,
denn die angefragte URI lässt sich in einem anderen Übersetzungs-Siteaccess
nicht wiederverwenden: Dort ist die URL für einen bestimmten Knoten eine
andere, nämlich in eine andere Sprache übersetzt.

Umfang
------

Unten sind mehrere Ansätze aufgeführt. Der erste zeigt ein Beispiel, in dem die
URLs praktisch von Hand gebildet werden, nur unterstützt durch die
URL-Umwandlung. Die späteren Ansätze sind viel stärker automatisiert: Das
System erzeugt alle nötigen Daten für den Benutzer selbst.

Beide Wege haben ihren Wert: Manche Einrichtungen brauchen volle Kontrolle
über das Aussehen der URLs, in vielen anderen Fällen ist einfache Bedienung
wichtiger.

Die Funktion wird in mindestens zwei Phasen umgesetzt.

Phase 1
~~~~~~~

In der ersten Phase wird die API von eZURLAliasML erweitert, sodass sich ein
URL-Alias für vom Benutzer gewählte Locales abrufen lässt. Im zweiten Teil
dieser Phase entsteht ein Modul, das Entwickler in Templates verwenden können,
um zum Ziel weiterzuleiten, also zum anderen Übersetzungs-Siteaccess mit
übersetztem URL-Alias.

Ein Modul ist hier nützlich, denn es ermöglicht die effizienteste Suche der
Daten, und zwar nur, wenn sie gebraucht werden. Das vermeidet zusätzliche
Abfragen und möglicherweise überflüssige Caches je Seite; sonst bräuchte man
für jede URL je Übersetzungs-Siteaccess einen eigenen Link. Das lässt sich mit
einem einfachen Modul vermeiden.

Phase 1.5
~~~~~~~~~

Auch in den Templates soll es eine Methode geben, URL-Aliase je nach
Übersetzung abzufragen. Auf URLs in verschiedenen Übersetzungen verweisen zu
können, brauchen viele mehrsprachige Sites.

Phase 2
~~~~~~~

Die letzte Phase soll das Einrichten von Links zu Übersetzungs-Siteaccesses
automatisieren, sodass weniger Eingaben von Hand nötig sind (vgl. die
Einrichtung der Übersetzungen in eZWebin heute). Ein Beispiel steht in
`Ansatz 3`_.

Ansatz 1
~~~~~~~~

::

    {if $curr_siteaccess|eq( "eng")}
        <span>English</span>
    {else}
        <a href=concat( "/switchlanguage/to/", "eng/", choose( $module_result.node_id, $site.url ))>English</a>
    {/if}


Ansatz 2
~~~~~~~~

::

    {def $class="lang"}
    {foreach $languages as $lang}
        {set $class="lang"}
        {if $current_siteaccess|eq( $lang.sa )}
            {set $class="selected"}
        {/if}
        <a class="{$lang}" href=lang_url( $module_result.node_id, {$lang.sa}, {$lang.locale} ) )|ezurl>{$lang.text}</a>
    {/foreach}


Ansatz 3
~~~~~~~~

::

    {def $langSwitcher=switchLang()}

    {foreach $langSwitcher as $lang}
        <a href={$lang.url}>{$lang.text}</a>
    {/foreach}


Änderungen an eZURLAliasML
--------------------------

In den betroffenen Methoden kommt ein neuer Parameter *$locale* hinzu. Fehlt
er, verhalten sich die Methoden wie bisher. Ist er angegeben, werden nach
dieser Locale gefilterte Ergebnisse berücksichtigt.

Geänderte Methoden:
~~~~~~~~~~~~~~~~~~~

**eZURLAliasML:**

- fetchByAction

  Für diese Methode wurde der vorhandene Parameter *$maskLanguages*
  wiederverwendet. Bei Wahrheitswerten greift die alte Logik, bei
  Zeichenketten werden die Einträge mit der Sprachmaske der Locale gefiltert.

  *$onlyprioritized* wirkt nicht, wenn in *$maskLanguages* eine Locale
  angegeben ist.

- getPath

  Neuer Parameter $locale. Wird $locale verwendet, ersetzt er die
  Standardfilterung in der Methode choosePriortizedRow().

- fetchPathByActionList

  Parameter $locale hinzugefügt, mit Rückfall auf das normale Verhalten.

Zu prüfende Methoden:
~~~~~~~~~~~~~~~~~~~~~

- fetchByPath

  Kann entfallen, da die Methode ein eZURLAliasML-Objekt für einen gegebenen
  Pfad abruft.

- fetchNodeIDByPath

  Kann ebenfalls entfallen; die Locale ergibt sich hier aus dem gegebenen
  Pfad.

- fetchByParentID

  Kann entfallen. Es geht nicht darum, beliebige URL-Elemente einer Locale
  abzurufen, sondern ausgewählte URL-Aliase in verschiedenen
  Übersetzungen/Locales.

- getChildren

  Entfällt, siehe den Kommentar zu fetchByParentID

**eZContentObjectTreeNode: (könnte geprüft werden)**

- urlAlias
- pathWithNames

Aufgaben
--------

- Die URL eines Siteaccesses für einen gegebenen SA berechnen

  Erschwert dadurch, dass es keinen guten Weg gibt, vollständige URLs von
  Siteaccesses zu berechnen, besonders über verschiedene Zuordnungsregeln
  hinweg.

- Die angefragte URL in die URL der gewünschten Übersetzung umwandeln

  Geschieht über die geänderte API.

- Zur endgültigen Ziel-URL des SA weiterleiten

- Die Zuordnung zwischen SA und Locale der Übersetzung einrichten.
  Wird für automatische Ansätze wie `Ansatz 3`_ verwendet.

- Nach den Änderungen abhängige Klassen auf Probleme prüfen
  - eZURLAliasML
  - eZURLAliasQuery
  - eZPathElement

Optimierungen
-------------

Einige Optimierungen sind möglich. Eine ist, wo vorhanden, die *Knoten-ID*
direkt aus den Templates zu verwenden; das spart eine Suche des Pfads bzw. der
angefragten URI.

(Nachtrag: das ist jetzt umgesetzt)

Einschränkungen
---------------

Zunächst wird nur ein Teil der möglichen Siteaccess-Typen unterstützt, nämlich
*URI* und *host*.
