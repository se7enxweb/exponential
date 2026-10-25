# Catalan (cat-ES): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated and cross-checked in October 2026; review by native speakers is welcome.

## 1. Language and register

Standard Catalan (IEC orthography, Diccionari de la llengua catalana, norma of the Institut d'Estudis Catalans), as
written in Catalonia; Valencian and Balearic forms are not used. The tone is plain and direct, as in Softcatalà, KDE
and GNOME Catalan, Mozilla ca and Microsoft Catalan; terminology follows Softcatalà/Microsoft where they agree, the
Termcat dictionaries where they do not. No Castilian calques ("fitxer", not "arxiu"; "desa", not "guarda"; "suprimeix", not "borra"); `locale_check.py` reports a list of Castilian forms (`castil`).

**The old catalogue** (6,315 of 6,871 messages finished, 96 percent of an old release, hand-made) is the `prev` hint.
Its conventions stay: the imperative of the second person singular on buttons (Edita, Esborra, Desa, Crea), "tu" in
sentences (275 uses, none of "vostè"), the middle dot in l·l (174 uses), the straight apostrophe (1,414 uses), the
terms Node, Classe, Versió, Esborrany, Paperera, Rol, Plantilla, Objecte, Fitxer. Corrected where the hint shows them:

| Old | New | Why |
|---|---|---|
| Emmagatzema for Store | Desa | "emmagatzemar" is a technical word for storage, not the button |
| Esborra for Remove | Treu | remove and delete are kept apart |
| Preferits for bookmarks | Adreces d'interès | "Preferits" is the browser's word; here it is a list kept by the CMS |
| Alerta for warning | Avís | Microsoft/Softcatalà |
| Sobreescriu (override) | Sobreescriu | spelling "sobreescriure" kept (DIEC2: sobreescriure) |
| Escriptori for dashboard | Tauler | |
| Accés al lloc for siteaccess | Accés al lloc | kept |

## 2. Form of address

The user is addressed with **tu** (te, teu, teva), never "vostè". Softcatalà, KDE, GNOME and Mozilla Catalan, and the
old catalogue, do so; so does Microsoft in its consumer products. The pronoun is dropped wherever the sentence
allows it.

- **Buttons, menu items, links and commands** use the **imperative singular**: Desa, Edita, Esborra, Treu, Afegeix,
  Crea, Cancel·la, Aplica, Publica, Puja, Baixa, Tanca, Torna, Selecciona. Fixed forms: D'acord, Sí, No, Enrere,
  Endavant, Següent, Anterior.
- **Titles, column headings and tabs** are nouns: Estructura de continguts, Comptes d'usuari, Paràmetres de notificació.
- **Sentences, hints and instructions** use the imperative: "Selecciona una ubicació.", "Introdueix una adreça de
  correu electrònic vàlida.", "Comprova la connexió i torna-ho a provar." "Please" is not translated ("si us plau"
  only where the source is a dialog's polite request).
- **Questions and confirmations**: "Segur que vols suprimir aquesta classe?"
- **Error messages** state the fact: "No s'ha pogut crear l'esborrany.", "No s'ha trobat el fitxer.", "No tens permís per
  suprimir classes."
- **Status messages**: "S'ha suprimit l'esborrany.", "S'han desat els paràmetres.", "S'han suprimit %count objectes."
- Not allowed: vostè/vostès; the infinitive on a button (Desar, Editar); Castilian forms.

## 3. Script and typography

- **Alphabet**: Latin with à è é í ï ò ó ú ü ç and the **middle dot** in the geminate l·l: **U+00B7** (·), never a
  full stop (l.l), a bullet (l•l) or a dot operator (l⋅l). Examples: cancel·la, col·lecció, intel·ligent, paral·lel.
  `locale_check.py` reports the wrong forms (`mdot`).
- **Apostrophe**: the straight ' (U+0027), as in the old catalogue, Softcatalà and Microsoft: l'objecte, d'usuari,
  s'ha desat. No typographic ’ (reported as `apos`). Elision follows the norm: l' before a vowel/h, d', s', m', n', t'.
- **Contractions**: del, dels, al, als, pel, pels (never "de el", "a el", "per el"; reported as `contr`); "ca l'" ...
- **Quotation marks**: «…» for quoted text, button and field names ("Prem «Desa»."); nested “…”. Straight quotes of
  the source stay inside HTML attributes, code and values typed exactly.
- **Punctuation**: the source's. The interrogative and exclamatory marks are single (no ¿ ¡). The colon after a label
  as in the source. The ellipsis is the source's ("..." stays "...", "…" stays "…").
- **Case**: sentence case ("Estructura de continguts", "Crea un objecte"); months and days lower case.
- **Gender and number**: the placeholder does not change the article; write the neutral form ("Objectes: %count",
  "S'han suprimit %count objectes.") or two-form "objecte(s)" only where the source has "(s)".
- **Numbers and units**: digits 0-9; thousands separator and decimals from the locale; space before a unit (10 MB).
- **Placeholders** stand free (TRANSLATING.md, rule 1); never elide into one ("d'%name" is fine since the apostrophe
  ends before the %; "l'%name" likewise), but nothing is glued after it that makes it longer.

## 4. Glossary

Binding for the kernel and every extension. Section 5 adds the settled special cases and goes first.

| English | Catalan | Note |
|---|---|---|
| content | contingut | |
| content object / object | objecte de contingut / objecte | |
| item (list entry) | element | sub items: subelements |
| node / main node | node / node principal | |
| parent node / child node | node pare / node fill | |
| location / main location | ubicació / ubicació principal | the old catalogue's "localització" is kept for geography |
| content class / class | classe de contingut / classe | a PHP class: classe PHP |
| class group | grup de classes | |
| attribute | atribut | |
| datatype | tipus de dades | |
| version / version history | versió / historial de versions | |
| draft | esborrany | Store draft: Desa l'esborrany; Discard draft: Descarta l'esborrany |
| publish / published | publica / publicat | Send for publishing: Envia per a publicar |
| translation | traducció | |
| language / main language / content language | idioma / idioma principal / idioma del contingut | |
| interface language | idioma de la interfície | |
| siteaccess | accés al lloc | the INI word SiteAccess stays in code |
| site / website | lloc / lloc web | |
| design | disseny | |
| template | plantilla | |
| override / override template | sobreescriptura / plantilla sobreescrita | verb: sobreescriu |
| fallback | alternativa | fallback language: idioma alternatiu |
| URL alias / URL wildcard | àlies d'URL / comodí d'URL | |
| redirect | redirecció | |
| section (Exponential) | secció | a part of a page: apartat |
| object state / state group | estat de l'objecte / grup d'estats | |
| status | estat | |
| trash / empty trash | paperera / buida la paperera | |
| restore | restaura | |
| bookmark | adreça d'interès | |
| notification | notificació | |
| dashboard | tauler | |
| content structure / media library | estructura de continguts / mediateca | |
| collected information | informació recollida | |
| form | formulari | |
| poll / vote | enquesta / vot | |
| comment | comentari | in code: comentari |
| newsletter / subscription | butlletí / subscripció | |
| feed / RSS feed | canal / canal RSS | |
| user / user group | usuari / grup d'usuaris | |
| user account / account | compte d'usuari / compte | |
| username | nom d'usuari | |
| password | contrasenya | |
| anonymous user | usuari anònim | |
| administrator / admin interface | administrador / interfície d'administració | |
| log in / log out | inicia la sessió / tanca la sessió | Login as a field: Nom d'usuari |
| sign up, register | registra't | |
| role / policy | rol / política | |
| permission / access | permís / accés | |
| access denied | accés denegat | |
| limitation (policy) | limitació | |
| assign / unassign | assigna / desassigna | |
| approve / reject / approval | aprova / rebutja / aprovació | |
| pending | pendent | |
| owner / author / creator | propietari / autor / creador | |
| editor (person) / editor (software) | editor / editor | rich text editor: editor de text enriquit |
| save / store | desa | |
| edit | edita | |
| delete / remove | suprimeix / treu | remove = take out of a list or place; delete = destroy |
| cancel / OK / apply | cancel·la / D'acord / aplica | |
| discard | descarta | |
| create / new / add | crea / nou / afegeix | |
| copy / move / swap | copia / mou / intercanvia | |
| back / next / previous | enrere / següent / anterior | |
| open / close | obre / tanca | |
| select / choose | selecciona / tria | select all: selecciona-ho tot |
| browse | explora | |
| preview | previsualitza | noun: vista prèvia |
| view (button) / view (module view) | mostra / vista | |
| upload / download | puja / baixa | |
| import / export | importa / exporta | |
| search / advanced search | cerca / cerca avançada | |
| filter | filtra | noun: filtre |
| sort / sort order | ordena / ordre | |
| refresh / reload | actualitza / recarrega | |
| enable / disable | activa / desactiva | |
| activate / deactivate | activa / desactiva | |
| install / uninstall | instal·la / desinstal·la | |
| update / upgrade | actualitza / actualització de versió | |
| click / double-click | fes clic / fes doble clic | |
| drag / drop | arrossega / deixa anar | |
| print | imprimeix | |
| retry | torna-ho a provar | |
| file | fitxer | |
| folder / directory | carpeta / directori | |
| path / file system | camí / sistema de fitxers | |
| binary file | fitxer binari | |
| image / image alias | imatge / àlies d'imatge | |
| video / audio | vídeo / àudio | |
| font | tipus de lletra | |
| database / table | base de dades / taula | |
| field | camp | |
| column / row | columna / fila | |
| data / information | dades / informació | |
| message | missatge | |
| cache / clear cache | memòria cau / buida la memòria cau | |
| index / reindex | índex / reindexa | |
| search engine | motor de cerca | |
| keyword | paraula clau | |
| tag (keyword) / tag (HTML) | etiqueta / etiqueta HTML | |
| format / formatting | format / formatació | |
| settings / setup | paràmetres / configuració | Setup (admin tab): Configuració |
| configuration | configuració | |
| INI file / INI setting | fitxer INI / paràmetre INI | |
| default | per defecte | |
| cron job | tasca cron | |
| package | paquet | |
| extension | extensió | |
| plugin / provider | connector / proveïdor | |
| server / web server | servidor / servidor web | |
| network | xarxa | |
| software / program | programari / programa | |
| code / source code | codi / codi font | |
| function / method | funció / mètode | |
| variable / parameter | variable / paràmetre | |
| string / character | cadena / caràcter | |
| encoding | codificació | |
| interface | interfície | |
| module | mòdul | |
| link / broken link | enllaç / enllaç trencat | |
| menu / context menu | menú / menú contextual | |
| toolbar / sidebar | barra d'eines / barra lateral | |
| tab | pestanya | |
| page / layout | pàgina / disposició | Exponential Layouts: disposicions |
| block / zone | bloc / zona | |
| navigation / breadcrumb | navegació / ruta de navegació | |
| icon / button | icona / botó | |
| checkbox / drop-down list / list | casella de selecció / llista desplegable / llista | |
| dialog / wizard | diàleg / assistent | |
| window / browser | finestra / navegador | |
| help / about | ajuda / quant a | |
| log (file) / log viewer | registre / visualitzador de registres | never the verb "log in" |
| debug | depuració | |
| error / warning / notice | error / avís / nota | |
| report / statistics | informe / estadístiques | |
| modified / created | modificat / creat | column headings |
| name / description / title | nom / descripció / títol | |
| identifier / ID / remote ID | identificador / ID / ID remot | |
| remote / local | remot / local | |
| hidden / visible | ocult / visible | |
| priority | prioritat | |
| unknown / none / all / any | desconegut / cap / tots / qualsevol | |
| archive (versions, orders) / archive file | arxiu / fitxer comprimit | |
| backup | còpia de seguretat | |
| storage / disk space | emmagatzematge / espai de disc | |
| memory / performance | memòria / rendiment | |
| process / thread | procés / fil | |
| queue / job / task | cua / treball / tasca | |
| schedule | planificació | |
| timeout | temps d'espera | |
| request / response | sol·licitud / resposta | |
| session / cookie | sessió / galeta | |
| token / API key | testimoni / clau d'API | |
| credentials / certificate | credencials / certificat | |
| authentication / two-factor | autenticació / autenticació de dos factors | |
| encryption | xifratge | |
| locale / time zone | configuració regional / fus horari | |
| project | projecte | |
| shop / webshop | botiga / botiga en línia | |
| order (shop) / order (sort) | comanda / ordre | |
| basket / checkout / wish list | cistella / tramitació de la comanda / llista de desitjos | |
| product / price / discount / currency | producte / preu / descompte / divisa | |
| customer | client | |
| VAT | IVA | |
| AI / model / prompt | IA / model / indicació | |
| embedding / vector / chunk | incrustació / vector / fragment | |
| health check | comprovació d'estat | |
| confirmation question | Segur que vols …? | |

## 5. Decisions after the translation

Settled before the round (the context-dependent words of the previous rounds). They take precedence over the glossary.

| English | Translation | Distinction |
|---|---|---|
| Store (button: save) | Desa | the shop tab of the admin header (kernel\|design/admin/pagelayout): Botiga; secrets: context override |
| Order (shop) / Order (sort order) | comanda / ordre | the context decides: context overrides |
| State (object state) / State (address) | estat / província | the address field of the shop: Província o estat |
| Login (button, verb) / Login (the user name) | Inicia la sessió / Nom d'usuari | |
| View (button) / View (module view) | Mostra / Vista | |
| Up / Down (move in a list) | Amunt / Avall | expqueue "Up" (how long a worker runs): Temps d'activitat |
| Top / Bottom (position in an order) | A dalt / A baix | |
| Delete (the key) | tecla Supr | key names of the keyboard layout; Enter and Esc stay |
| True / False (setting values) | Cert / Fals | code literals true / false stay Latin |
