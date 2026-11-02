---
title: "Andere Git-Hosts: GitLab, Gitea und eigener Server"
audience: admin, website-creator, developer, hosting, government
category: administration
order: 23
summary: Die statische Kopie zu GitLab Pages, Codeberg oder Gitea-Pages oder in ein Git-Repository auf Ihrem eigenen Server veröffentlichen - das Ziel mit Kind=git, Anmeldung mit Token oder Deploy-Key, und was jeder Host zum Ausliefern braucht.
keywords: Statisches Veröffentlichen, anderer Git-Host, eigener Server, selbst gehostet, static publishing, other git host, GitLab, GitLab Pages, Gitea, Forgejo, Codeberg, Codeberg Pages, own server, self-hosted, Kind=git, deploy key, token, git push, static site, free hosting, Netlify, Cloudflare Pages
featured: true
type: help
---
# Andere Git-Hosts: GitLab, Gitea und eigener Server

Statisches Veröffentlichen pusht in jedes Git-Repository. GitHub Pages ist nur der häufigste Ort; derselbe Build geht
zu GitLab, auf einen Gitea- oder Forgejo-Server, zu Codeberg oder in ein Bare-Repository auf einem eigenen Server.

## Was Sie am Ende haben

Ein Ziel, das zum Git-Host Ihrer Wahl veröffentlicht, und diesen Host, der die Dateien ausliefert.

## Das Ziel

Wählen Sie in Schritt 2 des Assistenten **ein anderer Git-Host** (`Kind=git`). Dann:

- **Git-Adresse**: `https://gitlab.com/<gruppe>/<projekt>.git`, `git@codeberg.org:<owner>/pages.git` oder
  `ssh://git@server.example.com/srv/git/site.git`. Nie mit einem Passwort darin.
- **Anmeldung**: ein **Token** unter Setup > Geheimnisse (die meisten Hosts nehmen ein Projekt- oder persönliches
  Zugriffstoken über `https://` an) oder ein **Deploy-Key** für eine SSH-Adresse. Erzeugen Sie den Schlüssel auf der
  Seite **Deploy-Key** des Ziels und tragen Sie seinen öffentlichen Teil mit Schreibrecht beim Repository ein.
  **Keine** genügt für ein Repository auf demselben Server.
- **Branch** und **Ordner**: woraus der Host ausliefert (siehe unten).

Die GitHub-Schritte (Repository anlegen, Pages einschalten, die DNS-Prüfung gegen GitHub) gibt es bei `Kind=git` nicht;
das erledigen Sie beim Host. Bauen, Vorschau, Veröffentlichen, Verlauf und Rücknahme funktionieren gleich.

## Was jeder Host braucht

| Host | Repository und Branch | Hinweise |
|---|---|---|
| **GitLab Pages** | jedes Projekt; eine Pipeline veröffentlicht den Ordner `public` | eine `.gitlab-ci.yml` mit einem Job `pages` anlegen, der die Dateien nach `public/` kopiert; die Datei bleibt im Branch, der Build lässt Dateien, die er nicht geschrieben hat, in Ruhe |
| **Codeberg Pages** | ein Repository `pages` (oder der Branch `pages` eines Repositorys) | ausgeliefert unter `https://<owner>.codeberg.page/` |
| **Gitea / Forgejo** | jedes Repository | das Ausliefern hängt vom Server ab; viele nutzen einen Pages-Server oder einen Hook |
| **Eigener Server** | ein Bare-Repository | ein `post-receive`-Hook, der den Branch in den Ordner des Webservers auscheckt, oder ein Webserver, der einen Checkout ausliefert |

Andere Hosts als GitHub haben eigene Größengrenzen und Bedingungen; lesen Sie sie vor dem Veröffentlichen.

## Links

Prüfen Sie, wo der Host die Website ablegt: unter `/<projekt>/` wie eine GitHub-Projektseite oder an der Wurzel einer
Domain. Relative Links funktionieren in beiden Fällen; setzen Sie die **öffentliche Adresse** für die Sitemap
([Links und Basispfad](links-und-basispfad.md)).

## Nächster Schritt

[Build, Vorschau und Veröffentlichen](build-vorschau-und-veroeffentlichen.md). Push und Zugangsdaten im Detail:
[Repositorys und Zugangsdaten](../../anleitungen/statisches-veroeffentlichen-repositorys-und-zugangsdaten.md).
