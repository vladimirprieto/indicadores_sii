<?php

class Indicadores{

  private $year;
  private $mes;
  private $dia;
  private $res;

  public function __construct($fecha = "") {
    // si no le damos fecha a la instancia, se usa la actual
    if ($fecha == "") { $fecha = date('d-m-Y'); }
    $fecha = explode("-",$fecha);

    $this->dia  = (int) $fecha[0];
    $this->mes  = (int) $fecha[1];
    $this->year = (int) $fecha[2];

  }

  private function limpia($valor){
    //eliminamos la separación de miles
    $limpio = str_replace(".","",$valor);
    //cambiamos signo decimal
    $limpio = str_replace(",",".",$limpio);
    //eliminamos signo peso
    $limpio = str_replace("$ ","",$limpio);

    //textos

    //eliminamos si es nulo (cosas de SII)
    $limpio = ( $limpio == '_' || $limpio == '-.-' || $limpio == '--' ) ? '' : $limpio;
    //exento
    $limpio = ( $limpio == 'Exento' ) ? '' : $limpio;
    // //y mas
    // $limpio = ( strpos( $limpio, 'Y MAS' ) !== false ) ? '' : $limpio;
    //MAS
    $limpio = ( strpos( $limpio, 'MAS' ) !== false ) ? '' : $limpio;


    return trim($limpio);
  }

  /*==========================
  =            UF            =
  ==========================*/
  
  /**
  *
  * sacado de http://www.chw.net/foro/webmasters/287574-extraer-uf-php-solucion-definitiva-3.html#post11322551
  * créditos del usuario Seth
  *
  **/
  
  private function getUf() {
      
    $url = "http://www.sii.cl/pagina/valores/uf/uf" . $this->year . ".htm";
    $dom = new DOMDocument;
    $dom->preserveWhiteSpace = false;
    @$dom->loadHTMLFile($url);
        
    $domxpath = new DOMXpath($dom);
    $arr = $domxpath->query("//td");
    $d = 0;
    $m = 0;

    $this->res = [];

    foreach ($arr as $uf) {
        $this->res[$m++][$d] = $uf->nodeValue;
        if ($m>11) {
          $m = 0;
          $d++;
        }
    }
      
  }

  public function uf($original=false) {

    $this->getUf();

    //sacando la separacion de miles
    $uf_limpia = $this->res[$this->mes-1][$this->dia-1];
    if ( !$original )
      $uf_limpia = $this->limpia( $uf_limpia );

    return $uf_limpia;
  }
  /*-----  End of UF  ------*/
  









  /*======================================
  =            IMPUESTO ÚNICO            =
  ======================================*/
  
  private function getIU() {

    $urls = [
      '201608' => "http://www.sii.cl/pagina/valores/segundacategoria/imp_2da_agosto2016.htm" ,
      '201601' => "http://www.sii.cl/pagina/valores/segundacategoria/imp_2da_enero2016.htm" ,
      '201512' => "http://www.sii.cl/pagina/valores/segundacategoria/imp_2da_diciembre2015.htm" ,
      '201511' => "http://www.sii.cl/pagina/valores/segundacategoria/imp_2da_noviembre2015.htm" ,
      '201510' => "http://www.sii.cl/pagina/valores/segundacategoria/imp_2da_octubre2015.htm" ,
      '201509' => "http://www.sii.cl/pagina/valores/segundacategoria/imp_2da_septiembre2015.htm" ,
      '201508' => "http://www.sii.cl/pagina/valores/segundacategoria/imp_2da_agosto2015.htm" ,
    ];

    $dom = new DOMDocument;
    $dom->preserveWhiteSpace = false;

    //buscamos el mes que corresponde a la fecha entregada a la clase
    $fecha_entregada = $this->year.$this->mes;
    $fecha_max = array_keys($urls)[0];
    foreach ($urls as $i => $url)
      if ( (integer)$i >= (integer)$fecha_entregada )
        $fecha_max = $i;

    @$dom->loadHTMLFile($urls[$fecha_max]);
        
    $domxpath = new DOMXpath($dom);
    $arr = $domxpath->query("//td");

    $this->res = [];

    $periodos = ['MENSUAL','QUINCENAL','SEMANAL','DIARIO'];
    $per = '';
    $contador_columna = 0;
    $contador_fila = 0;
    foreach ($arr as $celda) {

        $valor = $celda->nodeValue;
        if ( in_array($valor,$periodos) ){
          $per = $valor;
          $contador_columna = 0;
          $contador_fila = 0;
          continue;
        }

        if ( $contador_columna == 5 ){
          $contador_columna = 0;
          $contador_fila++;
          continue;
        }

        $this->res[$per][$contador_fila][$contador_columna] = $this->limpia($valor);
        $contador_columna++;


    }
      
  }

  public function iu($periodo='') {
    $this->getIU();

    $retorno = $this->res;
    if ( $periodo != '' )
      $retorno = $this->res[$periodo];

    return $retorno;
  }

  /*-----  End of IMPUESTO ÚNICO  ------*/






  /*===========================
  =            UTM            =
  ===========================*/
  
  private function getUtm() {
      
    $url = "http://www.sii.cl/pagina/valores/utm/utm" . $this->year . ".htm";
    $dom = new DOMDocument;
    $dom->preserveWhiteSpace = false;
    @$dom->loadHTMLFile($url);
        
    $domxpath = new DOMXpath($dom);
    $arr = $domxpath->query("//td");
    $m = 1;

    $this->res = [];
    $col = 0;
    foreach ($arr as $utm) {

        if ( $col == 0 )
          $this->res[$m++] = $utm->nodeValue;

        $col = ($col == 5) ? 0 : ( $col + 1 );

    }
      
  }

  public function utm($original=false) {

    $this->getUtm();

    //sacando la separacion de miles
    $utm_limpia = $this->res[$this->mes];
    if ( !$original )
      $utm_limpia = $this->limpia( $utm_limpia );

    return $utm_limpia;
  }
  
  /*-----  End of UTM  ------*/
  
  



  
}

