---
title: Grundlagen der Vorfallsbehandlung
audience: government, admin, project-manager, hosting
category: public-sector
order: 20
summary: Der gemeinsame Rahmen für jeden Vorfall auf einer Exponential-Website des öffentlichen Sektors - Rollen, Schweregrad, die ersten fünfzehn Minuten, Beweissicherung, Kommunikation mit der Öffentlichkeit und der Abschluss mit einer Nachbetrachtung - mit den Werkzeugen, die Exponential in jeder Phase bietet.
keywords: Vorfallsbehandlung, Vorfallmanagement, Schweregrad, Rufbereitschaft, Erstreaktion, Beweissicherung, Kommunikation, öffentliche Mitteilung, Wartungsmodus, Prüfprotokoll, Nachbetrachtung, Runbook, incident response, incident management, severity, on-call, evidence, maintenance mode, audit trail, post-incident review
type: help
---
# Grundlagen der Vorfallsbehandlung

Diese Seite richtet sich an die Personen, die Vorfälle auf einer öffentlichen Website behandeln: die Administration in
Rufbereitschaft, die Verantwortlichen für den Dienst und die Verantwortlichen für die Kommunikation. Sie beschreibt den
Rahmen, dem jedes Runbook in diesem Ordner folgt. Der eigene Vorfallprozess Ihrer Organisation (und, wo sie gilt, eine
nationale oder branchenbezogene Meldepflicht für Vorfälle) hat Vorrang; diese Seite zeigt, wie sich Exponential darin
einfügt.

## Inhalt
1. [Rollen](#rollen)
2. [Schweregrad](#schweregrad)
3. [Die ersten fünfzehn Minuten](#die-ersten-fünfzehn-minuten)
4. [Beweise sichern](#beweise-sichern)
5. [Kommunikation mit der Öffentlichkeit](#kommunikation-mit-der-öffentlichkeit)
6. [Abschluss](#abschluss)
7. [Verwandte Seiten](#verwandte-seiten)

## Rollen

| Rolle | Aufgabe |
|---|---|
| Vorfallsleitung | entscheidet, führt den Zeitablauf, zieht andere hinzu |
| Technische Bearbeitung | arbeitet mit den Runbooks am System |
| Kommunikation | informiert Öffentlichkeit, Beschäftigte und Leitung über das, was sie wissen müssen |
| Verantwortliche für den Dienst | tragen Risikoentscheidungen (Website vom Netz nehmen, Datensicherung wiederherstellen) |
| Informationssicherheitsbeauftragte | kommen hinzu, wenn ein Angriff oder eine Datenschutzverletzung möglich ist |

In einem kleinen Team hat eine Person mehrere Rollen; benennen Sie die Rollen zu Beginn trotzdem.

## Schweregrad

Eine Beispielskala (passen Sie sie an): **1** die öffentliche Website ist nicht erreichbar oder verunstaltet, oder
personenbezogene Daten könnten offengelegt sein; **2** eine wichtige Funktion ist defekt (Formulare, Suche,
Veröffentlichen); **3** eine Beeinträchtigung mit Umgehungslösung. Der Schweregrad entscheidet, wer gerufen wird und wie
schnell.

## Die ersten fünfzehn Minuten

1. **Bestätigen** Sie den Fehler von außen: `curl -sI https://www.example.gov/ | head -5` und die Anmeldeseite der
   Administration.
2. **Sehen Sie nach**: `./console exp:logs --tail=var/log/error.log --lines=50` sowie **Setup > Logdateien** und
   **Fehlerübersicht**; den Reiter **Audit** für die letzten Anmeldungen und Änderungen; `df -h` für die Festplatte.
3. **Entscheiden Sie über die Eindämmung**: Wenn Besucher die Website so, wie sie ist, nicht sehen dürfen
   (Verunstaltung, falsche Inhalte, offengelegte Daten), schalten Sie den Wartungsmodus mit einer neutralen Meldung ein:

   ```bash
   php bin/php/maintenance.php on --message="This service is temporarily unavailable." --allow-admin --allow-ip=<your address> --allow-root-user
   ```

4. **Beginnen Sie einen Zeitablauf**: Uhrzeit, wer, was gesehen wurde, was getan wurde. Das Prüfprotokoll von Exponential
   zeichnet Aktionen in der Administration und viele Befehle auf; Ihre Notizen ergänzen, was außerhalb davon geschah.
5. Öffnen Sie das passende Runbook in diesem Ordner.

## Beweise sichern

Bevor Sie bei einem Sicherheitsvorfall etwas ändern: Kopieren Sie die Dateien, einen Datenbank-Dump, `var/log/` (mit
`audit/`) sowie die Logs des Webservers und von PHP an einen Ort, an dem der Benutzer der Website nicht schreiben kann;
führen Sie `./console exp:audit verify --allow-root-user` aus und bewahren Sie die Ausgabe auf
([Kompromittierte Website (Hosting-Runbook)](../../../hosting/runbooks/kompromittierte-website.md#beweise-sichern)).

## Kommunikation mit der Öffentlichkeit

- Die Meldung der Wartungsseite ist Ihre erste öffentliche Aussage: schlicht, ohne technische Einzelheiten, mit dem
  Hinweis, wo man in der Zwischenzeit Hilfe bekommt (Telefon, Schalter).
- Nutzen Sie die Kanäle, die Ihre Organisation für Hinweise zu ihren Diensten verwendet; bleiben Sie sachlich und
  aktualisieren Sie die Meldungen, wenn sich die Lage ändert.
- Wenn personenbezogene Daten betroffen sein könnten, stimmen Sie jede Aussage mit der oder dem
  Datenschutzbeauftragten und der Rechtsberatung ab
  ([Meldung von Datenschutzverletzungen](meldung-von-datenschutzverletzungen.md)).

## Abschluss

Bringen Sie die Website zurück ([Die Website wieder online bringen](../../../../../eng-us/help/government/tasks/operations/bring-the-site-back-online.md)),
bestätigen Sie das Ergebnis von außen, leeren Sie die Caches vor der Website, informieren Sie die Öffentlichkeit und
halten Sie innerhalb weniger Tage eine Nachbetrachtung ab
([Nachbetrachtung eines Vorfalls](nachbetrachtung-eines-vorfalls.md)).

## Verwandte Seiten
- [Website nicht erreichbar](website-nicht-erreichbar.md)
- [Verdacht auf ein kompromittiertes Konto](verdacht-auf-kompromittiertes-konto.md)
- [Überwachung im Überblick](../ueberwachung/ueberwachung-im-ueberblick.md)
- [Hosting-Runbooks](../../../hosting/runbooks/README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
