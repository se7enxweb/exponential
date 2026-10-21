---
title: "Lektion 4.1: ezfind und einen Solr-Server installieren"
audience: trainer, admin, developer, hosting
category: training
order: 41
summary: Die Teilnehmenden fügen ihrer Schulungsinstallation die Erweiterung ezfind mit Composer hinzu, aktivieren sie, legen ihre Tabelle an, starten den von der Erweiterung mitgelieferten Solr-Server und bestätigen, dass die Website-Suche jetzt über ezsolr läuft. 35 Minuten.
keywords: suchkurs, ezfind, installieren, composer require, se7enxweb/ezfind, ActiveExtensions, ezfind_elevate_configuration, Solr, start.jar, ezp-default, Java, search course, install
type: help
---
# Lektion 4.1: ezfind und einen Solr-Server installieren

Diese Lektion macht aus einer Schulungsinstallation mit der eingebauten Suchmaschine eine, die mit Solr sucht. Die
Teilnehmenden folgen den dokumentierten Schritten der Reihe nach, weil jeder vom vorherigen abhängt: das Paket, die
Autoloads, die Aktivierung, die Tabelle, der Solr-Server, die Caches.

> **Hinweis**
> Tun Sie das nur auf Ihrer eigenen Schulungsinstallation. Auf einer Live-Website ist das Hinzufügen einer Suchmaschine
> eine geplante Änderung mit einem Probelauf auf einer Kopie, wie in [Lektion 1.4](../modul-01-wie-die-suche-funktioniert/ein-such-backend-waehlen.md).

## Auf einen Blick

| | |
|---|---|
| Kurs | [Suche](../README.md) |
| Modul | [Modul 4: Solr und ezfind](README.md) |
| Dauer | 35 Minuten (10 Präsentation, 25 Übung) |
| Niveau | Experten |
| Zielgruppe | Website-Administratoren, Entwickler, Hosting-Mitarbeitende |
| Voraussetzungen | [Modul 2](../modul-02-indizierung/README.md); eine Schulungsinstallation auf MySQL oder PostgreSQL; Composer, Netzwerkzugang und eine Java-Laufzeitumgebung auf dem Rechner |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die Erweiterung mit `composer require se7enxweb/ezfind:~3.0.7` hinzufügen und die Autoloads neu erzeugen;
- sie mit `exp:ini add` in `[ExtensionSettings] ActiveExtensions[]` aktivieren;
- die Tabelle der Erweiterung aus ihrer Schemadatei anlegen und sagen, wofür sie dient;
- den in `extension/ezfind/java` mitgelieferten Solr-Server starten und prüfen, dass er antwortet;
- mit `exp:ini get` bestätigen, dass `[SearchSettings] SearchEngine` jetzt `ezsolr` ist.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Suche mit ezfind und Solr](../../../ersteller/website-ersteller/suche-mit-ezfind-und-solr.md#einschalten),
[Such-Backends](../../../hosting/grundlagen/such-backends.md#solr-über-ez-find) und nach der Installation die eigene
`extension/ezfind/doc/INSTALL.md` der Erweiterung.

1. **Das Paket** (2 Minuten). ezfind ist ein Composer-Paket, das die `composer.json` der Installation vorschlägt:
   `composer require se7enxweb/ezfind:~3.0.7`, dann `php bin/php/ezpgenerateautoloads.php -e`.
2. **Aktivierung** (2 Minuten). `ActiveExtensions[]=ezfind` in `settings/override/site.ini.append.php`. Ihre eigene
   `site.ini.append.php` setzt dann `SearchEngine=ezsolr`. Die Hinweise der Erweiterung verlangen, dass sie vor den
   Design-Erweiterungen aufgeführt wird, deren Templates sie überschreiben soll.
3. **Die Tabelle** (2 Minuten). Die Erweiterung fügt eine Tabelle hinzu, `ezfind_elevate_configuration`, die von der
   Elevation verwendet wird (Lektion 4.4). Sie liefert Schemadateien für MySQL, PostgreSQL und Oracle in
   `extension/ezfind/sql/` mit; deshalb verwendet die Übungsumgebung dieses Moduls MySQL oder PostgreSQL statt des
   standardmäßigen SQLite.
4. **Der Solr-Server** (4 Minuten). Die installierte Kopie enthält ein Solr in `extension/ezfind/java` mit einem Core
   namens `ezp-default`, dem Core, auf den `solr.ini [SolrBase] SearchServerURI` von ezfind zeigt
   (`http://localhost:8983/solr/ezp-default`). Ihre `ezinfo.php` nennt die mitgelieferte Solr-Version; lesen Sie sie
   in Ihrer Kopie nach. Die Hinweise der Erweiterung starten es mit `java -Dezfind -jar start.jar` aus diesem Ordner
   und verlangen eine Java-Laufzeitumgebung; proben Sie vor dem Kurs mit der Java-Version Ihrer Schulungsrechner.

### Häufige Fragen

**Müssen wir das mitgelieferte Solr verwenden?** Nein. `SearchServerURI` kann auf jedes Solr mit einem für ezfind
konfigurierten Core zeigen, und `[SolrSettings] Url` des Kernels lässt `exp:solr` es prüfen (Lektion 4.2). Welche
Solr-Versionen ezfind unterstützt, ist eine Frage für das eigene Repository der Erweiterung.

**Was, wenn die Website SQLite verwendet?** Die Erweiterung liefert keine SQLite-Schemadatei mit. Verwenden Sie für
diese Übungsumgebung MySQL oder PostgreSQL, oder fragen Sie die Betreuer der Erweiterung.

**Kann ein Solr mehrere Installationen bedienen?** Die Hinweise der Erweiterung sagen ja; jede Installation hat ihre
eigene Installations-ID im Index.

### Stolperfallen

- **Die Autoloads vergessen.** Ohne `ezpgenerateautoloads.php -e` werden die Klassen der Erweiterung nicht gefunden,
  und die Suche scheitert mit einem Fehler. Das ist der häufigste Fehler in dieser Lektion.
- **Solr läuft nicht.** Mit aktivem ezfind und ohne Solr scheitert die Website-Suche. Starten Sie Solr vor dem Testen
  und lassen Sie das Terminal, in dem es läuft, offen.
- **Dateien, die root gehören.** Wenn Solr als root läuft, gehören seine Daten- und Logdateien root. Auf einem
  Schulungsrechner ist das hinnehmbar; auf einem Server führen Sie Solr als eigenen Benutzer aus.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation auf MySQL oder PostgreSQL (zum Beispiel installiert mit
`php bin/php/console exp:install --db=mysql`, siehe [Installation über die Kommandozeile](../../../../installation/07-installation-ueber-die-konsole.md))
und zwei Terminals in ihrem Wurzelverzeichnis. Fügen Sie `--allow-root-user` nur hinzu, wenn Sie als root arbeiten.

1. Fügen Sie das Paket hinzu:

   ```bash
   composer require se7enxweb/ezfind:~3.0.7
   ```

2. Erzeugen Sie die Autoloads der Erweiterungen neu:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   ```

3. Aktivieren Sie die Erweiterung und leeren Sie den INI-Cache:

   ```bash
   php bin/php/console exp:ini add "site.ini/ExtensionSettings/ActiveExtensions[]" ezfind global
   php bin/php/ezcache.php --clear-tag=ini
   ```

4. Legen Sie die Tabelle mit dem Client Ihrer Datenbank aus der Datei für Ihre Datenbank an, zum Beispiel für MySQL:

   ```bash
   mysql -u <database user> -p <database name> < extension/ezfind/sql/mysql/schema.sql
   ```

5. Starten Sie im zweiten Terminal das mitgelieferte Solr und lassen Sie es laufen:

   ```bash
   cd extension/ezfind/java && java -Dezfind -jar start.jar
   ```

6. Geben Sie im ersten Terminal die wirksame Suchmaschine und die Adresse aus, die ezfind verwenden wird:

   ```bash
   php bin/php/console exp:ini get site.ini/SearchSettings/SearchEngine
   php bin/php/console exp:ini get solr.ini/SolrBase/SearchServerURI
   ```

7. Leeren Sie alle Caches, damit die Templates der Erweiterung gefunden werden:

   ```bash
   php bin/php/ezcache.php --clear-all
   ```

8. Suchen Sie in der Administration nach einem Wort aus den Beispielinhalten. Notieren Sie das Ergebnis (Lektion 4.3
   baut den Index auf).

## Erwartetes Ergebnis

- Schritt 1 fügt `extension/ezfind` hinzu; Schritt 2 endet ohne Fehler.
- Schritt 3 schreibt die Zeile in `settings/override/site.ini.append.php` (`exp:ini` nennt die Datei).
- Schritt 5 gibt das Start-Log von Solr aus und läuft weiter.
- Schritt 6 gibt `ezsolr` und `http://localhost:8983/solr/ezp-default` aus.
- Schritt 8 läuft ohne Fehler, findet aber wenig oder nichts: Der Solr-Index ist noch leer.
- Der Trainer prüft Schritt 6 auf jedem Rechner, bevor es weitergeht.

## Quiz

1. Bringen Sie diese Schritte in die richtige Reihenfolge: die Erweiterung aktivieren, die Tabelle anlegen,
   `composer require`, Autoloads neu erzeugen, Solr starten.
2. Was ändert ezfind in `[SearchSettings]`, wenn es aktiv ist?
3. Wofür ist die Tabelle `ezfind_elevate_configuration`?
   A. den Suchindex B. Elevationskonfigurationen C. Suchstatistiken D. die Warteschlange der verzögerten Indizierung
4. Warum verwendet diese Übungsumgebung MySQL oder PostgreSQL?
5. Eine Suche scheitert direkt nach dem Aktivieren von ezfind. Nennen Sie zwei wahrscheinliche Ursachen.

<details>
<summary>Antworten</summary>

1. `composer require`, Autoloads neu erzeugen, aktivieren, die Tabelle anlegen, Solr starten (Tabelle und Solr können
   die Plätze tauschen).
2. `SearchEngine=ezsolr` (sowie `SearchViewHandling=template`, `AllowEmptySearch=enabled`, `EnableWildcard=true`).
3. **B.**
4. Die Erweiterung liefert Schemadateien für MySQL, PostgreSQL und Oracle mit, nicht für SQLite.
5. Zwei beliebige aus: Autoloads nicht neu erzeugt; Solr läuft nicht; Caches nicht geleert; `SearchServerURI` zeigt
   woandershin.

</details>

## Weiterführende Informationen

- [Suche mit ezfind und Solr](../../../ersteller/website-ersteller/suche-mit-ezfind-und-solr.md): die Schritte zum
  Einschalten für Website-Ersteller.
- [Composer und Git auf Servern](../../../hosting/grundlagen/composer-und-git-auf-servern.md): Pakete auf einem Server
  hinzufügen.
- [Kapitel 3: Den Code beschaffen](../../../../installation/03-den-code-beschaffen.md): Erweiterungen als Composer-Pakete.

## Verwandte Seiten

- [Modul 4: Solr und ezfind](README.md)
- [Suche: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
