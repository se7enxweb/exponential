---
title: "Lektion 7.2: Von Drupal und Joomla"
audience: trainer, admin, developer
category: training
order: 72
summary: Die Teilnehmenden planen Migrationen von Drupal (JSON:API, Nodes, Paragraphs, Taxonomie, Pfad-Aliase und /node/-Links) und von Joomla (die Web Services API oder die Datenbank, introtext und fulltext, Kategorien, menügesteuerte SEF-Adressen) und vergleichen beide mit WordPress. 45 Minuten.
keywords: migration zu exponential, taxonomie, pfad-alias, node-links, menüpunkte, weiterleitungen, vergleich, migrating to exponential, Drupal, JSON:API, Paragraphs, taxonomy, path alias, node links, Joomla, Web Services API, X-Joomla-Token, introtext, fulltext, SEF, menu items, redirects, comparison
type: help
---
# Lektion 7.2: Von Drupal und Joomla

Drupal und Joomla folgen demselben Rahmen wie WordPress, aber jedes hat seinen eigenen Export und seine eigenen
Adressfallen. Diese Lektion behandelt beide nebeneinander, damit die Teilnehmenden die Methode übertragen, statt sich
ein einzelnes System einzuprägen.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Migration zu Exponential](../README.md) |
| Modul | [Modul 7: Von anderen Systemen](README.md) |
| Dauer | 45 Minuten (20 Präsentation, 25 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Entwickler und Administratoren |
| Voraussetzungen | [Lektion 7.1](von-wordpress.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die praktischste Exportquelle von Drupal und von Joomla nennen, mit jeweils einer Grenze;
- Drupal-Inhaltstypen, Paragraphs und Taxonomie sowie Joomla-Artikel, -Kategorien und -Menüpunkte zuordnen;
- erklären, warum die `/node/<id>`-Links von Drupal und die menügesteuerten Adressen von Joomla besondere Sorgfalt
  brauchen;
- die Adressliste einer Joomla-Website aus einem Crawl und dem Server-Log aufbauen.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Drupal und Exponential](../../../../installation/migration/drupal.md) und
[Joomla und Exponential](../../../../installation/migration/joomla.md).

1. **Drupal-Export** (4 Minuten). Es gibt keine einzelne Datei „Website exportieren“. JSON:API liefert jeden
   Inhaltstyp mit Feldern, Beziehungen und dem Pfad-Alias; standardmäßig nur lesend; höchstens 50 Ressourcen pro
   Anfrage, folgen Sie dem Link `next`; exportieren Sie mit einem Konto, das alles sieht.
2. **Drupal-Zuordnung** (4 Minuten). Inhaltstyp zu Klasse; Paragraphs zu Kindobjekten kleiner Klassen oder zu
   Layout-Blöcken für Landing Pages; Taxonomie zu Ordnern oder `eztags`; Revisionen werden nicht importiert; Metatag
   zu `xrowmetadata`.
3. **Drupal-Adressen** (2 Minuten). Pfad-Aliase werden zu Knoten-Aliasen oder weiterleitenden Aliasen; auch auf
   `/node/123` wird verlinkt, ergänzen Sie also für jede Node-ID einen Alias oder eine Weiterleitung im Webserver;
   übernehmen Sie die Tabelle des Redirect-Moduls.
4. **Joomla-Export** (3 Minuten). Die Web Services API (Joomla 4 und 5) mit einem Token im Header `X-Joomla-Token`
   oder die Datenbank auf einer Kopie. Die Anleitung markiert zwei Punkte als nicht überprüft: eine Kernfunktion
   „Website exportieren“ und die Parameter für die Seitenaufteilung der API.
5. **Joomla-Zuordnung und -Adressen** (4 Minuten). `introtext` und `fulltext` (an der Weiterlesen-Linie getrennt) zu
   `intro` und `body`; Kategorien als Ordner; Module als Layout-Blöcke. Die Adressen hängen von den SEF-Einstellungen
   und dem Menü ab: Bauen Sie die Weiterleitungstabelle aus einem Crawl und dem Server-Log auf, nicht allein aus der
   Datenbank.
6. **Vergleichen** (3 Minuten). Derselbe Rahmen, drei verschiedene Exporte.

### Häufige Fragen

**Warum nicht direkt die Datenbank von Drupal lesen?** Das ist möglich, koppelt den Export aber an die Schemaversion;
JSON:API ist dokumentiert und braucht keinen Drupal-Code (Drupal-Anleitung, Abschnitt 1).

**Können Joomla-Passwörter übernommen werden?** Testen Sie, ob die Hashes Standardwerte von PHP `password_hash()`
sind; dann funktionieren sie als Typ 7; andernfalls planen Sie das Zurücksetzen (Joomla-Anleitung, Abschnitt 2).

**Was ist mit Joomla-Erweiterungen wie K2 oder Shops?** Jede ist eine eigene Entscheidung; erfassen Sie sie früh in
der Bestandsaufnahme.

### Stolperfallen

- **Seiten zählen statt Elemente.** Eine JSON:API-Seite kann wegen Zugriffsprüfungen weniger Elemente enthalten;
  zählen Sie gegen die Bestandsaufnahme.
- **Tiefe Verschachtelung von Paragraphs.** Entwerfen Sie zuerst die Kindklassen; testen Sie die tiefste
  Verschachtelung.
- **Die nicht überprüften Joomla-Punkte als Tatsache unterrichten.** Sagen Sie, was die Anleitung sagt: nicht
  überprüft.

## Praktische Übung

Eine Vergleichsübung zu zweit, mit beiden geöffneten Anleitungen.

1. Legen Sie eine Tabelle mit drei Spalten an, **WordPress**, **Drupal**, **Joomla**, und diesen Zeilen:
   Exportquelle, ihre Grenze, Muster der Remote-ID, Rich-Text-Feld, Taxonomie, Inhalte des Seitenaufbaus,
   Passwortplan, Adressfalle.
2. Füllen Sie die Drupal-Spalte anhand der [Drupal-Anleitung](../../../../installation/migration/drupal.md) aus.
3. Füllen Sie die Joomla-Spalte anhand der [Joomla-Anleitung](../../../../installation/migration/joomla.md) aus.
4. Füllen Sie die WordPress-Spalte anhand von Lektion 7.1 aus.
5. Schreiben Sie für eine Drupal-Website mit 300 Nodes, auf die im Text als `/node/<id>` verlinkt wird, auf, wie Sie
   diese Links nach dem Import auflösen (ein zweiter Durchlauf über die Remote-ID) und wie alte `/node/<id>`-Adressen
   weiter funktionieren.
6. Schreiben Sie für eine Joomla-Website mit der Adresse `/index.php?option=com_content&view=article&id=12` und der
   SEF-Adresse `/blog/my-article` auf, wo jede Weiterleitung liegt.
7. Schreiben Sie die Drush-Befehle auf, die die Drupal-Anleitung für die andere Richtung (Exponential zu Drupal)
   nennt.

## Erwartetes Ergebnis

- Die Tabelle hat 24 ausgefüllte Zellen; die Exportquelle von Drupal ist JSON:API (50 Ressourcen pro Seite), die von
  Joomla die Web Services API oder die Datenbank.
- Schritt 5: ein zweiter Importdurchlauf, der `/node/<id>`-Links über die Remote-ID auf die neuen Knoten umschreibt,
  dazu ein Alias oder eine Weiterleitung im Webserver für jede Node-ID.
- Schritt 6: die Form mit Query-String im Webserver, der SEF-Pfad als weiterleitender eigener URL-Alias.
- Schritt 7: `migrate:import`, `migrate:rollback`, `migrate:status`.
- Der Trainer prüft die Zeile mit der Adressfalle für alle drei Systeme.

## Quiz

1. Wie viele Ressourcen gibt die JSON:API von Drupal pro Anfrage höchstens zurück?
   A. 10 B. 50 C. 100 D. unbegrenzt
2. Warum müssen die `/node/<id>`-Adressen von Drupal weitergeleitet werden, selbst wenn jeder Node einen Pfad-Alias
   hat?
3. Wohin kommen `introtext` und `fulltext` von Joomla?
4. Warum wird die Weiterleitungstabelle einer Joomla-Website aus einem Crawl und dem Log aufgebaut und nicht allein
   aus der Datenbank?
5. Richtig oder falsch: Die Joomla-Anleitung bestätigt eine Kernfunktion „Inhalte der Website exportieren“.

<details>
<summary>Antworten</summary>

1. **B** ([Drupal-Anleitung, Abschnitt 1](../../../../installation/migration/drupal.md#1-was-drupal-exportieren-kann)).
2. Auch auf den Systempfad wird verlinkt
   ([Drupal-Anleitung, Abschnitt 4](../../../../installation/migration/drupal.md#4-adressen-und-weiterleitungen)).
3. Zu `intro` und `body` (`ezxmltext`), HTML umgewandelt
   ([Joomla-Anleitung, Abschnitt 2](../../../../installation/migration/joomla.md#2-joomla-auf-exponential-abbilden)).
4. Das Menü bestimmt den Pfad
   ([Joomla-Anleitung, Abschnitt 4](../../../../installation/migration/joomla.md#4-adressen-und-weiterleitungen)).
5. **Falsch.** Sie wurde nicht gefunden und ist in beide Richtungen als nicht überprüft markiert
   ([Joomla-Anleitung, Abschnitt 1](../../../../installation/migration/joomla.md#1-was-joomla-exportieren-kann)).

</details>

## Weiterführende Informationen

- [Drupal und Exponential](../../../../installation/migration/drupal.md): beide Richtungen mit Quellen.
- [Joomla und Exponential](../../../../installation/migration/joomla.md): beide Richtungen mit Quellen.
- [Migration zu und von Exponential, Abschnitt 4](../../../../installation/migration/README.md#4-migrationen-von-und-zu-anderen-systemen):
  die Anleitungen für Magento, Shopify, Contentful, AEM und Website-Baukästen.

## Verwandte Seiten

- [Modul 7: Von anderen Systemen](README.md)
- [Migration zu Exponential: Kursübersicht](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
