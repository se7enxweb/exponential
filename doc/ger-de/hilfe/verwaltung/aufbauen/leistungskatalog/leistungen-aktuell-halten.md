---
title: Leistungen aktuell halten
audience: government, website-creator, project-manager, admin
category: public-sector
order: 80
summary: Wie Sie Leistungsseiten in Exponential über die Zeit korrekt halten - eine verantwortliche Person je Seite, ein verpflichtendes Überprüfungsdatum, eine Template-Liste überfälliger Leistungen, Versionen für das Geänderte und eine monatliche Routine.
keywords: Pflege von Inhalten, Überprüfungsdatum, Verantwortliche für Inhalte, Liste überfälliger Seiten, Sektionen, Eigentümer, Versionen, redaktionelle Routine, Steuerung von Inhalten, content upkeep, review date, content owner, overdue list, fetch, attribute_filter, currentdate, sections, owner, versions, editorial routine, content governance
type: help
---
# Leistungen aktuell halten

Diese Seite richtet sich an Site-Ersteller, Projektleiter und Administratoren, die einen Leistungskatalog nach dem Start
korrekt halten. Informationen zu Leistungen veralten still: Gebühren ändern sich, Dienststellen ziehen um, Regeln
ändern sich. Exponential hat keine eingebaute Erinnerung an Überprüfungen; diese Seite zeigt, wie Sie eine aus einem
Attribut für das Überprüfungsdatum, einer Template-Liste und einer Routine bauen.

## Inhalt

1. [Eine verantwortliche Person für jede Leistung](#eine-verantwortliche-person-für-jede-leistung)
2. [Das Überprüfungsdatum](#das-überprüfungsdatum)
3. [Eine Liste überfälliger Leistungen](#eine-liste-überfälliger-leistungen)
4. [Festhalten, was sich geändert hat](#festhalten-was-sich-geändert-hat)
5. [Eine monatliche Routine](#eine-monatliche-routine)

## Eine verantwortliche Person für jede Leistung

| Wer | Festgehalten als |
|---|---|
| Die Abteilung, der die Leistung gehört | die Sektion der Leistung ([Sektionen](../../../../anleitungen/sektionen.md)) |
| Die Person, die die Seite pflegt | der Eigentümer des Objekts, änderbar ([Eigentümer ändern](../../../../anleitungen/eigentuemer-aendern.md)) |
| Die Fachperson, die die Fakten bestätigt | ein Rollenname im Ablauf, nicht auf der Seite |

Scheidet jemand aus, ändern Sie den Eigentümer seiner Seiten; **Benutzer > Benutzerkonten prüfen** hilft, Konten
ausgeschiedener Personen zu finden ([Benutzerkonten prüfen](../../../../../eng-us/features/6.0/user-hygiene.md)).

## Das Überprüfungsdatum

Die Klasse Service hat ein verpflichtendes Datum **Nächste Überprüfung** ([Leistungen modellieren](leistungen-modellieren.md)).
Typische Zyklen:

| Leistung | Mindestens überprüfen |
|---|---|
| Gebühren und Entgelte | jährlich, bevor neue Sätze gelten |
| Rechtliche Anforderungen | wenn sich das Recht ändert, und jährlich |
| Kontakt, Öffnungszeiten | über die Objekte der Ansprechpartner; jede Leistung jährlich |
| Stabile Informationsleistungen | alle zwei Jahre |

## Eine Liste überfälliger Leistungen

Eine Seite in einer eingeschränkten Sektion (nur Mitarbeiter dürfen sie lesen) mit einem Template, das Leistungen
listet, deren Überprüfungsdatum vorbei ist:

```text
{def $overdue = fetch( 'content', 'tree', hash(
        'parent_node_id', ezini( 'NodeSettings', 'RootNode', 'content.ini' ),
        'class_filter_type', 'include',
        'class_filter_array', array( 'service' ),
        'attribute_filter', array( array( 'service/review_date', '<', currentdate() ) ),
        'sort_by', array( 'attribute', true(), 'service/review_date' ),
        'main_node_only', true() ) )}
<table>
  <thead><tr><th scope="col">Service</th><th scope="col">Review was due</th><th scope="col">Section</th></tr></thead>
  <tbody>
  {foreach $overdue as $s}
    <tr><td><a href={$s.url_alias|ezurl}>{$s.name|wash}</a></td>
        <td>{$s.data_map.review_date.content.timestamp|l10n( 'shortdate' )}</td>
        <td>{$s.object.section_id}</td></tr>
  {/foreach}
  </tbody>
</table>
{undef $overdue}
```

Speichern Sie diese Seite nicht lange im Cache. Die Tabelle der Unterelemente in der Administration kann ebenfalls
Spalten zeigen und als CSV exportieren, was für eine einmalige Prüfung genügt
([Inhaltsmodell und Bearbeitung, 5](../../../../anleitungen/inhaltsmodell-und-bearbeitung.md#5-die-tabelle-der-unterelemente)).

## Festhalten, was sich geändert hat

| Bedarf | Exponential |
|---|---|
| Was die Seite vorher sagte | **Versionen verwalten**, **Vergleichen** ([Versionen eines Objekts](../../../../anleitungen/inhaltsverlauf.md)) |
| Wer sie geändert hat | Versionen und das Audit-Protokoll ([Audit-Protokoll](../../../../../eng-us/features/6.0/audit-trail.md)) |
| Menschen informieren, die der Leistung folgen | Benachrichtigungen beim Veröffentlichen; **Ohne Benachrichtigung veröffentlichen** für kleine Korrekturen, wo eingeschaltet ([Ohne Benachrichtigung veröffentlichen](../../../../../eng-us/features/6.0/publish-without-notification.md)) |

## Eine monatliche Routine

- [ ] Öffnen Sie die Liste überfälliger Seiten; senden Sie jeder verantwortlichen Person ihre Leistungen.
- [ ] Prüfen Sie **Setup > URL-Verwaltung** auf defekte Links auf Leistungsseiten.
- [ ] Prüfen Sie **Setup > Suchstatistiken** auf Suchbegriffe ohne Treffer (falls eingeschaltet).
- [ ] Lesen Sie neue Rückmeldungen unter **Setup > Gesammelte Informationen**; handeln Sie, dann entfernen Sie, was
  nicht mehr gebraucht wird.
- [ ] Sehen Sie unter **Meine ausstehenden Elemente** nach Freigaben, die zu lange warten.

## Verwandte Seiten

- [Einen Leistungskatalog aufbauen](README.md)
- [Messen und verbessern](messen-und-verbessern.md)
- [Ihre Website pflegen](../../../../../eng-us/help/creators/website-creators/maintaining-your-website.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
