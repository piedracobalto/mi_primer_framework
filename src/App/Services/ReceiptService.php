<?php

declare(strict_types=1);

namespace App\Services;

use Framework\Database;
use Framework\Exceptions\ValidationException;
use App\Config\Paths;

class ReceiptService
{
    public function __construct(private Database $db)
    {
        
    }

    public function validateFile(?array $file)
    {
        /* Validacion de cargado */
        if (!$file || $file["error"] !== UPLOAD_ERR_OK) 
        {
            throw new ValidationException([
                "receipt" => ["Failed to open file"]
            ]);
        }

        /* Validacion de tamanio */
        $max_file_size_mb = 3 * 1024 ** 2;

        if ($file["size"] > $max_file_size_mb) 
        {
            throw new ValidationException([
                "receipt" => ["File upload is too large"]
            ]);
        }

        /* Validacion de nombre del archivo  */
        $original_file_name = $file["name"];

        /* Verifica que el nombre del archivo $original_file_name 
        sólo contenga letras, números, espacios, puntos, guiones y guiones bajos.
        Si contiene algún otro carácter (como @, !, ?, etc.), es falso. */
        if (!preg_match('/^[A-Za-z0-9\s._-]+$/',$original_file_name)) 
        {
            throw new ValidationException([
                "receipt" => ["Invalid file name"]
            ]);
        }

        /* Validacion de tipo de archivo */

        $client_mime_type = $file["type"];
        $allowed_mime_types = ['image/jpeg','image/png','application/pdf'];

        if (!in_array($client_mime_type,$allowed_mime_types)) 
        {
            throw new ValidationException([
                "receipt" => ["Invalid file type"]
            ]);
        }

    }

    public function upload(array $file, int $transaction)
    {
        $file_extension = pathinfo($file["name"],PATHINFO_EXTENSION);
        $new_file_name = bin2hex(random_bytes(16)) . '.' . $file_extension;

        $upload_path = Paths::STORAGE_UPLOADS . "/" . $new_file_name;

        /* la funcion move_uploaded_file va a intentar mover el archivo desde la carpeta temporal 
        ($file["tmp_name"]) a la ubicación final ($upload_path). En el caso de que falle tira la
        excepcion. Caso contrario el archivo se moverá a $upload_path */
        if (!move_uploaded_file($file["tmp_name"],$upload_path)) 
        {
            throw new ValidationException([
                "receipt" => ["Failed to upload file"]
            ]);
        }

        $this->db->query(
            "INSERT INTO receipts(
                transaction_id, original_filename, storage_filename, media_type
            )
            VALUES (:transaction_id, :original_filename, :storage_filename, :media_type)
            ",
            [
                "transaction_id" => $transaction,
                "original_filename" => $file["name"],
                "storage_filename" => $new_file_name,
                "media_type" => $file["type"],
            ]
        );
    }

    public function getReceipt(string $id)
    {
        $receipt = $this->db->query(
            "SELECT * FROM receipts where id = :id",
            [
                "id" => $id
            ]
        )->find();

        return $receipt;
    }

    public function read(array $receipt)
    {
        $file_path = PATHS::STORAGE_UPLOADS . "/" . $receipt["storage_filename"];

        if(!file_exists($file_path))
        {
            redirectTo("/");
        }

        /* La función header("Content-Disposition: inline") en PHP 
        se utiliza para controlar cómo el navegador debe mostrar o manejar un archivo 
        enviado desde el servidor. 
        Los parametros de Content-Disposition pueden ser inline o attachment

        inline:      Muestra el contenido directamente en el navegador (si es posible) 
        attachment:  Fuerza la descarga del contenido como archivo                     

        */
        header("Content-Disposition: inline; filename={$receipt['original_filename']}");
        header("Content-Type: {$receipt['media_type']}");

        readfile($file_path);
    }

    public function delete(array $receipt)
    {
        $file_path = PATHS::STORAGE_UPLOADS . "/" . $receipt["storage_filename"];

        /* unlink() borra el archivo */
        unlink($file_path);

        $this->db->query(
            "DELETE FROM receipts where id = :id",
            [
                "id" => $receipt["id"]
            ]);
    }
}