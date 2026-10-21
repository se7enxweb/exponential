---
title: "Lektion 1.4: Ein Skript mit geladenem Kernel"
audience: trainer, developer
category: training
order: 14
summary: Die Teilnehmenden legen ihren ersten Erweiterungsordner unter Git an, schreiben ein kleines PHP-Skript, das Einstellungen und Inhalte liest, und führen es mit ezexec.php aus, damit der Kernel für sie geladen wird. 45 Minuten.
keywords: entwickler, ezexec, eZINI, eZCLI, eZContentObjectTreeNode, knoten holen, php -l, mysite_hello, erstes skript, erweiterungsordner, developers course, fetch node, first script, extension folder
type: help
---
# Lektion 1.4: Ein Skript mit geladenem Kernel

Am schnellsten lernt man die Klassen des Kernels kennen, indem man sie aufruft. `bin/php/ezexec.php` lädt den Kernel
(Einstellungen, Datenbank, Autoloads) und führt dann eine beliebige PHP-Datei aus. In dieser Lektion legen die
Teilnehmenden den Erweiterungsordner an, den sie für den Rest des Kurses verwenden, und schreiben ein Skript, das eine
Einstellung und einen Knoten liest.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 1: Die Entwicklungsinstallation](README.md) |
| Dauer | 45 Minuten (10 Präsentation, 35 Übung) |
| Niveau | Einsteiger |
| Zielgruppe | PHP-Entwickler, die neu bei Exponential sind |
| Voraussetzungen | [Lektion 1.3](debug-ausgabe-und-logs.md); ein Texteditor |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- einen Erweiterungsordner `extension/mysite_hello` anlegen und unter Git stellen;
- ein Skript schreiben, das `eZINI`, `eZCLI` und `eZContentObjectTreeNode` verwendet;
- eine PHP-Datei mit `php -l` prüfen, bevor sie ausgeführt wird;
- ein Skript mit `php bin/php/ezexec.php <file>` ausführen und seine Ausgabe lesen.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Ihre erste Stunde als Entwickler](../../../erweiterungsentwicklung/teil-1-vom-benutzer-zum-entwickler/ihre-erste-stunde-als-entwickler.md),
Übung 6.

1. **Die Kurserweiterung** (3 Minuten). Alle Teilnehmenden bauen im Laufe des Kurses eine Erweiterung,
   `mysite_hello`. Sie ist noch nicht aktiv; Modul 7 schaltet sie ein. Ein Skript, das mit `ezexec.php` läuft, braucht
   keine Aktivierung.
2. **Drei Klassen** (4 Minuten). `eZINI::instance( 'site.ini' )` liest Einstellungen mit allen angewendeten
   Overrides; `eZCLI::instance()` schreibt ins Terminal; `eZContentObjectTreeNode::fetch( $nodeID )` lädt einen
   Knoten (einen Platz im Inhaltsbaum).
3. **Zuerst prüfen** (1 Minute). `php -l` ist die Kompilierprüfung vor allem anderen.
4. **Ausführen** (2 Minuten). `php bin/php/ezexec.php extension/mysite_hello/scripts/hello.php`.

### Häufige Fragen

**Was ist der Unterschied zwischen einem Knoten und einem Objekt?** Ein Objekt ist der Inhalt (mit Versionen und
Übersetzungen); ein Knoten ist einer seiner Plätze im Baum. Die meisten Templates und Fetches arbeiten mit Knoten.
Modul 4 geht tiefer darauf ein.

**Darf ein so ausgeführtes Skript Inhalte ändern?** Es läuft mit den vollen Rechten des Kernels. In diesem Kurs lesen
Skripte bis Modul 9 nur.

**Wie übergebe ich dem Skript Optionen?** Skripte, die Optionen brauchen, werden als Konsolenbefehle geschrieben;
Lektion 9.2 zeigt, wie.

### Stolperfallen

- **Eine Datei außerhalb der Installation.** Legen Sie Skripte in der Erweiterung ab; Pfade in den Beispielen sind
  relativ zum Wurzelverzeichnis der Installation.
- **Als root ausführen.** Fügen Sie nichts hinzu: Arbeiten Sie als eigener Benutzer. Als root halten Skripte mit einer
  Warnung an, solange `--allow-root-user` nicht angegeben ist.
- **Knoten 2 ist nicht immer die Startseite.** Im Schulungspaket ist Knoten 2 die Spitze des Inhaltsbaums. Die
  Teilnehmenden können die ID in der Administration prüfen.

## Praktische Übung

Auf der Entwicklungsinstallation, aus ihrem Wurzelverzeichnis.

1. Legen Sie den Erweiterungsordner und sein Git-Repository an:

   ```bash
   mkdir -p extension/mysite_hello/scripts
   git -C extension/mysite_hello init
   ```

2. Legen Sie `extension/mysite_hello/scripts/hello.php` mit diesem Inhalt an:

   ```php
   <?php
   // Run: php bin/php/ezexec.php extension/mysite_hello/scripts/hello.php
   $cli = eZCLI::instance();
   $ini = eZINI::instance( 'site.ini' );
   $cli->output( 'Site name: ' . $ini->variable( 'SiteSettings', 'SiteName' ) );
   $cli->output( 'Active extensions: ' . count( $ini->variable( 'ExtensionSettings', 'ActiveExtensions' ) ) );

   $node = eZContentObjectTreeNode::fetch( 2 );
   if ( $node instanceof eZContentObjectTreeNode )
   {
       $cli->output( 'Node 2: ' . $node->attribute( 'name' ) . ' (class ' . $node->attribute( 'class_identifier' ) . ')' );
       $cli->output( 'Children: ' . $node->subTreeCount( array( 'Depth' => 1 ) ) );
   }
   else
   {
       $cli->error( 'Node 2 was not found' );
   }
   ```

3. Prüfen Sie die Syntax: `php -l extension/mysite_hello/scripts/hello.php`.
4. Führen Sie es aus: `php bin/php/ezexec.php extension/mysite_hello/scripts/hello.php`.
5. Vergleichen Sie den Namen der Website mit `php bin/php/console exp:ini get site.ini/SiteSettings/SiteName`.
6. Ändern Sie das Skript so, dass es die Knoten-ID eines Artikels holt, den Sie in der Administration sehen, und führen
   Sie es erneut aus.
7. Fügen Sie am Ende die Zeile `eZDebug::writeError( 'Test error from hello.php', 'mysite_hello' );` hinzu, führen
   Sie das Skript aus und suchen Sie die Zeile in `var/log/error.log`.
8. Entfernen Sie die Zeile aus Schritt 7 wieder.
9. Committen Sie das Skript im eigenen Repository der Erweiterung:

   ```bash
   git -C extension/mysite_hello add scripts/hello.php
   git -C extension/mysite_hello commit -m "Added: First script that reads settings and a node"
   ```

## Erwartetes Ergebnis

- Schritt 3 gibt `No syntax errors detected` aus.
- Schritt 4 gibt den Namen der Website, die Zahl der aktiven Erweiterungen, Namen und Klasse von Knoten 2 und die
  Zahl seiner Kinder aus.
- Schritt 7: `var/log/error.log` endet mit einer Zeile, die `Test error from hello.php` und die Kennzeichnung
  `mysite_hello` enthält.
- Schritt 9: `git -C extension/mysite_hello log --oneline` zeigt einen Commit.
- Der Trainer prüft die Ausgabe von Schritt 4 auf den Bildschirmen von zwei Teilnehmenden.

## Quiz

1. Was tut `bin/php/ezexec.php`, bevor es Ihre Datei ausführt?
2. Welche Klasse liest Einstellungen mit allen angewendeten Overrides?
   A. `eZCLI` B. `eZINI` C. `eZDB` D. `eZScript`
3. Was ist der Unterschied zwischen einem Inhaltsobjekt und einem Knoten?
4. Welcher Befehl prüft die Syntax einer PHP-Datei, ohne sie auszuführen?
5. Warum liegt die Kurserweiterung in einem eigenen Git-Repository und nicht in dem des Kernels?

<details>
<summary>Antworten</summary>

1. Es lädt den Kernel: Einstellungen, Datenbankverbindung und Autoloads.
2. **B.** `eZINI::instance( 'site.ini' )`.
3. Das Objekt ist der Inhalt mit seinen Versionen und Übersetzungen; ein Knoten ist einer seiner Plätze im
   Inhaltsbaum.
4. `php -l <file>`.
5. Erweiterungen sind eigene Repositories; der Kernel wird über Composer und Git aktualisiert, und Ihre Arbeit wird in
   Ihrer eigenen Erweiterung committet ([Eine Entwicklungsinstallation einrichten](../../../erweiterungsentwicklung/teil-1-vom-benutzer-zum-entwickler/eine-entwicklungsinstallation-einrichten.md)).

</details>

## Weiterführende Literatur

- [Ihre erste Stunde als Entwickler](../../../erweiterungsentwicklung/teil-1-vom-benutzer-zum-entwickler/ihre-erste-stunde-als-entwickler.md):
  sechs kurze Übungen zu Einstellungen, Templates, Debug-Ausgabe und Skripten.
- [Die Werkzeuge, die Sie brauchen](../../../erweiterungsentwicklung/teil-1-vom-benutzer-zum-entwickler/werkzeuge-die-sie-brauchen.md): PHP,
  Composer, Git, ein Editor und die Entwicklerwerkzeuge des Browsers.

## Verwandte Seiten

- [Modul 1: Die Entwicklungsinstallation](README.md)
- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
