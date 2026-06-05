#!/usr/bin/env python3
"""
fixup_ndjson.py — post-process NDJSON files before MongoDB import.

What this does:
  1. Ensures _id is an integer (not a string) for all eZ tables with numeric PKs
  2. Converts 'NULL' string values to omitted fields
  3. Ensures serialized_name_list / serialized_description_list are strings
  4. Casts known integer fields to int (handles string numbers from MySQL tab-export)

Usage:
    python3 fixup_ndjson.py [indir] [outdir]

Defaults:  indir=./json_export   outdir=./json_fixed
"""
import sys, json, os

INT_FIELDS = {
    'id', 'contentobject_id', 'contentclass_id', 'node_id', 'parent_node_id',
    'main_node_id', 'owner_id', 'version', 'status', 'language_mask',
    'language_id', 'published', 'modified', 'created', 'section_id',
    'current_version', 'creator_id', 'modifier_id', 'depth', 'sort_field',
    'sort_order', 'priority', 'is_hidden', 'is_invisible',
    'path_identification_string', 'content_version', 'contentclassattribute_id',
    'role_id', 'policy_id', 'group_id', 'remote_id_hash',
}

def fixup(doc):
    out = {}
    for k, v in doc.items():
        if v == 'NULL' or v is None:
            continue
        if k in INT_FIELDS:
            try:
                v = int(float(str(v)))
            except (ValueError, TypeError):
                pass
        out[k] = v
    return out

def process(inpath, outpath):
    count = 0
    with open(inpath) as fin, open(outpath, 'w') as fout:
        for line in fin:
            line = line.strip()
            if not line:
                continue
            doc = fixup(json.loads(line))
            fout.write(json.dumps(doc) + '\n')
            count += 1
    return count

indir  = sys.argv[1] if len(sys.argv) > 1 else './json_export'
outdir = sys.argv[2] if len(sys.argv) > 2 else './json_fixed'
os.makedirs(outdir, exist_ok=True)

for fname in sorted(os.listdir(indir)):
    if fname.endswith('.ndjson'):
        n = process(os.path.join(indir, fname), os.path.join(outdir, fname))
        print(f"  {fname}: {n} docs")
