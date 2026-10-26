# Javanese (jav-ID): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026, machine-assisted and adapted from the Indonesian catalogue (`ind-ID`) where the terms are shared; a
review by native speakers is welcome.

## 1. Language and register

Standard Javanese (basa Jawa) as written in Central and East Java, in **ngoko alus**: the everyday ngoko word forms,
with the respectful krama inggil words for what belongs to the user (panjenengan, kagungan). This is the register of
Javanese websites, Wikipedia (jv) and public notices that address a reader politely without full krama. No other
locale falls back to `jav-ID`.

- Indonesian is the source of many shared technical words (berkas, unggah, unduh, basis data), but the grammar, the
  function words and the everyday vocabulary are Javanese: iki, kuwi, sing, ora, durung, wis, arep, bisa, kanggo,
  saka, menyang, karo, utawa, nanging, yen. Never leave an Indonesian sentence as it is: "Apakah Anda yakin ingin
  menghapus kelas ini?" becomes "Apa panjenengan yakin arep mbusak kelas iki?".
- Short, plain sentences. No regional slang (Suroboyoan, Banyumasan).

## 2. Form of address

- The user is **panjenengan**. Never kowe, sampeyan or Anda.
- **Buttons, menu items and links**: the bare root (tembung lingga) or a short verb phrase, as Javanese Wikipedia
  does: Simpen, Busak, Batal, Sunting, Tambah, Gawe, Unggah, Undhuh, Golek, Pilih, Kirim, Terbitake, Simpen draf,
  Pindhah menyang tong sampah.
- **Titles, column headings and tabs**: nouns or noun phrases: Setelan, Terjemahan, Lokasi utama, Jeneng.
- **Instructions**: the polite imperative with -en/-na or the root, with "mangga" where English has "please":
  "Pilih item banjur klik \"OK\".", "Mangga ngenteni nganti prioritas wis kasimpen.". A reference ("see ...") is
  "deleng ...".
- **Confirmations and questions**: "Apa panjenengan yakin arep ...?": "Apa panjenengan yakin arep mbusak kelas iki?".
- **What the user can or may do**: "Panjenengan bisa ...", "Panjenengan ora bisa ...", or impersonal: "Basa utama ora
  bisa diganti ...". "You do not have permission" is "Panjenengan ora duwe idin ...".
- **Messages about a result**: passive with di- or ka- and "wis": "Draf wis kasimpen.", "Berkas wis kasil diunggah.".

## 3. Script and typography

- **Latin script** in the standard spelling: dh and th for the retroflex consonants (dhaptar, kathah), ny, ng; "a"
  for the open a in writing (apa, sega, not opo). **No diacritics**: e for both é and è (sabanjure, setelan), so the
  same word is always spelled the same. Not the Javanese script (aksara Jawa).
- **Capitals**: only the first word of a button, title or sentence and proper names: "Sunting draf", not
  "Sunting Draf".
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after. Never glue -e, -ne, -ku or a prefix to a placeholder: "%name-e" or a
  rewording, never "%namee". The same for code, file names, INI settings and product names.
- **Quotation marks**: straight double quotes "..." or the ones of the source; `&quot;` and other entities are kept
  exactly.
- **Punctuation**: as in the source; no space before colon, question or exclamation mark.
- **Numbers**: digits 0-9. Javanese does not inflect for number: "%count objek"; reduplication (objek-objek) only
  where the sentence needs it.

## 4. Glossary

Binding for the kernel and every extension.

| English | Javanese | Note |
|---|---|---|
| content | isi | content object: objek isi |
| object | objek | |
| node | simpul | main node: simpul utama; parent node: simpul induk |
| location | lokasi | main location: lokasi utama |
| item (list entry) | item | |
| class | kelas | the content class; class group: klompok kelas; a PHP class: kelas PHP |
| content class | kelas isi | |
| attribute | atribut | |
| datatype | jinis data | |
| version | versi | version history: riwayat versi |
| draft | draf | store draft: simpen draf |
| publish | terbitake | published: wis diterbitake; publishing: panerbitan |
| send for publishing | kirim kanggo diterbitake | |
| discard | buang | discard draft: buang draf |
| archived | diarsipake | |
| pending | isih ngenteni | |
| rejected | ditolak | |
| siteaccess | akses situs | siteaccess as a setting name stays in code |
| site, website | situs | |
| section | seksi | the Exponential section; an INI section: bagean |
| role | peran | |
| policy | kawicaksanan | one rule of a role |
| limitation | watesan | |
| permission | idin | access denied: akses ditolak |
| module | modul | |
| function | fungsi | |
| user | panganggo | |
| user group | klompok panganggo | |
| group | klompok | |
| anonymous | anonim | |
| administrator | pangurus | administration (interface): administrasi |
| username | jeneng panganggo | login (the name): jeneng login |
| password | tembung sandhi | |
| log in / sign in | mlebu | the act: proses mlebu |
| log out / sign out | metu | |
| e-mail | email | e-mail address: alamat email |
| cache | cache | clear cache: resiki cache |
| template | cithakan | override template: cithakan timpan |
| override | timpan | to override: nimpa |
| fallback | serep | fallback language: basa serep |
| design | desain | |
| workflow | alur kerja | |
| trigger | pemicu | |
| event | kedadeyan | |
| trash | tong sampah | empty trash: kosongake tong sampah; move to trash: pindhah menyang tong sampah |
| bookmark | tetenger | |
| settings | setelan | configuration: konfigurasi |
| setup | panyiapan | the Setup tab |
| layout | tata letak | Exponential Layouts |
| block / zone | blok / zona | |
| extension | ekstensi | |
| package | paket | |
| search | golek | search (noun): panggolekan; search index: indeks panggolekan; indexing: pangindeksan |
| log | log | a log file: berkas log; never "log in" |
| queue | antrian | job queue: antrian tugas |
| job / task | tugas | |
| cron | cron | a cron job: tugas cron |
| script | skrip | |
| worker | proses pekerja | |
| kernel, core | inti | |
| server | server | |
| database | basis data | |
| folder / directory | folder | |
| file | berkas | |
| image | gambar | thumbnail: gambar cilik |
| preview | pratinjau | |
| upload / download | unggah / undhuh | |
| export / import | ekspor / impor | |
| remove | copot | take out of a list or place: copot saka ... |
| delete | busak | destroy; deleted: wis dibusak |
| edit | sunting | |
| editor | panyunting | the software: piranti panyunting |
| save / store | simpen | the verb "Store" of the forms |
| Store (shop tab) | toko | the shop, as "Shop" |
| cancel | batal | |
| OK | OK | kept (allowlisted) |
| apply | trapake | |
| back | bali | |
| next / previous | sabanjure / sadurunge | |
| enable / disable | aktifake / pateni | enabled / disabled: aktif / mati |
| active / inactive | aktif / ora aktif | |
| run | lakokake | a script, a job |
| start / stop | wiwiti / mandheg | |
| session | sesi | |
| hidden / visible | didhelikake / katon | hide / reveal: dhelikake / tampilake |
| archive | arsip | a file archive: arsip berkas |
| order (sort) | urutan | sort order: urutan sortir |
| order (shop) | pesenan | |
| shop | toko | |
| basket | kranjang | |
| product | produk | |
| customer | pelanggan | |
| discount | diskon | |
| VAT | PPN | |
| price / currency | rega / mata uang | |
| tag | tag | a keyword tag; an HTML tag: tag HTML |
| translation | terjemahan | |
| language | basa | main language: basa utama; interface language: basa antarmuka |
| locale | lokal | |
| state | kahanan | the object state; state group: klompok kahanan |
| status | status | kept apart from kahanan |
| view (button) | deleng | a module view (noun): tampilan |
| format | format | |
| sort | urutake | |
| filter | saringan | the act: saring |
| select | pilih | select all: pilih kabeh; selected: sing dipilih |
| browse | jlajah | |
| copy / move | salin / pindhah | |
| add / create / new | tambah / gawe / anyar | |
| restore | pulihake | |
| install / uninstall | pasang / copot pasangan | |
| sub items | sub-item | |
| subtree | cabang | |
| related objects | objek sing gegandhengan | relation: sesambungan |
| URL alias | alias URL | |
| redirect | alihake | |
| dashboard | dasbor | |
| content structure | struktur isi | |
| media library | perpustakaan media | |
| identifier / ID | pengenal / ID | |
| priority | prioritas | |
| owner | sing nduwe | |
| modified / created | diowahi / digawe | |
| date / time | tanggal / wektu | |
| error / warning | kasalahan / pengetan | |
| help | pitulung | |
| notification | kabar | |
| wizard | panuntun | setup wizard: panuntun panyiapan |
| feed (RSS) | umpan | |
| tab | tab | |
| account | akun | |
| default | gawan | default value: nilai gawan |
| toolbar | bilah piranti | |
| container | wadhah | |
| collection | koleksi | |
| approve, approval | setujoni, persetujuan | |
| request / query | panjaluk / kueri | |
| backup | serep data | |
| maintenance | pangopenan | |
| link | pranala | |
| registration | registrasi | register: ndhaptar |
| device / browser | piranti / panjlajah | |
| none / all | ora ana / kabeh | |
| yes / no | ya / ora | |
| name / title / description | jeneng / irah-irahan / katrangan | |
| value / type / list | nilai / jinis / dhaptar | |
| page / path | kaca / path | |
| required / optional | wajib / opsional | |
| options | pilihan | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is toko, the word the shop menu uses for "Shop"; the verb "Store" of the forms
  is simpen.
- "Login" in the user, session and audit lists is the user's login name (jeneng login); the Login button of the
  error page is the act (Mlebu).
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay in Latin letters (Exponential, PHP, URL, ID, OK ...) and words spelled
  the same in Javanese are listed with their reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json`
  when a whole message consists of them.
