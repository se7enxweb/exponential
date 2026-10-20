---
title: Zu prüfende Dienste von Drittanbietern
audience: government, admin, project-manager
category: public-sector
order: 60
summary: Ein Prüfablauf für jeden optionalen Dienst, der Daten aus der Organisation hinaus sendet (Webanalyse, CAPTCHA, Karten, Social Login, KI-Anbieter, Einbettungen, CDNs), mit dem, was Exponential jeweils mitliefert, und den Entscheidungen, die eine öffentliche Stelle festhält, bevor sie einen davon aktiviert.
keywords: Dienste von Drittanbietern, Auftragsverarbeiter, Webanalyse, Google Analytics, reCAPTCHA, hCaptcha, Google Maps, Social Login, KI-Anbieter, Einbettungen, CDN, Datenschutz, Prüfung, Souveränität, third-party services, processors, analytics, AI provider, embeds, data protection, review, sovereignty
type: help
---
# Zu prüfende Dienste von Drittanbietern

Diese Seite richtet sich an Administratoren, Datenschutzbeauftragte und Projektleitungen, die entscheiden, ob ein
optionaler Dienst eingeschaltet wird, an dem eine andere Organisation beteiligt ist. Exponential installiert mehrere
solcher Erweiterungen, lässt sie aber inaktiv; die Entscheidung und die Dokumentation liegen bei Ihnen
([Datenschutzstandards](../../../../eng-us/standards/privacy.md#third-party-services-in-shipped-extensions)).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Die betroffenen Dienste](#die-betroffenen-dienste)
2. [Der Prüfablauf](#der-prüfablauf)
3. [Alternativen, die die Daten im Haus behalten](#alternativen-die-die-daten-im-haus-behalten)
4. [Nach der Aktivierung](#nach-der-aktivierung)

## Die betroffenen Dienste

| Dienst | Erweiterung oder Quelle | Daten, die hinausgehen | Alternative in Exponential |
|---|---|---|---|
| Webanalyse | `bcwebsitestatistics` (Google Analytics / Tag Manager) | Anfragen der Besucher, Gerätedaten | Serverprotokolle, Anfrageprotokolle ([Anfrageprotokolle](../../../../eng-us/features/6.0/request-logs.md)) |
| CAPTCHA | `recaptcha`, `hcaptcha` | Interaktionsdaten der Besucher | das Formular-Token (`ezformtoken`) und serverseitige Prüfungen ([Spamschutz](../../../../eng-us/help/creators/website-creators/spam-protection-for-forms.md)) |
| Karten | `ezgmaplocation` mit einem API-Schlüssel | Kartenabrufe der Besucher, Adresssuchen der Redaktion | Koordinaten und Adressen als Text, ohne Karte |
| Social Login | Anmelde-Handler von `sevenx_authentication_2fa` | Anmeldeereignisse | lokale Konten mit zweistufiger Anmeldung ([Anmeldung mit zwei Faktoren](../../../anleitungen/zwei-faktor-anmeldung.md)) |
| KI-Schreibassistent | `expai` | Inhalte, die an den Modellanbieter gesendet werden | ein lokales Open-Weight-Modell über Ollama ([expai](../../../../eng-us/features/6.0/extensions/expai.md)) |
| Eingebettete Videos, Social-Media-Beiträge, Schriften | Ihr Design oder Einbettungen der Redaktion | Anfragen der Besucher an den Host der Einbettung | selbst gehostete Dateien |

Die Statistiken von `cjw_newsletter` zählen Öffnungen und Klicks auf Ihrer eigenen Website, nicht bei einem
Drittanbieter; sie brauchen die vorherige Einwilligung der Empfänger in diese Kategorie
([Datenschutzstandards](../../../../eng-us/standards/privacy.md)).

## Der Prüfablauf

Halten Sie für jeden Dienst vor der Aktivierung fest:

1. **Zweck**: welches Problem er löst; ob eine Alternative im Haus genügt.
2. **Daten**: welche personenbezogenen Daten hinausgehen, von wem (Besucher, Mitarbeitende), in welches Land.
3. **Rechtsgrundlage und Einwilligung**: Ist eine Einwilligung nötig? Exponential liefert kein Cookie-Banner zur
   Einwilligung mit; eine Website, die Webanalyse, Einbettungen oder Werbung hinzufügt, muss eines hinzufügen
   ([Datenschutzstandards](../../../../eng-us/standards/privacy.md)).
4. **Vertrag**: Auftragsverarbeitungsvertrag oder Nutzungsbedingungen; die Unterauftragsverarbeiter des Anbieters.
5. **Sicherheit**: Einträge in der Content Security Policy, die der Dienst braucht; für `ezgmaplocation` listet die
   README die freizugebenden Google-Hosts auf.
6. **Ausstieg**: was mit der Website und den Daten geschieht, wenn Sie ihn abschalten.
7. **Verantwortliche Person**: wer ihn erneut prüft, und wann.

Bewahren Sie die Aufzeichnung bei Ihrem Verzeichnis von Verarbeitungstätigkeiten auf.

## Alternativen, die die Daten im Haus behalten

- Webanalyse aus serverseitigen Protokollen statt eines Skripts auf jeder Seite.
- Kein CAPTCHA, solange kein Missbrauch auftritt; dann serverseitige Maßnahmen bevorzugen.
- Lokale Modelle für die KI-Unterstützung, wo die Inhalte die Organisation nicht verlassen dürfen; `expai` hält pro
  Anbieter eine Region (`us`, `eu`, `local`) und die Aufbewahrung fest, und **Setup > EI: Summary** bietet eine Seite
  für die Datenschutzbeauftragten.

## Nach der Aktivierung

- Nennen Sie den Dienst in der Datenschutzerklärung.
- Prüfen Sie, dass der Browser ihn nur dort lädt, wo es beabsichtigt ist (Entwicklerwerkzeuge; CSP-Berichte).
- Prüfen Sie erneut, wenn der Anbieter seine Bedingungen ändert, und mindestens einmal im Jahr.

## Verwandte Seiten

- [Keine Pflicht zu externen Diensten](keine-pflicht-zu-externen-diensten.md)
- [Datenschutzstandards](../../../../eng-us/standards/privacy.md)
- [Content Security Policy](../../../../eng-us/features/6.0/content-security-policy.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
