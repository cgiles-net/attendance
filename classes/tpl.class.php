<?php

class TPL {
  
  public static $open_content = false;
  
  
  public static function old_button($args) {
    $args = func_get_args();
    $pos  = (reset($args)!= null)? '-'.reset($args) : "";
    array_shift($args);
    $icon = (reset($args)!= null)? ' data-icon="'.reset($args).'"' : '';
    array_shift($args);
    $text = (reset($args))? reset($args) : "Options";
    array_shift($args);
    $target = (reset($args))? reset($args) : "#";
    return "<a href=\"$target\"$icon class=\"ui-btn$pos ui-corner-all\">$text</a>\n";
  }
  
  public static function button ($argsArray) {
    $target = (isset($argsArray["target"]))? 'a href="'.$argsArray["target"].'"' : "span";
    $tag = (isset($argsArray["target"]))? "a":"span";
    $rel = (isset($argsArray["rel"]))? ' data-rel="'.$argsArray["rel"].'"':"";
    $pos    = (isset($argsArray["position"]))? " ui-btn-".$argsArray["position"]:"";
    $icon   = (isset($argsArray["icon"]))? ' ui-icon-'.$argsArray["icon"]: "";
    $dicon   = (isset($argsArray["data-icon"]))? ' data-icon="'.$argsArray["data-icon"].'"': "";
    $text   = (isset($argsArray["text"]))? $argsArray["text"]: "";
    $notext = (isset($argsArray["notext"]))? " ui-btn-icon-notext":"";
    $nodisc = (isset($argsArray["nodisc"]))? " ui-nodisc-icon":"";
    $corners = (isset($argsArray["corners"]))? "":" ui-corner-all";
    
    return "<$target$rel$dicon class=\"ui-btn $nodisc$pos$icon$notext$corners\">$text</$tag>\n";
    
  }
  
  public static function add_page ($id) {
    if (TPL::$open_content) close_content();
    echo '    </div>\n    <div data-role="page" id="'.$id.'">';
  }
  public static function add_card () {
    $content = array_shift(func_get_args());
    return <<<END
        <div class="ui-corner-all custom-corners">\n$content\n        </div>\n
END;
  }
  public static function add_body () {
    $content = array_shift(func_get_args());
    return <<<END
          <div class="ui-body ui-body-a ui-corner-all">
            $content
          </div>\n
END;
  }
  
}
class list_obj {
  private $list_type = "ul";
  private $list_html = "";
  private $spacer = "";
  public function __CONSTRUCT($argsArray) {
    $this->list_type = ("ol" == $argsArray["type"])? "ol" : "ul";
    $this->spacer = (isset($argsArray["spacer"]))? $argsArray["spacer"]: '';
    $inset = (isset($argsArray["inset"]))? ' data-inset="true"' : '';
    $dividers = (isset($argsArray["dividers"]))? ' data-autodividers="true"': '';
    $filter = (isset($argsArray["filter"]))? ' data-filter="true"': '';
    $input  = (isset($argsArray["search"]))? ' data-input="#'.$argsArray["search"].'"':'';
    $this->list_html .= "<".$this->list_type." data-role=\"listview\"$inset$dividers$filter$input>\n";
  }
  public function add_item () {
    $args = func_get_args();
    $content = (reset($args))? array_shift($args) : '';
    $isdivider = (array_shift($args))? ' data-role="list-divider"' : '';
    $class = (reset($args))? ' class="'.reset($args).'"' : '';
    $this->list_html .= $this->spacer."  <li$class$isdivider>$content</li>\n";
  }
  
  public function list_close () {
    $this->list_html .= $this->spacer."</".$this->list_type.">\n";
    return $this->list_html;
  }
}

class card_obj {
  private $card = "";
  private $spacer = "";
  public function __CONSTRUCT($argArray) {
    
    $this->spacer = (isset($argArray["spacer"]))?$argArray["spacer"]:'';
    $drole = (isset($argArray["role"]))? ' data-role="'.$argArray["role"].'" data-collapsed-icon="carat-d" data-expanded-icon="carat-u" data-collapsed="false"':' class="ui-corner-all"';
    $title = (isset($argArray["title"]))? "<h4>".$argArray["title"]."</h4>" : "";
    $id = $argArray["id"];
    
    $this->card = $this->spacer."<div$drole>\n  ".$this->spacer.$title."\n";
  }
  public function add_content () {
    $args = func_get_args();
    $content = (reset($args))? array_shift($args) : '';
    $this->card .= $this->spacer."  $content\n";
  }
  
  public function new_list($name) {
    $this->$name=new list_obj();
  }
  
  public function close () {
    $this->card .= $this->spacer."</div>\n";
    return $this->card;
  }
  
}

class Modal {
  private $modal  = "";
  private $type   = "";
  private $spacer = "";
  private $id     = "";
  public function __CONSTRUCT($argArray) {
    
    $this->spacer = (isset($argArray["spacer"]))?$argArray["spacer"]: '';
    $id = $argArray["id"];
    $this->id = $id;
    $this->type=$argArray["type"];
    $flags="";
    switch($this->type){
      case 'popup':
        $flags=' data-role="popup" data-overlay-theme="b" data-theme="b" data-dismissible="false"';
        break;
      default:
        $flags=' data-role="panel" data-position="right" data-display="overlay"';
    }
    
    $this->modal = $this->spacer."<div id='$id'$flags>\n";
  }
  public function add_content () {
    $args = func_get_args();
    $content = (reset($args))? array_shift($args) : '';
    $this->modal .= $this->spacer."  $content\n";
  }
  public function add_button ($argArray) {
    $this->modal .= $this->spacer."    ".TPL::button($argArray);
  }
  
  public function generate_link () {
    return TPL::button(array(
      ""=>""
    ));
  }
  
  public function close_modal () {
    $this->modal .= $this->spacer."</div>\n";
    return $this->modal;
  }
  
}

?>