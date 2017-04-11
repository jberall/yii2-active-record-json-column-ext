<?php

namespace jberall\arjsoncolumn\lib;


use yii;
use yii\helpers\ArrayHelper;
use kartik\builder\Form;
use yii\helpers\Html;
use yii\base\Model;

/**
 * Description of EmailDynamicModel
 *
 * @author Jonathan Berall <jberall@gmail.com>
 */
class EmailDynamicModel extends \yii\base\DynamicModel{
    
    public function __construct(array $attributes = array(), $config = array()) {
        $attributes = ArrayHelper::merge(['email','type','default'],$attributes);
        parent::__construct($attributes, $config);
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'type'], 'string','on' => [Model::SCENARIO_DEFAULT]],
            [['email'],'email','on' => [Model::SCENARIO_DEFAULT]],
            [['type','email'],'trim','on' => [Model::SCENARIO_DEFAULT]],
            [['default'],'boolean','on' => [Model::SCENARIO_DEFAULT]],
            ['type','required','when'=>function($model) {
                return $model->email != '';
            }, 'enableClientValidation' => false,'on' => [Model::SCENARIO_DEFAULT]],
//            ['type', 'required', 'when' => function ($model) {
//                return $model->email != '';
//            }, 'whenClient' => "function (attribute, value) {
//                return $('#email').val() != '';
//            }"],           
        ];
    }
    

    

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => Yii::t('app', 'Email'),
            'type' => Yii::t('app', 'Type'),
            'default' => Yii::t('app', 'Default'), 
        ];
    }
    
    public function getFormEmailAttribs() {
        return [
             
            'email'=>['label'=>false,'type'=>Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter email...']],
            'type'=>['label'=>false,'type'=>Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter type...']],
            'default'=>['type'=>Form::INPUT_CHECKBOX],
//            'actions'=>['type'=>Form::INPUT_RAW, 'value'=>Html::submitButton('Submit', ['class'=>'btn btn-primary'])];
        ];
    } 

    public function getFormEmailWbragancaAttribs($i) {
        
        return [
            "label" => ['type' => Form::INPUT_RAW,'value'=> '<h4><i class="glyphicon glyphicon-envelope"></i> Emails</h4>'],
            "[{$i}]email"=>['label'=>false,'type'=>Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter email...']],
            "[{$i}]type"=>['label'=>false,'type'=>Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter type...']],
            "[{$i}]default"=>['type'=>Form::INPUT_CHECKBOX],
            "actions" => ['type'=>Form::INPUT_RAW,
                            'value'=> '<div class="pull-left">'.
                                    Html::button('<i class="glyphicon glyphicon-plus"></i>', ['class'=>'add-item btn btn-success btn-xs']).
                                    ' '.Html::button('<i class="glyphicon glyphicon-minus"></i>', ['class'=>'remove-item btn btn-danger btn-xs'])
                                  . '</div>'
                        ],
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
