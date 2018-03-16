<?php

class Application_Form_Contacto extends Zend_Form
{

    public function init()
    {
       $name = new Zend_Form_Element_Text('name');
       $name->setLabel('Nombre:')->setRequired(true)->addFilter('StripTags')
               ->addFilter('StringTrim')->addValidator('NotEmpty')->addValidator('StringLength',false, array(0, 90));

       $identification = new Zend_Form_Element_Text('identification');
       $identification->setLabel('Identificacion:')->addValidator('StringLength',false, array(0, 45));

       $address = new Zend_Form_Element_Text('address');
       $address->setLabel('Direccion:');

       $city = new Zend_Form_Element_Text('city');
       $city->setLabel('Ciudad:');

       $email = new Zend_Form_Element_Text('email');
       $email->setLabel('Correo Electronico:')->addValidator('StringLength',false, array(0, 100));

       $phonePrimary = new Zend_Form_Element_Text('phonePrimary');
       $phonePrimary->setLabel('Telefono 1:')->addValidator('StringLength',false, array(0, 45));

       $phoneSecondary = new Zend_Form_Element_Text('phoneSecondary');
       $phoneSecondary->setLabel('Telefono 2:')->addValidator('StringLength',false, array(0, 45));

       $fax = new Zend_Form_Element_Text('fax');
       $fax->setLabel('Fax:')->addValidator('StringLength',false, array(0, 45));

       $mobile = new Zend_Form_Element_Text('mobile');
       $mobile->setLabel('Celular:')->addValidator('StringLength',false, array(0, 45));

       $client = new Zend_Form_Element_Checkbox('client');
       $client->setLabel('Cliente');

       $provider = new Zend_Form_Element_Checkbox('provider');
       $provider->setLabel('Proveedor');
       
       $observations = new Zend_Form_Element_TextArea('observations');
       $observations->setLabel('Observaciones')->addValidator('StringLength',false, array(0, 500));




       $submit = new Zend_Form_Element_Submit('submit');

        //agregolos objetos creados al formulario
        $this->addElements(array($name,$identification,$address,$city,$email,$phonePrimary,$phoneSecondary,
                               $fax,$mobile,$client,$provider,$observations,$submit));
    }


}

