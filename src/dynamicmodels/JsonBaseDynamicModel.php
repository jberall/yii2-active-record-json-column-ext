<?php

namespace jberall\arjsoncolumn\dynamicmodels;

use yii;
use yii\base\DynamicModel;
use yii\helpers\ArrayHelper;
use jberall\arjsoncolumn\helpers\DynamicModelHelper;

/**
 * Description of JsonBaseDynamicModel
 * 
 * The JsonBaseDynmaicModel is required to extend the dynamic model.<br>
 * The JsonBaseActiveRecordModel  is required to extend the ActiveRecord Model<br>
 * 
 * 
 * Example
    A database table address with attribute data_json as json or jsonb.<br>
  The Address Model, which normally extends \yii\db\ActiveRecord  will extend JsonBaseActiveRecordModel.<br>
  The class AddressDynamicModel, which normally extends yii\base\DynamicModel will extend JsonBaseDynamicModel.<br>
  These classes have the required methods to handle the $data_json attribute.  The data_json attribute is handled as the $arrDMData array.<br>
 * 
 *
 * @author Jonathan Berall <jberall@gmail.com>
 */
class JsonBaseDynamicModel  extends DynamicModel {
    /**
     * The $arrObjects constant types
     * 
     */
    const ARROBJECTS_TYPE = [
            'ARRAY_OBJECTS' =>'array_objects',
            'OBJECT' => 'object',
        ];
    
    /**
     * The $arrObjects constant Load Methods
     * These methods are located in the DynamicModelHelper
     */
    const ARROBJECTS_LOAD_METHOD = [
        'REPLACE_ARRAY_WITH_DATA' => 'replaceArrayWithData',
        'OBJECT' =>'loadObject',
    ];
    
    /**
     * It is very important that all $arrObjects must exist in the $startingAttributes
     * @var array Holds array of objects, ['emails'=>[
            'modelPath' =>'\thorall\insuranceframework\base\models\EmailDynamicModel',
            'type' => self::ARROBJECTS_TYPE['ARRAY_OBJECTS'],
            'loadMethod' => self::ARROBJECTS_LOAD_METHOD['REPLACE_ARRAY_WITH_DATA'],
        ]] 
     */
    public $arrObjects = [];
    /**
     * Rules must be set to work otherwise it safe against SQL Injection.
     * @var array Initilizes attributes, ensuring always available. ['attribute1' => null, 'emails' => [], ...]
     */
    public $startingAttributes = [];
    
    /**
     * @inheritdoc
     * We merge the $startingAttributes, with $attributes passed.<br>
     * We then call DynamicModelHelper::initializeArrayObjects<br>
     * This creates generates array objects based on attributes.
     * @param array $attributes
     * @param array $config
     */
    public function __construct(array $attributes = array(), $config = array()) { 
        $attributes = ArrayHelper::merge($this->startingAttributes,$attributes); 
        $attributes = $this->initializeArrayObjects($attributes);
        parent::__construct($attributes, $config);
    }
    
    /**
     * Loops through the $arrObjects and initilizes the attribute.<br>
     * When an array exists for the attributes create the attribute and load it with values.<br>
     * 
     * @param array $attributes 
     * @return array $attributes
     */
    private function initializeArrayObjects($attributes){
        foreach($this->arrObjects as $key => $array){
            if (!class_exists($modelPath = $array['modelPath'] ?? null)) {
                die(" modelPath ($modelPath) does not exist.<br>".__METHOD__ );
            }
            
            switch($this->arrObjects[$key]['type']) {
                case self::ARROBJECTS_TYPE['ARRAY_OBJECTS']:
                    $attributes[$key] = $this->arrObjectsTypeArrayObjectsInit($attributes, $key);
                    break;
                case self::ARROBJECTS_TYPE['OBJECT']:
                    $attributes[$key] = new $modelPath($attributes[$key] ?? []);
                    break;
                default:
                    die(__METHOD__ . ' DEFAULT LINE: ' . __LINE__);
            }                 
        }
        return $attributes;

    }    
    /**
     * Based on the $arrObjects we initialize each $arrObject.
     * The initialization can be with or without data.
     * 
     * @param array $attributes
     * @param string $attribute_key
     * @return array $attributes
     */
    private function arrObjectsTypeArrayObjectsInit($attributes,$attribute_key) {
        
        $modelPath = $this->arrObjects[$attribute_key]['modelPath'];
        //if not set assign null
        $att = $attributes[$attribute_key] ?? null;
        //if not array return empty array
        if (gettype($att) != 'array') {
            return [new $modelPath()];
        }
        //must be an array.
        if (($type=gettype(current($att)))!= 'array') {
            die("Have an array of something other that array. {$type}<br>".__METHOD__ );
        }
        //load and create
        foreach ($att as $key => $vals){
            $newAttr[$key] = new $modelPath($vals); 
        }
        unset($attributes[$attribute_key]);
        return $attributes[$attribute_key] = $newAttr;
        
    }

    /**
     *  We call DynamicModelHelper::validateArrayObjects after parent::validate.
     * @param array $attributeNames
     * @param boolean $clearErrors
     * @return boolean $valid
     */
    public function validateWithArray($attributeNames = null, $clearErrors = true) {
        
        $valid = parent::validate($attributeNames, $clearErrors);     
        $valid = DynamicModelHelper::validateArrayObjects($this,'arrObjects',$attributeNames,$clearErrors) && $valid;
        return $valid;
    }    
    
//    public function loadWithArray($data, $formName = null) {
//        $valid = parent::load($data, $formName);
//
//        
//        //loop thru array and load objects.
//        foreach($this->arrObjects as $key => $obj) {
////            print_R($data);
////            die(__METHOD__);
//            //if no data for the arrObject do nothing
//            if($newData = $data[DynamicModelHelper::getModelPathName($obj['modelPath'])] ?? null) {
////                echo $obj['loadMethod'];
////                die(__METHOD__ . __LINE__);
//                $dmh = new DynamicModelHelper();
//                $valid = $dmh->{$obj['loadMethod']}($this,$key,$data,$formName) && $valid;
//            }
//        }        
//        return $valid;
//    }
}
