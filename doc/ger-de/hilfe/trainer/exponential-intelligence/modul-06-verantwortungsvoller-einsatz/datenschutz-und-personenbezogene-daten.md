---
title: "Lektion 6.1: Datenschutz: was an den Anbieter geht und was nie dorthin gehört"
audience: trainer, editor, admin
category: training
order: 61
summary: Die Teilnehmenden erklären, was der Assistent an einen Modellanbieter schickt, wenden die Regel an, nie personenbezogene Daten in eine Anfrage zu schreiben, erkennen die Ablehnungen der Datenrichtlinie und schreiben unsichere Aufträge um. 45 Minuten.
keywords: exponential intelligence, datenschutz, personenbezogene daten, anbieter, region, aufbewahrung, datenrichtlinie, was geschickt wird, prompts, aufträge, bilder von menschen, lokales modell, privacy, personal data, data protection, provider, retention, data policy, what is sent, briefs, images of people, local model
type: help
---
# Lektion 6.1: Datenschutz: was an den Anbieter geht und was nie dorthin gehört

Jede Anfrage an ein gehostetes Modell schickt Text an ein Unternehmen außerhalb der Website. Der Assistent ist so
gebaut, dass er so wenig wie nötig schickt, und die Website kann Sektionen und Klassen von manchen Anbietern
fernhalten. Die letzte Verteidigungslinie ist der Redakteur: **keine personenbezogenen Daten in einer Anfrage**. Diese
Lektion macht das zur Gewohnheit.

> **Hinweis**
> Diese Lektion beschreibt, was die Software tut. Die Datenschutzregeln Ihrer Organisation und Ihr Vertrag mit dem
> Anbieter entscheiden, was erlaubt ist; fragen Sie im Zweifel Ihre Datenschutzansprechperson.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Exponential Intelligence](../README.md) |
| Modul | [Modul 6: Verantwortungsvoller Einsatz](README.md) |
| Dauer | 45 Minuten (25 Präsentation, 20 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Alle, die den Assistenten nutzen; Administratoren |
| Voraussetzungen | [Modul 2](../modul-02-der-intelligence-tab/README.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- aufzählen, was der Tab Intelligence, das Bearbeitungsformular und der Alternativtext an den Anbieter schicken und was
  nie geschickt wird;
- Region und Aufbewahrung eines Anbieters erklären und sagen, wo die Website sie festhält;
- eine Ablehnung durch die Datenrichtlinie erkennen und ein erlaubtes Modell wählen;
- einen Auftrag so umschreiben, dass er keine personenbezogenen Daten enthält;
- nennen, wann ein lokales Modell die richtige Wahl ist.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: die Seiten zur Sicherheit und zur Datenrichtlinie unter **EI: Help** (`extension/expai/doc/security.md`,
`extension/expai/doc/data-policy.md`).

1. **Was geschickt wird** (6 Minuten):
   - Tab Intelligence: die Anweisungen (mit Name, Klasse und Pfad des geöffneten Elements und der Standardsprache der
     Website), die Werkzeugdefinitionen, Ihre Nachrichten und die Ergebnisse der Werkzeuge, die das Modell aufruft:
     Namen und Feldwerte von Inhalten, die Sie lesen dürfen, Klassendefinitionen. Bilder werden hier nicht geschickt
     (nur ihre Alternativtexte); Dateien und Felder von Benutzerkonten nie.
   - Bearbeitungsformular: Name und Klasse des Elements, das Feld, sein Wert und die Werte der anderen Felder
     (höchstens etwa 30 KB), Ihre Anweisung.
   - Alternativtext: das gespeicherte Bild, der Seitentitel, der Text der anderen Felder und die Sprache.
   - Nicht geschickt: Ihr Name, Login oder Ihre E-Mail-Adresse, Passwörter, Sitzungsdaten, Entwürfe anderer Benutzer.
2. **Die Regel** (4 Minuten). Schreiben Sie sie an die Tafel: *Keine personenbezogenen Daten in einer Anfrage.* Keine
   Namen von Privatpersonen, keine Adressen, Telefonnummern, E-Mail-Adressen, Kunden- oder Patientendaten, keine
   internen Fallnummern. Inhalte, die bereits öffentlich sind, sind etwas anderes als der Brief eines Kunden, der in
   einen Auftrag kopiert wird.
3. **Region und Aufbewahrung** (5 Minuten). Jeder Anbieter ist mit einer Region (`us`, `eu`, `local`) und einer
   Aufbewahrung konfiguriert (keine, Zero Data Retention, eine Anzahl von Tagen, unbekannt); **EI: Summary** listet sie
   auf. `local` bedeutet, dass die Daten den Server nie verlassen.
4. **Die Datenrichtlinie** (5 Minuten). Die Website kann pro Sektion und Klasse Regionen oder Zero Retention verlangen.
   Der Bereich lehnt dann ab: *The data policy keeps "<name>" (object <id>) away from <provider>: ...*, und der
   Redakteur wählt ein anderes Modell. Listen lassen zurückgehaltene Elemente weg. Für Bilder können strengere Regeln
   gelten.
5. **Bilder von Menschen** (2 Minuten). Schicken Sie Fotos von Menschen nie ohne Einwilligung an ein Modell.

### Häufige Fragen

**Ist es sicher, nach einem veröffentlichten Artikel zu fragen?** Sein Text ist bereits öffentlich; der Anbieter
erhält ihn trotzdem. Folgen Sie den Regeln Ihrer Organisation für den Anbieter.

**Kann ich eine Kunden-E-Mail einfügen und um einen Antwortentwurf bitten?** Nicht mit personenbezogenen Daten darin.
Entfernen Sie zuerst Namen und Einzelheiten, oder verwenden Sie den Assistenten dafür nicht.

**Trainiert der Anbieter mit unseren Inhalten?** Das hängt von den Bedingungen des Anbieters ab; der Administrator hält
die Aufbewahrung und das Datum fest, an dem die Bedingungen geprüft wurden.

### Stolperfallen

- **„Nur dieses eine Mal.“** Personenbezogene Daten in einem Auftrag werden sofort geschickt; sie lassen sich nicht
  zurückholen.
- **Eingefügte Dokumente.** Lange eingefügte Texte enthalten Signaturen, Fußzeilen und Adressen. Bereinigen Sie sie
  zuerst.
- **Annehmen, dass die Datenrichtlinie alles abdeckt.** Sie deckt Inhalte im Baum nach Sektion und Klasse ab, nicht
  das, was Sie eintippen.

## Praktische Übung

Arbeiten Sie zuerst auf Papier, dann in der Schulungsinstallation.

1. Der Trainer zeigt fünf Aufträge am Beamer. Schreiben Sie zu jedem, ob er sicher ist, und, falls nicht, eine sichere
   Fassung:
   - `Write a thank-you note to Maria Example from 12 Sample Street for her complaint about invoice 4711.`
   - `Summarize the published article on our opening hours for the newsletter.`
   - `Rewrite this patient's question in plain language: ...`
   - `Write three FAQ answers about returning products, general, no names.`
   - `Here is our customer list, write a personal greeting to each.`
2. Vergleichen Sie Ihre Antworten mit Ihrem Nachbarn.
3. Öffnen Sie **EI: Summary**, wenn Ihre Rolle es hat (oder der Trainer zeigt es): Lesen Sie Region und Aufbewahrung
   des Anbieters, den Ihr Schulungsmodell verwendet.
4. Wenn der Trainer für eine Schulungssektion eine Datenrichtlinie gesetzt hat, öffnen Sie dort ein Element und
   versuchen Sie eine Anfrage mit einem Modell außerhalb der erlaubten Region: Lesen Sie die Ablehnung und wählen Sie
   ein erlaubtes Modell.

## Erwartetes Ergebnis

- Sicher: Aufträge 2 und 4. Unsicher: 1, 3 und 5; sichere Fassungen entfernen jede persönliche Einzelheit (oder die
  Aufgabe wird gar nicht dem Assistenten gegeben).
- Die Teilnehmenden kennen Region und Aufbewahrung des Schulungsanbieters.
- Wo gezeigt, hat die Ablehnung das Element, den Anbieter und die verlangten Regionen genannt.
- Der Trainer prüft die umgeschriebenen Aufträge von zwei Teilnehmenden.

## Quiz

1. Nennen Sie drei Dinge, die der Tab Intelligence an den Anbieter schickt.
2. Nennen Sie drei Dinge, die er nie schickt.
3. Was bedeutet die Region `local`?
4. Eine Anfrage wird von der Datenrichtlinie abgelehnt. Was tun Sie?
   A. Erneut versuchen B. Ein Modell wählen, dessen Anbieter erlaubt ist C. Die Datenrichtlinie ausschalten D. Den Text
   woanders einfügen
5. Schreiben Sie sicher um: `Draft a reply to Jan Example about his late order 5512.`

<details>
<summary>Antworten</summary>

1. Drei beliebige aus: Anweisungen mit Name, Klasse und Pfad des Elements; Werkzeugdefinitionen; Ihre Nachrichten;
   Werkzeugergebnisse mit Inhalten, die Sie lesen dürfen; Klassendefinitionen.
2. Drei beliebige aus: Ihr Name, Login oder Ihre E-Mail-Adresse; Passwörter; Sitzungsdaten; Entwürfe anderer Benutzer;
   Dateien; Felder von Benutzerkonten.
3. Das Modell läuft auf dem Server; die Daten verlassen ihn nie.
4. **B.**
5. Zum Beispiel: `Draft a general, friendly reply template for a customer whose order is late, without names or order
   numbers.`

</details>

## Weiterführende Informationen

- [Standards für den Datenschutz](../../../../../eng-us/standards/privacy.md): die Datenschutzerklärung des Projekts.
- Die Seiten zur Sicherheit, zur Datenrichtlinie und zu Summary unter **EI: Help**.

## Verwandte Seiten

- [Modul 6: Verantwortungsvoller Einsatz](README.md)
- [Exponential Intelligence: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
