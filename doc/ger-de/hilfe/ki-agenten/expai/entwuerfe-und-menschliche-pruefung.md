---
title: Entwürfe und menschliche Prüfung
audience: ai-agents, editor, admin
category: ai-agents
order: 22
summary: Warum Exponential Intelligence nie selbst veröffentlicht - jedes Ergebnis ist ein Entwurf des Redakteurs, "Send for approval" führt die normale Veröffentlichungsoperation aus, sodass Freigabe-Workflows die Version anhalten, und jede von ihm geschriebene Version trägt die Kennzeichnung Exponential Intelligence - sowie wie Sie einen Freigabeschritt für KI-geschriebene Inhalte einrichten.
keywords: nur Entwürfe, menschliche Prüfung, Mensch in der Schleife, Send for approval, Save as drafts, Freigabe-Workflow, ezapprove, ausstehend, Kennzeichnung, Kennzeichnung Exponential Intelligence, Meine Entwürfe, veröffentlichen, drafts only, human review, human in the loop, approval workflow, pending, label, My drafts, publish
type: help
---
# Entwürfe und menschliche Prüfung

Diese Seite richtet sich an Redakteure, die prüfen, was der Assistent schreibt, und an Administratoren, die möchten, dass ein Mensch
KI-geschriebene Inhalte freigibt, bevor sie öffentlich werden. Sie erklärt die eine Regel, die der Assistent nie bricht: Er schreibt
Entwürfe, und ein Mensch entscheidet, was veröffentlicht wird.

## Inhalt

1. [Alles ist ein Entwurf](#alles-ist-ein-entwurf)
2. [Save as drafts oder Send for approval](#save-as-drafts-oder-send-for-approval)
3. [Vorher wissen, was passieren wird](#vorher-wissen-was-passieren-wird)
4. [Der Freigabeweg](#der-freigabeweg)
5. [Die Kennzeichnung KI-geschriebener Versionen](#die-kennzeichnung-ki-geschriebener-versionen)
6. [Einen Freigabeschritt für KI-Inhalte einrichten](#einen-freigabeschritt-für-ki-inhalte-einrichten)

## Alles ist ein Entwurf

Alles, was der Assistent schreibt, ist ein gewöhnlicher Entwurf des Redakteurs:

- **ein neues Objekt**: Version 1 mit Status Entwurf, Eigentum des Redakteurs, unter dem gewählten Elternknoten platziert, in der
  Sektion des Elternknotens;
- **eine Änderung**: eine neue Version mit Status Entwurf; die veröffentlichte Version bleibt, wie sie ist.

Diese Entwürfe stehen in **Meine Entwürfe**, öffnen sich im normalen Bearbeitungsformular und lassen sich dort verwerfen. Die
gestuften Änderungen von Vector, Ketten, Berichte zum Alternativtext und der Übersetzungsabgleich enden ebenfalls in Entwürfen. Die
Erweiterung hat keine Einstellung, die sie selbst veröffentlichen lässt
([expai-FAQ](https://github.com/se7enxweb/expai/blob/main/doc/faq.md)).

## Save as drafts oder Send for approval

| Schaltfläche | Was passiert |
|---|---|
| **Save as drafts** | Die Entwürfe bleiben Entwürfe. Sie öffnen sie später, bearbeiten und veröffentlichen sie wie jeden anderen Entwurf |
| **Send for approval** | Führt die reguläre Veröffentlichungsoperation **als Sie** aus, mit ihren Benachrichtigungen und Audit-Aufzeichnungen. Greift ein Freigabe-Workflow, wartet die Version auf den Freigebenden; greift keiner, wird sie veröffentlicht |
| Verwerfen | Entfernt den Entwurf, wie es das Verwerfen des Kernels tut |

Vor dem Senden prüft der Assistent, dass die Version noch Ihr eigener Entwurf ist, dass Sie sie bearbeiten dürfen und dass jedes
Pflichtfeld Inhalt hat.

Lesen Sie die zweite Zeile genau: "Send for approval" **veröffentlicht sofort**, wo kein Freigabe-Workflow greift. Der Name
beschreibt die vorgesehene Einrichtung, keine Garantie. Brauchen Sie einen Menschen, der jede KI-geschriebene Version freigibt,
richten Sie einen Freigabe-Workflow ein ([unten](#einen-freigabeschritt-für-ki-inhalte-einrichten)).

## Vorher wissen, was passieren wird

Jeder Entwurf sagt im Voraus, welcher von drei Fällen zutrifft, indem er die Freigabe-Events des Veröffentlichungsauslösers so
auswertet, wie es der Kernel tut (Sektion, ausgeschlossene Gruppen, Freigebende, Sprache, Versionsoption):

| Angezeigt als | Bedeutung |
|---|---|
| Waits for approval when sent | Ein Freigabe-Event greift |
| May wait for approval when sent | Ein Freigabe-Event steht hinter einem Multiplexer, dessen Bedingungen nicht ausgewertet werden |
| Published at once when sent | Keine Freigabe greift (zum Beispiel, weil Sie selbst Freigebender sind) |

## Der Freigabeweg

1. Der Redakteur sendet den Entwurf. Das Freigabe-Event legt ein Kollaborationselement für die Freigebenden an; die Version ist
   **ausstehend**, und Panel und Dashboard zeigen "Waiting for approval".
2. Der Freigebende öffnet das Element. Die Seite zeigt einen Hinweis, dass die Version mit Exponential Intelligence geschrieben
   wurde, und das Modell.
3. **Freigeben**: Der Workflow-Cronjob setzt den Workflow fort und veröffentlicht die Version. **Ablehnen**: Die Version wird wieder
   zum Entwurf des Redakteurs, um sie zu bearbeiten und erneut zu senden.

Dieser Weg wurde in einer Kopie einer Live-Installation mit echten Modellaufrufen von Anfang bis Ende durchlaufen, wie die
[Freigabeseite](https://github.com/se7enxweb/expai/blob/main/doc/approval-workflow.md) der Erweiterung festhält.

## Die Kennzeichnung KI-geschriebener Versionen

Jede vom Assistenten geschriebene Version trägt die Kennzeichnung **Exponential Intelligence**: in Meine Entwürfe, auf der
Entwurfsseite, in Versionsansichten, Bearbeitungsformularen und Freigabeelementen. Sie stammt aus dem eigenen Entwurfsprotokoll des
Assistenten und markiert daher die Version, nicht das Objekt: Eine spätere, von Hand geschriebene Version hat keine Kennzeichnung.

## Einen Freigabeschritt für KI-Inhalte einrichten

Die Freigabe ist das eigene Workflow-Event des Kernels, keine Funktion des Assistenten. Eine typische Einrichtung:

1. Legen Sie einen Workflow mit dem Event **Approve** an und wählen Sie die Sektionen, die er abdeckt, und die Freigebenden
   ([Workflows, durchgearbeitetes Beispiel](../../../anleitungen/workflows.md#3-durchgerechnetes-beispiel-freigabe-vor-dem-veröffentlichen)).
2. Verbinden Sie ihn mit dem Auslöser **content / publish / before**.
3. Schließen Sie keine Redakteursgruppe aus, die den Assistenten verwendet.
4. Stellen Sie sicher, dass der Cronjob läuft, der Workflows fortsetzt, damit freigegebene Versionen veröffentlicht werden.
5. Testen Sie es mit einem Redakteurskonto: Schreiben Sie mit dem Assistenten einen Entwurf, prüfen Sie, dass er "Waits for
   approval when sent" anzeigt, senden Sie ihn, geben Sie ihn frei.

Weil das Event das des Kernels ist, gilt es auch für von Hand geschriebene Inhalte in denselben Sektionen. Möchten Sie eine Freigabe
nur für KI-geschriebene Inhalte, verwenden Sie für diese Arbeit eine Sektion oder eine Gruppe von Redakteuren; der Assistent fügt
keinen eigenen Freigabeweg hinzu.

## Verwandte Seiten

- [Was Exponential Intelligence tut](was-exponential-intelligence-tut.md)
- [KI-Entwürfe prüfen](../checklisten/ki-entwuerfe-pruefen.md)
- [Meine Entwürfe und meine ausstehenden Elemente](../../../anleitungen/entwuerfe-und-ausstehende-objekte.md)
- [Veröffentlichen, Versionen und der Papierkorb](../../veroeffentlichen-und-versionen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
