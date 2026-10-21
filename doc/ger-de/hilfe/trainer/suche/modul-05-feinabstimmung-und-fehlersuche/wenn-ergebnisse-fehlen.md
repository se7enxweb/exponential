---
title: "Lektion 5.3: Wenn Ergebnisse fehlen"
audience: trainer, admin
category: training
order: 53
summary: Die Teilnehmenden arbeiten eine feste Checkliste für Meldungen „die Suche findet es nicht“ ab, von Berechtigungen und Sichtbarkeit über verzögerte Indizierung, durchsuchbare Attribute, Einstellungen und den Index bis zu Solr, und lösen vier vorbereitete Fälle auf ihrer Schulungsinstallation. 35 Minuten.
keywords: suchkurs, fehlersuche, fehlende ergebnisse, nicht gefunden, berechtigungen, verborgen, entwurf, papierkorb, DelayedIndexing, durchsuchbar, stoppwörter, neu indizieren, exp:solr, checkliste, search course, troubleshooting, missing results, not found, permissions, hidden, draft, trash, searchable, stop words, reindex, checklist
type: help
---
# Lektion 5.3: Wenn Ergebnisse fehlen

„Die Suche ist kaputt“ ist eine der häufigsten Support-Meldungen, und es liegt selten an der Suchmaschine. Diese
Lektion gibt den Teilnehmenden eine Checkliste in fester Reihenfolge, die günstigsten Prüfungen zuerst, und Übung an
vorbereiteten Fällen, damit sie die Ursache in Minuten finden, statt aus Reflex Indizes neu aufzubauen.

> **Hinweis**
> Der Trainer bereitet die vier Fälle vor der Lektion auf jeder Schulungsinstallation vor (siehe „Dieses Modul
> vorbereiten“ in der Modulübersicht). Üben Sie nie auf einer Live-Website.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Suche](../README.md) |
| Modul | [Modul 5: Feinabstimmung und Fehlersuche](README.md) |
| Dauer | 35 Minuten (10 Präsentation, 25 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Website-Administratoren und Support-Mitarbeitende |
| Voraussetzungen | Module [2](../modul-02-indizierung/README.md) und [3](../modul-03-suchen-in-der-praxis/README.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die vier Fragen stellen, die jede Meldung einrahmen: wer gesucht hat, wo, nach welchen Wörtern und nach welchem
  Element;
- die Checkliste der Reihe nach abarbeiten und bei der ersten Ursache aufhören;
- eine Ursache bei Berechtigung oder Sichtbarkeit von einer Ursache im Index unterscheiden;
- die kleinste Lösung wählen: erneut veröffentlichen, den Cronjob ausführen, eine Einstellung ändern oder den Index
  neu aufbauen.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Suchen](../../../suchen.md#tipps), [Wenn etwas schiefgeht](../../../wenn-etwas-schiefgeht.md),
[10.8 Suchindizierung und Bildverarbeitung](../../../../installation/10-nach-der-installation.md#108-suchindex-und-bildverarbeitung)
und die früheren Lektionen dieses Kurses.

1. **Die Meldung einrahmen** (2 Minuten). Wer hat gesucht (welcher Benutzer oder Besucher), wo (welcher Siteaccess),
   welche Wörter genau, welches Element wurde erwartet (holen Sie seine Knoten-ID; der Inhaltsfinder bestätigt, dass es
   existiert).
2. **Die Checkliste** (6 Minuten). Schreiben Sie sie an die Tafel und lassen Sie sie dort stehen:
   1. Existiert das Element und ist es veröffentlicht? Ein Entwurf oder ein Element im Papierkorb wird nicht gefunden
      (Inhaltsfinder).
   2. Darf die suchende Person es lesen? Wiederholen Sie die Suche als dieser Benutzer oder als Besucher; prüfen Sie
      die Sektion und die Rolle.
   3. Ist es verborgen oder außerhalb des Baums (`PathPrefix`) oder der Sprachen des Siteaccess?
   4. Steht das Wort in einem durchsuchbaren Attribut seiner Klasse (*Ist durchsuchbar* auf der Klassenseite)?
   5. Ist das Wort ein Stoppwort, von `MaximumSearchTextWords` abgeschnitten oder ein Wortteil ohne Platzhalter?
   6. Ist die verzögerte Indizierung eingeschaltet, und ist der globale Cronjob-Teil seit dem Veröffentlichen
      gelaufen?
   7. Ist der Index aus dem Takt (nach einem Import, einem Datenbankumzug oder einer Klassenänderung)? Bauen Sie ihn
      neu auf.
   8. Mit ezfind: Antwortet `exp:solr status`, und zeigt `SearchServerURI` auf den richtigen Core?
3. **Kleinste Lösung** (2 Minuten). Jeder Schritt hat seine eigene Lösung; ein vollständiger Neuaufbau ist nur
   Schritt 7. Vermerken Sie die Ursache im Ticket, damit Muster sichtbar werden.

### Häufige Fragen

**Warum nicht zuerst den Index neu aufbauen, nur für alle Fälle?** Das kostet Zeit, kann die Suche während eines
Laufs mit `--clean` unvollständig machen und verdeckt die eigentliche Ursache, die wiederkommt.

**Wo sehe ich Fehler der Suchmaschine?** Im Fehlerlog, **Setup > Logdateien**; eine Fehlerseite zeigt eine Referenz,
die [Einen Fehler anhand seiner Referenz finden](../../../../anleitungen/fehler-anhand-der-referenz-finden.md) erklärt.

**Der Redakteur sagt „gestern ging es noch“.** Fragen Sie, was sich geändert hat: eine Klassenbearbeitung, eine
Rollenänderung, ein Import, eine Verschiebung in eine andere Sektion.

### Stolperfallen

- **Als Administrator testen.** Die Schritte 2 und 3 sind für einen Administrator unsichtbar. Wiederholen Sie immer als
  der meldende Benutzer oder als Besucher.
- **Schritt 1 überspringen.** Viele Meldungen betreffen Entwürfe. Prüfen Sie zuerst.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation, auf der der Trainer vier Fälle vorbereitet hat, jeder ein Artikel mit
einem eindeutigen Wort im Titel. Der Trainer nennt Ihnen die vier Wörter und den Siteaccess, in dem Sie suchen; Sie
wissen nicht, welche Ursache zu welchem Wort gehört.

1. Schreiben Sie für das erste Wort die vier einrahmenden Antworten auf: wer sucht, wo, welche Wörter, welches Element.
2. Suchen Sie das Element mit **Setup > Inhaltsfinder** (über den Namen im Inhaltsbaum, dann seine Knoten-ID) und
   notieren Sie, ob es veröffentlicht, ein Entwurf oder im Papierkorb ist.
3. Suchen Sie als Besucher auf der öffentlichen Website nach dem Wort, dann in der Administration.
4. Arbeiten Sie die Checkliste ab, bis Sie die Ursache finden. Notieren Sie die Schrittnummer.
5. Wenden Sie die kleinste Lösung an und suchen Sie erneut.
6. Wiederholen Sie die Schritte 1 bis 5 für die anderen drei Wörter.
7. Vergleichen Sie Ihre vier Ursachen mit denen Ihres Nachbarn.

## Erwartetes Ergebnis

- Für jedes Wort nennen die Teilnehmenden einen Schritt der Checkliste als Ursache und zeigen das nach der Lösung
  gefundene Element.
- Typische vorbereitete Ursachen und ihre Lösungen: ein als Entwurf belassener Artikel (veröffentlichen); ein Artikel
  in einer Sektion, die die anonyme Rolle nicht lesen darf (in eine lesbare Sektion verschieben); das Wort nur in einem
  nicht durchsuchbar gemachten Feld (wieder durchsuchbar machen und neu aufbauen); verzögerte Indizierung
  eingeschaltet und der Cronjob nicht gelaufen (den globalen Teil ausführen).
- Der Trainer prüft mit jeder Person die vier Schrittnummern.

## Quiz

1. Nennen Sie die vier Fragen, die eine Meldung „nicht gefunden“ einrahmen.
2. Welche Prüfung kommt in der Checkliste zuerst, und warum?
3. Ein Administrator findet das Element, ein Besucher nicht. Welche Schritte der Checkliste sind wahrscheinlich?
   A. 1 und 7 B. 2 und 3 C. 4 und 5 D. nur 8
4. Wann ist ein vollständiger Neuaufbau des Index die richtige Lösung?
5. Welcher Befehl zeigt mit ezfind, ob der Suchserver antwortet?

<details>
<summary>Antworten</summary>

1. Wer gesucht hat, wo (welcher Siteaccess), welche Wörter, welches Element.
2. Ob das Element existiert und veröffentlicht ist: Entwürfe und Elemente im Papierkorb werden nie gefunden, und es ist
   die günstigste Prüfung.
3. **B.** Berechtigungen, Sichtbarkeit, der Baum oder die Sprachen des Siteaccess.
4. Wenn der Index nicht mehr zu den Inhalten passt: nach einem Import, einem Datenbankumzug oder einer Änderung der
   durchsuchbaren Attribute ([10.8](../../../../installation/10-nach-der-installation.md#108-suchindex-und-bildverarbeitung)).
5. `php bin/php/console exp:solr status` (oder `ping`).

</details>

## Weiterführende Informationen

- [Suchen](../../../suchen.md#tipps): was Redakteuren gesagt wird, wenn sie ein Element nicht finden.
- [Wenn etwas schiefgeht](../../../wenn-etwas-schiefgeht.md): erste Schritte bei jedem Problem.
- [Einen Fehler anhand seiner Referenz finden](../../../../anleitungen/fehler-anhand-der-referenz-finden.md): von einer
  Fehlerseite zum Logeintrag.

## Verwandte Seiten

- [Modul 5: Feinabstimmung und Fehlersuche](README.md)
- [Suche: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
