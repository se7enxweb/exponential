---
title: Eine Schnittstelle für einen Agenten wählen
audience: ai-agents, developer, app-creator
category: ai-agents
order: 61
summary: Die REST-API, expservices, ezjscore-Funktionen und die Konsole auf dem Server für KI-Agenten verglichen - was jede lesen und schreiben kann, wie sie authentifiziert, wo der Agent laufen kann, ob Schreibzugriffe Entwürfe oder Veröffentlichungen sind und welche zu welcher Art von Agent passt.
keywords: Schnittstelle wählen, REST-API, expservices, ezjscore, Konsole, exp:help, Agentenanbindung, Authentifizierung, Lesezugriffe, Schreibzugriffe, Entwürfe, veröffentlichen, Vergleich, choose interface, REST API, console, agent integration, authentication, reads, writes, drafts, publish, comparison
type: help
---
# Eine Schnittstelle für einen Agenten wählen

Diese Seite richtet sich an Entwickler, die einen KI-Agenten mit Exponential verbinden und eine Schnittstelle wählen müssen. Alle
handeln als ein Konto und beachten dessen Richtlinien; sie unterscheiden sich darin, was sie erreichen, wie sie authentifizieren
und wo der Agent laufen kann. Ein MCP-Server ist heute nicht Teil von Exponential, ein Agent verwendet also eine der Schnittstellen
unten.

## Inhalt

1. [Nebeneinander](#nebeneinander)
2. [Wann Sie die REST-API verwenden](#wann-sie-die-rest-api-verwenden)
3. [Wann Sie expservices verwenden](#wann-sie-expservices-verwenden)
4. [Wann Sie eigene ezjscore-Funktionen verwenden](#wann-sie-eigene-ezjscore-funktionen-verwenden)
5. [Wann Sie die Konsole auf dem Server verwenden](#wann-sie-die-konsole-auf-dem-server-verwenden)
6. [Schreibzugriffe: Entwürfe oder Veröffentlichungen](#schreibzugriffe-entwürfe-oder-veröffentlichungen)

## Nebeneinander

| | REST-API | expservices | Eigene ezjscore-Funktion | Konsole auf dem Server |
|---|---|---|---|---|
| Mit dem Hauptrepository ausgeliefert | Ja (Kernel) | Ja (Standarddistribution) | Ja (ezjscore) | Ja (Kernel) |
| Erreicht | Knoten, Kinder, Felder, Objekte; Anlegen und Entfernen in v2 | Über tausend benannte Dienste der Administration | Was Sie schreiben | Einstellungen mit ihren Quelldateien, Zugriffsprüfungen, Logs, den Audit-Trail, die Hilfe |
| Authentifiziert | OAuth2-Token oder persönlicher API-Schlüssel mit Geltungsbereichen | Session oder ein persönliches Token `expt_...` | Session; Ihre eigene Richtlinienprüfung | Der Systembenutzer, der den Befehl ausführt |
| Schreibt | Als Eigentümer des Schlüssels, durch Geltungsbereich begrenzt | Als Benutzer des Tokens; POST | Wie Sie es festlegen | Vermeiden: Geben Sie einem Coding-Agenten nur lesende Befehle |
| Läuft wo | Überall über HTTPS | Überall über HTTPS | Meist im Browser | Auf dem Server oder einer Entwicklungskopie |
| Als Nächstes lesen | [Agenten und die REST-API](agenten-und-die-rest-api.md) | [Agenten und expservices](agenten-und-expservices.md) | [JSON-Endpunkte mit ezjscore](../../ersteller/anwendungsentwickler/json-endpunkte-mit-ezjscore.md) | [Ein Diagnoseablauf mit der Konsole](ablauf-diagnose-mit-der-konsole.md) |

## Wann Sie die REST-API verwenden

- Der Agent liest veröffentlichte Inhalte für ein anderes System oder legt Inhalte an einer bekannten Stelle an.
- Er läuft anderswo und spricht HTTPS.
- Sie möchten die engsten Zugangsdaten: einen persönlichen API-Schlüssel nur mit dem Geltungsbereich `read`
  ([Ein nur lesender API-Schlüssel, Schritt für Schritt](ein-nur-lesender-api-schluessel-schritt-fuer-schritt.md)).

## Wann Sie expservices verwenden

- Der Agent braucht Funktionen der Administration über Inhalte hinaus: Benutzer, Caches, Cronjobs, Shop, Feeds, Tags, das Audit.
- Sie möchten einen Katalog von Diensten mit Zugriffsregeln und einem Merkmal `write` je Dienst, der sich natürlich auf eine
  Werkzeugliste für ein Modell abbilden lässt.

## Wann Sie eigene ezjscore-Funktionen verwenden

- Ihre eigene Seite in der Administration oder auf der Website ruft den Agenten auf, und der Agent braucht eine kleine, bestimmte
  Antwort, die keine bestehende Schnittstelle gibt.
- Sie schreiben die Richtlinienprüfung selbst, wie die Anleitung zeigt. Ziehen Sie expservices vor, wenn die Funktion auch anderen
  nützen könnte ([JSON-Endpunkte mit ezjscore](../../ersteller/anwendungsentwickler/json-endpunkte-mit-ezjscore.md#ezjscore-expservices-oder-rest)).

## Wann Sie die Konsole auf dem Server verwenden

- Ein Coding-Agent arbeitet an einem Projekt und braucht Laufzeitfakten: welche Datei eine Einstellung setzt
  (`./console exp:ini where`), was ein Konto tun darf (`./console exp:access:check`), was die Logs sagen (`./console exp:logs`)
  und was die Dokumentation sagt (`./console exp:help search`).
- Verwenden Sie sie auf einer Entwicklungs- oder Staging-Kopie. Ein Befehl läuft als der Systembenutzer, der ihn startet, mit
  Zugriff auf die ganze Installation, er ist also keine Schnittstelle für Agenten in der Produktion.

## Schreibzugriffe: Entwürfe oder Veröffentlichungen

Wissen Sie, was ein Schreibzugriff erzeugt, bevor ein Agent einen ausführt:

| Schnittstelle | Ein Schreibzugriff auf Inhalte erzeugt |
|---|---|
| Exponential Intelligence | Einen **Entwurf** des Redakteurs, zur Prüfung |
| REST-API v2 create | Ein veröffentlichtes Objekt ("create and publish") |
| Inhaltsschreibzugriffe über expservices | Was der Dienst erklärt; prüfen Sie seinen Katalogeintrag |

Müssen Menschen die Ausgabe eines Agenten prüfen, bevor sie öffentlich wird, lesen Sie
[Einen Menschen beteiligt halten](einen-menschen-beteiligt-halten.md).

## Verwandte Seiten

- [Welcher Weg zu Ihrer Aufgabe passt](../welcher-weg-zu-ihrer-aufgabe-passt.md)
- [Zugangsdaten für Agenten](zugangsdaten-fuer-agenten.md)
- [Headless Exponential](../../ersteller/anwendungsentwickler/headless-exponential.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
