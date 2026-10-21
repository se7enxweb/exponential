---
title: "Lektion 3.4: Suche, praktische Aufgabe"
audience: trainer, admin, developer
category: training
order: 34
summary: Eine praktische Aufgabe von 45 Minuten für das Ende des Kurses Suche, Elemente mit Suchbereich, erweiterter Suche und Inhaltsfinder finden, auf einer Schulungsinstallation die verzögerte Indizierung einschalten, nachweisen, dass neue Inhalte auf Cron warten, sie von Hand indizieren, den Index neu aufbauen und die Einstellung zurücksetzen, bewertet mit einem Bewertungsraster aus fünf Kriterien. 70 Minuten mit Bewertung.
keywords: prüfungen und praktische übungen, suche, praktische aufgabe, bewertungsraster, suchbereich, erweiterte suche, inhaltsfinder, verzögerte indizierung, ezpending_actions, runcronjobs, indexcontent, updatesearchindex, assessments and practical exercises, search, practical task, rubric, search scope, advanced search, content finder, delayed indexing
type: help
---
# Lektion 3.4: Suche, praktische Aufgabe

Suchprobleme lauten fast immer „es wird noch nicht gefunden“. In dieser Aufgabe suchen die Teilnehmenden zuerst wie
Redakteure, stellen dann absichtlich den häufigsten Indizierungsfehler nach, beheben ihn und hinterlassen die
Installation so, wie sie sie vorgefunden haben.

> **Hinweis**
> Dies ist die eigene Prüfung des Trainers, keine offizielle Zertifizierung. Die Teilnehmenden ändern Einstellungen nur
> auf ihrer eigenen Schulungsinstallation, niemals auf einer Live-Website.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Prüfungen und praktische Übungen](../README.md) |
| Modul | [Modul 3: Die Administrationskurse prüfen](README.md) |
| Dauer | 70 Minuten (5 Einweisung, 45 Aufgabe, 20 Bewertung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Teilnehmende am Ende des Kurses Suche |
| Voraussetzungen | [Lektion 3.3](suche-schriftlicher-test.md); Administratorzugang und eine Shell auf der eigenen Schulungsinstallation der Teilnehmenden |

## Lernziele

Wer diese Aufgabe besteht, hat gezeigt, dass er Folgendes kann:

- Elemente mit dem Suchbereich, der erweiterten Suche und dem Inhaltsfinder finden;
- die verzögerte Indizierung einschalten und ihre Wirkung erklären;
- wartende Inhalte mit dem Standard-Cronjob-Teil indizieren;
- den Index aus der Shell neu aufbauen;
- die ursprüngliche Einstellung wiederherstellen und prüfen.

## Hinweise für Trainer

### So führen Sie sie durch

1. **Vorher**: eine Schulungsinstallation pro Teilnehmer mit einem Seitenpaket, das Inhalte hat (Artikel in mehreren
   Ordnern). Die Teilnehmenden brauchen eine Shell im Wurzelverzeichnis der Installation.
2. **Einweisung**: Teilen Sie die Schritte und das Bewertungsraster aus; 45 Minuten. Erinnern Sie die Teilnehmenden
   daran, jeden ausgeführten Befehl zu notieren.
3. **Bewertung**: Lesen Sie die Notizen der Teilnehmenden und prüfen Sie die endgültige Einstellung mit
   `./console exp:ini get site.ini/SearchSettings/DelayedIndexing --allow-root-user`.

### Häufige Fragen

**Der neue Artikel wird sofort gefunden, obwohl die verzögerte Indizierung eingeschaltet ist.** Der INI-Cache wurde
nach der Änderung nicht geleert; das ist ein Nachweis für das Bewertungsraster, keine defekte Übungsumgebung.

**Kann der Cronjob-Teil stattdessen unter Setup > Cronjobs ausgeführt werden?** Ja, der Teil, der `indexcontent.php`
enthält.

### Stolperfallen

- **Geänderte Einstellungen zurückgelassen.** Wer Schritt 10 vergisst, lässt die verzögerte Indizierung eingeschaltet;
  der nächste Kurs trifft auf eine verwirrende Installation. Das wird bewertet.

## Praktische Übung

Die Aufgabe, wie sie an die Teilnehmenden ausgeteilt wird. Verwenden Sie Ihre eigene Schulungsinstallation; notieren
Sie jeden Befehl.

1. Suchen Sie in der Administration nach einem Wort, das in mehreren Artikeln vorkommt. Grenzen Sie dieselbe Suche dann
   mit dem **Suchbereich** auf **Aktueller Ort** unterhalb eines Ordners ein. Notieren Sie beide Trefferzahlen.
2. Öffnen Sie die **Erweiterte Suche**. Suchen Sie nach demselben Wort, mit **Klasse** auf *Article* und
   **Veröffentlicht** auf das letzte Jahr gesetzt. Notieren Sie die Anzahl.
3. Notieren Sie die Knotennummer eines Artikels von seiner Seite. Öffnen Sie das Dashboard und öffnen Sie dann diesen
   Artikel erneut, indem Sie seine Nummer in den **Inhaltsfinder** eingeben.
4. Schalten Sie die verzögerte Indizierung in `settings/override/site.ini.append.php` ein:

   ```ini
   [SearchSettings]
   DelayedIndexing=enabled
   ```

   und leeren Sie den INI-Cache:

   ```bash
   php bin/php/ezcache.php --clear-tag=ini --allow-root-user
   ```

5. Erstellen und veröffentlichen Sie einen Artikel mit dem Titel `Indexing test <your name>` und einem Wort, das sonst
   niemand verwendet, zum Beispiel `zebracrossing`.
6. Suchen Sie nach diesem Wort. Notieren Sie das Ergebnis.
7. Führen Sie den Standard-Cronjob-Teil von Hand aus:

   ```bash
   php runcronjobs.php -s <your public siteaccess> --allow-root-user
   ```

   (oder führen Sie ihn unter **Setup > Cronjobs** aus). Suchen Sie erneut und notieren Sie das Ergebnis.
8. Bauen Sie den ganzen Index neu auf:

   ```bash
   php bin/php/updatesearchindex.php --clean --allow-root-user
   ```

   Suchen Sie noch einmal nach dem Wort.
9. Schreiben Sie zwei Sätze: warum Schritt 6 nichts gefunden hat und worauf ein Hoster bei einer Website mit
   verzögerter Indizierung achten muss.
10. Entfernen Sie die beiden Zeilen aus Schritt 4 (oder setzen Sie `DelayedIndexing=disabled`), leeren Sie den
    INI-Cache erneut und prüfen Sie den Wert mit
    `./console exp:ini get site.ini/SearchSettings/DelayedIndexing --allow-root-user`.

## Erwartetes Ergebnis

- Notizen mit drei Trefferzahlen, die stimmig sind (eingegrenzte und gefilterte Zahlen nicht größer als die erste).
- Schritt 6 hat nichts gefunden; Schritt 7 und Schritt 8 haben den neuen Artikel gefunden.
- Zwei richtige Sätze: Das Veröffentlichen hat das Objekt nur in die Warteschlange gestellt; Cron muss den
  Standardteil ausführen.
- Die abschließende Prüfung gibt `disabled` aus.

## Bewertungsraster

| Kriterium | 0: Nicht gezeigt | 1: Teilweise | 2: Größtenteils | 3: Vollständig | Nachweis |
|---|---|---|---|---|---|
| Suchen in der Administration | Nichts gefunden | Eines von Suchbereich, erweiterter Suche, Inhaltsfinder | Zwei davon | Alle drei, Zahlen notiert | Die Notizen |
| Verzögerte Indizierung eingeschaltet | Nicht erledigt | Einstellung geschrieben, Cache nicht geleert | Eingeschaltet, Wirkung nicht beobachtet | Eingeschaltet, Schritt 6 zeigt, dass nichts gefunden wird | Die Notizen, die Einstellungsdatei |
| Indizierung durch Cron | Nicht erledigt | Falscher Teil oder Siteaccess | Ausgeführt, Ergebnis nicht geprüft | Ausgeführt, Artikel gefunden | Die Notizen, der Befehl |
| Neuaufbau des Index | Nicht erledigt | Ohne `--clean` und ohne Erklärung ausgeführt | Ausgeführt, Ergebnis nicht geprüft | Mit `--clean` ausgeführt, Artikel gefunden | Die Notizen |
| Sauber hinterlassen (Pflicht) | Einstellung noch `enabled` | Geändert, Cache nicht geleert | Ausgeschaltet, nicht geprüft | Ausgeschaltet, Cache geleert, geprüft | Ausgabe von `exp:ini get` |

**Bestanden**: 10 von 15 Punkten oder mehr und **Sauber hinterlassen** mit 2 oder 3.

## Quiz

Fragen für die Nachbesprechung nach der Bewertung.

1. In welcher Tabelle hat Ihr Artikel zwischen Schritt 5 und Schritt 7 gewartet?
2. Wann lohnt es sich, die verzögerte Indizierung wirklich einzuschalten?
3. Wann würden Sie Schritt 8 auf einer großen Website ausführen, und als welcher Benutzer?
4. Was hat Ihnen Schritt 1 über den Bereich einer Suche gezeigt?

<details>
<summary>Antworten</summary>

1. `ezpending_actions` ([10.8](../../../../installation/10-nach-der-installation.md#suche)).
2. Wenn das Veröffentlichen großer Objekte langsam ist; es macht das Veröffentlichen schneller, um den Preis der
   Abhängigkeit von Cron ([Such-Backends](../../../hosting/grundlagen/such-backends.md#verzögerte-indizierung-und-cron)).
3. In einer ruhigen Stunde, als Benutzer der Website
   ([Such-Backends](../../../hosting/grundlagen/such-backends.md#den-index-neu-aufbauen)).
4. Ein Suchbereich unterhalb des aktuellen Orts findet nur, was darunter liegt
   ([Suchen](../../../suchen.md#die-suche-eingrenzen)).

</details>

## Weiterführende Literatur

- [Such-Backends](../../../hosting/grundlagen/such-backends.md): Indizierung, Neuaufbau und Solr.
- [Cronjobs](../../../../anleitungen/cronjobs.md): einen Teil über die Seite oder die Shell ausführen.

## Verwandte Seiten

- [Lektion 3.3: Suche, schriftlicher Test](suche-schriftlicher-test.md)
- [Modul 3: Die Administrationskurse prüfen](README.md)
- [Prüfungen und praktische Übungen: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
