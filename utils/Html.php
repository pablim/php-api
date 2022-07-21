<?php
namespace utils;
use utils\Util;

class Html {

  static function element($id, $values) {
    $out = "";
    $script = "<script>{content}</script>";
    $javascript = "var " . Util::snakeCase($id) . "=document.getElementById(\"" .$id. "\");";

    foreach($values as $attribute => $value) {
      $out .= $id . ".$attribute" . "=\"" .$value . "\";";
    }

    $out = Util::replace(["{content}"=>$javascript . $out], $script);
    return $out;
  }

  static function input($attributes, $parent="body") {

    $out = "";
    $script = "<script>{content}</script>";
    $input = "<input {attributes} />";

    foreach($values as $attribute => $value) {
      $out .= $id . ".$attribute" . "=" .$value . ";";
    }

    $out = Util::replace(["{content}"=>$javascript], $script);

    return $out;

  }



  static function addElement($element, $id, $parent="body") {
    $out = "";
    $script = "<script>{content}</script>";
    $javascript = "var newElement = document.createElement(\"" . $element . "\");";
    $javascript .= "newElement.id = \"".$id."\";";
    $javascript .= $parent . ".appendChild(newElement);";

    $out = Util::replace(["{content}"=>$javascript], $script);

    return $out;
  }

  static function addElementAfter($element, $id, $sibling) {
    $out = "";
    $script = "<script>{content}</script>";
    $javascript = "var newElement = document.createElement(\"" . $element . "\");";
    $javascript .= "newElement.id = \"".$id."\";";
    $javascript .= $sibling . ".after(newElement);";

    $out = Util::replace(["{content}"=>$javascript], $script);

    return $out;
  }

  static function styleClass($id, $values) {
    $out = "";
    $script = "<script>{content}</script>";
    $javascript = "var " . Util::snakeCase($id) . "=document.getElementById(\"" .$id. "\");";

    foreach($values as $attribute => $value) {
      $out .= $id . ".$attribute" . "=" .$value . ";";
    }

    $out = Util::replace(["{content}"=>$javascript . $out], $script);
    return $out;
  }

  static function mGrid($values) {
    $out = "";
    $script = "<script>{content}</script>";

    foreach($values as $id => $value) {
      $id = $id . "_col";
      // $javascript = "var " . Util::snakeCase($id) . "=document.getElementById(\"" .$id. "\");";
      $out .= "removeColGrid(".$id.",\"m\");";
      $out .= Util::snakeCase($id) . ".className=" . Util::snakeCase($id) . ".className " . "+ \" m" . $value . "\";";
    }

    $out = Util::replace(["{content}"=>$out], $script);
    return $out;
  }

  static function tableHeader($objectArray, $exclude=[]) {
    include_once $_SERVER["DOCUMENT_ROOT"] . "/strings/styleClasses.php";
    $tr = "<tr>{tr_content}</tr>";
    $th = "<th id=\"{header_id}\">{th_content}</th>";
    $tableHeader = "";

    // Construindo table header
    // var_dump($objectArray);
    foreach($objectArray as $object) {
      $entityName = Util::snakeCase(Util::shortClassName($object));
      $methods[$entityName] = get_class_methods($object);
      foreach($object as $key => $value) {
        if (!in_array($key, $exclude)) {
          $snakeKey = Util::snakeCase($key);
          $labelName = Util::verify("label", $object::props()[$snakeKey]) == "" ?
              ucfirst($object::props()[$snakeKey]["name"]) : $object::props()[$snakeKey]["label"];
          $tableHeader .= Util::replace(["{th_content}"=>$labelName,
              "{header_id}"=>$snakeKey], $th);
          $properties[$entityName][] = $key;
        }
      }
    }

    var_dump($tableHeader);
    return $tableHeader;
  }

  static function table($tableId, $objectArray, $superList, $extraColumns,
      $exclude=[], $options=[], $actions=[]) {
    include_once $_SERVER["DOCUMENT_ROOT"] . "/strings/styleClasses.php";

    $table = "<table id=\"$tableId\" class=\"".TABLE."\">{table_content}</table>";
    $tableHead = "<thead>{content}</thead>";
    $tr = "<tr>{tr_content}</tr>";
    $th = "<th id=\"{header_id}\">{th_content}</th>";
    $td = "<td>{td_content}</td>";
    $properties = [];
    $replaces = [];
    $methods = [];
    $a = [];
    $acts = "";
    $tableHeader = "";
    $tableContent = "";
    $cols = "";
    $rows = "";

    // Construindo table header
    // var_dump($objectArray);
    foreach($objectArray as $object) {
      $entityName = Util::snakeCase(Util::shortClassName($object));
      $methods[$entityName] = get_class_methods($object);
      foreach($object as $key => $value) {
        if (!in_array($key, $exclude)) {
          $snakeKey = Util::snakeCase($key);
          $labelName = Util::verify("label", $object::props()[$snakeKey]) == "" ?
              ucfirst($object::props()[$snakeKey]["name"]) : $object::props()[$snakeKey]["label"];
          $tableHeader .= Util::replace(["{th_content}"=>$labelName,
              "{header_id}"=>$snakeKey], $th);
          $properties[$entityName][] = $key;
        }
      }
    }

    // Colunas extras
    foreach ($extraColumns as $key => $extraColumn) {
      $tableHeader .= Util::replace(["{th_content}"=>$extraColumn["header"]], $th);
    }

    foreach ($superList as $list => $item) {
      foreach($properties as $entity => $arrayProperty) {
        foreach ($arrayProperty as $property) {
          $camelProperty = Util::classCase($property);
          $method = "get" . ucfirst($camelProperty);
          if (in_array($method, $methods[$entity])) {
            $itemMethod =	$item[$entity]->$method();
            if (Util::verify("format", $item[$entity]::props()[Util::snakeCase($property)]) == "currency") {
              $itemMethod = "R$" . Util::currency($itemMethod);
            }
            $cols .= Util::replace(["{td_content}"=>$itemMethod], $td);
          }
        }
      }

      // Ações
      $actionsCol = "";
      foreach ($actions as $action) {
        $vars = $action[1];
        foreach ($vars as $var) {
          $method = "get" . Util::classCase($var);
          if (in_array($method, $methods)) {
            $a[$var] = $item->$method();
          }
        }

        $act_url = explode("/", $action[0]);
        $actionsCol .= Html::action($act_url[0], $act_url[1], $a,
            $action[2], $action[3], array_key_exists(4, $action) ? $action[4] : "");
        $a=[];
      }

      // Colunas extras
      $contents = "";
      foreach ($extraColumns as $extraColumn) {
        foreach ($extraColumn["content"] as $key => $content) {
          if ($key === "execute") {

            // Passa os valores dos parâmetros
            $result = [];
            foreach ($content["params"] as $key => $value) {
              if (Util::verify($key, $item)) {
                $method = "get".Util::classCase($value);
                $result[] = $item[$key]->$method();
                $contents = Util::executeFunction($extraColumn["content"]["execute"],
                    $result);
              }
            }
          } else {


            foreach ($params as $key => $value) {
              $stringParams = "";

              if (is_array($value)) {
                foreach ($value as $entity => $property) {
                  $method = "get".Util::classCase($property);
                  $paramValue = $entity->$method();
                  $stringParams .= $paramValue . "/";
                }
              } else {
                $method = "get".Util::classCase($property);
                $paramValue = $entity->$method();
              }

              if (is_string($key)) {
                $stringParams = $key = "=";
              }
            }

            // Util::replace([], );
            $contents .= $content;
          }
        }
        $cols .= Util::replace(["{td_content}"=>$contents], $td);
      }
      $rows .= Util::replace(["{tr_content}"=>$cols], $tr);
      $cols = "";
    }

    // Caso haja ações na tabela
    if (count($actions)>0) {
      $tableHeader .= Util::replace(["{th_content}"=>"Ações"], $th);
    }

    $tableHeader = Util::replace(["{tr_content}"=>$tableHeader], $tr);
    $tableHeader = Util::replace(["{content}"=>$tableHeader], $tableHead);
    $out = Util::replace(["{table_content}"=>$tableHeader.$rows], $table);
    return $out;
  }

  static function row($params) {
    $replaceParams = [];
    foreach ($params as $key => $value) {
      if (is_array($value)) {
        foreach ($value as $entity => $property) {
          $replaceParams[] = "{{" . $value . "}}";
        }
      } else {
        if (is_string($key)) {

          $replaceParams[] = "{{" . $value . "}}";
        }
      }
    }

    return $replaceParams;
  }

  /*
    Adiciona uma coluna na tabela de acordo com a lista de valores passada
    por parametro
  */
  static function addTableColumn($tableId, $list, $header, $sibling) {
    $script = "<script >{content}</script>";
    $method = "addTableColumn('$tableId', '$list', '$header', '$sibling')";
    $out = Util::replace(["{content}"=>$method], $script);
    return $out;
  }

  /*
    Adiciona uma coluna na tabela de acordo com a lista de valores passada
    por parametro
  */
  static function addTextColumn($tableId, $columnId, $list) {
    $script = "<script >{content}</script>";
    $method = "addTextColumn('$tableId', '$columnId', '$list')";
    $out = Util::replace(["{content}"=>$method], $script);
    return $out;
  }

  static function action($controller, $action, $args=[], $callbacks=[],
      $actionName="", $icon="") {

    $replaces = [];
    $params = "";
    $cbks = "";

    $link = Util::open("/sistem_templates/link.html");
    // var_dump($args);
    foreach ($args as $arg => $value) {
      $params .= $value . "/";
    }

    $action = "/" . $controller . "/" . $action . "/" . $params;

    foreach ($callbacks as $event => $callback) {
      $cbks .= $event . "=" . "\"" . $callback . "()" . "\"";
    }

    $replaces["{name}"] = $actionName;
    $replaces["{action}"] = $action;
    $replaces["{options}"] = $cbks;
    $replaces["{icon}"] = Util::icon($icon);

    $out = Util::replace($replaces, $link);

    return $out;
  }

  static function form($object_array, $action, $method, $exclude=[], $attr=[]) {
    $out = "";
    $form = "";
    $attributes = "";

    $form = Util::open("/sistem_templates/form.html");
    $formCol = Util::open("/sistem_templates/form_col.html");
    $row = Util::open("/sistem_templates/row.html");
    $input = Util::open("/sistem_templates/input.html");
    $select = Util::open("/sistem_templates/select.html");
    $option = Util::open("/sistem_templates/option.html");
    $inputSubmit = Util::open("/sistem_templates/input_submit.html");

    $types = [
      "integer"=>"number",
      "date"=>"date",
      "float"=>"text",
      "string"=>"text",
      "boolean"=>"checkbox"
    ];

    include_once $_SERVER["DOCUMENT_ROOT"] . "/strings/styleClasses.php";

    foreach ($object_array as $object) {

      $className = Util::shortClassName($object);
      $className = Util::snakeCase($className);

      foreach ($attr as $key => $value) {
        $attributes .= $key . "=" . "\"" . $value . "\"";
      }

      $replaces = [
        "{col_class}"=>COL_CLASS,
        "{col_size}"=>COL_SIZE,
        "{row_class}"=>ROW_CLASS,
        "{label_font_size}"=>LABEL_FONT_SIZE,
        "{input}"=>INPUT,
        "{button}"=>BUTTON,
        "{color_button}"=>BUTTON_COLOR,
        "{row_class}"=>ROW_CLASS,
        "{class_name}"=>$className,
        "{action}"=>$action,
        "{method}"=>$method,
        "{attributes}"=>$attributes,
      ];


      foreach($object as $key => $value) {
        if (!in_array($key, $exclude)){
          $snakeKey = Util::snakeCase($key);
          $replaces["{name}"] = $snakeKey;
          $replaces["{label_name}"] = Util::verify("label", $object::props()[$snakeKey]) == "" ?
              ucfirst($object::props()[$snakeKey]["name"]) : $object::props()[$snakeKey]["label"];
          $replaces["{type}"] = $types[$object::props()[$snakeKey]["type"]];

          $formInput = Util::replace($replaces, $formCol);

          if (array_key_exists("references", $object::props()[$snakeKey])) {
            $reference_name = "\model\dao\\" . $object::props()[$snakeKey]["references"] . "DAO";
            $reference = new $reference_name();

            $list = $reference->findAll();

            $options = "";
            foreach ($list as $item) {
              $m = "get" . $object::props()[$snakeKey]["referenceId"];

              $replaces["{value}"] = $item->$m();
              $replaces["{content}"] = $item; // Aqui o método toString é acionado

              $options .= Util::replace($replaces, $option);
            }

            $replaces["{id}"] = $key;
            $replaces["{content}"] = $options;
            $formInput = Util::replace($replaces, $select);
          } else {
            $formInput = Util::replace($replaces, $formInput);
          }

          $out .= $formInput;
        }
      }
    }

    $replaces["{name}"]="submit";
    $replaces["{type}"]="submit";
    $replaces["{button_name}"]="Salvar";
    $replaces["{input}"]=BUTTON . " " . BUTTON_COLOR;

    $submit = Util::replace($replaces, $inputSubmit);

    $replaces["{content_row}"]=$out.$submit;
    $out = Util::replace($replaces, $row);

    $replaces["{content_form}"]=$out;
    $out = Util::replace($replaces, $form);

    return $out;
  }

  static function addFieldForm($entityName, $id, $label, $type, $sibling) {
    $out = "";

    $form_col = Util::open("/sistem_templates/form_col.html");
    $form_col = Util::replace(["\n"=>""],$form_col);

    $replaces = [
      "{col_class}"=>COL_CLASS,
      "{col_size}"=>COL_SIZE,
      "{label_font_size}"=>LABEL_FONT_SIZE,
      "{input}"=>INPUT,
      "{name}"=>$id,
      "{type}"=>$type,
      "{label_name}"=>$label,
      "{class_name}"=>$entityName
    ];

    $script = "<script>addFieldForm({sibling}, '{html}');</script>";

    $form_col = Util::replace($replaces, $form_col);
    $out = Util::replace(["{sibling}"=>$sibling, "{html}"=>$form_col], $script);

    return $out;
  }

  static function img($src) {
    $img = "<img src='" . Util::icon($src) . "'/>";
    return $img;
  }

  static function a($content="link", $href="#", $params) {

    $a = "<a href='$href$params' >$content</a>";

    return $a;
  }

  static function p($array) {
    foreach ($array as $key => $value) {

    }

  }

  static function paginate($url, $page, $qtde, $total) {

      $divPrincipal = "<div class=\"w3-center\">{content}</div>";
      $divBar = "<div class=\"w3-bar\">{first}{previous}{pages}{next}{last}</div>";
      $link = "<a href=\"#\" class=\"w3-bar-item w3-button {active}\" {disabled} {onclick}>{label}</a>";
      $onClick = "onclick=\"ajax('{url}', {'page':{page}, 'qtde':qtdePorPagina.options[qtdePorPagina.selectedIndex].value}, '{callbackId}', '{msg}', 'post', 'html', true)\"";

      $pages = ceil(($total/$qtde));

      $arrayPages = [];
      $ultima = intval($qtde);
      $primeira = 1;
      for ($i = 1; $i <= $pages; $i++) {
        if ($ultima == $i) {
          $ultima += intval($qtde-2);
          $primeira = $i-1;
        }
        $arrayPages[] = ["primeira"=>$primeira, "ultima"=>$ultima];
      }

      $p = "";

      $primeira = $arrayPages[$page-1]["primeira"];
      $ultima = $arrayPages[$page-1]["ultima"];

      for ($i=$primeira, $count=1; $i<=$ultima; $i++, $count++) {
  			$page == $i ? $active = " w3-teal" : $active = "";

        $event = Util::replace([
          "{url}"=>$url,
          "{page}"=>$i,
          "{callbackId}"=>"despesas"
        ], $onClick);

        $p .= Util::replace([
          "{label}"=>$i,
          "{active}"=>$active,
          "{disabled}"=>"",
          "{onclick}"=>$event
        ], $link);

        if ($count == 5 || $i == count($arrayPages)) break;
      }

      $page == 1 ? $disabled = "w3-disabled" : $disabled = "";
      $page == $pages ? $disabled = "w3-disabled" : $disabled = "";

      // Ir para primeira página
      $event = Util::replace([
        "{url}"=>$url,
        "{page}"=>'1',
        "{callbackId}"=>"despesas",
        "{msg}"=>""
      ], $onClick);
      $first = Util::replace([
        "{label}"=>"&#10094;&#10094;",
        "{active}"=>"",
        "{disabled}"=>$disabled,
        "{onclick}"=>$event
      ], $link);

      // Ir para página anterior
      $event = Util::replace([
        "{url}"=>$url,
        "{page}"=>$page-1,
        "{callbackId}"=>"despesas"
      ], $onClick);
      $previous = Util::replace([
        "{label}"=>"&#10094; <span class=\"w3-hide-small\">Previous</span>",
        "{active}"=>"",
        "{disabled}"=>$disabled,
        "{onclick}"=>$event
      ], $link);

      // Ir para próxima página
      $event = Util::replace([
        "{url}"=>$url,
        "{page}"=>($page+1) <= $pages ? ($page+1) : 0,
        "{callbackId}"=>"despesas"
      ], $onClick);
      $next = Util::replace([
        "{label}"=>"<span class=\"w3-hide-small\">Next</span> &#10095;",
        "{active}"=>"",
        "{disabled}"=>$disabled,
        "{onclick}"=>$event
      ], $link);

      // Ir para última página
      $event = Util::replace([
        "{url}"=>$url,
        "{page}"=>$pages,
        "{callbackId}"=>"despesas"
      ], $onClick);
      $last = Util::replace([
        "{label}"=>"&#10095;&#10095;",
        "{active}"=>"",
        "{disabled}"=>$disabled,
        "{onclick}"=>$event
      ], $link);

      $out = Util::replace([
        "{first}"=>$first,
        "{previous}"=>$previous,
        "{pages}"=>$p,
        "{next}"=>$next,
        "{last}"=>$last
      ], $divBar);

      $out = Util::replace(["{content}"=>$out], $divPrincipal);

      return $out;
  }

}
