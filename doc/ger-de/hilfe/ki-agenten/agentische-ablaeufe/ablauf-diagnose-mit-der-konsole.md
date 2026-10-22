---
title: Ein Diagnoseablauf mit der Konsole
audience: ai-agents, developer, admin
category: ai-agents
order: 71
summary: Ein durchgearbeitetes Beispiel für einen Coding-Agenten, der ein Problem auf einer Entwicklungskopie diagnostiziert, ohne etwas zu ändern - "eine Einstellung wirkt nicht, und anonyme Besucher sehen einen Artikel nicht" - mit nur lesenden Konsolenbefehlen (exp:ini where, exp:ini get, exp:access:check, exp:logs, exp:audit, exp:help) und einem Bericht am Ende, nach dem ein Mensch handelt.
keywords: Diagnose, Fehlerbehebung mit KI, nur lesender Agent, Konsole, exp:ini where, exp:ini get, exp:access:check, exp:logs, exp:audit, exp:help, durchgearbeitetes Beispiel, agentischer Ablauf, diagnosis, troubleshooting with AI, read only agent, console, worked example, agentic workflow
type: help
---
# Ein Diagnoseablauf mit der Konsole

Diese Seite richtet sich an Entwickler und Administratoren, die möchten, dass ein Coding-Agent die Ursache eines Problems findet,
ohne die Installation anzufassen. Sie führt durch einen realistischen Fall mit nur lesenden Konsolenbefehlen, die mit dem Kernel
ausgeliefert werden. Der Agent endet mit einem Bericht; ein Mensch entscheidet, was geändert wird.

## Inhalt

1. [Die Einrichtung](#die-einrichtung)
2. [Der Fall](#der-fall)
3. [Schritt für Schritt](#schritt-für-schritt)
4. [Der Bericht](#der-bericht)
5. [Warum dieser Ablauf sicher ist](#warum-dieser-ablauf-sicher-ist)

## Die Einrichtung

- Eine Entwicklungs- oder Staging-Kopie der Installation, auf der das Problem nachgestellt ist.
- Ein Coding-Agent in einem Terminal auf dieser Kopie, angewiesen, nur die unten stehenden Befehle und die Dokumentation zu
  verwenden ([Coding-Agenten in einem Exponential-Projekt](coding-agenten-in-einem-exponential-projekt.md)).
- Keine Produktionsdaten in der Kopie oder zumindest keine, die der Modellanbieter des Agenten nicht sehen darf.

## Der Fall

Ein Website-Verantwortlicher meldet zwei Dinge: Eine Einstellung im Override einer Erweiterung "wirkt nicht", und ein Artikel zeigt
anonymen Besuchern eine Seite "Zugriff verweigert".

## Schritt für Schritt

| Schritt | Der Agent fragt | Befehl | Warum |
|---|---|---|---|
| 1 | Was sagt die Dokumentation zu der Einstellung? | `./console exp:help search "<setting name>"` | Zuerst das beabsichtigte Verhalten kennen ([Agentenfreundliche Dokumentation](agentenfreundliche-dokumentation.md)) |
| 2 | Welche Dateien setzen sie, in Ladereihenfolge, und welcher Wert gewinnt? | `./console exp:ini where <file>/<Block>/<Variable>` | Zeigt jede Datei, die die Variable setzt, und den wirksamen Wert |
| 3 | Was ist der Wert jetzt? | `./console exp:ini get <file>/<Block>/<Variable>` | Bestätigt den wirksamen Wert |
| 4 | Darf der anonyme Benutzer den Artikel lesen, und welche Einschränkung verweigert es? | `./console exp:access:check --user=anonymous --node=<id>` | Endet mit ALLOWED oder DENIED und nennt Richtlinie und Einschränkung |
| 5 | Haben die Logs um die Zeit der Meldung etwas festgehalten? | `./console exp:logs --tail=var/log/error.log --lines=50` | Die letzten Einträge, mit standardmäßig maskierten personenbezogenen Daten |
| 6 | Hat jemand kürzlich die Rolle oder die Einstellungen geändert? | `./console exp:audit search --name=<event name>` und `./console exp:audit tail --channel=access` | Rollenänderungen und Schreibzugriffe auf Einstellungen stehen im Audit-Trail |

Gewohnheiten, die die Schritte verlässlich machen:

- Eine Frage je Schritt; lassen Sie den Agenten jede Antwort lesen, bevor er den nächsten Befehl ausführt.
- Verwenden Sie `--json`, wo ein Befehl es anbietet (`exp:access:check`, `exp:help`), damit der Agent auswertet, statt zu raten.
- Fügen Sie `--allow-root-user` nur hinzu, wo die Kopie Befehle als root ausführt, wie die Anleitungen zeigen.

## Der Bericht

```text
1. Setting: <file>/<Block>/<Variable> is set in three files. The extension's override sets it,
   but settings/override/<file>.append.php sets it again later in load order, so that value wins.
2. Access: the article is in section <n>. The Anonymous role's content/read is limited to
   section <m>. exp:access:check names the Section limitation as the refusal.
3. Suggested changes (not made): remove or change the later override; either move the article to
   section <m> or extend the Anonymous policy, after deciding which is intended.
4. Pages used: <help page ids>.
```

Ein Mensch nimmt die Änderung in der Administration oder in den Einstellungen vor, leert die Caches und bittet den Agenten dann,
die Schritte 2 bis 4 zur Bestätigung erneut auszuführen.

## Warum dieser Ablauf sicher ist

- Jeder verwendete Befehl liest nur: `exp:ini where` und `get`, `exp:access:check` ("nobody signs in and nothing is changed"),
  lesendes `exp:logs`, lesendes `exp:audit`, `exp:help`.
- Der Agent arbeitet auf einer Kopie, nicht in der Produktion.
- Der Bericht nennt seine Belege; ein Mensch kann dieselben Befehle ausführen, um jede Aussage zu prüfen.

Befehle, die etwas ändern (`exp:ini set`, das Leeren von Caches, schreibende Skripte), bleiben beim Menschen.

## Verwandte Seiten

- [Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md)
- [Einen Fehler anhand seiner Referenz finden](../../../anleitungen/fehler-anhand-der-referenz-finden.md)
- [Der Log-Betrachter](../../../funktionen/6.0/log-betrachter.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
