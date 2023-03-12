<?php

try{
    $baglanti=new PDO("mysql:host=localhost;dbname=haberbotu;charset=utf8","root","");
    $baglanti->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e){
    die($e->getMessage());
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>HABER BOTUMUZ</title>

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
<style>
body {
height:100%;
width:100%;
position:absolute;
	
}
</style>
  </head>

  <body>
  
  <div class="container-fluid h-100">
  
  
  
  		<div class="row h-100">
        		<div class="col-lg-3 border-right text-center">
                
                		<div class="row">                      
                        
                        <div class="col-lg-12 bg-danger text-white"><h4>TÜM HABERLER</h4></div>
                        
                        <div class="col-lg-12">
                            <?php
                            $basliklar=array();
                            $bakbakalim=$baglanti->prepare("SELECT * FROM localtablo");
                            $bakbakalim->execute();
                            while($sonuc=$bakbakalim->fetch(PDO::FETCH_ASSOC)):
                                $basliklar[]=$sonuc["baslik"];
                            endwhile;
                            if(@$_POST["ilkbuton"]==""):
                                
                            $veri = file_get_contents("http://localhost/haberbot/index.php");
                            $desen='@<div class="col-lg-4 col-md-4 col-sm-4 mt-2">(.*?)</div>@si';
                            preg_match_all($desen,$veri,$linkler);
                            $toplamsayi=count($linkler[1]);
                            for($i=0; $i<$toplamsayi; $i++):

                                $desen3='@<a id="haberlink" href="(.*?)">(.*?)</a>@si';
                                preg_match_all($desen3,$linkler[1][$i],$linkvebaslik);
    
                                if(in_array($linkvebaslik[2][0],$basliklar)):
                                    continue;
                                endif;



                                echo '<form action="" method="post">';
                                //RESİM
                                $desen2='@src="(.*?)" height="200">@si';
                                preg_match_all($desen2,$linkler[1][$i],$resim);
                                echo '<img src="http://localhost/haberbot/'.$resim[1][0].'"width="250" heigh="150">';
                                echo "<br>";
                                
                                echo '<input type="hidden" class="form-control m-2" name="res[]" value="'.$resim[1][0].'">';
                                //BAŞLIK
                                                          
                                echo '<input type="text" class="form-control m-2" name="baslik[]" value="'.$linkvebaslik[2][0].'">';
                                //--İÇERİYE GİRME--
                                //DETAY
                                $detay = file_get_contents("http://localhost/haberbot/".$linkvebaslik[1][0]);
                                $desen4='@id="habericerik">(.*?)</div>@si';
                                preg_match_all($desen4,$detay,$detayagirdim);
                                echo '<textarea name="icerik[]" class="form-control" rows="5">'.$detayagirdim[1][0].'</textarea>';
                                
                                echo '
                            <input type="submit" name="ilkbuton" class="btn btn-success mb-3 border-bottom" value="KAYDET">
                            </form>';
                            echo "<hr>";
                            endfor;
                            
                        else:
                            /*
                            //FARKLI FORMLARDADN ÇOKLU VERİ ALMAK İÇİN KULLANILABİLİR
                            foreach($_POST["baslik"] as $key=>$val):
                                echo $val."<br>";
                                echo $_POST["icerik"][$key];
                                echo "<br>";
                                echo $_POST["res"][$key]."<br><hr>";
                            endforeach;
                            */
                            $ekle=$baglanti->prepare("INSERT INTO localtablo (baslik,icerik,resim) VALUES(:baslik,:icerik,:resim)");
                            foreach($_POST["baslik"] as $key=>$val):

                                $rescek = file_get_contents("http://localhost/haberbot/".$_POST["res"][$key]);


                                $uzantiyaulasiyorum=explode(".",$_POST["res"][$key]);
                                $sonuzanti=".".$uzantiyaulasiyorum[count($uzantiyaulasiyorum)-1];
                                /*
                                //kfdpasSDAodf458816465.jpg
                                $asama1=str_shuffle("kfdpasSDAodf");
                                $asama2=mt_rand(0,1234568);
                                $dosyaad="res/".$asama1.$asama2.$sonuzanti;
                                */
                                //jf8sad74d89a7f8sa5.jpg
                                $karisacak=str_shuffle("kfdpasSDAodf".mt_rand(0,1234568));
                                $dosyaad="res/".$karisacak.$sonuzanti;
                                $indir=fopen($dosyaad,"a+"); //dosya oluşturma
                                fwrite($indir,$rescek);//dosyanın içini doldurma
                                fclose($indir);

                                
                                $ekle->execute(array(
                                    ':baslik'=>$val,
                                    ':icerik'=>$_POST["icerik"][$key],
                                    ':resim'=>$dosyaad
                                ));
                                

                            endforeach;
                            echo '<div class="alert alert-success m-2">EKLEME BAŞARILI</div>';


                        endif;

                            
                            ?>
                        </div>
                        
                        </div>
                
               
                
                
        		</div>
                
                <div class="col-lg-3 border-right text-center">
                
                		<div class="row">                      
                        
                        <div class="col-lg-12 bg-danger text-white"><h4>SON DAKİKALAR</h4></div>
                        
                        <div class="col-lg-12">
                            <?php
                            if(@$_POST["ikincibuton"]==""):
                                echo '<form action="" method="post">';
                            $sondk = file_get_contents("http://localhost/haberbot/index.php");
                            $desen5='@id="soneklenenstil">(.*?)</div>@si';
                            preg_match_all($desen5,$sondk,$sondak);
                            $toplamsayi2=count($sondak[1]);
                            for($i=0; $i<$toplamsayi2; $i++):
                                $desen6='@<a href="(.*?)">(.*?)</a>@si';
                                preg_match_all($desen6,$sondak[1][$i],$link);
                                echo '<input type="text" class="form-control m-2" name="baslik[]" value="'.$link[2][0].'">';
                                //--İÇERİYE GİRME--
                                $detay2 = file_get_contents("http://localhost/haberbot/".$link[1][0]);
                                $desen7='@id="habericerik">(.*?)</div>@si';
                                preg_match_all($desen7,$detay2,$detayagirdim);
                                echo '<textarea name="icerik[]" class="form-control" rows="5">'.$detayagirdim[1][0].'</textarea>';
                                $desen8='@<img class="card-img-top" src="(.*?)"@si';
                                preg_match_all($desen8,$detay2,$resim2);
                                echo '<img src="http://localhost/haberbot/'.$resim2[1][0].'"width="250" heigh="150">';
                                echo '<input type="hidden" class="form-control m-2" name="res[]" value="'.$resim2[1][0].'">';
                                echo "<hr>";
                            endfor;
                            echo '
                            <input type="submit" name="ikincibuton" class="btn btn-success" value="KAYDET">
                            </form>';
                        else:
                            $ekle=$baglanti->prepare("INSERT INTO localtablosondk (baslik,icerik,resim) VALUES(:baslik,:icerik,:resim)");
                            foreach($_POST["baslik"] as $key=>$val):
                                $ekle->execute(array(
                                    ':baslik'=>$val,
                                    ':icerik'=>$_POST["icerik"][$key],
                                    ':resim'=>$_POST["res"][$key]
                                ));
                            endforeach;
                            echo '<div class="alert alert-success m-2">EKLEME BAŞARILI</div>';
                        endif;
                            ?>
                        </div>
                        
                        </div>
                
        		</div>
                
                <div class="col-lg-3 border-right text-center">
                
                		<div class="row">                      
                        
                        <div class="col-lg-12 bg-danger text-white"><h4>SON EKLENENLER</h4></div>
                        
                        <div class="col-lg-12">
                        <?php
                            //SONDAKİKA VE TÜM HABERLER KISMI İLE AYNI KODLAR KULLANILILARAK VERİ AKTARILABİLİR
                            $sonekl = file_get_contents("http://localhost/haberbot/index.php");
                            $desen9='@id="yeneklenenstil">(.*?)</div>@si';
                            preg_match_all($desen9,$sonekl,$sonek);
                            $toplamsayi3=count($sonek[1]);
                            for($i=0; $i<$toplamsayi3; $i++):
                                $desen10='@<a href="(.*?)">(.*?)</a>@si';
                                preg_match_all($desen10,$sonek[1][$i],$link2);
                                echo "Lİnk: ". $link2[1][0];
                                echo "<br>";
                                echo "Başlık: ". $link2[2][0];
                                echo "<br>";
                                //--İÇERİYE GİRME--
                                $detay3 = file_get_contents("http://localhost/haberbot/".$link2[1][0]);
                                $desen11='@id="habericerik">(.*?)</div>@si';
                                preg_match_all($desen11,$detay3,$detayagirdi2);
                                echo "İçerik: ". $detayagirdi2[1][0];
                                echo "<br>";
                                $desen12='@<img class="card-img-top" src="(.*?)"@si';
                                preg_match_all($desen12,$detay3,$resim3);
                                echo "Resim Yolu: ". $resim3[1][0];
                                echo "<br><hr>";
                            endfor;
                            ?>
                        </div>
                        
                        </div>
                
                
        		</div>
                
                <div class="col-lg-3 text-center">
                
                		<div class="row">                      
                        
                        <div class="col-lg-12 bg-danger text-white"><h4>KATEGORİYE GÖRE</h4></div>
                        
                        <div class="col-lg-12">
                            <?php
                            $katagori = file_get_contents("http://localhost/haberbot/index.php");
                            $desen13='@<li class="nav-item">(.*?)</li>@si';
                            preg_match_all($desen13,$katagori,$kat);
                            $toplamsayi3=count($kat[1]);
                            echo '<form action="" method="post">
                            <select name="katid" class="btn btn-control m-2">
                            <option value="0">SEÇ</option>';
                            for($i=0; $i<$toplamsayi3; $i++):
                                $desen14='@<a class="nav-link" href="(.*?)">(.*?)</a>@si';
                                preg_match_all($desen14,$kat[1][$i],$veriler);
                                $id=str_replace("index.php?katid=","",$veriler[1][0]);
                                echo '<option value="'.$id.'">'.$veriler[2][0].'</option>';
                            endfor;
                            echo '</select>
                            <input type="submit" name="btn" class="btn btn-primary">
                            </form>';    
                            if(@$_POST["katid"]!=""):
                                $son2 = file_get_contents("http://localhost/haberbot/index.php?katid=".$_POST["katid"]);
                                $desen15='@<div class="col-lg-4 col-md-4 col-sm-4 mt-2">(.*?)</div>@si';
                                preg_match_all($desen15,$son2,$linkler2);
                                $toplamsayi5=count($linkler2[1]);
                                for($i=0; $i<$toplamsayi5; $i++):
                                    $desen16='@<img class="card-img-top" src="(.*?)" height="200">@si';
                                    preg_match_all($desen16,$linkler2[1][$i],$resim2);
                                    echo "Resim yolu: ". $resim2[1][0];
                                    echo "<br>";
                                    $desen17='@<a id="haberlink" href="(.*?)">(.*?)</a>@si';
                                    preg_match_all($desen17,$linkler2[1][$i],$linkvebaslik2);
                                    echo "Lİnk yolu: ". $linkvebaslik2[1][0];
                                    echo "<br>";
                                    echo "Başlık: ". $linkvebaslik2[2][0];
                                    echo "<br>";
                                    //--İÇERİYE GİRME--
                                    $detay9 = file_get_contents("http://localhost/haberbot/".$linkvebaslik2[1][0]);
                                    $desen18='@id="habericerik">(.*?)</div>@si';
                                    preg_match_all($desen18,$detay9,$detayagirdim2);
                                    echo "İçerik: ". $detayagirdim2[1][0];
                                    echo "<br><hr>";
                                endfor;
                            endif;
                            ?>
                        </div>
                        
                        </div>
                
        		</div>
        
        
        </div>
  
  
  
  
  </div>
  
  
 
  

  
  

  </body>

</html>
