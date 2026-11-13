---
title: "Request Shield: die Schalter"
audience: admin, hosting
category: administration
order: 20
summary: Die Schalter oben auf der Übersicht des Request Shields - Pausieren und Einschalten, Durchsetzen, Beobachten und Aus -, was jeder auf dem Server ändert, wann er wirkt, Sicherung und Übersetzungsprüfung beim Moduswechsel, die Richtlinie requestshield/manage und die Aufzeichnung, wer was geschaltet hat.
keywords: Request Shield, Schalter, requestshield/switch, requestshield/manage, Pausieren, Einschalten, PAUSED, var/request-shield/PAUSED, Durchsetzen, Beobachten, Aus, set mode, Sicherung, var/request-shield/backup, Übersetzungsprüfung, recheck, requestshield.log, Formular-Token, switches, pause, switch on, enforce, monitor, off
type: help
---
# Request Shield: die Schalter

Die Schalter stehen oben auf der Übersicht (Setup > Request Shield). Benutzer mit der Richtlinie
`requestshield/manage` sehen sie; alle anderen mit `requestshield/read` sehen die Übersicht ohne Schalter.

## Inhalt

1. [Die Schalter im Überblick](#die-schalter-im-überblick)
2. [Pausieren und Einschalten](#pausieren-und-einschalten)
3. [Durchsetzen, Beobachten und Aus](#durchsetzen-beobachten-und-aus)
4. [Wenn ein Schalter verweigert wird](#wenn-ein-schalter-verweigert-wird)
5. [Wer was geschaltet hat](#wer-was-geschaltet-hat)

## Die Schalter im Überblick

| Schalter | Was er ändert | Wirkt |
|---|---|---|
| **Pausieren** | legt `var/request-shield/PAUSED` an | ab der nächsten Anfrage, auf jedem Server |
| **Einschalten** | entfernt `var/request-shield/PAUSED` | ab der nächsten Anfrage, auf jedem Server |
| **Durchsetzen**, **Beobachten**, **Aus** | schreibt die Zeile `set mode` der Hauptregeldatei der Website neu | innerhalb der `recheck` Sekunden der Regeln (standardmäßig 10) |

Jeder Schalter ist eine Schaltfläche in einem Formular, das mit dem Formular-Token gesendet wird, und führt mit einer
Meldung, was getan wurde, zurück zur Übersicht.

## Pausieren und Einschalten

**Pausieren** ist der schnellste Weg, den Shield aus dem Weg zu nehmen: Solange `var/request-shield/PAUSED`
existiert, entscheidet der Shield nichts, und jede Anfrage erreicht die Website, als wäre er nicht installiert. An den
Regeln ändert sich nichts. Nutzen Sie es, wenn Sie den Shield im Verdacht haben und es sofort wissen wollen.

**Einschalten** entfernt die Datei. Aus einer Shell geht dasselbe: Legen Sie `var/request-shield/PAUSED` an oder
entfernen Sie die Datei.

## Durchsetzen, Beobachten und Aus

Die Modus-Schaltflächen ändern den Modus der Regeln:

- **Durchsetzen** (enforce): Die Entscheidungen werden ausgeführt. So wird der Shield installiert.
- **Beobachten** (monitor): Jede Entscheidung wird nur protokolliert, niemand wird abgewiesen. Siehe
  [Beobachten und durchsetzen](beobachten-und-durchsetzen.md).
- **Aus** (off): Nichts wird entschieden.

Ein Moduswechsel:

1. verwendet die Hauptregeldatei der Website (`settings/request-shield/request-shield.rules` oder die Datei, die
   `config.php` nennt). Solange die Standarddatei der Extension verwendet wird, legt der **erste** Wechsel daraus die
   eigene Datei der Website an und kopiert die drei `exponential*.rules` daneben; ab dann wird die Datei der Website
   verwendet;
2. sichert die bisherige Datei in `var/request-shield/backup/` (ihr Name, das Datum und ein kurzer Hash);
3. übersetzt die neuen Regeln; lassen sie sich nicht übersetzen, stellt er die bisherige Datei wieder her und nennt
   den Grund.

Der Modus `strict` liegt auf keiner Schaltfläche: Er bleibt eine Entscheidung, die in der Regeldatei steht.

## Wenn ein Schalter verweigert wird

Ein Schalter wird mit Begründung verweigert, wenn der Webserver die Regeldatei oder ihr Verzeichnis (für einen Modus)
oder `var/request-shield/` (für eine Pause) nicht beschreiben kann. Die Schaltflächen sind dann deaktiviert. Bitten
Sie die Person, die den Server betreut, die Dateien für den Benutzer beschreibbar zu machen, unter dem PHP für die
Website läuft, oder die Änderung auf dem Server vorzunehmen.

## Wer was geschaltet hat

Jeder Schalter wird mit Login und ID des Benutzers und dem Ergebnis in `var/log/requestshield.log` festgehalten. Sie
können die Datei unter **Setup > Logdateien** lesen.
