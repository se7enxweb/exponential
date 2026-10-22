---
title: Formulare und gesammelte Informationen
audience: integrator, developer, admin
category: integration
order: 210
summary: Formularantworten, Umfragen und Lead-Formulare aus Exponential in ein CRM oder ein anderes System bringen - wie das Sammeln von Informationen funktioniert, collect.ini, der CSV-Export und bccie, neue Sammlungen in einem Cronjob aus PHP lesen, E-Mail beim Absenden und die beteiligten personenbezogenen Daten.
keywords: Informationssammlung, Informationssammler, infocollector, collect.ini, Formulare, Kontaktformular, Lead-Formular, Umfrage, Feedback, CRM, CSV-Export, bccie, eZInformationCollection, fetchCollectionsList, dataMap, Cronjob, personenbezogene Daten, DSGVO, GDPR, information collection, forms, contact form, lead form, poll, personal data
type: help
---
# Formulare und gesammelte Informationen

Diese Seite ist für Integratoren, die die Formulare einer Exponential-Website mit einem CRM, einem Ticketsystem oder
einem Marketingwerkzeug verbinden. Ein Formular in Exponential ist ein Inhaltsobjekt, dessen Klasse als
**Informationssammler** markierte Attribute hat; jedes Mal, wenn ein Besucher es absendet, wird eine **Sammlung** mit
einem Wert pro Sammlerattribut gespeichert. Die Seite der Administration ist
[Gesammelte Informationen](../../anleitungen/gesammelte-informationen.md).

## Inhalt

1. [Die Wege nach draußen](#die-wege-nach-draußen)
2. [collect.ini: was beim Absenden geschieht](#collectini-was-beim-absenden-geschieht)
3. [Neue Sammlungen in einem Cronjob lesen](#neue-sammlungen-in-einem-cronjob-lesen)
4. [Exporte](#exporte)
5. [Personenbezogene Daten](#personenbezogene-daten)
6. [Checkliste](#checkliste)

## Die Wege nach draußen

| Weg | Wann | Aufwand |
|---|---|---|
| E-Mail bei jedem Absenden (`collect.ini [EmailSettings]`) | Das CRM hat eine Adresse für eingehende E-Mails; geringes Volumen | Nur Einstellungen |
| CSV-Export pro Objekt (`infocollector/export/<object id>`) | Gelegentliche Übergabe | Keiner |
| `bccie`-Exporte nach Zeitplan (CSV oder SYLK) | Eine regelmäßige Datei für ein anderes Team | Einstellungen und Cronjob |
| Ein Cronjob Ihrer Erweiterung, der neue Sammlungen liest und die API des CRM aufruft | Leads müssen innerhalb von Minuten strukturiert ankommen | Eine Erweiterung |

Der letzte ist die robuste Integration: strukturiert, wiederholt und protokolliert. Das Parsen von E-Mails auf der
Seite des CRM bricht bei jeder Template-Änderung.

## collect.ini: was beim Absenden geschieht

`settings/collect.ini`, überschrieben in `settings/override/collect.ini.append.php`. Die meisten Einstellungen haben
einen Standardwert, eine Liste pro Sammlungstyp (`form`, `poll`, `feedback`) und ein Attribut, mit dem das Objekt
wählen kann:

| Block | Einstellungen | Bedeutung |
|---|---|---|
| `[InfoSettings]` | `Type`, `TypeList[]`, `TypeAttribute=collection_type` | Welchen Typ ein Formular hat |
| `[EmailSettings]` | `SendEmail=enabled`, `SendEmailList[poll]=disabled`, `SendEmailAttribute=collection_email` | Ob beim Absenden eine E-Mail verschickt wird |
| `[DisplaySettings]` | `Display=result`, `RedirectURL`, `RedirectURLAttribute=collection_redirect` | Was der Besucher danach sieht |
| `[CollectionSettings]` | `CollectAnonymousData`, `CollectSensitiveData`, `CollectionUserData=multiple` | Ob anonyme Besucher absenden dürfen, ob sensible Daten gespeichert werden, eine oder viele Sammlungen pro Benutzer |

## Neue Sammlungen in einem Cronjob lesen

Merken Sie sich die ID der zuletzt gesendeten Sammlung, lesen Sie alles danach, senden Sie es und speichern Sie die
neue letzte ID. Die Tabellen sind `ezinfocollection` (eine Zeile pro Absenden) und `ezinfocollection_attribute`; die
Klasse ist `eZInformationCollection` (`kernel/classes/ezinformationcollection.php`).

```php
<?php
/**
 * Sends new collections of one form object to the CRM. $lastID is kept by the caller (a row of your own table).
 */
function myIntegrationSendNewCollections( $formObjectID, $lastID, $crmClient )
{
    $db = eZDB::instance();
    $rows = $db->arrayQuery( 'SELECT id FROM ezinfocollection WHERE contentobject_id = ' . (int)$formObjectID
                           . ' AND id > ' . (int)$lastID . ' ORDER BY id', array( 'limit' => 200 ) );
    foreach ( $rows as $row )
    {
        $collection = eZInformationCollection::fetch( (int)$row['id'] );
        if ( !$collection instanceof eZInformationCollection )
            continue;
        $fields = array();
        foreach ( $collection->dataMap() as $identifier => $attribute )
        {
            $fields[$identifier] = $attribute->attribute( 'data_text' );
        }
        if ( !$crmClient->sendLead( (int)$row['id'], $fields ) )
        {
            return $lastID;               // stop; the next run retries from here
        }
        $lastID = (int)$row['id'];
    }
    return $lastID;
}
```

`dataMap()` indiziert die gesammelten Attribute nach dem Bezeichner ihres Klassenattributs. Textwerte stehen in
`data_text`, Zahlen je nach Datentyp in `data_int` oder `data_float`. Schicken Sie die Sammlungs-ID mit, damit das CRM
ein Duplikat ablehnen kann, wenn ein Lauf wiederholt wird ([Idempotente Importe](idempotente-importe-ueber-die-remote-id.md)).

Für ganze Listen sind `eZInformationCollection::fetchCollectionsList( $contentObjectID, $creatorID, $userIdentifier,
$limitArray, $sortArray )` und `fetchCollectionsCount()` die Abfragen des Kernels.

## Exporte

- **Der CSV-Export** unter **Setup > Gesammelte Informationen** lädt `infocollector/export/<object id>` herunter:
  UTF-8 mit Byte Order Mark, die Sammlungs-ID, Sende- und Änderungszeit in UTC, die Benutzer-ID des Absenders, eine
  Spalte pro Sammlerattribut; höchstens 50 000 Sammlungen. Werte, die eine Tabellenkalkulation als Formel lesen würde,
  beginnen mit einem Apostroph. Jeder Export wird im Audit als `data.export.csv` festgehalten.
- **`bccie`** (verlangt als `~1.1.18`) exportiert gesammelte Informationen als CSV oder SYLK auf Abruf oder über seine
  Cronjob-Teile ([bccie](../../funktionen/6.0/erweiterungen/bccie.md)).

## Personenbezogene Daten

Formularantworten sind meist personenbezogene Daten: Namen, E-Mail-Adressen, Nachrichten.

- Senden Sie nur die Felder, die das empfangende System braucht.
- Löschen Sie Sammlungen in Exponential, sobald sie sicher im CRM sind, wenn die Aufbewahrungsregeln des Kunden das
  erlauben (**Setup > Gesammelte Informationen**, Entfernen; festgehalten als `data.infocollection.remove`).
- Bewahren Sie exportierte Dateien dort auf, wo personenbezogene Daten hingehören, und löschen Sie sie, wenn sie nicht
  mehr gebraucht werden.
- Tragen Sie den Datenfluss in das Verarbeitungsverzeichnis des Kunden ein; das ist keine Rechtsberatung
  ([Kundendaten schützen](../partner/kundendaten-schuetzen.md)).

## Checkliste

- [ ] Welche Formularobjekte integriert werden und welche Felder wohin gehen.
- [ ] Die E-Mail beim Absenden dort ausgeschaltet, wo der Cronjob sie ersetzt, damit Leads nicht doppelt ankommen.
- [ ] Die zuletzt gesendete ID dauerhaft gespeichert; das CRM lehnt Duplikate anhand der Sammlungs-ID ab.
- [ ] Gescheiterte Sendungen stoppen den Lauf und werden wiederholt; der Lauf wird protokolliert und überwacht.
- [ ] Aufbewahrung für Sammlungen in Exponential und im CRM vereinbart.

## Verwandte Seiten

- [Gesammelte Informationen](../../anleitungen/gesammelte-informationen.md)
- [bccie](../../funktionen/6.0/erweiterungen/bccie.md)
- [Cronjobs für Integrationen](cronjobs-fuer-integrationen.md)
- [Formulare und Rückmeldungen von Mitgliedern für Vereine](../unternehmen/vereine/formulare-und-rueckmeldungen-von-mitgliedern.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
