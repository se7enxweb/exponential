---
title: Verwaiste Konten überprüfen
audience: government, admin
category: public-sector
order: 60
summary: Wie Sie externe Konten, die niemand mehr nutzt, mit Benutzer > Benutzerkonten prüfen finden und deaktivieren (Nie angemeldet, Seit N Tagen nicht gesehen, auf die externen Gruppen gefiltert), die Liste für die Projektleiter exportieren und danach handeln.
keywords: verwaiste Konten, Kontenhygiene, Benutzerkonten prüfen, Seit N Tagen nicht gesehen, Nie angemeldet, Konten deaktivieren, CSV-Export, Überprüfung des Zugangs, Auftragnehmer, stale accounts, user hygiene, Check user accounts, user/hygiene, Not seen for N days, Never signed in, disable accounts, CSV export, access review, contractors
type: help
---
# Verwaiste Konten überprüfen

Diese Seite richtet sich an Administratoren, die externe Zugänge überprüfen, monatlich oder am Ende jedes Projekts.
**Benutzer > Benutzerkonten prüfen** (`/user/hygiene`) listet Konten mit Filtern und bearbeitet viele auf einmal
([Benutzerkonten prüfen](../../../../funktionen/6.0/benutzerpflege.md)).

## Inhalt

1. [Verwaiste Konten finden](#verwaiste-konten-finden)
2. [Für die Überprüfung exportieren](#für-die-überprüfung-exportieren)
3. [Deaktivieren](#deaktivieren)
4. [Eine monatliche Routine](#eine-monatliche-routine)

## Verwaiste Konten finden

1. Öffnen Sie **Benutzer > Benutzerkonten prüfen** und beschränken Sie die Liste auf die Gruppe der externen Benutzer.
2. Verwenden Sie die Filter:
   - **Nie angemeldet**: angelegte und nie genutzte Konten;
   - **Seit N Tagen nicht gesehen** (voreingestellt 180; wählen Sie zum Beispiel 60 für Auftragnehmer);
   - **Deaktiviert**, **Gesperrt**: bereits gestoppte oder durch fehlgeschlagene Anmeldungen gesperrte Konten.
3. Jede Zeile zeigt Name, Benutzername, letzten Besuch mit den Tagen seither, die Zahl der Anmeldungen, die Gruppen und
   die Rollen.

## Für die Überprüfung exportieren

**Liste als CSV exportieren** gibt die ganze gefilterte Liste als UTF-8-Datei aus. Senden Sie jedem Projektleiter die
Konten seiner Parteien und fragen Sie, welche bleiben müssen. Die Datei enthält personenbezogene Daten; löschen Sie sie
nach der Überprüfung.

## Deaktivieren

Markieren Sie die Konten und drücken Sie **Deaktivieren**; eine Bestätigung zeigt, wie viele Konten sich ändern und
wie viele ausgelassen werden, und warum. Ein deaktiviertes Konto kann sich nicht anmelden, und seine Sitzungen enden;
Inhalte, Gruppen und Rollen bleiben. **Aktivieren** macht es rückgängig. Nichts auf dieser Seite entfernt ein Konto.

## Eine monatliche Routine

- [ ] Externe Gruppen filtern, **Seit 60 Tagen nicht gesehen**: deaktivieren, sofern der Projektleiter nicht bestätigt.
- [ ] Filter **Nie angemeldet**, älter als zwei Wochen: nachfragen, dann deaktivieren.
- [ ] Mit den Enddaten im Zugangsregister vergleichen (Exponential hat kein eigenes Ablaufdatum).
- [ ] Die Überprüfung festhalten.

## Verwandte Seiten

- [Den Zugang beenden](den-zugang-beenden.md)
- [Benutzerkonten prüfen](../../../../funktionen/6.0/benutzerpflege.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
