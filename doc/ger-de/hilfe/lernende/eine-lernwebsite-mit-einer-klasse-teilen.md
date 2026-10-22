---
title: Eine Lernwebsite mit einer Klasse teilen
audience: student
category: learning
order: 16
summary: Für Lerngruppen und Klassen, die sich eine Exponential-Installation teilen - warum eine Installation pro Lernendem sicherer ist, wie man gut teilt, wenn es sein muss (Konten pro Lernendem, eingeschränkte Rollen, Demomodus, wenn alle Administrator sind), Regeln für die Gruppe und wie man die gemeinsame Website zurücksetzt.
keywords: lerngruppe, klasse, gemeinsame website, gemeinsame installation, konten, rollen, demomodus, EXP_DEMO_MODE, übungsumgebung, klassenzimmer, zurücksetzen, lernende, study group, class, shared site, shared installation, accounts, roles, demo mode, lab, classroom, reset, learners
type: help
---
# Eine Lernwebsite mit einer Klasse teilen

Diese Seite ist für Lerngruppen, Studierendenvereine und Klassen, die auf einer gemeinsamen Installation arbeiten,
zum Beispiel auf einem Server der Schule. Sie erklärt, wann das Teilen funktioniert, wie Sie es einrichten, damit
niemand die anderen aussperrt, und wie Sie die Website zurücksetzen. Trainer finden eine ausführlichere Behandlung in
[Train the Trainer, Modul 2](../trainer/train-the-trainer/modul-02-die-uebungsumgebung-aufbauen/README.md).

## Inhalt

1. [Eine pro Lernendem oder eine für alle](#eine-pro-lernendem-oder-eine-für-alle)
2. [Konten: eines pro Lernendem](#konten-eines-pro-lernendem)
3. [Wenn alle Administrator sind: der Demomodus](#wenn-alle-administrator-sind-der-demomodus)
4. [Regeln für die Gruppe](#regeln-für-die-gruppe)
5. [Die gemeinsame Website zurücksetzen](#die-gemeinsame-website-zurücksetzen)

## Eine pro Lernendem oder eine für alle

Die Lektion der Trainer [Eine Installation pro Teilnehmer](../trainer/train-the-trainer/modul-02-die-uebungsumgebung-aufbauen/eine-installation-pro-teilnehmer.md)
sagt es deutlich: „Die sicherste Übungsumgebung gibt allen Lernenden eine eigene Installation: Niemand kann die Übung
des Nachbarn kaputt machen, und alle Lernenden können Administrator sein.“ Da `exp:install` mit einem Befehl eine
vollständige Website auf SQLite installiert, ist das günstig.

| | Eine Installation pro Lernendem | Eine gemeinsame Installation |
|---|---|---|
| Wer was kaputt machen kann | Nur die eigene Website | Die Arbeit aller |
| Administrator für alle | Ja | Nur mit Vorsicht (siehe unten) |
| Übungen auf der Kommandozeile | Ja | Meist nicht |
| Gut geeignet für | Alle Lernpfade | Übungen für Redakteure, Gruppenprojekte, ein gemeinsames Abschlussprojekt |

Teilen Sie nur, wenn es sein muss, und geben Sie allen Lernenden für den Administrator- und den Entwicklerpfad eine
eigene Website. Die Trainer vergleichen beides in [Gemeinsamer Server oder einer pro Teilnehmer](../trainer/train-the-trainer/modul-02-die-uebungsumgebung-aufbauen/gemeinsamer-server-oder-einer-pro-teilnehmer.md).

## Konten: eines pro Lernendem

Auf einer gemeinsamen Website bekommen alle Lernenden ein eigenes Konto, niemals das gemeinsame Konto `admin`:

1. Die Person, die die Website betreibt, legt eine Benutzergruppe an, zum Beispiel „Klasse 2026“.
2. Alle Lernenden bekommen einen Benutzer in dieser Gruppe, mit einer Adresse unter `example.invalid`, wenn keine
   echten E-Mails nötig sind.
3. Die Gruppe bekommt eine Rolle, die zu den Übungen passt, zum Beispiel Bearbeiten nur in einem Teilbaum
   ([Wie gebe ich einer Gruppe Bearbeitungsrechte für eine Sektion?](../handbuch/wie-gebe-ich-einer-gruppe-bearbeitungsrechte-fuer-eine-sektion.md),
   [Rollen, Richtlinien und nicht aktivierte Benutzer](../../anleitungen/rollen-und-richtlinien.md)).
4. Alle Lernenden arbeiten in einem eigenen Ordner, damit die Arbeiten getrennt bleiben.

Die Lektion der Trainer [Konten und Rollen der Teilnehmer](../trainer/train-the-trainer/modul-02-die-uebungsumgebung-aufbauen/konten-und-rollen-der-teilnehmenden.md)
zeigt dieselbe Einrichtung Schritt für Schritt. Die Einrichtung selbst ist eine gute Übung: siehe
[Projekt 9](projekt-09-rollen-fuer-einen-eingeschraenkten-redakteur.md).

## Wenn alle Administrator sind: der Demomodus

Wenn sich alle Teilnehmenden als Administrator anmelden müssen, könnte jede einzelne Person das Administratorpasswort
ändern und den Rest aussperren. Der **Demomodus** verhindert das: Ist die Umgebungsvariable `EXP_DEMO_MODE=1` für
jeden PHP-Prozess der Installation gesetzt, können Administratorpasswörter weder geändert noch zurückgesetzt werden
([Demomodus-FAQ](../demomodus-faq.md)). Die Trainer verwenden ihn für diesen Fall
([Trainer: hier beginnen](../trainer/trainer-hier-beginnen.md#diese-seiten-wiederfinden)).

Was Sie wissen müssen, bevor Sie ihn verwenden:

- Er wird vom Server eingeschaltet, nicht aus der Administrationsoberfläche; nur genau der Wert `1` zählt
  ([Demomodus](../demomodus.md#ein--und-ausschalten)).
- Alles andere funktioniert weiterhin: Ein Administrator kann weiterhin Inhalte, Rollen und Einstellungen ändern,
  daher muss die Website regelmäßig neu aufgebaut werden ([Demomodus-FAQ](../demomodus-faq.md#was-sperrt-er-nicht)).
- Er bedeutet, dass das Administratorpasswort bekannt sein soll. Verwenden Sie ihn niemals auf einer Website mit
  echten Inhalten, echten Benutzern oder echten Daten ([Demomodus-FAQ](../demomodus-faq.md#kann-ich-ihn-auf-einer-echten-site-verwenden)).
- Niemand darf auf einer solchen Website etwas Privates eingeben. Das Banner sagt es: „Geben Sie hier nichts Privates
  ein.“

## Regeln für die Gruppe

Vereinbaren Sie diese Regeln vor der ersten Sitzung und schreiben Sie sie oben in die Notizen der Gruppe:

1. Alle verwenden ihr eigenes Konto. Niemand teilt ein Passwort in einem Chat.
2. Nur erfundene Inhalte und Testadressen unter `example.invalid`.
3. Niemand entfernt oder ändert die Arbeit einer anderen Person, ohne zu fragen.
4. Einstellungen ändert eine benannte Person, und jede Änderung wird in den Notizen der Gruppe festgehalten.
5. Vor einer riskanten Übung erstellt die Person, die die Website betreibt, eine Sicherung
   ([Die Lernwebsite zurücksetzen und neu aufbauen](die-lernwebsite-zuruecksetzen-und-neu-aufbauen.md#vor-einer-riskanten-übung-sichern)).
6. Probleme mit der Software werden einmal gemeldet, von einer Person, mit den Schritten
   ([Fehler, die Sie beim Lernen finden](fehler-die-sie-beim-lernen-finden.md)).

## Die gemeinsame Website zurücksetzen

- Zwischen Kursen oder Semestern: mit `exp:install --force` neu installieren
  ([Die Lernwebsite zurücksetzen und neu aufbauen](die-lernwebsite-zuruecksetzen-und-neu-aufbauen.md#mit-einer-frischen-installation-neu-anfangen)).
- Vor einer Prüfung oder einer Präsentation: einen gesicherten Zustand wiederherstellen.
- Die Lektion der Trainer [Die Übungsumgebung zurücksetzen](../trainer/train-the-trainer/modul-02-die-uebungsumgebung-aufbauen/die-uebungsumgebung-zuruecksetzen.md)
  enthält einen Ablauf für Gruppen.

## Verwandte Seiten

- [Demomodus-FAQ](../demomodus-faq.md)
- [Um Hilfe bitten und Lerngruppen](um-hilfe-bitten-und-lerngruppen.md)
- [Für Lehrkräfte: diese Seiten aufgeben](fuer-lehrkraefte-diese-seiten-aufgeben.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und
  verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
