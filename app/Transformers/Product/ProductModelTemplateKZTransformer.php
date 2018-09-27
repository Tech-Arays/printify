<?php
namespace App\Transformers\Product;

use App\Models\ProductModelTemplate;
use App\Transformers\File\FileFullTransformer;
use App\Transformers\File\ImageFileFullTransformer;

class ProductModelTemplateKZTransformer extends ProductModelTemplateFullTransformer
{
    protected $defaultIncludes = [
        'models',
        'category',
        'exampleFile',
        'overlay',
        'overlayBack',
        'garment'
    ];

    public function includeExampleFile(ProductModelTemplate $template)
    {
        $file = $template->example;
        if ($file) {
            return $this->item($file, new FileFullTransformer);
        }
    }

    public function includeOverlay(ProductModelTemplate $template)
    {
        $file = $template->overlay;
        if ($file) {
            return $this->item($file, new ImageFileFullTransformer);
        }
    }

    public function includeOverlayBack(ProductModelTemplate $template)
    {
        $file = $template->overlayBack;
        if ($file) {
            return $this->item($file, new ImageFileFullTransformer);
        }
    }

    public function transform(ProductModelTemplate $template)
    {
        $models = $template->models;
        $attrs = $template->catalogAttributes();

        $optionsTree = $this->optionsTree($models, $attrs);

        // TODO: DEPRECATED
        //$attrsTree = $this->attributesTree($models, $attrs);

        return [
            'id'                           => $this->getId($template),
            'name'                         => preg_replace('/(\s(Guy|Girl|Infant))$/', '', $template->name),
            'price'                        => $template->price,
            'priceMoney'                   => $template->priceMoney(),
            'isPrepaid'                    => $template->isPrepaid(),
                'prepaid_amount'               => $template->isPrepaid()
                    ? $template->category->prepaid_amount
                    : null,
                'prepaidAmountMoney'               => $template->isPrepaid()
                    ? $template->category->prepaidAmountMoney()
                    : null,
            'product_title'                => $template->product_title,
            'product_description'          => $template->product_description,
            'preview'                      => ($template->preview ? url($template->preview->url('thumb')) : null),
            'image'                        => ($template->image ? url($template->image->url()) : null),
            'imageBack'                    => ($template->imageBack ? url($template->imageBack->url()) : null),
            'backPrintCanBeAddedOnItsOwn'  => $template->backPrintCanBeAddedOnItsOwn(),
            'catalogAttributes'            => $attrs,

            // TODO: DEPRECATED
            //'attributesTree'  		   => $attrsTree,

            'optionsTree'  			       => $optionsTree
        ];
    }
}
