<?php

namespace App\Traits;
use Illuminate\Support\Facades\File;

trait SavePdfImageTrait
{
    private function savePdfImage($pdfImageFolderUrl, $pdfImage) {
        if ($pdfImage->isValid()) {
            // Guarda la pdfImage en la carpeta public especificada
            $currentDate = date('Y-m-d_H-i-s');
            $pdfImageName = $currentDate . '_' . $pdfImage->getClientOriginalName();
            $savedImagePdf = $pdfImage->move(public_path($pdfImageFolderUrl), $pdfImageName);
            $pdfImageUrl = $savedImagePdf ? asset($pdfImageFolderUrl . '/' . $pdfImageName) : null;
            return $pdfImageUrl;
        } else {
            return response()->json(['mensaje' => 'Error al guardar la imagen'], 400);
        }
    }

    public function deleteImage($pathImagen)
    {
        // $urlImagen = "http://localhost:8000/storage/users/2023-05-25_18-58-08_Imagen de WhatsApp 2022-09-17 a las 20.18.03.jpg";
    
        $rutaImagen = public_path(parse_url($pathImagen, PHP_URL_PATH));
    
        if (File::exists($rutaImagen)) {
            File::delete($rutaImagen);
            // Opcional: También puedes eliminar la imagen de la base de datos si está almacenada allí.
            // Tu lógica para eliminar la imagen de la base de datos aquí.
            // return "Imagen eliminada correctamente.";
            return true;
        }else{
            return false;
        }
    
        
    }

     /**
     * @param int $stockdb de la tabla de compras
     * @param int $stockuser pa saber si aumenta o merma
     * @return bool 
     */
    public function comparar($stockdb, $stockuser){
  
        return $stockdb > $stockuser ? true : ($stockdb < $stockuser ? false : $stockdb );
        
      }
  
      /**
       * @param int $stockdb de la tabla controlinventario
       * @param int $stockuser pa saber si aumenta o merma
       * @param bool $result
       * @return int 
       */
     public function calcular($stockdb, $stockuser, $result){
      
      return $result === false ? $stockuser - $stockdb : ($result === true ? $stockdb - $stockuser :  $stockdb);
      
      }
   
      /**
       * @param int $stock de la tabla compras
       * @param int $resultado 
       * @param bool $result
       * @return int el stock que vamos a enviar a la tabla de inventario
       */
     public function calcularbd($stock, $resultado, $result){
      
        return $result === true ? $stock - $resultado : ( $result === false ? $stock + $resultado :  $stock);
      
      }

        /**
       * @param int $stock de la tabla inventario
       * @param int $resultado 
       * @param bool $result
       * @return int el stock que vamos a enviar a la tabla de inventario
       */

      function calcularbdControlInventario($stock, $resultado, $result){
      
        return $result === true ? $stock + $resultado : ( $result === false ? $stock - $resultado :  $stock);
      
      }




}