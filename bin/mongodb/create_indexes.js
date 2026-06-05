// create_indexes.js — create all MongoDB indexes required by the sevenx_mongodb adapter.
//
// Run with:
//   mongosh "mongodb://db:publishing$8088@localhost:27017/exp" --file create_indexes.js
// or inline:
//   mongosh "mongodb://db:publishing$8088@localhost:27017/exp" --eval "$(cat create_indexes.js)"

// Content object lookups
db.ezcontentobject.createIndex({ id: 1 }, { unique: true });
db.ezcontentobject.createIndex({ contentclass_id: 1 });
db.ezcontentobject.createIndex({ section_id: 1 });
db.ezcontentobject.createIndex({ status: 1 });
db.ezcontentobject.createIndex({ remote_id: 1 }, { unique: true });
db.ezcontentobject.createIndex({ owner_id: 1 });

// Tree / node navigation
db.ezcontentobject_tree.createIndex({ node_id: 1 }, { unique: true });
db.ezcontentobject_tree.createIndex({ contentobject_id: 1 });
db.ezcontentobject_tree.createIndex({ parent_node_id: 1 });
db.ezcontentobject_tree.createIndex({ main_node_id: 1 });
db.ezcontentobject_tree.createIndex({ path_string: 1 });
db.ezcontentobject_tree.createIndex({ depth: 1, sort_field: 1, sort_order: 1 });
db.ezcontentobject_tree.createIndex({ contentobject_id: 1, is_main: 1 });

// Object attributes
db.ezcontentobject_attribute.createIndex({ contentobject_id: 1, version: 1 });
db.ezcontentobject_attribute.createIndex({ contentclassattribute_id: 1 });
db.ezcontentobject_attribute.createIndex({ language_code: 1 });

// Versions
db.ezcontentobject_version.createIndex({ contentobject_id: 1, version: 1 }, { unique: true });
db.ezcontentobject_version.createIndex({ status: 1 });
db.ezcontentobject_version.createIndex({ creator_id: 1 });

// Names
db.ezcontentobject_name.createIndex({ contentobject_id: 1, content_version: 1, content_translation: 1 });

// Content classes
db.ezcontentclass.createIndex({ identifier: 1 });
db.ezcontentclass.createIndex({ version: 1 });
db.ezcontentclass_attribute.createIndex({ contentclass_id: 1, version: 1 });
db.ezcontentclass_attribute.createIndex({ identifier: 1 });

// URL aliases — critical for navigation
db.ezurlalias_ml.createIndex({ parent: 1, text_md5: 1 }, { unique: true });
db.ezurlalias_ml.createIndex({ action: 1, is_original: 1, is_alias: 1 });
db.ezurlalias_ml.createIndex({ id: 1 }, { unique: true });
db.ezurlalias_ml.createIndex({ link: 1 });

// User / auth
db.ezuser.createIndex({ login: 1 }, { unique: true });
db.ezuser.createIndex({ email: 1 });
db.ezuser.createIndex({ contentobject_id: 1 }, { unique: true });

// Roles and policies
db.ezrole.createIndex({ name: 1 });
db.ezpolicy.createIndex({ role_id: 1 });
db.ezpolicy_limitation.createIndex({ policy_id: 1 });
db.ezuser_role.createIndex({ contentobject_id: 1 });

// States
db.ezcobj_state_group.createIndex({ identifier: 1 });
db.ezcobj_state.createIndex({ group_id: 1, identifier: 1 });
db.ezcobj_state_link.createIndex({ contentobject_id: 1 });

// Sections
db.ezsection.createIndex({ identifier: 1 });

// Info collector
db.ezinfocollection.createIndex({ contentobject_id: 1 });

// Search
db.ezsearch_object_word_link.createIndex({ word_id: 1 });
db.ezsearch_object_word_link.createIndex({ contentobject_id: 1 });
db.ezsearch_word.createIndex({ word: 1 }, { unique: true });

// Language
db.ezcontentlanguage.createIndex({ locale: 1 }, { unique: true });
db.ezcontentlanguage.createIndex({ language_mask: 1 });

print("All indexes created.");
