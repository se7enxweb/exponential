---
title: Was Exponential Intelligence tut
audience: ai-agents, editor, admin
category: ai-agents
order: 21
summary: Die Funktionen von Exponential Intelligence für Redakteure - der Reiter Intelligence, die Aktionen und Vorlagen des Bearbeitungsformulars, Alternativtext, Vector, Ketten und Übersetzungsabgleich - und genau, was jede davon an das Modell sendet und was nie.
keywords: Exponential Intelligence, expai, Reiter Intelligence, Fill the fields, Feldaktionen, Write, Rewrite, Shorten, Translate, Suggest, Menü Improve, Vorlagen, was gesendet wird, Panel, Gespräch, Intelligence tab, field actions, Improve menu, presets, what is sent, panel, conversation
type: help
---
# Was Exponential Intelligence tut

Diese Seite richtet sich an Redakteure, die wissen möchten, was der Schreibassistent tut, bevor sie ihn verwenden, und an
Administratoren, die ihn erklären müssen. Sie geht jeden Ort durch, an dem der Assistent erscheint, und sagt jeweils, was an das
Modell gesendet wird.

## Inhalt

1. [Der Reiter Intelligence](#der-reiter-intelligence)
2. [Das Bearbeitungsformular](#das-bearbeitungsformular)
3. [Massen- und Hintergrundarbeit](#massen--und-hintergrundarbeit)
4. [Was an das Modell gesendet wird](#was-an-das-modell-gesendet-wird)
5. [Was er nicht kann](#was-er-nicht-kann)
6. [Beispielanfragen](#beispielanfragen)

## Der Reiter Intelligence

Öffnen Sie einen Knoten in der Administration und wählen Sie den Reiter **Intelligence**. Sie erhalten ein Gespräch für sich und
diesen Knoten.

- Sie schreiben eine Anfrage in einfacher Sprache, zum Beispiel "write three short articles about our spring events under this
  folder".
- Der Assistent schlägt nach, was er braucht: den Knoten, seine Kinder, den Baum ringsum, die Definition der Inhaltsklasse, die er
  anlegen soll. Jede Abfrage wird als kompakter Schritt gezeigt.
- Er fragt nach, wenn eine Anfrage unklar ist.
- Er schreibt **Entwürfe** und listet sie mit Links zum Bearbeiten, Ansehen und Verwerfen jedes einzelnen. **Save as drafts** behält
  sie; **Send for approval** führt die normale Veröffentlichungsoperation als Sie aus
  ([Entwürfe und menschliche Prüfung](entwuerfe-und-menschliche-pruefung.md)).
- Der Verlauf ist eine einklappbare Zeile je Austausch. "New conversation" beginnt von vorn.

Der Reiter wird nur Benutzern mit der Richtlinie `expai/write` gezeigt und nur, solange der Assistent eingeschaltet ist
([Berechtigungen für KI-Funktionen](berechtigungen-fuer-ki-funktionen.md)).

## Das Bearbeitungsformular

| Element | Was es tut |
|---|---|
| **Fill the fields** | Füllt die Felder des Entwurfs aus einer kurzen Vorgabe |
| Feldaktionen **Write**, **Rewrite**, **Shorten**, **Translate**, **Suggest** | Arbeiten an einem Feld; Translate übersetzt in eine andere Sprache der Website |
| **Presets** | Ein Klick für einen Titel, eine Zusammenfassung, eine Meta-Beschreibung oder Schlüsselwörter |
| Menü **Improve** | An jedem Textfeld |
| Zeile "Defaults in use" | Nennt das Modell, die Region und die geschätzten Kosten eines Klicks |
| **Write alt text** an Bildfeldern | Schlägt den Alternativtext eines Bildes vor ([Alternativtext für Bilder](alternativtext-fuer-bilder.md)) |

Werte gehen **in das Formular**, damit Sie sie prüfen. Nichts wird gespeichert, bis Sie den Entwurf selbst speichern oder
veröffentlichen; Autosave, der Online Editor und Tiptap arbeiten weiter wie bei getipptem Text. Feldaktionen funktionieren nur an
einem Entwurf, der Ihnen gehört und den Sie bearbeiten dürfen.

## Massen- und Hintergrundarbeit

- **Vector** ist ein Arbeitsbereich mit Threads für größere Aufgaben: ein Geltungsbereich, ein Plan mit Kostenschätzung und eine
  gestufte Prüfung jeder Änderung je Feld, bevor etwas geschrieben wird ([Vector und gestufte Prüfung](vektorsuche-und-gestufte-pruefung.md)).
- **KI-Ketten** schreiben in Schritten über die Job-Warteschlange: schreiben, prüfen, überarbeiten, SEO-Felder vorbereiten, an einem
  menschlichen Kontrollpunkt anhalten, dann übersetzen ([Ketten und eingereihte Arbeit](ketten-und-eingereihte-arbeit.md)).
- **Der Übersetzungsabgleich** hält Übersetzungen im Gleichschritt, nachdem sich die Ausgangssprache geändert hat
  ([Übersetzungshilfe](uebersetzungshilfe.md)).
- **Der Bericht "Missing alt text"** reiht Vorschläge für Alternativtext für einen Teilbaum als Entwürfe ein.

Jede vom Assistenten geschriebene Version trägt die Kennzeichnung **Exponential Intelligence**, in Meine Entwürfe, auf der
Entwurfsseite, in Versionsansichten, Bearbeitungsformularen und Freigabeelementen.

## Was an das Modell gesendet wird

Aus der [Sicherheitsseite](https://github.com/se7enxweb/expai/blob/main/doc/security.md) der Erweiterung:

| Ort | Gesendet |
|---|---|
| Reiter Intelligence | Die Anweisungen (mit Name, Klasse, Pfad des geöffneten Knotens und der Standardsprache der Website), die Werkzeugdefinitionen, Ihre Nachrichten und die Ergebnisse der Werkzeuge, die das Modell aufruft: Namen und Feldwerte von Inhalten, die Sie lesen dürfen, Klassendefinitionen. Bilder werden hier nicht gesendet, nur ihre Alternativtexte; Dateien und Felder von Benutzerkonten nie |
| Bearbeitungsformular | Name und Klasse des Objekts, das Feld, sein aktueller Wert und die aktuellen Werte der anderen Felder im Formular (höchstens etwa 30 KB), Ihre Anweisung |
| Alternativtext | Das gespeicherte Bild des Feldes (verkleinert, wenn es groß ist), der Seitentitel, der Text der anderen Felder und die Sprache, an ein Vision-Modell, dessen Anbieter die Datenrichtlinie zulässt |
| Nie | Ihr Name, Login oder Ihre E-Mail, Passwörter, Session-Daten, der Inhalt der Entwürfe anderer Benutzer |

Was das Modell erreicht, begrenzt außerdem die **Datenrichtlinie**, die Sektionen und Klassen von einem Anbieter fernhalten kann
([Datenschutz und Daten, die die Website verlassen](../steuerung/datenschutz-und-daten-die-die-website-verlassen.md)).

## Was er nicht kann

- Er kann nicht veröffentlichen, löschen, verschieben, Berechtigungen ändern oder E-Mails senden.
- Er kann keine Bilder oder Dateien hochladen und keine Relationen setzen; er sagt das, und Sie setzen sie im Bearbeitungsformular.
- Er kann nichts tun, was Sie nicht tun dürfen: Jede Abfrage und jeder Entwurf läuft mit Ihren eigenen Inhaltsberechtigungen.

Weitere Grenzen: [Was KI in Exponential nicht tut](../was-ki-in-exponential-nicht-tut.md).

## Beispielanfragen

Aus dem [Nutzungsleitfaden](https://github.com/se7enxweb/expai/blob/main/USAGE.md) der Erweiterung:

| Fragen Sie | Was Sie erwarten können |
|---|---|
| "Write three short articles about our spring events under this folder, friendly tone, no images." | Neue Entwürfe unter dem Ordner, jeder mit seinen Links aufgeführt |
| "Rewrite the intro of this article: shorter and clearer." | Eine neue Entwurfsversion des Artikels; die veröffentlichte Version bleibt, wie sie ist |
| "Translate this article into German as a new draft." | Ein Entwurf auf Deutsch, den Sie prüfen |
| "Which kinds of content may I create here?" | Eine Antwort in Worten, aus den Klassen, die Sie an dieser Stelle anlegen dürfen |

Der Assistent versteht Anfragen auch auf Deutsch; die Beispiele stehen hier im Wortlaut des Leitfadens.

Die Feldaktionen, wie der Nutzungsleitfaden sie beschreibt:

| Schaltfläche | Was sie tut | Fragt nach |
|---|---|---|
| Write | schreibt einen neuen Wert aus Ihrer Anweisung | was geschrieben werden soll |
| Rewrite | klarer und besser geschrieben, gleiche Bedeutung und Länge | optional, wie geändert werden soll |
| Shorten | etwa halb so lang, gleiche Kernaussagen | nichts |
| Translate | in eine andere Sprache der Website | die Sprache |
| Suggest | ein Wert auf Grundlage der anderen Felder | nichts |

Der neue Wert kommt mit einem kurzen Hinweis und einem Link **Undo** in das Feld.

## Verwandte Seiten

- [Exponential Intelligence für KI-Nutzer](README.md)
- [Entwürfe und menschliche Prüfung](entwuerfe-und-menschliche-pruefung.md)
- [KI-Entwürfe prüfen](../checklisten/ki-entwuerfe-pruefen.md)
- [expai: Exponential Intelligence](../../../funktionen/6.0/erweiterungen/expai.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
