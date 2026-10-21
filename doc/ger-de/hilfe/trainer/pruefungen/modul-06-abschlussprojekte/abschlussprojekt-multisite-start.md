---
title: "Lektion 6.6: Abschlussprojekt, ein Multisite-Start mit einem Newsletter"
audience: trainer, admin, developer, editor
category: training
order: 66
summary: Ein anderthalbtägiges Abschlussprojekt für Administratoren, einer Schulungsinstallation eine zweite Website für eine erfundene Organisation mit eigenem Siteaccess und Hostnamen hinzufügen, die Suche für beide aktuell halten, eine Newsletter-Liste mit Double-Opt-in und einer ersten Ausgabe starten, die nur in Dateien versendet wird, und die Checkliste für den Start schreiben, bewertet mit einem Bewertungsraster aus sieben Kriterien. Anderthalb Tage plus Präsentationen.
keywords: prüfungen und praktische übungen, abschlussprojekt, multisite, siteaccess, host-matching, suche, newsletter, datei-transport, checkliste für den start, bewertungsraster, assessments and practical exercises, capstone, host matching, SiteURL, search, cjw_newsletter, file transport, example.invalid, launch checklist, rubric
type: help
---
# Lektion 6.6: Abschlussprojekt, ein Multisite-Start mit einem Newsletter

Eine erfundene Organisation fügt ihrer Installation eine zweite Website hinzu und startet sie mit einem Newsletter.
Die Teilnehmenden richten den Siteaccess ein, halten die Suche für beide Websites funktionsfähig und durchlaufen den
ersten Newsletter-Zyklus, vollständig in Dateien.

> **Hinweis**
> Dies ist die eigene Prüfung des Trainers, keine offizielle Zertifizierung. **Es werden keine Mails versendet.** Jeder
> Mail-Transport der Schulungsinstallation, der `Transport` der Website und alle drei Newsletter-Einstellungen
> `TransportMethod*`, steht auf `file`, und jeder Abonnent hat eine Adresse unter `example.invalid`. Test-Hostnamen
> enden auf `.test`.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Prüfungen und praktische Übungen](../README.md) |
| Modul | [Modul 6: Abschlussprojekte](README.md) |
| Dauer | Anderthalb Tage: 15 Minuten Einweisung, etwa 7 Stunden Arbeit mit zwei Zwischenständen, 15 Minuten Präsentation pro Teilnehmer, 45 Minuten Bewertung |
| Niveau | Experten |
| Zielgruppe | Teilnehmende am Ende von Multisite und Seitenpakete sowie Newsletter (Suche hilft) |
| Voraussetzungen | Diese Kurse; eine Schulungsinstallation mit der Newsletter-Erweiterung, eine Shell, über einen lokalen Port ausgeliefert |

## Lernziele

Wer dieses Abschlussprojekt besteht, hat gezeigt, dass er Folgendes kann:

- einen öffentlichen Siteaccess für eine zweite Website mit eigenem Inhaltsstamm und Hostnamen hinzufügen;
- nachweisen, welcher Siteaccess auf welchen Host antwortet;
- die Suchergebnisse für beide Websites korrekt halten;
- einen Newsletter-Start mit Double-Opt-in und einer ersten Ausgabe durchführen, ohne Mails zu versenden;
- eine Checkliste für den Start schreiben, die DNS, Zertifikate, Mail-Absender und Einwilligung abdeckt.

## Hinweise für Trainer

### So führen Sie es durch

1. **Vorher**: eine Schulungsinstallation pro Teilnehmer mit der Newsletter-Erweiterung und ihrem Baum, alle
   Transporte auf `file` (von Ihnen gesetzt), keine Crontab. Entscheiden Sie, ob die Teilnehmenden den zweiten
   Siteaccess von Hand anlegen oder aus einem Multisite-Seitenpaket, das Ihr Kurs verwendet hat
   ([Seitenpakete](../../../hosting/bereitstellung/seitenpakete-fuer-hoster.md)).
2. **Zwischenstände**: Mittag von Tag 1 (der zweite Siteaccess antwortet auf seinen Test-Host), Ende von Tag 1 (Suche
   und die Newsletter-Liste mit bestätigten Testabonnenten).
3. **Präsentation**: Die Teilnehmenden zeigen beide Hosts mit `curl`, eine Suche auf jeder Website, die
   Newsletter-Dateien und die Checkliste für den Start.

### Stolperfallen

- **Ein Transport, der auf `sendmail` geblieben ist.** Prüfen Sie vor Tag 1 selbst alle Transporte; die Prüfung der
  Teilnehmenden ist die zweite Absicherung, nicht die erste.
- **Die Suche vermischt beide Websites.** Die Teilnehmenden brauchen eventuell das Kursmaterial dazu, wie man die
  Suche auf einen Teilbaum beschränkt; akzeptieren Sie jeden dokumentierten Weg.

## Praktische Übung

Die Aufgabenstellung, wie sie an die Teilnehmenden ausgeteilt wird.

*Das Lakeside Arts Festival (ein erfundenes Festival) betreibt seine Haupt-Website auf Ihrer Installation und startet
jetzt eine zweite Website für sein Jugendprogramm, mit einem Newsletter für Eltern.*

1. Prüfen Sie jeden Mail-Transport (Website und Newsletter) und notieren Sie die Werte. Machen Sie nur weiter, wenn
   alle `file` sind.
2. Legen Sie einen Inhaltsstamm für die Jugend-Website mit fünf Elementen an.
3. Fügen Sie dafür einen öffentlichen Siteaccess mit eigener `SiteURL` hinzu, mit Host-Matching für
   `www.festival.test` (Haupt-Website) und `youth.festival.test` (Jugend-Website), und behalten Sie das URL-Matching
   als Rückfallebene bei.
4. Weisen Sie mit `curl` und dem `Host`-Header nach, welcher Siteaccess auf jeden Namen antwortet und auf einen nicht
   aufgeführten.
5. Sorgen Sie dafür, dass die Suche auf der Jugend-Website die Jugendinhalte findet, und prüfen Sie, dass ein neues
   Element auf beiden Websites so gefunden wird, wie es sein soll.
6. Legen Sie eine Newsletter-Liste für Eltern an; melden Sie drei Testabonnenten unter `example.invalid` an und
   bestätigen Sie sie über die Mail-Dateien.
7. Schreiben Sie die erste Ausgabe und versenden Sie sie über die beiden von Hand ausgeführten Cron-Schritte; lesen
   Sie die Ausgabedateien.
8. Melden Sie einen Abonnenten ab und notieren Sie den Zustand.
9. Schreiben Sie die Checkliste für den echten Start: DNS, Hostnamen im Webserver oder in Velocity, Zertifikat, den
   echten Transport des Newsletters und die Absender-Domain mit SPF, Einwilligungsnachweise, Bounce-Verarbeitung,
   Cron-Zeilen, Überwachung.
10. Stellen Sie am Ende die ursprünglichen Matching-Einstellungen der Installation wieder her oder lassen Sie sie so,
    wie Ihr Trainer es anweist.

## Erwartetes Ergebnis

- Notizen mit allen Transporten auf `file`; Ausgaben von `curl`, die den richtigen Siteaccess pro Host zeigen.
- Korrekte Suchergebnisse auf beiden Websites.
- Die Elternliste mit zwei bestätigten und einem entfernten Abonnenten; Ausgabedateien im Mail-Verzeichnis.
- Eine Checkliste für den Start, mit der eine Administratorin oder ein Administrator sicher live gehen könnte.

## Bewertungsraster

| Kriterium | 0: Nicht gezeigt | 1: Teilweise | 2: Größtenteils | 3: Vollständig | Nachweis |
|---|---|---|---|---|---|
| Mail-Sicherheit (Pflicht) | Ein Transport nicht `file` oder eine echte Adresse | Geprüft, nicht alle | Alle `file`, nicht notiert | Alle geprüft und notiert, nur `example.invalid` | Notizen |
| Zweiter Siteaccess | Keiner | Angelegt, antwortet nicht | Antwortet, `SiteURL` falsch | Antwortet mit eigenem Stamm und eigener `SiteURL` | `curl`, Einstellungen |
| Nachweis des Host-Matchings | Keiner | Ein Host | Beide, Rückfallebene nicht erklärt | Beide und die Rückfallebene erklärt | Ausgaben von `curl` |
| Suche | Nicht geprüft | Vermischte Ergebnisse | Auf einer Website korrekt | Auf beiden korrekt, neues Element geprüft | Suchen |
| Newsletter-Zyklus | Nichts | Nur Liste | Angemeldet und bestätigt, Ausgabe nicht versendet | Bestätigt, per Cron in Dateien versendet, Abmeldung erledigt | Mail-Verzeichnis, Liste |
| Checkliste für den Start | Keine | Einige Punkte | Die meisten Punkte, Mail oder Einwilligung fehlt | DNS, Hostnamen, Zertifikat, Mail-Absender und SPF, Einwilligung, Bounces, Cron, Überwachung | Checkliste |
| Aufräumen | Einstellungen kaputt hinterlassen | Teilweise wiederhergestellt | Wiederhergestellt, nicht geprüft | Wiederhergestellt oder wie angewiesen belassen, geprüft | `exp:ini list` |

**Bestanden**: 14 von 21 Punkten oder mehr und **Mail-Sicherheit** mit 3.

## Quiz

Fragen für die Nachbesprechung nach den Präsentationen.

1. Welche drei Dinge müssen neben den Einstellungen einen neuen Hostnamen kennen?
2. Welcher Newsletter-Transport ist im Auslieferungszustand `sendmail`?
3. Warum nennt die Checkliste für den Start SPF für jede Adressfamilie, über die der Server sendet?
4. Was schützt das Double-Opt-in eines Elternteils?

<details>
<summary>Antworten</summary>

1. Der Webserver oder Velocity, DNS und das Zertifikat
   ([Multisite auf einer Installation](../../../hosting/bereitstellung/mehrere-websites-in-einer-installation.md#zuordnung-nach-url-host-oder-port)).
2. `TransportMethodPreview`, für Testversände ([10.5](../../../../installation/10-nach-der-installation.md#newsletter-cjw_newsletter)).
3. Große Anbieter weisen Mails aus einer Adressfamilie ab, die der SPF-Eintrag nicht aufführt
   ([10.4](../../../../installation/10-nach-der-installation.md#104-mail-der-datei-transport-und-echter-versand)).
4. Dass nur Menschen den Newsletter erhalten, die ihn wirklich angefordert haben
   ([cjw_newsletter](../../../../../eng-us/features/6.0/extensions/cjw_newsletter.md)).

</details>

## Weiterführende Literatur

- [Multisite, praktische Aufgabe](../modul-03-administrationskurse/multisite-praktische-aufgabe.md) und
  [Newsletter, praktische Aufgabe](../modul-05-newsletter-und-migration/newsletter-praktische-aufgabe.md)
- [Mail-Einwilligung und Bounces](../../../hosting/betrieb/einwilligung-und-bounces-bei-mails.md)
- [Checkliste für den Livegang einer neuen Kunden-Website](../../../hosting/checklisten/checkliste-fuer-den-start-einer-kunden-website.md)

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
