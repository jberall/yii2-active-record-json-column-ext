<?php

namespace jberall\arjsoncolumn\helpers;

use yii\base\Model;
use jberall\arjsoncolumn\dynamicmodels\JsonBaseDynamicModel;

/**
 * Description of LoadAndCreateMultipleModels
 *
 * @author Jonathan Berall <jberall@gmail.com>
 */
class DynamicModelHelper {
    
    
    public function loadObject($model,$attribute,$data,$formName = null) {
        if (!$newData = $data[$this->getModelPathName($modelPath = $this->getModelPath($model,$attribute))] ?? null) {
            //do nothing
            return true;
        } 
        return $model->arrDMData->$attribute->load($data,$formName);
    }
    /**
     * 
     * @param type $model
     * @param type $attribute
     * @param type $data
     * @param type $formName
     * @return boolean
     */
    public function replaceArrayWithData($model,$attribute,$data,$formName = null) {
        if (!$newData = $data[$this->getModelPathName($modelPath = $this->getModelPath($model,$attribute))] ?? null) {
            //do nothing
            return true;
        } 
        
        //by resetting the array_values we always have 0, 1, 2 ...
        //thus the value in the jsonb  = "emails": [{"email": "jberall@gmail.com",},{"email": "jberall@yahoo.com",}]
//        $data[$this->getModelPathName($modelPath = $this->getModelPath($model,$attribute))] = array_values($newData);
//        exit;
        //loop through and create objects not in the model.
        $this->createNewModelsNotInData($model,$attribute,$data);
        
        //loop through the $key models and see if need to unset array elements.
        $this->unsetArrayKeysIfNotInData($model, $attribute, $data);
//        echo '<br>before<br>';
//        print_R($model->arrDMData->$attribute);


        $valid = Model::loadMultiple($model->arrDMData->$attribute, $data);
//        echo '<br>after<br>';
//        print_R($model->arrDMData->$attribute);
//        exit;
        return $valid;
    }
    
    public function getModelPath($model,$attribute){
        return $model->arrDMData->arrObjects[$attribute]['modelPath'];
    }
    
    public function getModelPathName($modelPath) {
        return $arr[count($arr = explode("\\",$modelPath))-1];
    }
    
    public function createNewModelsNotInData($model,$attribute,$data){
        $newData = $data[$this->getModelPathName($modelPath = $this->getModelPath($model,$attribute))] ?? null;
        $arrAtts = $model->arrDMData->$attribute;
        foreach($newData as $i => $arr) {
            if (!isset($arrAtts[$i])) {
                $arrAtts[$i] = new $modelPath();
            } 
        }     
        $model->arrDMData->$attribute = $arrAtts;
    }
    public function unsetArrayKeysIfNotInData($model,$attribute,$data){
        $newData = $data[$this->getModelPathName($modelPath = $this->getModelPath($model,$attribute))] ?? null;

        foreach($arrAtts = $model->arrDMData->$attribute as $i => $obj) {
            if (!isset($newData[$i])) {
                unset($arrAtts[$i]);
            } 
        }
        $model->arrDMData->$attribute=$arrAtts;
        
    }
    
    
    /**
     * Loop through all the objects with an array and validate.
     * 
     * @param object $models
     * @param string $arrObjects
     * @param array $attributeNames
     * @param boolean $clearErrors
     * @return boolean
     */
    public function validateArrayObjects($models, $arrObjects, $attributeNames = null, $clearErrors = true) {
        $valid = true;
        foreach ($models->arrObjects as $attr => $array){
            
            if ($array['type'] == JsonBaseDynamicModel::ARROBJECTS_TYPE['ARRAY_OBJECTS']) {
                $valid = Model::validateMultiple($models->$attr, $attributeNames) && $valid;
            } elseif($array['type'] == JsonBaseDynamicModel::ARROBJECTS_TYPE['OBJECT']) {
                $valid = $models->$attr->validate($attributeNames, $clearErrors);
            }
        }
//print_r($array);exit;
        return $valid;
    }
}
