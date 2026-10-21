---
title: "Lektion 2.3: Eine Ausgabe schreiben"
audience: trainer, editor
category: training
order: 23
summary: Die Teilnehmenden legen in ihrer Liste eine Ausgabe an, fügen zwei Newsletter-Artikel hinzu, veröffentlichen und sehen sich die HTML- und Textvorschau auf der Seite der Ausgabe an. 60 Minuten.
keywords: newsletter mit cjw newsletter, ausgabe, newsletter-artikel, newsletter hier erstellen, vorschau, html, text, ausgabe kopieren, cjw_newsletter_edition, cjw_newsletter_article, newsletters with cjw newsletter, edition, newsletter article, create newsletter here, preview, copy edition
type: help
---
# Lektion 2.3: Eine Ausgabe schreiben

Eine Ausgabe ist eine Nummer einer Liste: ein Inhalt mit eigenen Artikeln. Weil es ein Inhalt ist, nutzen
Redakteure, was sie vom Bearbeiten von Seiten schon kennen: Entwürfe, Versionen, den Online-Editor und die Vorschau.
In dieser Lektion schreiben die Teilnehmenden eine kleine Ausgabe und sehen sich an, wie sie als Mail aussehen wird,
in HTML und als reiner Text.

> **Hinweis**
> Das Schreiben und Veröffentlichen einer Ausgabe sendet nichts. Der Versand ist ein eigener Schritt (Modul 4).

## Auf einen Blick

| | |
|---|---|
| Kurs | [Newsletter mit CJW Newsletter](../README.md) |
| Modul | [Modul 2: Systeme, Listen und Ausgaben](README.md) |
| Dauer | 60 Minuten (15 Präsentation, 45 Übung) |
| Niveau | Einsteiger |
| Zielgruppe | Redakteure, die Newsletter schreiben |
| Voraussetzungen | [Lektion 2.2](eine-liste-anlegen-und-konfigurieren.md): Die Person hat eine Liste; grundlegendes Bearbeiten (Redakteure 101, Modul 3) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- mit **Newsletter hier erstellen** auf der Seite einer Liste eine Ausgabe anlegen;
- die Pflichtfelder **Title** und **Description** ausfüllen;
- der Ausgabe Newsletter-Artikel hinzufügen;
- die Vorschauen **HTML / Text** und **Text** einer Ausgabe öffnen;
- eine vorhandene Ausgabe als Ausgangspunkt für die nächste kopieren.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Eine Ausgabe schreiben und versenden](../../../../../eng-us/help/creators/website-creators/newsletters-with-cjw-newsletter.md#writing-and-sending-an-edition)
und [Inhalte anlegen und bearbeiten](../../../inhalte-bearbeiten.md).

1. **Die Klasse der Ausgabe** (4 Minuten). `CJW Newsletter Edition` hat **Title** (Pflicht), **Short title**,
   **Short description**, **Description** (Pflicht; der Haupttext der Nummer) und **Newsletter edition**, das Feld,
   das die eigenen Daten des Newsletters trägt. Ihr Name in Listen ist der Kurztitel, wenn er ausgefüllt ist.
2. **Artikel** (3 Minuten). Eine Ausgabe ist ein Container. Ihre Elemente der Klasse `CJW Newsletter Article` haben
   **Title** und **Short description**, beide Pflicht. Der Standard-Skin gibt die Beschreibung aus, dann die Artikel.
3. **Vorschau** (4 Minuten). Auf der Seite der Ausgabe zeigen die Reiter **HTML / Text** und **Text** die Ausgabe so
   gerendert wie die Teile der Mail. Das ist nur eine Vorschau; die echte Mail wird beim Versand erneut gerendert, im
   Skin des Versands.
4. **Kopieren** (2 Minuten). Die Seite der Ausgabe hat **Kopieren**. Kopieren Sie die Ausgabe des letzten Monats,
   ändern Sie sie, und Sie behalten die Struktur. Ein Zeitplan kann auch eine Vorlage-Ausgabe kopieren (Lektion 2.4).
5. **Status** (2 Minuten). Zeigen Sie auf **Statistiken** und **Newsletter Verarbeitungsinformation** auf der Seite:
   leer, bis die Ausgabe gesendet ist.

### Häufige Fragen

**Kann ich Bilder verwenden?** Ja, in den Rich-Text-Feldern, wie auf jeder Seite. Mailprogramme blockieren Bilder
oft, bis der Leser sie zulässt, daher muss der Text auch ohne sie verständlich sein.

**Was, wenn ich eine Ausgabe nach dem Versand ändere?** Der Versand behält, was zum Zeitpunkt des Versands gerendert
wurde. Eine Änderung erzeugt eine neue Version, die Sie als neuen Versand erneut senden würden.

**Kann eine Ausgabe übersetzt werden?** Ja; sie ist ein Inhalt. Eine Liste kann in 4.2 mehrere Sprachen anbieten
(siehe die Rendering-Anleitung, verlinkt von der
[Funktionsseite](../../../../../eng-us/features/6.0/extensions/cjw_newsletter.md#the-features-of-420)).

**Wo schreibe ich den Betreff?** Der Skin setzt den Betreff, normalerweise aus dem Titel der Ausgabe. Wählen Sie den
Titel mit diesem Wissen.

### Stolperfallen

- **Description leer gelassen.** Das Feld ist Pflicht; das Formular verweigert die Veröffentlichung und nennt das
  Feld.
- **Artikel außerhalb der Ausgabe angelegt.** Ein Artikel an der falschen Stelle gehört nicht zur Nummer. Prüfen Sie
  den Pfad vor **Neu erstellen**.
- **Lange Vorschauen.** Eine große Ausgabe rendert im Vorschaureiter auf einem kleinen Schulungsserver langsam; warten
  Sie darauf.

## Praktische Übung

Verwenden Sie die Schulungsinstallation und Ihr Konto. Ersetzen Sie `<name>` durch Ihren Vornamen.

1. Öffnen Sie Ihre Liste `Weekly tips <name>` aus Lektion 2.2.
2. Klicken Sie auf **Newsletter hier erstellen**.
3. Geben Sie in **Title** `Tips for week 1` ein.
4. Lassen Sie **Description** leer und klicken Sie auf **Zur Veröffentlichung schicken**. Lesen Sie die Meldung und
   geben Sie dann zwei Sätze Einleitung in **Description** ein.
5. Klicken Sie auf **Zur Veröffentlichung schicken**. Die Ausgabe erscheint unter **Newsletterausgaben** auf der
   Seite der Liste.
6. Öffnen Sie die Ausgabe. Klicken Sie unter **Unterelemente** auf **Neu erstellen** und wählen Sie **CJW Newsletter
   Article**.
7. **Title**: `Tip 1: Short subject lines`. **Short description**: ein Satz mit einem Rat. Veröffentlichen.
8. Legen Sie auf dieselbe Weise einen zweiten Artikel an, `Tip 2: One idea per issue`.
9. Öffnen Sie auf der Seite der Ausgabe den Reiter **HTML / Text** und lesen Sie den gerenderten Newsletter.
10. Öffnen Sie den Reiter **Text** und vergleichen Sie: Was ist mit den Überschriften und Links geschehen?
11. Klicken Sie bei der Ausgabe auf **Kopieren** und behalten Sie die Kopie in derselben Liste. Benennen Sie die Kopie
    in `Tips for week 2` um.

## Erwartetes Ergebnis

- Die Liste zeigt zwei Ausgaben, *Tips for week 1* und *Tips for week 2*, jede mit zwei Artikeln.
- Der Reiter **HTML / Text** zeigt die Einleitung und beide Tipps im Standard-Skin; der Reiter **Text** zeigt
  denselben Inhalt als reinen Text.
- **Newsletter Verarbeitungsinformation** ist bei beiden Ausgaben leer: Es wurde nichts gesendet.
- Der Trainer prüft das, indem er die Vorschaureiter einer Person am Beamer öffnet.

## Quiz

1. Welcher Knopf auf der Seite einer Liste legt eine Ausgabe an?
2. Welche zwei Felder einer Ausgabe sind Pflicht?
   A. **Title** und **Short title** B. **Title** und **Description** C. **Description** und **Short description**
   D. **Newsletter edition** und **Title**
3. Richtig oder falsch: Das Veröffentlichen einer Ausgabe sendet sie an die Abonnenten.
4. Wo sehen Sie, wie die Ausgabe als reiner Text aussehen wird?
5. Warum sollte der Text einer Ausgabe ohne Bilder verständlich sein?

<details>
<summary>Antworten</summary>

1. **Newsletter hier erstellen**.
2. **B.** Auf einer Standardinstallation von `cjw_newsletter`; prüfen Sie Ihr eigenes Formular.
3. **Falsch.** Der Versand ist ein eigener Schritt mit dem Versandformular und Cron ([Eine Ausgabe schreiben und versenden](../../../../../eng-us/help/creators/website-creators/newsletters-with-cjw-newsletter.md#writing-and-sending-an-edition)).
4. Im Reiter **Text** auf der Seite der Ausgabe.
5. Viele Mailprogramme blockieren Bilder, bis der Leser sie zulässt ([Newsletter sicher versenden](../../../../../eng-us/help/creators/website-creators/sending-newsletters.md#before-every-send)).

</details>

## Weiterführende Informationen

- [Newsletter mit cjw_newsletter](../../../../../eng-us/help/creators/website-creators/newsletters-with-cjw-newsletter.md#writing-and-sending-an-edition):
  die Ausgabe im gesamten Arbeitsablauf.
- [Inhalte anlegen und bearbeiten](../../../inhalte-bearbeiten.md): das Bearbeitungsformular, Entwürfe und Konflikte.
- [Newsletter sicher versenden](../../../../../eng-us/help/creators/website-creators/sending-newsletters.md#before-every-send): die
  Checkliste vor jedem Versand.

## Verwandte Seiten

- [Modul 2: Systeme, Listen und Ausgaben](README.md)
- [Newsletter mit CJW Newsletter: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
