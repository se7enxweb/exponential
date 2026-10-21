---
title: Exponential-Begriffe für die Verwaltung
audience: government, project-manager, admin, editor
category: public-sector
order: 70
summary: Die Exponential-Begriffe, denen eine öffentliche Stelle am häufigsten begegnet, erklärt danach, was sie für eine Abteilung, ein Website-Team oder eine Beschaffung bedeuten - Siteaccess, Sektion, Rolle, Objektzustand, Workflow, Paket, Erweiterung, Velocity, Prüfprotokoll und weitere - mit Links zu den Seiten, die sie erklären.
keywords: Exponential-Begriffe, Siteaccess, Sektion, Rolle, Richtlinie, Objektzustand, Workflow, Inhaltsklasse, Knoten, Paket, Erweiterung, Velocity, Prüfprotokoll, Editor-Siteaccess, Multisite, öffentlicher Sektor, Exponential terms, section, role, policy, object state, content class, node, package, extension, audit trail, Editor siteaccess, multisite, public sector
type: reference
---
# Exponential-Begriffe für die Verwaltung

Diese Seite richtet sich an Führungskräfte und Beschaffungsverantwortliche in einer öffentlichen Stelle, die von ihrem
Website-Team oder einem Dienstleister Exponential-Begriffe hören. Jeder Begriff wird danach erklärt, was er für Ihre
Organisation bedeutet, mit der Seite, die ihn vermittelt. Sie wiederholt nicht das
[Exponential-Glossar](../../../../eng-us/glossary.md), das jeden Begriff in einer Zeile aufführt.

## Inhalt

1. [Wie Inhalte organisiert sind](#wie-inhalte-organisiert-sind)
2. [Wer was darf](#wer-was-darf)
3. [Mehrere Websites und Abteilungen](#mehrere-websites-und-abteilungen)
4. [Das System bauen und ändern](#das-system-bauen-und-ändern)
5. [Es betreiben](#es-betreiben)

## Wie Inhalte organisiert sind

| Begriff | Was er für Sie bedeutet |
|---|---|
| **Inhaltsklasse** (content class) | Ihre Inhaltsarten (Nachricht, Leistungsseite, Dokument, Beteiligungsverfahren) sind als Klassen mit Feldern definiert. Sie festzulegen ist der erste Gestaltungsschritt ([Ihr Inhaltsmodell entwerfen](../../../../eng-us/help/creators/website-creators/designing-your-content-model.md)) |
| **Inhaltsobjekt** (content object) und **Version** | Jede Seite ist ein Objekt mit einer Geschichte von Versionen; ältere Versionen lassen sich vergleichen und wiederherstellen ([Versionen eines Objekts](../../../anleitungen/inhaltsverlauf.md)) |
| **Knoten** (node) oder **Ort** (location) | Wo ein Objekt im Inhaltsbaum erscheint; ein Objekt kann an mehreren Stellen erscheinen, zum Beispiel eine Bekanntmachung unter zwei Abteilungen ([Inhaltsmodell](../../../anleitungen/inhaltsmodell-und-bearbeitung.md)) |
| **Übersetzung** (translation) | Jedes Objekt kann eine Fassung pro Sprache haben ([Inhaltssprachen](../../../anleitungen/inhaltssprachen.md)) |
| **Tags** | Schlagwörter aus einem verwalteten Schlagwortbaum, mit der Erweiterung eztags ([Verschlagworten mit eztags](../../../../eng-us/help/creators/website-creators/tagging-with-eztags.md)) |

## Wer was darf

| Begriff | Was er für Sie bedeutet |
|---|---|
| **Benutzergruppe** (user group) | Konten, in Gruppen organisiert, zum Beispiel pro Abteilung |
| **Rolle** (role) und **Richtlinie** (policy) | Eine Rolle ist eine Menge von Rechten, die Gruppen zugewiesen wird; eine Richtlinie ist ein einzelnes Recht mit optionalen Beschränkungen ([Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md)) |
| **Sektion** (section) | Eine Kennzeichnung eines Teils der Inhalte, auf die Rollen beschränkt werden können; oft eine pro Abteilung ([Sektionen](../../../anleitungen/sektionen.md)) |
| **Objektzustand** (object state) | Eine eigene Stufe, etwa „Entwurf zur rechtlichen Prüfung“, von der Rechte abhängen können ([Objektzustände](../../../anleitungen/objektzustaende.md)) |
| **Workflow** | Regeln, die bei Ereignissen ablaufen, etwa das Zurückhalten einer Version zur Freigabe vor dem Veröffentlichen ([Workflows](../../../anleitungen/workflows.md)) |
| **Prüfprotokoll** (audit trail) | Die manipulationssichere Aufzeichnung, wer was getan hat, standardmäßig eingeschaltet ([Prüfprotokoll](../../../../eng-us/features/6.0/audit-trail.md)) |

## Mehrere Websites und Abteilungen

| Begriff | Was er für Sie bedeutet |
|---|---|
| **Siteaccess** | Ein Zugang zur Installation mit eigener Adresse, Sprache, eigenem Design und eigenen Einstellungen. Die öffentliche Website, die Administration und jede weitere Website sind Siteaccesses ([das Exponential-Glossar](../../../../eng-us/glossary.md#s)) |
| **Multisite** | Mehrere Websites aus einer Installation, zum Beispiel eine Hauptwebsite und Kampagnenseiten, die Benutzer und Inhalte teilen, wo Sie es wollen ([Multisite einrichten](../../../../eng-us/help/creators/website-creators/multisite-setup.md)) |
| **Editor-Siteaccess** (Editor siteaccess) | Eine einfachere Administration für Redaktionen ([Einrichtungsassistent und Editor-Siteaccess](../../../../eng-us/features/6.0/setup-wizard-and-editor-siteaccess.md)) |
| **Design** | Die Templates und Stile einer Website; im öffentlichen Design zählt Ihre Arbeit an der Barrierefreiheit am meisten ([Ein Design oder Theme wählen](../../../../eng-us/help/creators/website-creators/choosing-a-design-or-theme.md)) |

## Das System bauen und ändern

| Begriff | Was er für Sie bedeutet |
|---|---|
| **Erweiterung** (extension) | Ein Ordner, der Funktionen hinzufügt; die eigens entwickelte Arbeit Ihres Dienstleisters liegt meist in Erweiterungen, die Ihnen in einem Repository gehören sollten ([Erweiterungen](../../../anleitungen/extensions.md)) |
| **Override** | Eine Regel, die für eine Klasse, einen Knoten oder eine Sektion ein eigenes Template verwendet, sodass die Kerndateien unverändert und Aktualisierungen einfach bleiben ([Template-Overrides](../../../../eng-us/help/creators/website-creators/template-overrides.md)) |
| **Paket** (package) | Ein Bündel aus Klassen, Inhalten oder Design, das sich exportieren und installieren lässt; auch ein Weg, Inhalte zwischen Installationen zu übertragen ([Pakete](../../../anleitungen/pakete.md)) |
| **Exponential Layouts** | Ein Seitenbaukasten mit Layouts, Zonen und Blöcken ([Layout-Editor](../../layout-editor.md)) |
| **Exponential Intelligence** | Der Schreibassistent in der Administration; er schreibt Entwürfe mit den Rechten der Redakteurin oder des Redakteurs und veröffentlicht nie von sich aus ([das Exponential-Glossar](../../../../eng-us/glossary.md#e)) |

## Es betreiben

| Begriff | Was er für Sie bedeutet |
|---|---|
| **Velocity** | Der eigene PHP-Anwendungsserver von Exponential, eine Alternative zu Apache oder nginx mit PHP-FPM ([Webserver Velocity](../../../../eng-us/features/6.0/velocity-web-server.md)) |
| **Cache** | Gespeicherte Seitenteile, die die Website schnell machen; das Leeren des richtigen macht eine Änderung sichtbar ([Caches](../../../anleitungen/caches.md)) |
| **Cronjob** | Zeitgesteuerte Aufgaben auf dem Server, etwa Benachrichtigungen ([Cronjobs](../../../anleitungen/cronjobs.md)) |
| **Wartungsmodus** (maintenance mode) | Nimmt die Website während Arbeiten mit einem Hinweis vom Netz ([Wartungsmodus](../../../anleitungen/wartungsmodus.md)) |
| **Einrichtungsassistent** (setup wizard), **kickstarter**, `exp:install` | Die drei Wege zu installieren ([4. Eine Installationsmethode wählen](../../../installation/04-installationsweg-waehlen.md)) |
| **Upgrade-Prüfung** (upgrade check) | Vergleicht vor einer Aktualisierung die installierten Dateien mit dem Release ([Die Upgrade-Prüfung](../../../anleitungen/upgrade-pruefung.md)) |

## Verwandte Seiten

- [Glossar für Verwaltungen](README.md)
- [Abkürzungen für die Verwaltung](abkuerzungen.md)
- [Das Exponential-Glossar](../../../../eng-us/glossary.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
