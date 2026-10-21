---
title: "Lektion 2.3: Worker und Pools"
audience: trainer, hosting, admin
category: training
order: 23
summary: Die Teilnehmenden bemessen einen Velocity-Worker-Pool, wechseln zwischen einem festen und einem dynamischen Pool, beurteilen Speicher nach PSS statt RSS und erklären, wann ein Worker ersetzt wird. 45 Minuten.
keywords: hosting und betrieb, Velocity, worker, pool, SpareWorkers, IdleWorkerTimeout, Instances, zygote, PSS, RSS, persistente worker, KeepGlobals, maxRequests, requestTimeout, hosting and operations, workers, persistent workers
type: help
---
# Lektion 2.3: Worker und Pools

Der Worker-Pool entscheidet, wie viele Seiten eine Velocity-Website gleichzeitig darstellen kann und wie viel Speicher
sie belegt. Hoster, die eine Zahl von anderswo übernehmen oder Speicher nach RSS beurteilen, überlasten einen Rechner
oder verschwenden ihn. Diese Lektion vermittelt die Einstellungen, die gemessenen Kosten und die Methode, die die
Dokumentation zum Finden der richtigen Zahl angibt.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Hosting und Betrieb](../README.md) |
| Modul | [Modul 2: Velocity](README.md) |
| Dauer | 45 Minuten (20 Präsentation, 25 Übung) |
| Niveau | Fortgeschrittene bis Experten |
| Zielgruppe | Systemadministratoren und Support-Mitarbeitende im Hosting |
| Voraussetzungen | [Lektion 2.2](velocity-am-laufen-halten.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- `Workers`, `SpareWorkers` und `IdleWorkerTimeout` für einen festen oder einen dynamischen Pool setzen;
- erklären, warum Speicher nach PSS oder privatem Speicher beurteilt wird, nie nach RSS;
- vier Gründe nennen, aus denen ein Worker durch einen frischen Fork ersetzt wird;
- erklären, was persistente Worker zwischen Anfragen behalten und warum `KeepGlobals` nicht für Anfragedaten ist;
- die Methode beschreiben, mit der man die Poolgröße eines Hosting-Tarifs findet.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Velocity-Worker und -Pools](../../../hosting/server/velocity-worker-und-pools.md) und
[Kapitel 8, Abschnitt 8.3.6](../../../../installation/08-die-website-ausliefern.md#836-worker-pool-größe-der-dynamische-pool-und-die-zygote).

1. **Die Einstellungen** (4 Minuten). `Workers` (ausgeliefert 4, „explicit, always“), `SpareWorkers` (0 ist ein fester
   Pool), `IdleWorkerTimeout` (60 s), `Instances` (Server, die sich die Ports teilen), `Zygote`, `ForkPerRequest`
   (`disabled`: persistente Worker).
2. **Was ein Worker kostet** (4 Minuten). Gemessen auf 12 Kernen mit PHP 8.5: etwa 10 bis 11 MB eigener Speicher für ein
   vollständiges Exponential, mit einem gemeinsamen Teil von etwa 47 MB, der einmal bezahlt wird. RSS zählt jede
   gemeinsame Seite einmal pro Worker und überschätzt einen Pool um ein Vielfaches. Eine andere Messung in den Quellen,
   auf einer Testinstallation ohne Vorwärmen, fand weit mehr pro Worker; sagen Sie den Teilnehmenden, dass sie ihre
   eigenen Websites messen sollen.
3. **Fest oder dynamisch** (3 Minuten). Ein fester Pool von 590 belegte auf einer Installation 7.094 MB, der dynamische
   Pool 626 MB. Für einen Tarif: `Workers` ist die verkaufte Obergrenze, `SpareWorkers` die Untergrenze, die der Kunde
   immer hat. Der Startpunkt aus Kapitel 8 für den Produktivbetrieb: `Workers=64`, `SpareWorkers=8`,
   `IdleWorkerTimeout=60`.
4. **Ersetzen** (3 Minuten). Nach `maxRequests` (1000), wenn der Heap `workerMemoryCeiling` überschreitet, bei
   unausgeglichenen Ausgabepuffern oder wenn eine Anfrage über `requestTimeout` (30 s, der Client bekommt 504)
   hinausläuft. Ein `POST` wird nie zweimal ausgeführt.
5. **Persistente Worker** (3 Minuten). Gemessen an einer Seite der Administration mit angemeldetem Benutzer: 256 bis
   284 ms mit einem frischen Worker pro Anfrage, 73 bis 80 ms mit persistenten Workern. Vier Kernel-Registries werden
   absichtlich behalten; behalten Sie nichts anderes, sonst gelangt die Anfrage eines Besuchers in die nächste.
6. **Die Zahl finden** (3 Minuten). Messen Sie mit `exp:benchmark --cold`, erhöhen Sie `Workers` schrittweise,
   stimmen Sie bis zum Durchsatzabbruch ab und verwenden Sie dann einen dynamischen Pool (Modul 7 übt das).

### Häufige Fragen

**Was geschieht, wenn jeder Worker beschäftigt ist?** Die Anfrage wartet im Elternprozess; sie wird erst abgelehnt
(`503`), wenn `maxConnections` (1024) erreicht ist.

**Stellen mehr Instanzen Seiten schneller dar?** Nein. Instanzen helfen zwischengespeicherten Seiten, die ein Server
begrenzt durch einen Kern beantwortet; dargestellte Seiten sind durch die Worker begrenzt. Teilen Sie `Workers` durch
`Instances`, um denselben Speicher zu behalten.

**Was ist die Zygote?** Ein Prozess, der geforkt wird, bevor die erste Verbindung angenommen wird; spätere Worker
werden aus ihm geforkt und erben keine Besucherverbindungen. Sie braucht die Erweiterung `sockets`.

**Spielen Event-Streams eine Rolle?** Jeder offene Stream hält einen Worker für seine ganze Lebensdauer; `StreamShare`
und `MaxStreams` begrenzen sie. Halten Sie die Grenze deutlich unter `Workers`.

### Stolperfallen

- **Monitoring, das RSS summiert.** Es meldet Speicher, den der Rechner nicht verwendet. Zeigen Sie die Karte Worker
  Memory des Bedienfelds, die PSS zeigt.
- **Die Zahlen des Kurses übernehmen.** Die Zahlen beschreiben die Templates einer Website auf einem Rechner. Die Übung
  verwendet absichtlich kleine Zahlen.
- **`settings/velocity.ini` bearbeiten.** Verwenden Sie `config set`; die mitgelieferte Datei wird bei Updates ersetzt.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation, ausgeliefert von Velocity mit der Engine `qbix`.

1. Zeigen Sie die aktuellen Pool-Einstellungen:

   ```bash
   php bin/php/console exp:velocity config list ServerSettings
   ```

2. Notieren Sie `Workers`, `SpareWorkers`, `IdleWorkerTimeout` und `ForkPerRequest`.
3. Stellen Sie einen kleinen dynamischen Pool ein:

   ```bash
   php bin/php/console exp:velocity config set ServerSettings Workers 8
   php bin/php/console exp:velocity config set ServerSettings SpareWorkers 2
   ```

4. Starten Sie neu: `php bin/php/console exp:velocity restart`.
5. Öffnen Sie die Dashboard-Adresse, die `exp:velocity status` auflistet (`/Q/dashboard`), in einem Browser auf
   demselben Rechner und suchen Sie die Worker und ihren Speicher.
6. Erzeugen Sie mit zehn parallelen Anfragen etwas Last (ersetzen Sie die Adresse):

   ```bash
   for i in 1 2 3 4 5 6 7 8 9 10; do curl -s -o /dev/null http://127.0.0.1:8088/ & done; wait
   ```

7. Sehen Sie sich das Dashboard erneut an und notieren Sie die Anzahl der Worker.
8. Warten Sie mehr als 60 Sekunden und sehen Sie noch einmal nach.
9. Setzen Sie die Einstellungen mit `config unset` für beide Werte zurück und starten Sie neu.

## Erwartetes Ergebnis

- Schritt 5 zeigt direkt nach dem Neustart zwei untätige Worker (die Reserve-Worker).
- Schritt 7 zeigt möglicherweise mehr Worker, bis zu acht, wenn sich Anfragen überschnitten haben; Schritt 8 zeigt, dass
  zusätzliche Worker nach `IdleWorkerTimeout` beendet wurden.
- Schritt 9 hinterlässt die mitgelieferten Werte; `config list` zeigt bei den beiden Einstellungen kein `*`.
- Der Trainer prüft das, indem er die Teilnehmenden bittet, den Speicherwert pro Worker vom Dashboard abzulesen und zu
  sagen, ob es PSS ist.

## Quiz

1. Welche Einstellung macht aus einem festen Pool einen dynamischen?
   A. `Workers` B. `SpareWorkers` C. `Instances` D. `ForkPerRequest`
2. Warum darf ein Monitoringsystem RSS für die Worker von Velocity nicht summieren?
3. Eine Anfrage läuft mit den Standardeinstellungen der Engine 45 Sekunden. Was bekommt der Client?
4. Wofür ist `KeepGlobals`, und was darf nie hinzugefügt werden?
5. Richtig oder falsch: Vier Instanzen stellen nicht zwischengespeicherte Seiten viermal schneller dar als eine.

<details>
<summary>Antworten</summary>

1. **B.** Über 0 hält der Pool so viele untätige Worker bereit und wächst bis `Workers`.
2. RSS zählt jede gemeinsame Copy-on-Write-Seite einmal pro Worker und überschätzt den Pool um ein Vielfaches; beurteilen
   Sie PSS oder privaten Speicher ([Velocity-Worker und -Pools](../../../hosting/server/velocity-worker-und-pools.md#was-ein-worker-kostet)).
3. Ein `504`; die Anfrage wird nach `requestTimeout` (30 Sekunden) beendet.
4. Für eine globale Variable, die einmal von `include_once` gefüllt wird; nie eine anfragebezogene globale Variable, die
   die Anfrage eines Besuchers in die nächste tragen würde.
5. **Falsch.** Dargestellte Seiten sind durch die Worker begrenzt; Instanzen helfen zwischengespeicherten Seiten.

</details>

## Weiterführende Informationen

- [Velocity-Worker und -Pools](../../../hosting/server/velocity-worker-und-pools.md): Einstellungen, Kosten und die
  Methode zur Bemessung.
- [Spezifikation des Velocity-Worker-Pools](../../../../spezifikationen/6.0/velocity-worker-pool.md): die Messungen.
- [Speicher, CPU und Festplatte bemessen](../../../hosting/grundlagen/speicher-cpu-und-festplatte-dimensionieren.md): einen Rechner planen.
- [Eine gehostete Website messen](../../../hosting/leistung/benchmark-einer-gehosteten-website.md): messen, bevor Sie
  entscheiden.

## Verwandte Seiten

- [Modul 2: Velocity](README.md)
- [Hosting und Betrieb: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
