---
title: Aufgaben zum Veröffentlichen
audience: government, editor
category: public-sector
order: 10
summary: Schritt-für-Schritt-Aufgaben zum Veröffentlichen für Redakteure in Gemeinden, Ämtern, Schulen und anderen Behörden - Tagesordnungen, Protokolle, Bekanntmachungen, Warnmeldungen, Pressemitteilungen, Dokumente, Veranstaltungen, Menüs, Versionen, Freigaben, Übersetzungen und mehr - jeweils mit Voraussetzungen, Schritten, einer Prüfung des Ergebnisses und Fallstricken.
keywords: Aufgaben zum Veröffentlichen, Redakteur, Anleitung, Tagesordnung, Ratssitzung, Protokoll, öffentliche Bekanntmachung, Warnmeldung, Pressemitteilung, Dokument, Veranstaltung, Menü, Versionen, Freigabe, Übersetzung, Schlagwörter, RSS, publishing tasks, editor, how to, council agenda, minutes, public notice, alert, press release, document, event, menu, versions, approval, translation, tags
type: help
---
# Aufgaben zum Veröffentlichen

Dieser Ordner ist für Redakteure in Behörden: die Menschen, die am Montag eine Tagesordnung, am Dienstag eine
Bekanntmachung und am Freitag eine Korrektur veröffentlichen. Jede Seite ist eine Aufgabe, mit dem, was Sie vor dem
Beginn brauchen, den Schritten mit den Beschriftungen, die die Administration zeigt, der Prüfung des Ergebnisses und
den Fallstricken. Die Aufgaben nutzen Funktionen, die in diesem Repository dokumentiert sind; wo Exponential für etwas
keine Funktion hat, sagt die Seite das und nennt die praktische Alternative.

Manche Aufgaben hängen davon ab, wie Ihre Site eingerichtet wurde (welche Klassen es gibt, welche Workflows laufen).
Die verwendeten Namen von Klassen und Einstellungen sind die der Standardinstallation und der Aufbau-Anleitungen dieser
Hilfe für Behörden; fragen Sie Ihr Web-Team, wo Ihre Site davon abweicht.

## Inhalt

1. [Sitzungen, Bekanntmachungen und Warnmeldungen](#sitzungen-bekanntmachungen-und-warnmeldungen)
2. [Nachrichten, Dokumente und Veranstaltungen](#nachrichten-dokumente-und-veranstaltungen)
3. [Seiten, Menüs und Adressen](#seiten-menüs-und-adressen)
4. [Versionen, Freigabe und Sprachen](#versionen-freigabe-und-sprachen)
5. [Formulare, Feeds und Abonnenten](#formulare-feeds-und-abonnenten)
6. [Pflege](#pflege)

## Sitzungen, Bekanntmachungen und Warnmeldungen

| Aufgabe | Die Antwort in einer Zeile |
|---|---|
| [Die Tagesordnung einer Ratssitzung veröffentlichen](die-tagesordnung-einer-ratssitzung-veroeffentlichen.md) | Die Tagesordnung der Sitzung ausfüllen, die Unterlagen verknüpfen, vor der Frist veröffentlichen |
| [Das Sitzungsprotokoll nach der Genehmigung veröffentlichen](das-sitzungsprotokoll-nach-der-genehmigung-veroeffentlichen.md) | Das genehmigte Protokoll hochladen, mit der Sitzung verknüpfen, deren Status aktualisieren |
| [Eine öffentliche Bekanntmachung mit Ablaufdatum veröffentlichen](eine-oeffentliche-bekanntmachung-mit-ablaufdatum-veroeffentlichen.md) | Eine Bekanntmachung mit einem Versteckdatum, das der Cronjob `hide.php` nutzt |
| [Eine dringende Warnmeldung auf der Startseite veröffentlichen](eine-dringende-warnmeldung-auf-der-startseite-veroeffentlichen.md) | Eine Bekanntmachung in dem Ordner, den die Startseite zeigt; durch die Caches hindurch prüfen |
| [Eine veröffentlichte Bekanntmachung korrigieren und den Verlauf behalten](eine-veroeffentlichte-bekanntmachung-korrigieren-und-den-verlauf-behalten.md) | Bearbeiten, die Korrektur vermerken, veröffentlichen; der alte Text bleibt in den Versionen |

## Nachrichten, Dokumente und Veranstaltungen

| Aufgabe | Die Antwort in einer Zeile |
|---|---|
| [Eine Pressemitteilung veröffentlichen](eine-pressemitteilung-veroeffentlichen.md) | Anlegen, Bilder mit Alternativtext anhängen, zur Freigabe schicken oder veröffentlichen |
| [Eine Nachricht zeitgesteuert veröffentlichen](eine-nachricht-zeitgesteuert-veroeffentlichen.md) | Ein Workflow-Ereignis *Wait until date* hält die Veröffentlichung bis zu einem Datumsattribut zurück |
| [Einen PDF-Bericht mit barrierefreier Zusammenfassung hochladen](einen-pdf-bericht-mit-barrierefreier-zusammenfassung-hochladen.md) | Ein Datei-Objekt für den Bericht und eine HTML-Zusammenfassung, die ihn verknüpft |
| [Bildern Alternativtext geben](bildern-alternativtext-geben.md) | Der Alternativtext des Bildfelds, im Bearbeitungsformular oder im Upload-Dialog |
| [Eine Stellenausschreibung veröffentlichen](eine-stellenausschreibung-veroeffentlichen.md) | Die Klasse Job position mit einer Bewerbungsfrist |
| [Eine Ausschreibung veröffentlichen](eine-ausschreibung-veroeffentlichen.md) | Eine Bekanntmachung mit Dokumenten, einer Frist und einem Link zur Vergabeplattform |
| [Ein Beteiligungsverfahren veröffentlichen und Antworten sammeln](ein-beteiligungsverfahren-veroeffentlichen-und-antworten-sammeln.md) | Ein Beteiligungsverfahren mit Sammelfeldern; Antworten unter **Setup > Gesammelte Informationen** |
| [Eine Veranstaltung veröffentlichen](eine-veranstaltung-veroeffentlichen.md) | **Neu erstellen** > **Event** im Kalender, mit Zeiten, Ort und Barrierefreiheit |
| [Eine Veranstaltung absagen oder verschieben](eine-veranstaltung-absagen-oder-verschieben.md) | Die Seite behalten, Status und Daten ändern, die Menschen informieren |
| [Eine Fotogalerie veröffentlichen](eine-fotogalerie-veroeffentlichen.md) | Eine Galerie mit in einem Durchgang hochgeladenen Bildern, jedes mit Alternativtext |
| [Ein Video mit Untertiteln und Transkript einbetten](ein-video-mit-untertiteln-und-transkript-einbetten.md) | Untertitel und ein Transkript auf der Seite, das Video eingebettet oder verlinkt |
| [Eine Datentabelle veröffentlichen](eine-datentabelle-veroeffentlichen.md) | Eine barrierefreie Tabelle im Rich Text, dazu die Daten als CSV-Datei |
| [Ein Handbuch als PDF-Export veröffentlichen](ein-handbuch-als-pdf-export-veroeffentlichen.md) | **Setup > PDF-Export** des Teilbaums des Handbuchs |
| [Viele Dateien auf einmal hochladen](viele-dateien-auf-einmal-hochladen.md) | Die Upload-Seite: viele Dateien, Titel, **Keep as drafts** |
| [Ein Dokument durch eine neue Version ersetzen](ein-dokument-durch-eine-neue-version-ersetzen.md) | Das Datei-Objekt bearbeiten, die neue Datei wählen, veröffentlichen; die Adresse bleibt |

## Seiten, Menüs und Adressen

| Aufgabe | Die Antwort in einer Zeile |
|---|---|
| [Eine Dienstleistungsseite veröffentlichen](eine-dienstleistungsseite-veroeffentlichen.md) | Die Klasse Service: wer, Kosten, wie man beantragt, Kontakt, Prüfdatum |
| [Öffnungszeiten oder Kontaktdaten aktualisieren](oeffnungszeiten-oder-kontaktdaten-aktualisieren.md) | Die Kontaktstelle einmal bearbeiten; jede Dienstleistung, die sie verknüpft, zeigt die Änderung |
| [Das Profil eines Amtsträgers veröffentlichen](das-profil-eines-amtstraegers-veroeffentlichen.md) | Nur Angaben zur öffentlichen Funktion, mit Einwilligung für das Foto |
| [Eine Seite in das Menü aufnehmen](eine-seite-in-das-menue-aufnehmen.md) | Eine Menüklasse direkt unter dem richtigen Ordner; Reihenfolge nach Priorität |
| [Eine Seite verschieben](eine-seite-verschieben.md) | **Verschieben**, das neue übergeordnete Element wählen; die alte Adresse leitet weiter |
| [Eine Seite vorübergehend verstecken](eine-seite-voruebergehend-verstecken.md) | **Verstecken / Zeigen**; auch nichts darunter wird angezeigt |
| [Eine veraltete Seite entfernen und wiederherstellen](eine-veraltete-seite-entfernen-und-wiederherstellen.md) | **Entfernen** verschiebt sie in den Papierkorb; **Wiederherstellen** holt sie zurück |
| [Eine Liste verwandter Dokumente hinzufügen](eine-liste-verwandter-dokumente-hinzufuegen.md) | Ein Relationsfeld oder Einbettungen im Text, niemals Kopien |
| [Inhalte mit eztags verschlagworten](inhalte-mit-eztags-verschlagworten.md) | Im Feld Schlagwörter tippen, aus den Vorschlägen wählen |
| [Einen Link zu einem externen Dienst hinzufügen](einen-link-zu-einem-externen-dienst-hinzufuegen.md) | Ein klarer Linktext, die externe Adresse, ein Hinweis, dass der Link die Site verlässt |
| [Eine Landingpage mit Layouts erstellen](eine-landingpage-mit-layouts-erstellen.md) | **Layouts**, **New layout**, Blöcke hinzufügen, der Seite zuordnen, veröffentlichen |
| [Seiten in einem Ordner neu ordnen](seiten-in-einem-ordner-neu-ordnen.md) | Nach Priorität sortieren und Prioritäten setzen oder in der Liste der Unterelemente ziehen |
| [Einer Seite eine kurze Adresse geben](einer-seite-eine-kurze-adresse-geben.md) | Die Seite **URL-Aliase** des Knotens: **Neuer Alias** |
| [Alte Adressen nach einer Umstrukturierung weiterleiten](alte-adressen-nach-einer-umstrukturierung-weiterleiten.md) | Aliase für einzelne Seiten, **URL Platzhalter** für Gruppen |
| [Ein Element an mehreren Stellen veröffentlichen](ein-element-an-mehreren-stellen-veroeffentlichen.md) | **Orte hinzufügen**; ein Objekt, mehrere Adressen |
| [Eine Seite als Ausgangspunkt kopieren](eine-seite-als-ausgangspunkt-kopieren.md) | **Kopieren**, dann die Kopie bearbeiten; noch verweist nichts auf sie |
| [Eine Seite mit häufig gestellten Fragen veröffentlichen](eine-seite-mit-haeufig-gestellten-fragen-veroeffentlichen.md) | Fragen als Überschriften, kurze Antworten, Links zu den ausführlichen Seiten |

## Versionen, Freigabe und Sprachen

| Aufgabe | Die Antwort in einer Zeile |
|---|---|
| [Vor dem Veröffentlichen eine Vorschau ansehen](vor-dem-veroeffentlichen-eine-vorschau-ansehen.md) | **Vorschau** im Bearbeitungsformular, im Siteaccess und in der Gerätegröße Ihrer Wahl |
| [Eine Seite zur Freigabe schicken](eine-seite-zur-freigabe-schicken.md) | **Zur Veröffentlichung schicken** in einer Sektion mit Freigabe; verfolgen unter **Meine ausstehenden Elemente** |
| [Eine Seite freigeben oder ablehnen](eine-seite-freigeben-oder-ablehnen.md) | **Mein Konto > Zusammenarbeit**, die Anfrage öffnen, **Freigeben** oder **Ablehnen** |
| [Eine Seite in eine andere Sprache übersetzen](eine-seite-in-eine-andere-sprache-uebersetzen.md) | **Neue Übersetzung**, mit dem Original daneben übersetzen, veröffentlichen |
| [Zwei Versionen vergleichen](zwei-versionen-vergleichen.md) | **Versionen verwalten**, **Vergleichen** oder die beiden Auswahllisten |
| [Eine frühere Version wiederherstellen](eine-fruehere-version-wiederherstellen.md) | **Neuen Entwurf machen** aus der alten Version, dann veröffentlichen |
| [Prüfen, wer eine Seite geändert hat](pruefen-wer-eine-seite-geaendert-hat.md) | Die Versionsseite und der Reiter **Audit** |
| [Einen verlorenen Entwurf wiederfinden](einen-verlorenen-entwurf-wiederfinden.md) | **Meine Entwürfe**, dann **Bearbeiten** |
| [Viele Elemente auf einmal bearbeiten](viele-elemente-auf-einmal-bearbeiten.md) | **Weitere Aktionen** > **Ausgewählte bearbeiten**, dann **Alle veröffentlichen** |
| [Inhalte in Prüfung mit Objektzuständen markieren](inhalte-in-pruefung-mit-objektzustaenden-markieren.md) | Reiter **Details**, **Zustand des Objekts**, **Zustände einstellen** |
| [Eine Zusammenfassung in einfacher Sprache schreiben](eine-zusammenfassung-in-einfacher-sprache-schreiben.md) | Drei Sätze: was, wer, was zu tun ist |
| [Eine Meta-Beschreibung und ein Bild für soziale Netzwerke festlegen](eine-meta-beschreibung-und-ein-bild-fuer-soziale-netzwerke-festlegen.md) | Das Feld Meta Data des Elements |

## Formulare, Feeds und Abonnenten

| Aufgabe | Die Antwort in einer Zeile |
|---|---|
| [Einen RSS-Feed der Bekanntmachungen erstellen](einen-rss-feed-der-bekanntmachungen-erstellen.md) | **Setup > RSS**, **Neuer Export**, Quelle ist der Ordner der Bekanntmachungen |
| [Veröffentlichen, ohne die Abonnenten zu benachrichtigen](veroeffentlichen-ohne-die-abonnenten-zu-benachrichtigen.md) | **Ohne Benachrichtigung veröffentlichen**, wo die Site es anbietet |
| [Änderungen mit Benachrichtigungen verfolgen](aenderungen-mit-benachrichtigungen-verfolgen.md) | **Benachrichtigen** an einem Ordner; Zusammenfassungen unter **Meine Benachrichtigungseinstellungen** |
| [Eine Newsletter-Ausgabe vorbereiten](eine-newsletter-ausgabe-vorbereiten.md) | Eine Ausgabe von cjw_newsletter, ein Testversand, dann die Planung |
| [Eine Umfrage veröffentlichen](eine-umfrage-veroeffentlichen.md) | **Neu erstellen** > **Poll**, Optionen, Ergebnisseite |
| [Formularantworten als CSV exportieren](formularantworten-als-csv-exportieren.md) | **Setup > Gesammelte Informationen**, **CSV exportieren** |
| [Nicht mehr benötigte Formularantworten entfernen](nicht-mehr-benoetigte-formularantworten-entfernen.md) | **Ausgewählte entfernen** nach dem Export, nach Plan |

## Pflege

| Aufgabe | Die Antwort in einer Zeile |
|---|---|
| [Defekte Links finden und reparieren](defekte-links-finden-und-reparieren.md) | **Setup > URL-Verwaltung**, dann die Seiten, die einen defekten Link verwenden |
| [Eine Seite aktualisieren, die alte Inhalte zeigt](eine-seite-aktualisieren-die-alte-inhalte-zeigt.md) | **Cache leeren** am Knoten; fragen Sie das Web-Team nach anderen Caches |

Nach oben: [Behörden: hier beginnen](../../verwaltungen-hier-beginnen.md). Für die Planung lesen Sie
[Verwaltungsebenen](../../../../../eng-us/help/government/levels/README.md); für die Einrichtung die [Aufbau-Anleitungen](../../../../../eng-us/help/government/building/README.md).

## Verwandte Seiten

- [Inhalte erstellen und bearbeiten](../../../inhalte-bearbeiten.md)
- [Veröffentlichen, Versionen und der Papierkorb](../../../veroeffentlichen-und-versionen.md)
- [Kurs Redakteure 101](../../../../../eng-us/help/trainers/editors-101/README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
