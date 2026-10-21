---
title: Checklisten für Barrierefreiheit, Sicherheit und Compliance
audience: project-manager, admin
category: leadership
order: 80
summary: Checklisten für Barrierefreiheit, die Content Security Policy, Sicherheitshärtung und Go-live, Konten, das Audit-Protokoll und das Melden von Schwachstellen, dazu der Datenschutz als Fragenkatalog. Keine Rechtsberatung.
keywords: Barrierefreiheit, Rechts-nach-links, Sicherheit, Sicherheitshärtung, Audit-Protokoll, Compliance, Go-live, Datenschutz, Privatsphäre, DSGVO, Einwilligung, Schwachstelle, Checkliste, Projektleiter, Administrator, accessibility, a11y, WCAG, right to left, security, hardening, Content Security Policy, CSP, audit trail, data protection, privacy, GDPR, consent, vulnerability, SECURITY.md, checklist, project manager
type: help
---
# Checklisten für Barrierefreiheit, Sicherheit und Compliance

Diese Seite ist für Projektleiter, die nachweisen müssen, dass eine Site barrierefrei und sicher ist und im Einklang mit
den Regeln betrieben wird, die für ihre Organisation gelten, und für die Administratoren, die die Arbeit erledigen. Sie
beantwortet: was Exponential in jedem Bereich bereits leistet (mit Quelle), was Ihr Projekt noch tun muss und welche
Fragen Sie selbst beantworten müssen. Verwenden Sie die Checklisten in Reviews und bei der Abnahme.

> **Warnung**
> Diese Seite ist keine Rechtsberatung. Sie nennt, was das Produkt leistet und welche Fragen zu stellen sind. Welche
> Gesetze und Normen für Ihre Organisation gelten und ob eine Site sie erfüllt, entscheiden Ihre Organisation und ihre
> Berater.

## Inhalt

1. [So verwenden Sie diese Checklisten](#so-verwenden-sie-diese-checklisten)
2. [Barrierefreiheit](#barrierefreiheit)
3. [Content Security Policy](#content-security-policy)
4. [Sicherheitshärtung und Go-live](#sicherheitshärtung-und-go-live)
5. [Konten und Zugriff](#konten-und-zugriff)
6. [Das Audit-Protokoll](#das-audit-protokoll)
7. [Schwachstellen melden](#schwachstellen-melden)
8. [Datenschutz und Privatsphäre: zu beantwortende Fragen](#datenschutz-und-privatsphäre-zu-beantwortende-fragen)
9. [Damit es so bleibt](#damit-es-so-bleibt)
10. [Ein Review-Protokoll zum Kopieren](#ein-review-protokoll-zum-kopieren)

## So verwenden Sie diese Checklisten

- **Geben Sie jeder Zeile einen Verantwortlichen und ein Datum.** Eine Checkliste ohne Namen ist ein Wunschzettel.
- **Verlangen Sie Nachweise**: eine Befehlsausgabe, einen Screenshot, einen Testbericht. Die meisten Zeilen nennen den
  Befehl oder die Seite, die den Nachweis liefert.
- **Gehen Sie sie dreimal durch**: bei der Planung (um den Aufwand abzugrenzen), vor dem Start (zur Abnahme) und nach dem
  Start in festen Abständen (damit es so bleibt).
- **Halten Sie Ausnahmen fest.** Eine nicht erfüllte Zeile wird entweder von jemandem mit Begründung akzeptiert, oder sie
  ist ein Risiko ([Risikomanagement](risikomanagement.md)).

## Barrierefreiheit

### Was das Produkt leistet

| Bereich | Was die Quellen sagen | Quelle |
|---|---|---|
| Administration (Design admin4) | Durch Tests geprüfte WCAG-Regeln: Kontrast der Farb-Tokens im hellen und im dunklen Modus (Text 4,5:1), Zielgrößen von mindestens 24 mal 24 Pixeln (WCAG 2.5.8), jedes Formularfeld hat einen Namen, Landmarks und Namen für Screenreader | [Die Administration gestalten](../../bc/6.0/admin4-gestaltung.md) |
| Tastatur und Screenreader | Ein Sprunglink als erster Tab-Stopp, Landmarks für Kopfbereich, Menüs, Brotkrumenpfad und Hauptinhalt, die aktuelle Seite mit `aria-current` gekennzeichnet, Menüs mit Rollen und Tastaturbedienung | [Das Design Admin 4](../../funktionen/6.0/admin4-design.md) |
| Vom Browser angeforderte Modi | Helles und dunkles Theme, Windows-Kontrastmodus (forced colors), reduzierte Bewegung | [Die Administration gestalten](../../bc/6.0/admin4-gestaltung.md) |
| Tastenkürzel | Die Administration und die Hilfe lassen sich allein mit der Tastatur bedienen | [Tastenkürzel](../tastenkuerzel.md) |
| Rechts-nach-links-Sprachen | `dir="rtl"` in der Administration und in Inhalten, die in einer Rechts-nach-links-Sprache verfasst sind | [Rechts nach links](../../funktionen/6.0/rechts-nach-links.md) |
| Alternativtext | Bildfelder können pro Klasse einen Alternativtext verlangen; ausgeschaltet, bis es eingeschaltet wird | [Inhaltsmodell, Abschnitt 9](../../anleitungen/inhaltsmodell-und-bearbeitung.md) |
| Fehlender Alternativtext | Exponential Intelligence hat einen Bericht „Missing alt text“, der Vorschläge für einen Teilbaum in eine Warteschlange stellt | [expai](../../funktionen/6.0/erweiterungen/expai.md) |
| Die Hilfe | Sofort-Suchergebnisse werden Screenreadern angesagt | [Das Hilfesystem](../../funktionen/6.0/hilfesystem.md) |

> **Hinweis**
> Die Quellen beschreiben Tests für die **Administration**. Sie sagen nicht, dass irgendein Design einer öffentlichen
> Site eine bestimmte WCAG-Stufe erfüllt. Die Barrierefreiheit Ihrer öffentlichen Sites hängt von Ihren Designs,
> Templates und Inhalten ab und ist Aufgabe Ihres Projekts.

### Checkliste: Administration

- [ ] Redakteure verwenden das Design admin4 (oder ein anderes Administrationsdesign, dessen Barrierefreiheit Sie
      geprüft haben).
- [ ] Die Sprache der Oberfläche jedes Redakteurs ist in der Stufe verfügbar, die er braucht
      ([Sprachen, Unterstützungsstufen](../../sprachen.md#2-unterstützungsstufen)).
- [ ] Erweiterungen, die Sie der Administration hinzufügen, folgen den Gestaltungsregeln (keine Inline-Styles,
      gemeinsame Komponenten, Tokens mit Werten für den dunklen Modus): `CONTRIBUTING.md`,
      [Die Administration gestalten](../../bc/6.0/admin4-gestaltung.md).
- [ ] Seiten Ihrer eigenen Erweiterungen wurden im hellen und im dunklen Modus geprüft, in normaler Größe und bei 200 %
      Zoom, wie es die Gestaltungsregeln verlangen ([Die Administration gestalten](../../bc/6.0/admin4-gestaltung.md)).

### Checkliste: öffentliche Sites

- [ ] Die geltende Norm für Barrierefreiheit und ihre Stufe sind im Projektauftrag genannt. (Welche gilt, ist eine Frage
      für Ihre Organisation; die Quellen nennen keine für öffentliche Sites.)
- [ ] Bildfelder der Klassen, die Besucher sehen, verlangen einen Alternativtext.
- [ ] Der Bericht „Missing alt text“ (sofern Exponential Intelligence verwendet wird) oder eine manuelle Durchsicht
      findet keine Lücken.
- [ ] Templates setzen für jeden Siteaccess die Sprache der Seite und die Textrichtung, auch für
      Rechts-nach-links-Siteaccesses.
- [ ] Farbkontrast, Tastaturbedienung, sichtbarer Fokus, Überschriften und Formularbeschriftungen der Designs werden mit
      automatischen Werkzeugen und von Menschen getestet, auch von Menschen, die einen Screenreader nutzen.
- [ ] Von Redakteuren erstellter Rich Text behält eine sinnvolle Überschriftenfolge; Redakteure wissen, wie das geht
      ([Inhalte erstellen und bearbeiten](../inhalte-bearbeiten.md)).
- [ ] PDFs und andere Downloads für Besucher werden ebenfalls geprüft.

## Content Security Policy

Eine Content Security Policy „teilt dem Browser mit, welche Skripte, Stylesheets, Bilder und Verbindungen eine Seite
verwenden darf“; mit einer strengen Richtlinie läuft ein eingeschleustes Skript nicht, „selbst wenn ein Template
vergisst, es zu maskieren“ ([Content Security Policy](../../funktionen/6.0/content-security-policy.md)).

**Die Voreinstellung**: `Mode=auto` sendet auf Administrations-Siteaccesses eine Richtlinie im Nur-Bericht-Modus und auf
den Sites keine. Das Erzwingen ist eine Entscheidung pro Siteaccess, die nach einem Audit getroffen wird.

### Checkliste

- [ ] Entschieden: in der Administration erzwingen? Auf welchen Sites? Bis wann?
- [ ] Templates geprüft: `AdminContentSecurityPolicyTest` mit allen Erweiterungen der Installation ausgeführt
      (`CSP_ALL_EXTENSIONS=1`), jeder Fehler einem Verantwortlichen zugewiesen.
- [ ] Berichte aus der echten Nutzung einige Tage lang gesammelt (`ReportURI` oder die eingebaute Ansicht `csp/report`
      und die Seite **CSP reports**), jede Seite jeder Erweiterung geöffnet, Menüs und Dialoge eingeschlossen.
- [ ] Menüpunkte von Erweiterungen mit Inline-Skripten umgebaut oder `'unsafe-inline'` für diesen Siteaccess bewusst
      beibehalten (xrowextract, ezodf und ezmultiupload werden in der Quelle als Beispiele genannt).
- [ ] Zuerst auf einem zweiten Administrations-Siteaccess erzwungen, den wenige Personen nutzen.
- [ ] Jede gelockerte Direktive mit ihrer Begründung schriftlich festgehalten.
- [ ] Sites zuerst mit `report-only` gestartet, dann `enforce`; Rich Text und eingebettete Player geprüft.
- [ ] Der Rückweg ist bekannt: `Mode=report-only` und Leeren des INI-Caches.

Alle Schritte: [Schritt für Schritt zum Erzwingen](../../funktionen/6.0/content-security-policy.md#schritt-für-schritt-auf-enforce-umstellen).

## Sicherheitshärtung und Go-live

Das Projekt folgt dem Grundsatz „sicher ab Werk im Sinne von OpenBSD“: Eine frische Installation ist ohne zusätzliche
Arbeit sicher, jeder Teil hat nur die Rechte, die er braucht, und jede Änderung wird protokolliert; „Ihr Teil ist, dafür
zu sorgen, dass es so bleibt“ ([Sicherheitshärtung](../sicherheitshaertung.md)).

### Was ab Werk eingeschaltet ist

Aus [Sicherheitshärtung](../sicherheitshaertung.md) und [Sicherheit und Audit](../../anleitungen/sicherheit-und-audit.md):
strenge Rewrite-Regeln, fünf Sicherheits-Header, Cookies mit `HttpOnly` und `SameSite`, Debug-Ausgabe aus,
Formular-Tokens gegen Cross-Site Request Forgery, bcrypt-Passwort-Hashes, generierte Administratorpasswörter, der
Setup-Assistent auf einer installierten Site geschlossen, hochgeladene Bilder werden nach ihrem Inhalt und nicht nach
ihrem Namen akzeptiert.

### Checkliste für den Projektleiter

Ihr Administrator arbeitet zwei ausführliche Listen ab:
[die Härtungs-Checkliste der Hilfe](../sicherheitshaertung.md#checkliste) und
[13.16 Go-live-Checkliste](../../installation/13-sicherheitshaertung.md#1316-checkliste-für-den-livegang). Ihre Aufgabe ist es, darauf zu
achten, dass jede Zeile einen Verantwortlichen, ein Datum und einen Nachweis hat. Die am häufigsten vergessenen Zeilen:

- [ ] Das generierte Administratorpasswort geändert und `var/log/initial-admin-password` entfernt.
- [ ] Ein persönliches Administratorkonto pro Person; `admin` wird nicht mehr zum Anmelden verwendet; ein getestetes
      Notfallverfahren.
- [ ] Anmeldung in zwei Schritten für Administratoren verpflichtend ([Anmeldung in zwei Schritten](../../anleitungen/zwei-faktor-anmeldung.md)).
- [ ] `CheckValidity=false`; die Administration auf einem eigenen Host, eingeschränkt über das Netzwerk oder eine zweite
      Authentifizierung.
- [ ] Debug-Ausgabe aus; PHP `display_errors=Off`.
- [ ] Überall HTTPS, HSTS, sobald es stabil läuft, `CookieSecure=true` auf Sites, die nur über HTTPS erreichbar sind.
- [ ] Sicherheits-Header vorhanden: die `curl`-Prüfung aus [Sicherheit und Audit, Abschnitt 1](../../anleitungen/sicherheit-und-audit.md).
- [ ] `settings/override/` nicht in der Versionsverwaltung, als Geheimnis gesichert.
- [ ] Code für den Benutzer der Site nur lesbar; Worker und Cronjobs nie als root.
- [ ] Hinter einem Proxy: `TrustedProxies[]` führt genau Ihre Proxies auf.
- [ ] Backups außerhalb der Maschine, Wiederherstellung getestet.
- [ ] Eine Update-Routine: Wer verfolgt die Releases, wie werden Updates getestet, wer empfängt Sicherheitsmeldungen?

Ein Schutzschild vor der Site, request-shield (ein Projekt von CJW Network), ist als Option dokumentiert, zuerst im
Beobachtungsmodus, dann erzwungen ([Sicherheitshärtung](../sicherheitshaertung.md#ein-vorgeschalteter-schutzschild-request-shield)).

## Konten und Zugriff

- [ ] Rollen enthalten nur, was die Personen brauchen; wenige Administratoren
      ([Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)).
- [ ] `PreventPrivilegeEscalation` bleibt `enabled` (die Voreinstellung), sodass niemand mehr vergibt, als er selbst hat.
- [ ] Registrierung bewusst offen oder geschlossen: Auf neuen Installationen erfordert die Registrierung die Richtlinie
      `user/register` ([Rollen und Richtlinien, Abschnitt 8](../../anleitungen/rollen-und-richtlinien.md)).
- [ ] Konten regelmäßig unter **Benutzer > Benutzerkonten prüfen** überprüft: nie angemeldet, lange nicht gesehen,
      deaktiviert, gesperrt, verwaist, gemeinsam genutzte E-Mail-Adresse
      ([Benutzerkonten prüfen](../../funktionen/6.0/benutzerpflege.md)).
- [ ] `exp:user-login-check` meldet, dass jeder Anmeldename zu genau einem Konto gehört
      ([Sicherheit und Audit, Abschnitt 7](../../anleitungen/sicherheit-und-audit.md)).
- [ ] Sitzungen: Abmelden beendet die Sitzung endgültig ([Sicherheit und Audit](../../anleitungen/sicherheit-und-audit.md),
      [Sitzungen](../../anleitungen/sitzungen.md)).

## Das Audit-Protokoll

Exponential 6.0.15 zeichnet sicherheitsrelevante Ereignisse in einem manipulationssicheren Protokoll auf, ab Werk
eingeschaltet: Anmeldungen und Ablehnungen, Inhaltsänderungen, geschriebene Einstellungen, geleerte Caches, Deployments,
Bestellungen und Änderungen der Mehrwertsteuer. Jeder Eintrag trägt den Hash des vorherigen; `verify` sagt Ihnen, ob
etwas bearbeitet oder abgeschnitten wurde ([Audit-Protokoll](../../funktionen/6.0/pruefprotokoll.md)).

### Checkliste

- [ ] Die Cronjob-Gruppe `frequent` läuft; sie indiziert, beliefert Senken, wertet Alarmregeln aus, rotiert und
      archiviert.
- [ ] Empfänger der Alarme geprüft (`./console exp:audit alerts recipients`); eine Person, kein ungelesenes Postfach.
- [ ] `settings/override/audit.ini.append.php` gesichert: Die Datei enthält die Signatur- und Pseudonymschlüssel.
- [ ] Das Protokoll wird mit einer Senke (syslog oder ein signierter Webhook) außerhalb der Maschine weitergeleitet,
      damit eine kompromittierte Maschine ihre eigene Geschichte nicht unbemerkt umschreiben kann
      ([Sicherheitshärtung](../sicherheitshaertung.md)).
- [ ] `exp:audit verify` regelmäßig ausgeführt; Ergebnis festgehalten.
- [ ] Aufbewahrung entschieden. Ausgeliefert: 90 Tage live und 730 Tage im Archiv für Inhalt, Zugriff, System und
      Commerce; Lesezugriffe werden nur aufgezeichnet, wenn das eingeschaltet ist, 30 und 90 Tage.
- [ ] `OnWriteFailure` entschieden: `continue` (Voreinstellung, die Site arbeitet weiter) oder `refuse`
      (sicherheitsrelevante Aktionen werden abgelehnt, solange das Protokoll nicht geschrieben werden kann).
- [ ] `audit/read` und `audit/manage` nur den Personen gegeben, die sie brauchen (in einer neuen Installation hat sie
      nur Administrator).

## Schwachstellen melden

- **Vertrauliche Meldung.** `SECURITY.md` bittet darum, Sicherheitsprobleme vertraulich per E-Mail an die dort genannte
  Adresse zu melden, „nicht im öffentlichen Issue-Tracker oder in den Diskussionen“, und verspricht „eine erste Antwort
  innerhalb eines Werktags“. Ein privates Advisory auf GitHub ist der andere dokumentierte Weg
  ([Sicherheitshärtung](../sicherheitshaertung.md#eine-schwachstelle-melden)).
- **Was dazugehört**: die Version, die PHP-Version, die Engine, die die Site ausliefert, und die Datenbank, was ein
  Angreifer tun kann und die Schritte zum Nachvollziehen; lassen Sie Passwörter, Geheimnisse und personenbezogene Daten
  weg (`SECURITY.md`).
- **Wie Korrekturen kommen**: Ein angenommenes Problem wird auf `main` behoben und mit der nächsten 6.0.x-Version
  veröffentlicht; ein veröffentlichter Tag wird nie geändert (`SECURITY.md`).

Checkliste:

- [ ] Ihr Team weiß, wo `SECURITY.md` liegt und wer in Ihrer Organisation Meldungen abgibt.
- [ ] Ihre eigenen Erweiterungen haben einen Verantwortlichen, der Meldungen dazu empfängt und bearbeitet.
- [ ] Jemand verfolgt die Releases und die Changelogs ([Release- und Upgrade-Planung](release-und-upgrade-planung.md)).

## Datenschutz und Privatsphäre: zu beantwortende Fragen

Die Quellen beschreiben Datenschutzfunktionen des Produkts; ob sie die für Sie geltenden Regeln erfüllen (zum Beispiel
die DSGVO in der Europäischen Union), entscheidet Ihre Organisation. Verwenden Sie die folgenden Punkte als Fragen in
einem Review mit Ihrem Datenschutzbeauftragten.

### Was das Produkt leistet

| Bereich | Was die Quellen sagen | Quelle |
|---|---|---|
| Audit-Protokoll | Adressen gekürzt, Sitzungen gehasht, User-Agents verkürzt, Werte von Abfrageparametern entfernt; Passwörter, Tokens, Reparaturschlüssel und Geheimnisse werden nie aufgezeichnet | [Audit-Protokoll](../../funktionen/6.0/pruefprotokoll.md) |
| Log-Viewer | E-Mail-Adressen, IP-Adressen, Passwörter, Tokens, Schlüssel, Sitzungs-IDs und Cookies maskiert; das Aufheben der Maskierung erfordert `setup/unmasklogs` | [Log-Viewer](../../funktionen/6.0/log-betrachter.md#datenschutz-was-maskiert-wird) |
| Request-Logs | Maskierte Client-Adressen, ein Hash und ein kurzer Teil des User-Agents, Werte von Abfrageparametern ersetzt | [Request-Logs](../../funktionen/6.0/anfrageprotokolle.md) |
| E-Mail | Notwendige E-Mails gehen immer hinaus; optionale E-Mails nur an Personen, die sie eingeschaltet haben, mit Abmeldung per Klick; ein Einwilligungsprotokoll; eine Sperrliste, als gesalzene Hashes gespeichert; Export und Löschung pro Adresse | [13.13 E-Mail: Einwilligung und Sperrliste](../../installation/13-sicherheitshaertung.md#1313-mail-einwilligung-und-sperrliste), [E-Mail-Einstellungen](../../anleitungen/mail-einstellungen-administrator.md) |
| Schreibassistent | Eine Region (`us`, `eu`, `local`) und eine Aufbewahrungsdauer pro Anbieter; Regeln pro Sektion und Klasse; eine druckbare Seite „EI: Summary“ für einen Datenschutzbeauftragten; Prompts, Antworten und Schlüssel werden nie im Audit aufgezeichnet | [expai](../../funktionen/6.0/erweiterungen/expai.md) |
| Gesammelte Informationen | Formularexporte enthalten, was Besucher eingegeben haben, oft Namen und E-Mail-Adressen | [Gesammelte Informationen](../../anleitungen/gesammelte-informationen.md) |
| Suchstatistiken | Suchbegriffe, die Besucher eingegeben haben, können personenbezogene Daten enthalten | [URLs und Aliase, Abschnitt 5](../../anleitungen/urls-und-aliase.md) |

### Fragen

- [ ] Welche personenbezogenen Daten erhebt die Site (Konten, Formulare, Bestellungen, Newsletter, Kommentare,
      Suchbegriffe)? Wo werden sie jeweils gespeichert?
- [ ] Wer darf sie in der Administration lesen? Ist jede solche Richtlinie nur denen gegeben, die sie brauchen?
- [ ] Wie lange werden sie jeweils aufbewahrt? (Aufbewahrung im Audit, im Einwilligungsprotokoll, Log-Rotation,
      Formularexporte, Backups.)
- [ ] Wie beantwortet die Organisation eine Anfrage, die Daten einer Person einzusehen, zu exportieren oder zu löschen?
      Welche Befehle oder Seiten werden verwendet (für E-Mail: `exp:mail:preferences export` und `erase`)?
- [ ] Nennt die Fußzeile optionaler E-Mails den Absender und seine Postanschrift (`OrganisationName`,
      `OrganisationAddress`)? Die Quelle merkt an, dass „viele Rechtsordnungen“ eine Anschrift „verlangen“.
- [ ] Wenn Exponential Intelligence eingeschaltet ist: welche Anbieter, welche Region, welche Aufbewahrung, welche
      Sektionen und Klassen dürfen gesendet werden? Hat der Datenschutzbeauftragte die Seite EI: Summary gelesen? Sie
      „sendet nichts irgendwohin, bis Sie einen Anbieterschlüssel hinzufügen“
      ([Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md#exponential-intelligence)).
- [ ] Welche externen Dienste laden die Templates (Schriften, Karten, Webanalyse, Videos)? Führt die Content Security
      Policy sie auf, und tut es die Datenschutzerklärung?
- [ ] Werden Backups und Exporte dort gespeichert, wo personenbezogene Daten hingehören, und gelöscht, wenn sie nicht
      mehr gebraucht werden?
- [ ] Wer im Projekt ist für die Datenschutzerklärung und die Cookie-Informationen auf den Sites verantwortlich?

## Damit es so bleibt

| Wie oft | Was | Quelle |
|---|---|---|
| Wöchentlich | Die Fehlerübersicht des Log-Viewers und `security.log` lesen; ein plötzlicher Anstieg einer Regel ist einen Blick wert | [Sicherheitshärtung](../sicherheitshaertung.md) |
| Wöchentlich | Die wöchentliche Checkliste für den Betrieb einer Site | [Eine Site betreiben](../../anleitungen/betrieb-einer-website.md) |
| Regelmäßig | `exp:audit verify`; Kontenprüfung unter Benutzerkonten prüfen | [Audit-Protokoll](../../funktionen/6.0/pruefprotokoll.md), [Benutzerkonten prüfen](../../funktionen/6.0/benutzerpflege.md) |
| Bei jedem Update | Prüfung der Dateikonsistenz unter **Setup > Upgrade-Prüfung**; Changelog und Upgrade-Hinweise gelesen | [Die Upgrade-Prüfung](../../anleitungen/upgrade-pruefung.md), [13.15](../../installation/13-sicherheitshaertung.md#1315-auf-dem-neuesten-stand-bleiben) |
| Bei jeder Änderung eines Templates oder einer Erweiterung | Berichte der Content Security Policy und Barrierefreiheitsprüfungen der geänderten Seiten | diese Seite |

## Ein Review-Protokoll zum Kopieren

```text
Compliance review of <project name>        date: <date>        phase: <planning | pre-launch | periodic>
Reviewers: <names and roles>

| Area             | Checklist used                 | Lines met | Lines open | Exceptions accepted (by whom, why) | Evidence (link or file) |
|------------------|--------------------------------|-----------|------------|------------------------------------|-------------------------|
| Accessibility    | this page, public sites        | <n>       | <n>        | <...>                              | <...>                   |
| CSP              | this page                      | <n>       | <n>        | <...>                              | <...>                   |
| Hardening        | help checklist + 13.16         | <n>       | <n>        | <...>                              | <...>                   |
| Accounts         | this page                      | <n>       | <n>        | <...>                              | <...>                   |
| Audit trail      | this page                      | <n>       | <n>        | <...>                              | <...>                   |
| Data protection  | questions on this page         | <n>       | <n>        | <...>                              | <...>                   |

Next review: <date>        Open items moved to the risk register: <ids>
```

## Verwandte Seiten

- [Sicherheitshärtung](../sicherheitshaertung.md)
- [Kapitel 13: Sicherheitshärtung für den Produktivbetrieb](../../installation/13-sicherheitshaertung.md)
- [Sicherheit und Audit](../../anleitungen/sicherheit-und-audit.md)
- [Content Security Policy](../../funktionen/6.0/content-security-policy.md)
- [Das Audit-Protokoll](../../funktionen/6.0/pruefprotokoll.md)
- [Qualitätsschranken und Abnahme](qualitaetsschranken-und-abnahme.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
