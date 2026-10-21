---
title: Wechsel von Exponential zu einem anderen System
audience: government, project-manager, admin, developer
category: public-sector
order: 80
summary: Wie eine öffentliche Stelle ihre Inhalte von Exponential in ein anderes Content-Management-System überträgt - der Export in sieben Schritten, Rich Text, Dateien, Benutzer, Adressen und die Leitfäden je Zielsystem - und wie sie die alte und die neue Website bis zur Abnahme parallel betreibt.
keywords: Wegmigration, Systemwechsel, Exponential verlassen, Export, WordPress, Drupal, Joomla, Contentful, Website-Baukasten, Rich Text, Umleitungen, Parallelbetrieb, Außerbetriebnahme, migration away, change of system, leaving Exponential, export, site builder, rich text, redirects, parallel running, decommissioning
type: help
---
# Wechsel von Exponential zu einem anderen System

Diese Seite richtet sich an die Projektleitung einer Migration weg von Exponential, zum Beispiel nachdem eine neue
Ausschreibung ein anderes System gewählt hat. Die Dokumentation behandelt das als normalen, unterstützten Weg: „Ihre
Inhalte gehören Ihnen“ ([Von Exponential migrieren](../../../../installation/migration/exponential-verlassen.md)). Diese Seite
bringt die dokumentierten Schritte in einen Projektplan für eine öffentliche Stelle.

## Inhalt
1. [Das Projekt planen](#das-projekt-planen)
2. [Der dokumentierte Export in sieben Schritten](#der-dokumentierte-export-in-sieben-schritten)
3. [Inhalte, die umgewandelt werden müssen](#inhalte-die-umgewandelt-werden-müssen)
4. [Leitfäden je Zielsystem](#leitfäden-je-zielsystem)
5. [Beide Websites parallel betreiben](#beide-websites-parallel-betreiben)
6. [Die alte Website außer Betrieb nehmen](#die-alte-website-außer-betrieb-nehmen)

## Das Projekt planen

| Entscheidung | Fragen |
|---|---|
| Umfang | alle Inhalte oder nur aktuelle? Was geht stattdessen in ein Archiv? |
| Abbildung des Inhaltsmodells | welche Klasse wird zu welchem Inhaltstyp des Ziels? ([Abbildung des Inhaltsmodells](../../../../installation/migration/inhaltsmodell-zuordnen.md)) |
| Stopp oder Delta | Bearbeitung während des Umzugs stoppen oder zweimal exportieren und die Änderungen übertragen? |
| Benutzer | welche Konten umziehen; wie sie auf dem Ziel Passwörter erhalten |
| Adressen | jede öffentliche Adresse umgeleitet ([URL-Umleitungen nach dem Ausstieg](url-umleitungen-nach-dem-ausstieg.md)) |
| Schriftgut | was Sie nach den Regeln zum Schriftgut aus dem alten System aufbewahren müssen |

## Der dokumentierte Export in sieben Schritten

Aus [Exponential verlassen, Abschnitt 2](../../../../installation/migration/exponential-verlassen.md#2-der-vollständige-export-schritt-für-schritt):

1. **Stoppen** Sie die Bearbeitung, oder notieren Sie den Zeitpunkt für einen späteren Delta-Export.
2. **Website-Archiv** mit xrowextract, jede Klasse, im Format, das das Ziel am besten liest (CSV für die meisten
   Importprogramme, JSON für Skripte).
3. **Speicher**: `var/<site>/storage/` kopieren (in einem Datenbank-Cluster zuerst `exp:clusterize -u`).
4. **Datenbank-Dump**, als Archiv, auf das Sie zurückgreifen können.
5. **Layouts**, wenn das Ziel sie verwenden kann.
6. **Adressliste** für Umleitungen.
7. **Prüfen** Sie das Archiv: Öffnen Sie das Manifest, vergleichen Sie die Zeilen je Klasse mit den Zahlen der
   Administration, öffnen Sie eine Stichprobe von Dateien.

## Inhalte, die umgewandelt werden müssen

- **Rich Text** (`ezxmltext`) wird im XML von Exponential gespeichert. Rendern Sie ihn entweder zu HTML (die Templates
  der Website oder REST v2 mit `OutputFormat=xhtml`) oder wandeln Sie das XML in einem Skript um; eingebettete Bilder
  werden zu Bild-Tags oder Links auf die exportierten Dateien ([Abschnitt 3](../../../../installation/migration/exponential-verlassen.md#3-rich-text)).
- **Beziehungen und Einbettungen**: auf dem Ziel über die Remote-ID oder Objekt-ID zugeordnet.
- **Übersetzungen**: Das Archiv hat eine Zeile je Objekt und Sprache.
- **Formulare**: Eingaben getrennt mit `ext:bccie:export` exportieren.

## Leitfäden je Zielsystem

Die Übersicht zur Migration hat einen Abschnitt „von Exponential zu …“ für mehrere Ziele:

| Ziel | Leitfaden |
|---|---|
| WordPress | [WordPress und Exponential, Abschnitt 5](../../../../installation/migration/wordpress.md#5-von-exponential-zu-wordpress) |
| Drupal | [Drupal](../../../../installation/migration/drupal.md) |
| Joomla | [Joomla](../../../../installation/migration/joomla.md) |
| Contentful | [Contentful](../../../../installation/migration/contentful.md) |
| Website-Baukästen | [Website-Baukästen](../../../../installation/migration/website-baukaesten.md) |
| Statisches HTML-Archiv | [Statisches HTML](../../../../installation/migration/statisches-html.md) |
| Andere Mitglieder der Familie | [Platform Legacy](../../../../installation/migration/exponential-platform-legacy.md), [Nexus](../../../../installation/migration/exponential-platform-nexus.md), [DXP](../../../../installation/migration/exponential-platform-dxp.md) |

Für ein nicht aufgeführtes Ziel sind CSV und Manifest des Website-Archivs der allgemeine Ausgangspunkt.

## Beide Websites parallel betreiben

- Halten Sie die alte Website **schreibgeschützt**, bis die neue abgenommen ist; so steht es in der Checkliste der
  Dokumentation.
- Wartungsmodus oder eingeschränkte Rollen können die Bearbeitung auf der alten Website stoppen
  ([Wartungsmodus](../../../../anleitungen/wartungsmodus.md), [Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md)).
- Vergleichen Sie Stichproben von Seiten auf beiden Websites, bevor Sie das DNS umstellen.
- Installieren Sie die Umleitungstabelle vor der Umstellung auf dem neuen System.

## Die alte Website außer Betrieb nehmen

- [ ] Letzter Export und Datenbank-Dump nach Ihren Regeln zum Schriftgut archiviert.
- [ ] Ein statisches Archiv der alten öffentlichen Website aufbewahrt, wenn Ihre Regeln eines verlangen:
      `exp:makestaticcache` schreibt statische Seiten, wie der Migrationskurs erklärt
      ([Exponential verlassen, Lektion](../../../trainer/migration/modul-08-wechsel-weg-und-abschlussprojekt/von-exponential-wechseln.md)).
- [ ] Audit-Protokoll mit signiertem Manifest exportiert und für seine Aufbewahrungsdauer aufbewahrt.
- [ ] Personenbezogene Daten wie vereinbart behandelt ([Personenbezogene Daten beim Ausstieg](personenbezogene-daten-beim-ausstieg.md)).
- [ ] Server, Cronjobs, Zertifikate und Konten entfernt; Löschung schriftlich bestätigt.

## Verwandte Seiten
- [Ausstiegspläne: Übersicht](README.md)
- [Von Exponential migrieren](../../../../installation/migration/exponential-verlassen.md)
- [Checkliste zur Migration](../../../../installation/migration/checkliste.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
