<?php

namespace App\Models;

use Codesleeve\Stapler\ORM\StaplerableInterface;
use Illuminate\Database\Eloquent\Model;
use Storage;
use Auth;
use Imagine;

use App\Transformers\File\FileFullTransformer;
use App\Transformers\Serializers\SimpleArraySerializer;

class File extends Model implements StaplerableInterface
{
    use \Codesleeve\Stapler\ORM\EloquentTrait;
    use \Culpa\Traits\Blameable;
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use Traits\DatetimeTrait;
    use Traits\CacheTrait;

    const TYPE_PRINT_FILE = 'print_file';
    const TYPE_SOURCE_FILE = 'source_file';
    const TYPE_PRINT_FILE_MOCKUP = 'print_file_mockup';
    const TYPE_PRINT_FILE_MOCKUP_BACK = 'print_file_mockup_back';
    const TYPE_CATEGORY_PREVIEW = 'category_preview';
    const TYPE_GARMENT_GROUP_PREVIEW = 'garment_group_preview';
    const TYPE_GARMENT_PREVIEW = 'garment_preview';
    const TYPE_MODEL_PREVIEW = 'model_preview';
    const TYPE_MODEL_IMAGE = 'model_image';
    const TYPE_MODEL_IMAGE_BACK = 'model_image_back';
    const TYPE_MODEL_EXAMPLE = 'model_example';
    const TYPE_MODEL_OVERLAY = 'model_overlay';
    const TYPE_MODEL_OVERLAY_BACK = 'model_overlay_back';
    //const TYPE_VARIANT_AR3_DESIGNER_ATTACHMENT = 'variant_ar3_designer_attachment';

    const TYPE_PRODUCT_AR3_SMALL_WHITE = 'product_ar3_small_white';
    const TYPE_PRODUCT_AR3_SMALL_BLACK = 'product_ar3_small_black';
    const TYPE_PRODUCT_AR3_SMALL_COLOR = 'product_ar3_small_color';

    const TYPE_PRODUCT_AR3_MEDIUM_WHITE = 'product_ar3_medium_white';
    const TYPE_PRODUCT_AR3_MEDIUM_BLACK = 'product_ar3_medium_black';
    const TYPE_PRODUCT_AR3_MEDIUM_COLOR = 'product_ar3_medium_color';

    const TYPE_PRODUCT_AR3_LARGE_WHITE = 'product_ar3_large_white';
    const TYPE_PRODUCT_AR3_LARGE_BLACK = 'product_ar3_large_black';
    const TYPE_PRODUCT_AR3_LARGE_COLOR = 'product_ar3_large_color';

    protected $table = 'files';
    protected $fillable = ['file', 'type'];
    public $updatedAtField = 'file_updated_at';
    public $timestamps = false;

    // blameable
    protected $blameable = [
        'created' => 'user_id'
    ];

    // searchable
        protected $searchableColumns = [
            'file_file_name' => 1
        ];

    protected function initAttachments()
    {
        $this->hasAttachedFile('file', [
            'styles' => [
                'medium' => '600x600',
                'small' => '250x250',
                'thumb' => '150x150'
            ]
        ]);
    }

    public function __construct(array $attributes = array()) {
        $this->initAttachments();
        parent::__construct($attributes);
    }

    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    /************
     * Mutators
     */

    /*********
     * Scopes
     */

        public function scopeSearch($query, $term)
        {
            return $query
                ->where('file_file_name', 'like', '%'.$term.'%');
        }

    /***********
     * Relations
     */

        public function printProductClientFiles()
        {
            return $this->hasMany(ProductClientFile::class, 'print_id', 'id');
        }

        public function sourceProductClientFiles()
        {
            return $this->hasMany(ProductClientFile::class, 'source_id', 'id');
        }

    /***********
     * Checks
     */

        public function isFileAtachment()
        {
            return in_array($this->type, static::attachmentTypesList());
        }

        public function isInUse()
        {
            return (
                (
                    $this->type == static::TYPE_PRINT_FILE
                    || $this->type == static::TYPE_SOURCE_FILE
                )
                && (
                    !$this->printProductClientFiles->isEmpty()
                    || !$this->sourceProductClientFiles->isEmpty()
                )
            );
        }

    /*************
     * Decorators
     */

        public function url($size = null)
        {
            return $this->file->url($size);
        }

        public function path()
        {
            return $this->file->path();
        }

        public function name()
        {
            return $this->file_file_name;
        }

        public function getTypeName()
        {
            return $this->pivot ? static::typeName($this->pivot->type) : static::typeName($this->type);
        }

        public function getDimensions()
        {
            if (!$this->hasCache('dimensions') && file_exists($this->path())) {
                $imagine = new Imagine\Imagick\Imagine();
                $image = $imagine->open(
                    $this->path()
                );
                $size = [
                    'width' => $image->getSize()->getWidth(),
                    'height' => $image->getSize()->getHeight()
                ];

                $this->putToCache('dimensions', $size, 60 * 24);
            }

            return $this->getCache('dimensions');
        }

    /*********
     * Helpers
     */

        public static function typeName($type)
        {
            $types = static::listTypes();
            return $types[$type];
        }

        public static function listTypes()
        {
            return collect([
                static::TYPE_PRINT_FILE => trans('labels.print_file'),
                static::TYPE_SOURCE_FILE => trans('labels.source_file'),
                static::TYPE_PRINT_FILE_MOCKUP => trans('labels.print_file_mockup'),
                static::TYPE_PRINT_FILE_MOCKUP_BACK => trans('labels.print_file_mockup_back'),
                static::TYPE_CATEGORY_PREVIEW => trans('labels.category_preview'),
                static::TYPE_GARMENT_GROUP_PREVIEW => trans('labels.garment_group_preview'),
                static::TYPE_GARMENT_PREVIEW => trans('labels.garment_preview'),
                static::TYPE_MODEL_PREVIEW => trans('labels.model_preview'),
                static::TYPE_MODEL_IMAGE => trans('labels.model_image'),
                static::TYPE_MODEL_IMAGE_BACK => trans('labels.model_image_back'),
                static::TYPE_MODEL_EXAMPLE => trans('labels.model_example'),
                static::TYPE_MODEL_OVERLAY => trans('labels.model_overlay'),
                static::TYPE_MODEL_OVERLAY_BACK => trans('labels.model_overlay_back'),

                static::TYPE_PRODUCT_AR3_SMALL_WHITE => trans('labels.DESIGNER_ATTACHMENT_SMALL_WHITE_AR3'),
                static::TYPE_PRODUCT_AR3_SMALL_BLACK => trans('labels.DESIGNER_ATTACHMENT_SMALL_BLACK_AR3'),
                static::TYPE_PRODUCT_AR3_SMALL_COLOR => trans('labels.DESIGNER_ATTACHMENT_SMALL_COLOR_AR3'),
                static::TYPE_PRODUCT_AR3_MEDIUM_WHITE => trans('labels.DESIGNER_ATTACHMENT_MEDIUM_WHITE_AR3'),
                static::TYPE_PRODUCT_AR3_MEDIUM_BLACK => trans('labels.DESIGNER_ATTACHMENT_MEDIUM_BLACK_AR3'),
                static::TYPE_PRODUCT_AR3_MEDIUM_COLOR => trans('labels.DESIGNER_ATTACHMENT_MEDIUM_COLOR_AR3'),
                static::TYPE_PRODUCT_AR3_LARGE_WHITE => trans('labels.DESIGNER_ATTACHMENT_LARGE_WHITE_AR3'),
                static::TYPE_PRODUCT_AR3_LARGE_BLACK => trans('labels.DESIGNER_ATTACHMENT_LARGE_BLACK_AR3'),
                static::TYPE_PRODUCT_AR3_LARGE_COLOR => trans('labels.DESIGNER_ATTACHMENT_LARGE_COLOR_AR3')
            ]);
        }

        public static function saveFileFromCanvas($previewImageData, $type = null)
        {
            $file = null;
            $previewImageData = str_replace('data:,', '', $previewImageData);

            if ($previewImageData) {

                $previewImageData = str_replace(['data:image/jpeg;base64,'], '', $previewImageData);
                $previewImageData = base64_decode($previewImageData, true);

                if ($previewImageData) {
                    $tmpFileName = 'storage/uploads/'.uniqid('user_'.auth()->user()->id).'.jpg';
                    Storage::put($tmpFileName, $previewImageData);
                    $previewFile = config('filesystems.disks.local.root').'/'.$tmpFileName;

                    $file = static::create([
                        'file' => $previewFile,
                        'type' => $type
                    ]);

                    Storage::delete($tmpFileName);
                }
            }

            return $file;
        }

        public static function saveMockupFromCanvas($previewImageData)
        {
            return static::saveFileFromCanvas($previewImageData, static::TYPE_PRINT_FILE_MOCKUP);
        }

        public static function attachmentTypesList()
        {
            return [
                static::TYPE_SOURCE_FILE,
                static::TYPE_MODEL_EXAMPLE,
                //static::TYPE_VARIANT_AR3_DESIGNER_ATTACHMENT,

                static::TYPE_PRODUCT_AR3_SMALL_WHITE,
                static::TYPE_PRODUCT_AR3_SMALL_BLACK,
                static::TYPE_PRODUCT_AR3_SMALL_COLOR,

                static::TYPE_PRODUCT_AR3_MEDIUM_WHITE,
                static::TYPE_PRODUCT_AR3_MEDIUM_BLACK,
                static::TYPE_PRODUCT_AR3_MEDIUM_COLOR,

                static::TYPE_PRODUCT_AR3_LARGE_WHITE,
                static::TYPE_PRODUCT_AR3_LARGE_BLACK,
                static::TYPE_PRODUCT_AR3_LARGE_COLOR,
                // ... TODO: add other when needed
            ];
        }

    /***************
     * Transformers
     */

        public function transformFull()
        {
            $resource = \FractalManager::item($this, new FileFullTransformer);
            return \FractalManager::i(new SimpleArraySerializer())->createData($resource)->toArray();
        }

    /***********
     * Functions
     */

}
