-- The tables of an eznewsletter 1.6 installation that ext:cjw_newsletter:import-eznewsletter reads, as SQLite
-- (column names and types of the 1.6 schema). Used by cjwNewsletterImportExportTest to build a throwaway SQLite
-- file under var/tmp; it never touches the installation's database.
CREATE TABLE ezsubscription_list (
  id INTEGER NOT NULL,
  name TEXT NOT NULL DEFAULT '',
  url_type INTEGER NOT NULL DEFAULT 0,
  url TEXT NOT NULL DEFAULT '',
  description TEXT NOT NULL DEFAULT '',
  allow_anonymous INTEGER NOT NULL DEFAULT 0,
  login_steps INTEGER NOT NULL DEFAULT 1,
  require_password INTEGER NOT NULL DEFAULT 1,
  auto_confirm_registered INTEGER NOT NULL DEFAULT 1,
  auto_approve_registered INTEGER NOT NULL DEFAULT 0,
  created INTEGER NOT NULL DEFAULT 0,
  creator_id INTEGER NOT NULL DEFAULT 0,
  related_object_id_1 INTEGER DEFAULT 0,
  related_object_id_2 INTEGER DEFAULT 0,
  related_object_id_3 INTEGER DEFAULT 0,
  status INTEGER NOT NULL DEFAULT 0,
  PRIMARY KEY (id, status)
);
CREATE TABLE ez_newsletter_subscription (
  newsletter_id INTEGER NOT NULL DEFAULT 0,
  status INTEGER NOT NULL DEFAULT 0,
  subscription_id INTEGER NOT NULL DEFAULT 0,
  PRIMARY KEY (newsletter_id, status, subscription_id)
);
CREATE TABLE eznewslettertype (
  id INTEGER NOT NULL,
  name TEXT DEFAULT NULL,
  subscriptionlist_list_id INTEGER DEFAULT 0,
  contentclass_list TEXT DEFAULT '',
  inbox_id INTEGER DEFAULT 0,
  sender_address TEXT DEFAULT '',
  description TEXT NOT NULL DEFAULT '',
  defaultsubscriptionlist_id INTEGER DEFAULT 0,
  allowed_output_formats TEXT DEFAULT '',
  allowed_designs TEXT DEFAULT NULL,
  digest_settings INTEGER DEFAULT 0,
  related_object_id_1 INTEGER DEFAULT 0,
  related_object_id_2 INTEGER DEFAULT 0,
  related_object_id_3 INTEGER DEFAULT 0,
  article_pool_object_id INTEGER DEFAULT 0,
  status INTEGER NOT NULL DEFAULT 0,
  created INTEGER NOT NULL DEFAULT 0,
  send_date_modifier INTEGER NOT NULL DEFAULT 0,
  creator_id INTEGER NOT NULL DEFAULT 0,
  personalise INTEGER NOT NULL DEFAULT 1,
  Pretext TEXT NOT NULL DEFAULT '',
  Posttext TEXT NOT NULL DEFAULT '',
  PRIMARY KEY (id, status)
);
CREATE TABLE ezsendnewsletteritem (
  id INTEGER PRIMARY KEY,
  newsletter_id INTEGER NOT NULL DEFAULT 0,
  subscription_id INTEGER NOT NULL DEFAULT 0,
  send_status INTEGER NOT NULL DEFAULT 0,
  send_ts INTEGER NOT NULL DEFAULT 0,
  hash TEXT DEFAULT '',
  bounce_id INTEGER NOT NULL DEFAULT 0,
  object_read_ids TEXT,
  object_print_ids TEXT
);
CREATE TABLE ezsubscription (
  id INTEGER NOT NULL,
  version_status INTEGER NOT NULL DEFAULT 0,
  subscriptionlist_id INTEGER DEFAULT 0,
  email TEXT DEFAULT '',
  hash TEXT DEFAULT '',
  status INTEGER DEFAULT 0,
  vip INTEGER DEFAULT 0,
  last_active INTEGER NOT NULL DEFAULT 0,
  output_format TEXT DEFAULT '',
  creator_id INTEGER NOT NULL DEFAULT 0,
  created INTEGER NOT NULL DEFAULT 0,
  confirmed INTEGER NOT NULL DEFAULT 0,
  approved INTEGER NOT NULL DEFAULT 0,
  removed INTEGER NOT NULL DEFAULT 0,
  user_id INTEGER DEFAULT 0,
  bounce_count INTEGER DEFAULT 0,
  contentobject_id INTEGER DEFAULT NULL,
  PRIMARY KEY (id, version_status)
);
CREATE TABLE ez_bouncedata (
  id INTEGER PRIMARY KEY,
  newslettersenditem_id INTEGER NOT NULL DEFAULT 0,
  address TEXT DEFAULT '',
  bounce_count INTEGER NOT NULL DEFAULT 0,
  bounce_type INTEGER NOT NULL DEFAULT 0,
  bounce_arrived INTEGER NOT NULL DEFAULT 0,
  bounce_message TEXT NOT NULL DEFAULT ''
);
CREATE TABLE eznewsletter (
  id INTEGER NOT NULL,
  name TEXT DEFAULT NULL,
  hash TEXT DEFAULT NULL,
  output_format TEXT DEFAULT '',
  design_to_use TEXT DEFAULT '',
  send_date INTEGER NOT NULL DEFAULT 0,
  send_status INTEGER NOT NULL DEFAULT 0,
  contentobject_id INTEGER NOT NULL DEFAULT 0,
  contentobject_version INTEGER NOT NULL DEFAULT 0,
  newslettertype_id INTEGER NOT NULL DEFAULT 0,
  category TEXT DEFAULT '',
  preview_email TEXT DEFAULT '',
  recurrence_type TEXT NOT NULL DEFAULT '',
  recurrence_value TEXT NOT NULL DEFAULT '',
  recurrence_condition TEXT NOT NULL DEFAULT '',
  recurrence_last_sent INTEGER NOT NULL DEFAULT 0,
  object_relations TEXT,
  status INTEGER NOT NULL DEFAULT 0,
  created INTEGER NOT NULL DEFAULT 0,
  creator_id INTEGER NOT NULL DEFAULT 0,
  pretext TEXT NOT NULL DEFAULT '',
  posttext TEXT NOT NULL DEFAULT '',
  preview_mobile TEXT DEFAULT '',
  PRIMARY KEY (id, status)
);
CREATE TABLE ezsubscriptionuserdata (
  id INTEGER PRIMARY KEY,
  email TEXT DEFAULT '',
  firstname TEXT DEFAULT '',
  name TEXT DEFAULT '',
  password TEXT DEFAULT '',
  hash TEXT DEFAULT '',
  mobile TEXT DEFAULT ''
);
CREATE TABLE ezrobinsonlist (
  id INTEGER PRIMARY KEY,
  value TEXT NOT NULL DEFAULT '',
  type INTEGER NOT NULL DEFAULT 0,
  global INTEGER NOT NULL DEFAULT 0
);
