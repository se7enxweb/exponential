import Link from 'next/link';
import type { ReactNode } from 'react';
import { graphql } from '../lib/graphql';
import { MENU } from '../lib/queries';

export const metadata = { title: '__EXP_SITE__' };

export default async function RootLayout( { children }: { children: ReactNode } ) {
  const data = await graphql<{ node: { children: { nodes: { name: string; urlAlias: string }[] } } | null }>( MENU );
  return (
    <html lang="en">
      <body>
        <header>
          <Link href="/"><strong>__EXP_SITE__</strong></Link>
          <nav>{ ( data.node?.children.nodes ?? [] ).map( ( item ) => <Link key={ item.urlAlias } href={ `/${ item.urlAlias }` }> { item.name } </Link> ) }</nav>
        </header>
        <main>{ children }</main>
      </body>
    </html>
  );
}
