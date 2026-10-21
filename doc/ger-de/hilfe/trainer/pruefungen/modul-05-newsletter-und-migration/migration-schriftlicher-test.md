---
title: "Lektion 5.3: Migration zu Exponential, schriftlicher Test"
audience: trainer, admin, developer
category: training
order: 53
summary: Ein schriftlicher Test mit vierzehn Fragen für das Ende des Kurses Migration zu Exponential, zur Wahl des Migrationswegs, zur Migration von älteren Releases des Kernels und von den Symfony-basierten Plattformen, zur Abbildung eines Inhaltsmodells, zu Weiterleitungen, Generalprobe, Umstellung, Rückfallplan und dem Verlassen von Exponential, mit einem Lösungsschlüssel, der die Migrationskapitel zitiert. 30 Minuten für die Teilnehmenden.
keywords: prüfungen und praktische übungen, migration, schriftlicher test, ältere releases, abbildung des inhaltsmodells, remote-id, weiterleitungen, url-alias, url-wildcard, generalprobe, umstellung, rückfallplan, exponential verlassen, assessments and practical exercises, written test, older releases, 4.x, 5.x legacy, eZ Platform, Ibexa, content model mapping, remote id, redirects, URL alias, URL wildcard, rehearsal, cut-over, rollback, leaving Exponential
type: help
---
# Lektion 5.3: Migration zu Exponential, schriftlicher Test

Dieser Test schließt den Migrationskurs ab. Er prüft Methode, nicht auswendig gelernte Befehle: den richtigen
Leitfaden wählen, die Adressen der alten Website erhalten, einen Import wiederholbar machen, proben und wissen, wann
man auf den alten Stand zurückgeht.

> **Hinweis**
> Dies ist die eigene Prüfung des Trainers, keine offizielle Zertifizierung. Der frühere Name des Produkts erscheint
> in den Migrationskapiteln als Geschichte; verwenden Sie ihn in Fragen nur dort, wo Teilnehmende ihn brauchen, um ihr
> altes System wiederzuerkennen.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Prüfungen und praktische Übungen](../README.md) |
| Modul | [Modul 5: Die Kurse Newsletter und Migration prüfen](README.md) |
| Dauer | 45 Minuten (5 Einweisung, 30 Test, 10 Besprechung der Antworten) |
| Niveau | Experten |
| Zielgruppe | Teilnehmende am Ende des Kurses Migration zu Exponential |
| Voraussetzungen | Der Migrationskurs; [Lektion 1.2](../modul-01-pruefungen-gestalten/schriftliche-tests-schreiben.md) für den Trainer |

## Lernziele

Wer diesen Test besteht, hat gezeigt, dass er Folgendes kann:

- den richtigen Migrationsleitfaden für ein Quellsystem wählen;
- erklären, was bei einem älteren Release des Kernels gleich bleibt und was sich ändert;
- ein Quell-Inhaltsmodell auf Klassen, Attribute und Orte abbilden;
- Weiterleitungen, Generalproben, Umstellung und Rückfallplan planen;
- die Werkzeuge für den Export von Inhalten beim Verlassen nennen.

## Hinweise für Trainer

### So führen Sie ihn durch

1. Teilen Sie die Fragen ohne den Schlüssel aus; 30 Minuten.
2. Gehen Sie die Antworten mit dem [Migrationsüberblick](../../../../installation/migration/README.md) und der
   [Checkliste](../../../../installation/migration/checkliste.md) auf dem Projektor durch.

### Häufige Fragen

**Unser Kurs hat nur WordPress behandelt.** Ersetzen Sie die Fragen 3 bis 6 durch Fragen aus dem
[WordPress-Leitfaden](../../../../installation/migration/wordpress.md), mit derselben Regel: Jede Antwort zitiert einen
Abschnitt.

### Stolperfallen

- **„Convert XmlText to RichText“.** Teilnehmende, die die alten Leitfäden des Herstellers gelesen haben, erwarten
  das. Frage 4 behandelt es.

## Der Test

1. Eine Website läuft auf einem älteren Release dieses Kernels (4.x). Welches Kapitel ist der Leitfaden?
2. Sollte die alte Installation an Ort und Stelle migriert werden? Was wird stattdessen gemacht?
3. Warum laufen die Datenbank-Update-Dateien mit dem heutigen Code in zwei Phasen (zuerst das Schema, danach die
   Reparaturskripte)?
4. Wird Rich Text (`ezxmltext`) beim Umzug von 4.x oder 5.x legacy auf Exponential 6 umgewandelt?
5. Ein Team auf Ibexa möchte das geringste Risiko für eine Live-Website. Auf welchen Weg verweist Kapitel 16 zuerst?
6. Nennen Sie eine „Faustregel, die für jeden Weg gilt“ aus dem Migrationsüberblick.
7. Was wird bei der Abbildung eines Inhaltsmodells aus einer WordPress-Beitragskategorie in Exponential
   üblicherweise?
8. Warum erhält jedes importierte Objekt eine Remote-ID, die seine Quelle benennt?
9. Nennen Sie die drei Wege, in Exponential eine alte Adresse funktionsfähig zu halten.
10. Alte Adressen mit Query-Strings (`?p=123`) können keine URL-Aliase sein. Wo werden sie behandelt?
11. Wie oft sollte der Import vor der Umstellung geprobt werden, und worauf?
12. Was sollte vor der Umstellung über den Rückfallplan entschieden sein?
13. Benutzerpasswörter aus einem anderen System: Was wird üblicherweise geplant, und warum?
14. Ein Kunde verlässt Exponential. Welches Werkzeug exportiert alle Inhalte einer Website, und was exportiert es
    niemals?

<details>
<summary>Antworten</summary>

1. Kapitel 14, [Migration von 4.x](../../../../installation/14-migration-von-4x.md).
2. Nein. Bauen Sie die neue Website neben der alten auf, proben Sie auf einer Kopie der Produktionsdatenbank und
   wiederholen Sie es dann einmal mit einer frischen Kopie, während die alte Website im Wartungsmodus ist
   ([14.2](../../../../installation/14-migration-von-4x.md#142-die-migration-auf-einer-staging-kopie-planen)).
3. Die Reparaturskripte laufen auf dem Kernel 6.0, dessen Definitionen persistenter Objekte das aktuelle Schema
   beschreiben ([14.5.4](../../../../installation/14-migration-von-4x.md#1454-zwei-phasen-zuerst-das-schema-danach-die-datenreparatur)).
4. Nein. `ezxmltext` bleibt `ezxmltext`; es gibt keine Umwandlung von XmlText in RichText
   ([14.1](../../../../installation/14-migration-von-4x.md#141-was-sich-ändert-und-was-nicht),
   [15.1](../../../../installation/15-migration-von-5x-legacy.md#151-was-sich-ändert-und-was-die-anleitung-des-herstellers-sagt)).
5. Weg A, zu Exponential Platform Nexus derselben Generation, ein Austausch von Paketen; die Generation wird danach
   gewechselt ([16.2](../../../../installation/16-migration-von-ez-platform-und-ibexa.md#162-einen-weg-wählen)).
6. Eine davon: zuerst die Inhalte, dann die Darstellung; eine Änderung nach der anderen; jede alte Adresse
   funktionsfähig halten; proben
   ([Migrationsüberblick](../../../../installation/migration/README.md#2-welcher-weg-passt)).
7. Ein Ort im Inhaltsbaum (oder Schlagwörter oder Tags, je nach Entscheidung)
   ([Checkliste](../../../../installation/migration/checkliste.md#3-das-inhaltsmodell-abbilden),
   [Abbildung des Inhaltsmodells](../../../../installation/migration/inhaltsmodell-zuordnen.md#1-die-bausteine-im-überblick)).
8. Damit ein erneuter Import aktualisiert statt zu duplizieren und sich daraus Weiterleitungen erzeugen lassen
   ([Checkliste](../../../../installation/migration/checkliste.md#3-das-inhaltsmodell-abbilden)).
9. Der eigene URL-Alias des Knotens, ein benutzerdefinierter URL-Alias, der weiterleitet, oder eine URL-Wildcard
   ([Checkliste](../../../../installation/migration/checkliste.md#4-urls-und-weiterleitungen)).
10. Im Webserver oder in einem kleinen Weiterleitungsmodul vor Exponential, aus derselben Weiterleitungstabelle
    ([Checkliste](../../../../installation/migration/checkliste.md#4-urls-und-weiterleitungen)).
11. Mindestens zweimal, auf einer Kopie des Ziels, mit Zeitmessung
    ([Checkliste](../../../../installation/migration/checkliste.md#6-den-import-bauen-und-proben)).
12. Was einen Rückfall auslöst und wer entscheidet; die alte Website läuft schreibgeschützt weiter, und vor dem
    letzten Import wird eine Sicherung erstellt
    ([Checkliste](../../../../installation/migration/checkliste.md#9-rückweg)).
13. Ein Zurücksetzen per Mail mit einem Link, weil die Hash-Typen der Quelle für das Ziel selten lesbar sind
    ([Checkliste](../../../../installation/migration/checkliste.md#3-das-inhaltsmodell-abbilden)).
14. Das Website-Archiv von xrowextract (`./console ext:xrowextract:archive --set=everything ...`);
    Passwort-Hashes werden niemals exportiert
    ([Von Exponential weg migrieren](../../../../installation/migration/exponential-verlassen.md#1-was-sie-exportieren-sollten)).

</details>

## Bewertung

- Ein Punkt pro Frage; Frage 9 braucht für den Punkt alle drei Wege.
- Bestanden: 10 von 14 Punkten oder mehr, mit den Fragen 2 und 9 richtig: An Ort und Stelle migrieren und Adressen
  verlieren sind die beiden teuersten Fehler.

## Weiterführende Literatur

- [Der Migrationsüberblick](../../../../installation/migration/README.md) und die
  [Checkliste](../../../../installation/migration/checkliste.md)
- [Inhalte auf Exponential abbilden](../../../../installation/migration/inhaltsmodell-zuordnen.md)
- [Kapitel 17: Migrationsreferenz](../../../../installation/17-migrationsreferenz.md)

## Verwandte Seiten

- [Lektion 5.4: Migration zu Exponential, praktische Aufgabe](migration-praktische-aufgabe.md)
- [Modul 5: Die Kurse Newsletter und Migration prüfen](README.md)
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
