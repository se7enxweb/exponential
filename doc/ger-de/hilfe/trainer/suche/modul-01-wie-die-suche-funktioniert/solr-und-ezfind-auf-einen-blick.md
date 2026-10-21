---
title: "Lektion 1.3: Solr und ezfind auf einen Blick"
audience: trainer, admin, developer
category: training
order: 13
summary: Die Teilnehmenden beschreiben, was die optionale Erweiterung ezfind und ein Solr-Server einer Installation hinzufügen, welche Teile beteiligt sind und wie eine Installation zeigt, welche Suchmaschine sie verwendet. 25 Minuten.
keywords: suchkurs, Solr, ezfind, eZ Find, ezsolr, solr.ini, exp:solr, relevanz, facetten, optionaler suchserver, search course, relevance, facets, optional search server
type: help
---
# Lektion 1.3: Solr und ezfind auf einen Blick

Die Erweiterung ezfind verbindet Exponential mit Apache Solr, einem eigenen Suchserver. Sie ist optional: Nichts
ändert sich, bis sie installiert und eingeschaltet ist. Diese Lektion gibt den Teilnehmenden den Überblick; Modul 4
geht in die Praxis.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Suche](../README.md) |
| Modul | [Modul 1: Wie die Suche funktioniert](README.md) |
| Dauer | 25 Minuten (15 Präsentation, 10 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Website-Administratoren, Entwickler, Hosting-Mitarbeitende |
| Voraussetzungen | [Lektion 1.2](die-eingebaute-suchmaschine.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die drei Teile einer Solr-Einrichtung nennen: die Erweiterung `ezfind`, die Kernel-Datei `settings/solr.ini` und den
  Konsolenbefehl `exp:solr`;
- erklären, dass ezfind `[SearchSettings] SearchEngine` auf `ezsolr` umstellt, wenn es aktiv ist;
- aufzählen, was Solr gegenüber der eingebauten Suchmaschine hinzufügt: Relevanzsortierung, Gewichtung, Facetten,
  Hervorhebung, Rechtschreibprüfung, Elevation;
- mit `exp:solr status` prüfen, ob für eine Installation ein Solr-Server konfiguriert ist.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Suche mit ezfind und Solr](../../../../../eng-us/help/creators/website-creators/search-with-ezfind-and-solr.md),
[Such-Backends](../../../hosting/grundlagen/such-backends.md) und
[Befehle zur Serversteuerung](../../../../../eng-us/features/6.0/web-server-and-solr-commands.md#expsolr-an-optional-search-server).

1. **Die Teile** (5 Minuten). Zeichnen Sie drei Kästen:
   - `extension/ezfind`: die Erweiterung, ein Composer-Paket (`se7enxweb/ezfind`, von der `composer.json` der
     Installation vorgeschlagen). Ihre `settings/site.ini.append.php` setzt `SearchEngine=ezsolr`,
     `SearchViewHandling=template`, `AllowEmptySearch=enabled` und `EnableWildcard=true`. Ihre `ezfind.ini` stimmt
     Relevanz und Funktionen ab; ihr eigener `solr.ini`-Block `[SolrBase]` enthält `SearchServerURI`, die Adresse, an
     die ezfind Anfragen schickt.
   - `settings/solr.ini [SolrSettings]`: die Steuerung eines Solr-Servers durch den Kernel, `Enabled=false`, bis sie
     eingeschaltet wird, mit `Url` für ein laufendes Solr oder `BinaryPath` für eine lokale Instanz.
   - `exp:solr`: `status`, `ping` sowie `start`, `stop`, `restart` für eine lokale Instanz.
2. **Was es hinzufügt** (5 Minuten). Verwenden Sie die Tabelle aus
   [Wann sich Solr lohnt](../../../../../eng-us/help/creators/website-creators/search-with-ezfind-and-solr.md#when-solr-is-worth-it):
   Relevanzsortierung und Gewichtung, Facetten, Skalierung für sehr viele Objekte, Tag-Wolken aus der Suche mit
   eztags. Aus der `ezfind.ini` der Erweiterung: Hervorhebung der Suchwörter in den Ergebnissen (`[HighLighting]`),
   Rechtschreibprüfung (`[SpellCheck]`), Autovervollständigung (`[AutoCompleteSettings]`) und in der Administration
   **Elevate**: ein Objekt für eine bestimmte Suchanfrage an die Spitze der Ergebnisse heften.
3. **Was es kostet** (5 Minuten). Solr ist ein Java-Server, der installiert, betrieben, überwacht und gesichert
   werden muss; sein Speicher und seine Festplatte werden getrennt von PHP geplant. Das Repository gibt nicht an,
   welche Solr-Versionen ezfind braucht; entnehmen Sie das dem eigenen Repository der Erweiterung, bevor Sie eine
   Version zusagen.

### Häufige Fragen

**Verwendet der Inhaltsfinder Solr?** Nein. Er schlägt direkt in der Datenbank nach, unabhängig von der Suchmaschine.

**Kann ein Solr mehrere Websites bedienen?** Laut den Installationshinweisen der Erweiterung kann eine Solr-Instanz
mehrere Installationen bedienen; jeder Installation wird über ihre eigenen Einstellungen mitgeteilt, wo Solr ist.

**Merken Redakteure die Umstellung?** Die Admin-Suche und die Suchdialoge des Online-Editors verwenden Solr, wenn es
aktiv ist, sodass sich die Ergebnisse und ihre Reihenfolge ändern können
([Die Reihenfolge der Suchergebnisse in den Dialogen des Editors](../../../../anleitungen/inhaltsmodell-und-bearbeitung.md#die-reihenfolge-der-suchergebnisse-in-den-dialogen-des-editors)).

### Stolperfallen

- **„Enabled“ in einer Datei reicht nicht.** `[SolrSettings] Enabled=true` lässt `exp:solr` einen Server verwalten
  oder erreichen; die Suche selbst wechselt erst zu Solr, wenn die Erweiterung ezfind aktiv ist. Die Teilnehmenden
  setzen oft nur eines davon.
- **Funktionen von alten Screenshots versprechen.** Vermitteln Sie nur, was die installierte Version zeigt. Wenn
  ezfind auf der Schulungs-Website nicht installiert ist, sagen Sie das und zeigen Sie stattdessen die
  Einstellungsdateien.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation und ein Terminal in ihrem Wurzelverzeichnis.

1. Führen Sie die Statusprüfung für Solr aus:

   ```bash
   php bin/php/console exp:solr status
   ```

2. Lesen Sie die Meldung. Auf einer Schulungsinstallation ohne Solr sagt sie, dass Solr nicht konfiguriert ist und wie
   man es einschaltet.
3. Geben Sie den Solr-Schalter des Kernels aus:

   ```bash
   php bin/php/console exp:ini get solr.ini/SolrSettings/Enabled
   ```

4. Geben Sie noch einmal die wirksame Suchmaschine aus (`site.ini/SearchSettings/SearchEngine`) und notieren Sie sie.
5. Prüfen Sie, ob die Erweiterung ezfind vorhanden ist: Listen Sie den Ordner `extension/` auf und suchen Sie nach
   `ezfind`.
6. Öffnen Sie in der Administration **Setup > Extensions** und suchen Sie ezfind in der Liste. Notieren Sie, ob sie
   aktiv ist.
7. Schreiben Sie einen Satz: Welche Suchmaschine verwendet Ihre Installation, und was müsste sich ändern, um Solr zu
   verwenden?

## Erwartetes Ergebnis

- Schritt 1 meldet *Solr is not installed or configured on this installation*, mit den Zeilen **reason**
  (`[SolrSettings] Enabled is not true`), **search now** (der eingebaute Index), **ezfind ext** (ob die Erweiterung
  vorhanden ist) und **to enable**. Nichts wird gestartet.
- Schritt 3 gibt `false` aus; Schritt 4 gibt `eZSearchEngine` aus.
- Die Schritte 5 und 6 zeigen, ob die Erweiterung installiert und aktiv ist; auf einer frischen Installation ist sie
  meist keines von beiden.
- Der Satz aus Schritt 7 nennt beide Änderungen: einen in `solr.ini` konfigurierten Solr-Server und die aktive
  Erweiterung ezfind.

## Quiz

1. Welchen Wert setzt ezfind in `[SearchSettings] SearchEngine`?
2. Nennen Sie drei Funktionen, die Solr hinzufügt und die die eingebaute Suchmaschine nicht hat.
3. Richtig oder falsch: `[SolrSettings] Enabled=true` allein verlegt die Website-Suche zu Solr.
4. Was bewirkt **Elevate**?
5. Wo finden Sie, welche Solr-Versionen ezfind unterstützt?

<details>
<summary>Antworten</summary>

1. `ezsolr` (die `settings/site.ini.append.php` der Erweiterung).
2. Drei beliebige aus: Relevanzsortierung und Gewichtung, Facetten, Hervorhebung, Rechtschreibprüfung,
   Autovervollständigung, Elevation, Tag-Wolken aus der Suche.
3. **Falsch.** Die Erweiterung ezfind muss ebenfalls aktiv sein; `solr.ini` teilt der Installation nur mit, wo Solr
   ist.
4. Es heftet ein ausgewähltes Objekt für eine bestimmte Suchanfrage unter die allerersten Ergebnisse.
5. Im eigenen Repository der Erweiterung; dieses Repository gibt es nicht an
   ([Such-Backends](../../../hosting/grundlagen/such-backends.md#solr-über-ez-find)).

</details>

## Weiterführende Informationen

- [Suche mit ezfind und Solr](../../../../../eng-us/help/creators/website-creators/search-with-ezfind-and-solr.md): die Anleitung für
  Website-Ersteller zum Einschalten.
- [Such-Backends](../../../hosting/grundlagen/such-backends.md): was der Betrieb jeder Suchmaschine kostet.
- [Befehle zur Serversteuerung](../../../../../eng-us/features/6.0/web-server-and-solr-commands.md): die Referenz zu `exp:solr`.

## Verwandte Seiten

- [Modul 1: Wie die Suche funktioniert](README.md)
- [Suche: Kursübersicht](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
