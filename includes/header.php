<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>TAS.KOM</title>

    <!-- Sets initial viewport load and disables zooming  -->
    <meta name="viewport" content="initial-scale=1, maximum-scale=1">

    <!-- Makes your prototype chrome-less once bookmarked to your phone's home screen -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="#0000FF">

    <!-- Include the compiled Ratchet CSS -->
    <!-- <link href="includes/ratchet.min.css" rel="stylesheet"> -->
    <link href="http://ajax.googleapis.com/ajax/libs/jquerymobile/1.4.5/jquery.mobile.min.css" rel="stylesheet">
    <link href="includes/override.css" rel="stylesheet">
    
    <!-- Include the compiled Ratchet JS -->
    <!-- <script src="appresource/js/ratchet.js"></script> -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <script>jQ=jQuery.noConflict( true );</script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquerymobile/1.4.5/jquery.mobile.min.js"></script>
    <script>$.mobile.defaultPageTransition = "slide";</script>
  </head>
  <body>
    <div data-role="page" id="main" data-cache="false" data-dom-cache="false">
      <script type="text/css">
        $('div').live('pagehide', function(event, ui){
          var page = $(event.target);

          if(page.attr('data-cache') == 'false'){
            page.remove();
          };
        });
      </script>
