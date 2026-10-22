---
title: Episoden nur für Mitglieder
audience: podcaster
category: podcasting-streaming
order: 36
summary: Wie Sie Bonusepisoden, frühere Veröffentlichungen oder ein Premium-Archiv auf einer Exponential-Website angemeldeten Mitgliedern vorbehalten - Sektionen und Rollen, die Rechteprüfung bei Downloads, warum eine Datei nur für Mitglieder nicht in einem öffentlichen Podcast-Feed stehen kann, und was nicht verfügbar ist, etwa private Feeds pro Abonnent.
keywords: nur für Mitglieder, Premium, Bonusepisoden, Abonnenten, Bezahlschranke, Sektionen, Rollen, content/read, geschützter Download, privater Feed, Mitgliederbereich, früher Zugang, Patreon, members only, bonus episodes, subscribers, paywall, sections, roles, protected download, private feed, members area, early access
type: help
---
# Episoden nur für Mitglieder

Diese Seite ist für Sendungen, die Mitgliedern etwas Besonderes bieten: Bonusepisoden, früheren Zugang, ein
Premium-Archiv. Exponential schützt Inhalte mit **Sektionen** und **Rollen**: Besucher, die nicht angemeldet sind,
sehen nur, was die Rolle Anonymous lesen darf. Dieselbe Prüfung schützt die Audiodateien. Was Exponential nicht hat,
ist ein privater Podcast-Feed pro Abonnent.

## Inhalt

1. [Wie der Schutz funktioniert](#wie-der-schutz-funktioniert)
2. [Einen Mitgliederbereich für eine Sendung einrichten](#einen-mitgliederbereich-für-eine-sendung-einrichten)
3. [Auch Dateien sind geschützt](#auch-dateien-sind-geschützt)
4. [Episoden nur für Mitglieder und der Podcast-Feed](#episoden-nur-für-mitglieder-und-der-podcast-feed)
5. [Was nicht verfügbar ist](#was-nicht-verfügbar-ist)
6. [Testen](#testen)

## Wie der Schutz funktioniert

- Jedes Objekt gehört zu einer **Sektion** ([Sektionen](../../anleitungen/sektionen.md)).
- **Rollen** gewähren `content/read` für Sektionen, Teilbäume oder Klassen ([Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)).
- Die Rolle **Anonymous** entscheidet, was nicht angemeldete Besucher sehen dürfen
  ([Was Besucher sehen dürfen](../ersteller/website-ersteller/rechte-fuer-besucher.md)).
- Eine Mitgliederrolle, einer Benutzergruppe für Mitglieder zugewiesen, fügt die Mitgliedersektion hinzu
  ([Registrierung von Mitgliedern und Bereiche nur für Mitglieder](../ersteller/website-ersteller/mitgliederregistrierung-und-mitgliederbereiche.md)).

## Einen Mitgliederbereich für eine Sendung einrichten

Für den Administrator:

1. Legen Sie eine Sektion an, zum Beispiel **Members**.
2. Legen Sie in der Sendung einen Ordner "Bonusepisoden" an und weisen Sie ihn mit allem darunter der Sektion zu.
3. Geben Sie der Mitgliederrolle `content/read` für die Sektion **Members**; lassen Sie die Rolle Anonymous ohne sie.
4. Stellen Sie sicher, dass die Benutzergruppe der Mitglieder die Mitgliederrolle hat, und entscheiden Sie, wie Menschen
   Mitglied werden (Registrierung, Import, ein Shop oder von Hand).

Das Rezept im Einzelnen: [Sektionen und Objektzustände für Website-Ersteller](../ersteller/website-ersteller/sektionen-und-objektzustaende.md#rezept-ein-mitgliederbereich).

## Auch Dateien sind geschützt

Episodendateien werden von `content/download` ausgeliefert, das prüft, ob der Besucher das Objekt lesen darf, bevor es
die Datei sendet ([Dateien und Downloads](../ersteller/website-ersteller/dateien-und-downloads.md#geschützte-downloads)). Ein
nicht angemeldeter Besucher erhält keine Datei, auch nicht mit der genauen Adresse. Kopieren Sie Dateien nicht an einen
öffentlichen Ort und verlinken Sie sie nicht aus `var/.../storage/`; das umgeht die Prüfung.

## Episoden nur für Mitglieder und der Podcast-Feed

Podcast-Apps und Verzeichnisse rufen Feeds und Dateien **ohne Anmeldung** ab. Daher:

- Eine Episode, die nur Mitglieder lesen dürfen, kann eine App aus einem öffentlichen Feed nicht abspielen: Der
  Download des Enclosures wird der anonymen App verweigert.
- Nehmen Sie Episoden nur für Mitglieder nicht in die Feed-Quelle der öffentlichen Sendung auf. Halten Sie sie in einem
  eigenen Ordner außerhalb der Quellen des öffentlichen Feeds oder in einer Sektion, die die Leser des öffentlichen
  Feeds nicht sehen können.
- Prüfen Sie nach dem Einrichten des Bereichs den öffentlichen Feed abgemeldet: Er darf nur öffentliche Episoden
  aufführen.

Mitglieder hören auf der Website, angemeldet, mit dem Player der Episodenseite.

## Was nicht verfügbar ist

| Nicht verfügbar | Stattdessen |
|---|---|
| Eine private Feed-Adresse pro Abonnent (ein Feed mit persönlichem Token) | Mitglieder hören auf der Website; oder Sie nutzen einen Podcast-Hoster, der private Feeds anbietet |
| Anmeldung in Podcast-Apps | mit dem mitgelieferten Feed nicht möglich |
| Zeitlich begrenzter früher Zugang, der sich von selbst öffnet | im Mitgliederordner veröffentlichen und die Episode am öffentlichen Termin von Hand in den öffentlichen Ordner verschieben; Verschieben behält das Objekt, daher bleibt seine `guid` gleich |
| Bezahlung der Mitgliedschaft in der Podcast-Funktion | Shop und Zahlungsanbieter sind eigene Funktionen ([Shop-Überblick](../ersteller/website-ersteller/shop-ueberblick.md)) |

## Testen

- [ ] Abgemeldet: Der Bonusordner und seine Episoden erscheinen nicht in Menüs, Listen oder der Suche.
- [ ] Abgemeldet: Die Download-Adresse einer Bonusepisode liefert die Datei nicht aus.
- [ ] Als Testmitglied angemeldet: Die Episodenseite und ihr Player funktionieren.
- [ ] Der öffentliche Podcast-Feed führt abgemeldet keine Episode nur für Mitglieder auf.

Verwenden Sie ein Testkonto für Mitglieder mit einer Adresse unter `example.invalid`, nie das Konto eines echten
Mitglieds.

## Verwandte Seiten

- [Sektionen und Objektzustände für Website-Ersteller](../ersteller/website-ersteller/sektionen-und-objektzustaende.md)
- [Registrierung von Mitgliedern und Bereiche nur für Mitglieder](../ersteller/website-ersteller/mitgliederregistrierung-und-mitgliederbereiche.md)
- [Rechte und Lizenzen dessen, was Sie veröffentlichen](rechte-und-lizenzen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
