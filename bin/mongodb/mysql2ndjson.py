#!/usr/bin/env python3
"""
mysql2ndjson.py — export all eZ Publish tables from MySQL/MariaDB to NDJSON files.

Usage:
    python3 mysql2ndjson.py --host localhost --user root --password X --db exp --outdir ./json_export

Install dependency:
    pip install mysql-connector-python
"""
import argparse, json, os, mysql.connector

PK_MAP = {
    'ezcontentobject':          'id',
    'ezcontentobject_version':  None,   # composite: contentobject_id + version
    'ezcontentobject_attribute':'id',
    'ezcontentobject_tree':     'node_id',
    'ezcontentclass':           'id',
    'ezcontentclass_attribute': 'id',
    'ezsection':                'id',
    'ezcontentobject_name':     None,
    'ezurlalias_ml':            'id',
    'ezuser':                   'contentobject_id',
}

def cast_row(row, pk_field):
    doc = {}
    for k, v in row.items():
        if isinstance(v, (bytes, bytearray)):
            v = v.decode('utf-8', errors='replace')
        elif v is None:
            continue   # omit null fields — keeps documents lean
        doc[k] = v
    if pk_field and pk_field in doc:
        doc['_id'] = int(doc[pk_field])
    return doc

def export_table(cur, table, pk_field, outdir):
    cur.execute(f"SELECT * FROM `{table}`")
    cols = [d[0] for d in cur.description]
    path = os.path.join(outdir, f"{table}.ndjson")
    count = 0
    with open(path, 'w') as f:
        for row_tuple in cur:
            row = dict(zip(cols, row_tuple))
            doc = cast_row(row, pk_field)
            f.write(json.dumps(doc) + '\n')
            count += 1
    print(f"  {table}: {count} rows → {path}")

def main():
    ap = argparse.ArgumentParser(description='Export MySQL/MariaDB eZ Publish tables to NDJSON')
    ap.add_argument('--host', default='localhost')
    ap.add_argument('--user', required=True)
    ap.add_argument('--password', required=True)
    ap.add_argument('--db', required=True)
    ap.add_argument('--outdir', default='./json_export')
    ap.add_argument('--tables', nargs='*', help='Specific tables (default: all)')
    args = ap.parse_args()

    os.makedirs(args.outdir, exist_ok=True)
    conn = mysql.connector.connect(
        host=args.host, user=args.user, password=args.password,
        database=args.db, charset='utf8mb4'
    )
    cur = conn.cursor(dictionary=False)

    if args.tables:
        tables = args.tables
    else:
        cur.execute("SHOW TABLES")
        tables = [r[0] for r in cur]

    for table in tables:
        pk = PK_MAP.get(table, 'id')
        export_table(cur, table, pk, args.outdir)

    cur.close()
    conn.close()

if __name__ == '__main__':
    main()
