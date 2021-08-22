<?php 
namespace App\Models;
use CodeIgniter\Model;

class UfModel extends Model
{
    protected $table = 'uf';

    protected $primaryKey = 'uf_id';
    
    protected $allowedFields = ['codigo', 'nombre', 'unidad_medida', 'fecha', 'valor'];
}