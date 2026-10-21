---
title: "Lektion 6.4: Abschlussprojekt, eine Erweiterung und ein Theme"
audience: trainer, developer, designer
category: training
order: 64
summary: Ein zweitägiges Abschlussprojekt für Entwickler und Designer, eine Theme-Erweiterung und eine funktionale Erweiterung für eine erfundene Organisation bauen, mit einer geschützten Modul-View, Templates und Overrides, ohne Inline-Code, mit konsistenten Metadaten und einem lokalen Git-Verlauf, bereit für ein erstes Release, bewertet mit einem Bewertungsraster aus sieben Kriterien. Zwei Tage plus Präsentationen.
keywords: prüfungen und praktische übungen, abschlussprojekt, erweiterung, theme-erweiterung, entwickler, theme-design, modul-view, richtlinie, override, metadaten, release-reife, bewertungsraster, assessments and practical exercises, capstone, extension, theme extension, developers, theme design, module view, policy, CSP, metadata, Git, release readiness, rubric
type: help
---
# Lektion 6.4: Abschlussprojekt, eine Erweiterung und ein Theme

Ein Entwickler und ein Designer (oder eine Person, die beides macht) liefern ein kleines Produkt für eine erfundene
Organisation: ein Theme für ihre öffentliche Website und eine Erweiterung mit einer Funktion. Das Ergebnis muss bereit
für ein erstes Release sein, ohne das Release selbst.

> **Hinweis**
> Dies ist die eigene Prüfung des Trainers, keine offizielle Zertifizierung. Die Arbeit bleibt auf den
> Schulungsinstallationen und in lokalen Git-Repositorys: Nichts wird gepusht, getaggt oder veröffentlicht, weil ein
> gepushter Tag endgültig ist.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Prüfungen und praktische Übungen](../README.md) |
| Modul | [Modul 6: Abschlussprojekte](README.md) |
| Dauer | Zwei Tage: 15 Minuten Einweisung, etwa 10 Stunden Arbeit mit drei Zwischenständen, 15 Minuten Präsentation pro Paar, 1 Stunde Bewertung |
| Niveau | Experten |
| Zielgruppe | Teilnehmende am Ende von Entwickler und Theme-Design |
| Voraussetzungen | Diese Kurse; pro Paar eine Schulungsinstallation aus einem Git-Klon, eine Shell, ein Editor und Git |

## Lernziele

Paare, die dieses Abschlussprojekt bestehen, haben gezeigt, dass sie Folgendes können:

- eine Theme-Erweiterung bauen, die nur von einem Siteaccess verwendet wird, ohne Inline-Code;
- eine funktionale Erweiterung mit einer geschützten View, eigenen Templates und eigenen Einstellungen bauen;
- die beiden Erweiterungen über Ladereihenfolge und Overrides zusammenarbeiten lassen;
- die Metadaten konsistent und den Verlauf sauber und lesbar halten;
- erklären, was ein erstes Release erfordern würde, ohne es zu veröffentlichen.

## Hinweise für Trainer

### So führen Sie es durch

1. **Vorher**: eine Schulungsinstallation pro Paar, aus einem Git-Klon, über einen lokalen Port ausgeliefert, mit
   Artikeln im Inhaltsbaum. Vereinbaren Sie mit jedem Paar, wer beim Theme und wer bei der Erweiterung die Führung
   übernimmt; beide beantworten Fragen zu beidem.
2. **Zwischenstände**: Mittag von Tag 1 (beide Erweiterungen aktiv, leer), Ende von Tag 1 (die Seite der Funktion
   funktioniert; das Theme gestaltet die Website), Mittag von Tag 2 (Overrides und Zugriffskontrolle erledigt).
3. **Präsentation**: Das Paar zeigt die Website, die Funktion als anonymer Besucher und als berechtigter Benutzer, das
   grep nach Inline-Code, `php -l` und die Template-Prüfungen sowie `git log`.

### Stolperfallen

- **Ausufernder Umfang.** Die Funktion ist eine Seite. Feinschliff zählt mehr als Funktionen.
- **Globales `SiteDesign`.** Die Administration verwandelt sich in das Theme. Wird bewertet.

## Praktische Übung

Die Aufgabenstellung, wie sie an die Paare ausgeteilt wird.

*Der Greenfield Farmers' Market (ein erfundener Markt) möchte ein frisches Aussehen für seine Website und eine Seite,
die die Stände dieser Woche auflistet.*

1. Theme-Erweiterung `<pair>theme`: ein eigenes Pagelayout, ein aus dem Design geladenes Stylesheet, registriert mit
   `DesignExtensions[]`, nur vom öffentlichen Siteaccess verwendet; `ezinfo.php`, `extension.xml`, `LICENSE` und eine
   `README.md`, die die Zeile `SiteDesign` dokumentiert.
2. Funktionserweiterung `<pair>market`: ein Modul `market` mit einer View `stalls`, die die Unterelemente eines Ordners
   namens **Stalls** (von Ihnen angelegt, mit fünf Ordner- oder Artikelelementen) über ein Template auflistet, wobei
   jeder ausgegebene Wert mit wash ausgegeben wird.
3. Zugriff: Die View verlangt eine Funktion `read`; eine Rolle gewährt sie der Gruppe Anonymous Users erst, nachdem Sie
   entschieden haben, dass die Seite öffentlich ist. Weisen Sie beide Zustände nach.
4. Einstellungen: Der Knoten des Ordners wird aus einer eigenen Einstellungsdatei (`<pair>market.ini`) gelesen, nicht
   in den Code geschrieben.
5. Overrides: Das Theme überschreibt `node/view/full.tpl` für Artikel; das Template der Funktion verwendet die
   Stylesheet-Klassen des Themes. Die Funktionserweiterung deklariert in ihrer `extension.xml`, dass sie das Theme
   verwendet, wo das nötig ist, oder Sie erklären, warum nicht.
6. Qualität: `php -l` auf jeder PHP-Datei, die Template-Prüfung für beide Designs und das grep nach Inline-Code ohne
   Treffer.
7. Git: ein Repository pro Erweiterung, Commits mit den Präfixen `Added:`, `Updated:`, `Removed:`, `Renamed:`, Version
   `0.1.0` in `ezinfo.php` und `extension.xml` jeder Erweiterung, kein Tag.
8. Ein einseitiger Release-Plan: wie Sie die Nummer anhand des bereits Veröffentlichten wählen würden, was Sie vor dem
   Tag prüfen würden und was Sie tun würden, wenn sich eine veröffentlichte Version als falsch erweist.

## Erwartetes Ergebnis

- Die öffentliche Website verwendet das Theme; die Administration nicht. Die Seite mit den Ständen funktioniert und
  ist wie geplant geschützt.
- Die Prüfungen sind ohne Befund; jedes Repository hat einen lesbaren Verlauf und konsistente Versionsfelder; es gibt
  keine Tags.
- Der Release-Plan stimmt mit dem Release-Abschnitt des Leitfadens zu Erweiterungen überein.

## Bewertungsraster

| Kriterium | 0: Nicht gezeigt | 1: Teilweise | 2: Größtenteils | 3: Vollständig | Nachweis |
|---|---|---|---|---|---|
| Theme | Nicht verwendet | Überall verwendet (global) | Nur öffentlich, Lücken in den Dateien | Nur öffentlich, vollständige Dateien, README dokumentiert `SiteDesign` | Beide Websites, Dateien |
| Seite der Funktion | Keine | Statische Seite | Listet Elemente auf, Werte nicht mit wash ausgegeben | Listet die Unterelemente des Ordners auf, mit wash | Die Seite |
| Zugriffskontrolle (Pflicht) | Ungeschützt | Deklariert, nicht verlangt | Verlangt, ein Zustand nicht gezeigt | Beide Zustände nachgewiesen | Anonyme und berechtigte Ansicht |
| Einstellungen | Fest im Code | Einstellungsdatei, nicht verwendet | Verwendet, nicht dokumentiert | Aus eigener INI gelesen, dokumentiert | Code, INI |
| Overrides und Zusammenarbeit | Keine | Override ohne Regel | Funktioniert, Abhängigkeit nicht bedacht | Funktioniert, Abhängigkeit deklariert oder begründet | Seiten, `extension.xml` |
| Qualitätsprüfungen | Keine | Einige Dateien geprüft | Alle geprüft, ein Befund übrig | Alle ohne Befund, grep leer | Terminal-Ausgabe |
| Git und Release-Reife | Nichts | Ein Repository, keine Präfixe | Beide, Versionen widersprüchlich | Beide, Präfixe, konsistente Versionen, kein Tag, schlüssiger Release-Plan | `git log`, Dateien, Plan |

**Bestanden**: 14 von 21 Punkten oder mehr und **Zugriffskontrolle** mit 2 oder 3.

## Quiz

Fragen für die Nachbesprechung nach den Präsentationen.

1. Warum steht der Knoten des Ordners mit den Ständen in einer Einstellungsdatei?
2. Wovor hat das grep nach Inline-Code die Website geschützt?
3. Was geschieht in Ihrem Release-Plan, wenn sich `0.1.0` nach der Veröffentlichung als falsch erweist?
4. Welche Erweiterung wird zuerst geladen, und warum ist das hier wichtig?

<details>
<summary>Antworten</summary>

1. Damit jemand bei der Installation auf einen eigenen Ordner verweisen kann, ohne Code zu ändern
   ([Erweiterungen](../../../../anleitungen/extensions.md#64-eigene-einstellungen-inhalte-und-cronjobs)).
2. Davor, dass etwas kaputtgeht, wenn eine Website eine Content Security Policy einschaltet
   ([Inline-Skripte und -Styles entfernen](../../../../../eng-us/help/designers/lifecycle/removing-inline-scripts-and-styles.md)).
3. Lassen Sie sie stehen; veröffentlichen Sie `0.1.1` mit der Korrektur und schreiben Sie in deren Hinweise, was
   falsch war ([Erweiterungen](../../../../anleitungen/extensions.md#7-die-extension-veröffentlichen)).
4. Die, die in der Liste weiter vorn steht (oder die durch die Abhängigkeiten in `extension.xml` eingeordnet ist); sie
   gewinnt bei Einstellungen und Templates
   ([Ladereihenfolge und welches Theme gewinnt](../../../../../eng-us/help/designers/extensions/loading-order-for-themes.md)).

</details>

## Weiterführende Literatur

- [Entwickler, praktische Aufgabe](../modul-04-technische-kurse/entwickler-praktische-aufgabe.md) und
  [Theme-Designer, praktische Aufgabe](../modul-04-technische-kurse/theme-designer-praktische-aufgabe.md)
- [Checkliste: Theme-Erweiterung bereit](../../../../../eng-us/help/designers/extensions/checklist-theme-extension-ready.md)
- [Erweiterungsentwicklung](../../../erweiterungsentwicklung/README.md)

## Verwandte Seiten

- [Modul 6: Abschlussprojekte](README.md)
- [Prüfungen und praktische Übungen: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
