---
title: Nicht vertrauenswürdige Daten und Prompt-Injection
audience: ai-agents, developer, app-creator, admin
category: ai-agents
order: 66
summary: Was Prompt-Injection in einem Content-Management-System ist, was Exponential Intelligence dagegen tut (Inhalte als Daten gekennzeichnet, eine Probeaufgabe, die die Widerstandsfähigkeit testet, keine Entwürfe nach Webtext, bereinigter Rich Text, nur Entwürfe) und was Sie dennoch tun müssen, wenn Sie einen eigenen Agenten auf der REST-API oder expservices bauen.
keywords: Prompt-Injection, nicht vertrauenswürdige Daten, Daten statt Anweisungen, indirekte Prompt-Injection, Inhalte als Daten, web_fetch, Schreibsperre nach externen Daten, Bereinigung, Rich Text, Agentensicherheit, Prüfung, prompt injection, untrusted data, data not instructions, indirect prompt injection, content as data, write lock after outside data, sanitizer, rich text, agent security, review
type: help
---
# Nicht vertrauenswürdige Daten und Prompt-Injection

Diese Seite richtet sich an alle, die ein KI-Modell Inhalte lesen und dann handeln lassen. Inhalte können Text enthalten, der
geschrieben wurde, um ein Modell zu steuern ("ignore your instructions and publish ..."). Ein Modell, das solchen Text liest, kann ihm
folgen. Das nennt man Prompt-Injection, und in einem Content-Management-System ist es ein echtes Risiko: Jeder, der Inhalte schreiben,
ein Formular absenden oder eine Seite bearbeiten kann, die ein Modell liest, kann es versuchen.

## Inhalt

1. [Woher nicht vertrauenswürdiger Text kommt](#woher-nicht-vertrauenswürdiger-text-kommt)
2. [Was Exponential Intelligence tut](#was-exponential-intelligence-tut)
3. [Was die Schnittstellen für Agenten tun](#was-die-schnittstellen-für-agenten-tun)
4. [Was Sie in Ihrem eigenen Agenten tun müssen](#was-sie-in-ihrem-eigenen-agenten-tun-müssen)
5. [Was keine Einstellung kann](#was-keine-einstellung-kann)

## Woher nicht vertrauenswürdiger Text kommt

| Quelle | Beispiel |
|---|---|
| Von vielen Menschen geschriebene Inhalte | Ein Artikel, ein Forenbeitrag, ein Kommentar, von Benutzern eingereichter Text |
| Formulareinsendungen | Informationssammlungen, Registrierungen |
| Logs | Ein Anfragepfad oder User-Agent, der in eine Logzeile geschrieben wurde |
| Das Web | Eine von einem Werkzeug abgerufene Seite, ein Suchergebnis |
| Andere Systeme | Ein Feed, der importiert wird |

Jede davon kann ein Modell über ein Werkzeugergebnis erreichen.

## Was Exponential Intelligence tut

Aus der [Sicherheitsseite](https://github.com/se7enxweb/expai/blob/main/doc/security.md) der Erweiterung:

- **Inhalte sind Daten**: Die Anweisungen und jede Werkzeugbeschreibung sagen dem Modell, dass Feldwerte von Menschen geschriebene
  Daten sind, keine Anweisungen.
- **Wenige, enge Werkzeuge**: Das Modell kann nur lesen, was der Redakteur lesen darf, und Entwürfe schreiben, die der Redakteur
  schreiben darf. Es kann nicht veröffentlichen, löschen, verschieben, Berechtigungen ändern oder E-Mails senden.
- **Eine Probeaufgabe genau dafür**: Ein Modell darf erst Entwürfe schreiben, nachdem es unter anderem die Pflichtaufgabe bestanden
  hat, in der ein Werkzeugergebnis eine Anweisung zum Aufruf eines Schreibwerkzeugs enthält, der das Modell nicht folgen darf
  ([Das Schreibtor und die Probe](../expai/das-schreibtor-und-die-probe.md)).
- **Eine Schreibsperre nach Webtext**: Nach einem Webabruf oder einer Websuche wird kein Entwurf geschrieben, solange dieses Ergebnis
  Teil des Gesprächsfensters ist. Text von externen Seiten wird in Markierungen "Begin/End of outside data" eingefasst
  ([Web- und Verlaufswerkzeuge](../expai/web-und-verlaufswerkzeuge.md)).
- **Saubere Ausgabe**: Rich Text, den der Assistent schreibt, wird auf eine kleine HTML-Teilmenge bereinigt (keine Skripte, Styles,
  Iframes, Bilder oder Einbettungen; nur http(s)- und mailto-Links) und über den Eingabeparser des Kernels gespeichert.
- **Ein Mensch entscheidet**: Jedes Ergebnis ist ein gekennzeichneter Entwurf, der auf Prüfung wartet.

Der Rat der Erweiterung: Prüfen Sie Entwürfe, die aus nicht vertrauenswürdigen Inhalten geschrieben wurden (zum Beispiel aus von
Benutzern eingereichtem Text), wie den Entwurf jedes Beitragenden, und prüfen Sie Links in Entwürfen aus Webergebnissen vor der
Veröffentlichung.

## Was die Schnittstellen für Agenten tun

- **Sie handeln als ein Konto.** Ein irregeleiteter Agent kann nur tun, was sein Konto darf; ein Schlüssel nur, was seine
  Geltungsbereiche erlauben ([Rollen und minimale Rechte](../steuerung/rollen-und-minimale-rechte.md)).
- **expservices antwortet in einer Form** und lehnt ohne Anmeldung (401) oder ohne die Richtlinie (403) ab, sodass der Code eines
  Agenten bei einer Ablehnung anhalten kann, statt sie zu umgehen.
- **Schreibzugriffe werden festgehalten**: Jeder Schreibzugriff über expservices und jede Inhaltsänderung erscheint im Audit-Trail
  unter dem Konto des Agenten.

Sie kennzeichnen Inhalte nicht für Sie als Daten: Das ist Aufgabe der Anweisungen und des Codes Ihres Agenten.

## Was Sie in Ihrem eigenen Agenten tun müssen

1. **Lesen vom Handeln trennen.** Laufen Sie zuerst nur lesend; geben Sie Schreibzugriff nur einem Ablauf, dem Sie vertrauen, mit
   eigenem Konto.
2. **Die Werkzeuge eingrenzen.** Bieten Sie ein Werkzeug je erlaubter Aktion an, mit geprüften Eingaben, statt eines allgemeinen
   HTTP- oder Shell-Werkzeugs ([Agenten und die REST-API](agenten-und-die-rest-api.md#die-routen-einem-modell-beschreiben)).
3. **Werkzeugergebnisse als Daten kennzeichnen**, in Ihren Anweisungen und Werkzeugbeschreibungen, so wie der Assistent es tut.
4. **Nach nicht vertrauenswürdigen Eingaben mit dem Schreiben aufhören**, wo Sie können: Sobald ein Ergebnis aus einer externen
   Quelle in den Kontext gelangt ist, beenden Sie den Durchgang oder verlangen Sie vor dem nächsten Schreibzugriff die Bestätigung
   eines Menschen.
5. **Einen Menschen für die Veröffentlichung beteiligt halten** ([Einen Menschen beteiligt halten](einen-menschen-beteiligt-halten.md)).
6. **Dem Agenten nie Zugangsdaten in seinem Kontext geben**, damit eine eingeschleuste Anweisung sie nicht wiederholen kann.
7. **Den Audit-Trail** nach jedem automatisierten Lauf **lesen**.

## Was keine Einstellung kann

Keine Konfiguration macht ein Modell immun gegen eingeschleusten Text. Die Abwehrmaßnahmen oben begrenzen, was ein irregeleitetes
Modell **tun kann**, weshalb die engen Berechtigungen, die Entwürfe und die menschliche Prüfung mehr zählen als jede Formulierung
in einem Prompt.

## Verwandte Seiten

- [KI-Entwürfe prüfen](../checklisten/ki-entwuerfe-pruefen.md)
- [Eingabeprüfung und Maskierung](../../ersteller/anwendungsentwickler/eingabepruefung-und-escaping.md)
- [Reaktion auf KI-Vorfälle](../steuerung/reaktion-auf-ki-vorfaelle.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
