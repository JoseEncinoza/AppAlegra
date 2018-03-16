<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        $this->initView();
        $this->view->baseUrl = $this->_request->getBaseUrl();
    }

    public function indexAction()
    {
        $data = $this->Request();
        $view = $this->_getParam('view', 0);
        //Si pasamos el parametro cliente o proveedor
        foreach ($data as $d)
          {
            if($d['type'][0] == $view or $d['type'][1] == $view)
              {
                $data2[] = $d;
              }     
          }
        $this->view->datos = $data2;  
    }

    public function addAction()
    {
        $form = new Application_Form_Contacto ();
        $form->submit->setLabel('Guardar Contacto');
        $this->view->form = $form;

        //POST
        if ($this->getRequest()->isPost())
        {
            //extrae un arreglo con los datos recibidos por POST
            $formData = $this->getRequest()->getPost();

            //isValid() revisa todos los validadores y filtros
            if ($form->isValid($formData))
            {
                //tomamos datos del form
                $json = $this->TomarDatos($form);
                //Ejecutamos request
                $this->Request(2,0,$json);
                $this->_helper->redirector('index');
            }
            //si los datos del formulario no son validos
            else
            {
                $form->populate($formData);
            }
        }
    }

    public function showAction()
    {
      $id = $this->_getParam('id', 0);
      //si viene algun id
      if ($id > 0)
            {
               $data = $this->Request(0,$id);
               $this->view->datos = $data;
            }
    }

    public function deleteAction()
    {
        //debe venir un parametro, por GET o POST, llamado id, con el id del album a borrar
        $id = $this->_getParam('id', 0);
        if ($id > 0)
            {
                $this->Request(1,$id);
                $this->_helper->redirector('index');
            }
    }

    public function updateAction()
    {
        //creo el formulario
        $form = new Application_Form_Contacto ();
        $form->submit->setLabel('Guardar Cambios');
        $this->view->form = $form;

        //Llenar Formulario con Datos
        $id = $this->_getParam('id', 0);
            if ($id > 0)
            {
               $data = $this->Request(0,$id);

               $form->name->setValue($data["name"]);
               $form->identification->setValue($data["identification"]);
               $form->email->setValue($data["email"]);
               $form->phonePrimary->setValue($data["phonePrimary"]);
               $form->phoneSecondary->setValue($data["phoneSecondary"]);
               $form->fax->setValue($data["fax"]);
               $form->mobile->setValue($data["mobile"]);
               $form->observations->setValue($data["observations"]);

               //Tipo Array
               foreach ($data["type"] as $type)
               {
                   if ($type == "client") $form->client->setValue(1);
                   if ($type == "provider")$form->provider->setValue(1);
               }
               foreach ($data["address"] as $ad => $e)
               {
                   if ($ad == "address") $form->address->setValue($e);
                   if ($ad == "city")$form->city->setValue($e);
               }
            }

        //PUT
        if ($this->getRequest()->isPost())
        {
            //extrae un arreglo con los datos recibidos por POST
            $formData = $this->getRequest()->getPost();

            //isValid() revisa todos los validadores y filtros
            if ($form->isValid($formData))
            {
                //Tomamos datos del form
                $json = $this->TomarDatos($form);
                //Request
                $data = $this->Request(3,$id,$json);
                $this->_helper->redirector('index');
            }
        }
    }

    public function Request($metodo,$id,$json)
    {
        //Auntenticacion
        $correo = "joseencinoza07@gmail.com";
        $token = "d14cb1f6daa04b79c6d3";
        $cadena = $correo.":".$token;
        $base= base64_encode($cadena);

        $Authorization = "Basic"." ".$base;
        //Url y Header de la API Alegra
        $url = 'https://app.alegra.com/api/v1/contacts/'.$id;
        $ch = curl_init($url);
        $header= array('Authorization:'.$Authorization,);

                    //1 Si es DELETE
                    if($metodo == 1)
                    {
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                    }
                    //2 Si es POST
                    if($metodo == 2)
                    {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
                    }
                    //3 Si es PUT
                    if($metodo == 3)
                    {
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
                    }
        //Agregar
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        //Ejecutar request
        $result = curl_exec($ch);
        curl_close($ch);

        //Decodificar de Json a PHP
        $data = json_decode($result,true);

        return $data;
    }

    public function TomarDatos($form)
    {
        //extraer datos del formulario
        $name = $form->getValue('name');
        $identification = $form->getValue('identification');
        $address = $form->getValue('address');
        $city = $form->getValue('city');
        $email = $form->getValue('email');
        $phonePrimary =$form->getValue('phonePrimary');
        $phoneSecondary = $form->getValue('phoneSecondary');
        $fax = $form->getValue('fax');
        $mobile = $form->getValue('mobile');
        $client = $form->getValue('client');
        $provider = $form->getValue('provider');
        $observations = $form->getValue('observations');

        //Datos tipo Array

        if($client == 1)$client="client";
        if($provider == 1)$provider="provider";
        $type = array ($provider,$client);

        //Datos tipo Object
        $objAddress = (object) array ('address' => $address,'city' => $city);

        //Parametros del Request
        $parametros = array(
             'name' => $name,
             'identification' => $identification,
             'address' => $objAddress,
             'email' => $email,
             'phonePrimary' => $phonePrimary,
             'phoneSecondary' => $phoneSecondary,
             'fax' => $fax,
             'mobile' => $mobile,            
             'type' => $type,
             'observations' => $observations

          );
          //Parametros en formato Json
          $json = json_encode($parametros);

          return $json;
    }

}
