import Link from 'next/link';
import { notFound } from 'next/navigation';
import { Field } from '../../components/Field';
import { graphql } from '../../lib/graphql';
import { BY_CLASS, NODE } from '../../lib/queries';

type Node = {
  id: number;
  name: string;
  classIdentifier: string;
  children: { totalCount: number; nodes: { name: string; urlAlias: string }[] };
};

// Every address of the site: / is the root, /about/company the content at that URL alias. The fields come from the
// query of the content's class (lib/queries.ts, generated from the schema).
export default async function Page( { params }: { params: Promise<{ path?: string[] }> } ) {
  const { path } = await params;
  const { node } = await graphql<{ node: Node | null }>( NODE, { path: '/' + ( path ?? [] ).map( decodeURIComponent ).join( '/' ) } );
  if ( !node ) {
    notFound();
  }
  const query = BY_CLASS[ node.classIdentifier ];
  const { content } = query ? await graphql<{ content: Record<string, any> | null }>( query, { id: node.id } ) : { content: null };
  return (
    <article>
      <h1>{ node.name }</h1>
      { Object.entries( content ?? {} )
        .filter( ( [ key ] ) => ![ 'id', 'name', 'classIdentifier' ].includes( key ) )
        .map( ( [ key, value ] ) => <section key={ key } className={ `field field-${ key }` }><Field value={ value } /></section> ) }
      { node.children.totalCount > 0 && (
        <ul className="children">
          { node.children.nodes.map( ( child ) => <li key={ child.urlAlias }><Link href={ `/${ child.urlAlias }` }>{ child.name }</Link></li> ) }
        </ul>
      ) }
    </article>
  );
}
