---
title: Die gemeinsame Migrations-Checkliste
audience: admin, developer, project-manager
category: install-upgrade
order: 6
summary: Die Schritte, die jede Migration nach, innerhalb von oder weg von Exponential gemeinsam hat – Bestandsaufnahme, Einfrieren, Abbildung der Inhalte, URLs und Weiterleitungen, Metadaten für Suchmaschinen, Proben, Tests, Umstellung, Rückweg und Abnahme – als Checklisten für die Projektleitung und das Team.
keywords: Migrations-Checkliste, Bestandsaufnahme, Inhaltsstopp, Abbildung, Weiterleitungen, Probelauf, Umstellung, Rückweg, Abnahme, migration checklist, inventory, content freeze, mapping, redirects, 301, SEO, rehearsal, cutover, go-live, rollback, sign-off, acceptance
type: install
---
# Die gemeinsame Migrations-Checkliste

Jede Migration auf diesen Seiten durchläuft dieselben Phasen. Die Anleitungen zu den einzelnen Wegen ergänzen, was für
eine bestimmte Quelle oder ein bestimmtes Ziel gilt; diese Seite ist der Rahmen, in den sie alle passen. Übertragen
Sie die Checklisten in das Aufgabensystem Ihres Projekts und haken Sie sie ab.

[Übersicht Migration](README.md) · Werkzeuge: [Die Import- und Export-Werkzeuge von Exponential](exponential-tools.md) · Abbildung: [Inhalte auf Exponential abbilden](inhaltsmodell-zuordnen.md)

## Inhalt

- [1. Bestandsaufnahme: wissen, was Sie haben](#1-bestandsaufnahme-wissen-was-sie-haben)
- [2. Umfang und Verantwortliche für jeden Teil festlegen](#2-umfang-und-verantwortliche-für-jeden-teil-festlegen)
- [3. Das Inhaltsmodell abbilden](#3-das-inhaltsmodell-abbilden)
- [4. URLs und Weiterleitungen](#4-urls-und-weiterleitungen)
- [5. Metadaten für Suchmaschinen](#5-metadaten-für-suchmaschinen)
- [6. Den Import bauen und proben](#6-den-import-bauen-und-proben)
- [7. Testen](#7-testen)
- [8. Umstellung](#8-umstellung)
- [9. Rückweg](#9-rückweg)
- [10. Nach dem Go-live](#10-nach-dem-go-live)
- [11. Wiederkehrende Risiken](#11-wiederkehrende-risiken)

## 1. Bestandsaufnahme: wissen, was Sie haben

- [ ] **Jede Adresse der alten Site.** Crawlen Sie sie (eine Sitemap allein übersieht verwaiste Seiten), exportieren
      Sie das Zugriffsprotokoll des Servers der letzten Monate und listen Sie die Adressen auf, auf die Suchmaschinen
      und andere Sites verlinken. Diese Liste bestimmt die Weiterleitungen in [4](#4-urls-und-weiterleitungen).
- [ ] **Jeder Inhaltstyp** und wie viele Elemente es davon gibt, in jeder Sprache.
- [ ] **Medien**: Bilder, Dokumente, Videos; wo sie gespeichert sind; Gesamtgröße; welche noch referenziert werden.
- [ ] **Benutzer und Gruppen**: wie viele, welche davon Redakteure, welche Mitglieder der Site sind; ob Passwörter
      übernommen werden können (meist nicht, siehe [3](#3-das-inhaltsmodell-abbilden)).
- [ ] **Formulare, Kommentare, Bewertungen, Shop-Bestellungen, Newsletter**: Daten, die keine „Inhalte“ sind, aber
      irgendwohin müssen oder archiviert werden.
- [ ] **Integrationen**: Single Sign-on, Suche, Webanalyse, Zahlung, E-Mail, Feeds, APIs, die andere Systeme aufrufen.
- [ ] **Eigener Code**: Plugins, Module, Extensions, Templates, geplante Jobs.
- [ ] **Recht und Aufbewahrung**: was aufbewahrt werden muss, was nicht übernommen werden darf (personenbezogene Daten,
      die Sie nicht mehr brauchen, bleiben am besten zurück), wer die Abnahme erteilt.

## 2. Umfang und Verantwortliche für jeden Teil festlegen

| Teil | Zu entscheiden | Typische Verantwortliche |
|---|---|---|
| Zu übernehmende Inhalte | alle, ab einem Stichtag oder nur, was noch Zugriffe hat | Inhaltsverantwortliche, Redaktion |
| Design | übernehmen, auffrischen oder neu | Design, Marketing |
| Adressen | identisch beibehalten oder neue Struktur mit Weiterleitungen | SEO, Entwicklung |
| Benutzer | Konten übernehmen oder Benutzer um eine neue Registrierung bitten | Administrator, Datenschutz |
| Funktionen | welche alten Funktionen vom ersten Tag an gebraucht werden | Projektleitung |
| Einfrieren | ab wann die Redaktion die alte Site nicht mehr ändert und wie späte Änderungen nachgezogen werden | Projektleitung, Redaktion |

## 3. Das Inhaltsmodell abbilden

Schreiben Sie die Abbildung als Tabelle pro Inhaltstyp auf, bevor Sie Importcode schreiben. Die wichtigen Spalten:

| Typ der Quelle | Feld der Quelle | Exponential-Klasse | Attribut (Bezeichner) | Datentyp | Umwandlung | Hinweise |
|---|---|---|---|---|---|---|
| z. B. Beitrag (Post) | `post_title` | `article` | `title` | `ezstring` | keine | Pflichtfeld |
| | `post_content` | `article` | `body` | `ezxmltext` | HTML zu XmlText | Bilder werden zu Einbettungen |
| | Kategorien | Ort | übergeordneter Knoten | | ein Hauptort, die übrigen als zusätzliche Orte | |

- [ ] Eine Klasse pro Typ der Quelle oder weniger Klassen mit einer Typauswahl: pro Typ entscheiden.
- [ ] Jedes Feld einem Attribut zugeordnet oder bewusst verworfen.
- [ ] Formatierter Text: entscheiden, wie HTML zu XmlText wird (oder auf der Symfony-Plattform zu RichText) und was
      mit Inline-Styles, Skripten und Iframes geschieht (verworfen oder in benutzerdefinierte Tags oder Einbettungen
      umgewandelt).
- [ ] Taxonomie: Orte (ein Baum), Schlüsselwörter (`ezkeyword`), Tags (`eztags`) oder Beziehungen.
- [ ] Medien: ein eigenes Bild-/Dateiobjekt pro Asset, verknüpft oder eingebettet, damit es wiederverwendet werden kann.
- [ ] Sprachen: ein Objekt mit Übersetzungen, nicht ein Objekt pro Sprache.
- [ ] Benutzer: Die Passwort-Hash-Typen der Quelle kann das Ziel selten lesen. Planen Sie ein Zurücksetzen ein (eine
      E-Mail mit einem Link), sofern die Anleitung Ihres Weges nicht sagt, dass die Hashes übernommen werden.
- [ ] Jedes Objekt erhält eine **Remote-ID**, die seine Quelle benennt (`wp-post-1234`), damit ein erneuter Import
      aktualisiert statt zu duplizieren und damit sich Weiterleitungen daraus erzeugen lassen.

Die Details zu jedem Konzept stehen unter [Inhalte auf Exponential abbilden](inhaltsmodell-zuordnen.md).

## 4. URLs und Weiterleitungen

- [ ] Entscheiden Sie für jede alte Adresse: dieselbe Adresse auf der neuen Site, eine 301-Weiterleitung auf ihre neue
      Adresse oder bewusst ein 410/404 (entfernt).
- [ ] Erzeugen Sie die Weiterleitungstabelle aus dem Import: Remote-ID zu neuem Knoten, alte Adresse zu neuer Adresse.
- [ ] In Exponential bewahren Sie eine alte Adresse auf eine von drei Arten (alle beschrieben unter
      [URLs und Aliase](../../anleitungen/urls-und-aliase.md)):
  - **Der eigene URL-Alias des Knotens**: wenn der alte Pfad der neue Pfad sein kann (`about-us/team`). Setzen Sie den
    Namen des Objekts oder einen eigenen Alias so, dass der Alias passt.
  - **Ein eigener URL-Alias am Knoten**, der (mit 301) auf die Hauptadresse des Knotens weiterleitet: einer pro
    abweichender alter Adresse.
  - **Ein URL-Platzhalter** (`news/*` zu `articles/{1}`) für ganze Gruppen, die sich nach einem festen Muster geändert
    haben.
- [ ] Adressen mit Query-Strings (`?p=123`, `index.php?id=5`) oder mit Dateiendungen, die das Alias-System bereinigt:
      Behandeln Sie diese im Webserver oder in einem kleinen Weiterleitungsmodul vor Exponential, aus derselben Tabelle.
- [ ] Testen Sie jede Weiterleitung automatisch (Status und Ziel), nicht nur stichprobenartig.
- [ ] Behalten Sie Weiterleitungen mindestens ein Jahr bei; so lange brauchen Suchmaschinen und alte Links sie.

## 5. Metadaten für Suchmaschinen

- [ ] Titel und Meta-Beschreibungen: auf Attribute abbilden. `xrowmetadata` (in `composer.json` vorausgesetzt) fügt
      einen Metadaten-Datentyp mit Titel, Schlüsselwörtern und Beschreibung hinzu sowie einen Cronjob für XML-Sitemaps.
- [ ] Kanonische Adressen, `hreflang` für Übersetzungen, `robots`-Regeln: im Pagelayout nachbilden.
- [ ] Eine XML-Sitemap auf der neuen Site (`xrowmetadata` oder `bcgooglesitemaps`), nach dem Go-live eingereicht.
- [ ] Strukturierte Daten (JSON-LD), die die alte Site hatte: auflisten und in Templates neu aufbauen.
- [ ] Analyse- und Verifizierungs-Tags.

## 6. Den Import bauen und proben

- [ ] Nutzen Sie zuerst die vorhandenen Werkzeuge ([Die Import- und Export-Werkzeuge von Exponential](exponential-tools.md));
      schreiben Sie ein Skript nur für das, was diese nicht können.
- [ ] Machen Sie den Import **wiederholbar**: Er liest einen eingefrorenen Export, gleicht über die Remote-ID ab und
      aktualisiert, was bereits vorhanden ist.
- [ ] Protokollieren Sie jedes Element: angelegt, aktualisiert, übersprungen (und warum).
- [ ] Proben Sie mindestens zweimal an einer Kopie des Ziels. Messen Sie die Dauer jedes Laufs; daraus ergibt sich
      das Zeitfenster für die echte Umstellung.
- [ ] Zählen Sie nach jedem Lauf: Elemente pro Typ und Sprache, Mediendateien, Benutzer, Weiterleitungen. Vergleichen
      Sie mit der Bestandsaufnahme.

## 7. Testen

| Test | Vorgehen |
|---|---|
| Anzahlen | pro Typ und Sprache, gegen die Bestandsaufnahme |
| Stichproben | die 50 meistbesuchten Seiten im direkten Vergleich, alt und neu |
| Formatierter Text | Überschriften, Listen, Tabellen, Links, Bilder, eingebettete Medien in einem Dutzend komplexer Elemente |
| Links | der Link-Checker der neuen Site ([URLs und Aliase](../../anleitungen/urls-und-aliase.md#2-wie-links-geprüft-werden)) und `./console exp:contentcheck` für defekte Beziehungen und fehlende Dateien |
| Weiterleitungen | jede Zeile der Weiterleitungstabelle, automatisch |
| Suche | die Suche der Site findet neue Elemente; der Suchindex ist neu aufgebaut (`./console exp:updatesearchindex`) |
| Berechtigungen | Redakteure können bearbeiten, anonyme Besucher sehen keine unveröffentlichten Elemente |
| Performance | die wichtigsten Seitentypen unter Last ([Benchmarking](../../anleitungen/benchmarking.md)) |
| Barrierefreiheit | die Templates, mit einem automatischen Prüfwerkzeug und per Tastatur |

## 8. Umstellung

- [ ] Kündigen Sie der Redaktion das Einfrieren an; stoppen Sie Änderungen an der alten Site.
- [ ] Letzter Export, letzter Import, Zählung, Test der Weiterleitungen.
- [ ] Senken Sie die DNS-TTL einen Tag vorher; schalten Sie DNS oder den Proxy um.
- [ ] Leeren Sie die Caches und wärmen Sie die meistbesuchten Seiten vor (`./console exp:warm`, `./console exp:preload --apply`).
- [ ] Beobachten Sie in den ersten Stunden die Protokolle (`./console exp:explogs`) und die 404-Liste; ergänzen Sie
      Weiterleitungen für das, was dort auftaucht.
- [ ] Reichen Sie die Sitemap ein; prüfen Sie die Search Console jeder Suchmaschine auf Crawling-Fehler.

## 9. Rückweg

- [ ] Lassen Sie die alte Site bis zur Abnahme schreibgeschützt weiterlaufen. Der Rückweg besteht darin, DNS oder den
      Proxy zurückzuschalten.
- [ ] Bewahren Sie eine Sicherung des Ziels auf, die unmittelbar vor dem letzten Import erstellt wurde, damit sich ein
      fehlgeschlagener letzter Import wiederholen lässt.
- [ ] Legen Sie im Voraus fest, was einen Rückweg auslöst (zum Beispiel: mehr als ein festgelegter Anteil wichtiger
      Seiten defekt, Anmeldung funktioniert nicht) und wer darüber entscheidet.
- [ ] Änderungen, die nach dem Go-live auf der neuen Site vorgenommen wurden, gehen bei einem Rückweg verloren, sofern
      sie nicht festgehalten werden; halten Sie das Zeitfenster kurz.

## 10. Nach dem Go-live

- [ ] 404-Bericht zwei Wochen lang täglich, danach wöchentlich.
- [ ] Suchmaschinen-Ranking und Zugriffe mit dem Stand vorher verglichen.
- [ ] Altes System archiviert (Export plus Datenbank-Dump) und zum vereinbarten Termin abgeschaltet.
- [ ] Dokumentation dessen, was abgebildet wurde, für die nächste Migration.

## 11. Wiederkehrende Risiken

| Risiko | Anzeichen | Gegenmaßnahme |
|---|---|---|
| Verlorene Adressen | Zugriffe brechen ein, 404-Fehler | eine vollständige Bestandsaufnahme der Adressen und automatische Tests der Weiterleitungen |
| Defekter formatierter Text | seltsames Markup, verlorene Bilder | eine Umwandlung, die zuerst an den schwierigsten Elementen getestet wird |
| Duplikate nach erneuten Läufen | dasselbe Element doppelt | Remote-IDs und Aktualisieren bei Übereinstimmung |
| Ausgesperrte Benutzer | fehlgeschlagene Anmeldungen | Zurücksetzen der Passwörter planen und Benutzer vorab informieren |
| Verlorene späte Inhaltsänderungen | die Redaktion beschwert sich | ein kurzes Einfrieren und ein Delta-Import für die letzten Tage |
| Unterschätzte Templates | Design nicht fertig | früh mit den Templates beginnen, anhand der Inhalte der ersten Probe |
| Ohne Notwendigkeit übernommene personenbezogene Daten | | nur übernehmen, was Sie brauchen; die Rechtsgrundlage dokumentieren |

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->

Diese Seite ist Teil der Exponential-Dokumentation und freie Dokumentation unter der GNU Free Documentation License
1.3 oder einer späteren Version ([die Lizenz dieser Dokumentation](../../README-LIZENZ.md)).
