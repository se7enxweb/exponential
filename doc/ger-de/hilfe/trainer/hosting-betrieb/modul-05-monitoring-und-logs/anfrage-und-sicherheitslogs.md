---
title: "Lektion 5.3: Anfrage-Logs und das Sicherheitslog"
audience: trainer, hosting, admin
category: training
order: 53
summary: Die Teilnehmenden lesen Zeilen aus 404.log und security.log Feld für Feld, erklären den Anfrage-Klassifizierer und warum er nie blockiert, stimmen ihn mit einem Ausschluss ab und bauen eine wöchentliche Sicherheitsroutine auf. 40 Minuten.
keywords: hosting und betrieb, anfrage-logs, 404.log, security.log, notice.log, anfrage-klassifizierer, sondierung, traversal, injection, scanner, schwellenwert, requestclassifier.ini, auth.log, csp.log, audit-protokoll, maskierung, hosting and operations, request logs, request classifier, probe, threshold, audit trail, masking
type: help
---
# Lektion 5.3: Anfrage-Logs und das Sicherheitslog

Exponential schreibt Antworten auf nicht gefundene Seiten und verdächtige Anfragen in eigene Logs, sodass `error.log`
nur die echten Fehler enthält. Für einen Hoster bedeutet das schnelleren Support („warum bekommen Besucher Fehler?“)
und ein klares Sicherheitssignal. Diese Lektion bringt den Teilnehmenden bei, beide Logs zu lesen, den Klassifizierer
abzustimmen und zu wissen, was er nicht tut.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Hosting und Betrieb](../README.md) |
| Modul | [Modul 5: Monitoring und Logs](README.md) |
| Dauer | 40 Minuten (20 Präsentation, 20 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Support- und Sicherheitsmitarbeitende im Hosting |
| Voraussetzungen | [Lektion 5.2](log-viewer-und-rotation.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- sagen, in welchem von `error.log`, `404.log`, `notice.log` und `security.log` eine Anfrage landet;
- die Felder einer Zeile in `404.log` lesen, besonders `reason=`;
- eine Zeile in `security.log` lesen (`<category>/<rule>`, Konfidenz, Status) und die Schwellenwertregel;
- erklären, dass der Klassifizierer nur klassifiziert, und nennen, wohin das Blockieren gehört;
- einen legitimen Pfad von einer Regel ausschließen, statt einen Regelsatz auszuschalten.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Anfrage-Logs](../../../hosting/betrieb/anfrage-logs.md), [Sicherheitslog](../../../hosting/betrieb/sicherheitslog.md)
und [Anfrage-Logs: 404.log und security.log](../../../../funktionen/6.0/anfrageprotokolle.md).

1. **Drei Logs statt einem** (4 Minuten). `error.log` für echte Fehler, `404.log` für 404 und 410 mit dem Grund,
   `notice.log` für 401 und 403, `security.log` für klassifizierte Anfragen mit beliebigem Status. Jede Anfrage wird in
   eines davon geschrieben, nie in zwei.
2. **Eine Zeile in 404.log** (4 Minuten). Maskierte Adresse, Status, Methode und URI mit Query-Werten als `***`,
   Siteaccess, `reason=` (`node:<id>` ist ein Link auf entfernte Inhalte, `module:wp-admin` ein Robot, der nach einem
   anderen Produkt sucht), Referrer, ein kurzer Hash des User-Agents.
3. **Eine Zeile in security.log** (4 Minuten). `probe/wp-login confidence=high 404 GET /wp-login.php ...`. Kategorien:
   `probe`, `traversal`, `injection`, `scanner`, `threshold`. Die Schwellenwertregel markiert mehr als 30 Antworten
   „nicht gefunden“ innerhalb von 60 Sekunden, verschlüsselt mit einem gesalzenen Hash, nie mit der Adresse.
4. **Blockiert nie** (3 Minuten). „Nichts wird blockiert.“ Blockieren gehört zu einem Schutzschild vor dem Kernel, zum
   Webserver oder zur Firewall oder zu einer Listener-Klasse. Eine Anfrage, die der Schutzschild ablehnt, steht nicht in
   `security.log`; zählen Sie sie nicht doppelt.
5. **Eine wöchentliche Routine** (5 Minuten). Die Fehlerübersicht gruppiert `security.log` nach Regel; ein plötzlicher
   Anstieg einer Regel ist einen Blick wert. Nehmen Sie `auth.log` (Anmeldung in zwei Schritten), `csp.log` (Berichte
   der Content Security Policy) und das Audit-Protokoll dazu.

### Häufige Fragen

**Stehen IP-Adressen in den Logs?** Standardmäßig maskiert (`MaskIP=enabled`); die rohe Adresse wird nie geschrieben.
Sie vollständig zu schreiben braucht einen Grund und eine Rechtsgrundlage; wie lange Logs aufbewahrt werden, ist die
Richtlinie des Kunden und des Hosters.

**Die eigene Anwendung eines Kunden verwendet `/administrator/`. Schalte ich die Sondierungsregeln aus?** Nein. Fügen
Sie in einem Override von `requestclassifier.ini` einen Eintrag `Excludes[<name>]=<regex>` für diesen Pfad hinzu.

**Kann ein 200 in security.log stehen?** Ja: Ein Injection-Versuch gegen eine Suchseite wird normal beantwortet.

**Warum sehe ich eine Anfrage nicht, die am Webserver abgelehnt wurde?** Sie hat Exponential nie erreicht; sehen Sie in
den eigenen Logs des Webservers nach.

### Stolperfallen

- **security.log als Liste von Einbrüchen behandeln.** Es listet Versuche auf. Die meisten sind Robots, die nach
  anderen Produkten suchen.
- **Sondierungen an die Website eines anderen schicken.** In der Übung fragen die Teilnehmenden Pfade nur auf ihrer
  eigenen Installation an.
- **Die mitgelieferte `requestclassifier.ini` bearbeiten.** Verwenden Sie ein Override.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation und eine Shell in ihrem Wurzelverzeichnis. Ersetzen Sie die Adresse
durch Ihre eigene.

1. Fragen Sie eine fehlende Seite mit einem Query-Wert an:
   `curl -s -o /dev/null "http://127.0.0.1:8088/no-such-page?campaign=test"`.
2. Lesen Sie die letzte Zeile von `404.log`: `tail -n 1 var/log/404.log`. Bestimmen Sie die maskierte Adresse, den
   Query-Wert `***` und `reason=`.
3. Fragen Sie auf Ihrer eigenen Website einen Pfad an, den eine Sondierungsregel erkennt:
   `curl -s -o /dev/null http://127.0.0.1:8088/wp-login.php`.
4. Lesen Sie die letzte Zeile von `security.log`: `tail -n 1 var/log/security.log`. Bestimmen Sie Kategorie, Regel,
   Konfidenz und Status.
5. Prüfen Sie, dass die Sondierung nicht auch in `404.log` steht (vergleichen Sie die letzten Zeilen).
6. Öffnen Sie `settings/requestclassifier.ini` nur lesend (`less settings/requestclassifier.ini`) und suchen Sie
   `Max404PerMinute`, den Block `[RuleSet-probes]` und den Kommentar, der `Excludes[<name>]=<regex>` erklärt.
7. Schreiben Sie, ohne sie zu speichern, die Override-Zeilen, die einen legitimen Pfad `/administrator/` von den
   Sondierungsregeln ausschließen würden: einen Block `[RuleSet-probes]` mit einer Zeile `Excludes[<name>]=<regex>`,
   wie die mitgelieferte Datei es beschreibt.
8. Öffnen Sie in der Administration **Setup**, **Logdateien**, dann **Fehlerübersicht**, und suchen Sie Ihre
   Sondierung, nach Regel gruppiert.

## Erwartetes Ergebnis

- Schritt 2: Die Zeile zeigt eine maskierte Adresse, `campaign=***` und ein `reason=`.
- Schritt 4: Die Zeile beginnt mit `probe/` und einem Regelnamen, mit Konfidenz und Status.
- Schritt 5: Die Sondierung steht nur in `security.log`.
- Schritt 7: ein Override in `settings/override/requestclassifier.ini.append.php` mit einer Zeile
  `Excludes[<name>]=<regex>` im Block `[RuleSet-probes]`.
- Der Trainer prüft die Zeilen aus Schritt 7 von zwei Personen anhand der mitgelieferten Datei.

## Quiz

1. Ein Robot fragt `/wp-login.php` an und bekommt ein 404. Welches Log enthält die Zeile?
   A. `error.log` B. `404.log` C. `security.log` D. sowohl `404.log` als auch `security.log`
2. Was sagt `reason=node:9999` in `404.log` dem Support?
3. Blockiert der Anfrage-Klassifizierer Angriffe?
4. Wann markiert die Schwellenwertregel einen Client?
5. Warum steht die rohe Client-Adresse nicht in diesen Logs?

<details>
<summary>Antworten</summary>

1. **C.** Jede Anfrage wird in ein Log geschrieben; eine Sondierung, die mit einem 404 endet, steht nur in
   `security.log`.
2. Ein Link zeigt auf Inhalte (Knoten 9999), die entfernt wurden.
3. Nein. Er klassifiziert nur; Blockieren gehört zu einem Schutzschild, zum Webserver oder zur Firewall oder zu einem
   Listener ([Sicherheitslog](../../../hosting/betrieb/sicherheitslog.md#blockieren-ist-nicht-die-aufgabe-des-klassifizierers)).
4. Wenn er mehr als `Max404PerMinute` (30) Antworten „nicht gefunden“ innerhalb von `WindowSeconds` (60) bekommt.
5. `MaskIP=enabled` ist der Standard; die Adresse wird maskiert, bevor sie geschrieben wird.

</details>

## Weiterführende Informationen

- [Anfrage-Logs](../../../hosting/betrieb/anfrage-logs.md): Felder, Datenschutz, Speicherplatz.
- [Sicherheitslog](../../../hosting/betrieb/sicherheitslog.md): der Klassifizierer, Abstimmung, eine wöchentliche
  Routine.
- [Anfrage-Schutzschild und Anfrageregeln](../../../hosting/sicherheit/request-shield-und-request-regeln.md): wohin das
  Blockieren gehört.
- [Aufbewahrung von Logs und Daten](../../../hosting/sicherheit/aufbewahrung-von-logs-und-daten.md): wie lange Logs aufbewahrt
  werden.

## Verwandte Seiten

- [Modul 5: Monitoring und Logs](README.md)
- [Hosting und Betrieb: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
