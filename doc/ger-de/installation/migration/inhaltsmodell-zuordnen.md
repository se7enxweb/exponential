---
title: Inhalte auf Exponential abbilden
audience: developer, admin, project-manager
category: install-upgrade
order: 8
summary: Wie sich die Konzepte jedes anderen Systems auf das Inhaltsmodell von Exponential abbilden lassen – Klassen und Datentypen, Objekte, Versionen und Übersetzungen, Orte, Sektionen und Zustände, Benutzer und Passwörter, Medien, Taxonomie, Kommentare und Formulare, URLs und SEO-Metadaten – mit den Entscheidungen, die jeweils zu treffen sind.
keywords: Inhaltsmodell, Abbildung, Inhaltsklasse, Datentyp, Attribut, Ort, Knoten, Übersetzung, Sprache, Sektion, Objektzustand, Benutzer, Passwort-Hash, Medien, Bild, Taxonomie, Tags, Schlüsselwörter, URL-Alias, SEO, Metadaten, Remote-ID, content model, mapping, content class, datatype, attribute, location, node, translation, language, section, object state, user, password hash, media, image, taxonomy, tags, keywords, URL alias, metadata, remote id
type: install
---
# Inhalte auf Exponential abbilden

Gleich, woher die Inhalte kommen: Eine Migration endet immer bei denselben Bausteinen. Diese Seite erklärt jeden davon
aus Sicht einer Migration und führt die zu treffenden Entscheidungen auf. Die Anleitungen zu den einzelnen Wegen
verweisen für die Details hierher.

[Übersicht Migration](README.md) · [Checkliste](checkliste.md) · [Import- und Export-Werkzeuge](exponential-tools.md)

## Inhalt

- [1. Die Bausteine im Überblick](#1-die-bausteine-im-überblick)
- [2. Inhaltsklassen und Datentypen](#2-inhaltsklassen-und-datentypen)
- [3. Objekte, Versionen und Übersetzungen](#3-objekte-versionen-und-übersetzungen)
- [4. Orte: der Inhaltsbaum](#4-orte-der-inhaltsbaum)
- [5. Sektionen und Objektzustände](#5-sektionen-und-objektzustände)
- [6. Benutzer, Gruppen, Rollen und Passwörter](#6-benutzer-gruppen-rollen-und-passwörter)
- [7. Medien](#7-medien)
- [8. Taxonomie](#8-taxonomie)
- [9. Kommentare, Formulare, Produkte](#9-kommentare-formulare-produkte)
- [10. URLs](#10-urls)
- [11. Metadaten für Suchmaschinen](#11-metadaten-für-suchmaschinen)
- [12. Datumsangaben und Autoren](#12-datumsangaben-und-autoren)

## 1. Die Bausteine im Überblick

| In vielen Systemen | In Exponential | Hinweis |
|---|---|---|
| Inhaltstyp, Post-Typ, Entity-Bundle, Modell | **Inhaltsklasse** | definiert unter **Setup > Klassen** oder in einem Paket mitgeliefert |
| Feld, benutzerdefiniertes Feld, Eigenschaft | **Attribut** eines **Datentyps** | jedes Klassenattribut hat einen Bezeichner |
| Eintrag, Beitrag, Seite, Knoten, Element | **Inhaltsobjekt** | mit Versionen und Übersetzungen |
| Kategoriebaum, Menühierarchie, Ordner | **Orte** (Knoten) im Inhaltsbaum | ein Objekt kann mehrere Orte haben; einer davon ist der Hauptort |
| Kategorie, Tag, Begriff | Orte, `ezkeyword` oder `eztags` | siehe [8](#8-taxonomie) |
| Asset, Anhang, Medienelement | ein **Bild**-, **Datei**- oder **Medien**-Objekt | wiederverwendbar, verknüpft oder eingebettet |
| Sprache, Locale | **Übersetzung** desselben Objekts | Sprachen werden zuerst eingerichtet |
| Status (Entwurf, veröffentlicht) | Versionsstatus; **Objektzustände** für Workflow-Zustände | |
| Bereich der Site mit eigenem Zugriff | **Sektion** | Rollen lassen sich auf Sektionen beschränken |
| Slug, Permalink | **URL-Alias** | aus dem Namen erzeugt oder von Hand gesetzt |
| Weiterleitung | URL-Alias mit Weiterleitung oder **URL-Platzhalter** | |
| SEO-Felder | `xrowmetadata`-Attribut oder einfache Attribute | |
| Benutzer, Mitglied, Kunde | **Benutzerobjekt** (Klasse `user`) in Benutzergruppen | |
| Rolle, Fähigkeit, Berechtigung | **Rolle** mit **Richtlinien**, Benutzern oder Gruppen zugewiesen | |

## 2. Inhaltsklassen und Datentypen

Legen Sie eine Klasse pro Quelltyp an, der eigene Felder hat. Halten Sie die Klassenbezeichner kurz und stabil:
Templates, Einstellungen und Importe verweisen auf sie. Die Datentypen des Kernels (`kernel/classes/datatypes/`) und
die Datentypen der Extensions der Standarddistribution decken die meisten Felder ab:

| Feld der Quelle | Datentyp | Hinweis |
|---|---|---|
| Einzeiliger Text, Titel | `ezstring` | der Objektname stammt meist daraus (das Namensmuster der Klasse) |
| Einfacher mehrzeiliger Text | `eztext` | |
| Formatierter Text, HTML-Inhalt | `ezxmltext` | HTML wird in XmlText umgewandelt; Skripte, Styles und Inline-Event-Handler bleiben dabei bewusst nicht erhalten |
| Zahl | `ezinteger`, `ezfloat` | |
| Ja/Nein | `ezboolean` | |
| Datum, Datum und Uhrzeit, Uhrzeit | `ezdate`, `ezdatetime`, `eztime` | |
| E-Mail, URL | `ezemail`, `ezurl` | |
| Einfach- oder Mehrfachauswahl | `ezselection` oder `enhancedselection2` | |
| Bild | `ezimage` (in einer Bildklasse oder direkt am Element) | |
| Datei zum Herunterladen | `ezbinaryfile` | |
| Video, Audio | `ezmedia` | oder ein Link zum Hosting-Dienst |
| Verweis auf andere Inhalte | `ezobjectrelation`, `ezobjectrelationlist` | über die Remote-ID auflösen, wenn alle Elemente vorhanden sind |
| Schlüsselwörter | `ezkeyword` | |
| Tags mit Hierarchie | `eztags` (`se7enxweb/eztags`) | |
| Kartenposition | `ezgmaplocation` (`se7enxweb/ezgmaplocation`) | |
| Bewertung | `ezsrrating` aus `ezstarrating` (`se7enxweb/ezstarrating`) | |
| Wertetabelle | `ezmatrix` | |
| Autorenliste | `ezauthor` | |
| Land | `ezcountry` | |
| Preis | `ezprice`, `ezmultiprice` | Shop-Klassen |
| Eine Seite aus Blöcken | Exponential Layouts (`explayouts`) oder `ezflow` | Layouts sind von den Objekten getrennt; siehe die Anleitungen |
| Link mit internem oder externem Ziel | `exp_enhanced_link` (`se7enxweb/exp_enhanced_link`) | |
| Wiederholte Feldgruppen | Unterobjekte einer kleinen Klasse oder `ezmatrix` | es gibt keinen verschachtelten „Repeater“-Datentyp |
| Code, Einbettung, Iframe | ein benutzerdefiniertes Tag in XmlText oder eine eigene Klasse | entscheiden, was sich gefahrlos übernehmen lässt |

Jeder Datentyp liest beim Import über `fromString()` eine Zeichenkette in seinem eigenen Format: Die Tabelle steht in
[Import- und Export-Werkzeuge, Abschnitt 4](exponential-tools.md#4-ein-eigenes-importskript-das-grundgerüst).

## 3. Objekte, Versionen und Übersetzungen

- Aus einem Element der Quelle wird ein **Objekt**. Geben Sie ihm eine **Remote-ID**, die aus der ID der Quelle
  gebildet wird (`wp-post-1234`): Importe können dann aktualisieren statt zu duplizieren, und Beziehungen und
  Weiterleitungen lassen sich später auflösen.
- Historie: Der Import legt eine erste Version an. Alte Revisionen lohnen den Import selten; archivieren Sie sie
  stattdessen.
- **Übersetzungen** sind Versionen desselben Objekts in anderen Sprachen. Richten Sie zuerst die Sprachen ein
  (**Setup > Sprachen**), importieren Sie die Hauptsprache und fügen Sie dann jede Übersetzung demselben Objekt hinzu
  (der Parameter `language` von `updateAndPublishObject()`). Eine Quelle, die jede Sprache als eigenes Element
  speichert, braucht vor dem Import einen Schlüssel, der die Übersetzungen verbindet (WordPress-Plugins, die
  Übersetzungssätze von Drupal, ein gemeinsamer Slug).
- *Immer verfügbar*: Ein Objekt kann in Sprachen angezeigt werden, in die es nicht übersetzt ist; entscheiden Sie das
  pro Klasse.

## 4. Orte: der Inhaltsbaum

Exponential ordnet jedes Objekt in einen Baum ein. Der Baum ist die Navigation der Site, ihre URL-Struktur und der
Geltungsbereich vieler Berechtigungen.

- Machen Sie aus der Haupthierarchie der Quelle (Seitenbaum, Kategoriebaum, Menü) **Ordner** oder Einstiegsobjekte.
- Geben Sie jedem Objekt einen **Hauptort**; zusätzliche Kategorien können zu **weiteren Orten** werden (das Objekt
  erscheint an mehreren Stellen, mit einer kanonischen Adresse) oder zu Schlüsselwörtern/Tags.
- Die Sortierreihenfolge gilt pro übergeordnetem Knoten (nach Priorität, Name, Datum ...): Bilden Sie die
  Menüreihenfolge der Quelle auf die Knotenpriorität ab.

## 5. Sektionen und Objektzustände

- **Sektionen** gruppieren Inhalte für Zugriff und Design (zum Beispiel „Nur für Mitglieder“, „Intranet“). Bilden Sie
  geschützte Bereiche der Quelle auf Sektionen ab und die Rollen auf Richtlinien, die auf Sektionen beschränkt sind.
- **Objektzustände** bilden Status jenseits von Entwurf/veröffentlicht ab (zum Beispiel „In Prüfung“, „Archiviert“).
  [Objektzustände](../../anleitungen/objektzustaende.md) erklärt sie.

## 6. Benutzer, Gruppen, Rollen und Passwörter

- Benutzer sind Objekte der Klasse `user` in **Benutzergruppen**; Mitglieder und Redakteure kommen meist in getrennte
  Gruppen.
- Rollen ergeben sich aus den Rollen oder Fähigkeiten der Quelle und werden als Exponential-Rollen mit Richtlinien
  neu aufgebaut ([Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)). Kopieren Sie Berechtigungen nicht eins
  zu eins; entwerfen Sie sie für die neue Site.
- **Passwörter.** Exponential prüft die Hash-Typen, die in
  [17.7](../17-migrationsreferenz.md#177-passwörter-url-aliase-und-bildspeicherung) aufgeführt sind. Eine Quelle, die
  **Standard-Hashes von PHP `password_hash()`** speichert (sie beginnen mit `$2y$`, `$argon2i$` oder `$argon2id$`),
  lässt sich als Typ 7 (`php_default`) importieren: Der Kernel prüft sie mit `password_verify()` und erzeugt bei der
  nächsten Anmeldung einen neuen Hash. Hashes in jedem anderen Verfahren (gesalzene MD5- oder SHA-Varianten,
  framework-spezifische Formate, Hashes mit eigenem Präfix) lassen sich nicht prüfen. Für diese importieren Sie die
  Konten ohne verwendbares Passwort und versenden E-Mails zum Zurücksetzen des Passworts, oder Sie halten die alte
  Anmeldung für eine Übergangszeit über einen eigenen Login-Handler verfügbar (Entwicklungsarbeit).
- Personenbezogene Daten: Importieren Sie nur, was die neue Site braucht, und nur mit einer Rechtsgrundlage.

## 7. Medien

- Importieren Sie Medien als Objekte (Bild-, Datei-, Medienklassen) unterhalb eines Medienordners und verknüpfen oder
  betten Sie sie dann ein. So werden sie wiederverwendbar, und Redakteure können eine Datei überall auf einmal
  ersetzen.
- Behalten Sie den ursprünglichen Dateinamen und den Alternativtext bei (Bilder, deren Klasse einen Alternativtext
  verlangt, brauchen einen).
- Bilder werden über **Bild-Aliase** (`image.ini`) skaliert und nicht in jeder Größe gespeichert; importieren Sie das
  größte Original.
- Schreiben Sie Links auf alte Medienadressen im formatierten Text in Einbettungen der neuen Objekte um, oder leiten
  Sie die alten Medienadressen weiter.
- Der ZIP-Import der Upload-Seite kann eine Mediathek in einem Zug übernehmen
  ([Werkzeuge](exponential-tools.md#der-zip-import-der-upload-seite-se7enxwebezmultiupload)).

## 8. Taxonomie

| Quelle | Exponential |
|---|---|
| Ein Kategoriebaum, der zugleich die Navigation der Site ist | Orte (Ordner) |
| Flache Tags | `ezkeyword` oder `eztags` |
| Kontrollierte Vokabulare, hierarchische Tags, Synonyme | `eztags` |
| Kategorien, die nur zum Filtern dienen | `eztags` oder Objektbeziehungen zu Kategorieobjekten |

## 9. Kommentare, Formulare, Produkte

- **Kommentare**: als Unterobjekte einer eigenen Kommentarklasse importieren oder archivieren. Entscheiden Sie, ob
  sich die Übernahme alter Kommentare überhaupt lohnt.
- **Formulare und Einsendungen**: Formulare werden zu Klassen mit Informationssammlung; Einsendungen archivieren Sie
  am besten als Datei. `bccie` exportiert gesammelte Informationen als CSV.
- **Produkte**: Klassen mit `ezprice`/`ezmultiprice` und dem Shop des Kernels oder ein externer Shop, auf den die Site
  verlinkt. Bestellungen und Kunden bleiben meist im Archiv des alten Systems.

## 10. URLs

- Die Adresse eines Knotens ergibt sich aus seinem Namen und den Adressen seiner übergeordneten Knoten (das
  URL-Alias-Muster der Klasse kann ein anderes Attribut verwenden). Wenn der neue Baum die alten Pfade nachbildet,
  bleiben die meisten Adressen gleich.
- Abweichende alte Adressen erhalten einen **weiterleitenden eigenen URL-Alias** am Knoten oder für ein Muster einen
  **URL-Platzhalter**; Adressen mit Query-Strings werden im Webserver weitergeleitet
  ([Checkliste, Abschnitt 4](checkliste.md#4-urls-und-weiterleitungen)).
- Jede Sprache kann für denselben Knoten eine eigene Adresse haben.

## 11. Metadaten für Suchmaschinen

- `xrowmetadata` fügt ein Metadaten-Attribut hinzu (Titel, Schlüsselwörter, Beschreibung) und erzeugt XML-Sitemaps;
  bilden Sie SEO-Titel und -Beschreibung der Quelle darauf ab.
- Kanonische Links, `hreflang` und strukturierte Daten werden von den Pagelayout-Templates geschrieben.

## 12. Datumsangaben und Autoren

- Setzen Sie nach dem Import das ursprüngliche Veröffentlichungsdatum: Die Attribute `published` und `modified` des
  Objekts kann das Importskript setzen und speichern; prüfen Sie das Ergebnis in der Administration.
- Ordnen Sie die Autoren der Quelle Benutzerobjekten zu (importieren oder legen Sie diese zuerst an) und setzen Sie den
  Eigentümer des Objekts ([Eigentümer ändern](../../anleitungen/eigentuemer-aendern.md)), oder halten Sie den Namen des Autors
  in einem `ezauthor`-Attribut fest.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->

Diese Seite ist Teil der Exponential-Dokumentation und freie Dokumentation unter der GNU Free Documentation License
1.3 oder einer späteren Version ([die Lizenz dieser Dokumentation](../../README-LIZENZ.md)).
