---
title: Eine fertige Inhaltsklasse wählen
audience: cms-developer, admin
category: cms-development
order: 51
summary: Welche der Inhaltsklassen, die Seitenpakete installieren, zu welcher Aufgabe passt - Ordner, Artikel, Bild, Datei, Link, Startseite, Feedback-Formular, Kommentar, Forum, Galerie, Veranstaltung und die Medienklassen - und was Sie prüfen sollten, bevor Sie eine wiederverwenden oder eine eigene bauen.
keywords: Inhaltsklassen, Standardklassen, Ordner, Artikel, Bild, Datei, Link, Startseite, Feedback-Formular, Kommentar, Forum, Forumsthema, Galerie, Veranstaltungskalender, Medienklassen, reiner Text, Rich Text, Wiederverwendung, content classes, default classes, folder, article, frontpage, feedback form, comment, gallery, event calendar, reuse
type: help
---
# Eine fertige Inhaltsklasse wählen

Eine aus einem Seitenpaket installierte Website bringt einen Satz von Inhaltsklassen für häufige Aufgaben mit. Eine
davon zu verwenden spart Templates und Schulung der Redakteure, solange ihre Felder wirklich zu Ihren Inhalten passen.
Diese Seite ordnet die üblichen Klassen nach Aufgabe und sagt, worauf Sie vor der Wahl achten sollten. Welche Klassen
Ihre Website hat und ihre genauen Felder stehen unter **Setup** > **Klassen**; die Klassen der Pakete
`sevenx_multisite` sind unter [sevenx_classes](../seitenpakete/sevenx-multisite/sevenx-classes-das-paket-der-inhaltsklassen.md) aufgeführt.

## Erste Entscheidung: reiner Text oder Rich Text

Jede Klasse mischt drei Arten von Textfeldern:

| Art | Datentyp | Verwenden Sie sie für |
|---|---|---|
| Textzeile | `ezstring` | Titel, Namen, kurze Beschriftungen: eine Zeile, kein Markup |
| Textabschnitt | `eztext` | reinen Text über mehrere Zeilen, etwa eine Nachricht, die ein Besucher eintippt |
| Rich Text | `ezxmltext` | formatierten Text mit Überschriften, Links, Listen, Tabellen und eingebetteten Objekten |

Reiner Text lässt das Aussehen in der Hand des Designs: Das Template entscheidet, wie er erscheint, und Redakteure
können keine andere Schrift und keinen verirrten Link hinzufügen. Rich Text gibt Redakteuren Struktur, und das Design
gestaltet trotzdem jedes Element. Felder, die Besucher ausfüllen, etwa Kommentare und Formularnachrichten, sollten
reiner Text sein. Links auf andere Inhalte, die Verschiebungen und Umbenennungen überstehen, sind nur in Rich Text
möglich, weil er das Ziel über Knoten oder Objekt speichert, nicht über die Adresse.

## Struktur: Ordner und Startseiten

| Klasse | Behälter | Typische Felder | Wählen Sie sie für |
|---|---|---|---|
| Ordner (`folder`) | ja | Name, Kurzname, Zusammenfassung, Beschreibung, ein Schalter, ob seine Unterelemente aufgelistet werden | Bereiche der Website und ihre Einstiegsseiten |
| Startseite (`frontpage`) | ja | Name, ein Billboard (ein verknüpftes Objekt), linke, mittlere, rechte und untere Spalte aus Rich Text | eine Startseite in Spalten, auf Websites ohne Layout-Editor |

Der Kurzname eines Ordners wird dort verwendet, wo der Platz knapp ist, etwa in Menüs und Brotkrumen (das Muster für
den Objektnamen `<short_name|name>` nimmt den Kurznamen, wenn er ausgefüllt ist). Mit dem Schalter zum Auflisten der
Unterelemente kann ein Redakteur einen Ordner zu einer einfachen Seite machen. Bei neuen Websites ersetzt meist eine mit
[Layouts](layouts-fuer-cms-entwickler.md) gebaute Seite die Klasse Frontpage.

## Textinhalte

| Klasse | Behälter | Typische Felder | Wählen Sie sie für |
|---|---|---|---|
| Artikel (`article`) | ja | Titel, Kurztitel, Autor, Einleitung, Haupttext, Bild (eine Relation), ein Schalter für Kommentare | News und andere datierte Texte |
| Artikel (Hauptseite) und Artikel (Unterseite) | Hauptseite ja | die Hauptseite hat die Artikelfelder plus einen Indextitel; Unterseiten darunter enthalten die weiteren Teile | lange Texte, die in Teilen gelesen werden |
| Dokumentationsseite | ja | ein Titel und ein Haupttext, kann Unterseiten enthalten | Handbücher und Anleitungen in einem Baum |
| Infobox | nein | Überschrift, Bild, Rich-Text-Inhalt, ein Link | kleine Kästen in einer Spalte |
| Blog und Blogbeitrag | ja | ein Behälter für Beiträge und der Beitrag selbst | Blogs, mit Kommentaren unter jedem Beitrag |

Die Klasse Article ist absichtlich ein Behälter: Kommentare werden unter dem Artikel angelegt. Welche Klassen ein
Redakteur unter einem Artikel anlegen darf, ist Sache der Richtlinie `content/create` der Rolle mit einer Einschränkung
**Klasse** und **ParentClass**, nicht der Klasse.

## Medien

| Klasse | Datentyp der Datei | Wählen Sie sie für |
|---|---|---|
| Bild (`image`) | `ezimage` | Bilder; jedes Bild hat einen eigenen Alternativtext, der je Klassenattribut verpflichtend gemacht werden kann |
| Datei (`file`) | `ezbinaryfile` | Dokumente zum Herunterladen, etwa PDF-Dateien |
| Video | `ezmedia` | Videodateien, die in der Seite abgespielt werden |
| Flash, Quicktime, Real video, Windows media, Silverlight | `ezmedia` | nur alte Websites: Diese Player brauchen Browser-Plugins, die aktuelle Browser nicht mehr ausführen |

Bewahren Sie Bilder und Dateien in der Medienbibliothek auf (Knoten 43, Sektion Media) und verknüpfen oder betten Sie
sie dort ein, wo sie verwendet werden. Ein Bildobjekt kann dann an vielen Stellen erscheinen, und wenn Sie es
ersetzen, ändern sich alle.

## Links

| Klasse | Typische Felder | Wählen Sie sie für |
|---|---|---|
| Link (`link`) | Name, Beschreibung, Ort (`ezurl`) | Listen von Adressen anderer Websites, mit eigener Seite und Beschreibung |

Eine Adresse, die in einem Link-Objekt oder in einem Link in Rich Text gespeichert ist, läuft über die Linkliste; sie
wird also auf Defekte geprüft und lässt sich an einer Stelle korrigieren
([Wie finde und behebe ich defekte Links?](../handbuch/wie-finde-und-repariere-ich-defekte-links.md)).

## Besucher, die schreiben

| Klasse | Typische Felder | Wählen Sie sie für |
|---|---|---|
| Kommentar (`comment`) | Betreff, Autor, Nachricht | kurze Antworten unter einem Artikel oder Blogbeitrag |
| Feedback-Formular (`feedback_form`) | Name, Beschreibung, dann die Felder, die ein Besucher ausfüllt (Name des Absenders, Betreff, Nachricht, E-Mail) und eine Empfängeradresse | Kontaktformulare |
| Forums, Forum, Forumsthema, Forumsantwort | Forumsthema: Betreff, Nachricht, angeheftet, über Aktualisierungen benachrichtigen | Diskussionsbereiche |
| Umfrage | Name, Beschreibung und eine Frage mit Optionen, die Besucher beantworten (ein Informationssammler) | eine einzelne Abstimmung |

Die Felder, die ein Besucher in einem Feedback-Formular ausfüllt, sind Informationssammler: Ihre Werte werden getrennt
vom Objekt gespeichert, unter **Setup** > **Gesammelte Informationen** aufgelistet und per E-Mail versendet. Welche
Adresse die E-Mail erhält, entscheidet das Mail-Template des Designs (`content/collectedinfomail/feedback.tpl` setzt
`email_receiver`); Website-Designs lesen sie normalerweise aus dem Feld Empfänger. Liefert das Template keine gültige
Adresse, geht die E-Mail an `site.ini [InformationCollectionSettings] EmailReceiver`, sonst an
`[MailSettings] AdminEmail`.

Ein Forum ist ein Baum mit vier Ebenen: Forums enthält Forum-Objekte, ein Forum enthält Themen, ein Thema enthält seine
Antworten. Antworten werden nicht unter anderen Antworten verschachtelt. Das Forum-Template des Designs ezwebin listet
angeheftete Themen zuerst, und "über Aktualisierungen benachrichtigen" abonniert für die Person, die das Thema
veröffentlicht, die Antworten darunter. Wer ein Forum entfernt, entfernt alle seine Themen und Antworten mit.

## Kalender und Galerien

| Klasse | Behälter | Wählen Sie sie für |
|---|---|---|
| Veranstaltungskalender und Veranstaltung | Kalender ja | datierte Veranstaltungen, nach Monat aufgelistet |
| Galerie | ja | eine Reihe von Bildobjekten, die zusammen gezeigt werden |

## Bevor Sie eine Klasse wiederverwenden

- Lesen Sie die Klasse unter **Setup** > **Klassen**: ihre Attribute, welche erforderlich sind, und ihr Muster für den
  Objektnamen.
- Prüfen Sie die Templates, die Ihr Design für sie hat. Eine Klasse ohne Full-View-Template Ihres Designs fällt auf ein
  allgemeines zurück.
- Benennen Sie Felder in der Klasse um, statt eines davon zweckentfremdet zu nutzen: Ein Feld "Autor", das einen Ortsnamen enthält,
  verwirrt jeden Redakteur nach Ihnen.
- Passt keine Klasse, legen Sie eine eigene an ([Eine Inhaltsklasse anlegen](eine-inhaltsklasse-anlegen.md)). Ein paar
  klare Klassen sind besser als eine Klasse mit optionalen Feldern für alles.

## Verwandte Seiten

- [Das Inhaltsmodell](das-inhaltsmodell.md)
- [Datentypen wählen](datentypen-waehlen.md)
- [Ein Inhaltsmodell entwerfen](ein-inhaltsmodell-entwerfen.md)
- [Formulare und Informationssammlung](formulare-und-informationssammlung.md)
- [Wie füge ich einer Inhaltsklasse ein Attribut hinzu?](../handbuch/wie-fuege-ich-einer-inhaltsklasse-ein-attribut-hinzu.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
