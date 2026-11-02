---
title: "Ein GitHub-Konto und Repository anlegen"
audience: admin, website-creator, non-technical, non-profit, local-business, individual, student
category: administration
order: 4
summary: Für alle, die neu bei GitHub sind - ein kostenloses Konto anlegen, das Repository für Ihre statische Website erstellen und das Token anlegen, mit dem Exponential pusht, Bildschirm für Bildschirm beschrieben.
keywords: Statisches Veröffentlichen, GitHub-Konto, registrieren, Repository anlegen, öffentliches Repository, Token, Einsteiger, static publishing, GitHub account, sign up, create repository, new repository, public repository, fine-grained token, personal access token, GitHub Pages, github.io, free hosting, gh-pages, beginners
type: help
---
# Ein GitHub-Konto und Repository anlegen

GitHub ist ein Dienst, der Dateien in **Repositorys** aufbewahrt (Ordnern mit Verlauf). Seine **GitHub Pages** liefern
die Dateien eines Repositorys als Website aus, für öffentliche Repositorys kostenlos. Diese Seite richtet auf GitHub
ein, was statisches Veröffentlichen braucht. Die Namen der Schaltflächen von GitHub stehen fett und auf Englisch, wie
GitHub sie zeigt; GitHub ändert seine Bildschirme ab und zu, suchen Sie dann nach denselben Wörtern.

## Was Sie am Ende haben

Ein GitHub-Konto, ein leeres öffentliches Repository für Ihre Website und ein in Exponential gespeichertes Token.

## 1. Das Konto

1. Öffnen Sie `https://github.com/` und wählen Sie **Sign up**.
2. Geben Sie eine E-Mail-Adresse, ein Passwort und einen **Username** ein. Wählen Sie den Namen mit Bedacht: Die
   kostenlose Adresse Ihrer Website wird `https://<username>.github.io/`.
3. Bestätigen Sie Ihre E-Mail-Adresse. Der Plan **Free** genügt.
4. Schalten Sie die Zwei-Faktor-Authentifizierung unter **Settings > Password and authentication** ein; GitHub
   verlangt sie ohnehin.

Eine Organisation (für einen Verein, eine Firma oder eine Schule) legen Sie aus Ihrem Konto unter **Your
organizations > New organization** an; ihre Websites liegen unter `https://<organisation>.github.io/`.

## 2. Das Repository

1. Wählen Sie oben **+**, dann **New repository**.
2. **Owner**: Sie oder Ihre Organisation. **Repository name**: kurz, klein geschrieben, zum Beispiel `my-site`. Er
   wird Teil der Adresse: `https://<username>.github.io/my-site/`.
3. Wählen Sie **Public**. Kostenlose Pläne liefern GitHub Pages nur aus öffentlichen Repositorys aus.
4. Lassen Sie **Add a README** und die anderen Kästchen leer und wählen Sie **Create repository**.

Die folgende Seite zeigt die Adresse des Repositorys, `https://github.com/<username>/my-site.git`. Bewahren Sie sie
für den Assistenten auf.

## 3. Das Token

Exponential meldet sich mit einem **Fine-grained Token** an, das nur dieses eine Repository berühren darf:
[Mit GitHub verbinden: Token, Deploy-Key oder App](mit-github-verbinden-token-key-oder-app.md) nennt die Klicks und
die Berechtigungen. Speichern Sie es sofort unter **Setup > Geheimnisse**; GitHub zeigt es nur einmal.

## Gut zu wissen

- Alles in einem öffentlichen Repository kann jeder sehen, auch seinen Verlauf. Veröffentlichen Sie nur, was
  öffentlich sein darf: [Private Inhalte privat halten](private-inhalte-privat-halten.md).
- Die Bedingungen von GitHub gelten für Ihre Website: [Grenzen und Bedingungen von GitHub Pages](grenzen-und-bedingungen-von-github-pages.md).

## Nächster Schritt

[In 15 Minuten eine kostenlose Website](in-15-minuten-eine-kostenlose-website.md), Schritt 2.
