Reihenfolge der Erweiterungen
=============================

Die Reihenfolge, in der aktive Erweiterungen in
*site.ini/[ExtensionSettings]/ActiveExtensions* stehen, ist für die Auswahl der
Templates und möglicherweise der Konfigurationseinstellungen wichtig. Deshalb
müssen Benutzer genau darauf achten, dass Erweiterungen, die andere kennen, in
der richtigen Reihenfolge stehen.

**Problem**: Heute bietet die Administrationsoberfläche keine Möglichkeit, sie
von Hand oder automatisch umzusortieren, und die richtige Reihenfolge ist bei
vielen Erweiterungen manchmal schwer zu bestimmen.

Umfang
------

Mit Metadaten zur Reihenfolge kann Exponential die richtige Reihenfolge selbst
bestimmen. Ziel ist derzeit weder die Prüfung von Abhängigkeiten oder
Voraussetzungen noch das automatische Aktivieren einer Erweiterung.

Metadaten
---------

Metadaten zur Reihenfolge der Erweiterungen sind optional. Sie stehen in einer
Datei *extension.xml* im Wurzelverzeichnis der Erweiterung und sollten so
aufgebaut sein:

::

    extension/example/extension.xml
    <?xml version="1.0" encoding="utf-8" ?>
    <software>
        <dependencies>
            <requires>
                <extension name="extension1" />
                <extension name="extension2" />
            </requires>
            <extends>
                <extension name="extension3" />
            </extends>
        </dependencies>
    </software>

Das legt fest, dass die Erweiterung *example* **nach** *extension1* und
*extension2* geladen wird, weil sie diese **braucht** (requires), während
*extension3* **vorher** geladen wird, weil *example* sie **erweitert** (extends).

Algorithmus der Reihenfolge
---------------------------

Der Weg ist die **topologische Sortierung**: Aus den Angaben der
Metadaten-Dateien entsteht ein gerichteter Graph. Für das Beispiel oben ergibt
sich dieser Graph:

::

    extension1
              \
               \
                ->example ---> extension3
               /
              /
    extension2

Die Auflösung geschieht so:

1. Eine Liste der zu ladenden Erweiterungen anlegen.

2. Eine leere Liste für die Reihenfolge anlegen.

3. Die Erweiterungen ohne Abhängigkeiten durchlaufen.

4. Sie der geordneten Liste hinzufügen.

5. Sie aus dem Graphen und aus der Liste der zu ladenden Erweiterungen
   entfernen.

6. a) Bei 3. fortfahren, bis die Liste der zu ladenden Erweiterungen leer ist.
   b) Ist die Liste der Erweiterungen ohne Abhängigkeiten leer, gibt es einen
      Zyklus.

Der Algorithmus steckt in der Klasse *ezpTopologicalSort*, die intern die
Klasse *ezpTopologicalSortNode* für die Knoten des Graphen verwendet.
