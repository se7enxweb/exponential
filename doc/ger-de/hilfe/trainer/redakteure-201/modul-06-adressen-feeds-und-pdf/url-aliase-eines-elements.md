---
title: "Lektion 6.1: Die Adresse eines Elements und seine URL-Aliase"
audience: trainer, editor
category: training
order: 61
summary: Die Teilnehmenden erklären, wie die Adresse eines Elements aus seinem Namen und seiner Stelle entsteht, lesen die erzeugten Aliase unter URL-Aliase festlegen und legen einen kurzen zusätzlichen Alias an, der zum Element weiterleitet. 45 Minuten.
keywords: redakteure 201, url-alias, adresse, url-aliase festlegen, erzeugte aliase, neuer alias, weiterleitung, hauptverzeichnis, kurze adresse, kampagnenadresse, editors 201, url alias, address, manage url aliases, generated aliases, create new alias, redirect, site root, short address, campaign address
type: help
---
# Lektion 6.1: Die Adresse eines Elements und seine URL-Aliase

Jede Seite der Website hat eine Adresse, gebildet aus den Namen der Elemente auf ihrem Pfad. Manchmal brauchen
Redakteure für eine Kampagne oder einen gedruckten Flyer eine kurze, einprägsame zusätzliche Adresse. Diese Lektion
erklärt erzeugte Adressen und zeigt, wie man einem Element einen Alias hinzufügt.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Redakteure 201](../README.md) |
| Modul | [Modul 6: Adressen, Feeds und PDF](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Redakteure, die Kampagnen veröffentlichen oder Adressen drucken |
| Voraussetzungen | [Modul 3](../modul-03-inhalte-organisieren/README.md) (Inhalte verschieben) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- erklären, wie die Adresse eines Elements aus seinem Namen und seiner Stelle im Baum erzeugt wird;
- **URL-Aliase festlegen** für ein Element öffnen und seine **Erzeugten Aliase** lesen;
- mit **Neuer Alias** einen zusätzlichen Alias anlegen, mit oder ohne Weiterleitung und im Hauptverzeichnis;
- einen selbst angelegten Alias entfernen und sagen, was mit alten Adressen geschieht, wenn ein Element umbenannt oder
  verschoben wird.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Links, URL-Aliase, Platzhalter und Suchstatistiken](../../../../anleitungen/urls-und-aliase.md#3-globale-url-aliase)
(die Aliase von Inhalten liegen auf der eigenen Seite jedes Knotens, unten beschrieben).

1. **Erzeugte Adressen** (3 Minuten). Die Adresse eines Elements wird aus den Namen auf seinem Pfad gebildet, zum
   Beispiel `/news/opening-hours`. Ein Umbenennen oder Verschieben des Elements ändert die erzeugte Adresse; auf den
   meisten Websites führt die alte Adresse weiterhin zum Element, aber prüfen Sie Ihre eigene Website, bevor Sie es
   versprechen.
2. **Die Seite** (4 Minuten). Menü des Elements > **Fortgeschritten** > **URL-Aliase festlegen** öffnet *URL-Aliase für
   <Name> (n)*: **Erzeugte Aliase (n)** mit **URL-Alias** und **Sprache**, die von Ihnen angelegten zusätzlichen Aliase
   und **Neuer Alias**.
3. **Anlegen** (5 Minuten). **URL Alias Name:** die zusätzliche Adresse; **Ziel:** das Element (angezeigt);
   **Sprache:** die Sprache, in der der Alias funktioniert; **Der Alias soll zum Ziel weiterleiten** (Besucher werden
   an die echte Adresse weitergeschickt) und **Alias im Hauptverzeichnis erstellen** (der Alias ist `/flyer` statt unter
   dem übergeordneten Element des Elements); dann **Erstellen**. **Ausgewählte entfernen** und **Alle entfernen**
   entfernen zusätzliche Aliase.
4. **Globale Aliase** (3 Minuten). Adressen für Modulseiten (zum Beispiel `login`) legen Administratoren unter
   **Setup** > **URL-Übersetzer** an, nicht hier.

### Häufige Fragen

**Sollte ein Alias weiterleiten?** Meist ja: Besucher sehen dann die echte Adresse des Elements und legen sie als
Lesezeichen ab, und Suchmaschinen sehen eine Adresse.

**Können zwei Elemente sich einen Alias teilen?** Nein. Eine Adresse kann nur zu einer Stelle führen; die Seite meldet,
wenn eine Adresse vergeben ist.

**Mein Alias funktioniert in der anderen Sprache nicht.** Er funktioniert in der unter **Sprache:** gewählten Sprache.

### Stolperfallen

- **Erzeugte Aliase entfernen.** Die Seite entfernt die zusätzlichen Aliase; versuchen Sie nicht, erzeugte
  „aufzuräumen“.
- **Aliase im Hauptverzeichnis.** Sie konkurrieren mit den Adressen der obersten Ordner. Stimmen Sie die Namen im Team
  ab.
- **Gedruckte Adressen.** Testen Sie einen Alias in einem privaten Fenster, bevor er in den Druck geht.

## Praktische Übung

Verwenden Sie die Schulungsinstallation und Ihren Artikel *Opening hours* (in Lektion 3.1 nach `Archive` verschoben).

1. Öffnen Sie *Opening hours* mit **Auf der Website ansehen** und notieren Sie seine Adresse.
2. Öffnen Sie im Baum das Menü des Artikels und wählen Sie **Fortgeschritten** > **URL-Aliase festlegen**.
3. Lesen Sie den Seitentitel und die Tabelle **Erzeugte Aliase**. Vergleichen Sie sie mit der Adresse aus Schritt 1.
4. Geben Sie unter **Neuer Alias** in **URL Alias Name:** `hours-<Ihr Vorname>` ein, behalten Sie die Sprache,
   kreuzen Sie **Der Alias soll zum Ziel weiterleiten** und **Alias im Hauptverzeichnis erstellen** an und klicken Sie
   auf **Erstellen**.
5. Öffnen Sie in einem privaten Fenster die Adresse der Website, gefolgt von `/hours-<Ihr Vorname>`. Sie landen auf
   der echten Adresse des Artikels.
6. Benennen Sie den Artikel in `Opening hours and holidays` um und veröffentlichen Sie. Öffnen Sie **URL-Aliase
   festlegen** erneut und lesen Sie die erzeugten Aliase.
7. Öffnen Sie die Adresse aus Schritt 1 im privaten Fenster und notieren Sie, was geschieht.
8. Markieren Sie zurück auf **URL-Aliase festlegen** Ihren zusätzlichen Alias und klicken Sie auf **Ausgewählte
   entfernen**.

## Erwartetes Ergebnis

- Der erzeugte Alias entsprach der Adresse aus Schritt 1.
- Der zusätzliche Alias hat in Schritt 5 zum Artikel weitergeleitet.
- Nach dem Umbenennen hat sich der erzeugte Alias geändert; Schritt 7 zeigt, ob die alte Adresse auf dieser Website
  noch zum Artikel führt.
- Der zusätzliche Alias ist nach Schritt 8 weg.
- Der Trainer prüft das, indem er vor Schritt 8 den Alias einer Person am Beamer ausprobiert.

![Die URL-Aliase eines Elements: der erzeugte Alias und das Formular, um einen weiteren hinzuzufügen](../../../../../eng-us/help/images/trainers/e201-url-aliases.webp)

## Quiz

1. Woraus wird die Adresse eines Elements erzeugt?
2. Wo fügen Sie einem Element eine zusätzliche Adresse hinzu?
   A. **Setup** > **URL-Übersetzer** B. Menü des Elements > **Fortgeschritten** > **URL-Aliase festlegen** C. Der Tab
   **Details** D. Das Bearbeitungsformular
3. Was bewirkt **Der Alias soll zum Ziel weiterleiten**?
4. Was ändert **Alias im Hauptverzeichnis erstellen**?
5. Wer legt Adressen für Modulseiten wie die Anmeldeseite an, und wo?

<details>
<summary>Antworten</summary>

1. Aus den Namen der Elemente auf seinem Pfad im Baum.
2. **B.**
3. Besucher, die den Alias öffnen, werden an die echte Adresse des Elements weitergeschickt.
4. Der Alias liegt direkt unter der Adresse der Website statt unter dem übergeordneten Element des Elements.
5. Administratoren, unter **Setup** > **URL-Übersetzer**
   ([Links, URL-Aliase, Platzhalter und Suchstatistiken](../../../../anleitungen/urls-und-aliase.md#3-globale-url-aliase)).

</details>

## Weiterführende Informationen

- [Links, URL-Aliase, Platzhalter und Suchstatistiken](../../../../anleitungen/urls-und-aliase.md): globale Aliase,
  Platzhalter und die Linkliste.
- [URL-Platzhalter](../../../ersteller/website-ersteller/url-platzhalter.md): Muster von Adressen, für Website-Ersteller.

## Verwandte Seiten

- [Modul 6: Adressen, Feeds und PDF](README.md)
- [Redakteure 201: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
