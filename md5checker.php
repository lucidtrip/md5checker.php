<?php

# configs and options
$input["delimiter"] = ":";
$input["user_offset"] = 0;
$input["pass_offset"] = 1;
$proxy["proxy"] = "";
$proxy["login"] = "";
$proxy["type"]  = CURLPROXY_SOCKS5;

/*# example for csv file and use tor proxy
$input["delimiter"] = ";";
$input["user_offset"] = 1;
$input["pass_offset"] = 3;
$proxy["proxy"] = "127.0.0.1:9150";
$proxy["login"] = "";
$proxy["type"]  = CURLPROXY_SOCKS5;
*/

# includes
include "simple_html_dom.php";



# functions

function log_html( $txt ) {
    if( false ) {
        print_r( $txt[1] );
        $handler = fopen( "DEV.html", "w+" );
        fwrite( $handler, $txt[0] );
        fclose( $handler );
    }
}

function save( $txt ) {
    $handler = fopen( "checked.log", "a" );
    fwrite( $handler, $txt );
    fclose( $handler );
}

class md5pages
{
    function md5_my_addr( $user, $md5 ) {
        $html = grab_page( "http://md5.my-addr.com/md5_decrypt-md5_cracker_online/md5_decoder_tool.php", "http://md5.my-addr.com/", false, true );
        if( $html[1]["http_code"] == 200 ) {
            $phtml = str_get_html( $html[0] );
            foreach( $phtml->find("input") as $in ) {
                $post[ $in->getAttribute( "name" ) ] = $in->getAttribute( "value" );
            }
            $post["md5"] = $md5;
            //print_r( $post );
            $html = grab_page( "http://md5.my-addr.com/md5_decrypt-md5_cracker_online/md5_decoder_tool.php", "http://md5.my-addr.com/md5_decrypt-md5_cracker_online/md5_decoder_tool.php", $post, false );
            if( $html[1]["http_code"] == 200 && $html[0] == str_replace( "not found in database", "", $html[0] ) ) {
                $phtml = str_get_html( $html[0] );
                foreach( $phtml->find( "div.white_bg_title" ) as $p ) {
                    $md5_clear = $p->innertext;
                    $md5_clear = array_pop( explode( ": ", $md5_clear ) );
                }
                if( isset( $md5_clear ) ) {
                    $save = "{$user}:{$md5_clear}\n";
                    echo "YEAH!! {$user}:{$md5_clear} @md5.my-addr.com\n";
                    if( $md5 != "" && $md5 != "not found in database" ) {
                        save( $save );
                        return true;
                    }
                }
            }
        }
        echo "NOT FOUND! {$user}:{$md5} @md5.my-addr.com\n";
        log_html( $html );
        return false;
    }

    function md5_net( $user, $md5 ) {
        $html = grab_page( "http://www.md5.net/md5-cracker/", "http://www.md5.net/", false, true );
        if( $html[1]["http_code"] == 200 ) {
            $phtml = str_get_html( $html[0] );
            foreach( $phtml->find("input") as $in ) {
                $post[ $in->getAttribute( "name" ) ] = $in->getAttribute( "value" );
            }
            $post["generator[hash]"] = $md5;
            $html = grab_page( "http://www.md5.net/md5-cracker/", "http://www.md5.net/md5-cracker/", $post, false );
            if( $html[1]["http_code"] == 200 ) {
                $phtml = str_get_html( $html[0] );
                foreach( $phtml->find( ".panel-body p" ) as $p ) {
                    $md5_clear = $p->innertext;
                }
                if( isset( $md5_clear ) && $md5_clear != "" && $md5_clear != "Not found..." ) {
                    $save = "{$user}:{$md5_clear}\n";
                    echo "YEAH!! {$user}:{$md5_clear} @md5.net\n";
                    save( $save );
                    return true;
                }
            }
        }
        echo "NOT FOUND! {$user}:{$md5} @md5.net\n";
        log_html( $html );
        return false;
    }
    
    function mypass( $user, $md5 ) {
        $data["hash"] = $md5;
        $data["get_pass"] = "Get+Pass";
        $html = grab_page( "http://md5pass.info/", "http://md5pass.info/", $data, true );
        if( $html[1]["http_code"] == 200 && strpos($html[0],"Not found!") === false ) {
            if( preg_match( "#Password \- \<b\>(.*)\<\/b\>#", $html[0], $md5_clear ) ) {
                echo "YEAH!! ${user}:${md5_clear[1]} @md5pass.info\n";
                save( "${user}:${md5_clear[1]}\n" );
                return true;
            }
        }
        echo "NOT FOUND! {$user}:{$md5} @md5pass.info\n";
        log_html( $html );
        return false;
    }
    
    function md5online( $user, $md5 ) {
        $data["pass"] = $md5;
        $data["option"] = "hash2text";
        $data["send"] = "Submit";
        $html = grab_page( "http://md5online.net/", "http://md5online.net/", $data, true );
        if( $html[1]["http_code"] == 200 && strpos( $html[0], "not found in our database." ) === false ) {
            if( preg_match( "#<br>pass : <b>(.*)</b></p>#", $html[0], $md5_clear ) ) {
                echo "YEAH!! ${user}:${md5_clear[1]} @md5online.net\n";
                save( "${user}:${md5_clear[1]}\n" );
                return true;
            }
        }
        echo "NOT FOUND! {$user}:{$md5} @md5online.net\n";
        log_html( $html );
        return false;
    }
    
    function md5cracker( $user, $md5 ) {
        $html = grab_page( "http://md5cracker.com/qkhash.php?option=json&pass={$md5}", "http://md5cracker.com/", false, true );
        if( $html[1]["http_code"] == 200 ) {
            if( $json = json_decode( $html[0], true ) ) {
                if( isset( $json["status"] ) && $json["status"] == "Found" ) {
                    echo "YEAH!! ${user}:${json["plaintext"]} @md5cracker.com\n";
                    save( "${user}:${json["plaintext"]}\n" );
                    return true;
                }
            }
        }
        echo "NOT FOUND! {$user}:{$md5} @md5cracker.com\n";
        log_html( $html );
        return false;
    }
}

function grab_page($url, $ref_url = false, $data = false, $login = false){
    
    ## cookie erstellen
    if( !is_dir("/tmp/cookies/")) {
        mkdir( "/tmp/cookies/" );
    }
    $cookie = "/tmp/cookies/".str_replace("www.", "", parse_url($url, PHP_URL_HOST)).".txt";
    if( !is_file($cookie) || $login == true ) {
        $fp = fopen($cookie, "w+");
        fclose($fp);
    }
    ## cookie end
    
    ## config curl
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
    //curl_setopt($ch, CURLOPT_COOKIESESSION, true);
    if(parse_url($url, PHP_URL_SCHEME) == "https") {
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    }
    if($data != false) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }
    if($ref_url != false) {
        curl_setopt($ch, CURLOPT_REFERER, $ref_url);
    }
    
    ## config the curl proxy mode
    if( isset( $proxy["proxy"] ) && $proxy["proxy"] != false && $proxy["proxy"] != "" ) {
        curl_setopt($ch, CURLOPT_PROXYTYPE, $proxy["type"]);
        curl_setopt($ch, CURLOPT_PROXY, $proxy["proxy"]);
        if( isset( $proxy["login"] ) && $proxy["login"] != false && $proxy["login"] != "" ) {
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy["login"]);
        }
    }
    
    ## exec curl
    $exec = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    unset($ch);
    
    ## return
    return array($exec, $info);
}

function curl_cookie_rm() {
    foreach( scandir( "/tmp/cookies/" ) as $cookie ) {
        unlink( $cookie );
    }
    unlink( "/tmp/cookies" );
}


function banner( $argv )
{
    echo <<<BANNER

+---------------------------------------------------------------+
|                        ♥ MD5 checker ♥                        |
+---------------------------------------------------------------+
|                        use php and curl                       |
|                by _bop at 14.01.15 the sunnyday               |
+---------------------------------------------------------------+

USAGE ~ $ php ${argv[0]} user:md5
TEST  ~ $ php ${argv[0]} test:098f6bcd4621d373cade4e832627b4f6

for multicheck use xargs
USAGE ~ $ xargs -a user_md_list.txt -n 1 -P 25 php ${argv[0]}

lines with first char "#" will be skipped...

for more configs and options look the first lines of this script!

HAVE FUN!


BANNER;
}


# main
if( isset( $argv[1] ) && $argv[1] != "" )
{
    ## skip #
    if( substr( $argv[1], 1 ) == "#" )
        die();
    
    ## prepare
    $argv[1] = trim( $argv[1] );
    $vic = explode( $input["delimiter"], $argv[1] );
    $user = $vic[$input["user_offset"]];
    $md5 = $vic[$input["pass_offset"]];
    
    ## crack
    $md5pages = new md5pages;
    foreach( get_class_methods($md5pages) as $page ) {
        if( $md5pages->$page( $user, $md5 ) )
            break;
    }
}
else {
    banner( $argv );
}
?>
