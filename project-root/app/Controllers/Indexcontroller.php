<?php

namespace App\Controllers;
use App\Models\UfModel;

class Indexcontroller extends BaseController
{
	public function index()
	{
		$apiUrl = 'https://mindicador.cl/api';
		$apiUrlMes = 'https://mindicador.cl/api/uf';
		$ufModel = new UfModel();
		
		if (ini_get('allow_url_fopen')) 
		{
			$json = file_get_contents($apiUrl);
			$jason2 = file_get_contents($apiUrlMes);
		} else 
		{
			$curl = curl_init($apiUrl);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			$json = curl_exec($curl);
			curl_close($curl);

			$curl = curl_init($apiUrlMes);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			$jason2 = curl_exec($curl);
			curl_close($curl);
		}

		$ufMes = json_decode($jason2);

		//si no se encuentran datos en la base datos (tabla uf), se le insertan todos los valores de la uf del último mes
		if(empty($ufModel->findAll()))
		{
			for($i = 0; $i < count($ufMes->serie); $i++)
			{
				$data = [
					'codigo' => $ufMes->codigo,
					'nombre' => $ufMes->nombre,
					'unidad_medida' => $ufMes->unidad_medida,
					'fecha' => $ufMes->serie[$i]->fecha, 
					'valor' => $ufMes->serie[$i]->valor,
				];
				$ufModel->insert($data);	
			}
		}
		//se le envía a la vista los tipos de indicador para desplegar en el Select y los datos historicos de la uf para mostrarlos en la tabla
		$datosUf = $ufModel->findAll();
		$indicators = json_decode($json, true);	
		$datos = array_combine(array_column($indicators, 'nombre'), array_column($indicators, 'codigo'));

		$data = [
			'datos' => $datos,
			'datosUf' => $datosUf
		];
		return view('solutoria', $data);
	}

    public function store() 
	{
        $ufModel = new UfModel();
        $data = [
            'codigo' => $this->request->getVar('codigo'),
            'nombre'  => $this->request->getVar('nombre'),
			'unidad_medida' => $this->request->getVar('unidad_medida'),
            'fecha'  => $this->request->getVar('fecha'),
			'valor' => $this->request->getVar('valor'),
        ];
        $ufModel->insert($data);
		$uf_id = $ufModel->getInsertID();

		echo $uf_id;
    }

    public function update($id = null)
	{
        $ufModel = new UfModel();
        $data = [
            'codigo' => $this->request->getVar('codigoEditar'),
            'nombre'  => $this->request->getVar('nombreEditar'),
			'unidad_medida' => $this->request->getVar('unidad_medidaEditar'),
            'fecha'  => $this->request->getVar('fechaEditar'),
			'valor' => $this->request->getVar('valorEditar'),
        ];
        $ufModel->update($id, $data);
		return json_encode($data);
    }

    public function delete($id = null)
	{
        $ufModel = new UfModel();
        $data = $ufModel->where('uf_id', $id)->delete($id);
		if($data)
		{
   			echo json_encode(array("status" => true));
		}else
		{
   			echo json_encode(array("status" => false));
		}
    }    
	//Metodo que recibe un rango de fechas y devuelve los valores del tipo de indicador dado, por cada dia.
	public function obtenerDatos()
	{
		$fechas = $this->request->getPost('fechas');
		$tipos = $this->request->getPost('tipos');
		$valores = array();
	
		for ($i = 0; $i < count($fechas); $i++) 
		{
			$apiUrl = 'https://mindicador.cl/api/'.$tipos.'/'.$fechas[$i];

			if ( ini_get('allow_url_fopen') ) 
			{
				$json = file_get_contents($apiUrl);
			} else 
			{
				$curl = curl_init($apiUrl);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				$json = curl_exec($curl);
				curl_close($curl);
			}

			$dailyIndicators = json_decode($json);
			if(!empty($dailyIndicators->serie))
			{
				array_push($valores, $dailyIndicators->serie[0]->valor);
			}else
			{
				array_push($valores, 0);
			}
		}
		return json_encode($valores);
	}
}
