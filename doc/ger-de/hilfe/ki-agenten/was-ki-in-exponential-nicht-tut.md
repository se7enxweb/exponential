---
title: Was KI in Exponential nicht tut
audience: ai-agents, developer, app-creator, admin, editor
category: ai-agents
order: 7
summary: Die Grenzen von KI in Exponential an einem Ort - der Assistent veröffentlicht nicht selbst, verwendet kein Dienstkonto, lädt keine Bilder oder Dateien hoch und sendet nichts, bevor Sie einen Anbieter konfigurieren; Agenten erhalten keine Rechte über ihr Konto hinaus; und was heute nicht Teil von Exponential ist - damit niemand eine Funktion verspricht, die es nicht gibt.
keywords: Grenzen, tut nicht, kann nicht, keine automatische Veröffentlichung, nur Entwürfe, kein Dienstkonto, keine Uploads, nicht enthalten, Aussagen, Genauigkeit, limits, does not, cannot, no auto publish, drafts only, no service account, no uploads, not included, claims, accuracy
type: help
---
# Was KI in Exponential nicht tut

Diese Seite richtet sich an alle, die die KI-Funktionen von Exponential erklären, kaufen, konfigurieren oder darauf aufbauen. Sie
sammelt die Grenzen an einem Ort. Jede ist eine bewusste Entscheidung, dokumentiert bei dem Teil, der sie trifft.

## Inhalt

1. [Exponential Intelligence](#exponential-intelligence)
2. [Schnittstellen für Agenten](#schnittstellen-für-agenten)
3. [Im ganzen System](#im-ganzen-system)
4. [Zu vermeidende Aussagen](#zu-vermeidende-aussagen)

## Exponential Intelligence

| Es tut nicht | Was es stattdessen tut |
|---|---|
| Selbst veröffentlichen | Schreibt Entwürfe; "Send for approval" führt die normale Veröffentlichungsoperation als der Redakteur aus, und ein Freigabe-Workflow kann sie anhalten |
| Ein Dienstkonto verwenden oder den Benutzer wechseln | Führt jede Abfrage und jeden Entwurf als der angemeldete Redakteur aus, mit dessen Inhaltsrichtlinien; eingereihte Arbeit läuft als der Benutzer, der sie eingereiht hat |
| Löschen, verschieben, Berechtigungen ändern oder E-Mails senden | Kann nur lesen, was der Redakteur lesen darf, und Entwürfe schreiben, die der Redakteur schreiben darf |
| Bilder oder Dateien hochladen oder Relationen setzen | Sagt das; der Redakteur setzt sie im Bearbeitungsformular. Mit einem Vision-Modell kann es den Alternativtext eines bereits vorhandenen Bildes vorschlagen |
| Die ganze Website auf einmal lesen | Liest nur, was das Modell über seine Werkzeuge anfordert, mit Grenzen für Listen- und Feldlänge, und nur, was die Datenrichtlinie an diesen Anbieter gehen lässt |
| Etwas senden, bevor ein Anbieter konfiguriert ist | Bleibt untätig; es braucht einen Schlüssel oder ein lokales Modell |
| Jedes Modell schreiben lassen | Nur Modelle mit nativem Tool-Calling, die die Live-Probe bestanden haben oder per Konfiguration als vertrauenswürdig gelten, dürfen Entwürfe schreiben ([Das Schreibtor und die Probe](expai/das-schreibtor-und-die-probe.md)) |
| Dem Modell Schreibdienste von expservices anbieten | Nur Lesedienste, die sich dafür entschieden haben ([Dienstwerkzeuge](expai/dienstwerkzeuge.md)) |
| Anderen Programmen dienen | Es arbeitet in der Administration für angemeldete Redakteure; Programme verwenden die REST-API oder expservices |
| Die Texte von Anfragen und Antworten in seinem Nutzungsprotokoll behalten | Hält Tokens, Modell, Kosten, Werkzeuge und Fehler fest; vollständige Anfragen nur mit `[UsageSettings] LogRequests=enabled`, einer Debug-Einstellung, die nicht für die Produktion gedacht ist |

Quellen: die Seiten [FAQ](https://github.com/se7enxweb/expai/blob/main/doc/faq.md) und
[Security](https://github.com/se7enxweb/expai/blob/main/doc/security.md) von expai.

## Schnittstellen für Agenten

| Sie tun nicht | Was sie stattdessen tun |
|---|---|
| Einem Agenten mehr geben, als sein Konto hat | REST und expservices handeln als Eigentümer des Schlüssels oder Tokens, über die Richtlinienprüfungen des Kernels |
| Einem Schlüssel mehr geben, als seine Geltungsbereiche erlauben | Eine REST-Route braucht den Geltungsbereich, dem sie zugeordnet ist; eine Schreibroute einer Erweiterung bleibt für Schlüssel geschlossen, bis ihr Geltungsbereich benannt ist |
| Schreibzugriffe ohne POST annehmen | Schreibzugriffe über expservices brauchen POST und mit einer Session zusätzlich ein Formular-Token |
| Für einen deaktivierten Benutzer weiterarbeiten | Jeder Schlüssel und jedes Token eines deaktivierten Eigentümers hört sofort auf |

Ein MCP-Server für Agenten ist heute nicht Teil von Exponential.

## Im ganzen System

- **Es ist kein KI-Modell eingebaut.** Exponential ruft Anbieter auf, die Sie konfigurieren; ein lokales Modell läuft in Ollama,
  nicht in PHP.
- **Kein Training mit Ihren Inhalten** führt Exponential durch. Was ein Anbieter mit Anfragen tut, regelt Ihr Vertrag mit ihm;
  expai hält die erklärte Region und Aufbewahrung des Anbieters fest
  ([Datenschutz und Daten, die die Website verlassen](steuerung/datenschutz-und-daten-die-die-website-verlassen.md)).
- **Keine rechtliche Bewertung.** Die Einstellungen der Datenrichtlinie und die Seite "EI: Summary" helfen einem
  Datenschutzbeauftragten; sie sind keine Erklärung der Konformität.

## Zu vermeidende Aussagen

| Sagen Sie nicht | Sagen Sie |
|---|---|
| "Die KI veröffentlicht Inhalte für Sie." | "Sie schreibt Entwürfe; ein Mensch veröffentlicht." |
| "Es ist DSGVO-konform." | "Es hält fest, wohin jede Anfrage ging, und lässt Sie Sektionen und Klassen bei EU- oder lokalen Anbietern halten." |
| "Es ist kostenlos." | "Die Software ist frei; ein gehosteter Anbieter kann je Anfrage Gebühren verlangen, und expai schätzt und begrenzt diese Kosten." |
| "Exponential AI" oder "ExpAI" | "Exponential Intelligence", oder `expai` in technischem Text |
| "Agenten können im CMS alles tun." | "Agenten handeln als ein Konto, das Sie anlegen, mit den Rechten, die Sie ihm geben." |

Die Fassung dieser Regeln für Fürsprecher: [Argumente: Exponential Intelligence](../fuersprecher/gespraechspunkte-exponential-intelligence.md)
und [Aussagen, die Sie nicht machen dürfen](../fuersprecher/aussagen-die-sie-nicht-machen-duerfen.md).

## Verwandte Seiten

- [Was es heute gibt](was-es-heute-gibt.md)
- [Entwürfe und menschliche Prüfung](expai/entwuerfe-und-menschliche-pruefung.md)
- [Zugangsdaten für Agenten](agentische-ablaeufe/zugangsdaten-fuer-agenten.md)
- [Einwand: "Was ist mit den KI-Funktionen?"](../fuersprecher/einwand-ki-funktionen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
