---
title: Messen und verbessern
audience: government, project-manager, website-creator, admin
category: public-sector
order: 90
summary: Was Exponential Ihnen darüber sagen kann, wie Leistungsseiten genutzt werden - Suchstatistik, Aufrufzahlen, ein Formular „War diese Seite hilfreich?“, Anfrage-Logs - was einen Analysedienst braucht und wie Sie mit Rücksicht auf den Datenschutz der Besucher messen.
keywords: Messung, Analyse, Suchstatistik, Aufrufzahlen, Rückmeldeformular, War diese Seite hilfreich, Anfrage-Logs, Datenschutz, Einwilligung, kontinuierliche Verbesserung, measurement, analytics, search statistics, view counts, feedback form, was this page useful, request logs, privacy, consent, continuous improvement
type: help
---
# Messen und verbessern

Diese Seite richtet sich an Projektleiter, Site-Ersteller und Administratoren, die wissen möchten, ob der
Leistungskatalog funktioniert und wie er sich verbessern lässt. Sie listet, was Exponential selbst aufzeichnet, was
einen eigenen Analysedienst braucht und wie Sie messen, ohne mehr über Besucher zu erheben, als Sie brauchen.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Was Exponential aufzeichnet](#was-exponential-aufzeichnet)
2. [Ein Formular „War diese Seite hilfreich?“](#ein-formular-war-diese-seite-hilfreich)
3. [Analysedienste](#analysedienste)
4. [Aus Befunden Änderungen machen](#aus-befunden-änderungen-machen)

## Was Exponential aufzeichnet

Aus [Statistik und Analyse](../../../../../eng-us/help/creators/website-creators/statistics-and-analytics.md):

| Quelle | Was sie beantwortet | Wo |
|---|---|---|
| Suchstatistik (voreingestellt aus) | wonach Menschen suchen, wie oft, mit wie vielen Ergebnissen | **Setup > Suchstatistiken**, mit `LogSearchStats=enabled` |
| Aufrufzahlen | welche Seiten am meisten aufgerufen werden, aus dem Zugriffsprotokoll | das Cronjob-Skript `updateviewcount`, `fetch( 'content', 'view_top_list', ... )` |
| Anfrage-Logs | Anfragen mit Zeitmessung, für den Betrieb | [Anfrage-Logs](../../../../../eng-us/features/6.0/request-logs.md) |
| Gesammelte Informationen | was Menschen Ihnen in Formularen sagen | **Setup > Gesammelte Informationen** |

Diese beantworten betriebliche Fragen. Sie ersetzen keinen Analysedienst.

## Ein Formular „War diese Seite hilfreich?“

Ein kleines Formular mit dem Information Collector unter jeder Leistungsseite gibt direkte Rückmeldung:

| Attribut | Datentyp | Sammelnd |
|---|---|---|
| War diese Seite hilfreich? | Auswahlliste (`ezselection`): ja, nein | ja |
| Was hat gefehlt? | Textabschnitt (`eztext`), optional | ja |
| Seite | Textzeile, vom Template mit der Adresse der Seite in einem versteckten Feld gefüllt | ja |

Ein Formularobjekt, vom Template auf jeder Leistungsseite gezeigt, hält alle Antworten in einer Liste. Exportieren Sie
sie als CSV unter **Setup > Gesammelte Informationen**
([Gesammelte Informationen](../../../../anleitungen/gesammelte-informationen.md)). Bitten Sie die Menschen, keine persönlichen
Angaben in das Textfeld einzugeben, und entfernen Sie Antworten, sobald sie gelesen sind.

## Analysedienste

| Möglichkeit | Hinweis |
|---|---|
| `bcwebsitestatistics` | fügt das Skript von Google Analytics hinzu ([bcwebsitestatistics](../../../../../eng-us/features/6.0/extensions/bcwebsitestatistics.md)); ein Drittanbieter |
| Ein anderer Dienst, möglicherweise selbst gehostet | sein Skript über die Skriptliste Ihres Designs ([Stylesheets und Skripte](../../../../../eng-us/help/creators/website-creators/stylesheets-and-scripts.md)) |
| Keiner | Suchstatistik, Aufrufzahlen und Rückmeldungen können für eine kleine Site genügen |

Analysen brauchen in vielen Rechtsordnungen meist eine Einwilligung; Exponential liefert kein Cookie-Banner mit
([Standards zum Datenschutz](../../../../../eng-us/standards/privacy.md)). In der EU gelten die
[ePrivacy-Richtlinie](https://eur-lex.europa.eu/eli/dir/2002/58/oj) und die
[DSGVO](https://eur-lex.europa.eu/eli/reg/2016/679/oj). Eine Content Security Policy muss den Host des Analysedienstes
erlauben, wenn Sie einen verwenden
([Content Security Policy](../../../../../eng-us/features/6.0/content-security-policy.md)).

## Aus Befunden Änderungen machen

| Befund | Änderung |
|---|---|
| Ein Suchbegriff ohne Treffer | ergänzen Sie das Wort in einer Zusammenfassung oder als Synonym eines Schlagworts; ergänzen Sie eine fehlende Leistung |
| Viele Antworten „nicht hilfreich“ auf einer Seite | schreiben Sie sie mit den Rückmeldungen neu; testen Sie mit einigen Nutzern |
| Eine Leistungsseite wird selten aufgerufen, aber am Schalter oft nachgefragt | verbessern Sie ihren Titel und ihren Platz in den Menüs |
| Viele Aufrufe einer Seite mit einer Telefonnummer | die Online-Alternative ist vielleicht unklar |

Werten Sie die Befunde monatlich zusammen mit der Liste überfälliger Seiten aus
([Leistungen aktuell halten](leistungen-aktuell-halten.md)).

## Verwandte Seiten

- [Einen Leistungskatalog aufbauen](README.md)
- [Leistungen finden](leistungen-finden.md)
- [Statistik und Analyse](../../../../../eng-us/help/creators/website-creators/statistics-and-analytics.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
