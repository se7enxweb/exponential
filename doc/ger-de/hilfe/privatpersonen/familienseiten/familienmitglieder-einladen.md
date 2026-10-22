---
title: Familienmitglieder einladen
audience: individual, website-creator, customer
category: personal-sites
order: 1703
summary: Wie eine Person Verwandten Zugang zu einer privaten Familienseite in Exponential gibt - Konten selbst anlegen oder Verwandte sich registrieren lassen, sie in die richtige Gruppe stellen, erste Anmeldung und Zurücksetzen des Passworts, Hilfe für weniger technikaffine Verwandte, zweistufige Anmeldung und ein Konto still deaktivieren.
keywords: einladen, Familienmitglieder, Verwandte, Konten, Benutzer anlegen, Registrierung, Benutzergruppe, DefaultUserPlacement, Aktivierungs-E-Mail, Passwort, Passwort vergessen, Konto deaktivieren, Zwei-Faktor, Familienseite, invite, family members, relatives, create user, disable account
type: help
---
# Familienmitglieder einladen

Diese Seite ist für Privatpersonen, die eine Familienseite betreiben und nun Verwandte hereinlassen möchten. Sie
können jedes Konto selbst anlegen oder Verwandte sich registrieren lassen und sie freigeben, indem Sie sie in die
richtige Gruppe stellen. Für eine Familie ist es meist besser, die Konten selbst anzulegen.

## Inhalt

1. [Zwei Wege hinein](#zwei-wege-hinein)
2. [Ein Konto selbst anlegen](#ein-konto-selbst-anlegen)
3. [Verwandte sich registrieren lassen](#verwandte-sich-registrieren-lassen)
4. [Die erste Anmeldung](#die-erste-anmeldung)
5. [Weniger technikaffinen Verwandten helfen](#weniger-technikaffinen-verwandten-helfen)
6. [Zweistufige Anmeldung](#zweistufige-anmeldung)
7. [Wenn jemand keinen Zugriff mehr haben soll](#wenn-jemand-keinen-zugriff-mehr-haben-soll)
8. [Checkliste](#checkliste)

## Zwei Wege hinein

| | Sie legen das Konto an | Die verwandte Person registriert sich |
|---|---|---|
| Wer beginnt | Sie | Die verwandte Person, unter `user/register` |
| Wer drin ist | Nur von Ihnen gewählte Personen | Jeder, der die Seite findet, bis Sie handeln |
| E-Mail nötig | Zum Zurücksetzen des Passworts | Zur Aktivierung und zum Zurücksetzen des Passworts |
| Passt zu | Familienseiten | Vereinen und größeren Gruppen ([Mitglieder und Registrierung](../vereinsseiten/mitglieder-und-registrierung.md)) |

## Ein Konto selbst anlegen

1. Öffnen Sie in der Administration **Benutzer** und die Gruppe, zu der die verwandte Person gehört, zum Beispiel
   „Familie" ([Private Bereiche mit Rollen und Sektionen](private-bereiche-mit-rollen-und-sektionen.md)).
2. **Hier erstellen** > *User*. Füllen Sie Vor- und Nachname, die E-Mail-Adresse und einen Benutzernamen aus.
3. Setzen Sie ein vorläufiges Passwort oder lassen Sie die Person über „Passwort vergessen?" selbst eines setzen.
4. Veröffentlichen. Das Konto existiert und hat die Rechte seiner Gruppe.

Sagen Sie der Person persönlich (am Telefon, direkt oder in einer Nachricht, die sie als Ihre erkennt), dass ein
Konto kommt, damit eine E-Mail Ihrer Website nicht für Spam oder Betrug gehalten wird.

## Verwandte sich registrieren lassen

Wenn Verwandte sich lieber selbst anmelden sollen, muss die Registrierungsseite `user/register` für Besucher offen
sein, und neue Konten landen in der Gruppe, die `site.ini [UserSettings] DefaultUserPlacement` festlegt. Mit
`VerifyUserType=email` wird das Konto über einen Link in einer E-Mail aktiviert
([Mitgliederregistrierung und Bereiche nur für Mitglieder](../../ersteller/website-ersteller/mitgliederregistrierung-und-mitgliederbereiche.md)).

Für eine Familienseite:

- Richten Sie `DefaultUserPlacement` auf eine Wartegruppe **ohne** Zugriff auf den privaten Bereich.
- Wenn Sie eine neue Registrierung erkennen, verschieben Sie diesen Benutzer in die Gruppe Familie.
- Entfernen Sie Registrierungen, die Sie nicht erkennen.

So sieht niemand Familieninhalte, nur weil er die Registrierungsseite gefunden hat.

## Die erste Anmeldung

Schicken Sie jeder verwandten Person:

- die Adresse der Website;
- ihren Benutzernamen;
- wie sie ihr Passwort setzt: „Passwort vergessen?" auf der Anmeldeseite schickt einen Link, der abläuft
  ([Benutzerpasswort zurücksetzen](../../../funktionen/6.0/benutzerpasswort-zuruecksetzen.md));
- einen Satz zu den Hausregeln ([Eine Familienseite planen](eine-familienseite-planen.md#hausregeln)).

Für den Link zum Zurücksetzen muss ausgehende E-Mail funktionieren; testen Sie es zuerst mit Ihrem eigenen Testkonto.

## Weniger technikaffinen Verwandten helfen

- Schreiben Sie eine einseitige Anleitung mit Bildschirmfotos Ihrer eigenen Website: anmelden, Neuigkeiten ansehen,
  Fotos ansehen, abmelden.
- Bieten Sie für die erste Anmeldung einen kurzen Videoanruf an.
- Halten Sie das Menü kurz und die Wörter einfach („Fotos", nicht „Medienbibliothek").
- Geben Sie jeder verwandten Person anfangs nur Leserechte; ergänzen Sie Schreibrechte für die, die sie möchten.
- Die Seiten für [Nicht-technische Menschen](../../../../eng-us/help/non-technical-people/non-technical-people-start-here.md) können Sie
  mit ihnen teilen.

## Zweistufige Anmeldung

Für Konten, die veröffentlichen oder verwalten können, schützt die zweistufige Anmeldung (ein App-Code oder ein Code
per E-Mail) vor gestohlenen Passwörtern. Sie kann optional oder verpflichtend sein
([Zwei-Faktor-Anmeldung](../im-betrieb/zwei-faktor-anmeldung.md)). Für Leser, denen sie schwerfällt, ist der Code per
E-Mail die sanftere Wahl.

## Wenn jemand keinen Zugriff mehr haben soll

Das Leben ändert sich. So entziehen Sie den Zugriff ohne Aufsehen:

1. Öffnen Sie den Benutzer unter **Benutzer**.
2. Deaktivieren Sie das Konto in seinen Benutzereinstellungen oder verschieben Sie es aus der Gruppe Familie. Beim
   Deaktivieren bleibt alles erhalten, was die Person geschrieben hat; das Entfernen des Kontos ist endgültig.
3. Wenn die Person Schreibrechte hatte, sehen Sie sich an, was sie zuletzt veröffentlicht hat.
4. Wenn Sie vermuten, dass das Passwort weitergegeben wurde, bitten Sie alle, ihres zu ändern, oder setzen Sie die
   Passwörter zurück.

Prüfen Sie die Liste der Konten zweimal im Jahr ([Monatliche Pflegecheckliste](../checklisten/monatliche-pflegecheckliste.md)).

## Checkliste

- [ ] Eine Gruppe Familie mit den richtigen Rollen existiert.
- [ ] Jede verwandte Person hat ein persönliches Konto in dieser Gruppe.
- [ ] Die Registrierung ist entweder geschlossen oder landet in einer Gruppe ohne Zugriff.
- [ ] Zurücksetzen des Passworts getestet.
- [ ] Eine kurze Anleitung für Verwandte.
- [ ] Zweistufige Anmeldung für Beitragende und Administratoren.
- [ ] Sie wissen, wie Sie ein Konto deaktivieren.

## Verwandte Seiten

- [Familienseiten](README.md)
- [Konten und Anmeldung](../../konten-und-anmeldung.md)
- [Mitgliederregistrierung](../../unternehmen/mitglieder/registrierung-von-mitgliedern.md)
- [Ausscheiden von Mitarbeitern](../../kunden/alltag/ausscheiden-von-mitarbeitenden.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
