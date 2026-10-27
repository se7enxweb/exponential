# Sundanese (sun-ID): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026, machine-assisted and adapted from the Indonesian catalogue (`ind-ID`) where the terms are shared; a
review by native speakers is welcome.

## 1. Language and register

Standard Sundanese (basa Sunda) as written in West Java, in the neutral, polite register of Sundanese websites,
Wikipedia (su) and public notices: **loma** word forms for things, with the polite forms where the user's own
actions are meant (anjeun, mangga, punten). No full lemes (high) register and no slang. No other locale falls back
to `sun-ID`.

- Indonesian is the source of many shared technical words (berkas, unggah, unduh, basis data), but the grammar, the
  function words and the everyday vocabulary are Sundanese: ieu, éta, nu, henteu (teu), acan, parantos (geus), badé,
  tiasa (bisa), pikeun, ti, ka, sareng (jeung), atanapi (atawa), tapi, upami (lamun). Never leave an Indonesian
  sentence as it is: "Apakah Anda yakin ingin menghapus kelas ini?" becomes "Naha anjeun yakin badé ngahapus kelas
  ieu?".
- Short, plain sentences. Use the polite function words of the list above (parantos, badé, tiasa, sareng, atanapi,
  upami) throughout, so the register does not change from one sentence to the next.

## 2. Form of address

- The user is **anjeun**. Never manéh, salira or Anda.
- **Buttons, menu items and links**: the bare root or a short verb phrase: Simpen, Hapus, Bolaykeun, Édit,
  Tambahkeun, Jieun, Unggah, Unduh, Paluruh, Pilih, Kirim, Terbitkeun, Simpen draf, Pindahkeun ka tong runtah.
- **Titles, column headings and tabs**: nouns or noun phrases: Setélan, Tarjamahan, Lokasi utama, Ngaran.
- **Instructions**: the imperative root, with "mangga" where English has "please": "Pilih item lajeng klik
  \"OK\".", "Mangga antosan dugi ka prioritas parantos disimpen.". A reference ("see ...") is "tingali ...".
- **Confirmations and questions**: "Naha anjeun yakin badé ...?": "Naha anjeun yakin badé ngahapus kelas ieu?".
- **What the user can or may do**: "Anjeun tiasa ...", "Anjeun teu tiasa ...", or impersonal: "Basa utama teu
  tiasa dirobih ...". "You do not have permission" is "Anjeun teu gaduh idin ...".
- **Messages about a result**: passive with di- and "parantos": "Draf parantos disimpen.", "Berkas parantos
  hasil diunggah.".

## 3. Script and typography

- **Latin script** in the standard spelling (Éjahan Basa Sunda): **é** for the clear e (édit, vérsi, objék), plain e
  for the pepet (setélan), **eu** for the Sundanese vowel (euweuh, eusi, teu). Not the Sundanese script (aksara
  Sunda).
- **Capitals**: only the first word of a button, title or sentence and proper names: "Édit draf", not "Édit Draf".
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after. Never glue -na, -eun or a prefix to a placeholder: "%name-na" or a
  rewording, never "%namena". The same for code, file names, INI settings and product names.
- **Quotation marks**: straight double quotes "..." or the ones of the source; `&quot;` and other entities are kept
  exactly.
- **Punctuation**: as in the source; no space before colon, question or exclamation mark.
- **Numbers**: digits 0-9. Sundanese does not inflect for number: "%count objék".

## 4. Glossary

Binding for the kernel and every extension.

| English | Sundanese | Note |
|---|---|---|
| content | eusi | content object: objék eusi |
| object | objék | |
| node | simpul | main node: simpul utama; parent node: simpul indung |
| location | lokasi | main location: lokasi utama |
| item (list entry) | item | |
| class | kelas | the content class; class group: grup kelas; a PHP class: kelas PHP |
| content class | kelas eusi | |
| attribute | atribut | |
| datatype | tipe data | |
| version | vérsi | version history: riwayat vérsi |
| draft | draf | store draft: simpen draf |
| publish | terbitkeun | published: parantos diterbitkeun; publishing: panerbitan |
| send for publishing | kirim pikeun diterbitkeun | |
| discard | piceun | discard draft: piceun draf |
| archived | diarsipkeun | |
| pending | nuju ngantosan | |
| rejected | ditolak | |
| siteaccess | aksés situs | siteaccess as a setting name stays in code |
| site, website | situs | |
| section | séksi | the Exponential section; an INI section: bagéan |
| role | peran | |
| policy | kawijakan | one rule of a role |
| limitation | watesan | |
| permission | idin | access denied: aksés ditolak |
| module | modul | |
| function | fungsi | |
| user | pamaké | |
| user group | grup pamaké | |
| group | grup | |
| anonymous | anonim | |
| administrator | pangurus | administration (interface): administrasi |
| username | ngaran pamaké | login (the name): ngaran login |
| password | sandi | |
| log in / sign in | asup | the act: prosés asup |
| log out / sign out | kaluar | |
| e-mail | surélék | e-mail address: alamat surélék |
| cache | cache | clear cache: beresihan cache |
| template | citakan | override template: citakan timpaan |
| override | timpaan | to override: nimpa |
| fallback | cadangan | fallback language: basa cadangan |
| design | desain | |
| workflow | alur gawé | |
| trigger | pemicu | |
| event | kajadian | |
| trash | tong runtah | empty trash: kosongkeun tong runtah; move to trash: pindahkeun ka tong runtah |
| bookmark | markah | |
| settings | setélan | configuration: konfigurasi |
| setup | panyetélan | the Setup tab |
| layout | tata perenah | Exponential Layouts |
| block / zone | blok / zona | |
| extension | éksténsi | |
| package | pakét | |
| search | paluruh | search (noun): pamaluruhan; search index: indéks pamaluruhan; indexing: ngindéks |
| log | log | a log file: berkas log; never "log in" |
| queue | antrian | job queue: antrian tugas |
| job / task | tugas | |
| cron | cron | a cron job: tugas cron |
| script | skrip | |
| worker | prosés pagawé | |
| kernel, core | inti | |
| server | server | |
| database | basis data | |
| folder / directory | map | |
| file | berkas | |
| image | gambar | thumbnail: gambar leutik |
| preview | pratinjau | |
| upload / download | unggah / unduh | |
| export / import | ékspor / impor | |
| remove | kaluarkeun | take out of a list or place: kaluarkeun tina ... |
| delete | hapus | destroy; deleted: parantos dihapus |
| edit | édit | |
| editor | éditor | a person: nu ngédit |
| save / store | simpen | the verb "Store" of the forms |
| Store (shop tab) | toko | the shop, as "Shop" |
| cancel | bolaykeun | |
| OK | OK | kept (allowlisted) |
| apply | terapkeun | |
| back | balik | |
| next / previous | salajengna / saméméhna | |
| enable / disable | aktipkeun / pareuman | enabled / disabled: aktip / pareum |
| active / inactive | aktip / teu aktip | |
| run | jalankeun | a script, a job |
| start / stop | mimitian / eureunkeun | |
| session | sési | |
| hidden / visible | disumputkeun / katingali | hide / reveal: sumputkeun / témbongkeun |
| archive | arsip | a file archive: arsip berkas |
| order (sort) | urutan | sort order: urutan susunan |
| order (shop) | pesenan | |
| shop | toko | |
| basket | karanjang | |
| product | produk | |
| customer | palanggan | |
| discount | diskon | |
| VAT | PPN | |
| price / currency | harga / mata uang | |
| tag | tag | a keyword tag; an HTML tag: tag HTML |
| translation | tarjamahan | |
| language | basa | main language: basa utama; interface language: basa antarbeungeut |
| locale | lokal | |
| state | kaayaan | the object state; state group: grup kaayaan |
| status | status | kept apart from kaayaan |
| view (button) | tingali | a module view (noun): tampilan |
| format | format | |
| sort | susun | |
| filter | saringan | the act: saring |
| select | pilih | select all: pilih sadayana; selected: nu dipilih |
| browse | sungsi | |
| copy / move | tiron / pindahkeun | |
| add / create / new | tambahkeun / jieun / énggal | |
| restore | pulihkeun | |
| install / uninstall | pasang / cabut pamasangan | |
| sub items | sub-item | |
| subtree | cabang | |
| related objects | objék nu patali | relation: hubungan |
| URL alias | alias URL | |
| redirect | alihkeun | |
| dashboard | dasbor | |
| content structure | struktur eusi | |
| media library | perpustakaan média | |
| identifier / ID | idéntifikasi / ID | |
| priority | prioritas | |
| owner | nu gaduh | |
| modified / created | dirobih / dijieun | |
| date / time | tanggal / waktos | |
| error / warning | kasalahan / pépéling | |
| help | pitulung | |
| notification | béja | |
| wizard | pituduh | setup wizard: pituduh panyetélan |
| feed (RSS) | feed | |
| tab | tab | |
| account | akun | |
| default | standar | default value: nilai standar |
| toolbar | bar pakakas | |
| container | wadah | |
| collection | koléksi | |
| approve, approval | satujuan, persetujuan | |
| request / query | paménta / kueri | |
| backup | cadangan data | |
| maintenance | pangropéa | |
| link | tumbu | |
| registration | pendaptaran | register: ngadaptar |
| device / browser | alat / panyungsi | |
| none / all | euweuh / sadayana | |
| yes / no | enya / henteu | |
| name / title / description | ngaran / judul / pedaran | |
| value / type / list | nilai / tipe / daptar | |
| page / path | kaca / jalur | |
| required / optional | wajib / pilihan | |
| options | pilihan | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is toko, the word the shop menu uses for "Shop"; the verb "Store" of the forms
  is simpen.
- "Login" in the user, session and audit lists is the user's login name (ngaran login); the Login button of the
  error page is the act (Asup).
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay in Latin letters (Exponential, PHP, URL, ID, OK ...) and words spelled
  the same in Sundanese are listed with their reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json`
  when a whole message consists of them.
