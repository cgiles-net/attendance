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
  
  public static function button ($args) {
    $tag = (isset($args["target"]))? "a":"span";
    $target = (isset($args["target"]))? ' href="'.$args["target"].'"' : "";
    $reversi = (isset($args["reverse"])||(isset($args["rel"])&&$args["rel"]=="back"));
    $data  = (isset($args["rel"]))? ' data-rel="'.$args["rel"].'"':"";
    $data  = ($reversi)?$data.' data-direction="reverse"':$data;
    $data  = (isset($args["data-icon"]))? $data.' data-icon="'.$args["data-icon"].'"': $data;
    $css = "ui-btn";
    $css = (isset($args["corners"]))? $css:$css." ui-corner-all";
    $css = (isset($args["position"]))? $css." ui-btn-".$args["position"]:$css;
    $css = (isset($args["icon"])&&!isset($args["icon-position"]))? $css.' ui-btn-icon-left':$css;
    $css = (isset($args["icon"]))? $css.' ui-icon-'.$args["icon"]: $css;
    $css = (isset($args["notext"]))? $css." ui-btn-icon-notext": $css;
    $css = (isset($args["nodisc"]))? $css." ui-nodisc-icon":$css;
    $text   = (isset($args["text"]))? $args["text"]: "";
    
    return "<$tag$target$data class=\"$css\">$text</$tag>\n";
    
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
  private $tag = "ul";
  private $list_html = "";
  private $spacer = "";
  public function __CONSTRUCT($args) {
    $this->spacer = (isset($args["spacer"]))? $args["spacer"]: '';
    $this->tag = ("ol" == $args["type"])? "ol" : "ul";
    $data = (isset($args["inset"]))? ' data-inset="'.$args["inset"].'"' : "";
    $data = (isset($args["dividers"]))? $data.' data-autodividers="'.$args["dividers"].'"': $data;
    $data = (isset($args["filter"]))? $data.' data-filter="true"': $data;
    $data = (isset($args["search"]))? $data.' data-input="#'.$args["search"].'"':$data;
    $data = (isset($args["collapse"]))? $data.' data-collapsed="'.$args["collapse"].'"':$data;
    $this->list_html .= "<".$this->tag." data-role=\"listview\"$data>\n";
  }
  public function add_item () {
    $args = func_get_args();
    $content = (reset($args))? array_shift($args) : '';
    $isdivider = (array_shift($args))? ' data-role="list-divider"' : '';
    $class = (reset($args))? ' class="'.reset($args).'"' : '';
    $this->list_html .= $this->spacer."  <li$class$isdivider>$content</li>\n";
  }
  public function add_content ($content) {
    $this->list_html .= $this->spacer."  $content\n";
  }
  
  public function close () {
    $this->list_html .= $this->spacer."</".$this->tag.">\n";
    return $this->list_html;
  }
}

class Select_input {
  private $options = "";
  private $spacer = "";
  private $newlines = "";
  public function __CONSTRUCT($args) {
    $this->spacer = (isset($args["spacer"]))? $args["spacer"]: '';
    $this->newlines = (isset($args["spacer"])||isset($args["newlines"]))? "\n": '';
    $attr = (isset($args["name"]))? ' name="'.$args["name"].'"' : '';
    $attr = (isset($args["id"]))? $attr.' id="'.$args["id"].'"' : $attr;
    $attr = (isset($args["disabled"]))? $attr.$args["disabled"]:$attr;
    $this->options .= "<select$attr>".$this->newlines;
  }
  public function add_option ($name,$val) {
    $args=func_get_args();
    $selected = (isset($args[2]))? " selected='selected'": "";
    $value = " value='$val'";
    $this->options .= $this->spacer."  <option$value$selected>$name</option>".$this->newlines;
  }
  public function add_content ($content) {
    $this->options .= $this->spacer."  $content".$this->newlines;
  }
  
  public function close () {
    $this->options .= $this->spacer."</select>";
    return $this->options;
  }
}

class Card {
  private $spacer = "";
  private $tag = "";
  private $card = "";
  private $card_list = "";
  private $card_list_open = false;
  private $card_inner = "";
  private $card_content_open = false;
  public function __CONSTRUCT($args) {
    $tag = (isset($args["tag"]))? $args["tag"] : "div";
    $title = (isset($args["title"]))? "<h4>".$args["title"]."</h4>" : "";
    $data = (isset($args["role"]))? ' data-role="'.$args["role"].'"':"";
    $data = (isset($args["c-icon"]))? $data.' data-collapsed-icon="'.$args["c-icon"].'"':$data;
    $data = (isset($args["e-icon"]))? $data.' data-expanded-icon="'.$args["e-icon"].'"':$data;
    $data = (isset($args["collapse"]))? $data.' data-collapsed="'.$args["collapse"].'"':$data;
    $data = (isset($args["id"]))? $data.' id="'.$args["id"].'"':$data;
    $data = (isset($args["class"]))? $data.' class="'.$args["class"].'"':$data;
    $data = (!empty($args["action"]))? $data.' action="'.$args["action"].'"':$data;
    $data = (!empty($args["method"]))? $data.' method="'.$args["method"].'"':$data;
    
    if ($title!=""&&!isset($args["collapse"]))
      $title="<div class='ui-bar ui-bar-a'>".$title."</div>";
    
    $this->spacer = (isset($args["spacer"]))? $args["spacer"]: '';
    $this->card  = "<$tag$data>\n";
    $this->tag = $tag;
    if ($title!="")
      $this->card .= $this->spacer."  ".$title."\n";
    
  }
  
  public function add_content () {
    $args = func_get_args();
    $content = (reset($args))? array_shift($args) : '';
    $this->card .= $this->spacer."  $content\n";
  }
  
  public function content ($content) {
    $this->card .= $content;
  }
  
  public function close () {
	  if ($this->card_list_open)
      $this->close_list();
    
    $this->card .= $this->spacer."</".$this->tag.">\n";
    return $this->card;
  }
  
  /* list functions */
  public function add_list($args) {
	  if ($this->card_list_open)
      $this->close_list();
	  $args["spacer"]=$this->spacer."  ";
	  $this->card_list=new list_obj($args);
	  $this->card_list_open = true;
  }
  
  public function add_item() {
    $args = func_get_args();
    $content = (reset($args))? array_shift($args) : '';
    if (array_shift($args))
      $this->card_list->add_item($content,true);
    else
      $this->card_list->add_item($content);
  }
  
  public function add_list_content($content) {
    $this->card_list->add_content($content);
  }
  
  public function close_list() {
	  $this->card_list_open = false;
	  $this->card.=$this->spacer."  ".$this->card_list->close();
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