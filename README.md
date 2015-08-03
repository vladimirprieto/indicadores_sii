# Indicadores SII de Chile
métedos para rescatar en línea los indicadores económicos de SII de Chile a través de PHP

ejemplo de uso:

//Creamos la instancia de la clase UF
$indicador = new Indicadores();

//Llamamos al metodo uf y guardamos el resultado (valor de la uf) en una variable
$res = $indicador->uf();

// Muestra "22296.19"
echo "UF:$res<br>";



$res = $indicador->utm();

// Muestra "22296.19"
echo "UTM:$res<br>";


echo "<pre>".print_r($indicador->iu(),true)."</pre>";
