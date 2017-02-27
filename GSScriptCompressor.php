<?php
/*
Plugin Name: GS CSS Compression
Description: 
Version: 1.0
Author: Dieter Holzmann
Author URI: http://dieterholzmann.de/
*/
 
define( 'GSDATACOMPRESSOR', GSCACHEPATH . 'compressor/' );

# get correct id for plugin
$thisfile = basename( __FILE__, ".php" );

$plugin_id = $thisfile;
$tab_name = $plugin_id;
 
# register plugin
register_plugin(
	$thisfile,
	'GS Script Compressor',
	'1.0.0',
	'Dieter Holzmann',
	'http://dieterholzmann.de/'
);

// add_action( 'nav-tab', 'createNavTab', array( $tab_name, $plugin_id, 'Script Compressor', 'list' ) );

add_action( 'compress', 'compressor' );

# file check
if( !is_dir( GSDATACOMPRESSOR ) )
    mkdir( GSDATACOMPRESSOR );

function compressor()
{
    global $GS_styles, $SITEURL;

    $buffer = "";
    
    foreach ( $GS_styles as $name => $style )
    {
        if( ($style['where'] & GSFRONT) && $style['load'] == true )
        {
            $buffer .= file_get_contents( $style['src'] );
            unset($GS_styles[$name]);
        }
    }

    // Remove comments
    $buffer = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer );

    // Remove space
    $buffer = str_replace( ': ', ':', $buffer );
    $buffer = str_replace( '; ', ';', $buffer );
    $buffer = str_replace( ', ', ',', $buffer );

    // Remove whitespace
    $buffer = str_replace(
        array(
            "\r\n",
            "\r",
            "\n",
            "\t",
            '  ',
            // '    ',
            // '    '
        ),
        '',
        $buffer
    );

    $fileName = 'merged-' . md5($buffer) . '.css';

    if( !is_file( GSDATACOMPRESSOR . $fileName ) )
        file_put_contents( GSDATACOMPRESSOR . $fileName, $buffer );

    register_style( 'compression-fe', $SITEURL . 'data/cache/compressor/' . $fileName, get_site_version(false), 'all' );
    queue_style( 'compression-fe', GSFRONT );
}
