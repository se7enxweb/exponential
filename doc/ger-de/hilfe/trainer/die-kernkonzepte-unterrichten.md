---
title: "Die Kernkonzepte unterrichten"
audience: trainer, editor, admin, developer
category: training
order: 5
summary: Das Dutzend Ideen, das alle Teilnehmenden an Exponential brauchen, Inhaltsklasse, Attribut, Inhaltsobjekt, Knoten, Version, Übersetzung, Sektion, Rolle und Richtlinie, Siteaccess, Design und Template, Erweiterung, INI-Einstellungen, mit einer einfachen Erklärung, einem Bild für den Unterricht, einem Plan für eine einstündige Einheit und einem Quiz.
keywords: kernkonzepte, inhaltsmodell, inhaltsklasse, attribut, inhaltsobjekt, knoten, version, entwurf, übersetzung, sektion, rolle, richtlinie, siteaccess, design, template, erweiterung, ini, unterrichten, erklärung, core concepts, content model, content class, node, policy, extension
type: help
---
# Die Kernkonzepte unterrichten

Jeder Exponential-Kurs, ob für Redakteure, Administratoren oder Entwickler, beruht auf demselben Dutzend Ideen.
Teilnehmende, die sie früh verstehen, begreifen alles Weitere schneller; wer sie verpasst, stellt in jedem Modul
dieselbe Frage. Diese Seite gibt Trainern einen einfachen Satz pro Konzept, ein Bild, das im Unterricht funktioniert,
und eine einstündige Einheit, die sie alle vermittelt. Die Definitionen folgen dem [Glossar](../../../eng-us/glossary.md).

## Die Konzepte

| Konzept | In einem Satz | Ein Bild für den Unterricht | Wo die Teilnehmenden es sehen |
|---|---|---|---|
| **Inhaltsklasse** | Die Definition einer Art von Inhalt (zum Beispiel Artikel) als Liste von Attributen | Ein leeres Formular | **Setup > Klassen** (Administratoren) |
| **Attribut** | Ein Feld einer Inhaltsklasse, dessen Art ein Datentyp festlegt (Text, Bild, Relation) | Ein Kästchen auf dem Formular | Die Felder des Bearbeitungsformulars |
| **Inhaltsobjekt** | Ein einzelner Inhalt, eine Instanz einer Klasse, mit Versionen und Übersetzungen | Ein ausgefülltes Formular | Jedes Element, das Sie öffnen |
| **Knoten** | Ein Platz eines Inhaltsobjekts im Inhaltsbaum, mit einem übergeordneten Element, einer Sortierung und einer Sichtbarkeit | Eine Verknüpfung in einem Ordner: Dasselbe Dokument kann mehrere haben | Der Baum in **Inhaltsstruktur** |
| **Version und Entwurf** | Jede Bearbeitung erzeugt eine neue Version; ein Entwurf wird beim Veröffentlichen zur veröffentlichten Version | Nummerierte Kopien eines Dokuments; nur eine hängt am Schwarzen Brett | **Versionen verwalten** an einem Element |
| **Übersetzung** | Eine Sprachfassung eines Inhaltsobjekts | Dasselbe Formular, in einer anderen Sprache ausgefüllt | Die Sprachliste neben **Bearbeiten** |
| **Sektion** | Eine Kennzeichnung von Inhalten, die ein Design und Berechtigungen auswählt | Ein farbiger Aufkleber auf einem Ordner | Der Reiter **Details** eines Elements |
| **Rolle und Richtlinie** | Eine Rolle ist eine benannte Menge von Richtlinien (Berechtigungen), die Benutzern und Gruppen zugewiesen wird | Ein Schlüsselbund; jeder Schlüssel öffnet eine Art von Tür | **Benutzer > Rollen und Richtlinien** |
| **Siteaccess** | Ein Zugang zur Website (über URL, Host oder Port) mit eigenen Einstellungen und eigenem Design, etwa `site` und `admin` | Mehrere Türen in dasselbe Gebäude | Die Adressleiste: `/admin`, der Host des Editors |
| **Design und Template** | Ein Design ist eine Sammlung von Templates, Styles und Bildern; ein Template ist eine `.tpl`-Datei, die einen Teil einer Seite zeichnet | Das Drucklayout einer Zeitschrift | Das Aussehen der öffentlichen Website |
| **Erweiterung** | Ein Paket, das Funktionen hinzufügt oder ändert, ohne den Kernel zu verändern | Ein Zusatzmodul für eine Maschine | **Setup > Extensions** |
| **INI-Einstellungen** | Einstellungsdateien in Blöcken aus `Key=Value`, gelesen in dieser Reihenfolge: Standardwerte, Erweiterungen, Siteaccess, Override | Werkseinstellungen, darüber lokale Anpassungen | `settings/`, `exp:ini` |

Zwei Unterscheidungen bringen Teilnehmende am häufigsten durcheinander; sagen Sie sie zweimal:

- **Objekt und Knoten.** Das Objekt ist der Inhalt; ein Knoten ist der Ort, an dem er erscheint. Wer einen Knoten
  verschiebt, verschiebt den Platz des Inhalts, nicht den Inhalt. Ein weiterer Ort gibt demselben Objekt einen
  zweiten Knoten.
- **Versionsstatus und Objektzustand.** Entwurf, veröffentlicht und archiviert sind Status einer Version. Ein
  Objektzustand (zum Beispiel „Nicht gesperrt“) ist eine Kennzeichnung aus einer Objektzustandsgruppe, die Richtlinien
  prüfen können.

## Eine einstündige Einheit

| Zeit | Aktivität |
|---|---|
| 0:00 | Das Formularbild: Zeichnen Sie ein leeres Formular (Klasse), seine Kästchen (Attribute) und eine ausgefüllte Kopie (Objekt) |
| 0:10 | Die Teilnehmenden öffnen **Inhaltsstruktur**, öffnen einen Artikel, klicken auf **Bearbeiten** und nennen drei Attribute seiner Klasse |
| 0:20 | Objekt und Knoten: Öffnen Sie den Reiter **Orte** eines Elements; erklären Sie das Bild der Verknüpfung |
| 0:25 | Versionen: Die Teilnehmenden bearbeiten und veröffentlichen ein Testelement zweimal und öffnen **Versionen verwalten** |
| 0:35 | Sektionen, Rollen und Richtlinien: der Aufkleber und der Schlüsselbund; öffnen Sie **Benutzer > Rollen und Richtlinien** und lesen Sie eine Rolle als Sätze vor |
| 0:45 | Siteaccesses: Öffnen Sie die öffentliche Website und die Administration nebeneinander; derselbe Inhalt, verschiedene Türen |
| 0:50 | Designs, Erweiterungen und INI in jeweils zwei Minuten (für Administratoren und Entwickler) |
| 0:55 | Das Quiz unten |

Für Redakteure hören Sie nach 0:45 auf und nutzen die letzten 15 Minuten für Fragen. Für Entwickler erweitern Sie
0:50 um einen Blick auf `settings/site.ini` und `php bin/php/console exp:ini`
([exp:ini](../../../eng-us/features/6.0/exp-ini-command.md)).

## Praktische Übung

Auf einer Schulungsinstallation, als Administrator:

1. Öffnen Sie **Inhaltsstruktur** und öffnen Sie einen beliebigen Artikel.
2. Klicken Sie auf **Bearbeiten**, schreiben Sie die Namen der Felder (Attribute) auf, klicken Sie dann auf
   **Entwurf verwerfen** und bestätigen Sie.
3. Öffnen Sie auf der Seite des Artikels den Reiter **Details** und suchen Sie seine Knoten-ID, seine Objekt-ID und
   seine Sektion.
4. Öffnen Sie **Benutzer > Rollen und Richtlinien**, öffnen Sie die Rolle **Editor** und lesen Sie ihre ersten drei
   Richtlinien als Sätze.
5. Öffnen Sie die öffentliche Website in einem zweiten Tab und suchen Sie denselben Artikel. Vergleichen Sie die
   Adresse in beiden Tabs: Welcher Teil nennt den Siteaccess?

**Erwartetes Ergebnis:** Die Teilnehmenden können drei Attribute, zwei IDs und eine Sektion eines Elements nennen,
sagen, was eine Richtlinie der Rolle Editor erlaubt, und auf den Siteaccess-Teil einer Adresse zeigen.

## Quiz

1. Was ist der Unterschied zwischen einer Inhaltsklasse und einem Inhaltsobjekt?
2. Ein Artikel erscheint an zwei Stellen im Baum. Wie viele Objekte und wie viele Knoten gibt es?
3. Ist „Entwurf“ ein Objektzustand?
4. Was bewirkt eine Rolle, bevor sie zugewiesen ist?
5. Nennen Sie zwei Siteaccesses einer Standardinstallation.

<details>
<summary>Antworten</summary>

1. Die Klasse ist die Definition (das leere Formular); das Objekt ist ein einzelner Inhalt, der daraus entstanden ist
   (ein ausgefülltes Formular).
2. Ein Objekt, zwei Knoten.
3. Nein. Entwurf ist ein Status einer Version; Objektzustände sind Kennzeichnungen aus einer Objektzustandsgruppe
   ([Glossar](../../../eng-us/glossary.md)).
4. Nichts; eine Rolle gewährt erst dann Zugriff, wenn sie Benutzern oder Gruppen zugewiesen ist
   ([Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)).
5. Zum Beispiel `site` und `admin` (und auf neuen Installationen der Siteaccess des Editors).

</details>

## Weiterführende Literatur

- [Glossar](../../../eng-us/glossary.md): jeder Begriff mit einem Link auf die Seite, die ihn erklärt.
- [Das Inhaltsmodell und das Bearbeiten von Inhalten](../../anleitungen/inhaltsmodell-und-bearbeitung.md)
- [Erste Schritte](../erste-schritte.md) und [Ein Rundgang durch die Administration](../rundgang-durch-die-administration.md)

## Verwandte Seiten

- [Trainer: hier beginnen](trainer-hier-beginnen.md)
- [Kurskatalog](kurskatalog.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
