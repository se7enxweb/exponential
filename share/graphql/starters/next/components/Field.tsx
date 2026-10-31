import Link from 'next/link';

// A field's value as the page shows it: an image, rich text (HTML rendered by the site), a relation, a list, text.
export function Field( { value }: { value: any } ) {
  if ( value === null || value === undefined || value === '' ) {
    return null;
  }
  if ( Array.isArray( value ) ) {
    return value.length ? <ul>{ value.map( ( item, i ) => <li key={ i }><Field value={ item } /></li> ) }</ul> : null;
  }
  if ( typeof value === 'object' ) {
    if ( 'width' in value && 'url' in value ) {
      // eslint-disable-next-line @next/next/no-img-element
      return value.url ? <img src={ value.url } alt={ value.alt ?? '' } width={ value.width ?? undefined } height={ value.height ?? undefined } loading="lazy" /> : null;
    }
    if ( 'html' in value ) {
      return value.html ? <div className="rich-text" dangerouslySetInnerHTML={ { __html: value.html } } /> : null;
    }
    if ( 'urlAlias' in value ) {
      return <Link href={ `/${ value.urlAlias ?? '' }` }>{ value.name }</Link>;
    }
    return <span>{ Object.values( value ).filter( Boolean ).join( ', ' ) }</span>;
  }
  return <span>{ String( value ) }</span>;
}
