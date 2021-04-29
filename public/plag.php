<?php
function ncd($x, $y) { 
  $cx = strlen(gzcompress($x));
  $cy = strlen(gzcompress($y));
  return (strlen(gzcompress($x . $y)) - min($cx, $cy)) / max($cx, $cy);
}   
function similar_NCD_gzip($sx, $sy, $prec=0, $MAXLEN=90000) {
    # NCD with gzip artifact correctoin and percentual return.
    # sx,sy = strings to compare. 
    # Use $prec=-1 for result range [0-1], $pres=0 for percentual,
    #     $pres=1 or =2,3... for better precision (not a reliable)  
    # Use MAXLEN=-1 or a aprox. compress lenght. 
    # For NCD definition see http://arxiv.org/abs/0809.2553
    # (c) Krauss (2010).
      $x = $min = strlen(gzcompress($sx));
      $y = $max = strlen(gzcompress($sy));
      $xy= strlen(gzcompress($sx.$sy));
      $a = $sx;
      if ($x>$y) { # swap min/max
        $min = $y;
        $max = $x;
        $a = $sy;
      }
      $res = ($xy-$min)/$max; # NCD definition.
      
      # Optional correction (for little strings):
      if ($MAXLEN<0 || $xy<$MAXLEN) {
        $aa= strlen(gzcompress($a.$a));
        $ref = ($aa-$min)/$min;
        $res = $res - $ref; # correction
      }
      return ($prec<0)? $res: 100*round($res,2+$prec);
    }    
print(ncd('this is a test', 'this was a test') . '<br/>');
print(similar_NCD_gzip('this is a test', 'this was a test') . '<br/><br/>');

print(ncd('this is a test', 'thdsiss tdext isdsss compdletely different') . '<br/>');
print(similar_NCD_gzip('this is a test', 'this text is completely different'));

?>