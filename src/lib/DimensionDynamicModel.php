<?php

namespace jberall\arjsoncolumn\lib;

//use yii\base\DynamicModel;
use yii;
use yii\helpers\ArrayHelper;
use kartik\builder\Form;
use yii\helpers\Html;
use yii\base\Model;

/**
 * Description of DimensionDynamicModel
 *
 * @author Jonathan Berall <jberall@gmail.com>
 */
class DimensionDynamicModel extends \yii\base\DynamicModel{
    
    public function __construct(array $attributes = array(), $config = array()) {
        
        $attributes = ArrayHelper::merge(['length'=>null,'width'=>null,'height'=>null,'uom'=>null],$attributes);
        parent::__construct($attributes, $config);
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['length','width','height','uom'], 'string','on' => [Model::SCENARIO_DEFAULT]],
            [['length','width','height','uom'], 'trim','on' => [Model::SCENARIO_DEFAULT]],
            
//            ['type','required','when'=>function($model) {
//                return $model->email != '';
//            }, 'enableClientValidation' => false],
////            ['type', 'required', 'when' => function ($model) {
////                return $model->email != '';
////            }, 'whenClient' => "function (attribute, value) {
////                return $('#email').val() != '';
////            }"],           
        ];
    }
    

    

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'length' => Yii::t('app', 'Length'),
            'width' => Yii::t('app', 'Width'),
            'height' => Yii::t('app', 'Height'), 
            'uom' => Yii::t('app', 'Unit Of Measure'), 
           
        ];
    }
    


    public function getFormDimensionAttribs() {
        
        return [
            "label" => ['type' => Form::INPUT_RAW,'value'=> '<h4>Dimension </h4>'],
            "length"=>['label'=>$this->getAttributeLabel('length'),'type'=>Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter length...']],
            "width"=>['label'=>$this->getAttributeLabel('width'),'type'=>Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter width...']],
            "height"=>['label'=>$this->getAttributeLabel('height'),'type'=>Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter height...']],
            "uom"=>['label'=>$this->getAttributeLabel('uom'),'type'=>Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter UOM...']],
//            "actions" =>['type'=>Form::INPUT_RAW,'value'=>
//                            '<div class="pull-right">
//                                <button type="button" class="add-item btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
//                                <button type="button" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
//                           </div>'
//                        ],
//            'actions'=>['type'=>Form::INPUT_RAW, 'value'=>Html::submitButton('Submit', ['class'=>'btn btn-primary'])];
        ];
    }     

}
