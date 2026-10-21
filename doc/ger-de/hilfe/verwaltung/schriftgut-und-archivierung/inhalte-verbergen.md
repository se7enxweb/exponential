---
title: Inhalte verstecken
audience: government, editor, admin
category: public-sector
order: 90
summary: Für Redaktionen und Administratoren - was das Verstecken eines Knotens in Exponential bewirkt (Besucher sehen ihn und alles darunter nicht mehr, er bleibt im Baum und kann wieder angezeigt werden), wie die Administration versteckte Einträge zeigt, zeitgesteuertes Verstecken und wann Verstecken das richtige Werkzeug für Schriftgut ist.
keywords: verstecken, verbergen, aufdecken, wieder anzeigen, versteckt, versteckt durch übergeordneten Eintrag, Sichtbarkeit, ShowHiddenNodes, hide.php, HideSettings, is_hidden, is_invisible, content.node.hide, Suchindex, Schriftgut, zurücknehmen, öffentlicher Sektor, hide, reveal, hidden, hidden by a parent, visibility, search index, records, withdraw, public sector
type: help
---
# Inhalte verstecken

Diese Seite richtet sich an Redaktionen und Administratoren. Das Verstecken nimmt einen Eintrag von der öffentlichen
Website, ohne ihn zu entfernen. Es ist oft das richtige Werkzeug, wenn eine Bekanntmachung zurückgenommen, aber
aufbewahrt werden muss, oder wenn eine Seite bis zu ihrer Prüfung verschwinden muss.

## Inhalt

1. [Was Verstecken bewirkt](#was-verstecken-bewirkt)
2. [Wie Sie verstecken und wieder anzeigen](#wie-sie-verstecken-und-wieder-anzeigen)
3. [Sehen, was versteckt ist](#sehen-was-versteckt-ist)
4. [Zeitgesteuertes Verstecken](#zeitgesteuertes-verstecken)
5. [Verstecken und Schriftgut](#verstecken-und-schriftgut)
6. [Verwandte Seiten](#verwandte-seiten)

## Was Verstecken bewirkt

- Der Knoten und alles darunter werden für die öffentlichen Siteaccesses unsichtbar (`ShowHiddenNodes=false` dort).
- Der Eintrag bleibt im Baum, mit seinen Versionen, Relationen und seinem URL-Alias.
- Der Suchindex wird aktualisiert, sodass Besucher den versteckten Eintrag nicht finden.
- Das Wiederanzeigen bringt ihn so zurück, wie er war.
- Verstecken und Wiederanzeigen werden im Audit als `content.node.hide` und `content.node.reveal` aufgezeichnet
  (standardmäßig eingeschaltet).

## Wie Sie verstecken und wieder anzeigen

In der Administration, beim Eintrag: die Aktion **Verstecken** (oder **Verstecken / Zeigen** im Kontextmenü) und
**Aufdecken**, um es rückgängig zu machen. Sie brauchen in Ihrer Rolle das Recht, Inhalte zu verstecken.

## Sehen, was versteckt ist

admin4 kennzeichnet die Sichtbarkeit auf einen Blick
([Das Design admin4: Sichtbarkeit und Kontostatus](../../../funktionen/6.0/admin4-design.md#sichtbarkeit-und-kontostatus)):

| Kennzeichen | Bedeutung |
|---|---|
| gefüllter Punkt | sichtbar |
| Ring | versteckt |
| Ring mit Balken | versteckt, weil ein Eintrag darüber versteckt ist |

Der Kopf der Knotenansicht zeigt ein Kennzeichen (Sichtbar, Versteckt, Versteckt durch übergeordneten Eintrag); die
Tabelle der Unterelemente zeigt das Kennzeichen vor jedem Namen; der Inhaltsbaum kennzeichnet versteckte Knoten.

## Zeitgesteuertes Verstecken

Das Cronjob-Skript `hide.php` versteckt Objekte ausgewählter Klassen, wenn ein Datumsattribut verstrichen ist, sobald
`content.ini [HideSettings] RootNodeList[]` und `HideDateAttributeList[<class>]=<attribute>` gesetzt sind. Siehe „Alte
Bekanntmachungen archivieren“.

## Verstecken und Schriftgut

- Verstecken lässt das Schriftstück unversehrt; Entfernen nicht.
- Ein versteckter Eintrag ist für Mitarbeitende in der Administration weiterhin erreichbar, ebenso für alle mit einer
  Rolle, die versteckte Knoten anzeigt; Verstecken ist keine Zugriffskontrolle für vertrauliche Inhalte. Verwenden Sie
  dafür Sektionen oder Zustände mit Leserichtlinien.
- Halten Sie den Grund für das Verstecken fest (ein Feld in der Klasse oder Ihr Schriftgutsystem), denn das Audit zeigt,
  wer und wann, nicht warum.

## Verwandte Seiten

- [Alte Bekanntmachungen archivieren](alte-bekanntmachungen-archivieren.md)
- [Das Design admin4](../../../funktionen/6.0/admin4-design.md)
- [Inhalte bearbeiten](../../inhalte-bearbeiten.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
