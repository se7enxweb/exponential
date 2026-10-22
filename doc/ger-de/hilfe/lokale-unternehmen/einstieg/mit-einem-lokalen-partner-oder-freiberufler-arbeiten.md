---
title: Mit einem lokalen Partner oder Freiberufler arbeiten
audience: local-business, businesses, customer
category: local-business
order: 14
summary: Wie ein kleines Unternehmen einen Partner oder Freiberufler aus der Region für eine Exponential-Website findet und mit ihm arbeitet - was Sie vor der Beauftragung fragen, was Sie schriftlich festhalten, welche Konten und Dateien Sie selbst behalten, die Übergabe und eine Anfragevorlage zum Versenden.
keywords: Partner, Freiberufler, Agentur, Webdesigner, Beauftragung, Angebot, Vertrag, Übergabe, Konten, Passwörter, Eigentum, lokales Unternehmen, partner, freelancer, agency, web designer, hiring, quote, contract, handover, local business
type: help
---
# Mit einem lokalen Partner oder Freiberufler arbeiten

Diese Seite ist für Inhaber eines kleinen Unternehmens, die jemanden beauftragen, die Website zu bauen oder zu
betreiben. Eine gute Vereinbarung lässt Ihnen Ihre Website, Ihre Domain und Ihre Inhalte, mit jemand Verlässlichem,
den Sie anrufen, wenn etwas kaputtgeht.

## Inhalt

1. [Partner oder Freiberufler](#partner-oder-freiberufler)
2. [Fragen vor der Beauftragung](#fragen-vor-der-beauftragung)
3. [Was Sie schriftlich festhalten](#was-sie-schriftlich-festhalten)
4. [Was Sie selbst behalten](#was-sie-selbst-behalten)
5. [Die Übergabe](#die-übergabe)
6. [Eine Anfrage, die Sie verschicken können](#eine-anfrage-die-sie-verschicken-können)

## Partner oder Freiberufler

| | Freiberufler | Partnerunternehmen |
|---|---|---|
| Typische Größe | eine Person | ein Team |
| Urlaub und Krankheit | planen Sie eine Vertretung ein | meist abgedeckt |
| Erfahrung mit Exponential | fragen Sie nach Beispielen | Exponential-Partner sind im Partnerprogramm aufgeführt ([Partner: hier beginnen](../../partner/partner-hier-beginnen.md)) |
| Hosting | oft mit einem Hoster vereinbart | manchmal enthalten |

Beide können gute Arbeit leisten. Entscheidend sind Erfahrung mit Exponential, eine klare schriftliche Vereinbarung und
die Übergabe.

## Fragen vor der Beauftragung

Aus [Fragen an einen Partner](../../unternehmen/auswahl/fragen-an-einen-partner.md) die Fragen, die für ein kleines
Unternehmen am wichtigsten sind:

1. Welche Exponential-Websites haben Sie gebaut oder betreut, und darf ich eine sehen?
2. Wer aktualisiert die Software, und wie oft? Was kostet ein Update?
3. Wo liegen die Sicherungen, wie oft werden sie erstellt, und haben Sie schon einmal eine wiederhergestellt?
4. Welche Erweiterungen werden Sie verwenden (zum Beispiel `xrowmetadata`, `bcgooglesitemaps`, `ezgmaplocation`,
   `recaptcha`)? Stehen sie in der Standard-`composer.json`, oder ist es etwas Eigenes?
5. Wer verwahrt die API-Schlüssel für Karten und CAPTCHA, auf wessen Namen?
6. Wie ändere ich die Öffnungszeiten selbst? Zeigen Sie es mir.
7. Was passiert, wenn Sie nicht mehr mit mir arbeiten?

## Was Sie schriftlich festhalten

- Den Preis für den Aufbau und was **nicht** enthalten ist.
- Den monatlichen oder jährlichen Preis für Hosting, Updates und Support und die enthaltenen Stunden.
- Reaktionszeiten: wie schnell geantwortet wird, wenn die Website nicht erreichbar ist, und an welchen Tagen
  ([Vorlage für ein SLA](../../kunden/partner-und-hoster/sla-vorlage.md)).
- Dass die Inhalte, die für Sie erstellten Designdateien und die Domain dem Unternehmen gehören.
- Dass die Website freie Software unter der GNU GPL verwendet, sodass keine Lizenzgebühr weitergegeben werden kann
  ([Lizenzierung für Käufer](../../geschaeft/lizenzen-fuer-kaeufer.md)).
- Eine Übergabeliste am Ende des Projekts ([Checkliste für die Übergabe](../../kunden/partner-und-hoster/checkliste-uebergabe.md)).

Vorlagen, die Sie anpassen können: [Vorlage für einen Wartungsvertrag](../../kunden/partner-und-hoster/vorlage-wartungsvereinbarung.md),
[Vorlage für einen Supportvertrag](../../kunden/partner-und-hoster/vorlage-support-vereinbarung.md).

## Was Sie selbst behalten

| Behalten | Warum |
|---|---|
| Die Registrierung der Domain, auf den Namen des Unternehmens | wer die Domain hält, kontrolliert die Website |
| Ein eigenes Administratorkonto | damit Sie nie ausgesperrt sind ([Konten, Passwörter und Zwei-Faktor-Anmeldung](../../kunden/alltag/konten-passwoerter-und-zweistufige-anmeldung.md)) |
| Die Konten bei externen Diensten (Zahlungsanbieter, Karten, CAPTCHA, Analyse, Unternehmenseinträge) | sie rechnen für Ihr Unternehmen ab oder sprechen für es |
| Eine Kopie der letzten Sicherung oder das Recht, eine anzufordern | um bei Bedarf zu einem anderen Partner zu wechseln |
| Die Liste der verwendeten Erweiterungen und Einstellungen | damit ein neuer Partner übernehmen kann ([Eine Website übernehmen](../../kunden/partner-und-hoster/eine-website-uebernehmen.md)) |

## Die Übergabe

Bitten Sie am Ende des Aufbaus um eine Stunde, in der der Partner Ihnen auf Ihrer Live-Website zeigt:

1. sich anmelden und die Öffnungszeiten ändern;
2. ein Angebot oder eine Veranstaltung hinzufügen;
3. eine Nachricht über das Kontaktformular lesen;
4. ein Foto ersetzen;
5. wo Sie die Bestellliste finden, wenn Sie verkaufen;
6. wen Sie anrufen, und wie, wenn etwas nicht stimmt.

Schreiben Sie die Antworten auf; [Vorlage für Eskalationskontakte](../../kunden/partner-und-hoster/vorlage-eskalationskontakte.md)
ist ein fertiges Formular für den letzten Punkt.

## Eine Anfrage, die Sie verschicken können

Die Vorlage steht hier auf Englisch; eine deutsche Fassung folgt darunter.

```text
Subject: Website for <business name>, <town>

We are a <kind of business> with <number> location(s) in <town>.
We would like a website on Exponential that:
- shows our services/menu, opening hours, address with a map and contact details;
- has a contact form with spam protection;
- <optional> sells <products, vouchers> with <PayPal>;
- <optional> is in <languages>.
We want to change texts, hours and photos ourselves.
Please quote separately for: build, hosting, updates and support per year.
Please say which extensions you would use and who holds the API keys.
Our brief is attached.
```

> Betreff: Website für &lt;Name des Unternehmens&gt;, &lt;Ort&gt;
>
> Wir sind ein &lt;Art des Unternehmens&gt; mit &lt;Anzahl&gt; Standort(en) in &lt;Ort&gt;.
> Wir hätten gern eine Website mit Exponential, die:
> - unsere Leistungen/Speisekarte, Öffnungszeiten, Adresse mit Karte und Kontaktdaten zeigt;
> - ein Kontaktformular mit Spamschutz hat;
> - &lt;optional&gt; &lt;Produkte, Gutscheine&gt; mit &lt;PayPal&gt; verkauft;
> - &lt;optional&gt; auf &lt;Sprachen&gt; ist.
>
> Wir möchten Texte, Öffnungszeiten und Fotos selbst ändern.
> Bitte bieten Sie getrennt an: Aufbau, Hosting, Updates und Support pro Jahr.
> Bitte nennen Sie die Erweiterungen, die Sie verwenden würden, und wer die API-Schlüssel verwahrt.
> Unser Briefing liegt bei.

Hängen Sie das [Website-Briefing](../vorlagen/website-briefing-fuer-ein-lokales-unternehmen.md) an.

## Verwandte Seiten

- [Selbst machen oder Hilfe holen](selbst-machen-oder-hilfe-holen.md)
- [Einen Partner wählen](../../kunden/partner-und-hoster/einen-partner-auswaehlen.md)
- [Verträge und SLAs](../../kunden/partner-und-hoster/vertraege-und-slas.md)
- [Implementierungspartner](../../partner/implementierungspartner.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
