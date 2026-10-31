import { useEffect, useState } from 'react';
import { ActivityIndicator, Image, Pressable, SafeAreaView, ScrollView, Text, useWindowDimensions, View } from 'react-native';
import RenderHtml from 'react-native-render-html';
import { graphql } from './lib/graphql';
import { BY_CLASS, MENU, NODE } from './lib/queries';

type Item = { name: string; urlAlias: string };

// A field's value as the app shows it: an image, rich text (HTML rendered by the site), a relation, a list, text.
function Field( { value, open }: { value: any; open: ( path: string ) => void } ) {
  const { width } = useWindowDimensions();
  if ( value === null || value === undefined || value === '' ) {
    return null;
  }
  if ( Array.isArray( value ) ) {
    return <View>{ value.map( ( item, i ) => <Field key={ i } value={ item } open={ open } /> ) }</View>;
  }
  if ( typeof value === 'object' ) {
    if ( 'width' in value && 'url' in value ) {
      return value.url ? <Image source={ { uri: value.url } } accessibilityLabel={ value.alt ?? '' }
                                style={ { width: width - 32, height: value.width ? ( width - 32 ) * value.height / value.width : 200 } } /> : null;
    }
    if ( 'html' in value ) {
      return value.html ? <RenderHtml contentWidth={ width - 32 } source={ { html: value.html } } /> : null;
    }
    if ( 'urlAlias' in value ) {
      return <Pressable onPress={ () => open( value.urlAlias ) }><Text style={ { color: '#2563eb' } }>{ value.name }</Text></Pressable>;
    }
    return <Text>{ Object.values( value ).filter( Boolean ).join( ', ' ) }</Text>;
  }
  return <Text>{ String( value ) }</Text>;
}

export default function App() {
  const [ menu, setMenu ] = useState<Item[]>( [] );
  const [ path, setPath ] = useState( '/' );
  const [ page, setPage ] = useState<{ node: any; fields: [ string, any ][] } | null>( null );
  const [ error, setError ] = useState( '' );

  useEffect( () => {
    graphql( MENU ).then( ( data ) => setMenu( data.node?.children.nodes ?? [] ) ).catch( ( e ) => setError( e.message ) );
  }, [] );

  useEffect( () => {
    setPage( null );
    graphql( NODE, { path } ).then( async ( { node } ) => {
      if ( !node ) {
        setError( 'Not found.' );
        return;
      }
      const query = BY_CLASS[ node.classIdentifier ];
      const full = query ? await graphql( query, { id: node.id } ) : { content: null };
      const fields = Object.entries( full.content ?? {} ).filter( ( [ key ] ) => ![ 'id', 'name', 'classIdentifier' ].includes( key ) ) as [ string, any ][];
      setError( '' );
      setPage( { node, fields } );
    } ).catch( ( e ) => setError( e.message ) );
  }, [ path ] );

  const open = ( alias: string ) => setPath( '/' + ( alias ?? '' ) );
  return (
    <SafeAreaView style={ { flex: 1 } }>
      <ScrollView horizontal style={ { flexGrow: 0, padding: 16 } }>
        <Pressable onPress={ () => setPath( '/' ) }><Text style={ { fontWeight: '700', marginRight: 16 } }>__EXP_SITE__</Text></Pressable>
        { menu.map( ( item ) => <Pressable key={ item.urlAlias } onPress={ () => open( item.urlAlias ) }><Text style={ { marginRight: 16 } }>{ item.name }</Text></Pressable> ) }
      </ScrollView>
      <ScrollView contentContainerStyle={ { padding: 16 } }>
        { error ? <Text>{ error }</Text> : !page ? <ActivityIndicator /> : (
          <View>
            <Text style={ { fontSize: 24, fontWeight: '700', marginBottom: 12 } }>{ page.node.name }</Text>
            { page.fields.map( ( [ key, value ] ) => <View key={ key } style={ { marginBottom: 12 } }><Field value={ value } open={ open } /></View> ) }
            { page.node.children.nodes.map( ( child: Item ) => <Field key={ child.urlAlias } value={ child } open={ open } /> ) }
          </View>
        ) }
      </ScrollView>
    </SafeAreaView>
  );
}
