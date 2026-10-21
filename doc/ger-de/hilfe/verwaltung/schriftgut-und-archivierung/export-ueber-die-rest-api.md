---
title: Export über die REST-API
audience: government, developer, admin
category: public-sector
order: 150
summary: Für Entwickler, die Inhalte in ein Schriftgutsystem, ein Archiv oder ein Portal für offene Daten übertragen - die Leserouten der REST-API von Exponential (v1 und v2), Authentifizierung mit OAuth2-Token oder persönlichen API-Schlüsseln, Rechteprüfungen und ein Muster für einen wiederholbaren Export.
keywords: REST-API, Export, JSON, v1, v2, content/node, content/object, Felder, Unterknoten, OAuth2, API-Schlüssel, Rechte, Schriftgutsystem, Archiv, Integration, ezprestapi, öffentlicher Sektor, REST API, export, fields, children, API keys, permissions, records system, archive, integration, public sector
type: help
---
# Export über die REST-API

Diese Seite richtet sich an Entwickler, die einen wiederholbaren Export aus einer Exponential-Website in ein
Schriftgutsystem, ein Archiv oder eine andere Plattform bauen. Die REST-API antwortet unter `/api/ezp/<version>` mit
JSON; jede Inhaltsroute braucht standardmäßig einen Benutzer und prüft dessen Rechte
([REST-API v1 und v2](../../../anleitungen/rest-api-v1-und-v2.md)).

## Inhalt

1. [Leserouten](#leserouten)
2. [Authentifizierung](#authentifizierung)
3. [Ein wiederholbarer Export](#ein-wiederholbarer-export)
4. [Grenzen](#grenzen)
5. [Verwandte Seiten](#verwandte-seiten)

## Leserouten

Relativ zu `/api/ezp/<version>` (v1 und v2):

| Route | Liefert |
|---|---|
| `GET /content/node/<id>` | einen Knoten (`ResponseGroups=Metadata,Locations,Fields`, `OutputFormat=xhtml` für gerenderten Text) |
| `GET /content/node/<id>/list(/offset/<n>)(/limit/<n>)(/sort/<key>(/asc\|desc))` | die Unterknoten, seitenweise |
| `GET /content/node/<id>/childrenCount` | die Anzahl der Unterknoten |
| `GET /content/node/<id>/fields`, `/field/<identifier>` | die Felder des Objekts |
| `GET /content/object/<id>`, `.../fields`, `.../field/<identifier>` | dasselbe über die Objekt-ID |

Schreibzugriffe (Anlegen, Löschen) gibt es nur in v2; ein Export braucht keine.

## Authentifizierung

`rest.ini [Authentication] RequireAuthentication=enabled` ist die Voreinstellung. Verwenden Sie OAuth2-Token oder
einen **persönlichen API-Schlüssel** eines eigenen Export-Benutzers ([API-Schlüssel](../../../anleitungen/api-schluessel.md)).
Geben Sie diesem Benutzer eine Rolle mit Lesezugriff, beschränkt auf das, was der Export braucht (Sektionen,
Teilbäume); die API fragt die Richtlinien des Benutzers so ab wie das Modul content. Antworten: `401` ohne gültiges
Token oder gültigen Schlüssel, `403`, wenn der Benutzer es nicht darf.

## Ein wiederholbarer Export

1. Wählen Sie den oder die Wurzelknoten dessen, was exportiert werden soll (zum Beispiel den Ordner der
   Bekanntmachungen).
2. Durchlaufen Sie den Baum mit `/list` seitenweise (`/offset/<n>/limit/<n>`) und rufen Sie für jedes Objekt `/fields`
   ab.
3. Speichern Sie das JSON jedes Objekts zusammen mit Knoten-ID, Objekt-ID, Änderungsdatum und Exportdatum.
4. Dateien aus Datei- und Bildfeldern: Prüfen Sie, was das JSON des Feldes in Ihrer Version dafür liefert; wo es keine
   abrufbare Adresse liefert, kopieren Sie die Dateien bei einem serverseitigen Export aus `var/storage`.
5. Vergleichen Sie beim nächsten Lauf die Änderungsdaten und exportieren Sie nur, was sich geändert hat.
6. Protokollieren Sie jeden Lauf; bewahren Sie den Schlüssel des Export-Benutzers in Ihrem Geheimnisspeicher auf.

## Grenzen

- Die API liefert die aktuell veröffentlichte Version; frühere Versionen werden über diese Routen nicht angeboten
  (verwenden Sie für die Geschichte die Versionsseite oder einen Datenbankexport).
- `listAtom` antwortet mit 500 (ein bekanntes Problem im Leitfaden).
- Große Exporte belasten die Website: Führen Sie sie außerhalb der Spitzenzeiten aus und arbeiten Sie mit moderaten
  Seitengrößen.

## Verwandte Seiten

- [REST-API v1 und v2](../../../anleitungen/rest-api-v1-und-v2.md)
- [API-Schlüssel](../../../anleitungen/api-schluessel.md)
- [Entfernte Dienste und Apps](../../../anleitungen/entfernte-dienste-und-apps.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
