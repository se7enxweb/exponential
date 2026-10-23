# French (fre-FR): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md).

## 1. Language and register

Standard French of France, neutral and technical, as in current French software. `fre-FR` is the one French
catalogue: the other French locales fall back to it (`settings/i18n.ini`, `FallbackLanguages[]`), among them
`fre-CA`, `fre-BE`, `fre-CH`, `fre-LU`, `fre-MC` and the French-speaking countries of Africa, the Indian Ocean and
the Caribbean (`fre-CD`, `fre-CI`, `fre-CM`, `fre-MA`, `fre-SN`, `fre-DZ`, `fre-TN`, `fre-MG`, `fre-HT` and others).
Write French that reads naturally in all of them: no colloquial words, no slang, no regional terms.

## 2. Form of address

The user is addressed formally with **vous**, everywhere.

- **Buttons, menu items and links**: the infinitive. "Supprimer", "Modifier", "Annuler", "Enregistrer",
  "Supprimer la sélection", "Envoyer pour publication", "Vider la corbeille". Not the imperative ("Supprimez").
- **Titles, column headings and tabs**: nouns. "Paramètres", "Traductions", "Emplacement principal", "Tableau de
  bord".
- **Requests**: "Veuillez" with the infinitive: "Veuillez choisir un emplacement.", "Veuillez saisir une adresse
  e-mail valide."
- **Instructions in hints and messages**: the imperative with vous: "Vérifiez la connexion et réessayez.",
  "Arrêtez-la à tout moment ; elle pourra être reprise plus tard."
- **Confirmations**: "Voulez-vous vraiment ... ?": "Voulez-vous vraiment supprimer la langue ?". The older form
  "Êtes-vous sûr de vouloir ... ?" is also correct, but new texts use "Voulez-vous vraiment".
- **Statements about the user**: vous and votre: "Vous ne pouvez pas supprimer la dernière classe qui possède des
  comptes d'utilisateurs.", "L'adresse e-mail de votre compte est en cours de modification".
- Not allowed: tu, ton, ta, tes, toi; the imperative on buttons; mixing vous and impersonal forms in one message.

## 3. Script and typography

- Write the accents on capital letters too: "État", "Élément", "Êtes-vous", "À propos". Write the ligature in
  "nœud", "œuvre".
- **Space before two-part punctuation**: a space before ":", ";", "?" and "!": "Statut : %status",
  "Voulez-vous vraiment supprimer la langue ?". A non-breaking space (U+00A0) is welcome there, a plain space is
  accepted; never no space at all.
- **Quotation marks**: French guillemets with spaces inside, « ... », for words and names in running text: « %name ».
  Where the source quotes a button label or a value with straight quotes, straight quotes are also accepted.
- **Apostrophe**: the straight apostrophe ' as in the rest of the catalogue: "l'objet", "d'utilisateurs".
- **Numbers**: digits 0-9, decimals with a comma in prose ("1,5 Mo"); numbers that come in through placeholders
  are formatted by the locale.
- **Capitals**: only the first word of a button, a title or a heading starts with a capital: "Supprimer la
  sélection", "Classes de contenu". No title case.
- **Ellipsis**: "..." or "…" as in the source.

## 4. Glossary

Binding for the kernel and every extension.

| English | French | Note |
|---|---|---|
| content | contenu | |
| content object | objet de contenu | |
| object | objet | |
| node | nœud | |
| location | emplacement | the place of an object in the tree |
| main location | emplacement principal | |
| site | site | |
| class | classe | the content class; a PHP class is une classe PHP |
| content class | classe de contenu | |
| class group | groupe de classes | |
| attribute | attribut | |
| datatype | type de données | |
| identifier | identifiant | |
| remote ID | ID distant | |
| version | version | |
| draft | brouillon | |
| publish | publier | |
| published | publié | |
| send for publishing | envoyer pour publication | |
| store draft | enregistrer le brouillon | |
| discard | abandonner | discard draft: abandonner le brouillon |
| archived | archivé | an archived version |
| pending | en attente | |
| rejected | rejeté | |
| siteaccess | siteaccess | stays in English, masculine: le siteaccess, ce siteaccess |
| section | section | always the Exponential section |
| role | rôle | |
| policy | règle | a policy of a role; see section 5 |
| limitation | limitation | |
| module | module | |
| function | fonction | |
| permission | permission | |
| user | utilisateur | |
| user group | groupe d'utilisateurs | |
| user account | compte utilisateur | |
| log in | se connecter | the action: connexion |
| login (the name) | identifiant | |
| username | nom d'utilisateur | |
| log out | se déconnecter | the action: déconnexion |
| password | mot de passe | |
| e-mail | e-mail | adresse e-mail |
| cache | cache | masculine: le cache; clear cache: vider le cache |
| template | template | masculine: le template; override template: template de surcharge |
| override | surcharge | the verb: surcharger |
| fallback | repli | fallback language: langue de repli |
| workflow | workflow | masculine: le workflow |
| trigger | déclencheur | |
| event | évènement | |
| trash | corbeille | empty trash: vider la corbeille |
| bookmarks | signets | |
| settings | paramètres | |
| configuration | configuration | |
| setup | administration | the Setup tab; the setup wizard: installation |
| dashboard | tableau de bord | |
| layout | mise en page | Exponential Layouts; layouts: mises en page |
| block | bloc | a block of a layout |
| zone | zone | |
| design | design | |
| extension | extension | |
| package | paquetage | |
| kernel | noyau | |
| search | recherche | the button: Rechercher |
| search index | index de recherche | indexing: indexation |
| search engine | moteur de recherche | |
| log | journal | a log file; logs: journaux |
| queue | file d'attente | queued: en file d'attente |
| job | tâche | cron jobs: tâches cron |
| cronjob | tâche cron | |
| file | fichier | |
| folder | dossier | |
| image | image | |
| thumbnail | miniature | |
| media | média | media library: médiathèque |
| preview | prévisualisation | the button: Prévisualiser |
| upload | charger | the action: chargement; never télécharger |
| download | télécharger | the action: téléchargement |
| export | exporter | the noun: export |
| import | importer | the noun: import |
| remove | supprimer | retirer where the item only leaves a list or place and remains |
| delete | supprimer | destroy |
| edit | modifier | not éditer |
| editor | éditeur | the editing software; a person who edits: rédacteur |
| save | enregistrer | |
| store | enregistrer | the verb "Store" of the forms |
| Store / shop | boutique | the shop |
| cancel | annuler | |
| OK | OK | |
| back | retour | |
| next / previous | suivant / précédent | |
| enable / disable | activer / désactiver | |
| enabled / disabled | activé / désactivé | |
| active / inactive | actif / inactif | |
| hidden / visible | masqué / visible | hide: masquer; the column heading Hidden: Caché |
| apply | appliquer | |
| reset | réinitialiser | |
| clear | vider | to empty a cache, a list or a field |
| archive | archive | |
| order (sort) | ordre | sort by: trier par |
| order (shop) | commande | orders: commandes |
| ascending / descending | croissant / décroissant | |
| basket | panier | |
| wish list | liste de suggestions | |
| product | produit | |
| customer | client | |
| discount | remise | |
| VAT | TVA | |
| tag (keyword) | mot-clé | tags: mots-clés (eztags) |
| tag (HTML/XML) | balise | |
| translation | traduction | |
| language | langue | main language: langue principale |
| locale | localisation | `Locale` as a setting name stays |
| state | état | the object state; object states: états d'objet |
| status | statut | |
| view (button) | voir | |
| view (noun) | vue | a module view |
| format | format | |
| select | sélectionner | invert selection: inverser la sélection |
| browse | parcourir | |
| copy | copier | |
| move | déplacer | |
| add | ajouter | |
| create | créer | created: créé |
| new | nouveau | |
| restore | restaurer | |
| install / uninstall | installer / désinstaller | |
| sub items | sous-éléments | |
| children / parent | enfants / parent | |
| subtree | sous-arborescence | |
| related objects | objets associés | relations: relations |
| URL alias | alias d'URL | |
| content structure | contenus | the tab; in running text: structure du contenu |
| owner | propriétaire | |
| creator / author | créateur / auteur | |
| modified | modifié | |
| priority | priorité | |
| session | session | |
| debug | débogage | |
| default | par défaut | |
| none | aucun / aucune | agrees with the noun |
| yes / no | oui / non | |
| help | aide | |

## 5. Decisions after the translation

- **Form of address**: vous everywhere, decided on 18 October 2026. The catalogue had no tu before, and it stays
  that way.
- **siteaccess**, **template** and **workflow** stay in English as masculine nouns. They are configuration terms that
  appear in `site.ini`, in paths and in the documentation; the catalogue uses them so in nearly every message.
  Only the menu title "Templates" reads "Modèles".
- **policy** is règle (a rule of a role). Some older titles read "Politiques de sécurité"; new texts use règle(s).
- **upload** is charger, never télécharger: télécharger is download, and the two must not be confused in one
  interface. Téléverser, found in a few newer messages, is understood but not used for new texts.
- **edit** is modifier. Éditer appears in a few older messages and is not used for new texts.
